<?php

if ($flightplanSet == 1) {
    ?>
<h1>Fancy charts of great import</h1>

 <div class="row">
 <?php

    $colours = "'#FF6600', '#FFCC00', '#FFFF00', '#33FF66', '#33CC33', '#339900', '#FF0000'";
    $graph1 = array(
        '0' => '0',
        'Score 0 (' . $reviewOneScore1 . ')' => $reviewOneScore1,
        'Score 1 (' . $reviewTwoScore1 . ')' => $reviewTwoScore1,
        'Score 2 (' . $reviewThreeScore1 . ')' => $reviewThreeScore1,
        'Score 3 (' . $reviewFourScore1 . ')' => $reviewFourScore1,
        'Score 4 (' . $reviewFiveScore1 . ')' => $reviewFiveScore1,
        'Score 5 (' . $reviewSixScore1 . ')' => $reviewSixScore1,
        'No Flight Plan (' . $noflight1 . ')' => $noflight1,
    );

    include('pie_chart_function.php');
    pieChart($graph1, 'Review 1', $colours);

    $graph2 = array(
        '0' => '0',
        'Score 0 (' . $reviewOneScore2 . ')' => $reviewOneScore2,
        'Score 1 (' . $reviewTwoScore2 . ')' => $reviewTwoScore2,
        'Score 2 (' . $reviewThreeScore2 . ')' => $reviewThreeScore2,
        'Score 3 (' . $reviewFourScore2 . ')' => $reviewFourScore2,
        'Score 4 (' . $reviewFiveScore2 . ')' => $reviewFiveScore2,
        'Score 5 (' . $reviewSixScore2 . ')' => $reviewSixScore2,
        'No Flight Plan (' . $noflight2 . ')' => $noflight2,
    );

    pieChart($graph2, 'Review 2', $colours);

    $graph3 = array(
        '0' => '0',
        'Score 0 (' . $reviewOneScore3 . ')' => $reviewOneScore3,
        'Score 1 (' . $reviewTwoScore3 . ')' => $reviewTwoScore3,
        'Score 2 (' . $reviewThreeScore3 . ')' => $reviewThreeScore3,
        'Score 3 (' . $reviewFourScore3 . ')' => $reviewFourScore3,
        'Score 4 (' . $reviewFiveScore3 . ')' => $reviewFiveScore3,
        'Score 5 (' . $reviewSixScore3 . ')' => $reviewSixScore3,
        'No Flight Plan (' . $noflight3 . ')' => $noflight3,
    );

    pieChart($graph3, 'Review 3', $colours);

    $graph4 = array(
        '0' => '0',
        'Score 0 (' . $reviewOneScore4 . ')' => $reviewOneScore4,
        'Score 1 (' . $reviewTwoScore4 . ')' => $reviewTwoScore4,
        'Score 2 (' . $reviewThreeScore4 . ')' => $reviewThreeScore4,
        'Score 3 (' . $reviewFourScore4 . ')' => $reviewFourScore4,
        'Score 4 (' . $reviewFiveScore4 . ')' => $reviewFiveScore4,
        'Score 5 (' . $reviewSixScore4 . ')' => $reviewSixScore4,
        'No Flight Plan (' . $noflight4 . ')' => $noflight4,
    );

    pieChart($graph4, 'Review 4', $colours);


    unset($graph1);
    unset($graph2);
    unset($graph3);
    unset($graph4);
}

?>

</div>

  <div class="row">
      <?php

      if ($attendanceSet == 1) {

          $colours = "'#339900', '#33FF66', '#FFCC00', '#FF6600', '#FF0000'";

          $graphAtt = array(
              '0' => '0',
              'Outstanding (' . $outstanding . ')' => $outstanding,
              'Excellent (' . $excellent . ')' => $excellent,
              'Good (' . $good . ')' => $good,
              'Concern (' . $causeForConcern . ')' => $causeForConcern,
              'Poor (' . $poor . ')' => $poor,

          );

          pieChart($graphAtt, 'Attendance', $colours);
      }

      if ($ragSet == 1) {

// RAG pie charts
          $graph = array(
              '0' => '0',
              'Green (' . $green . ')' => $green,
              'Amber (' . $amber . ')' => $amber,
              'Red (' . $red . ')' => $red,
          );

          $colours = "'#33FF66', '#FFD400', '#FF0000'";
          pieChart($graph, 'RAG Status', $colours);
      }


      if ($mtgSet == 1) {

          $mtg_not_set = $count - $mtg_set;
          $graph = array(
              '0' => '0',
              'MTG Set (' . $mtg_set . ')' => $mtg_set,
              'MTG Not Set (' . $mtg_not_set . ')' => $mtg_not_set,
          );

          $colours = "'#31B131', '#FF0000'";
          $colours2 = "'#31B131', '#87AACB', '#FF0000'";
          pieChart($graph, 'P-best Set', $colours);


      }

      if ($parentalSet == 1) {

          $parental_not_signed = $count - ($parental_signed + $parental_na);
          $graph = array(
              '0' => '0',
              'Signed (' . $parental_signed . ')' => $parental_signed,
              'N/A (' . $parental_na . ')' => $parental_na,
              'Not Signed (' . $parental_not_signed . ')' => $parental_not_signed,
          );
      }

      pieChart($graph, 'Parental Agreements', $colours2);

      if ($castSet == 1) {

          $cast_not_signed = $count - $cast_signed;
          $graph = array(
              '0' => '0',
              'Support (' . $cast_signed . ')' => $cast_signed,
              'No Support (' . $cast_not_signed . ')' => $cast_not_signed,
          );

          pieChart($graph, 'Cast Support', $colours);
      }

      unset($graph);
      unset($graphAtt);
      unset($legend);
      unset($colours);
      unset($colours2);
      ?>
  </div>
</div>
</div>