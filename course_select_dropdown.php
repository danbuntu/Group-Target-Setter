<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 14/07/11
 * Time: 16:50
 * To change this template use File | Settings | File Templates.
 */

//process the dropdown form


//echo 'info in select-dropdown<br/>';

//echo 'courseid: ' . $_SESSION['course_code_session'] . '<br/>';
//echo 'context id: ' . $_SESSION['course_context_session'] . '<br/>';
////echo 'type is: ' . $_SESSION['type'] . '<br/>';
//echo 'group is Id: ' . $_SESSION['course_group_session'] . '<br/>';

//print_r($_SESSION);


// code to run the drop downs
//List all courses the user is a teacher on and allow them to filter by them

$query = "SELECT c.id, c.fullname, ra.contextid  FROM " . $CFG->prefix . "role_assignments ra JOIN " . $CFG->prefix . "context co ON ra.contextid=co.id JOIN " . $CFG->prefix . "course c ON co.instanceid=c.id WHERE userid='" . $USER->id . "' AND roleid='3' AND co.contextlevel='50'";
//echo $query;
$result = mysql_query($query);
?>
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

                    while ($row = mysql_fetch_assoc($result)) {

                        //        echo '<br/>groups type ' . $_SESSION['course_code_session'] . ' row id ' . $row["id"];

                        //set the list to the current course
                        if ($_SESSION['course_code_session'] == $row["id"]) {
                            $selected = 'selected="selected"';
                            //        echo 'row id is: ' . $row["id"];
                        } elseif ($_SESSION['course_code_session'] != $row["id"]) {
                            $selected = ' ';
                            //        echo 'row id is: ' . $row["id"];
                        }
                        echo '<option ' . $selected . ' value="' . $row["id"] . '">' . $row["fullname"] . '-' . $row["id"] . '</option>';

                    }
                    ?>
                </select>
                <input type="submit" name="filter" value="filter"/>
            </form>
        </td>
    <tr>
        <th>Filter by Group:</th>
    </tr>
    <tr>
        <td align="center">

<?php
//filter by groups
    $query = "SELECT * FROM {$CFG->prefix}groups WHERE courseid='" . $_SESSION['course_code_session'] . "'";
//echo $query;
            $result = mysql_query($query);
            $num_rows = mysql_num_rows($result);

            if ($num_rows == 0) {
                echo 'No groups found on this course';
            } else {

                echo '<form name="filter" action="' . $PHP_SELF . '" method="POST">';
                echo '<b>Filter by group </b>';
                echo '<select name="groups" id="groups">';
                echo '<option value="All groups">All groups</option>';
                while ($row = mysql_fetch_assoc($result)) {

                    //set the list to the current course
                    if ($_SESSION['course_group_session'] == $row["id"]) {
                        $selected = 'selected="selected"';
                        //    echo 'row id is: ' . $row["id"];
                    } elseif ($_SESSION['course_group_session'] != $row["id"]) {
                        $selected = ' ';
                        //   echo 'row id is: ' . $row["id"];
                    }


                    echo '<option ' . $selected . ' value="' . $row['id'] . '">' . $row['name'] . '</option>';
                }
                echo '</select>';
                echo '<input type="hidden"  name="courseid" value="' . $_SESSION['course_code_session'] . '"/>';
                echo '<input type="submit" name="filter" value="filter" />';
                echo '</form>';

                // get the group name
                $queryGroupName = "SELECT * FROM mdl_groups WHERE id='" . $_SESSION['course_group_session'] . "'";
                //echo $queryGroupName;
                $resultGroups = mysql_query($queryGroupName);
                $nums = mysql_num_rows($queryGroupName);

                while ($row = mysql_fetch_assoc($resultGroups)) {
                    echo 'Currently filtered by: ', $row['name'];
                }

                //        echo 'Currently filtered by: ' . $filteredGroup;
            }
            ?>

        </td>
    </tr>
</table>
<br/>


