<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 01/07/11
 * Time: 11:04
 * Edit and add new unit critieria
 */
include('top_include.php');
?>
<div class="container-fluid">
    <?php
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
        $mysqli->query($query);
    }

    if (isset($_POST['updateCriteria'])) {
        $query = "UPDATE  unit_tracker_units_criteria SET markid='" . $_POST['marking'] . "', name='" . $_POST['criteriaName'] . "', description='" . $_POST['criteriaDescription'] . "' WHERE id='" . $_POST['criteriaId'] . "'";
        //    echo $query;
        $mysqli->query($query);
    }

    if (isset($_POST['deleteCriteria'])) {
        $query = "DELETE FROM unit_tracker_units_criteria WHERE id='" . $_POST['criteriaId'] . "' AND unitid='" . $_SESSION['unitid'] . "'";
        //    echo $query;
        $mysqli->query($query);
    }

// Get the unit name

    $query = "SELECT name, description FROM unit_tracker_units WHERE id='" . $_SESSION['unitid'] . "'";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_object()) {
        $unitName = $row->name;
        $unitDescription = $row->description;
    }

    ?>
    <div id="page">
        <div id="layout">
            <div class="demo">

                <?php
                echo '<h1>Edit details for Criteria for unit: ', $unitName, ' ', ' ID: ', $_SESSION['unitid'] . '</h1>';
                echo '<font color="red"><b>Warning all unit names must be 2 characters or longer</b></font><br/>';
                echo '<a href="edit_units2.php"><img src="./images/back-icon.png" width="30px" border="0">Back to the unit screen</a>';
                echo '<br/>';
//echo $unitDescription;

// get the current critrias for this unit

                $query = "SELECT unit_tracker_units_criteria.id, name, unitid, description, markid, unit_tracker_marks_criteria.id as markid2, type
FROM unit_tracker_units_criteria
JOIN unit_tracker_marks_criteria ON unit_tracker_units_criteria.markid=unit_tracker_marks_criteria.id
WHERE unitid='" . $_SESSION['unitid'] . "'";


//echo $query;
                $result = $mysqli->query($query);
                ?>
            <table>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Marking</th>
                    <th></th>
                    <th></th>
                </tr>
                <?php
                while ($row = $result->fetch_object()) {

                    ?>
                    <form action="edit_unit_details2.php" method="POST">
                        <tr>
                            <td><input type="hidden" name="criteriaId" value="<?php echo $row->id; ?>"/></td>
                            <td><input type="text" name="criteriaName" value="<?php echo $row->name; ?>"/></td>
                            <td><textarea name="criteriaDescription"><?php echo $row->description; ?></textarea></td>
                            <td><?php echo $row->unitid; ?></td>
                            <td>
                                <?php
                                //            echo ' markid ' . $row['markid'];
                                $queryMark = "SELECT id, type FROM unit_tracker_marks_criteria";
                                $resultMark = $mysqli->query($queryMark);

                                echo '<select name="marking"/>';
                                while ($rowMark = $resultMark->fetch_object()) {

                                    if ($rowMark->id == $row->markid) {
                                        $selected = 'selected="selected" ';
                                    } else {
                                        $selected = ' ';
                                    }
                                    echo '<option ' . $selected . 'value="' . $rowMark->id . '" >' . $rowMark->type . '</option>';
                                }
                                echo '</select>';
                                ?>

                            </td>
                            </td>
                            <td>
                                <input class="btn btn-success" type="submit" name="updateCriteria" value="Update"/>
                    </form>
                    </td>
                    <td>
                        <form action="edit_unit_details2.php" method="POST">
                            <input type="hidden" name="criteriaId" value="<?php echo $row->id; ?>"/>
                            <input class="btn btn-danger" type="submit" name="deleteCriteria" value="Delete"/>
                        </form>
                    </td>


                    </tr>
        </table>

    <?php

                }

                ?>
                <h1> Add a new unit criteria</h1>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Marking</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <form action="edit_unit_details2.php" method="POST">
                            <td>
                                <input type="text" name="unitName"/>
                            </td>
                            <td>
                                <textarea name="unitDescription"></textarea>
                            </td>
                            <td>
                                <?php
// Get possbile marking scehemes
                                $query = "SELECT id, type FROM unit_tracker_marks_criteria";
                                $result = $mysqli->query($query);
                                echo '<select name="marking">';
                                while ($row = $result->fetch_object()) {
                                    echo '<option value="', $row->id, '">', $row->type, '</option>';
                                }
                                echo '</select>';
                                ?>
                            </td>
                            <td>
                                <input class="btn btn-primary" type="submit" name="add_new_criteria"
                                       value="Add new criteria"/>
                        </form>
                        </td></tr>
                </table>
            </div>
        </div>
    </div>
</div>