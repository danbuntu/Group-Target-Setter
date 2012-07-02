<!--A modified version of /blocks/ilp/classes/dashboard/tabs/ilp_dashboard_reports_tab.html to enable filtering of deadlines
and disabling of the status drop down to allow bulk updates-->

<?php

//     showTargets();

if ($_POST['deadline'] == 'Only Expired') {
    foreach ($reportfields as $field) {
        $fieldname = $field->id . "_field";
        // check the deadline field
        //@FIXME write to dynamically pickup the deadline field
        if ($field->plugin_id == 4) {
            // remove any image from the data
            $deadline = preg_replace("/<img[^>]+\>/i", "", $entry_data->$fieldname);
            $deadline = strtotime($deadline);

            // todays date
            $date = strtotime(date("D d M Y"));
            if ($_POST['deadline'] == 'Only Expired') {

                ?>
            <div class="clearfix"></div>
            <div class="reports-container">
                <div class="left-reports">
                    <?php
                    foreach ($reportfields as $field2) {
                        if (!in_array($field2->id, $dontdisplay)) {
                            $fieldname2 = $field2->id . "_field";

                            //create the fieldname which will be used in to retrieve data from the object

                            ?>

                            <p>
                                <strong><?php echo $field2->label; ?>
                                    : </strong> <?php    echo (!empty($entry_data->$fieldname2)) ? $entry_data->$fieldname2 : '';    ?>
                            </p>
                            <?php
                        }
                    }

                    ?>
                </div>
                <div class="right-reports">
                    <p><b><?php echo get_string('addedby', 'block_ilp') . " :</b> " . $entry_data->creator;?></p>
                    <?php if (!empty($has_courserelated)) { ?>
                    <p><b><?php echo get_string('course', 'block_ilp') . " :</b> " . $entry_data->coursename;?>
                    </p><?php } ?>
                    <?php if (!empty($has_deadline)) { ?><p><?php echo get_string('deadline', 'block_ilp');?>
                    : <?php //userdate($entry->deadline, get_string('strftimedate'));?>date</p><?php } ?>
                    <p><b><?php echo get_string('date') . " :</b> " . $entry_data->modified;?></p>
                </div>
                <div class="clearfix"></div>

                <?php
            }
        }
    }
    ?>


    <?php
} else {
    ?>
    <div class="clearfix"></div>
<div class="reports-container">
    <div class="left-reports">
        <?php foreach ($reportfields as $field) {
        if (!in_array($field->id, $dontdisplay)) {
            ?>

            <?php
            //create the fieldname which will be used in to retrieve data from the object
            $fieldname = $field->id . "_field";

            ?>
            <p>
                <strong><?php echo $field->label; ?>
                    : </strong> <?php    echo (!empty($entry_data->$fieldname)) ? $entry_data->$fieldname : '';    ?>
            </p>
            <?php
        }
    }
        ?>
    </div>
    <div class="right-reports">
        <p><b><?php echo get_string('addedby', 'block_ilp') . " :</b> " . $entry_data->creator;?></p>
        <?php if (!empty($has_courserelated)) { ?>
        <p><b><?php echo get_string('course', 'block_ilp') . " :</b> " . $entry_data->coursename;?></p><?php } ?>
        <?php if (!empty($has_deadline)) { ?><p><?php echo get_string('deadline', 'block_ilp');?>
        : <?php //userdate($entry->deadline, get_string('strftimedate'));?>date</p><?php } ?>
        <p><b><?php echo get_string('date') . " :</b> " . $entry_data->modified;?></p>
    </div>
    <div class="clearfix"></div>

    <?php
}
?>
      </div>

