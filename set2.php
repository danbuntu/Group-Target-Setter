<?php
include('top_include.php');
?>
<div class="container">
    <?php topbar('Change Target Status', $navItems); ?>
</div>


<div class="container">

    <?php

    include ('access_context.php');

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
        $status = '0';
    }

    $query = "SELECT fullname FROM " . $CFG->prefix . 'course WHERE id="' . $type . '"';
//echo $query;
    $result = mysql_query($query);


    while ($row = mysql_fetch_assoc($result)) {
        if ($type == '1') {
            $coursename = 'on all my courses';
        } else {
            $coursename = $row['fullname'];
        }
    }
    ?>
    <div id="page">
        <div id="layout">

            <?php
            $_SESSION['course_code_session'];

            ?>

            <?php
//List all courses the user is a teacher on and allow them to filter by them

            $query = "SELECT c.id, c.fullname  FROM " . $CFG->prefix . "role_assignments ra JOIN " . $CFG->prefix . "context co ON ra.contextid=co.id JOIN " . $CFG->prefix . "course c ON co.instanceid=c.id WHERE userid='" . $USER->id . "' AND roleid='3' AND co.contextlevel='50'";

            $result = $mysqli->query($query);
            ?>

            <div class="row">
                <div class="span12">
                    <div class="row">

                        <table class="table">

                            <tr>
                                <th>Select Course:</th>
                                <th>Select Course:</th>
                            </tr>
                            <tr>
                                <td align="center">
                                    <form name="filter" method="POST" action="set2.php">
                                        <select name="course" id="select_type">
                                            <option value="1">All courses</option>

                                            <?php
                                            while ($row = $result->fetch_object()) {
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
                                    echo '<select name="status" id="select_status">';
                                    if ($status == '0') {
                                        echo '<option selected="selected" value="0">Open</option>';
                                    } else
                                        echo '<option value="0">Open</option>';

                                    if ($status == '1') {
                                        echo '<option selected="selected" value="1">Achieved</option>';
                                    } else
                                        echo '<option value="1">Achieved</option>';

                                    if ($status == '3') {
                                        echo '<option selected="selected" value="3">Withdrawn</option>';
                                    } else
                                        echo '<option value="3">Withdrawn</option>';

                                    ?>
                                    </select>
                                    <input class="btn btn-info" name="filter" type="submit" value="Filter"/>

                                    </form>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php

        if ($type == '1') {
            $query = "SELECT * from mdl_ilptarget_posts WHERE setbyuserid='" . $USER->id . "' AND status='" . $status . "'";
        } else {

            $query = "SELECT * from mdl_ilptarget_posts WHERE setbyuserid='" . $USER->id . "' AND status='" . $status . "' AND targetcourse='" . $type . "'";
        }

//  echo $query;
        $result = $mysqli->query($query);

        while ($row = $result->fetch_object()) {
            echo '<form name="targets" method="POST" action="process.php">';
            echo '<table style="width: 100%;" border="1">';
            // echo $row['setforuserid'] . '</td>';
            //get the user name form their id
            $queryname = "SELECT id, firstname, lastname FROM mdl_user WHERE id='" . $row->setforuserid . "'";
            $resultname = $mysqli->query($queryname);


            while ($row2 = $resultname->fetch_object()) {
                $id = $row->id;
                echo '<th>Id</th><td>' . $row->id . '</td><th>Student name: </th><td>' . $row2->firstname . ' ' . $row2->lastname . '</td>';
            }

            echo '<th>Deadline: </th><td>' . date("d-M-Y", $row->deadline) . '</td></tr>';
            echo '<tr><th>Target Name:</th><td colspan="5">Target Name: ' . $row->name . '</td></tr>';
            echo '<tr><th>Target: </th><td colspan="5">' . $row->targetset . '</td></tr>';
            echo '<tr><td colspan="6" style="text-align: right;" colspan="3">';
            echo 'Select* <input type="checkbox" name="mark[]" value="' . $id . '" />';
            echo '</td></tr></table>';
            echo '</br>';
        }
        ?>
        <!-- pass the course id and userid -->
        <input type="hidden" name="courseid" value=" <?php echo $type ?> "/>
        <input type="hidden" name="userid" value=" <?php echo $USER->id ?> "/>
        <input type="hidden" name="url" value=" <?php echo $url ?> "/>
        <input type="hidden" name="url2" value=" <?php echo $url2 ?> "/>
        <input type="hidden" name="status" value=" <?php echo $status ?> "/>

        <table style="width: 100%; text-align: right;">
            <tr>
                <th>Select type to change target to:</th>
                <td style="width: 400px;">

                    <div id="radio">
                        <input type="radio" id="radio1" name="mark_as" value="0"/><label for="radio1">Open</label>
                        <input type="radio" id="radio2" name="mark_as" value="1"/><label for="radio2">Achieved</label>
                        <input type="radio" id="radio3" name="mark_as" value="3"/><label for="radio3">Withdrawn</label>
                    </div>
                </td>
                <td>
                    <input class="btn btn-success" id="save" type="submit" value="Save Changes"/>
                </td>
            </tr>
        </table>

        <div class="demo">

        </div>
        </form>
    </div>
</div>
</div>
</div>

<?php
include('jscripts2.php');
?>

<!--Script to run the submit button graphic - doesn't work when added tot he main js script files-->
<script type="text/javascript">
    $(document).ready(function () {
        $('#save').hover(function () {
            $(this).addClass('mhover')
        }, function () {
            $(this).removeClass('mhover');
        });
    })

    $(function () {
        $("#radio").buttonset();
    });

    $(function () {
        $("#tabs").tabs();
    });
</script>