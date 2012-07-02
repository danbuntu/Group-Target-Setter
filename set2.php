<?php
include('top_include.php');
global $CFG, $COURSE, $USER, $DB;
?>
<div class="container">
    <?php topbar('Change Target Status', $navItems); ?>
</div>

<div class="container">
<div class="page">

    <?php

//    include ('access_context.php');

    $status = $_GET['status'];
//$USER->id = $USER->id;
//echo 'userid is ' . $USER->id;
//echo 'status is ' . $status;
    $url2 = $CFG->wwwroot . '/blocks/group_targets/set2.php?courseid=' . $_SESSION['course_code_session'] . '&var1=' . $USER->id;

//include('jquery_imports.php');

    $type = '1';
    if (isset($_POST)) {
        $type = htmlentities($_POST['course']);
    }

    if ($type == null) {
        $type = $_SESSION['course_code_session'];
    }

    if (isset($_POST)) {
        $status = htmlentities($_POST['status']);
    } elseif (!isset($_POST)) {
        $status = '';
    }


    if (isset($_POST['filter'])) {
        //    echo 'course field: ' .  htmlentities($_POST['course']);
        $_SESSION['course_code_session'] = htmlentities($_POST['course']);
        //    echo ' course_code_session: ' . $_SESSION['course_code_session'];
    }

    $query = "SELECT fullname FROM " . $CFG->prefix . 'course WHERE id="' . $type . '"';
//echo $query;
    $result = $DB->get_records_sql($query);


    foreach ($result as $row) {
        if ($type == '1') {
            $coursename = 'on all my courses';
        } else {
            $coursename = $row->fullname;
        }
    }

//List all courses the user is a teacher on and allow them to filter by them

    $query = "SELECT c.id, c.fullname  FROM " . $CFG->prefix . "role_assignments ra JOIN " . $CFG->prefix . "context co ON ra.contextid=co.id JOIN " . $CFG->prefix . "course c ON co.instanceid=c.id WHERE userid='" . $USER->id . "' AND roleid='3' AND co.contextlevel='50'";
//    echo $query;
    $result = $DB->get_records_sql($query);
//    echo ' user id is ' . $USER->id;
    $user_id = $USER->id;
//    echo ' type is: ', $type;
    ?>

    <div class="row">
        <div class="span12">
            <div class="row">

                <table class="table">

                    <tr>
                        <th>Select Course:</th>
                        <th>Select Target State:</th>
                        <th>Show only targets with expired deadlines</th>
                    </tr>
                    <tr>
                        <td align="center">
                            <form name="filter" method="POST" action="set2.php">
                                <?php

                                ?>
                                <select name="course" id="select_type">
                                    <option value="1">All courses</option>

                                    <?php
                                    foreach ($result as $row) {
                                        //set the list to the current course
                                        if ($type == $row->id) {
                                            $selected = 'selected="selected"';
                                        } elseif ($type != $row->id) {
                                            $selected = ' ';
                                        }
                                        echo '<option ' . $selected . ' value="' . $row->id . '">' . $row->fullname . '</option>';
                                    }
                                    ?>
                                </select>

                        </td>
                        <td align="center">

                            <?php

                            // get the target types

                            // get the report field id for the state plugin for the target report
                            $query = "SELECT item.id, item.name FROM mdl_block_ilp_report_field fi
                                    JOIN mdl_block_ilp_plugin id ON fi.plugin_id=id.id
                                    JOIN mdl_block_ilp_plu_ste ste ON fi.id=ste.reportfield_id
                                    JOIN mdl_block_ilp_plu_ste_items item ON ste.id=item.parent_id
                                    WHERE report_id='" . $targetId . "' AND id.name='ilp_element_plugin_state'";

//                                          echo $query;
                            $resultStates = $DB->get_records_sql($query);

                            echo '<select name="status" id="select_status">';
                            echo '<option value="0">All States</option>';
                            foreach ($resultStates as $item) {
                                // if the status is empty set it to the first id number from the query above
//                                        if (empty($status)) {
//                                        $status =  $item->id;
//                                    }
                                if ($status == $item->id) {
                                    echo '<option selected="selected" value="', $item->id, '">', $item->name, '</option>';
                                } else
                                    echo '<option value="', $item->id, '">', $item->name, '</option>';
                            }
                            ?>
                            </select>

                        </td>
                        <td>

                            <select id="deadline" name="deadline">
                                <option>All</option>
                                <option <?php if ($_POST['deadline'] == 'Only Expired') {
                                    echo 'selected="yes"';
                                }; ?>>Only Expired
                                </option>
                            </select>
                            <button class="btn btn-info" name="filter" type="submit" value="Filter"/>
                            <i class="icon-tint icon-white"></i> Filter
                            </button>

                            </form>
                        </td>

                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php

foreach ($context as $item) {
    $context = $item->id;
}

?>
<div>
    <?php
    showCurrentlySelectedCourse($CFG, $DB);
    ?>
</div>

<?php
// get the course context
$context = context_course::instance($type);
// get the enrolled students for the course
$students = get_enrolled_users($context, null, 0);

?>

<!--<form method="POST" action="process_targets.php">-->

<?php

// get all the targets set by the current user for each user on the selected course
include('get_targets.php');
// loop thorugh each student and grab their targets
foreach ($students as $student) {
    echo '<div class="well">';
    echo '<h3>Targets for: ', $student->firstname, ' ', $student->lastname . '</h3>';
    $reports = new get_targets($student->id, $_SESSION['course_code_session']);
    echo   $reports->display($targetId, $status);
    echo '</div>';

}

//        ?>


<div id="sideblock" class="alert">
<form method="POST" action="process_set.php" name="statusset" id="statusset">
    <!-- pass the course id and userid -->
    <input type="hidden" name="courseid" value=" <?php echo $type ?> "/>
    <input type="hidden" name="userid" value=" <?php echo $USER->id ?> "/>
    <input type="hidden" name="url" value=" <?php echo $url ?> "/>
    <input type="hidden" name="url2" value=" <?php echo $url2 ?> "/>
    <input type="hidden" name="status" value=""/>
    <input type="hidden" name="statusname" value=""/>
    <input type="hidden" name="users" value=""/>

            <h4>Change selected targets to:</h4>


                <div class="btn-group" data-toggle="buttons-radio" data-toggle-name="status" id="status" name="status">
                    <?php

                    foreach ($resultStates as $item) {

                        echo '<button class="btn" value="', $item->id, '">', $item->name, '</button>';
                    }
                    ?>
                </div>

    </table>


    <!--    <div class="here">here</div>-->

</form>
</div>
</div>

<script>

    //Remove all the options on the targets select dropdowns that aren't selected
    //    This is to stop the list being used
    //@FIXME this is a fudge, there must be a better way
    $(".select option[selected!='selected']").remove();


    // when a box is ticked we add it to the hideen value in the form so that we can process it
    // this is a kludge to get round the fact that we can't override the forms created by the plp classes.
    $(".checkbox").change(function () {
        var htmls = "";
        $('input[type="checkbox"]:checked').each(
            function () {
                htmls += $(this).val() + ",";
//                $('.here').html(htmls);

                $('[name=users]').val(htmls);
            }

        );
    });

      // get the status value of the button pressed and sets it as a hidden value so that the form can send it for processing.
    $('div.btn-group[data-toggle-name=*]').each(function(){
        var group   = $(this);
        var form    = group.parents('form').eq(0);
        var name    = group.attr('data-toggle-name');
        var hidden  = $('input[name="' + name + '"]', form);
        var hidden2  = $('input[name="' + name + 'name"]', form);
        $('button', group).each(function(){
            var button = $(this);
            button.live('click', function(){
                hidden.val($(this).val());
                hidden2.val($(this).text());
            });
            if(button.val() == hidden.val()) {
                button.addClass('active');
            }
        });
    });


</script>