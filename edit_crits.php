<div class="row">
    <div class="span 6">
        <h2>Criteria for <?php echo $row->name; ?></h2>
    </div>
    <div class="button-right">
        <form action="edit_crits2.php?courseId=<?php echo $courseId; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
            <input type="hidden" name="name" value="<?php echo $row->name; ?>"/>
            <button class="btn btn-info" type="submit" name="editUnit" value="Edit unit criteria"/>
            <i class="icon-edit icon-white"></i> Edit unit criteria</button>
        </form>
    </div>
</div>
<?php

// get all the criteria

$query = "SELECT {$CFG->prefix}unit_tracker_units_criteria.id, name, unitid, description, markid, {$CFG->prefix}unit_tracker_marks_criteria.id as markid2, type
FROM {$CFG->prefix}unit_tracker_units_criteria
JOIN {$CFG->prefix}unit_tracker_marks_criteria ON {$CFG->prefix}unit_tracker_units_criteria.markid={$CFG->prefix}unit_tracker_marks_criteria.id
WHERE unitid='" . $row->id . "'";

$crits = $DB->get_records_sql($query);

if (empty($crits)) {
    echo '<h3>There are no criteria set yet for this course</h3>';
} else {
    ?>

<table class="table">
    <thead>
    <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Marking</th>
    </tr>
    </thead>
    <tbody>
        <?php
        foreach ($crits as $crit) {

            ?>
        <tr>
            <td><input type="text" name="criteriaName" value="<?php echo $crit->name; ?>"/></td>
            <td><textarea name="criteriaDescription"><?php echo $crit->description; ?></textarea></td>
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


        </tr>

            <?php

        }
        ?>
    </tbody>
</table>
<?php
}
?>

</div>