<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../../config.php");
include ('access_context.php');
global $USER, $CFG, $COURSE;

$courseid = $_GET['courseid'];
$contextid = $_GET['var1'];
$status = $_GET['status'];
$userid = $USER->id;

//echo 'userid is ' . $userid;
//echo 'status is ' . $status;
$url2 = $CFG->wwwroot . '/blocks/group_targets/set.php?courseid=' . $courseid . '&var1=' . $userid;

$userid = $USER->id;
//echo '<br> Userid is ' . $userid;

include('jquery_imports.php');

$type = '1';
if (isset($_POST)) {
    $type = htmlentities($_POST['course']);
}

if ($type == null) {
    $type = $courseid;
}

if (isset($_POST)) {
    $status = htmlentities($_POST['status']);
} elseif (!isset($_POST)) {
    $status = '0';
}
//echo 'status is ' . $status;
//echo 'type is: ' . $type;
//Get selected course name
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
$courseid = $_GET['courseid'];
    $contextid = $_GET['var1'];

    include('navigation.php');
    ?>

    <table width="100%">
        <tr>
            <td>
            <td rowspan="2" style="text-align: right;">
                <img src="images/User-Group-icon.png" width="156" height="156" alt="User-Group-icon"/>
            </td>
        <tr>
            <td>
                <h1>Update Multiple PLPS: </h1> <br/>

                <h3>Filter by course and target status and select the targets you wish to change</h3>
                Please note that this only shows targets set by yourself
            </td>
        </tr>
    </table>

<?php
//List all courses the user is a teacher on and allow them to filter by them

    $query = "SELECT c.id, c.fullname  FROM " . $CFG->prefix . "role_assignments ra JOIN " . $CFG->prefix . "context co ON ra.contextid=co.id JOIN " . $CFG->prefix . "course c ON co.instanceid=c.id WHERE userid='" . $userid . "' AND roleid='3' AND co.contextlevel='50'";

    $result = mysql_query($query);

    echo '<label for="select_course"><b>Select Course</b></label>';
    echo '<form method="POST" action="set.php?courseid=' . $courseid . '&var1=' . $userid . '">';
    echo '<select name="course" id="select_type">';
    echo '<option value="1">All courses</option>';

    while ($row = mysql_fetch_assoc($result)) {
        //set the list to the current course
        if ($type == $row['id']) {
            $selected = 'selected="selected"';
        } elseif ($type != $row['id']) {
            $selected = ' ';
        }
        echo '<option ' . $selected . ' value="' . $row['id'] . '">' . $row['fullname'] . '</option>';
    }

    echo '</select>';
//echo '<h2>status for list is: ' . $status . '<h2>';
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

        echo '<input id="filter" type="submit" value="" />';

        echo '</form>';

//echo 'Currently filtering by ' . $type;

//echo 'status is; ' . $status;

        if ($type == '1') {
            $query = "SELECT * from mdl_ilptarget_posts WHERE setbyuserid='" . $userid . "' AND status='" . $status . "'";
        } else {

            $query = "SELECT * from mdl_ilptarget_posts WHERE setbyuserid='" . $userid . "' AND status='" . $status . "' AND course='" . $type . "'";
        }

//  echo $query;
        $result = mysql_query($query);

        while ($row = mysql_fetch_assoc($result)) {
            echo '<form name="targets" method="POST" action="process.php">';
            echo '<table style="width: 100%;" border="1">';
            // echo $row['setforuserid'] . '</td>';
            //get the user name form their id
            $queryname = "SELECT * FROM mdl_user WHERE id='" . $row['setforuserid'] . "'";
            $resultname = mysql_query($queryname);


            while ($row2 = mysql_fetch_assoc($resultname)) {
                $id = $row['id'];
                echo '<th>Id</th><td>' . $row['id'] . '</td><th>Student name: </th><td>' . $row2['firstname'] . ' ' . $row2['lastname'] . '</td>';
            }

            echo '<th>Deadline: </th><td>' . date("d-M-Y", $row['deadline']) . '</td></tr>';
            echo '<tr><th>Target Name:</th><td colspan="5">Target Name: ' . $row['name'] . '</td></tr>';
            echo '<tr><th>Target: </th><td colspan="5">' . $row['targetset'] . '</td></tr>';
            echo '<tr><td colspan="6" style="text-align: right;" colspan="3">';
            echo 'Select* <input type="checkbox" name="mark[]" value="' . $id . '" />';
            echo '</td></tr></table>';
            echo '</br>';
        }
        ?>
        <!-- pass the course id and userid -->
        <input type="hidden" name="courseid" value=" <?php echo $type ?> "/>
        <input type="hidden" name="userid" value=" <?php echo $userid ?> "/>
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
                <td style="width: 40px;">
                    <input id="save" type="submit" value=""/>
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
    $(document).ready(function() {
        $('#save').hover(function() {
            $(this).addClass('mhover')
        }, function() {
            $(this).removeClass('mhover');
        });
    })

    $(function() {
        $("#radio").buttonset();
    });

    $(function() {
        $( "#tabs" ).tabs();
    });
</script>
