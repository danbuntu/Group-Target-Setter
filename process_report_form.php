<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 22/06/11
 * Time: 10:04
 * To change this template use File | Settings | File Templates.
 */

require_once("../../config.php");

//print_r($_SESSION);
//
$center = $_POST['center'];
$courseCode = $_POST['courseCode'];
$progammeStart = $_POST['progammeStart'];
$moodleId = $_POST['moodleid'];
$ID = $_POST['ID'];
$placementOfficer = $_POST['placementOfficer'];
$employId = $_POST['employId'];
$employer = $_POST['employer'];
$unitId = $_POST['unitId'];
$target = $_POST['target'];
$evidence = $_POST['evidence'];
$effectivenessId = $_POST['effectivenessId'];
//$effectiveness = $_POST['effectiveness'];
$comments = $_POST['comments'];
$anyotherComments = $_POST['anyotherComments'];
$repName = $_POST['repName'];
$studentDate = $_POST['studentDate'];
$repDate = $_POST['repDate'];
$fromDate = $_POST['fromDate'];
$toDate = $_POST['toDate'];
//$effectivenessComments = $_POST['effectivenessComments'];
// check for a value
checkDropdown($center, 'Training Centre', $ID);

$todaysDate = date("Y-m-d H:i:s");
$progammeStart = date("Y-m-d H:i:s", strtotime($progammeStart));

echo '<h2>details</h2>';
echo $center;
echo 'course code is ' . $courseCode;
echo $progammeStart;
echo $ID;
echo $placementOfficer;
echo $employId;
echo $employer;
echo 'unitid ';
print_r($unitId);
print_r($target);
print_r($evidence);
echo 'eefectiveness id: ';
print_r($effectivenessId);
print_r($effectiveness);
print_r($comments);
echo $comments;
echo $anyotherComments;
echo 'moodle is: ' . $moodleId;
echo 'coure code is: ' . $courseCode;
// Write in the user details
$query = "SELECT * FROM unit_tracker_users WHERE learner_ref='" . $ID . "' AND course_id='" . $courseCode . "'";
echo $query;
$result = mysql_query($query);
$num_rows = mysql_num_rows($result);

if ($num_rows >= 1) {
    echo 'there is already a student record for this course so update it';
    $query = "UPDATE unit_tracker_users
    SET moodle_id='" . $moodleId . "', learner_ref='" . $ID . "', rep_name='" . $placementOfficer . "', employer_id='" . $employId . "', training_centre='" . $center . "', programme_start_date='" . $progammeStart . "', officer='" . $placementOfficer . "', course_id='" . $courseCode . "', employer='" . $employer . "'
    WHERE learner_ref='" . $ID . "' AND course_id='" . $courseCode . "'";
    echo $query;
    mysql_query($query);
} else {
    echo 'there is no student record for this course so create it';
    $query = "INSERT INTO unit_tracker_users (moodle_id, learner_ref, rep_name, employer_id, training_centre, programme_start_date, officer, course_id, employer)
    VALUES ('" . $moodleId . "','" . $ID . "','" . $placementOfficer . "','" . $employId . "','" . $center . "','" . $progammeStart . "','" . $placementOfficer . "','" . $courseCode . "','" . $employer . "')";
    echo $query;
    mysql_query($query);
}

// write in the unit scores
echo 'Output units scores </br>';
for ($i = 0; $i < count($_POST['unitId']); $i++) {
    $unitId = $_POST['unitId'][$i];
    $target = $_POST['target'][$i];
    $evidence = $_POST['evidence'][$i];
    echo $unitId . ' ' . $target . ' ' . $evidence . '</br>';

    // check for an already existing record
    $queryCheck = "SELECT * FROM unit_tracker_user_units WHERE unit_id='" . $unitId . "' AND user_id='" . $ID . "'";
    echo $queryCheck;
    $resultCheck = mysql_query($queryCheck);
    $num_rows_check = mysql_num_rows($resultCheck);
    echo  'rows check', $num_rows_check;
    if ($num_rows_check >= 1) {
        echo 'a record already exists, updating';
        echo 'targetis: ' . $target;
        $query = "UPDATE unit_tracker_user_units SET target='$target', evidence='$evidence' WHERE unit_id='" . $unitId . "' AND user_id='" . $ID . "'";
        echo $query;
        mysql_query($query);
    } else {
        echo 'the record doesn\'t exist creating';
        $query = "INSERT INTO unit_tracker_user_units (unit_id, user_id, target, evidence) VALUES ('$unitId', '$ID', '$target', '$evidence')";
        echo $query;
        mysql_query($query);
    }
    //    $query = "INSERT INTO unit_tracker_effectiveness_user (learner_ref, effect_id, comment, date) VALUES ($ID, $effectivenessId, $effectiveness, $effectivenessComments $todaysDate)";
    //echo $query;
}

// Write in the effectiveness scores
echo 'Output effectiveness scores </br>';
for ($i = 0; $i < count($_POST['effectivenessId']); $i++) {
    $effectivenessId = $_POST['effectivenessId'][$i];
    $effectiveness = $_POST['effectiveness'][$i];
    $effectivenessComments = $_POST['effectivenessComments'][$i];
    echo $effectivenessId . ' ' . $effectiveness . ' ' . $effectivenessComments . '</br>';
    $query = "INSERT INTO unit_tracker_effectiveness_user (learner_ref, effect_id, effectiveness_score, comment, date) VALUES ('$ID', '$effectivenessId', '$effectiveness', '$effectivenessComments', '$todaysDate')";
    echo $query;
    mysql_query($query);
}

// send back to the report screen
       echo '<meta http-equiv="refresh" content="0; url=/blocks/group_targets/tracker.php">';

function checkDropdown($value, $test, $moodleId)
{
    if ($value == '--Select--') {
        echo 'You must enter a value for ' , $test;
             echo '<meta http-equiv="refresh" content="3; url=/blocks/group_targets/apprentice_report.php?stuID=' . $moodleId , '">';
        break;

    }
}

?>