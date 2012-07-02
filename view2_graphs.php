<?php

 if ($flightplanSet == 1) {
//flightplan scores 1
$graph1 = array(
$reviewOneScore1,
$reviewTwoScore1,
$reviewThreeScore1,
$reviewFourScore1,
$reviewFiveScore1,
$reviewSixScore1,
$noflight1,
);


$graph2 = array(
$reviewOneScore2,
$reviewTwoScore2,
$reviewThreeScore2,
$reviewFourScore2,
$reviewFiveScore2,
$reviewSixScore2,
$noflight2,
);

//flightplan scores
$graph3 = array(
$reviewOneScore3,
$reviewTwoScore3,
$reviewThreeScore3,
$reviewFourScore3,
$reviewFiveScore3,
$reviewSixScore3,
$noflight3,
);

//flightplan scores
$graph4 = array(
$reviewOneScore4,
$reviewTwoScore4,
$reviewThreeScore4,
$reviewFourScore4,
$reviewFiveScore4,
$reviewSixScore4,
$noflight4,
);

?>
<h1>Fancy charts of great import</h1>
<?php

$colours = array('#FF6600', '#FFCC00', '#FFFF00', '#33FF66', '#33CC33', '#339900', '#FF0000');
makePieChart($graph1, $colours, 'Review 1');
makePieChart($graph2, $colours, 'Review 2');
makePieChart($graph3, $colours, 'Review 3');
makePieChart($graph4, $colours, 'Review 4');

     unset($graph1);
     unset($graph2);
     unset($graph3);
     unset($graph4);
 }

if ($attendanceSet == 1) {

$graphAtt = array(
    $outstanding,
    $excellent,
    $good,
    $causeForConcern,
    $poor,

);

$colours = array('#339900', '#33FF66', '#FFCC00', '#FF6600', '#FF0000');
$legend = array('Outstanding', 'Excellent', 'Good', 'Concern', 'Poor');
makePieChart2($graphAtt, $legend, $colours, 'Attendance');

}

if ($ragSet == 1) {

// RAG pie charts
$graph = array(
    $green,
    $amber,
    $red,
);

$colours = array('#2AFF2A', '#FFD400', '#FF0000');
$legend = array('Green', 'Amber', 'Red');
makePieChart2($graph, $legend, $colours, 'RAG Status');
}


if ($mtgSet == 1) {

$mtg_not_set = $count - $mtg_set;
$graph = array(
    $mtg_set,
    $mtg_not_set,
);

$colours = array('#31B131', '#FF0000');
$colours2 = array('#31B131', '#87AACB', '#FF0000');
$legend = array('MTG Set', 'MTG Not Set');
makePieChart2($graph, $legend, $colours, 'P-best Set');


}

 if ($parentalSet == 1) {

$parental_not_signed = $count - ($parental_signed + $parental_na);
$graph = array(
    $parental_signed,
    $parental_na,
    $parental_not_signed,
);
 }

$legend = array('Signed', 'N/A', 'Not Signed');
makePieChart2($graph, $legend, $colours2, 'Parental Agreements');

 if ($castSet == 1) {

$cast_not_signed = $count - $cast_signed;
$graph = array(
    $cast_signed,
    $cast_not_signed,
);

$legend = array('Support', 'No Support');
makePieChart2($graph, $legend, $colours, 'Cast Support');
 }


unset($graph);
unset($graphAtt);
unset($legend);
unset($colours);
unset($colours2);
?>
</div>
</div>