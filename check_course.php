<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 21/06/11
 * Time: 08:52
 * Gets the employer details
 */
 

$queryEmploy = "SELECT * FROM unit_tracker_employers JOIN unit_tracker_courses ON unit_tracker_employers.id=unit_tracker_courses.employerid WHERE unit_tracker_courses.course_code='" . $_SESSION['course_ref_session'] . "'";
//echo $queryEmploy;
$resultEmploy = mysql_query($queryEmploy);

if (!$resultEmploy) {
    $message = 'Invalid:' . mysql_error();
    die($message);
}

// get the course Id
$query = "SELECT * FROM mdl_course WHERE idnumber='" . $_SESSION['course_ref_session'] . "'";
$resultCourse = mysql_query($query);

if (!$resultCourse) {
    $message = 'Invalid:' . mysql_error();
    die($message);
}

?>