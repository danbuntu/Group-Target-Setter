<?php
/**
 * Created by JetBrains PhpStorm.
 * User: DATTWOOD
 * Date: 17/01/12
 * Time: 09:19
 * To change this template use File | Settings | File Templates.
 */
include('top_include.php');


//$_POST['course_id'];
//$_POST['group_id'];
//$_POST['site'];
//$_POST['background'];
//$_POST['learning_styles'];
//$_POST['level_skills'];
//$_POST['support_needs'];
//$_POST['differentiation_needs'];
//$_POST['special_circumstances'];
//$_POST['confidence'];
//$_POST['other'];
//$_POST['tutor'];
//$_POST['date'];


//format the date
$date = date('Y-m-d', strtotime($_POST['date']));

//check group_id
if ($_POST['group_id'] == 'All Groups') {
    $and = "";
} else {
    $and = " AND group_id='" . $_POST['group_id'] . "'";
}

// check for a record
$query = "SELECT * FROM group_profiles WHERE course_id='" . $_POST['course_id'] . "'" . $and;
$result = $mysqli->query($query);
echo $query;
echo 'rows: ' , $result->num_rows;
if ($result->num_rows > 0) {
    echo '<h2>Updating the Details</h2>';
//    echo 'record found';
    $queryUpdate = "UPDATE group_profiles ";
    $querySet = " SET background='" . $_POST['background'] . "', preferred_learning_styles='" . $_POST['learning_styles'] . "', level_of_key_skills='" . $_POST['level_skills'] . "', difficulties='" . $_POST['difficulties'] . "', circumstances='" . $_POST['special_circumstances'] . "', confidence='" . $_POST['confidence'] . "', differentiation='" . $_POST['differentiation_needs'] . "', other='" . $_POST['other'] . "', facilitators='" . $_POST['facilitators'] . "', site='" . $_POST['site'] . "', tutor='" . $_POST['tutor'] . "', date='" . $date . "'";
    $where = " WHERE course_id='" . $_POST['course_id'] . "'" . $and;
$query = $queryUpdate . $querySet . $where;
    echo $query;
} else {
    echo '<h2>Creating the record</h2>';
//    echo 'no record yet';
    $queryInsert = "INSERT INTO group_profiles (group_id, course_id, site, background, preferred_learning_styles, level_of_key_skills, difficulties, circumstances, confidence , differentiation, other, facilitators, tutor, date )";
    $values = " VALUES ('" . $_POST['group_id'] . "','" . $_POST['course_id'] . "','" .  $_POST['site'] . "','" . $_POST['background'] . "','" . $_POST['learning_styles'] . "','" . $_POST['level_skills'] . "','" . $_POST['difficulties'] . "','" . $_POST['special_circumstances'] . "','" . $_POST['confidence'] . "','" . $_POST['differentiation_needs'] . "','" . $_POST['other'] . "','" . $_POST['facilitators'] .  "','" . $_POST['tutor'] . "','" . $date . "')";
$query = $queryInsert . $values;
    echo $query;
}


$mysqli->query($query);


?>

<meta http-equiv="Refresh" content="5;URL='group2.php'" />