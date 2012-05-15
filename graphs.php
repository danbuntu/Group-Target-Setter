<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 08/04/11
 * Time: 11:12
 * To change this template use File | Settings | File Templates.
 */

//flightplan scores
$graph1 = array(
    $reviewOneScore1,
    $reviewTwoScore1,
    $reviewThreeScore1,
    $reviewFourScore1,
    $reviewFiveScore1,
    $reviewSixScore1,
    $noflight1,

);

echo "<img src=http://chart.apis.google.com/chart?cht=p3&chma=5,5,5,5&chtt=Flightplan+Scores+R1&chs=300x300&chts=000000,18&chf=bg,s," . $background . "&chd=" . chart_data($graph1) . "&chco=FF6600|FFCC00|FFFF00|33FF66|33CC33|339900|FF0000&chdl=1+" .  $reviewOneScore1 . "|2+" . $reviewTwoScore1 . "|3+" . $reviewThreeScore1 . "|4+" . $reviewFourScore1 . "|5+" . $reviewFiveScore1 . "|6+" . $reviewSixScore1 . "|No+flightplan+set+" . $noflight1 . ">";


//flightplan scores
$graph2 = array(
    $reviewOneScore2,
    $reviewTwoScore2,
    $reviewThreeScore2,
    $reviewFourScore2,
    $reviewFiveScore2,
    $reviewSixScore2,
    $noflight2,
);

echo "<img src=http://chart.apis.google.com/chart?cht=p3&chma=5,5,5,5&chtt=Flightplan+Scores+R2&chs=300x300&chts=000000,18&chf=bg,s," . $background . "&chd=" . chart_data($graph2) . "&chco=FF6600|FFCC00|FFFF00|33FF66|33CC33|339900|FF0000&chdl=1+" .  $reviewOneScore2 . "|2+" . $reviewTwoScore2 . "|3+" . $reviewThreeScore2 . "|4+" . $reviewFourScore2 . "|5+" . $reviewFiveScore2 . "|6+" . $reviewSixScore2 . "|No+flightplan+set+" . $noflight2 . ">";


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

echo "<img src=http://chart.apis.google.com/chart?cht=p3&chma=5,5,5,5&chtt=Flightplan+Scores+R3&chs=300x300&chts=000000,18&chf=bg,s," . $background . "&chd=" . chart_data($graph3) . "&chco=FF6600|FFCC00|FFFF00|33FF66|33CC33|339900|FF0000&chdl=1+" .  $reviewOneScore3 . "|2+" . $reviewTwoScore3 . "|3+" . $reviewThreeScore3 . "|4+" . $reviewFourScore3 . "|5+" . $reviewFiveScore3 . "|6+" . $reviewSixScore3 . "|No+flightplan+set+" . $noflight3 . ">";


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

echo "<img src=http://chart.apis.google.com/chart?cht=p3&chma=5,5,5,5&chtt=Flightplan+Scores+R4&chs=300x300&chts=000000,18&chf=bg,s," . $background . "&chd=" . chart_data($graph4) . "&chco=FF6600|FFCC00|FFFF00|33FF66|33CC33|339900|FF0000&chdl=1+" .  $reviewOneScore4 . "|2+" . $reviewTwoScore4 . "|3+" . $reviewThreeScore4 . "|4+" . $reviewFourScore4 . "|5+" . $reviewFiveScore4 . "|6+" . $reviewSixScore4 . "|No+flightplan+set+" . $noflight4 . ">";


//echo $excellent . ', ';
//echo $satisfactory . ', ';
//echo $unsatisfactory . ', ';
//echo $poor . ', ';
//echo $verypoor . ', ';


$graphAtt = array(
    $outstanding,
    $excellent,
    $good,
    $causeForConcern,
    $poor,

);

echo "<img src=http://chart.apis.google.com/chart?cht=p3&chma=5,5,5,5&chtt=Attendance&chs=300x300&chts=000000,18&chf=bg,s," . $background . "&chd=" . chart_data($graphAtt) . "&chco=339900|33FF66|FFCC00|FF6600|FF0000&chdl=Outstanding+" . $outstanding . "|Excellent+" . $excellent . "|Good+" . $good . "|Cause+For+Concern+" . $causeForConcern . "|Poor+" . $poor . ">";






// RAG pie charts
$graph = array(
    $green,
    $amber,
    $red,
);

echo "<tr><td><img src=http://chart.apis.google.com/chart?cht=p3&chma=5,5,5,5&chs=300x300&chts=000000,18&chtt=RAG+Status&chf=bg,s," . $background . "&chd=" . chart_data($graph) . "&chco=2AFF2A|FFD400|FF0000&chdl=Green+" . $green . "|Amber+" . $amber . "|Red+" . $red . ">";

// MTGs pie chart
$mtg_not_set = $count - $mtg_set;
$graph = array(
    $mtg_set,
    $mtg_not_set,
);

echo "<img src=http://chart.apis.google.com/chart?cht=p3&chma=5,5,5,5&chs=300x300&chts=000000,18&chtt=Manual+MTGS+Set&chf=bg,s," . $background . "&chd=" . chart_data($graph) . "&chco=31B131|FF0000&chdl=MTG+Set+" . $mtg_set . "|MTG+Not+Set+" . $mtg_not_set . ">";

$parental_not_signed = $count - ($parental_signed + $parental_na);
$graph = array(
    $parental_signed,
    $parental_na,
    $parental_not_signed,
);

echo "<img src=http://chart.apis.google.com/chart?cht=p3&chma=5,5,5,5&chtt=Parental+Agreements&chs=300x300&chts=000000,18&chf=bg,s," . $background . "&chd=" . chart_data($graph) . "&chco=339900|87AACB|FF0000&chdl=Signed+" . $parental_signed . "|N/A+"  . $parental_na . "|Not+Signed+" . $parental_not_signed . ">";

$cast_not_signed = $count - $cast_signed;
$graph = array(
    $cast_signed,
    $cast_not_signed,
);

echo "<img src=http://chart.apis.google.com/chart?cht=p3&chma=5,5,5,5&chtt=Cast+Support&chs=300x300&chts=000000,18&chf=bg,s," . $background . "&chd=" . chart_data($graph) . "&chco=339900|FF0000&chdl=Support+" . $cast_signed . "|No+Support+" . $cast_not_signed . ">";
?>