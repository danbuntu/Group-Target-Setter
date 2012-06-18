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


try {
    $report = $client->__soapCall("groupReport2", array($studentRefs));
} catch (SoapFault $e) {
    // handle issues returned by the web service
    echo ' There has been a problem getting the attendance from NG';
}

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
<form name="process" action="process_targets_new.php" method="POST">
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
// the data tables plugins needs to have the coloumn data types types declared as string when outputing images or they won't be sortable
// build the array that hold the columns and column types for the datatable to allow ordering and re-ordering

$string = '{ "sType":"string" }';
$stringn = '{ "sType":"numeric" }';

$tables = "null,null";

if ($ragSet == 1) {
    $tables .= ',' . $string;
}

$tables .= ",null," . $stringn;

if ($targetSet == 1) {
    $tables .= ',' . $string;
}

// print out the reports headers based on the available reports
foreach ($reportsArray as $key => $item) {
    $tables .= ',null';
}

if ($mtgSet = 1) {
    $tables .= ',null,null,null,null';
}


if ($flightplanSet == 1) {
    $tables .= ',' . $stringn . ',' . $string . ',' . $stringn . ',' . $string . ',' . $stringn . ',' . $string . ',' . $stringn . ',' . $string;
}

if ($parentalSet == 1) {
    $tables .= ',' . $string;
}
if ($castSet == 1) {
    $tables .= ',' . $string;
}
if ($withdrawnSet == 1) {
    $tables .= ',' . $string;
}
if ($mobileSet == 1) {
    $tables .= ',' . $string;
}
if ($badgesSet == 1) {
    $tables .= ',' . $string;
}
if ($passportSet == 1) {
    $tables .= ',' . $string;
}

if ($lastReviewSet == 1) {
    $tables .= ',' . 'null,null';
}

unset($report);
//

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
        $ragicon = '<img src="images/1Green_Ball.png" title="green" height="20px" width="20px"/>';
        $green++;
    } elseif ($row['ragstatus'] == '2') {
        $colour = 'amber';
        $ragicon = '<img src="images/2Yellow_Ball.png" title="yellow" height="20px" width="20px"/>';
        $amber++;
    } elseif ($row['ragstatus'] == '1') {
        $ragicon = '<img src="images/3Red_Ball.png" title="red" height="20px" width="20px"/>';
        $colour = 'red';
        $red++;
    } else {
        $colour = 'green';
        $ragicon = '<img src="images/1Green_Ball.png" title="green" height="20px" width="20px"/>';
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
        <?php
    }

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

        <?php
    }

    if ($passportSet == 1) {

        ?>
    <td><?php echo passportMedals($row['highestaward'], $row['parts']); ?></td>

        <?php
    }

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


<!-- pass the course id and userid -->
<input type="hidden" name="courseid" value=" <?php echo $_SESSION['course_code_session'] ?> "/>
<input type="hidden" name="userid" value=" <?php echo $USER->id ?> "/>
<input type="hidden" name="username" value=" <?php echo $USER->username ?> "/>
<input type="hidden" name="groupid" value=" <?php echo trim($_SESSION['course_group_session']) ?> "/>
<!--    <input type="hidden" name="url" value=" --><?php //echo $url ?><!-- "/>-->
<!--    <input type="hidden" name="url2" value=" --><?php //echo $url2 ?><!-- "/>-->

<br/>



<?php
$reports = $DB->get_records('block_ilp_report');
echo '<form action="script.php" method="get">';
echo '<select name="reports" id="reports">';
echo '<option>--Select a report--</option>';
if ($ragSet == 1) {
    ?>
<option value="rag">RAG - Traffic Light</option>
    <?php
}
if ($badgesSet == 1) {
    ?>
<option value="badges">Medals</option>
    <?php
}
if ($passportSet == 1) {
    ?>
<option value="passport">Employability Passport</option>
    <?php
}

foreach ($reports as $report) {
    echo '<option value="', $report->id, '">', $report->name, '</option>';
}

echo '</select>';
?>

<input id="save" class="btn btn-success" type="submit" name="submit_change" value="Select Report"/>

</form>

<?php

if ($showTotals == 1) {
    include('view2_totals.php');
}

if ($showGraphs == 1) {
    include('view2_graphs.php');
} /**/

//
?>

<script>

    function toggleChecked(status) {
        $(".checkbox").each(function () {
            $(this).attr("checked", status);
        })
    }

    /* Default class modification */
    $.extend($.fn.dataTableExt.oStdClasses, {
        "sWrapper":"dataTables_wrapper form-inline"
    });

    /* API method to get paging information */
    $.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings) {
        return {
            "iStart":oSettings._iDisplayStart,
            "iEnd":oSettings.fnDisplayEnd(),
            "iLength":oSettings._iDisplayLength,
            "iTotal":oSettings.fnRecordsTotal(),
            "iFilteredTotal":oSettings.fnRecordsDisplay(),
            "iPage":Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
            "iTotalPages":Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
        };
    }

    /* Bootstrap style pagination control */
    $.extend($.fn.dataTableExt.oPagination, {
        "bootstrap":{
            "fnInit":function (oSettings, nPaging, fnDraw) {
                var oLang = oSettings.oLanguage.oPaginate;
                var fnClickHandler = function (e) {
                    e.preventDefault();
                    if (oSettings.oApi._fnPageChange(oSettings, e.data.action)) {
                        fnDraw(oSettings);
                    }
                };

                $(nPaging).addClass('pagination').append(
                    '<ul>' +
                        '<li class="prev disabled"><a href="#">&larr; ' + oLang.sPrevious + '</a></li>' +
                        '<li class="next disabled"><a href="#">' + oLang.sNext + ' &rarr; </a></li>' +
                        '</ul>'
                );
                var els = $('a', nPaging);
                $(els[0]).bind('click.DT', { action:"previous" }, fnClickHandler);
                $(els[1]).bind('click.DT', { action:"next" }, fnClickHandler);
            },

            "fnUpdate":function (oSettings, fnDraw) {
                var iListLength = 5;
                var oPaging = oSettings.oInstance.fnPagingInfo();
                var an = oSettings.aanFeatures.p;
                var i, j, sClass, iStart, iEnd, iHalf = Math.floor(iListLength / 2);

                if (oPaging.iTotalPages < iListLength) {
                    iStart = 1;
                    iEnd = oPaging.iTotalPages;
                }
                else if (oPaging.iPage <= iHalf) {
                    iStart = 1;
                    iEnd = iListLength;
                } else if (oPaging.iPage >= (oPaging.iTotalPages - iHalf)) {
                    iStart = oPaging.iTotalPages - iListLength + 1;
                    iEnd = oPaging.iTotalPages;
                } else {
                    iStart = oPaging.iPage - iHalf + 1;
                    iEnd = iStart + iListLength - 1;
                }

                for (i = 0, iLen = an.length; i < iLen; i++) {
                    // Remove the middle elements
                    $('li:gt(0)', an[i]).filter(':not(:last)').remove();

                    // Add the new list items and their event handlers
                    for (j = iStart; j <= iEnd; j++) {
                        sClass = (j == oPaging.iPage + 1) ? 'class="active"' : '';
                        $('<li ' + sClass + '><a href="#">' + j + '</a></li>')
                            .insertBefore($('li:last', an[i])[0])
                            .bind('click', function (e) {
                                e.preventDefault();
                                oSettings._iDisplayStart = (parseInt($('a', this).text(), 10) - 1) * oPaging.iLength;
                                fnDraw(oSettings);
                            });
                    }

                    // Add / remove disabled classes from the static elements
                    if (oPaging.iPage === 0) {
                        $('li:first', an[i]).addClass('disabled');
                    } else {
                        $('li:first', an[i]).removeClass('disabled');
                    }

                    if (oPaging.iPage === oPaging.iTotalPages - 1 || oPaging.iTotalPages === 0) {
                        $('li:last', an[i]).addClass('disabled');
                    } else {
                        $('li:last', an[i]).removeClass('disabled');
                    }
                }
            }
        }
    });


    /* Table initialisation */
    $(document).ready(function () {
        $('#example').dataTable({
            "bPaginate":false,
            "sDom":"<'row'<'span6'Rl><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
            "oLanguage":{
                "sLengthMenu":"_MENU_ records per page"
            },
            "aoColumns":[<?php echo $tables; ?>]

        });
    });

</script>