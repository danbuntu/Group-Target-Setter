<?php

function pieChart($array, $title, $colours)
{

    $chart_row = '[';

    foreach ($array as $key => $item) {
        $chart_row = $chart_row . "['" . $key . "'," . $item . "],";
    }

//chop of the last comma
    $chart_row = substr($chart_row, 0, -1);

// append the last square bracket
    $chart_row = $chart_row . ']';

    ?>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {

        var data = google.visualization.arrayToDataTable(
            <?php print ($chart_row); ?>
        );

        var options = {
            title:'<?php echo $title; ?>',
            titleTextStyle: {fontSize: 20, position: 'center'},
            legend: {position: 'right'},
//            slice visibility to force 0 results to show in legend
            sliceVisibilityThreshold:0,
            is3D:true,
            colors: <?php echo '[' , $colours , ']'; ?>,
            chartArea: {left: 5, top: 30, right: 5, width: 400, height: 500 }
    };

        // Create and draw the visualization.
        var chart = new google.visualization.PieChart(document.getElementById('<?php echo 'pie_', $title; ?>'));
        chart.draw(data, options);
    }


</script>

<!--Div that will hold the pie chart-->
<div class="pie_chart" id="<?php echo 'pie_', $title;?>" style="width:400; height:400"></div>
<?php } ?>
