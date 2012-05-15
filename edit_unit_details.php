<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 01/07/11
 * Time: 11:04
 * Edit and add new unit critieria
 */
require_once("../../config.php");
include('jquery_imports.php');

//echo $_POST['id'];
$unitId = $_POST['id'];

//echo 'session id is ' . session_id();
// check is the session variable has already been set
if (empty($_SESSION['unitid'])) {
    $_SESSION['unitid'] = $unitId;
}
//echo 'seesion id: ' . $_SESSION['unitid'];

if (isset($_POST['add_new_criteria'])) {
    echo 'marking: ' . $_POST['marking'];
    $query = "INSERT INTO unit_tracker_units_criteria (name, description, unitid, markid) VALUES ('" . $_POST['unitName'] . "','" . $_POST['unitDescription'] . "','" . $_SESSION['unitid'] . "','" . $_POST['marking'] . "')";
//    echo $query;
    mysql_query($query);
}

if (isset($_POST['updateCriteria'])) {
    $query = "UPDATE  unit_tracker_units_criteria SET markid='" . $_POST['marking'] . "', name='" . $_POST['criteriaName'] . "', description='" . $_POST['criteriaDescription'] . "' WHERE id='" . $_POST['criteriaId'] . "'";
//    echo $query;
    mysql_query($query);
}

if (isset($_POST['deleteCriteria'])) {
    $query = "DELETE FROM unit_tracker_units_criteria WHERE id='" . $_POST['criteriaId'] . "' AND unitid='" . $_SESSION['unitid'] . "'";
//    echo $query;
    mysql_query($query);
}

// Get the unit name

$query = "SELECT * FROM unit_tracker_units WHERE id='" . $_SESSION['unitid'] . "'";
$result = mysql_query($query);

while ($row = mysql_fetch_assoc($result)) {
    $unitName = $row['name'];
    $unitDescription = $row['description'];
}

?>
<div id="page">
<div id="layout">
<div class="demo">

    <?php
echo '<h1>Edit details for Criteria for unit: ', $unitName, ' ', ' ID: ', $_SESSION['unitid'] . '</h1>';
echo '<font color="red"><b>Warning all unit names must be 2 characters or longer</b></font><br/>';
echo '<a href="edit_units.php"><img src="./images/back-icon.png" width="30px" border="0">Back to the unit screen</a>';
echo '<br/>';
//echo $unitDescription;

// get the current critrias for this unit

//$query = "SELECT * FROM unit_tracker_units_criteria WHERE unitid='" . $_SESSION['unitid'] . "'";
$query = "SELECT unit_tracker_units_criteria.id, name, unitid, description, markid, unit_tracker_marks_criteria.id as markid2, type
FROM unit_tracker_units_criteria
JOIN unit_tracker_marks_criteria ON unit_tracker_units_criteria.markid=unit_tracker_marks_criteria.id
WHERE unitid='" . $_SESSION['unitid'] . "'";


//echo $query;
$result = mysql_query($query);
?>
<table>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Description</th>
        <th>Marking</th>
    </tr>
<?php
while ($row = mysql_fetch_assoc($result)) {

    ?>
    <form action="edit_unit_details.php" method="POST">
        <tr>
            <td><input type="hidden" name="criteriaId" value="<?php echo $row['id']; ?>"/></td>
            <td><input type="text" name="criteriaName" value="<?php echo $row['name']; ?>"/></td>
            <td><textarea name="criteriaDescription"><?php echo $row['description']; ?></textarea></td>
            <td><?php echo $row['unitid']; ?></td>
                <td>
                       <?php
            echo ' markid ' . $row['markid'];
            $queryMark = "SELECT * FROM unit_tracker_marks_criteria";
                    $resultMark = mysql_query($queryMark);

                    echo '<select name="marking"/>';
                            echo '<option>--Select--</option>';
                        while ($rowMark = mysql_fetch_assoc($resultMark)) {

                            if ($rowMark['id'] == $row['markid']) {
                                $selected = 'selected="selected" ';
                            } else {
                                $selected = ' ';
                            }
                echo '<option ' . $selected . 'value="' . $rowMark['id'] . '" >' . $rowMark['type'] . '</option>';
                }
                    echo '</select>';
                ?>

    </td>
    </td>
            <td>
                <input type="submit" name="updateCriteria" value="Update"/>
    </form>
    </td>
    <td>
        <form action="edit_unit_details.php" method="POST">
            <input type="hidden" name="criteriaId" value="<?php echo $row['id']; ?>"/>
            <input type="submit" name="deleteCriteria" value="Delete"/>
        </form>
    </td>


    </tr>

    <?php

}

    ?>
<h1> Add a new unit criteria</h1>
    <table>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Marking</th>
        </tr>
        <tr>
            <form action="edit_unit_details.php" method="POST">
                <td>
                    <input type="text" name="unitName"/>
                </td>
                <td>
                    <textarea name="unitDescription"></textarea>
                </td>
                <td>
                      <?php
// Get possbile marking scehemes
                    $query = "SELECT * FROM unit_tracker_marks_criteria";
                    $result = mysql_query($query);
                    echo '<select name="marking">';
                        while ($row = mysql_fetch_assoc($result)) {
                       echo '<option value="' , $row['id'] , '">' , $row['type'] , '</option>';
                    }
                    echo '</select>';
                    ?>
                </td>
                <td>
                    <input type="submit" name="add_new_criteria" value="Add new criteria"/>
            </form>
            </td></tr>
    </table>

</div>

    </div>
    </div>
