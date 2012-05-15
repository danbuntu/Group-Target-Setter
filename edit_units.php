<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 30/06/11
 * Time: 13:36
 * Page to admin the units per course
 */

require_once("../../config.php");
include('jquery_imports.php');

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
    $query = "DELETE FROM unit_tracker_units WHERE id='" . $_POST['id'] . "'";
//    echo $query;
    mysql_query($query);
}

if (isset($_POST['updateUnit'])) {
    $query = "UPDATE unit_tracker_units set markid='" . $_POST['marking'] . "', name='" . $_POST['unitName'] . "', description='" . $_POST['unitDescription'] . "' WHERE id='" . $_POST['unitId'] . "'";
//    echo $query;
    mysql_query($query);
}

if (isset($_POST['add_new_unit'])) {
    $query = "INSERT INTO unit_tracker_units (name, description, courseid, markid) VALUES ('" . $_POST['unitName'] . "','" . $_POST['unitDescription'] . "','" . $_SESSION['courseid'] . "','" . $_POST['marking'] . "')";
//    echo $query;
    mysql_query($query);
}


// Get the course title
$query = "SELECT * FROM unit_tracker_courses WHERE id='" . $_SESSION['courseid'] . "'";
$result = mysql_query($query);

while ($row = mysql_fetch_assoc($result)) {
    $courseName = $row['coursename'];
}
?>

<div id="page">
<div id="layout">
<div class="demo">

 <h1>All units for Course <?php echo $courseName ?> </h1>
    <a href="tracker.php?courseid=<?php echo $_SESSION['course_code_session'] . '&var1=' . $_SESSION['course_context_session']; ?>"><img src="./images/back-icon.png" width="30px" border="0">Back to the main screen</a>

    <?php


$query = "SELECT unit_tracker_units.id, name, courseid, description, markid, unit_tracker_marks.id as markid2, type
FROM unit_tracker_units JOIN unit_tracker_marks ON unit_tracker_units.markid=unit_tracker_marks.id
WHERE courseid='" . $_SESSION['courseid'] . "'";
//echo $query;
$result = mysql_query($query);
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
while ($row = mysql_fetch_assoc($result)) {
    ?>

    <tr>
        <td>
            <?php echo $row['id']; ?>
            <form action="edit_units.php" method="POST">
                <input type="hidden" name="unitId" value="<?php echo $row['id']; ?>"/>
        </td>
        <td>
            <input type="text" name="unitName" value="<?php echo $row['name']; ?>"/>
        </td>
        <td>

            <textarea name="unitDescription"><?php echo $row['description']; ?></textarea>
        </td>
        <td>
           
               <?php
            echo ' markid ' . $row['markid'];
            $queryMark = "SELECT * FROM unit_tracker_marks";
                    $resultMark = mysql_query($queryMark);

                    echo '<select name="marking"/>';
                        while ($rowMark = mysql_fetch_assoc($resultMark)) {

                            if ($rowMark['id'] == $row['markid']) {
                                $selected = 'selected="selected" ';
                            } else {
                                $selected = ' ';
                            }
                echo '<option ' . $selected . 'value="' . $rowMark['id'] . '" >' . $rowMark['type'] . '</option>';
                }
                    echo '</select>';
                ?>
         

        </td>
        <td>
            <input type="submit" name="updateUnit" value="Update"/>
            </form>
        </td>
        <td>
            <form action="edit_units.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>"/>
                <input type="submit" name="deleteUnit" value="Delete"/>
            </form>
        </td>
        <td>
              <form action="edit_unit_details.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>"/>
                <input type="submit" name="editUnit" value="Edit unit criteria"/>
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
            <form action="edit_units.php" method="POST">
        <td>
            <input type="text" name="unitName"/>
        </td>
        <td>
            <textarea name="unitDescription"></textarea>
        </td>
                <td>
                    <?php
// Get possbile marking scehemes
                    $query = "SELECT * FROM unit_tracker_marks";
                    $result = mysql_query($query);
                    echo '<select name="marking">';
                        while ($row = mysql_fetch_assoc($result)) {
                       echo '<option value="' , $row['id'] , '">' , $row['type'] , '</option>';
                    }
                    echo '</select>';
                    ?>
                </td>
        <td>
            <input type="submit" name="add_new_unit" value="Add new unit "/>
            </form>
        </td>
    </tr>
</table>

</div>
</div>
</div>