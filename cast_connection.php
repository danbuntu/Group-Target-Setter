<?php

/*
 * Set up the connection the cast databases, checks for a user and use the right database it the user is found
 * If there is no user exit and don't display the block in the PLP
 */

$link11 = mysql_connect('10.0.100.35', 'root', 'XXXXXX');
if (!$link11) {
    die('Could not connects: ' . mysql_error());
}
//echo 'Connected to medals';
//Test if the user exsits in that database
$queryexists = "SELECT id, learnerref FROM students WHERE learnerref=" . $studentId;


$has_support='0';

//select maidstonedb
mysql_select_db('castmaidstone') or die('Unable to select the database');

$result = mysql_query($queryexists);
$num_rows = mysql_num_rows($result);

if ($num_rows > 0) {
    $db = 'Maidstone';
    $has_support='1';

} else {

//swap to the medway database
    mysql_select_db('castmedway') or die('Unable to select the database');

//Check if they exit again
    $result = mysql_query($queryexists);
    $num_rows = mysql_num_rows($result);

    if ($num_rows > 0) {
        $db = 'Medway';
        $has_support='1';
    }
}
?>