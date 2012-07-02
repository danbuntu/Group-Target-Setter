<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../../config.php");

global $USER, $CFG, $COURSE;

$courseid = $_POST['courseid'];
$mark_as = $_POST['mark_as'];
$userid = $_POST['userid'];

// split the passed users variable into an array
$users = explode(',', $_POST['users']);

//echo 'users array';

//print_r($_POST);
//echo '<h2>Making the changes you requested</h2>';
//echo 'date is ' . $date . '<br/>';
$date = strtotime($date);

$currentdate = date('d-m-Y h:i:s');
//echo $currentdate;
$currentdate = strtotime($currentdate);

// check that something has been selected
if (empty($_POST['users'])) {
//    echo '<h2>You must select some users</h2>';
//    echo $url2;
    $backurl = $CFG->wwwroot . "/blocks/group_targets/set2.php?courseid=" . $courseid;
    redirect($backurl, 'You must select some users', '5');
}


//step through the array and mark the posts as completed

foreach ($users as $user) {
    echo 'user is ' , $user;
    $query = "UPDATE mdl_block_ilp_plu_ste_ent SET parent_id='" . $_POST['status'] . "', value='" . $_POST['statusname'] . "', timemodified='" . $currentdate . "' WHERE entry_id='" . $user . "'";
    echo $query;
    $DB->execute($query);
}


$backurl = $CFG->wwwroot . "/blocks/group_targets/set2.php?courseid=" . $courseid;
redirect($backurl, 'MTG Saved Sending you back the group target setter', '0');
?>
