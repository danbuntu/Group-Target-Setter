    <?php

    include('moodle_connection.php');
     $query = "SELECT primary_qual FROM moodle.primary_qual WHERE learner_code='06039876'";
    echo $query;
    $result = mysql_query($query);

            $primaryQual = $result['primary_qual'];

    echo $primaryQual;

    return $primaryQual;

            ?>