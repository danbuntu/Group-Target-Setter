<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 08/04/11
 * Time: 11:42
 * To change this template use File | Settings | File Templates.
 */
require_once($CFG->libdir . '/formslib.php');

// Get the date 1 month ago
function getDateMonth()
{
    $dateMonth = strtotime("-1 month");
    return $dateMonth;
}


// Get the medals awarded to the student
function getMedals($studentNumber)
{
    include('moodle_connection.php');
//    $query = "SELECT * FROM medals.students s JOIN medals.badges_link b ON b.student_id=s.id JOIN medals.badges bb ON bb.id=b.badge_id WHERE s.students_name='" . $username . "'";
    $query = "SELECT * FROM badges_link  JOIN badges ON badges_link.badge_id=badges.id  where student_id='" . $studentNumber .  "'";

//     echo $query;
    $result = mysql_query($query, $link);
    $badges = array();
    while ($row = mysql_fetch_assoc($result)) {
        $icon = $row["icon"];
        array_push($badges, $icon);
    }
    return $badges;

}


// Find out of the student has cast support
function getCastSupport($studentId)
{

    $link11 = mysql_connect('10.0.100.35', 'root', '88boom!');
    if (!$link11) {
        die('Could not connect: ' . mysql_error());
    }
    //echo 'Connected to medals';
    //Test if the user exsits in that database
    $queryexists = "SELECT id, learnerref FROM students WHERE learnerref=" . $studentId;


    $has_support = 0;
    ;

    //select maidstonedb
    mysql_select_db('castmaidstone') or die('Unable to select the database');

    $result = mysql_query($queryexists, $link11);
    $num_rows = mysql_num_rows($result);

    if ($num_rows > 0) {
        $db = 'Maidstone';
        $has_support = 1;
        ;

    } else {

        //swap to the medway database
        mysql_select_db('castmedway') or die('Unable to select the database');

        //Check if they exit again
        $result = mysql_query($queryexists, $link11);
        $num_rows = mysql_num_rows($result);

        if ($num_rows > 0) {
            $db = 'Medway';
            $has_support = 1;
            ;
        }
    }

    mysql_close($link11);


    return $has_support;
}

// Compare the two reviews and set the colour to denote improvement or worsening
function compareReviews($one, $two)
{
    if (($one == '') or ($two == '')) {
        $reviewGraphic = '<img src="./images/list-remove.png" height"20px" width="20px">';
    } else {
        if ($two < $one) {
            $reviewColour = 'red';
            $reviewGraphic = '<img src="./images/go-down.png" height"20px" width="20px">';
        } elseif ($two > $one) {
            $reviewColour = 'green';
            $reviewGraphic = '<img src="./images/go-up.png" height"20px" width="20px">';
        } elseif ($two == $one) {
            $reviewColour = 'amber';
            $reviewGraphic = '<img src="./images/go-next.png" height"20px" width="20px">';
        }
    }
    $reviews = array();
    array_push($reviews, $reviewColour);
    array_push($reviews, $reviewGraphic);
    return $reviews;
}

// check the student shas a mobile number registered ont he system and reports a yes or no
function checkIfHasMobile($studentMoodleId)
{
    include('moodle_connection.php');
    $query = "SELECT phone2 FROM moodle.mdl_user WHERE id='" . $studentMoodleId . "' and phone2!=' '";
    $result = mysql_query($query) or die('Invalid query: ' . mysql_error());
    ;
    $num_rows = mysql_num_rows($result);
    if ($num_rows != 0) {
        $mobile = 1;
    } else {
        $mobile = 0;
    }
    return $mobile;
}

// Set the flight plan scores array
function getFlightplanScores($studentId, $mysqli)
{
    // select the last 4 reviews
    $query = "SELECT score FROM flightplan WHERE student_id='" . $studentId . "' ORDER BY id DESC LIMIT 4";
//    echo $query;

    $result = $mysqli->query($query);

    $num_rows = $result->num_rows;

    $i = $num_rows;
    while ($row = $result->fetch_object()) {

        ${review . $i} = $row->score;
        $i--;
    }

//    echo 'review 1 =' . $review1 . '<br/>';
//    echo 'review 2 =' . $review2 . '<br/>';
//    echo 'review 3 =' . $review3 . '<br/>';
//    echo 'review 4 =' . $review4 . '<br/>';

    $reviews = array();
    array_push($reviews, $review1);
    array_push($reviews, $review2);
    array_push($reviews, $review3);
    array_push($reviews, $review4);
//    print_r($reviews);
    return $reviews;

}


function getPrimaryQual($studentId, $client)
{
//    include('moodle_connection.php');
//    $query = "SELECT primary_qual FROM moodle.primary_qual WHERE learner_code='" . $studentId . "'";
//    $result = mysql_query($query);
//    while ($row = mysql_fetch_assoc($result)) {
//        $primaryQual = $row['primary_qual'];
//    }
//    return $primaryQual;


//    try {
//        $quals = $client->getPrimaryQualById($studentId);
//    }
//
//    catch (SoapFault $e) {
//        // handle issues returned by the web service
//        echo 'soap-error';
//    }

    $quals = $client->__soapCall("getPrimaryQualById",array($studentId));

    return $quals;


}

function getMtgMis($ladNum, $averagescore, $mtg_grade, $qualTitle, $level, $qualType)
{
//echo 'this is in the function';
//    echo 'ladnum :' . $ladNum . ' averagscore ' . $averagescore . ' $mtg_grade' . $mtg_grade .  ' $level' . $level;

    if ($averagescore != 0)  {

    if ($ladNum == '5007801X') {
        if ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 44) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 38) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore >= 30) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        } elseif ($averagescore < 30) {
            $mtg_grade = $mtg_grade . '-' . 'MPP';
        }
    } elseif ($ladNum == '50091499') {
        if ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 46) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 41) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 33) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore >= 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'MPP';
        }
    } elseif ($ladNum == '50075664') {
        if ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 44) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 38) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 30) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore < 29) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        }
    } elseif ($ladNum == '50078781') {
        if ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 45) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 39) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 34) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore >= 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'MPP';
        }
    } elseif ($ladNum == '50068726') {
        if ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 39) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 29) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        }
    } elseif ($ladNum == '50068726') {
        if ($averagescore >= 47) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 40) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 34) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 29) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        }
    } elseif ($ladNum == '50068726') {
        if ($averagescore >= 47) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 39) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 32) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 27) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore < 26) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        }
    } elseif ($ladNum == '50073813') {
        if ($averagescore >= 47) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 39) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 32) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 27) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore < 26) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        }
    } elseif ($ladNum == '5007717X') {
        if ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 28) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        }
    } elseif ($ladNum == '50077168') {
        if ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 45) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 40) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 35) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore >= 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'MPP';
        }
    } elseif ($ladNum == '50067643') {
        if ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 45) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 40) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 35) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore >= 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'MPP';
        }
    } elseif ($ladNum == '50068015') {
        if ($averagescore >= 49) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 44) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 41) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 36) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore >= 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'MPP';
        }
    } elseif ($ladNum == '50098615') {
        if ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 47) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 43) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 36) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore >= 30) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        } elseif ($averagescore < 30) {
            $mtg_grade = $mtg_grade . '-' . 'MPP';
        }
    } elseif ($ladNum == '50095018') {
        if ($averagescore >= 45) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 41) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 35) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 30) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore < 30) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        }
    } elseif ($ladNum == '50067205') {
        if ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 45) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 39) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 35) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore >= 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'MPP';
        }
    } elseif ($ladNum == '50082656') {
        if ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 45) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 39) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 35) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore >= 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'MPP';
        }
    } elseif ($ladNum == '50071397') {
        if ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 46) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 34) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore >= 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'MPP';
        }
    } elseif ($ladNum == '50081652') {
        if ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 46) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 43) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 36) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore >= 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'MPP';
        }
    } elseif ($ladNum == '50023214') {
        if ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'DDD';
        } elseif ($averagescore >= 46) {
            $mtg_grade = $mtg_grade . '-' . 'DDM';
        } elseif ($averagescore >= 43) {
            $mtg_grade = $mtg_grade . '-' . 'DMM';
        } elseif ($averagescore >= 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMM';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'MMP';
        }
    } elseif ($ladNum == '00285507') {
        //        echo 'function hit';
        if ($averagescore >= 58) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 55) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 47) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 42) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
        //        echo  'function mtg' . $mtg_grade;
    } elseif ($ladNum == '00255953') {
        if ($averagescore >= 58) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 55) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 47) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 42) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00260072') {
        if ($averagescore >= 58) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 55) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 47) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 42) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00285518') {
        if ($averagescore >= 57) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 54) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 44) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 37) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 37) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00285029') {
        if ($averagescore >= 58) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 54) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 44) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 38) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 38) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00255951') {
        if ($averagescore >= 56) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 45) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 39) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 34) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 34) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '10001542') {
        if ($averagescore >= 58) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 54) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 44) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 34) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 34) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00260032') {
        if ($averagescore >= 59) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 54) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 49) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 35) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 35) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00260031') {
        if ($averagescore >= 59) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 54) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 49) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 35) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 35) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00260079') {
        if ($averagescore >= 57) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 53) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 43) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 34) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 34) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00260081') {
        if ($averagescore >= 57) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 53) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 43) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 34) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 34) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00260093') {
        if ($averagescore >= 56) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 45) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 39) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 34) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 34) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00255990') {
        if ($averagescore >= 58) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 54) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 32) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 32) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00255993') {
        if ($averagescore >= 58) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 54) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 32) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 32) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00260000') {
        if ($averagescore >= 59) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 54) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 49) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 32) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 32) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00285508') {
        if ($averagescore >= 54) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 44) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 36) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 32) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 32) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '00260388') {
        if ($averagescore >= 59) {
            $mtg_grade = $mtg_grade . '-' . 'A*';
        } elseif ($averagescore >= 57) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 54) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 38) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 38) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '50023524') {
        if ($averagescore >= 55) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 43) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 43) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '50026574') {
        if ($averagescore >= 55) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 43) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 43) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '50025545') {
        if ($averagescore >= 55) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 43) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 43) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '10034110') {
        if ($averagescore >= 56) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 47) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 42) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '50022751') {
        if ($averagescore >= 55) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 49) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 45) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 39) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 39) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '50023263') {
        if ($averagescore >= 56) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 49) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 44) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 32) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 32) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '5002324X') {
        if ($averagescore >= 55) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 47) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 43) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 43) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '50026677') {
        if ($averagescore >= 53) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 47) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 42) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '50022672') {
        if ($averagescore >= 53) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 44) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 39) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 39) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '50026653') {
        if ($averagescore >= 54) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 48) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 42) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 32) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 32) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '50025764') {
        if ($averagescore >= 55) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 44) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 38) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 38) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '5002243X') {
        if ($averagescore >= 52) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 47) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 38) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 28) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 28) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '50023056') {
        if ($averagescore >= 55) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 50) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 44) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 37) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 37) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } elseif ($ladNum == '50019016') {
        if ($averagescore >= 51) {
            $mtg_grade = $mtg_grade . '-' . 'A';
        } elseif ($averagescore >= 44) {
            $mtg_grade = $mtg_grade . '-' . 'B';
        } elseif ($averagescore >= 37) {
            $mtg_grade = $mtg_grade . '-' . 'C';
        } elseif ($averagescore >= 26) {
            $mtg_grade = $mtg_grade . '-' . 'D';
        } elseif ($averagescore < 26) {
            $mtg_grade = $mtg_grade . '-' . 'E';
        }
    } else {
        //        echo ' no lad match we need to look elsewhere ';

        //        echo $qualType . $level;
        if (($qualType == 'Other') && (($level == 'X') or ($level == '4'))) {
            //            echo 'mrg match';
            //            $mtg_grade = 'N/A';
        } elseif (($qualType == 'NVQ') && ($level == '4')) {
            //            echo 'NVQ 4 match';
            $mtg_grade = 'N/A';
        } elseif ($qualType == 'Non Council funded studies') {
            //            echo 'Non council';
            //            $mtg_grade = 'N/A';
        } elseif ($qualType == 'NVQ/GNVQ Key Skills Unit') {
            //            echo 'NVQ Key Skills';
            //            $mtg_grade = 'N/A';
        } elseif (($qualType == 'Award') && ($level == '1')) {
            //            echo 'award level 1';
            if ($averagescore >= 21) {
                $mtg_grade = $mtg_grade . '-' . 'P++';
            } elseif ($averagescore >= 16) {
                $mtg_grade = $mtg_grade . '-' . 'P+';
            } elseif ($averagescore < 15) {
                $mtg_grade = $mtg_grade . '-' . 'P';
            }

        } elseif (($qualType == 'Award') && ($level == '2')) {
            //            echo 'award level 2';
            if ($averagescore >= 38) {
                $mtg_grade = $mtg_grade . '-' . 'P++';
            } elseif ($averagescore >= 28) {
                $mtg_grade = $mtg_grade . '-' . 'P+';
            } elseif ($averagescore < 28) {
                $mtg_grade = $mtg_grade . '-' . 'P';
            }
        } elseif (($qualType == 'NVQ') && ($level == '3')) {
            //            echo 'NVQ level 3';
            if ($averagescore >= 40) {
                $mtg_grade = $mtg_grade . '-' . 'D';
            } elseif ($averagescore >= 30) {
                $mtg_grade = $mtg_grade . '-' . 'M';
            } elseif ($averagescore < 30) {
                $mtg_grade = $mtg_grade . '-' . 'P';
            }
        } elseif (($qualType == 'Diploma') && ($level == '1')) {
            //            echo 'Diploma level 1';
            if ($averagescore >= 21) {
                $mtg_grade = $mtg_grade . '-' . 'P++';
            } elseif ($averagescore >= 16) {
                $mtg_grade = $mtg_grade . '-' . 'P+';
            } elseif ($averagescore < 15) {
                $mtg_grade = $mtg_grade . '-' . 'P';
            }

        } elseif (($qualType == 'Diploma') && ($level == '2')) {
            //            echo 'award level 2';
            if ($averagescore >= 38) {
                $mtg_grade = $mtg_grade . '-' . 'P++';
            } elseif ($averagescore >= 28) {
                $mtg_grade = $mtg_grade . '-' . 'P+';
            } elseif ($averagescore < 28) {
                $mtg_grade = $mtg_grade . '-' . 'P';
            }

        } elseif (($qualType == 'Diploma') && ($level == '3')) {
    //                        echo 'award level 2';
                if ($averagescore >= 45) {
                    $mtg_grade = $mtg_grade . '-' . 'DD';
                } elseif ($averagescore >= 41) {
                    $mtg_grade = $mtg_grade . '-' . 'DM';
                 } elseif ($averagescore >= 33) {
                    $mtg_grade = $mtg_grade . '-' . 'MM';
                    } elseif ($averagescore >= 28) {
                    $mtg_grade = $mtg_grade . '-' . 'PM';
                } elseif ($averagescore < 28) {
                    $mtg_grade = $mtg_grade . '-' . 'PP';
                }



        } elseif (($qualType == 'Other') && ($level == 'E')) {
            if ($averagescore >= 2) {
                $mtg_grade = $mtg_grade . '-' . 'High';
            } elseif ($averagescore >= 1) {
                $mtg_grade = $mtg_grade . '-' . 'Medium';
            } elseif ($averagescore < 0) {
                $mtg_grade = $mtg_grade . '-' . 'Low';
            }
        } elseif ($qualType == 'HNC') {
            if ($averagescore >= 100) {
                $mtg_grade = $mtg_grade . '-' . 'D';
            } elseif ($averagescore >= 90) {
                $mtg_grade = $mtg_grade . '-' . 'M';
            } elseif ($averagescore < 90) {
                $mtg_grade = $mtg_grade . '-' . 'P';
            }
        } elseif ($qualType == 'HND') {
            if ($averagescore >= 100) {
                $mtg_grade = $mtg_grade . '-' . 'D';
            } elseif ($averagescore >= 90) {
                $mtg_grade = $mtg_grade . '-' . 'M';
            } elseif ($averagescore < 90) {
                $mtg_grade = $mtg_grade . '-' . 'P';
            }
        }

    }
    //    echo 'function end: ' . $mtg_grade;
    return $mtg_grade;
} else {
    $mtg_grade = 'nn/a';
        return $mtg_grade;
    }
}


function getMTGS($studentId) {
    $query = "SELECT mtg, tutor_mtg FROM moodle.mtg WHERE student_id='" . $studentId . "'";
    $result = mysql_query($query);
        $mtgArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        array_push($mtgArray, $row["mtg"]);
        array_push($mtgArray, $row["tutor_mtg"]);
    }

    return $mtgArray;
}


// Find the selected course context id
function getCourseContextID($courseId)
{
    $query = "SELECT * FROM mdl_context WHERE instanceid='" . $courseId . "' AND contextlevel='50'";
    //    echo 'get context ' . $query;
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        $courseContextId = $row['id'];
    }
    //   echo 'context is ' . $courseContextId;
    return $courseContextId;
}

// Get the students review, concern etc numbers
function getReviews($studentMoodleId, $month_review, $month_concern, $month_reason, $month_contribs, $dateMonth)
{
    $query = "SELECT * FROM mdl_ilpconcern_posts WHERE setforuserid='" . $studentMoodleId . "'";
    $result = mysql_query($query);

    $review = 0;
    $concern = 0;
    $reason = 0;
    $contribution = 0;

    while ($row = mysql_fetch_assoc($result)) {
        if ($row["status"] == 0) {
            $review = $review + 1;
            $date = $row["timemodified"];
            if ($date > $dateMonth) {
                $month_review = $month_review + 1;
            }
        } elseif ($row["status"] == 1) {
            $concern = $concern + 1;
            $date = $row["timemodified"];
            if ($date > $dateMonth) {
                $month_concern = $month_concern + 1;
            }
        } elseif ($row["status"] == 2) {
            $reason = $reason + 1;
            $date = $row["timemodified"];
            if ($date > $dateMonth) {
                $month_reason = $month_reason + 1;
            }
        } elseif ($row["status"] == 3) {
            $contribution = $contribution + 1;
            $date = $row["timemodified"];
            if ($date > $dateMonth) {
                $month_contribs = $month_contribs + 1;
            }
        }
    }

    $reviews = Array();
    array_push($reviews, $review);
    array_push($reviews, $concern);
    array_push($reviews, $reason);
    array_push($reviews, $contribution);
    array_push($reviews, $month_review);
    array_push($reviews, $month_concern);
    array_push($reviews, $month_reason);
    array_push($reviews, $month_contribs);

    return $reviews;

}


// Get the students targets
function getTargets($studentMoodleId, $target_month, $target_month_with, $target_month_ach, $dateMonth)
{

    $query = "SELECT * FROM mdl_ilptarget_posts  WHERE setforuserid='" . $studentMoodleId . "'";
    // echo $query;
    $tobe = 0;
    $achieved = 0;
    $withdrawn = 0;
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        $status = $row["status"];
        if ($status == '0') {
            $tobe = $tobe + 1;
            $date = $row["timemodified"];
            if ($date > $dateMonth) {
                $target_month = $target_month + 1;
            }
        } elseif ($status == '3') {
            $withdrawn = $withdrawn + 1;
            $date = $row["timemodified"];
            if ($date > $dateMonth) {
                $target_month_with = $target_month_with + 1;
            }
        } elseif ($status == '1') {
            $achieved = $achieved + 1;
            $date = $row["timemodified"];
            if ($date > $dateMonth) {
                $target_month_ach = $target_month_ach + 1;
            }
        }
    }
    $targets = Array();
    array_push($targets, $tobe);
    array_push($targets, $withdrawn);
    array_push($targets, $achieved);
    array_push($targets, $target_month);
    array_push($targets, $target_month_with);
    array_push($targets, $target_month_ach);
    // print_r($targets);
    return $targets;
}

// Get the student attednance
function getAttendance($academicyear, $studentId)
{

    $query = "SELECT     VREGT.REGT_Year AS Year, VREGT.REGT_Student_ID AS StuID, RTRIM(VREGT.REGT_Provision_Code) AS Course,
                      PRPIProvisionInstance.PRPI_Title AS [Course Title], SUM(CASE WHEN AttPresAbs = 'N' THEN 1 ELSE 0 END) AS Present,
                      SUM(CASE WHEN AttPresAbs IN ('Y', 'N') THEN 1 ELSE 0 END) AS Possible, SUM(CASE WHEN AttPresAbs = 'N' THEN 1 ELSE 0 END) AS Absent
                      FROM         REGHrghdr INNER JOIN
                      VREGT ON REGHrghdr.REGH_ISN = VREGT.REGT_REGH_ISN INNER JOIN
                      REGDropin ON VREGT.REGT_REGH_ISN = REGDropin.REGD_REGH_ISN AND VREGT.REGT_Student_ID = REGDropin.REGD_Student_ID INNER JOIN
                      PRPIProvisionInstance ON VREGT.REGT_Provision_Code = PRPIProvisionInstance.PRPI_Code AND
                      VREGT.REGT_Provision_Instance = PRPIProvisionInstance.PRPI_Instance LEFT OUTER JOIN
                      (SELECT     RGAT_Attendance_Code AS AttCode, RGAT_Present AS AttPresAbs
                      FROM        RGATAttendance
                      WHERE      (RGAT_Present = 'Y') OR
                                                   (RGAT_Present = 'N')) AS AttMark ON REGDropin.REGD_Attendance_Mark = AttMark.AttCode
                      WHERE     (REGHrghdr.REGH_Register_Type = 'T')
                      GROUP BY PRPIProvisionInstance.PRPI_Title, VREGT.REGT_Year, VREGT.REGT_Student_ID, RTRIM(VREGT.REGT_Provision_Code)
                      HAVING  (VREGT.REGT_Year ='" . $academicyear . "') AND (VREGT.REGT_Student_ID ='" . $studentId . "' )
                      ORDER BY RTRIM(VREGT.REGT_Provision_Code)";
    // echo $query;
    $attendResult2 = mssql_query($query);

    $present = 0;
    $possible = 0;
    $absent = 0;

    while ($row = mssql_fetch_assoc($attendResult2)) {
        //   echo 'test';
        $present = $present + $row['Present'];
        $possible = $possible + $row['Possible'];
        $absent = $absent + $row['Absent'];

        //  echo 'Days is ' . $present . ' ' . $possible . ' ' . $absent;
        $presentdays = $possible - $absent;

        $totalattendance = $presentdays / $possible;
        // times by 100 to move deicmal place
        $totalattendance = $totalattendance * 100;


    }
    //  echo 'att is: ' . $totalattendance;
    return $totalattendance;
}

// Check if the student has a parental aggreement signed
function getParentalAgreement($studentId, $client)
{
// try {
//        $resultStudent2 = $client->getParentalById($studentId);
//    }
//
//    catch (SoapFault $e) {
//        // handle issues returned by the web service
//        echo 'soap-error';
//    }

$resultStudent2 = $client->__soapCall("getParentalById",array($studentId));

    $signed = $resultStudent2['Signed'];
    return $signed;
}


function checkIfStudentWithdrawn($studentId, $client)
{

//    try {
//        $resultStudent = $client->getStudentWithdrawn($studentId);
//    }
//
//    catch (SoapFault $e) {
//        // handle issues returned by the web service
//        echo 'soap-error';
//    }


$resultStudent = $client->__soapCall("getStudentWithdrawn",array($studentId));

    $withdrawn = $resultStudent['withdrawn'];
    return $withdrawn;
}


function getQCA($academicyear, $studentId)
{

    $qcaquery = "SELECT     STCM_Year AS Year, STCM_Student_ID AS StuID, STCM_QCA_Actual_C AS [College Score]
FROM         STCMCommon
WHERE     (STCM_Year = '" . $academicyear . "') AND (STCM_Student_ID = '" . $studentId . "')";

    $qcasresult = mssql_query($qcaquery);

    //print_r($qcasresult);

    //echo 'QCA Score as logged with MIS<br/>';
    //echo 'hardcoded to: 07041217<br/>';
    //echo 'hardcoded to year: 2010<br/>';
    // loop through the returned rows and print the results
    while ($row = mssql_fetch_assoc($qcasresult)) {
        $qca = $row['College Score'];
    }
    return $qca;
}

function getPossbileUnitMarks($gradesArray, $coloursArray, $current, $id, $unit, $critId, $learnerId, $colId)
{
    $grades = explode(',', $gradesArray);
    $colours = explode(',', $coloursArray);

    //    print_r($grades);
    echo '<br/>';
    //print_r($colours);

    $combined = array_combine($grades, $colours);
    //print_r($combined);

    echo '<input type="hidden" name="unitName[', $colId, '][id]" value="', $critId, '"/>';
    echo '<input type="hidden" name="unitName[', $colId, '][learnerId]" value="', $learnerId, '"/>';
    echo '<input type="hidden" name="unitName[', $colId, '][unit]" value="', $unit, '"/>';

    echo '<select class="select_list" name="unitName[', $colId, '][select]">';

    echo '<option>-</option>';

    foreach ($combined as $key => $item) {
        $selected = '';
        $colour = '';
        if (trim($current) == trim($key)) {
            $selected = 'selected="selected"';
            $colour = $item;
        }
        echo '"<option ', $selected, ' style="color:' . $item . '" >' . $key . '</option>';
    }

    echo '</select>';
    //    return $colour;
}

// Get the colour for the mark 
function getColour($unitId, $value, $type)
{

    // Check the type to use the right query
    if ($type == 'units') {

        $query = "SELECT * FROM  moodle.unit_tracker_units u
    JOIN moodle.unit_tracker_marks mu ON mu.id=u.markid
    WHERE u.id='" . $unitId . "'";

    } elseif ($type == 'criteria') {
        $query = "SELECT * FROM  moodle.unit_tracker_units_criteria u
    JOIN moodle.unit_tracker_marks_criteria mu ON mu.id=u.markid
    WHERE u.id='" . $unitId . "'";
    }
    //echo $query;
    $result = mysql_query($query);

    while ($row = mysql_fetch_assoc($result)) {

        $grades = explode(',', $row['type']);
        $colours = explode(',', $row['colours']);
        //        print_r($grades);
        $combined = array_combine($grades, $colours);
        //        print_r($combined);
    }

    foreach ($combined as $key => $item) {
        //        echo '-' . $key;
        //        echo '-' . $item;
        //        echo '-' . $value;
        if (trim($value) == trim($key)) {
            echo 'hit on ' . $item;
            $colour = $item;
        }
    }
    return trim($colour);
}

?>