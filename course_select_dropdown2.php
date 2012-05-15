<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 14/07/11
 * Time: 16:50
 * Populates the drop down lists used for filtering
 */



if (isset($_POST['filter'])) {
    //    echo 'course field: ' .  htmlentities($_POST['course']);
    $_SESSION['course_code_session'] = htmlentities($_POST['course']);
    //    echo ' course_code_session: ' . $_SESSION['course_code_session'];
    $_SESSION['course_context_session'] = getCourseContextID(htmlentities($_POST['course']), $mysqli);
}

if (isset($_POST['filtergroups'])) {
    $_SESSION['course_code_session'] = htmlentities($_POST['group']);
    $_SESSION['course_group_session'] = htmlentities($_POST['group']);
    $_SESSION['course_context_session'] = htmlentities($_POST['context']);
}

$group_id = 'All groups';
$_SESSION['course_group_session'] = 'All groups';
//catch and check the filter

if (!empty($_GET['group'])) {
    $_SESSION['course_group_session'] = $_GET['group'];
}
if (isset($_POST['groups'])) {
    $_SESSION['course_group_session'] = htmlentities($_POST['groups']);
    $_SESSION['course_code_session'] = htmlentities($_POST['courseid']);
    $_SESSION['course_context_session'] = getCourseContextID($_SESSION['course_code_session'], $mysqli);
    //get the group id
    if ($_SESSION['course_group_session'] != 'All groups') {
        $query_group_id = "SELECT id FROM {$CFG->prefix}groups WHERE id='" . $_SESSION['course_group_session'] . "' AND courseid='" . $_SESSION['course_code_session'] . "'";
        //        echo $query_group_id;
        $result_group_id = $mysqli->query($query_group_id)  or die($mysqli->error());
        while ($row = $result_group_id->fetch_object()) {
            $_SESSION['course_group_session'] = $row->id;
            $num_rows = $result_group_id->num_rows;
        }
    } else {
        $_SESSION['course_group_session'] = 'All groups';
    }
}

//print_r($_SESSION);

//process the dropdown form

$query = "SELECT c.id, c.fullname, ra.contextid  FROM " . $CFG->prefix . "role_assignments ra JOIN " . $CFG->prefix . "context co ON ra.contextid=co.id JOIN " . $CFG->prefix . "course c ON co.instanceid=c.id WHERE userid='" . $USER->id . "' AND roleid='3' AND co.contextlevel='50'";
//echo $query;
$result = $mysqli->query($query);

?>

<div class="row">
    <div class="span18">
        <div class="row">
            <div class="span6">
                <div class="noprint">
                    <table class="table">
                        <tr>
                            <th>Filter by Course:</th>
                        </tr>
                        <tr>
                            <td>

                                <form name="process" action="<?PHP echo $_SERVER['PHP_SELF'] ?>" method="POST">
                                    <select name="course" id="select_type">
                                        <option value="1">--Select a Course--</option>

                                        <?php
                                        while ($row = $result->fetch_object()) {
                                            //set the list to the current course
                                            if ($_SESSION['course_code_session'] == $row->id) {
                                                $selected = 'selected="selected"';
                                            } elseif ($_SESSION['course_code_session'] != $row->id) {
                                                $selected = ' ';
                                            }
                                            echo '<option ' . $selected . ' value="' . $row->id . '">' . preg_replace("/[^a-zA-Z0-9\s]/", "", $row->fullname) . '-' . $row->id . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <input class="btn btn-info" type="submit" name="filter" value="filter"/>
                                </form>
                            </td>

                        <tr>
                    </table>
                </div>
            </div>

            <div class="span6">
                <div class="noprint">
                    <table class="table">
                        <tr>
                            <th>Filter by Group:</th>
                        </tr>
                        <tr>
                            <td align="center">

                                <?php
//filter by groups
                                $query = "SELECT id, name FROM {$CFG->prefix}groups WHERE courseid='" . $_SESSION['course_code_session'] . "'";
//                            echo $query;
                                $result = $mysqli->query($query);
                                $num_rows = $result->num_rows;

                                if ($num_rows == 0) {
                                    echo 'No groups found on this course';
                                } else {

                                    echo '<form name="filter" action="' . $_SERVER['PHP_SELF'] . '" method="POST">';
                                    echo '<select name="groups" id="groups">';
                                    echo '<option value="All groups">All groups</option>';
                                    while ($row = $result->fetch_object()) {

                                        //set the list to the current course
                                        if ($_SESSION['course_group_session'] == $row->id) {
                                            $selected = 'selected="selected"';
                                            //    echo 'row id is: ' . $row["id"];
                                        } elseif ($_SESSION['course_group_session'] != $row->id) {
                                            $selected = ' ';
                                            //   echo 'row id is: ' . $row["id"];
                                        }

                                        echo '<option ' . $selected . ' value="' . $row->id . '">' . $row->name . ' - ' . $row->id . '</option>';
                                    }
                                    echo '</select>';
                                    echo '<input type="hidden"  name="courseid" value="' . $_SESSION['course_code_session'] . '"/>';
                                    echo '<input class="btn btn-info" type="submit" name="filter" value="filter" />';
                                    echo '</form>';

                                    // get the group name
                                    $queryGroupName = "SELECT id, name FROM mdl_groups WHERE id='" . $_SESSION['course_group_session'] . "'";
                                    //echo $queryGroupName;
                                    $resultGroups = $mysqli->query($queryGroupName);
                                    $nums = $resultGroups->num_rows;

                                    while ($row = $resultGroups->fetch_object()) {
                                        echo '<a class="label notice">Currently filtered by: ', $row->name, '</a>';
                                        echo '<br/>';
                                        $_SESSION['course_group_session'] = $row->id;
                                        $groupName = $row->name;

                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>


