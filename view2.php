<?php

include('top_include.php');
?>

    <?php topbar('Group Target Setter'); ?>


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
    showCurrentlySelectedCourse($CFG, $mysqli);
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

$resultsstudents = $mysqli->query($querystudents);
$num_students = $resultsstudents->num_rows;
//echo 'num rows: ', $num_students;
$count = 0;

?>

<?php
// Reset all graph score to zero
include('zero-scores.php');

// Get the date 1 month ago
$dateMonth = getDateMonth();
?>

<h2>Students on this course</h2>
<form name="process" action="process_targets2.php" method="POST">
<table id="example" class="table table-striped" style="text-align: center;">
<thead>
<tr>
    <th>Name</th>
    <th>Surname</th>
    <th>RAG</th>
    <th>ID</th>
    <th>Select</th>
    <th>Att %</th>
    <th>Targets</th>
    <th>Reviews</th>
    <th>Concerns</th>
    <th>Reasons</th>
    <th>Contribs</th>
    <th>T-MTG</th>
    <th>P-Best</th>
    <th>QCA</th>
    <th>MIS MTG</th>
    <th>R 1</th>
    <th></th>
    <th>R 2</th>
    <th></th>
    <th>R 3</th>
    <th></th>
    <th>R 4</th>
    <th>Parental</th>
    <th>Cast</th>
    <th>Withdrawn</th>
    <th>Mobile</th>
    <th>Medals</th>
    <th>Passport</th>
    <th>Last Review</th>
    <th>Last Review By</th>

</tr>
</thead>
<tbody>

<?php
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
while ($row = $resultsstudents->fetch_object()) {

    //    echo 'idnumber ' . $row->idnumber;

    // build a where query
    //    $whereSetFor = $whereSetFor . "setforuserid='" . $row->id . "' OR ";
    $whereSetForUserid = $whereSetForUserid . "u.id='" . $row->id . "' OR ";
    $studentRefs[] = array(
        'id' => $row->id,
        'lref' => $row->idnumber,
        'firstname' => $row->firstname,
        'lastname' => $row->lastname,
        'username' => $row->username,
    );
}

//print_r($studentRefs);

// cut the end of the where
//$whereSetFor = substr($whereSetFor, 0, -3);
$whereSetForUserid = substr($whereSetForUserid, 0, -3);

// get the students reviews
$reviews = getReviews2($whereSetForUserid, $mysqli);
$targets = getTargets2($whereSetForUserid, $mysqli);
$rag = getRag2($whereSetForUserid, $mysqli);
$mtg = getMTGS2($whereSetForUserid, $mysqli);
$lastReview = getLastReview($whereSetForUserid, $mysqli);

try {
    $report = $client->__soapCall("groupReport2", array($studentRefs));
} catch (SoapFault $e) {
    // handle issues returned by the web service
    echo ' There has been a problem getting the attendance from NG';
}
//echo 'dump reviews';
//var_dump($report);

//combine the student array and reviews array

//var_dump($lastReview);
foreach ($studentRefs as &$student)
{
    foreach ($reviews as $reviews2)
    {
        if ($student['id'] === $reviews2['id']) {
            $student = array_merge($student, $reviews2);
            break;
        }
    }
    foreach ($lastReview as $lastReview2)
    {
        if ($student['id'] === trim($lastReview2['id'])) {
            $student = array_merge($student, $lastReview2);
            break;
        }
    }
    foreach ($targets as $targets2)
    {
        if ($student['id'] === $targets2['id']) {
            $student = array_merge($student, $targets2);
            break;
        }
    }
    foreach ($rag as $rag2)
    {
        if ($student['id'] === $rag2['id']) {
            $student = array_merge($student, $rag2);
            break;
        }
    }
    foreach ($mtg as $mtg2)
    {
        if ($student['id'] === $mtg2['id']) {
            $student = array_merge($student, $mtg2);
            break;
        }
    }
    foreach ($report as $report2)
    {
        if ($student['lref'] === trim($report2['lref'])) {
            $student = array_merge($student, $report2);
            break;
        }
    }

}

//dump the reviews array as we don't need it anymore
unset($reviews);
unset($ragets);
unset($rag);
unset($mtg);
unset($report);
unset($lastReview);

//print_r($studentRefs);

//
//
foreach ($studentRefs as $row) {
    //    echo 'idnumber ' . $row['lref'] . '<br/>';
//echo 'testing' . $row['highestaward'] . $row['mobile'];

    // end the soap related bits

    if (($row['mtg'] != '') or ($row['mtg'] != null)) {
        $mtg_set++;
    }


    // set the rag colour and icon
    if (($row['ragstatus'] == '0') or ($row['ragstatus'] == null)) {
        $colour = 'green';
        $ragicon = '<img src="images/1Green_Ball.png" title="1green" height="20px" width="20px"/>';
        $green++;
    } elseif ($row['ragstatus'] == '1') {
        $colour = 'amber';
        $ragicon = '<img src="images/2Yellow_Ball.png" title="2yellow" height="20px" width="20px"/>';
        $amber++;
    } elseif ($row['ragstatus'] == '2') {
        $ragicon = '<img src="images/3Red_Ball.png" title="3red" height="20px" width="20px"/>';
        $colour = 'red';
        $red++;
    } else {
        $colour = 'green';
        $ragicon = '<img src="images/1Green_Ball.png" title="1green" height="20px" width="20px"/>';
        $green++;
    }

    //            Flightplan stuff

    list($review1, $r2, $review2, $r3, $review3, $r4, $review4) = getFlightplanScores($row['lref'], $mysqli);

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

    //   $badges = getMedals($row->idnumber, $mysqli);
    $query = "SELECT icon FROM badges_link  JOIN badges ON badges_link.badge_id=badges.id  where student_id='" . $row['lref'] . "'";
    //            echo $query;
    $resultBadges = $mysqli->query($query);
    $badges = array();
    while ($row2 = $resultBadges->fetch_object()) {
        $icon = $row2->icon;
        array_push($badges, $icon);

    }

    $activeTargets = $activeTargets + $row['tobe'];
    $targetsAchieved = $targetsAchieved + $row['achieved'];
    $targetsWithdrawn = $targetsWithdrawn + $row['withdrawn'];

    ?>
<tr>
    <td><a
        href="<?php echo $CFG->wwwroot; ?>/blocks/ilp/view.php?courseid=<?php echo $_SESSION['course_code_session']; ?>&id=<?php echo $row['id']; ?>"
        target="_blank"><?php echo $row['firstname']; ?></a>
    </td>
    <td><a
        href="<?php echo $CFG->wwwroot; ?>/blocks/ilp/view.php?courseid=<?php echo $_SESSION['course_code_session']; ?>&id=<?php echo $row['id']; ?>"
        target="_blank"><?php echo $row['lastname']; ?></a>
    </td>
    <td><?php echo $ragicon; ?></td>
    <td><?php echo $row['lref']; ?></td>
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
    <td><?php echo $row['tobe']; ?>/<?php echo $row['achieved']; ?>/<?php echo $row['withdrawnTarget']; ?></td>
    <td><?php echo $row['review']; ?></td>
    <td><?php echo $row['concern']; ?></td>
    <td><?php echo $row['reason']; ?></td>
    <td><?php echo $row['contribution']; ?></td>
    <?php

    $totalReviews = $totalReviews + $row['review'];
    $totalConcerns = $totalConcerns + $row['concern'];
    $totalReasons = $totalReasons + $row['reason'];
    $totalContributions = $totalContributions + $row['contribution'];

    if ($row['review'] != 0) {
        $studentsWithReviews++;
    }

    if ($row['concern'] != 0) {
        $studentsWithConcerns++;
    }

    if ($row['reason'] != 0) {
        $studentsWithReasons++;
    }

    if ($row['contribution'] != 0) {
        $studentsWithContributions++;
    }

    ?>
    <td><?php echo $row['tutor_mtg']; ?></td>
    <td><?php echo $row['mtg']; ?></td>
    <td><?php echo $row['qca']; ?></td>
    <td><?php echo $row['mis_mtg']; ?></td>
    <td><?php echo $review1; ?>
    <td><?php echo $r2; ?></td>
    <td><?php echo $review2; ?></td>
    <td><?php echo $r3; ?></td>
    <td><?php echo $review3; ?></td>
    <td><?php echo $r4; ?></td>
    <td><?php echo $review4; ?></td>
    <td><?php echo checkIfTrue($row['parental']); ?></td>
    <?php if($row['parental'] == 1) {
    $parental_signed++;
}   ; ?>


    <td><?php echo checkIfTrue($row['cast']); ?></td>
    <?php if($row['cast'] == 1) {
            $cast_signed++;
    }   ; ?>
    <td><?php echo checkIfTrueWithNo($row['withdrawn']); ?></td>
    <td><?php echo checkIfTrue($row['mobile']); ?></td>


    <td>
        <div class="badges">
            <?php

            foreach ($badges as $row2) {
                echo '<img src="' . $CFG->wwwroot . '/blocks/ilp/templates/custom/badges/images/' . $row2 . '.png" width="25" height=25" />';
            } ?>
        </div>
    </td>

    <td><?php echo passportMedals($row['highestaward'], $row['parts']); ?></td>
    <td><?php echo $row['date']; ?></td>
    <td><?php echo $row['lastreview']; ?></td>
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
            <option>Employability Passport</option>
        </select>
    </div>
</div>


<p/>

<div class="clearfix">
    <label for="rag" id="rag_title">Select RAG</label>

    <div class="input pad">
        <select name="rag" id="rag">
            <option>--Select--</option>
            <option>Green</option>
            <option>Amber</option>
            <option>Red</option>
        </select>
    </div>
</div>


<div class="clearfix">
    <label for="target_name" id="target_name_title">Target Name</label>
    <label for="target_name" id="target_name_title_progression">To progress to title !WARNING seting this will overwrite any current 'in order to progress to...' set!</label>

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
            <input type="text" id="datepicker" name="date" class="xxlarge">
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
    $querymedals = "SELECT id, name, icon, description, category FROM badges";
    $resultsbadges = $mysqli->query($querymedals);

    $num_rows = $resultsbadges->num_rows;
    //echo 'num rows: ' . $num_rows;
    echo '<h3>Select medals</h3>';
//        echo '**Warning the student must have manual mtg set on the flightplan for medals to work**';
    echo '<table>';
    while ($row = $resultsbadges->fetch_object()) {

        if ($badgeCount == 0) {
            echo '<tr><td>' . $row->name . ' ';
            echo '</td><td><img src="http://' . $domain . '/blocks/ilp/templates/custom/badges/images/' . $row->icon . '.png"/></td>';
            echo '<td>';
            //<input type="checkbox" id="checkbox_medal" name="checkbox_medal[]" value="' . $row['id'] . '" />';
            echo '<input type="radio" name="medal" value="' . $row->id . '"   />';
            echo '</td>';

        } else {
            echo '<td width="20px"></td><td>' . $row->name . ' ';
            echo '</td><td><img src="http://' . $domain . '/blocks/ilp/templates/custom/badges/images/' . $row->icon . '.png"/></td>';

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
</div>
</fieldset>
</div>
</div>


  <?php
//echo '<h3>refds</h3>';
//print_r($studentRefs);
          ?>

<!-- graphs and numbers -->
<div id="totals">
    <h1>Totals</h1>
    <table style="text-align: center;  margin-left: auto; margin-right: auto;" class="totals">
        <tr>
            <th>Active Targets</th>
            <th>Targets Achieved</th>
            <th>Targets Withdrawn</th>
        </tr>
        <td><?php echo $activeTargets; ?></td>
        <td><?php echo $targetsAchieved; ?></td>
        <td><?php echo $targetsWithdrawn; ?></td>
        </tr>
    </table>

    <table style="text-align: center;  margin-left: auto; margin-right: auto;" class="totals">
        <tr>
            <th colspan='2'>Reviews</th>
            <th colspan='2'>Concerns</th>
            <th colspan='2'>Reason for Status Change</th>
            <th colspan='2'>Contributions</th>
        </tr>
        <tr>
            <th>Students with Reviews</th>
            <th>Total Reviews</th>
            <th>Students with Concerns</th>
            <th>Total Concerns</th>
            <th>Students with Reasons</th>
            <th>Total Reasons</th>
            <th>Students with Contributions</th>
            <th>Total Contributions</th>
        </tr>
        <tr>
            <td><?php echo $studentsWithReviews; ?></td>
            <td><?php echo $totalReviews; ?></td>
            <td><?php echo $studentsWithConcerns; ?></td>
            <td><?php echo $totalConcerns; ?></td>
            <td><?php echo $studentsWithReasons; ?></td>
            <td><?php echo $totalReasons; ?></td>
            <td><?php echo $studentsWithContributions; ?></td>
            <td><?php echo $totalContributions; ?></td>
        </tr>
    </table>

    <?php

//flightplan scores 1
    $graph1 = array(
        $reviewOneScore1,
        $reviewTwoScore1,
        $reviewThreeScore1,
        $reviewFourScore1,
        $reviewFiveScore1,
        $reviewSixScore1,
        $noflight1,
    );


    $graph2 = array(
        $reviewOneScore2,
        $reviewTwoScore2,
        $reviewThreeScore2,
        $reviewFourScore2,
        $reviewFiveScore2,
        $reviewSixScore2,
        $noflight2,
    );

//flightplan scores
    $graph3 = array(
        $reviewOneScore3,
        $reviewTwoScore3,
        $reviewThreeScore3,
        $reviewFourScore3,
        $reviewFiveScore3,
        $reviewSixScore3,
        $noflight3,
    );

//flightplan scores
    $graph4 = array(
        $reviewOneScore4,
        $reviewTwoScore4,
        $reviewThreeScore4,
        $reviewFourScore4,
        $reviewFiveScore4,
        $reviewSixScore4,
        $noflight4,
    );

    ?>
    <h1>Fancy charts of great import</h1>
    <?php

    $colours = array('#FF6600', '#FFCC00', '#FFFF00', '#33FF66', '#33CC33', '#339900', '#FF0000');
    makePieChart($graph1, $colours, 'Review 1');
    makePieChart($graph2, $colours, 'Review 2');
    makePieChart($graph3, $colours, 'Review 3');
    makePieChart($graph4, $colours, 'Review 4');


    $graphAtt = array(
        $outstanding,
        $excellent,
        $good,
        $causeForConcern,
        $poor,

    );

    $colours = array('#339900', '#33FF66', '#FFCC00', '#FF6600', '#FF0000');
    $legend = array('Outstanding', 'Excellent', 'Good', 'Concern', 'Poor');
    makePieChart2($graphAtt, $legend, $colours, 'Attendance');

    // RAG pie charts
    $graph = array(
        $green,
        $amber,
        $red,
    );

    $colours = array('#2AFF2A', '#FFD400', '#FF0000');
    $legend = array('Green', 'Amber', 'Red');
    makePieChart2($graph, $legend, $colours, 'RAG Status');

    $mtg_not_set = $count - $mtg_set;
    $graph = array(
        $mtg_set,
        $mtg_not_set,
    );

    $colours = array('#31B131', '#FF0000');
    $colours2 = array('#31B131', '#87AACB', '#FF0000');
    $legend = array('MTG Set', 'MTG Not Set');
    makePieChart2($graph, $legend, $colours, 'P-best Set');

    $parental_not_signed = $count - ($parental_signed + $parental_na);
    $graph = array(
        $parental_signed,
        $parental_na,
        $parental_not_signed,
    );

    $legend = array('Signed', 'N/A', 'Not Signed');
    makePieChart2($graph, $legend, $colours2, 'Parental Agreements');

    $cast_not_signed = $count - $cast_signed;
    $graph = array(
        $cast_signed,
        $cast_not_signed,
    );

    $legend = array('Support', 'No Support');
    makePieChart2($graph, $legend, $colours, 'Cast Support');

    unset($graph1);
    unset($graph2);
    unset($graph3);
    unset($graph4);
    unset($graph);
    unset($graphAtt);
    unset($legend);
    unset($colours);
    unset($colours2);
    ?>
</div>
</div>
<?php

//
?>
<!--Load the javascript to control the datatable - needs to be edited when new coloumns are added-->

<script src="<?php echo $CFG->wwwroot; ?>/blocks/group_targets/bootstrap2/js/dt_bootstrap.js" type="text/javascript"
        charset="utf-8"></script>




<script type="text/javascript">
    $(function () {
        $('#accordion').accordion({
            collapsible:true
        });
    });
</script>

<script type="text/javascript">

    $(function () {
        $("#datepicker").datepicker({
            dateFormat:'dd-mm-yy',
            changeMonth:true,
            changeYear:true
        });
        // tl is the default so don't bother setting it's positio
    });


    function toggleChecked(status) {
        $(".checkbox").each(function () {
            $(this).attr("checked", status);
        })
    }

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


</script>