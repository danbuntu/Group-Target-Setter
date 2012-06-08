<?php
/**
 * Created by JetBrains PhpStorm.
 * User: DATTWOOD
 * Date: 30/05/12
 * Time: 08:42
 * To change this template use File | Settings | File Templates.
 */

// Settings to be manually edited for now. Needs to be moved into an admin screen/ setting stable

//The ID of the target reprots
// Found by  select * from mdl_block_ilp_report
$targetId = '3';


// the ID of the reviews

$reportsArray = array(
    '2' => 'Reviews',
    '4' => 'Concerns'
);



// Decalre the number of the PT reviews. Used to display the last review date and the person that did it
$reviewNumber = '2';



// settings to turn features on and off - 1 = on
$mtgSet = '1';
$ragSet = '1';
$flightplanSet = '1';
$targetSet = '1';
$badgesSet = '1';
$lastReviewSet = 1;
$passportSet = 1;
$reportsSet = 1;
$parentalSet = 1;
$castSet = 1;
$withdrawnSet = 1;
$mobileSet = 1;
$attendanceSet = 1;


//sections

$showTotals = 1;
$showGraphs = 1;

?>