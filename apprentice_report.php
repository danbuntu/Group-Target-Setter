<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 20/06/11
 * Time: 12:04
 * To change this template use File | Settings | File Templates.
 */

require_once("../../config.php");
include('jquery_imports.php');

?>

<div class="demo">
<div class="reportdiv">

<?php

$learnerRef = $_GET['stuID'];
//$moodleId = $_GET['stuId'];
//$courseId = $_GET['var2'];

//$moodleId = '5';
//$courseId = '222';

include('check_course.php');

$query = "SELECT * FROM mdl_user WHERE idnumber='" . $learnerRef . "' AND lastname !='Guardian'";
//    echo $query;
$result = mysql_query($query);
//echo $result;
// Get the student name and results
while ($row = mysql_fetch_assoc($result)) {
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $learnerRef = $row['idnumber'];
    $moodleId = $row['id'];
}

// Get the course employer details
//echo $resultEmploy;
while ($rowEmploy = mysql_fetch_assoc($resultEmploy)) {
    $logo = $rowEmploy['logo'];
    $employName = $rowEmploy['name'];
    $employId = $rowEmploy['id'];
    $coursename = $rowEmploy['coursename'];

}

// get the course details
while ($rowCourse = mysql_fetch_assoc($resultCourse)) {
    $courseCode = $rowCourse['idnumber'];
}

?>
<table style="text-align: center; width: 100%;">
    <tr>
        <td colspan="5">
            <?php echo '<h1>Report for ', $firstname, ' ', $lastname, '</h1>'; ?>
        </td>
        <td colspan="2">
            <?php echo '<img src="./images/' . $logo . '.png" />'; ?>
        </td>
    </tr>
    <tr>
        <th class="report">Training Centre</th>
        <th class="report">Course Code</th>
        <th class="report">Course Name</th>
        <th class="report">Programme Start Date</th>
        <th class="report">Student ID</th>
        <th class="report"><?php echo $employName; ?> ID</th>
        <th class="report">Placement Officer</th>
        <th class="report">Employer</th>
    </tr>

<?php
// Get the current apprentice details
    $query = "SELECT * FROM unit_tracker_users WHERE learner_ref='$learnerRef' AND course_id='$courseCode'";
echo $query;
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {

        $placementOfficer = $row['rep_name'];
        $employer = $row['employer'];
        $programmeStart = $row['programme_start_date'];
        $programmeStart = date("d-m-Y", strtotime($programmeStart));
        $centre = $row['training_centre'];
    }
    ?>

    <tr>
        <td>
            <form name="apprentice" method="Post" action="process_report_form.php">
                <select name="center">
                    <option>--Select--</option>
                    <!--                --><?php
                echo 'center is: ' . $centre;
                    $selected = '';
                    if ($centre == 'Medway') {
                        $selected = 'selected="selected"';
                        echo 'medway';
                    }
//                ?>

                    <option <?php echo $selected; ?>>Medway</option>
<?php
                $selected = '';
                    if ($centre == 'Maidstone') {
                        $selected = 'selected="selected"';
                    }
                    ?>
                    <option <?php echo $selected; ?>>Maidstone</option>
                </select>
        </td>
        <td>
            <?php echo $courseCode; ?>
        </td>

        <td>
            <?php echo $coursename; ?>
        </td>
        <td>
            <input type="text" id="datepicker" name="progammeStart" value="<?php echo $programmeStart; ?>"/>
        </td>
        <td>
            <input type="text" name="ID" value="<?php echo $learnerRef; ?>"/>
        </td>
        <td>
            <input type="text" name="employId" value="<?php echo $employId; ?>"/>
        </td>
        <td>
            <input type="text" name="placementOfficer" value="<?php echo $placementOfficer; ?>"/>
        </td>
        <td>
            <input type="text" name="employer" value="<?php echo $employer; ?>"/>
            <input type="hidden" name="moodleid" value="<?php echo $moodleId; ?>"/>
            <input type="hidden" name="courseCode" value="<?php echo $courseCode; ?>"/>
        </td>

    </tr>

</table>

</br>

<table style="width: 100%; text-align:center;">
    <tr>
        <th class="report">Unit</th>
        <?php        // get the course units
        $query = "SELECT * FROM unit_tracker_units WHERE courseid='$employId'";
//        echo $query;
        $resultUnits = mysql_query($query);

        // Echo the unit names
        $units = array();
        while ($row = mysql_fetch_assoc($resultUnits)) {
            echo '<td class="unit" >', $row['name'], '</td>';
            $unitId = $row['id'];
            array_push($units, $row['id']);
            echo '<input type="hidden" name="unitId[]" value="', $row['id'], '"/>';
        }

//            print_r($units);

        ?>
    </tr>
    <tr>
        <th class="report">Target</th>
<?php
                  // Match the units to results already stored
        foreach ($units as $item) {
            $queryUnit = "SELECT * FROM unit_tracker_user_units WHERE unit_id='$item' AND user_id='$learnerRef'";
            //                    echo $queryUnit;
            $resultUnit = mysql_query($queryUnit);
            $num_rows = mysql_num_rows($resultUnit);
            //        echo 'num rows ' . $num_rows;
            // check for a result as echo an empty cell
            if ($num_rows == 1) {
                while ($row = mysql_fetch_assoc($resultUnit)) {
                    echo '<td><input type="text" name="target[]" value="', $row['target'], '"/></td>';
                }
            } else {
                echo '<td><input type="text" name="target[]" value=""/></td>';
            }
        }
        ?>
    </tr>
    <tr>
        <th class="report">Evidence</th>
<?php
 // Match the units to evidence already stored
        foreach ($units as $item) {
            $queryUnit = "SELECT * FROM unit_tracker_user_units WHERE unit_id='$item' AND user_id='$learnerRef'";
            $resultUnit = mysql_query($queryUnit);
            $num_rows = mysql_num_rows($resultUnit);
            if ($num_rows == 1) {
                while ($row = mysql_fetch_assoc($resultUnit)) {
                    echo '<td><textarea type="text" name="evidence[]" >', $row['evidence'], '</textarea></td>';
                }
            } else {
                echo '<td><textarea name="evidence[]" ></textarea></td>';
            }
        }

        ?>
    </tr>

</table>

<?php

// clear the units array
unset($units);

// hard code variables for testing
//$learnerRef = '04017328';
//$courseCode = '666';
// Get the attendance for the course
$ws = "https://xmlservicesdev.midkent.ac.uk/xmlservices.php?wsdl";

$client = new SoapClient($ws);

try {
    $resultAtt = $client->__soapCall("getAttendanceById", array($learnerRef));
}

catch (SoapFault $e) {
    // handle issues returned by the web service
    echo ' There has been a problem getting the attendance from NG';
}

// Search the array

foreach ($resultAtt as $item) {
    if ($item->Course_code == $courseCode) {
        echo '<table style="text-align: center;">';
        echo '<tr><th class="report" colspan="3">Attendance for ', $courseCode, '</th></tr>';
        echo '<tr><th  class="report">Possbile Attendance</th><th class="report">Actual Attendance</th><th class="report">Attendance as a Percentage</th></tr>';
        echo '<tr><td>', $item->possible, '</td><td>', $item->present, '</td><td>', $item->attendancePercentage, '%</td></tr>';
        echo '</table>';
    }
}


$count = '0';
// start the scores section

// get the effectiveness titles and count used to set table header colspan
$query = "SELECT * FROM unit_tracker_effectiveness";
$result = mysql_query($query);
$effectivenessTitles = array();
while ($row = mysql_fetch_assoc($result)) {
    $effectiveness = array('id' => $row['id'], 'name' => $row['name']);
    echo '<td><input type="hidden" name="effectivenessId[]" value="', $row['id'], '"/></td>';
    array_push($effectivenessTitles, $effectiveness);
    $count = $count + 1;
}

$count2 = $count * 2 + 1;
//print_r($effectivenessTitles);
?>

</br>
<table style="width: 100%;">
    <tr>
        <th class="report" colspan="<?php echo $count2; ?>">Personal Effectiveness [1=Outstanding, 2=Good,
            3=Satisfactory,
            4=Unsatisfactory]
        </th>
    </tr>

<?php

//// The last scores for a student
//    $query = "SELECT * FROM apprentice_effectiveness_user JOIN apprentice_effectiveness ON apprentice_effectiveness_user.effect_id=apprentice_effectiveness.id WHERE learner_ref='$learnerRef' AND effect_id='" . $item['id'] . "' ORDER BY date LIMIT 1";
////     echo $query;
//    $result = mysql_query($query);
//    $num_rows = mysql_num_rows($result);
////    echo 'Num rows: ' . $num_rows;
//    if ($num_rows >= 1) {
//        echo '<tr><th>Previous</th>';
//        while ($row = mysql_fetch_assoc($result)) {
//            echo '<td>', $row['name'], '</td>';
//        }
//        echo '</tr>';
//    } else {
//        echo '<tr><td colspan="', $count2, '">No previous effectiveness records found</td></tr>';
//    }

//    print_r($effectivenessTitles);
    echo  '<tr><th class="report">Measure</th>';
    foreach ($effectivenessTitles as $item) {
        //        echo $item['id'];
        // get previous score
        $queryPrev = "SELECT * FROM unit_tracker_effectiveness_user JOIN unit_tracker_effectiveness ON unit_tracker_effectiveness_user.effect_id=unit_tracker_effectiveness.id WHERE learner_ref='$learnerRef' AND effect_id='" . $item['id'] . "' ORDER BY date DESC LIMIT 1";
//                echo $queryPrev;
        $resultPrev = mysql_query($queryPrev);
        $num_rows = mysql_num_rows($resultPrev);

        if ($num_rows >= 1) {

        while ($rowPrev = mysql_fetch_assoc($resultPrev)) {
            //            echo $rowPrev = $rowPrev['effectiveness_score'];

            echo '<th class="unit">', $item['name'], '</br>Previous: ' . $rowPrev['effectiveness_score'] . '</br><select name="effectiveness[]"/><option>--Select--</option><option>1</option><option>2</option><option>3</option><option>4</option></select></th>';

        }
        } else {
            echo '<th class="unit">', $item['name'], '</br><select name="effectiveness[]"/><option>--Select--</option><option>1</option><option>2</option><option>3</option><option>4</option></select></th>';

        }

    }

// Get the theory lesson comments
// select the latest progress review for the student linked to the course
    ?>
    </tr>
    <tr>
        <th class="report">Comments</th>
        <?php foreach ($effectivenessTitles as $item) {

        echo '<td style="text-align: center;"><textarea  rows="2" name="effectivenessComments[]" ></textarea></td>';

    }
        ?>
    </tr>
</table>

</br>

<table style="width: 100%;">
    <tr>
        <th class="report" style="width: 50%;">Last PLP Progress Review for this Course</th>
    </tr>
    <tr>
        <td style="width: 50%;">
<?php
$query = "SELECT * FROM mdl_ilpconcern_posts WHERE  targetcourse='" . $_SESSION['course_code_session'] . "' AND setforuserid='$moodleId' ORDER BY timemodified desc LIMIT 1";
    $result = mysql_query($query);
//echo $query;
            while ($row = mysql_fetch_assoc($result)) {

                echo $row['concernset'];
                echo '<input type="hidden" name="comments" value="' . htmlspecialchars($row['concernset']) . '"/>';
            }
            ?>
        </td>
    </tr>
</table>

</br>

<table style="width: 100%;">
    <tr>
        <th class="report" colspan="3">Targets</th>
    </tr>
    <tr>
        <th class="unit" width="75%">Actions Required</th>
        <th class="unit" width="15%">By Whom</th>
        <th class="unit" width="10%">By When</th>
    </tr>

<?php
// Get the open targets for the student assigned to the course
    $query = "SELECT * FROM mdl_ilptarget_posts WHERE setforuserid='$moodleId' AND targetcourse='$courseId' AND status='0' AND format='1'";
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        echo '<tr><td>', $row['targetset'], '</td><td style="text-align: center;">', getMoodleUser($row['setbyuserid']), '</td><td style="text-align: center;">', date("d-m-Y", ($row['timemodified'])), '</td></tr>';
    }

    ?>

    </tr>
</table>

<?php

mysql_close($link);

?>

<?php

function getMoodleUser($id)
{
    $query = "SELECT * FROM mdl_user WHERE id='$id'";
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        $name = $row['firstname'] . ' ' . $row['lastname'];
    }
    return $name;
}

?>

<!-- Signature block -->
</br>

<table width="100%">
    <tr>
        <th class="report" colspan="4">Learner Details</th>
        <th class="report" colspan="4">Training Centre Rep Details</th>
        <th class="report" colspan="4">Period of the Report</th>
    </tr>
    <tr>
        <th class="unit">Name</th>
        <td><?php echo $firstname, ' ', $lastname; ?></td>
        <th class="unit">Date</th>
        <td><input type="text" id="datepicker2" name="studentDate"/></td>
        <th class="unit">Name</th>
        <td><input type="text" name="repName"/></td>
        <th class="unit">Date</th>
        <td><input type="text" id="datepicker3" name="repDate"/></td>
        <th class="unit">From</th>
        <td><input type="text" id="datepicker4" name="fromDate"/></td>
        <th class="unit">To</th>
        <td><input type="text" id="datepicker5" name="toDate"/></td>
    </tr>

</table>
<input type="submit" name="submit"/>
</form>

</div>
</div>


<?php

include('report_scripts.php');

?>