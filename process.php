<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../../config.php");

global $USER, $CFG, $COURSE;

$mark = $_POST['mark'];
$courseid = $_POST['courseid'];
$mark_as = $_POST['mark_as'];
$userid = $_POST['userid'];
$url = $_POST['url'];
$url2 = $_POST['url2'];
$status = $_POST['status'];
$url2 = rtrim($url2) . '&status=' . trim($status);

echo $mark_as;
print_r($mark);
echo '<h2>Making the changes you requested</h2>';
//echo 'date is ' . $date . '<br/>';
$date = strtotime($date);

$currentdate = date('d-m-Y h:i:s');
//echo $currentdate;
$currentdate = strtotime($currentdate);

// check that something has been selected
if ($mark == null) {
    echo '<h2>You must select a target</h2>';
    echo $url2;
    echo '<meta http-equiv="Refresh" content="3;URL=' . $url2 . '" />';
}

if ($mark_as == '') {
    echo '<h2>You must select open/achieved or withdrawn</h2>';
    echo $url2;
    echo '<meta http-equiv="Refresh" content="3;URL=' . $url2 . '" />';
}

//step through the array and mark the posts as completed

foreach ($mark as $box) {
    $query = 'UPDATE mdl_ilptarget_posts SET status="' . $mark_as . '" WHERE id="' . $box . '"';
    echo $query;
    mysql_query($query);
}
echo '<meta http-equiv="Refresh" content="0;URL=' . $url2 . '" />';
?>
