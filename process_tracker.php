<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 08/07/11
 * Time: 12:50
 * Process the tracker form and make the changes needed
 */

require_once("../../config.php");
include('report_functions.php');
echo '<h1><font color="red">Processing information hold tight</font></h1>';

$courseCode = $_POST['courseCode'];
$learner= $_POST['learner'];
$select = $_POST['select'];

$unit1 = $_POST['unit1'];
$unit2 = $_POST['unit2'];
$unitCode1 = $_post['unitcode1'];

$unitName = $_POST['unitName'];
print_r($unitName);
echo $courseCode;
echo '</br>';
echo '</br>';
//print_r($learner);
echo '</br>';
echo '</br>';
echo ' unitcode: ' , $unitCode1;
// print_r($select);

//echo '<h1>Unit 1 </h1>';
////print_r($unit1);
//
//
//echo '<h1>Unit 2 </h1>';
//print_r($unit2);

echo '<h1>Print the units</h1>';
foreach ($unitName as $value) {

    echo $value['learnerId'] , ' ' , $value['id'] , ' ' , $value['select'] , ' ' , $value['unit'];
          if ($value['unit'] == 'unit') {
              echo ' a unit </br>';

              // test for a record already existing
              $query = "SELECT * FROM unit_tracker_user_units WHERE unit_id='" . $value['id'] . "' AND user_id='" . $value['learnerId'] . "'";
              echo $query;
              $result = mysql_query($query);
            $num_rows_update = mysql_num_rows($result);
    
              echo 'num row: ' . $num_rows_update;
              if ($num_rows_update >= 1 ) {
                  $queryUpdate = "UPDATE unit_tracker_user_units SET target='" . $value['select'] . "', colour='" . getColour($value['id'], $value['select'], 'units') . "' WHERE unit_id='" . $value['id'] . "' AND user_id='" . $value['learnerId'] . "'";
                  echo $queryUpdate;
                  mysql_query($queryUpdate);
              } else {
                    $queryInsert = "INSERT INTO unit_tracker_user_units (unit_id, user_id, target, colour) VALUES ('" . $value['id'] . "','" . $value['learnerId'] . "','" . $value['select'] . "','" . getColour($value['id'], $value['select'], 'units') . "')";
                    echo $queryInsert;
                    mysql_query($queryInsert);
              }

          } else {
              echo ' a criteria </br>';

 // test for a record already existing
              $query = "SELECT * FROM unit_tracker_user_criteria WHERE criteria_id='" . $value['id'] . "' AND user_id='" . $value['learnerId'] . "'";
              echo $query;
              $result = mysql_query($query);
            $num_rows_update = mysql_num_rows($result);

              echo 'num row: ' . $num_rows_update;
              if ($num_rows_update >= 1 ) {
                  $queryUpdate = "UPDATE unit_tracker_user_criteria SET target='" . $value['select'] . "', colour='" . getColour($value['id'], $value['select'], 'criteria') . "' WHERE criteria_id='" . $value['id'] . "' AND user_id='" . $value['learnerId'] . "'";
                  echo $queryUpdate;
                  mysql_query($queryUpdate);
              } else {
                    $queryInsert = "INSERT INTO unit_tracker_user_criteria (criteria_id, user_id, target, colour) VALUES ('" . $value['id'] . "','" . $value['learnerId'] . "','" . $value['select'] . "','" . getColour($value['id'], $value['select'], 'criteria') . "')";
                    echo $queryInsert;
                    mysql_query($queryInsert);
              }


          }

}

echo '<meta http-equiv="refresh" content="0; url=/blocks/group_targets/tracker.php">';

?>