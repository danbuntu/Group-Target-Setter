<?php
include('top_include.php');
global $CFG, $COURSE, $USER, $DB;


// crit stuff

if (isset($_POST['add_new_criteria'])) {
    $query = "INSERT INTO  {$CFG->prefix}unit_tracker_units_criteria (name, description, unitid, markid) VALUES ('" . $_POST['unitName'] . "','" . $_POST['unitDescription'] . "','" . $_POST['id'] . "','" . $_POST['marking'] . "')";
    //    echo $query;
    $DB->execute($query);
    $critId = $_POST['critId'];
    // unset the post variables

}

if (isset($_POST['updateCriteria'])) {
    $query = "UPDATE   {$CFG->prefix}unit_tracker_units_criteria SET markid='" . $_POST['marking'] . "', name='" . $_POST['criteriaName'] . "', description='" . $_POST['criteriaDescription'] . "' WHERE id='" . $_POST['criteriaId'] . "'";
    //    echo $query;
    $DB->execute($query);
    // unset the post variables

}

if (isset($_POST['deleteCriteria'])) {
    $query = "DELETE FROM  {$CFG->prefix}unit_tracker_units_criteria WHERE id='" . $_POST['criteriaId'] . "' AND unitId='" . $_POST['unitId'] . "'";
//        echo $query;
    $DB->execute($query);
    // unset the post variables

}

?>
<div class="container" xmlns="http://www.w3.org/1999/html">
    <?php topbar('Unit Tracker'); ?>
</div>

<div class="container-fluid">


    <?php
    if (isset($_POST['id'])) {
        $critId = $_POST['id'];
    }

    if (isset($_POST['name'])) {
        $critName = $_POST['name'];
    }

               ?>
   <a class="btn btn-success" href="edit_units2_2.php?courseId=<?php echo $_GET['courseId']; ?>" ><i class=" icon-chevron-left icon-white"></i>Back to the Units</a>
    <div class="well units">


    <h1>Criteria for unit <?php echo $critName; ?></h1>

        <?php
// get all the criteria

    $query = "SELECT {$CFG->prefix}unit_tracker_units_criteria.id, name, unitid, description, markid, {$CFG->prefix}unit_tracker_marks_criteria.id as markid2, type
FROM {$CFG->prefix}unit_tracker_units_criteria
JOIN {$CFG->prefix}unit_tracker_marks_criteria ON {$CFG->prefix}unit_tracker_units_criteria.markid={$CFG->prefix}unit_tracker_marks_criteria.id
WHERE unitid='" . $critId . "'";

    $crits = $DB->get_records_sql($query);

    if (empty($crits)) {
        echo '<div class="alert alert-error">There are no criteria set yet for this course</div>';
    } else {
        ?>

                                    <table class="table">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Marking</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead><tbody>
                <?php
        foreach ($crits as $crit) {

            ?>
        <form action="edit_crits2.php" method="POST">
            <tr>
                <td><input type="hidden" name="criteriaId" value="<?php echo $crit->id; ?>"/></td>
                <td><input type="text" name="criteriaName" value="<?php echo $crit->name; ?>"/></td>
                <td><textarea name="criteriaDescription"><?php echo $crit->description; ?></textarea></td>
                <td><?php echo $crit->unitid; ?></td>
                <td>
                    <?php
                    //            echo ' markid ' . $row['markid'];
//                                $queryMark = "SELECT id, type FROM unit_tracker_marks_criteria";
                    $resultMark = $DB->get_records('unit_tracker_marks_criteria');

                    echo '<select name="marking"/>';
                    foreach ($resultMark as $rowMark) {

                        if ($rowMark->id == $crit->markid) {
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
                    <input class="btn btn-success" type="submit" name="updateCriteria" value="Update"/>
        </form>
        </td>
        <td>
            <form action="edit_crits2.php" method="POST">
                <input type="hidden" name="criteriaId" value="<?php echo $crit->id; ?>"/>
                <input type="hidden" name="unitId" value="<?php echo $critId; ?>"/>
                <input type="hidden" id="critId" name="id" value="<?php echo $critId; ?>">
                <input type="hidden" id="critName" name="name" value="<?php echo $critName; ?>">
                <input class="btn btn-danger" type="submit" name="deleteCriteria" value="Delete"/>
            </form>
        </td>


        </tr>


    <?php

        }
     ?>
                                    </tbody></table>
                                        <?php
    }
    ?>
        <div class="button-right">
    <a class="btn btn-success" data-toggle="modal" href="#Add_crit_<?php echo $row->id; ?>"><i class="icon-plus-sign icon-white"></i> Add a new criteria</a>
            </div>
   </div>

    <div class="modal hide" id="Add_crit_<?php echo $row->id; ?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3>Add a new unit criteria for <?php echo $critName; ?></h3>
        </div>
        <div class="modal-body">

            <form class="well" action="edit_crits2.php?courseId=<?php echo $_GET['courseId']; ?>" method="POST">
                <label class="control-label" for="unitName">Unit name</label>
                <input class="input-xlarge" type="text" name="unitName" id="unitName"/>
                <label class="control-label" for="unitDescription">Unit Description</label>
                <textarea  class="input-xlarge" name="unitDescription" id="unitDescription"></textarea>

                <?php
// Get possbile marking scehemes
//                                $query = "SELECT id, type FROM unit_tracker_marks_criteria";
                $resultMarks = $DB->get_records('unit_tracker_marks_criteria');
                ?>
                <label class="control-label" for="marking">Unit Marking Scheme</label>
                <?php
                echo '<select name="marking" id="marking">';
                foreach ($resultMarks as $mark) {
                    echo '<option value="', $mark->id, '">', $mark->type, '</option>';
                }
                echo '</select>';
                ?>
                <br>
                <input type="hidden" id="critId" name="id" value="<?php echo $critId; ?>">
                <input type="hidden" id="critName" name="name" value="<?php echo $critName; ?>">
                <input class="btn btn-primary" type="submit" name="add_new_criteria"
                       value="Add new criteria"/>

            </form>

        </div>
    </div>
</div>