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
    '3' => 'Reviews',
    '4' => 'Concerns'
);



// Decalre the number of the PT reviews. Used to display the last review date and the person that did it
$reviewNumber = '3';



// settings to turn features on and off - 1 = on
$mtgSet = '1';
$ragSet = '1';

?>