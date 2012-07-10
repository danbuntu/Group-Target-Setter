<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 30/06/11
 * Time: 13:36
 * Page to admin the units per course
 */

include('top_include.php');
global $CFG, $COURSE, $USER, $DB;
?>
<div class="container-fluid">
    <?php
    $courseId = $_GET['var1'];

    session_start();
//echo 'session id is ' . session_id();

// check is the session variable has already been set
    if (empty($_SESSION['courseid'])) {
        $_SESSION['courseid'] = $courseId;
    }

    unset($_SESSION['unitid']);

//echo ' session course id is ' . $_SESSION['courseid'];

    if (isset($_POST['deleteUnit'])) {
        $query = "DELETE {$CFG->prefix}FROM unit_tracker_units WHERE id='" . $_POST['id'] . "'";
        //    echo $query;
        $DB->execute($query);
    }

    if (isset($_POST['updateUnit'])) {
        $query = "UPDATE {$CFG->prefix}unit_tracker_units set markid='" . $_POST['marking'] . "', name='" . $_POST['unitName'] . "', description='" . $_POST['unitDescription'] . "' WHERE id='" . $_POST['unitId'] . "'";
        //    echo $query;
        $DB->execute($query);
    }

    if (isset($_POST['add_new_unit'])) {
        $query = "INSERT INTO mdl_unit_tracker_units (name, description, courseid, markid) VALUES ('" . $_POST['unitName'] . "','" . $_POST['unitDescription'] . "','" . $courseId . "','" . $_POST['marking'] . "')";
        //    echo $query;
        $DB->execute($query);
    }


// Get the course title
//    $query = "SELECT coursename FROM unit_tracker_courses WHERE id='" . $_SESSION['courseid'] . "'";
//    $result = $mysqli->query($query);


    $result = $DB->get_records('unit_tracker_courses', array('id' => $_SESSION['courseid']));

    foreach ($result as $row) {
        $courseName = $row->coursename;
    }
    ?>

    <div id="page">
        <div id="layout">
            <div class="demo">

                <h1>Edit and Add Units for Course <?php echo $courseName ?> </h1>
                <a href="tracker2.php?courseid=<?php echo $_SESSION['course_code_session'] . '&var1=' . $_SESSION['course_context_session']; ?>"><img
                    src="./images/back-icon.png" width="30px" border="0">Back to the main screen</a>

                <?php

                $query = "SELECT {$CFG->prefix}unit_tracker_units.id, name, courseid, description, markid, {$CFG->prefix}unit_tracker_marks.id as markid2, type
FROM {$CFG->prefix}unit_tracker_units JOIN {$CFG->prefix}unit_tracker_marks ON {$CFG->prefix}unit_tracker_units.markid={$CFG->prefix}unit_tracker_marks.id
WHERE courseid='" . $_SESSION['courseid'] . "'";
//echo $query;
                $result = $DB->get_records($query);
                ?>
                <table>
                    <tr>
                        <th>Id</th>
                        <th>Unit name</th>
                        <th>Description</th>
                        <th>Marking Scheme</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <?php
                    foreach ($result as $row) {
                        ?>

                        <tr>
                            <td>
                                <?php echo $row->id; ?>
                                <form action="edit_units2.php" method="POST">
                                    <input type="hidden" name="unitId" value="<?php echo $row->id; ?>"/>
                            </td>
                            <td>
                                <input type="text" name="unitName" value="<?php echo $row->name; ?>"/>
                            </td>
                            <td>

                                <textarea name="unitDescription"><?php echo $row->description; ?></textarea>
                            </td>
                            <td>

                                <?php
                                //            echo ' markid ' . $row->markid;
                                $queryMark = "SELECT id, type FROM {$CFG->prefix}unit_tracker_marks";
                                //                        echo $queryMark;
                                $resultMark = $DB->get_records($queryMark);

                                echo '<select name="marking"/>';
                                foreach ($resultMark as $rowMark) {

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
                            <td>
                                <input class="btn btn-success" type="submit" name="updateUnit" value="Update"/>
                                </form>
                            </td>
                            <td>
                                <form action="edit_units2.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
                                    <input class="btn btn-danger" type="submit" name="deleteUnit" value="Delete"/>
                                </form>
                            </td>
                            <td>
                                <form action="edit_unit_details2.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
                                    <input class="btn btn-info" type="submit" name="editUnit" value="Edit unit criteria"/>
                                </form>
                            </td>

                        </tr>

                        <?php

                    }
                    ?>

                </table>
                <h1> Add a new unit</h1>
                <table>
                    <tr>
                        <th>Unit Title</th>
                        <th>Unit Description</th>
                        <th>Marking Scheme</th>
                        <th></th>
                    </tr>
                    <tr>
                        <form action="edit_units2.php" method="POST">
                            <td>
                                <input type="text" name="unitName"/>
                            </td>
                            <td>
                                <textarea name="unitDescription"></textarea>
                            </td>
                            <td>
                                <?php
// Get possbile marking scehemes
                                $query = "SELECT id, type FROM {$CFG->prefix}unit_tracker_marks";
                                $result = $DB->get_records($query);
                                echo '<select name="marking">';
                                foreach ($result as $row) {
                                    echo '<option value="', $row->id, '">', $row->type, '</option>';
                                }
                                echo '</select>';
                                ?>
                            </td>
                            <td>
                                <input class="btn btn-primary" type="submit" name="add_new_unit" value="Add new unit "/>
                        </form>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>