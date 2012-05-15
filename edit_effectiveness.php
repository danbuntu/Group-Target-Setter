<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 29/06/11
 * Time: 12:30
 * edit, add and delete the effectivenss types
 */


require_once("../../config.php");
echo 'session id is ' . session_id();
unset($_SESSION['courseid']);

if (isset($_POST['update'])) {
//    echo 'test';

    // Write in the effectiveness scores
    echo 'Update the effectiveness <br/>';

    $query = 'UPDATE unit_tracker_effectiveness SET name="' . $_POST['effectiveness'] . '" WHERE id="' . $_POST['id'] . '"';
    echo $query . '<br/>';
    mysql_query($query);
}

if (isset($_POST['submit_add_new'])) {
    $query = "INSERT INTO unit_tracker_effectiveness (name) VALUES ('" . $_POST['new'] . "')";
    echo $query;
    mysql_query($query);
}

if (isset($_POST['delete'])) {
    $query = "DELETE FROM unit_tracker_effectiveness WHERE id='" . $_POST['id'] . "'";
    echo $query;
    mysql_query($query);
}


if (isset($_POST['deleteEmployer'])) {
    $query = "DELETE FROM unit_tracker_employers WHERE id='" . $_POST['employerId'] . "'";
    echo $query;
    mysql_query($query);
}


if (isset($_POST['deleteCourse'])) {
    $query = "DELETE FROM unit_tracker_courses WHERE id='" . $_POST['courseId'] . "'";
    echo $query;
    mysql_query($query);
}


if (isset($_POST['newEmployer'])) {
    $query = "INSERT INTO unit_tracker_employers (name) VALUES ('" . $_POST['newEmployer'] . "')";
    echo $query;
    mysql_query($query);
    unset($_POST['newEmployer']);
}


if (isset($_POST['updateEmployer'])) {

    $query = 'UPDATE unit_tracker_employers SET name="' . $_POST['employerName'] . '" WHERE id="' . $_POST['employer_id'] . '"';
    echo $query . '<br/>';
    mysql_query($query);
}


if (isset($_POST['updateCourse'])) {
    $query = "UPDATE unit_tracker_courses SET coursename='" . $_POST['courseName'] . "', course_code='" . $_POST['courseId'] . "' WHERE  id='" . $_POST['id'] . "' ";
    echo $query;
    mysql_query($query);
}


if (isset($_POST['submit_new_Course'])) {
    $query = "INSERT INTO unit_tracker_courses (course_code, coursename) VALUES ('" . $_POST['courseid'] . "','" . $_POST['coursename'] . "')";
    echo $query;
    mysql_query($query);
}



?>
<h1>Show the effectiveness types</h1>
<?php
    $query = "SELECT * FROM unit_tracker_effectiveness";
$result = mysql_query($query);
echo '<table>';

while ($row = mysql_fetch_assoc($result)) {
    echo '<tr><td>';
    //    echo $row['id'] . ' ' . $row['name'] . '<br/>';

    echo '</td><td>';
    echo '<form action="edit_effectiveness.php" method="POST" >';
    echo '<input type="hidden" name="id" value="' . $row['id'] . '"/>';
    echo $row['id'] . ' ' . '<input type="text" name="effectiveness" value="' . $row['name'] . '"/>';
    echo '<input type="submit" name="update" value="Update"/>';
    echo '</form>';
    echo '</td><td>';
    echo '<form action="edit_effectiveness.php" method="POST" >';
    echo '<input type="hidden" name="id" value="' . $row['id'] . '"/>';
    echo '<input type="submit" name="delete" value="Delete"/>';
    echo '</form>';
    //        echo '<input type="submit" name="submit" value="Delete"/>';
    echo '</td></tr>';
}

echo '</tr>';
echo '</table>';

echo '<form action="edit_effectiveness.php" method="POST" >';
echo '<input type="text" name="new" />';
echo '<input type="submit" name="submit_add_new" value="Add a new effectiveness type "/>';
echo '</form>';


echo '<h1>Show Employers</h1>';

$query = "select * from unit_tracker_employers";
$result = mysql_query($query);
$num_rows = mysql_num_rows($result);
?>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Logo</th>
        <th></th>
        <th></th>
    </tr>


    <form action="edit_effectiveness.php" method="POST">


<?php
    while ($row = mysql_fetch_assoc($result)) {
    ?>
    <tr>
        <td>

            <form action="edit_effectiveness.php" method="POST">
                <input type="hidden" name="employer_id" value="' . $row['id'] . '"/>
                <tr>
                    <td>
                        <?php echo $row['id']; ?>
                    </td>
                    <td>
                        <input type="text" name="employerName" value="<?php echo $row['name']; ?>"/>
                    </td>
                    <td>
                        <img src="./images/<?php echo $row['logo']; ?>.png" width="50%"/>
                    </td>
                    <td>

                        <input type="submit" name="updateEmployer" value="Update"/>
                    </td>
                    <td>

            </form>
            <form action="edit_effectiveness.php" method="POST">
                <input type="hidden" name="employerId" value="<?php echo $row['id']; ?>"/>
                <input type="submit" name="deleteEmployer" value="Delete"/>
            </form>
        </td>
    </tr>
    <?php

}
    ?>
    </tr>
</table>
<?php

echo '<form action="edit_effectiveness.php" method="POST" >';
echo '<input type="text" name="newEmployer" />';
echo '<input type="submit" name="submit_new_employer" value="Submit new employer"/>';
echo '</form>';

echo '<h1>Show Courses</h1>';

$query = "SELECT c.id, c.course_code, c.coursename, c.employerid, e.id as id2, e.name, e.logo FROM unit_tracker_courses c LEFT JOIN unit_tracker_employers e ON c.employerid=e.id";
$result = mysql_query($query);

echo '<table>';
while ($row = mysql_fetch_assoc($result)) {
echo '<tr><th>ID</th><th>Coursename</th><th>Course Code</th><th>employer Id</th><th>buttons</th></tr>';

    echo '<tr><td>';

    echo '<form action="edit_effectiveness.php" method="POST" >';
    echo $row['id'] . '</td><td><input type="text" name="courseName" value="' . $row['coursename'] . '"/></td>';

    echo '<input type="hidden" name="id" value="' . $row['id'] . '"/>';
    echo '<td><input type="text" name="courseId" value="' . $row['course_code'] . '"/></td>';
    echo '<td><input type="text" name="employerid" value="' . $row['id2'] . '"/></td>';
    echo '<td><input type="submit" name="updateCourse" value="Update"/>';
    echo '</form>';
    echo '</td><td>';
    echo '<form action="edit_effectiveness.php" method="POST" >';
    echo '<input type="hidden" name="courseId" value="' . $row['id'] . '"/>';
    echo '<input type="submit" name="deleteCourse" value="Delete"/>';
    echo '</form>';
    echo '<a href="edit_units.php?var1=', $row['id'], '">Edit the units for this course</a></td>';
    echo '</tr>';
}

echo '</table>';

echo '<form action="edit_effectiveness.php" method="POST" >';
echo '<input type="text" name="newEmployer" />';
echo 'courseid<input type="text" name="courseid" />';
echo 'Coursename<input type="text" name="coursename" />';
echo '<input type="submit" name="submit_new_Course" value="Submit new Course"/>';
echo '</form>';

?>