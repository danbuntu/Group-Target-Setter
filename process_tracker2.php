<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 08/07/11
 * Time: 12:50
 * Process the tracker form and make the changes needed
 */
include('top_include.php');


echo '<h1><font color="red">Processing information hold tight</font></h1>';

$courseCode = $_POST['courseCode'];
$learner= $_POST['learner'];
$select = $_POST['select'];

$unit1 = $_POST['unit1'];
$unit2 = $_POST['unit2'];
$unitCode1 = $_post['unitcode1'];

$unitName = $_POST['unitName'];
//print_r($unitName);
echo $courseCode;
echo '</br>';
echo '</br>';
print_r($learner);
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

    echo 'lid: ' , $value['learnerId'] , ' ' , ' id' , $value['id'] , ' ' , $value['select'] , ' ' , $value['unit'];
          if ($value['unit'] == 'unit') {
              echo ' a unit </br>';

              // test for a record already existing
              $query = "SELECT * FROM mdl_unit_tracker_user_units WHERE unit_id='" . $value['id'] . "' AND moodle_id='" . $value['learnerId'] . "'";
              echo $query;
              $resultUnits = $DB->get_records_sql($query);
//            $num_rows_update = $mysqli->num_rows($result);
    
//              echo 'num row: ' . $num_rows_update;
//              echo 'rows: ' . $resultUnits->num_rows;
              if (count($resultUnits) >= 1 ) {
                  echo 'hit';
                  $queryUpdate = "UPDATE mdl_unit_tracker_user_units SET target='" . $value['select'] . "', colour='" . getColour($value['id'], $value['select'], 'units', $DB, $CFG) . "' WHERE unit_id='" . $value['id'] . "' AND moodle_id='" . $value['learnerId'] . "'";
                  echo $queryUpdate;
                  $DB->execute($queryUpdate);
              } else {
                  echo 'hit2ww';
                  echo 'test2';
                    $queryInsert = "INSERT INTO mdl_unit_tracker_user_units (unit_id, target, colour, moodle_id) VALUES ('" . $value['id'] . "','" . $value['select'] . "','" . getColour($value['id'], $value['select'], 'units', $DB, $CFG) . "','" . $value['learnerId'] . "')";
                  echo 'test';
                  echo $queryInsert;
                  $DB->execute($queryInsert);
              }

          } else {
              echo ' a criteria </br>';

 // test for a record already existing
              $query = "SELECT * FROM mdl_unit_tracker_user_criteria WHERE criteria_id='" . $value['id'] . "' AND moodle_id='" . $value['learnerId'] . "'";
              echo $query;
              $result = $DB->get_records_sql($query);
//            $num_rows_update = $mysqli->num_rows($result);

//              echo 'num row: ' . $num_rows_update;
              if (count($result) >= 1 ) {
                  $queryUpdate = "UPDATE mdl_unit_tracker_user_criteria SET target='" . $value['select'] . "', colour='" . getColour($value['id'], $value['select'], 'criteria', $DB, $CFG) . "' WHERE criteria_id='" . $value['id'] . "' AND moodle_id='" . $value['learnerId'] . "'";
                  echo $queryUpdate;
                  $DB->execute($queryUpdate);
              } else {
                    $queryInsert = "INSERT INTO mdl_unit_tracker_user_criteria (criteria_id, user_id, target, colour, moodle_id) VALUES ('" . $value['id'] . "','" . $value['learnerId'] . "','" . $value['select'] . "','" . getColour($value['id'], $value['select'], 'criteria', $DB, $CFG) . "','" . $value['learnerId'] . "')";
                    echo $queryInsert;
                  $DB->execute($queryInsert);
              }


          }

}


echo '<meta http-equiv="refresh" content="0; url=/blocks/group_targets/tracker2.php">';

?>