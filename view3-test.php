<?php
/**
 * Created by JetBrains PhpStorm.
 * User: DATTWOOD
 * Date: 24/01/12
 * Time: 15:05
 * To change this template use File | Settings | File Templates.
 */
require_once("../../config.php");
  include('soap_connection.php');
require_once('moodle_connection_mysqli.php');
    $_SESSION['course_group_session'] = '794';

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


$querystudents = "SELECT distinct u.id, u.firstname, u.lastname, u.idnumber, u.username FROM mdl_role_assignments a JOIN mdl_user u on a.userid=u.id LEFT JOIN mdl_groups_members gm ON gm.userid=a.userid WHERE contextid='57510' AND a.roleid='5' order by lastname";

echo $querystudents;

$resultsstudents = $mysqli->query($querystudents);
$num_students = $resultsstudents->num_rows;
//echo 'num rows: ', $num_students;
$count = 0;

print_r($resultstudents);


while ($row = $resultsstudents->fetch_object()) {

       echo 'idnumber ' . $row->idnumber . '<br/>';

       $report = $client->__soapCall("groupReport", array($row->idnumber));
//   //
       var_dump($report);
//   ////
//       while ($item = $report) {
//           echo 'test';
////             echo 'totla att: ' . $item['TotalAtt'];
////   //        $totalAttendance = $item['TotalAtt'];
////           echo 'totalatt var: ' . $totalAttendance;
////             echo 'mis1: ' . $item['Mis'][0];
////                       echo 'mis1: ' . $item['Mis'][1];
////                       echo 'mobile: ' . $item['Mobile'];
////
//       }

//    foreach ($report as $item) {
////        echo $item;
//        echo ' totaltt is: ' . $item[TotalAtt];
//    }


    foreach ($report as $key => $item) {
        echo 'test';
        echo $key;
    }

}