<?php
include('top_include.php');

?>
<div class="container">
    <?php topbar('Unit Tracker'); ?>
</div>


<div class="container-fluid">

    <?php
    /**
     * Created by JetBrains PhpStorm.
     * User: DATTWOOD
     * Date: 04/07/12
     * Time: 13:04
     * To change this template use File | Settings | File Templates.
     */


    $courseId = $_GET['courseId'];


// stuff to process the incoming forms
    if (isset($_POST['deleteUnit'])) {
        $query = "DELETE FROM {$CFG->prefix}unit_tracker_units WHERE id='" . $_POST['id'] . "'";
        //    echo $query;
        $DB->execute($query);
        // unset the post variables

    }

    if (isset($_POST['updateUnit'])) {
        $query = "UPDATE {$CFG->prefix}unit_tracker_units set markid='" . $_POST['marking'] . "', name='" . $_POST['unitName'] . "', description='" . $_POST['unitDescription'] . "' WHERE id='" . $_POST['unitId'] . "'";
        //    echo $query;
        $DB->execute($query);
        // unset the post variables

    }

    if (isset($_POST['add_new_unit'])) {
        $query = "INSERT INTO {$CFG->prefix}unit_tracker_units (name, description, courseid, markid) VALUES ('" . $_POST['unitName'] . "','" . $_POST['unitDescription'] . "','" . $courseId . "','" . $_POST['marking'] . "')";
//       echo $query;
        $DB->execute($query);
        unset($_POST['add_new_unit']);
        // unset the post variables

    }

//global $CFG, $COURSE, $USER, $DB;
    ?>

    <a class="btn btn-success" href="tracker2.php"><i class=" icon-chevron-left icon-white"></i>Back to the Unit Tracker</a>

    <div class="well units">

        <h1>Edit and Add Units for Course <?php echo $courseName ?> </h1>

        <?php

// Get all the units for the course
        $query = "SELECT {$CFG->prefix}unit_tracker_units.id, name, courseid, description, markid, {$CFG->prefix}unit_tracker_marks.id as markid2, type
FROM {$CFG->prefix}unit_tracker_units JOIN {$CFG->prefix}unit_tracker_marks ON {$CFG->prefix}unit_tracker_units.markid={$CFG->prefix}unit_tracker_marks.id
WHERE courseid='" . $courseId . "'";
//echo $query;
        $result = $DB->get_records_sql($query);

        if (count($result) == 0) {
            echo '<div class="alert alert-error">There are no units yet for this course</div>';
        } else {

            ?>

            <table class="table">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Unit name</th>
                    <th>Description</th>
                    <th>Marking Scheme</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <?php

                    foreach ($result as $row) {
                        ?>

                    <tr>
                        <td>
                            <?php echo $row->id; ?>
                            <form action="tracker2.php" method="POST">
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
                            $resultMark = $DB->get_records_sql($queryMark);

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
                            <form action="edit_units2_2.php?courseId=<?php echo $courseId; ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
                                <input class="btn btn-danger" type="submit" name="deleteUnit" value="Delete"/>
                            </form>
                        </td>
                        <td>

                            <button class="btn btn-info" data-toggle="collapse"
                                    data-target="#crit_<?php echo $row->id;?>">
                                <i class="icon-zoom-in icon-white"></i> Show Unit Criteria
                            </button>
                        </td>

                    </tr>
                    <tr>
                        <td colspan="7">
                            <div id="crit_<?php echo $row->id;?>" class="collapse">
                                <?php

                                // get all the criteria
                                echo '<div class="well breakout">';
                                include('edit_crits.php');
                                echo '</div>';

                                ?>
                            </div>

                        </td>
                    </tr>

                        <?php
                    }
                    ?>
                </tbody>
            </table>

            <?php } ?>

        </table>


        <div class="button-right"><a class="btn btn-success" data-toggle="modal" href="#Add_unit"><i
            class="icon-plus-sign icon-white"></i> Add a new unit</a></div>

        <div class="modal hide" id="Add_unit">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Add a new unit to <?php echo $courseName; ?></h3>
            </div>

            <form class="well" action="edit_units2_2.php?courseId=<?php echo $courseId; ?>" method="POST">


                <label class="control-label" for="unitName">Unit name</label>
                <input type="text" name="unitName" id="unitName"/>
                <label class="control-label" for="unitDescription">Unit Description</label>
                <textarea name="unitDescription" id="unitDescription"></textarea>

                <?php
// Get possbile marking schemes
                $query = "SELECT id, type FROM {$CFG->prefix}unit_tracker_marks";
                $result = $DB->get_records_sql($query);
                echo '<label class="control-label" for="marking">Marking Scheme</label>';
                echo '<select name="marking" id="marking">';
                foreach ($result as $row) {
                    echo '<option value="', $row->id, '">', $row->type, '</option>';
                }
                echo '</select>';
                ?>
                <br>
                <input class="btn btn-primary" type="submit" name="add_new_unit" value="Add new unit "/>
            </form>

        </div>
    </div>
</div>
</div>