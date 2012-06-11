<?php
include('settings.php');
include('top_include.php');

topbar('Group Target Setter'); ?>


<div class="container-fluid">

    <?php
    include('course_select_dropdown2.php');
    ?>

    <div class="span6">
        <table>
            <tr>
                <th>Select / Deselect all students on this course</th>
            </tr>
            <tr>
                <td>
                    Please note that all QCA and MIS MTG scores are updated every 24 hours
                </td>
            </tr>
            <tr>
                <td style="vertical-align: middle;"><input type="checkbox"
                                                           onclick="toggleChecked(this.checked)">
                </td>
            </tr>
        </table>

    </div>

    <!--    End the drop down-->
</div>
</div>
</div>

<div>
    <?php
    showCurrentlySelectedCourse($CFG, $DB);
    ?>
</div>

<?php

//echo 'context session is: ' . $_SESSION['course_context_session'];
$domain = $_SERVER['HTTP_HOST'];
//select and show all students on this course used roleid 5 to indentify studentsserver
$select = "SELECT  distinct u.id, u.firstname, u.lastname, u.idnumber, u.username";
$from = " FROM {$CFG->prefix}role_assignments a JOIN {$CFG->prefix}user u on a.userid=u.id LEFT JOIN {$CFG->prefix}groups_members gm ON gm.userid=a.userid";
$where = " WHERE contextid='" . $_SESSION['course_context_session'] . "'";
$and = " AND a.roleid='5' order by lastname";
if ($_SESSION['course_group_session'] == 'All groups') {
    $andgroup = " ";
} elseif ($_SESSION['course_group_session'] != 'All groups') {
    $andgroup = " AND gm.groupid='" . $_SESSION['course_group_session'] . "'";
}

$querystudents = $select . $from . $where . $andgroup . $and;
//echo $querystudents;

$resultsStudents = $DB->get_records_sql($querystudents); #


// check the basic student results

//foreach ($resultsStudents as $row) {
//    echo $row->firstname . ' ' . $row->lastname . '<br>';
//}

// Reset all graph score to zero
include('zero-scores.php');

// Get the date 1 month ago
$dateMonth = getDateMonth();
?>

<?php
$count = 0;
$whereSetForUserid = "";
$activeTargets = 0;
$targetsAchieved = 0;
$targetsWithdrawn = 0;
$causeForConcern = 0;
$studentsWithReviews = 0;
$totalReviews = 0;
$studentsWithConcerns = 0;
$totalConcerns = 0;
$studentsWithReasons = 0;
$totalReasons = 0;
$studentsWithContributionshttp = 0;
$totalContributions = 0;
$good = 0;
$reviewThreeScore1 = 0;
$reviewFourScore1 = 0;
$reviewOneScore1 = 0;
$reviewFiveScore1 = 0;
$reviewSixScore1 = 0;
$reviewTwoScore1 = 0;
$outstanding = 0;
$studentsWithReviews = 0;
$studentsWithConcerns = 0;
$studentsWithReasons = 0;
$studentsWithContributions = 0;
$badgeCount = 0;
$where = '';
$students = array();
$studentsRefs = array();

// build the array of students and the array to send to the soap service
foreach ($resultsStudents as $row) {

    //    echo 'idnumber ' . $row->idnumber;

    // build a where query
    //    $whereSetFor = $whereSetFor . "setforuserid='" . $row->id . "' OR ";
    $whereSetForUserid = $whereSetForUserid . "u.id='" . $row->id . "' OR ";
    $whereSetForUserid2 = $whereSetForUserid2 . "e.user_id='" . $row->id . "' OR ";
    $studentRefs[] = array(
        'id' => $row->id,
        'lref' => $row->idnumber,
        'firstname' => $row->firstname,
        'lastname' => $row->lastname,
        'username' => $row->username,
    );
}

// cut the end of the where
$whereSetForUserid = substr($whereSetForUserid, 0, -3);
$whereSetForUserid2 = substr($whereSetForUserid2, 0, -3);

// get the students targets

$targets = getTargets2($whereSetForUserid, $DB, $targetId);

//print_r($targets);

$reviews = getReviews2($whereSetForUserid, $DB, $reportsArray);
$rag = getRag2($whereSetForUserid2, $DB);
$mtg = getMTGS2($whereSetForUserid, $DB);
$lastReview = getLastReview($whereSetForUserid2, $DB, $reviewNumber);

//echo '<br>student refs<br>';
//print_r($studentRefs);
//echo '<br>';

try {
    $report = $client->__soapCall("groupReport2", array($studentRefs));
} catch (SoapFault $e) {
    // handle issues returned by the web service
    echo ' There has been a problem getting the attendance from NG';
}
//echo '<br>dump report soap call<br>';
//var_dump($report);

//combine the student array and reviews array


//echo '<br>hacking ont he reviews array<br>';
//
//print_r($reviews);
//
//echo '<br>';


//var_dump($lastReview);
foreach ($studentRefs as &$student) {
    foreach ($reviews as $reviews2) {
        if ($student['id'] === $reviews2['id']) {
            $student = array_merge($student, $reviews2);
            break;
        }
    }
    foreach ($lastReview as $lastReview2) {
        if ($student['id'] === trim($lastReview2['id'])) {
            $student = array_merge($student, $lastReview2);
            break;
        }
    }
    foreach ($targets as $targets2) {
        if ($student['id'] === $targets2['id']) {
            $student = array_merge($student, $targets2);
            break;
        }
    }
    foreach ($rag as $rag2) {
        if ($student['id'] === $rag2['id']) {
            $student = array_merge($student, $rag2);
            break;
        }
    }
    foreach ($mtg as $mtg2) {
        if ($student['id'] === $mtg2['id']) {
            $student = array_merge($student, $mtg2);
            break;
        }
    }
    foreach ($report as $report2) {
        if ($student['lref'] === trim($report2['lref'])) {
            $student = array_merge($student, $report2);
            break;
        }
    }
}

//echo '<br>student refs - final merged set<br>';
//print_r($studentRefs);

//dump the reviews array as we don't need it anymore
unset($reviews);
unset($targets);
unset($rag);
unset($mtg);

unset($lastReview);

//print_r($studentRefs);

// rpint out the table headers
?>
<h2>Students on this course</h2>
<form name="process" action="process_targets2.php" method="POST">
<table id="example" class="table table-striped" style="text-align: center;">
<thead>
<tr>
    <th>Name</th>
    <th>Surname</th>
    <?php if ($ragSet == 1) { ?>
    <th>RAG</th>
    <?php } ?>
    <th>ID</th>
    <th>Select</th>
    <th>Att %</th>
    <?php if ($targetSet == 1) { ?>
    <th>Targets</th>
    <?php } ?>
    <?php
    // print out the reports headers based on the available reports
    foreach ($reportsArray as $key => $item) {
        echo '<th>' . $item . '</th>';
    }

    if ($mtgSet = 1) {
        ?>

        <th>T-MTG</th>
        <th>P-Best</th>
        <th>QCA</th>
        <th>MIS MTG</th>
        <?php } ?>
    <?php if ($flightplanSet == 1) { ?>
    <th>R 1</th>
    <th></th>
    <th>R 2</th>
    <th></th>
    <th>R 3</th>
    <th></th>
    <th>R 4</th>
    <?php } ?>
    <?php if ($parentalSet == 1) { ?>
    <th>Parental</th>
    <?php
}
    if ($castSet == 1) {
        ?>
        <th>Cast</th>
        <?php
    }
    if ($withdrawnSet == 1) {
        ?>
        <th>Withdrawn</th>
        <?php
    }
    if ($mobileSet == 1) {
        ?>
        <th>Mobile</th>
        <?php
    }
    if ($badgesSet == 1) {
        ?>

        <th>Medals</th>
        <?php
    }
    if ($passportSet == 1) {
        ?>
        <th>Passport</th>
        <?php }?>

    <?php
    if ($lastReviewSet == 1) {
        ?>
        <th>Last Review</th>
        <th>Last Review By</th>
        <?php
    }
    ?>

</tr>
</thead>
<tbody>

<?php

unset($report);
//

//print_r($studentRefs);

//
foreach ($studentRefs as $row) {
    ?>

<tr>
<td><a
    href="<?php echo $CFG->wwwroot; ?>/blocks/ilp/actions/view_main.php?user_id=<?php echo $row['id']; ?>&course_id=<?php echo $_SESSION['course_code_session']; ?>"
    target="_blank"><?php echo $row['firstname']; ?></a>
</td>
<td><a
    href="<?php echo $CFG->wwwroot; ?>/blocks/ilp/actions/view_main.php?user_id=<?php echo $row['id']; ?>&course_id=<?php echo $_SESSION['course_code_session']; ?>"
    target="_blank"><?php echo $row['lastname']; ?></a>
</td>

    <?php if ($ragSet == 1) {
    // set the rag colour and icon
    if (($row['ragstatus'] == '3') or ($row['ragstatus'] == null)) {
        $colour = 'green';
        $ragicon = '<img src="images/1Green_Ball.png" title="1green" height="20px" width="20px"/>';
        $green++;
    } elseif ($row['ragstatus'] == '2') {
        $colour = 'amber';
        $ragicon = '<img src="images/2Yellow_Ball.png" title="2yellow" height="20px" width="20px"/>';
        $amber++;
    } elseif ($row['ragstatus'] == '1') {
        $ragicon = '<img src="images/3Red_Ball.png" title="3red" height="20px" width="20px"/>';
        $colour = 'red';
        $red++;
    } else {
        $colour = 'green';
        $ragicon = '<img src="images/1Green_Ball.png" title="1green" height="20px" width="20px"/>';
        $green++;
    }

    ?>
<td><?php echo $ragicon; ?></td>
    <?php } ?>

<td><?php echo trim($row['lref']); ?></td>
<td><input type="checkbox" class="checkbox" name="checkbox[]" value="<?php echo $row['id']; ?>"></td>
<td><?php echo $row['totalAtt']; ?></td>
    <?php
    if ($row['totalAtt'] >= '100') {
        $outstanding++;
    } elseif ($row['totalAtt'] >= '95') {
        $excellent = $excellent + 1;
    } elseif ($row['totalAtt'] <= '94' && $row['totalAtt'] >= '90') {
        $good++;
    } elseif ($row['totalAtt'] <= '89' && $row['totalAtt'] >= '80') {
        $causeForConcern++;
    } elseif ($row['totalAtt'] <= '80') {
        $poor++;
    }
    ?>

    <?php if ($targetSet == 1) { ?>
<td><?php echo $row['tobe']; ?>/<?php echo $row['achieved']; ?>/<?php echo $row['withdrawnTarget']; ?></td>
    <?php
    //@FIXME not right needs to work on the fly
    // totals for the graphs
    $activeTargets = $activeTargets + $row['tobe'];
    $targetsAchieved = $targetsAchieved + $row['achieved'];
    $targetsWithdrawn = $targetsWithdrawn + $row['withdrawn'];

}

    if ($reportsSet == 1) {

        // Echoes the number of number of reports for the students and also create variables based to the available reports
        // IE those is reports Array - 'Reports', 'Concerns' etc
        foreach ($reportsArray as $key => $item) {
            echo '<td>', $row[strtolower($item)], '</td>';
//            echo ' key is: ' . $row[strtolower($item)];

            // create/update the total number of X
            ${'total' . $item} = ${'total' . $item} + $row[strtolower($item)];

            // update the students with X numbers
            if ($row[strtolower($item)] != 0) {
                ${'studentsWith' . $item}++;
            }

//        echo 'test varible ' .   $Reviews;

        }

//       echo 'student with reviews: ' .  $studentsWithReviews;
    }

    if ($mtgSet = 1) {
        ?>

    <td><?php echo $row['tutor_mtg']; ?></td>
    <td><?php echo $row['mtg']; ?></td>

        <?php
        // set the MTG running total for the graphs
        if (($row['mtg'] != '') or ($row['mtg'] != null)) {
            $mtg_set++;
        }
        ?>

    <td><?php echo $row['qca']; ?></td>
    <td><?php echo $row['mis_mtg']; ?></td>

        <?php
    }

    if ($flightplanSet == 1) {
        //            Flightplan stuff
        list($review1, $r2, $review2, $r3, $review3, $r4, $review4) = getFlightplanScores($row['lref'], $DB);

        // Work out the scores for the flight plans graphs

        for ($i = 1; $i <= 4; $i++) {
            if (${'review' . $i} == 1) {
                ${'reviewOneScore' . $i}++;
            } elseif (${'review' . $i} == 2) {
                ${'reviewTwoScore' . $i}++;
            } elseif (${'review' . $i} == 3) {
                ${'reviewThreeScore' . $i}++;
            } elseif (${'review' . $i} == 4) {
                ${'reviewFourScore' . $i}++;
            } elseif (${'review' . $i} == 5) {
                ${'reviewFiveScore' . $i}++;
            } elseif (${'review' . $i} == 6) {
                ${'reviewSixScore' . $i}++;
            } elseif (${'review' . $i} == '') {
                ${'noflight' . $i}++;
            }
        }

        ?>

    <td><?php echo $review1; ?></td>
    <td><?php echo $r2; ?></td>
    <td><?php echo $review2; ?></td>
    <td><?php echo $r3; ?></td>
    <td><?php echo $review3; ?></td>
    <td><?php echo $r4; ?></td>
    <td><?php echo $review4; ?></td>

        <?php
    }

    if ($parentalSet = 1) {

        ?>
    <td><?php echo checkIfTrue($row['parental']); ?></td>
        <?php if ($row['parental'] == 1) {
            $parental_signed++;
        }
        ;
    }

    if ($castSet = 1) {
        ?>
    <td><?php echo checkIfTrue($row['cast']); ?></td>
        <?php if ($row['cast'] == 1) {
            $cast_signed++;
        }
        ;
    }
    if ($withdrawnSet = 1) {
        ?>
    <td><?php echo checkIfTrueWithNo($row['withdrawn']); ?></td>
        <?php
    }
    if ($mobileSet = 1) {
        ?>
    <td><?php echo checkIfTrue($row['mobile']); ?></td>
        <?php }

//@FIXME this is a mess - combine
    if ($badgesSet == 1) {
        //   $badges = getMedals($row->idnumber, $mysqli);
        $query = "SELECT icon FROM mdl_badges_link  JOIN mdl_badges ON mdl_badges_link.badge_id=mdl_badges.id  where student_id='" . trim($row['lref']) . "'";
//        echo $query;
        $resultBadges = $DB->get_records_sql($query);
        $badges = array();
        foreach ($resultBadges as $row2) {
            $icon = $row2->icon;
            array_push($badges, $icon);
        }
        ?>

    <td>
        <div class="badges">
            <?php

            foreach ($badges as $row2) {
                echo '<img src="' . $CFG->wwwroot . '/blocks/ilp/custom/pix/badges/' . $row2 . '.png" width="25" height=25" />';
            } ?>
        </div>
    </td>

        <?php }

    if ($passportSet == 1) {

        ?>
    <td><?php echo passportMedals($row['highestaward'], $row['parts']); ?></td>

        <?php }

    if ($lastReviewSet == 1) {

        ?>

    <td><?php echo $row['lastreview']; ?></td>
    <td><?php echo $row['lastreviewdate']; ?></td>
        <?php } ?>

</tr>
    <?php $count++;
}
?>

</tbody>
</table>

<?php // select options ?>

<div class="row">
<div class="container">
<h1>Select target type and Set</h1>
<fieldset>
<div class="clearfix">
    <label for="select_review" id="review_title">Select Review</label>

    <div class="input pad">
        <select name="type" id="select_review">
            <option>--Select--</option>
            <option>Target</option>
            <option>Progress Review</option>
            <option>Concern</option>
            <option>Reason for Status Change</option>
            <option>Contribution</option>
            <option>RAG - Traffic Light</option>
            <option>Medals</option>
            <option>In Order to Progress to...</option>
            <option>Progression Targets</option>
            <?php if ($passportSet ==1) { ?>
            <option>Employability Passport</option>
                <?php } ?>
        </select>
    </div>
</div>


<p/>

<div class="clearfix">
    <label for="rag" id="rag_title">Select RAG</label>

    <div class="input pad">
        <select name="rag" id="rag">
            <?php // get the RAG statuses in the system

           $statuses = $DB->get_records('block_ilp_plu_sts_items');
            echo '<option>--Select--</option>';
            foreach ($statuses as $item) {
                echo '<option value="' ,  $item->id  , '">' , $item->name , '</option>';
            }
            ?>
        </select>
    </div>
</div>


<div class="clearfix">
    <label for="target_name" id="target_name_title">Target Name</label>
    <label for="target_name" id="target_name_title_progression">To progress to title !WARNING seting this will overwrite
        any current 'in order to progress to...' set!</label>

    <div class="input pad">
        <input id="target_name" type="text" name="title" size="52" class="xxlarge"/>
    </div>
</div>


<!--<div class="clearfix">-->
<!--    <label for="target_name" id="target_name_title_progression">To progress to title !WARNING seting this will overwrite any current 'in order to progress to...' set!</label>-->
<!---->
<!--    <div class="input pad">-->
<!--        <input id="target_name_progression" type="text" name="target_name_progression" size="52" class="xxlarge"/>-->
<!--    </div>-->
<!--</div>-->


<div class="demo">
    <div class="clearfix">
        <label for="datepicker" id="datepicker_title">Select Date</label>

        <div class="input pad">
<!--            <input type="text" id="datepicker" name="date" class="xxlarge">-->
            <input class="datepicker" id="datepicker" class="span2" size="16" type="text" data-date-format="dd/mm/yyyy" name="date" value="">

        </div>
    </div>
</div>

<div class="clearfix">
    <label for="details" id="details_title">Enter Details</label>

    <div class="input pad">
        <textarea name="target" rows="8" cols="40" id="details" class="xxlarge"></textarea>
    </div>
</div>

<div class="clearfix">
    <label for="checkbox" id="checkbox_title">The target is related to this course</label>

    <div class="input pad">
        <input type="checkbox" id="checkbox" name="course_related" value="ON" checked/>
    </div>
</div>


<div id="medals_div">

    <?php
    $badgeCount == 0;
//    mysql_select_db('medals') or die('Unable to select the database');
    $querymedals = "SELECT id, name, icon, description, category FROM mdl_badges";
    $resultsBadges = $DB->get_records_sql($querymedals);

    $num_rows = count($resultsBadges);
    //echo 'num rows: ' . $num_rows;
    echo '<h3>Select medals</h3>';
//        echo '**Warning the student must have manual mtg set on the flightplan for medals to work**';
    echo '<table>';
    foreach ($resultsBadges as $row) {

        if ($badgeCount == 0) {
            echo '<tr><td>' . $row->name . ' ';
            echo '</td><td><img src="http://' . $domain . '/blocks/ilp/custom/pix/badges/' . $row->icon . '.png"/></td>';
            echo '<td>';
            echo '<input type="radio" name="medal" value="' . $row->id . '"   />';
            echo '</td>';

        } else {
            echo '<td width="20px"></td><td>' . $row->name . ' ';
            echo '</td><td><img src="http://' . $domain . '/blocks/ilp/custom/pix/badges/' . $row->icon . '.png"/></td>';

            echo '<td>';
            //<input type="checkbox" id="checkbox_medal" name="checkbox_medal[]" value="' . $row['id'] . '" />';
            echo '<input type="radio" name="medal" value="' . $row->id . '"   />';
            echo '</td></tr>';
        }
        //        echo 'badge count is ' . $badgeCount;
        if ($badgeCount == 0) {
            $badgeCount = 1;
        } elseif ($badgeCount == 1) {
            $badgeCount = 0;
        }
    }
    echo '</table>';
    ?>
</div>


<div id="employability">
    <div class="clearfix">
        <label id="employ" accesskey="">Select employability options to mark completed</label>
        <table style="text-align:center;">
            <tr class="bronze">
                <td>
                    Bronze 1
                </td>
                <td>
                    Bronze 2
                </td>
                <td>
                    Bronze 3
                </td>
            </tr>
            <tr class="bronze">
                <td>
                    Professional Standards
                </td>
                <td>
                    Professional Communication
                </td>
                <td>
                    Draft CV
                </td>
            </tr>
            <tr class="silver">
                <td>
                    <input type="checkbox" id="b1" name="b1">
                </td>
                <td>
                    <input type="checkbox" id="b2" name="b2">
                </td>
                <td>
                    <input type="checkbox" id="b3" name="b3">
                </td>
            </tr>

            <tr class="silver">
                <td>Silver 1</td>
                <td>Silver 2</td>
                <td>Silver 3</td>
            </tr>
            <tr class="silver">
                <td>Searching for a job</td>
                <td>Employer Interview</td>
                <td>Create a CV</td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" id="s1" name="s1">
                </td>
                <td>
                    <input type="checkbox" id="s2" name="s2">
                </td>
                <td>
                    <input type="checkbox" id="s3" name="s3">
                </td>

            </tr>

            <tr class="gold">
                <td>Gold 1</td>
                <td>Gold 2</td>
                <td>Gold 3</td>
            </tr>
            <tr class="gold">
                <td>Optional</td>
                <td>Optional</td>
                <td>Optional</td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" id="g1" name="g1">
                </td>
                <td>
                    <input type="checkbox" id="g2" name="g2">
                </td>
                <td>
                    <input type="checkbox" id="g3" name="g3">
                </td>
            </tr>
        </table>

    </div>
</div>

<!-- pass the course id and userid -->
<input type="hidden" name="courseid" value=" <?php echo $_SESSION['course_code_session'] ?> "/>
<input type="hidden" name="userid" value=" <?php echo $USER->id ?> "/>
<input type="hidden" name="username" value=" <?php echo $USER->username ?> "/>
<input type="hidden" name="groupid" value=" <?php echo trim($_SESSION['course_group_session']) ?> "/>
<!--    <input type="hidden" name="url" value=" --><?php //echo $url ?><!-- "/>-->
<!--    <input type="hidden" name="url2" value=" --><?php //echo $url2 ?><!-- "/>-->

<br/>

<input id="save" class="btn btn-success" type="submit" name="submit_change" value="Submit Changes"/>

</form>

             <?php include('reports_forms.php'); ?>

</div>
</fieldset>
</div>
</div>


<?php
//echo '<h3>refds</h3>';
//print_r($studentRefs);

 if ($showTotals == 1) {
include('view2_totals.php');
}

if ($showGraphs == 1) {
    include('view2_graphs.php');
}

//
?>

<script>


    $(function () {
        //initially hide the textbox
        $("#target_name").hide();
        $("#target_name_title").hide();
        $("#target_name_title_progression").hide();
        $("#target_name_progression").hide();
        $("#datepicker").hide();
        $("#datepicker_title").hide();
        $("#rag").hide();
        $("#rag_title").hide();
        $("#details").hide();
        $("#details_title").hide();
        $("#checkbox").hide();
        $("#checkbox_title").hide();
        $("#medals_div").hide();
        $("#medals_title").hide();
        $("#medal").hide();
        $("#employability").hide();
        $("#save").hide();
        $('#select_review').change(function () {
            if ($(this).find('option:selected').val() == "Target") {
                $("#target_name").show();
                $("#datepicker").show();
                $("#datepicker_title").show();
                $("#target_name_title").show();
                $("#target_name_title_progression").hide();
                $("#target_name_progression").hide();
                $("#rag").hide();
                $("#rag_title").hide();
                $("#details").show();
                $("#details_title").show();
                $("#checkbox").show();
                $("#checkbox_title").show();
                $("#employability").hide();
                $("#save").show();
            } else if ($(this).find('option:selected').val() == "RAG - Traffic Light") {
                $("#rag").show();
                $("#rag_title").show();
                $("#target_name").hide();
                $("#datepicker").hide();
                $("#target_name_title").hide();
                $("#target_name_title_progression").hide();
                $("#target_name_progression").hide();
                $("#datepicker_title").hide();
                $("#details").show();
                $("#details_title").show();
                $("#checkbox").hide();
                $("#checkbox_title").hide();
                $("#medals_div").hide();
                $("#employability").hide();
                $("#save").show();

            } else if ($(this).find('option:selected').val() == "Progression Targets") {
                $("#rag").hide();
                $("#rag_title").hide();
                $("#target_name").hide();
                $("#datepicker").show();
                $("#target_name_title").hide();
                $("#target_name_title_progression").hide();
                $("#target_name_progression").hide();
                $("#datepicker_title").show();
                $("#details").show();
                $("#details_title").show();
                $("#checkbox").hide();
                $("#checkbox_title").hide();
                $("#medals_div").hide();
                $("#employability").hide();
                $("#save").show();

            } else if ($(this).find('option:selected').val() == "In Order to Progress to...") {
                $("#rag").hide();
                $("#rag_title").hide();
                $("#target_name").show();
                $("#datepicker").hide();
                $("#target_name_title").hide();
                $("#target_name_title_progression").show();
                $("#target_name_progression").hide();
                $("#datepicker_title").hide();
                $("#details").hide();
                $("#details_title").hide();
                $("#checkbox").hide();
                $("#checkbox_title").hide();
                $("#medals_div").hide();
                $("#employability").hide();
                $("#save").show();

            } else if ($(this).find('option:selected').val() == "Medals") {

                $("#medals_div").show();
                $("#target_name").hide();
                $("#datepicker").hide();
                $("#target_name_title").hide();
                $("#target_name_title_progression").hide();
                $("#target_name_progression").hide();
                $("#datepicker_title").hide();
                $("#details").hide();
                $("#details_title").hide();
                $("#checkbox").hide();
                $("#checkbox_title").hide();
                $("#rag").hide();
                $("#rag_title").hide()
                $("#employability").hide();
                $("#save").show();
            } else if ($(this).find('option:selected').val() == "Employability Passport") {

                $("#medals_div").hide();
                $("#target_name").hide();
                $("#datepicker").hide();
                $("#target_name_title").hide();
                $("#target_name_title_progression").hide();
                $("#target_name_progression").hide();
                $("#datepicker_title").hide();
                $("#details").hide();
                $("#details_title").hide();
                $("#checkbox").hide();
                $("#checkbox_title").hide();
                $("#rag").hide();
                $("#rag_title").hide()
                $("#employability").show();
                $("#save").show();
            } else if ($(this).find('option:selected').val() == "--Select--") {
                $("#target_name").hide();
                $("#target_name_title").hide();
                $("#target_name_title_progression").hide();
                $("#target_name_progression").hide();
                $("#datepicker").hide();
                $("#datepicker_title").hide();
                $("#rag").hide();
                $("#rag_title").hide();
                $("#details").hide();
                $("#details_title").hide();
                $("#checkbox").hide();
                $("#checkbox_title").hide();
                $("#medals_div").hide();
                $("#medals_title").hide();
                $("#medal").hide();
                $("#employability").hide();
                $("#save").hide();
            } else {
                $("#target_name").hide();
                $("#datepicker").hide();
                $("#target_name_title").hide();
                $("#target_name_title_progression").hide();
                $("#target_name_progression").hide();
                $("#datepicker_title").hide();
                $("#rag").hide();
                $("#rag_title").hide();
                $("#details").show();
                $("#details_title").show();
                $("#checkbox").show();
                $("#checkbox_title").show();
                $("#medals_div").hide();
                $("#employability").hide();
                $("#save").show();
            }
        });

    });


//    $(function () {
//        $("#datepicker").datepicker({
//            dateFormat:'dd-mm-yy',
//            changeMonth:true,
//            changeYear:true
//        });
//        // tl is the default so don't bother setting it's positio
//    });


    function toggleChecked(status) {
        $(".checkbox").each(function () {
            $(this).attr("checked", status);
        })
    }

    $('.datepicker').datepicker(
//        $('.hiddendate').text($('.datepicker'))

    )


</script>


<!--Load the javascript to control the datatable - needs to be edited when new coloumns are added at the end or it breaks selection boxes-->

<script src="<?php echo $CFG->wwwroot; ?>/blocks/group_targets/bootstrap2/js/dt_bootstrap.js" type="text/javascript"
        charset="utf-8"></script>