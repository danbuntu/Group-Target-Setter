<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 15/07/11
 * Time: 16:24
 * To change this template use File | Settings | File Templates.
 */

if (isset($_POST['filter'])) {
    //    $courseContextId = htmlentities($_POST['course']);
    //    $type = htmlentities($_POST['course']);
//  echo '<font color="red" >group is: ' .  htmlentities($_POST['group']) . '</font>';
    $_SESSION['course_code_session'] = htmlentities($_POST['course']);
//echo ' codde session is ' .  $_SESSION['course_code_session'];
    $_SESSION['course_context_session'] = getCourseContextID(htmlentities($_POST['course']));

    //    $_SESSION['type'] = htmlentities($_POST['type']);
    //      echo 'type is: ' . $_SESSION['type'];

}


if (isset($_POST['filtergroups'])) {
    //    $courseid = htmlentities($_POST['group']);
    //    $courseContextId = htmlentities($_POST['context']);

    $_SESSION['course_code_session'] = htmlentities($_POST['group']);
    $_SESSION['course_context_session'] = htmlentities($_POST['context']);
}

$group_id = 'All groups';
$_SESSION['course_group_session'] = 'All groups';
//catch and check the filter
if (isset($_POST['groups'])) {
//    echo ' groups set';
    $_SESSION['course_group_session'] = htmlentities($_POST['groups']);
    $_SESSION['course_code_session'] = htmlentities($_POST['courseid']);
    $_SESSION['course_context_session'] = getCourseContextID($_SESSION['course_code_session']);
    //    $type = htmlentities($_POST['courseid']);
    //    $_SESSION['course_code_session'] = htmlentities($_POST['courseid']);
    //get the group id
    //        echo 'group name is: ' . $group_id;
    if ($_SESSION['course_group_session'] != 'All groups') {
        $query_group_id = "SELECT * FROM {$CFG->prefix}groups WHERE id='" . $_SESSION['course_group_session'] . "' AND courseid='" . $_SESSION['course_code_session'] . "'";
//                  echo $query_group_id;
        //        echo ' all groups set';
        $result_group_id = mysql_query($query_group_id)  or die(mysql_error());
        while ($row = mysql_fetch_assoc($result_group_id)) {
            $_SESSION['course_group_session'] = $row['id'];
            //            echo 'not all groups';
            $num_rows = mysql_num_rows($result_group_id);
            //            echo 'num rows' . $num_rows;

        }
    } else {
        $_SESSION['course_group_session'] = 'All groups';
    }
}

?>