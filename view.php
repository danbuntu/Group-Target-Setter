<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<!--Doctype needs to be declared to allow the fixed table heading to work else IE 8 operates in quirks mode-->

<?php

require_once("../../config.php");
include('moodle_connection_mysqli.php');
//include ('access_context.php');
include('jquery_imports.php');
// Grid from : http://www.datatables.net
//include('academic_year_function.php');
include('connection.php');
include('chart_function.php');
include('report_functions.php');
include('accord_functions.php');
include('soap_connection.php');

//                print_r($_SESSION);


//try {
//    $resultAtt = $client->__soapCall("getAcademicYear", array());
//}
//
//catch (SoapFault $e) {
//    // handle issues returned by the web service
//    echo ' There has been a problem getting the AcademicYear';
//}

$resultAtt = $client->__soapCall("getAcademicYear", array(''));

//$lastArray = end($resultAtt);
//$academicyear = $lastArray->academicyear;

foreach ($resultAtt as $item) {
    $academicyear = $item['academicyear'];
}
//        echo 'year is' . $academicyear;


?>
<!-- Load the main javscript Asynchronously-->
<script id="myscript" type="text/javascript">

    (function () {
        var myscript = document.createElement('script');
        myscript.type = 'text/javascript';
        myscript.src = ('jscripts2.js');
        var s = document.getElementById('myscript');
        s.parentNode.insertBefore(myscript, s);
    })();

</script>



<?php

$outstanding = 0;
$excellent = 0;
$good = 0;
$causeForConcern = 0;
$poor = 0;

// get the url - used to set the badges images url
$domain = $_SERVER['HTTP_HOST'];
$scriptname = $_SERVER['SCRIPT_NAME'];
$path = $_SERVER['QUERY_STRING'];
// echo $domain;
// echo $scriptname;
// echo $path;
$url = 'http://' . $domain . $scriptname . '?' . $path;
$url2 = 'http://' . $domain . '/course/view.php?id=' . $courseid;

//echo  $academicyear;
global $USER, $CFG, $COURSE;

// Get the course id based on the course that the user has jumped in form
if (!empty($_GET['courseid'])) {
    $_SESSION['course_code_session'] = $_GET['courseid'];
}

if (!empty($_GET['var1'])) {
    $_SESSION['course_context_session'] = $_GET['var1'];
}

//print_r($USER);


//$courseid = $_GET['courseid'];
//$contextid = $_GET['var1'];
//$status = $_GET['status'];
//$userid = $USER->id;

//echo $courseid;
//echo $contextid;
//echo 'userid is ' . $userid;
//echo 'status is ' . $status;
$url2 = $CFG->wwwroot . '/blocks/group_targets/view.php?courseid=' . $courseid . '&var1=' . $userid;

//echo '<br> Userid is ' . $userid;


?>

<div id="page">
<div id="layout">
<div class="demo">
<?php

//$courseid = $_GET['courseid'];

// Defualt the type to be the coruse id. This gets overridden when the user selects a course from the drop down
//$type = $courseid;
////echo 'type is: ', $type;
//$courseContextId = $_GET['var1'];

// the order is important here or the correct information isn't loaded into the navigation header
include('process_forms.php');
include('navigation.php');
?>

<table width="100%">
    <tr>
        <td>
        <td rowspan="2" style="text-align: right;">
            <!--           <button id="opener">Open Dialog</button>-->
            <img src="images/help-icon.png" height="50px" width="50px" id="opener"/>
            <img src="images/User-Group-icon.png" width="156" height="156" alt="User-Group-icon"/>
        </td>
    <tr>
        <td>
            <h1>Course reports and target setting: </h1> <br/>
            This page can take a while to load for course with lots of students - please bear with it</p>
        </td>

    </tr>
</table>

<?php
include('course_select_dropdown.php');
?>

<table>
<tr>
<td>

<?php

//echo 'type is: ' . $type;
$courseContextId = getCourseContextID($type);

//echo 'group id is: ' . $group_id;
//select and show all students on this course used roleid 5 to indentify studentsserver
// $querystudents = "SELECT a.userid, firstname, lastname FROM {$CFG->prefix}role_assignments a JOIN {$CFG->prefix}user u on a.userid=u.id where contextid='" . $contextid . "' AND a.roleid='5' order by lastname";
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

$resultsstudents = mysql_query($querystudents);

$count = 0;
?>

<div>
    <table>
        <tr>
            <th style="vertical-align: middle;">Students on this course</th>
            <td style="vertical-align: middle;">Select / Deselect all<input type="checkbox"
                                                                            onclick="toggleChecked(this.checked)"></td>
        </tr>
    </table>

    <?php

// Reset all graph score to zero
    include('zero-scores.php');

// Get the date 1 month ago
    $dateMonth = getDateMonth();

//echo 'date is ' . $dateMonth;
    ?>
</div>

<form name="process" action="process_targets.php" method="POST">

<?php
echo '<table  id="example"  style="text-align: center;"><thead>';
echo '<tr><th>Name</th>
<th>Surname</th>
<th>RAG</th>
<th>ID</th>
<th>Select</th>
<th>Attendance</th>
<th>Targets</th>
<th>Reviews</th>
<th>Concerns</th>
<th>Reasons</th>
<th>Contributions</th>
<th>T-MTG</th>
<th>P-Best</th>
<th>QCA</th>
<th>MIS MTG</th>
<th>R 1</th>
<th>R2g</th>
<th>R 2</th><th>R3g</th>
<th>R 3</th>
<th>R4g</th>
<th>R 4</th>
<th>Parental</th>
<th>Cast</th>
<th>Withdrawn</th>
<th>Mobile</th></th>
<th>Medals</th></tr>';
echo '</thead><tbody>';
while ($row = mysql_fetch_assoc($resultsstudents)) {
    $id = $row['id'];
    //echo 'IDs are ' . $id;
    $studentMoodleId = $row["id"];
    $firstname = $row["firstname"];
    $lastname = $row["lastname"];
    $studentId = $row["idnumber"];
    $username = $row["username"];
    //$status = $row['status'];
    //
    //get the rag
    $queryrag = "SELECT status FROM mdl_ilpconcern_status WHERE userid='" . $id . "'";
    $resultrag = mysql_query($queryrag);
    $num_rows_rag = mysql_num_rows($resultrag);

    if ($num_rows_rag > 0) {
        while ($row2 = mysql_fetch_assoc($resultrag)) {
            $status = $row2['status'];
        }
    } else
        $status = '0';

    if (($status == '0') or ($status == null)) {
        $colour = 'green';
        $ragicon = '<img src="images/1Green_Ball.png" title="1green" height="20px" width="20px"/>';
        $green++;
    } elseif ($status == '1') {
        $colour = 'amber';
        $ragicon = '<img src="images/2Yellow_Ball.png" title="2yellow" height="20px" width="20px"/>';
        $amber++;
    } elseif ($status == '2') {
        $ragicon = '<img src="images/3Red_Ball.png" title="3red" height="20px" width="20px"/>';
        $colour = 'red';
        $red++;
    }

    $studentId = substr($studentId, 0, 8);

    $totalattendance = getAttendance($academicyear, $studentId);

    $totalattendance = round($totalattendance);

    if ($totalattendance >= '100') {
        $outstanding++;
    } elseif ($totalattendance >= '95') {
        $excellent = $excellent + 1;
    } elseif ($totalattendance <= '94' && $totalattendance >= '90') {
        $good++;
    } elseif ($totalattendance <= '89' && $totalattendance >= '80') {
        $causeForConcern++;
    } elseif ($totalattendance <= '80') {
        $poor++;
    }


    //  echo 'total att is form func ' . $totalattendance;
    //check if count is odd or even to create two columns

    // Get the students targets
    list($tobe, $withdrawn, $acheived, $target_month, $target_month_with, $target_month_ach) = getTargets($studentMoodleId, $target_month, $target_month_with, $target_month_ach, $dateMonth);

    $activeTargets = $activeTargets + $tobe;
    $targetsAchieved = $targetsAchieved + $achieved;
    $targetsWithdrawn = $targetsWithdrawn + $withdrawn;

    //    $num_reviews = getReviews($studentMoodleId);

    // Get the student concersn, reviews etc
    list($reviews, $concerns, $reasons, $contributions, $month_review, $month_concern, $month_reason, $month_contribs) = getReviews($studentMoodleId, $month_review, $month_concern, $month_reason, $month_contribs, $dateMonth);

    if ($reviews !== 0) {
        $studentsWithReviews++;
    }
    $totalReviews = $totalReviews + $reviews;


    if ($concerns !== 0) {
        $studentsWithConcerns++;
    }
    $totalConcerns = $totalConcerns + $concerns;


    if ($reasons !== 0) {
        $studentsWithReasons++;
    }
    $totalReasons = $totalReasons + $reasons;


    if ($contributions !== 0) {
        $studentsWithContributions++;
    }
    $totalContributions = $totalContributions + $contributions;

    //    $studentId = '11085082';
    $primaryQual = getPrimaryQual($studentId, $client);

    //    print_r($primaryQual);

    // Get the mis qca points
    $qca = getQCA($academicyear, $studentId);

    $averagescore = $qca;
    $ladNum = '';
    $qualTitle = '';
    $level = '';
    $qualType = '';
    $mtg_grade = '';


    foreach ($primaryQual as $item) {
        $ladNum = $item['LAD_Aim'];
        $qualTitle = trim($item['Qual_Title']);
        $level = trim($item['Notional_Level']);
        $qualType = trim($item['Qual_type']);

        //        echo 'ladnum ' . $ladNum . ' qca ' . $averagescore . ' mtg_grade ' . $mtg_grade . ' qualtype ' . $qualTitle . ' level ' . $level . ' qual type ' . $qualType;
        //    echo 'a hit ' . $ladNum;
        $mtg_grade = getMtgMis($ladNum, $averagescore, $mtg_grade, $qualTitle, $level, $qualType);


        //             getMtgMis($ladNum, $averagescore, $mtg_grade, $qualTitle, $level, $qualType)

        //    echo'mtg is', $mtg_grade;
    }

    //
    //    // Get the mt


    list($mtg, $pbest) = getMTGS($studentId);

    //     get the mis mtg scores
    try {
        $resultStudent = $client->getPrimaryQualById($student_number);
    }

    catch (SoapFault $e) {
        // handle issues returned by the web service
        echo '<br/>';
        echo "No MIS records found for user " . $CFG->name;
        echo " either the there is a problem with MIS or more likely the Student isn't enrolled for this year";
        echo '<br/>Please wait a few moments and refresh the page';
    }

    $resultStudent = $client->__soapCall("getPrimaryQualById", array($student_number));

    $year = date("Y", $academicYearStamp);

    if (($mtg != '') or ($mtg != null)) {
        $mtg_set++;
    }

    $result = '';
    //    echo 'student id ' . $studentId;
    list($review1, $review2, $review3, $review4) = getFlightplanScores($studentId, $mysqli);

    //    echo 'last review ' .  $review1;

    list($r2Colour, $r2) = compareReviews($review1, $review2);
    list($r3Colour, $r3) = compareReviews($review2, $review3);
    list($r4Colour, $r4) = compareReviews($review3, $review4);


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

    $signed = getParentalAgreement($studentId, $client);
    //   print_r($reviews);

//        echo ' signed is:' . $signed;

    if ($signed == '1') {
        //        echo 'yesy hit';
        $signed = '<img src="./images/tick-icon.png" height"20px" width="20px"/>';
        $parental_signed++;
    } elseif ($signed == '2') {
        $signed = 'N/A';
        $parental_na++;
    } else {
        $signed = '<img src="./images/delete-icon.png" height"20px" width="20px"/>';
    }

    $cast = getCastSupport($studentId);

    if ($cast == '1') {
        $cast = '<img src="./images/tick-icon.png" height"20px" width="20px"/>';
        $cast_signed++;
    } else {
        $cast = '<img src="./images/delete-icon.png" height"20px" width="20px"/>';
    }

    $withDrawnFrom = checkIfStudentWithdrawn($studentId, $client);
    if (trim($withDrawnFrom) == '1') {
        $withDrawnFromPic = '<img src="./images/delete-icon.png" height"20px" width="20px"/>';
    } else {
        $withDrawnFromPic = '';
    }

    $mobile = checkIfHasMobile($studentMoodleId);
    if ($mobile == 1) {
        $mobile = '<img src="./images/tick-icon.png" height"20px" width="20px"/>';
    } else {
        $mobile = '';
    }

    $badges = getMedals($studentId);

    //  print_r($badges);
    //odd
    echo '<tr class="even">';
    echo '<td><a href="' . $CFG->wwwroot . '/blocks/ilp/view.php?courseid=' . $_SESSION['course_code_session'] . '&id=' . $studentMoodleId . '" target="_blank"/>' . $firstname . '</td>';
    echo '<td><a href="' . $CFG->wwwroot . '/blocks/ilp/view.php?courseid=' . $_SESSION['course_code_session'] . '&id=' . $studentMoodleId . '" target="_blank"/>' . $lastname . '</td>';
    echo '<td>' . $ragicon . '</td>';
    echo '<td>' . $studentId . '</td>';
    echo '<td><input type="checkbox" class="checkbox" name="checkbox[]" value="' . $studentMoodleId . '"   /></td>';
    echo '<td>' . round($totalattendance) . '</td>';
    echo '<td>' . $tobe . '/' . $acheived . '/' . $withdrawn . '</td>';
    echo '<td>' . $reviews . '</td>';
    echo '<td>' . $concerns . '</td>';
    echo '<td>' . $reasons . '</td>';
    echo '<td>' . $contributions . '</td>';
    echo '<td>' . $pbest . '</td>';
    echo '<td>' . $mtg . '</td>';
    echo '<td>' . $qca . '</td>';
    echo '<td>' . substr($mtg_grade, 1) . '</td>';
    echo '<td>' . $review1 . '</td>';
    echo '<td>' . $r2 . '</td>';
    echo '<td>' . $review2 . '</td>';
    echo '<td>' . $r3 . '</td>';
    echo '<td>' . $review3 . '</td>';
    echo '<td>' . $r4 . '</td>';
    echo '<td>' . $review4 . '</td>';
    echo '<td>' . $signed . '</td>';
    echo '<td>' . $cast . '</td>';
    echo '<td>' . $withDrawnFromPic . '</td>';
    echo '<td>' . $mobile . '</td>';
    echo '<td style="text-align:left;">';

    foreach ($badges as $row) {
        echo '<img src="' . $CFG->wwwroot . '/blocks/ilp/templates/custom/badges/images/' . $row . '.png" width="25" height=25" />';
    }
    echo '</td></tr>';
    $count = $count + 1;
}
?>
</tbody>
</table>
</br>

<div id="multiOpenAccordion3">
    <?php accord_first('Set Group Targets - click to expand'); ?>
    <h3>Select Type and Enter Details</h3>
    Targets are set for students selected above</p>
    <select name="type" id="select_review">
        <option>--Select--</option>
        <option>Target</option>
        <option>Progress Review</option>
        <option>Concern</option>
        <option>Reason for Status Change</option>
        <option>Contribution</option>
        <option>RAG - Traffic Light</option>
        <option>Medals</option>
        <option>Progression Targets</option>
        <option>Employability Passport</option>
    </select>

    </p>
    <label id="rag_title" for="rag"><br/><b>Select RAG</b></label>
    <select name="rag" id="rag">
        <option>--Select--</option>
        <option>Green</option>
        <option>Amber</option>
        <option>Red</option>
    </select>

    <label id="target_name_title" for="target_name"><b>Target Name</b></label>
    <input id="target_name" type="text" name="title" size="52"/>

    <div class="demo">
        <label id="datepicker_title" for="datepicker"><br/><b>Target Deadline</b></label>
        <input type="text" id="datepicker" name="date">
    </div>

    <label id="details_title" for="details"><b>Enter Details</b></label>
    <textarea name="target" rows="8" cols="40" id="details"></textarea>


    <br/><label id="checkbox_title">The target is related to this course</label><input type="checkbox" id="checkbox"
                                                                                       name="course_related" value="ON"
                                                                                       checked/>


    <div id="medals_div">

        <?php
        $badgeCount == 0;
//    mysql_select_db('medals') or die('Unable to select the database');
        $querymedals = "SELECT * FROM badges";
        $resultsbadges = mysql_query($querymedals);
        $num_rows = mysql_num_rows($resultsbadges);
        //echo 'num rows: ' . $num_rows;
        echo '<h3>Select medals</h3>';
        echo '**Warning the student must have manual mtg set on the flightplan for medals to work**';
        echo '<table>';
        while ($row = mysql_fetch_assoc($resultsbadges)) {

            if ($badgeCount == 0) {

                echo '<tr><td>' . $row['name'] . ' ';
                echo '</td><td><img src="http://' . $domain . '/blocks/ilp/templates/custom/badges/images/' . $row['icon'] . '.png"/></td>';
                echo '<td>';
                //<input type="checkbox" id="checkbox_medal" name="checkbox_medal[]" value="' . $row['id'] . '" />';
                echo '<input type="radio" name="medal" value="' . $row['id'] . '"   />';
                echo '</td>';

            } else {
                echo '<td width="20px"></td><td>' . $row['name'] . ' ';
                echo '</td><td><img src="http://' . $domain . '/blocks/ilp/templates/custom/badges/images/' . $row['icon'] . '.png"/></td>';

                echo '<td>';
                //<input type="checkbox" id="checkbox_medal" name="checkbox_medal[]" value="' . $row['id'] . '" />';
                echo '<input type="radio" name="medal" value="' . $row['id'] . '"   />';
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

    <!-- pass the course id and userid -->
    <input type="hidden" name="courseid" value=" <?php echo $_SESSION['course_code_session'] ?> "/>
    <input type="hidden" name="userid" value=" <?php echo $USER->id ?> "/>
    <input type="hidden" name="username" value=" <?php echo $USER->username ?> "/>
    <!--    <input type="hidden" name="url" value=" --><?php //echo $url ?><!-- "/>-->
    <!--    <input type="hidden" name="url2" value=" --><?php //echo $url2 ?><!-- "/>-->

    <br/>

    <input id="save" type="submit" name="submit" value="submit_change"/>


</div>
<?php accord_last();

mysql_select_db('moodle');
//include('text_form.php');

// End the form
echo '</form>';

?>

</br>

<h2 class="ui-widget-header"> Totals</h2>
<table style="text-align: center;  margin-left: auto; margin-right: auto;">
    <tr>
        <th colspan='3'>Targets</th>
    </tr>
    <tr>
        <th>Active Targets</th>
        <th>Targets Achieved</th>
        <th>Targets Withdrawn</th>
    </tr>
    <td><?php echo $activeTargets; ?></td>
    <td><?php echo $targetsAchieved; ?></td>
    </tr>
</table>

<table style="text-align: center;  margin-left: auto; margin-right: auto;">
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

</br>
<h2 class="ui-widget-header"> Fancy charts</h2>


<?php
include('graphs.php');
?>

</div>
</div>

<!-- end layout -->
</div>
<!-- end page -->
</div>
</div>
<!-- Help dialog -->
<div class="demo">
    <div id="dialog" title="Help Me">
        <p><b>Name:</b> Clicking on a name will take you to that students plp
        </p>

        <p><b>Id:</b> The students id number</p>

        <p><b>Attendance:</b> The student current attendance as a percentage</p>

        <p><b>Tagets:</b> Number of targets to be achieved/ number of targets achieved / number of targets withdrawn</p>

        <p><b>Reviews:</b> Number of reviews</p>

        <p><b>Concerns:</b> Number of concerns</p>

        <p><b>Reasons:</b> Number of reasons for status change</p>

        <p><b>Contributions:</b> Number of Contributions</p>

        <p><b>T-MTG:</b> The students manual MTG as set by the tutor</p>

        <p><b>T-Best:</b> The students personal best as set by the tutor</p>

        <p><b>MIS MTG:</b> The students MTG as noted in MIS - a hyphen means MIS has no record</p>

        <p><b>R1 - R4:</b> The students last 4 scores from the flightplan reviews. X denotes no review. Colour indicates
            status
            in
            relation to the last review where red is less, green is improvement and amber is no change</p>

        <p><b>R1g - R4g:</b> The direction of the arrow shows the change in flightplan score in relation to the last one
        </p>

        <p><b>Parent:</b> Has the student had a parental agreement signed?</p>

        <p><b>Cast:</b> Does the student have cast support?</p>

        <p><b>Withdrawn:</b> A cross means the student has withdrawn or is a tutor and should be removed from the course
            or moved to a more appropriate role</p>

        <p><b>Mobile:</b> A tick means that the student has a mobile number registered in the system</p>

        <p><b>Medals:</b> Shows the students currently awarded medals</p>

    </div>
</div>

<!--<script src="libraries/RGraph.common.core.js"></script>-->
<!--<script src="libraries/RGraph.line.js"></script>-->
<?php
//    include('jscripts.php');

$mysqli->close;
mysql_close($link);

?>


<!--Script to run the submit button graphic - doesn't work when added tot he main js script files-->
<script type="text/javascript">
    $(document).ready(function () {
        $('#save').hover(function () {
            $(this).addClass('mhover')
        }, function () {
            $(this).removeClass('mhover');
        });
    })
</script>