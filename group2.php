<?php
include('top_include.php');
?>
<div class="container">
    <?php topbar('Group Profile', $navItems); ?>
</div>

<div class="container">

    <?php
    include('course_select_dropdown2.php');
    ?>

</div>

<div>

    <?php

    $querycoursename = "SELECT id, fullname, idnumber FROM {$CFG->prefix}course WHERE id='" . $_SESSION['course_code_session'] . "'";

//echo $querycoursename;

    $resultcourse = $mysqli->query($querycoursename);

    while ($row = $resultcourse->fetch_object()) {
        $courseId = $row->id;
        $groupId = $_SESSION['course_group_session'];
        $fullname = $row->fullname;
        $code = $row->idnumber;
        $courseid = $row->id;
    }
    ?>
    <div class="noprint">
        <?php
        showCurrentlySelectedCourse($CFG, $mysqli);
        ?>
    </div>
</div>

<?php
// Get the students on the course

//echo 'course_context_session: ' .  $_SESSION['course_context_session'];

$select = "SELECT  distinct u.idnumber ";
$from = " FROM {$CFG->prefix}role_assignments a JOIN {$CFG->prefix}user u on a.userid=u.id LEFT JOIN {$CFG->prefix}groups_members gm ON gm.userid=a.userid";
$where = " WHERE contextid='" . $_SESSION['course_context_session'] . "'";
$and = " AND a.roleid='5' order by lastname";
if ($_SESSION['course_group_session'] == 'All groups') {
    $andgroup = " ";
} elseif ($_SESSION['course_group_session'] != 'All groups') {
    $andgroup = " AND gm.groupid='" . $_SESSION['course_group_session'] . "'";
}

$querystudents = $select . $from . $where . $andgroup . $and;
//echo $querystudents;
$resultstudents = $mysqli->query($querystudents);

while ($row2 = $resultstudents->fetch_object()) {
    $resultstudentsArray[] = $row2->idnumber;
}

//print_r($resultstudentsArray);
$num_students = $resultstudents->num_rows;

//echo $querystudents;
//print_r($resultstudentsArray);

$profile = $client->__soapCall("getGroupProfile", array($resultstudentsArray));

//var_dump($profile);

// get the  instance

$year = date('y');
$year2 = $year + 1;

$currentInstance = $year . "/" . $year + 1;

// Load up the details if they already exists for the course & group

if ($_SESSION['course_group_session'] == 'All Groups') {
    $and = " AND group_id='null'";
} else {
    $and = " AND group_id='" . $_SESSION['course_group_session'] . "'";
}

//echo '<br/>';
$query = "SELECT * FROM group_profiles WHERE course_id='" . $_SESSION['course_code_session'] . "' " . $and . "";
//echo $query;
$result = $mysqli->query($query);
$result = $result->fetch_array();

//print_r($result);

?>
<form name="profile" id="profile" method="POST" action="process_groups2.php">
<table id="example" class="table table-striped">
    <tr>
        <th>Moodle ID</th>
        <td><?php echo $courseId; ?></td>
        <th>Course Title</th>
        <td><?php echo $fullname; ?></td>
        <th>Group Name</th>
        <td><?php echo $groupName; ?></td>
        <th>Course Code</th>
        <td><?php echo $code; ?></td>
    </tr>
    <tr>
        <th>Site</th>
        <td><select id="site" name="site">
            <?php
            echo '<option>--Select--</option>';
            if ($result['site'] == 'Medway') {
                echo '<option selected="Yes">Medway</option>';
            } else {
                echo '<option>Medway</option>';
            }

            if ($result['site'] == 'Maidstone') {
                echo '<option selected="Yes">Maidstone</option>';
            } else {
                echo '<option>Maidstone</option>';
            }
            ?>

        </select></td>
        <th>Year</th>
        <td><?php echo $year . '/' . $year2;?></td>
        <th>Group Size</th>
        <td><?php echo $num_students; ?></td>
    </tr>
</table>

<h3>Graphs</h3>
<table>
    <tr>
        <td>

            <?php $gender = array(
            $profile['male'],
            $profile['female'],
            $profile['unknown'],
        );

            $colours = array('#FF2A00', '#FFCC00', '#000000');
            $legend = array('Male', 'Female', 'Unknown');

            makeWithThePretty($gender, $colours, $legend, 'Gender', '150');

            $age = array(
                $profile['ageUnder16'],
                $profile['age16'],
                $profile['age19'],
                $profile['age22'],
                $profile['age30'],
                $profile['age40'],
                $profile['age50'],
            );
//print_r($age);
            $colours = array('#FF6600', '#FFCC00', '#FFFF00', '#33FF66', '#33CC33', '#339900', '#FF0000');
            $legend = array('< 16', '16 > 18', '19 > 21', '22 > 29', '30 > 39', '40 > 49', '50 +');

            makeWithThePretty($age, $colours, $legend, 'Ages', '150');

            ?>
        </td>
        <td>
            <?php
            $ethnicity = array(
                $profile['ethnicity']['Bangladeshi'],
                $profile['ethnicity']['Black_African'],
                $profile['ethnicity']['Black_Carribbean'],
                $profile['ethnicity']['Black_Other'],
                $profile['ethnicity']['Chinese'],
                $profile['ethnicity']['Indian'],
                $profile['ethnicity']['Pakistani'],
                $profile['ethnicity']['White'],
                $profile['ethnicity']['Asian_other'],
                $profile['ethnicity']['Asian_or_Asian_British'],
                $profile['ethnicity']['Black_or_Black_British'],
                $profile['ethnicity']['Mixed_White_and_Asian'],
                $profile['ethnicity']['Mixed_White_and_Black_African'],
                $profile['ethnicity']['Mixed_White_and_Black_Caribbean'],
                $profile['ethnicity']['Mixed_Other_Mixed_background'],
                $profile['ethnicity']['White_British'],
                $profile['ethnicity']['White_Irish'],
                $profile['ethnicity']['White_Other_White'],
                $profile['ethnicity']['White_English_Welsh_Scottish_Northern_Irish_British'],
                $profile['ethnicity']['White_Irish'],
                $profile['ethnicity']['White_Gypsy_or_Irish_Traveller'],
                $profile['ethnicity']['White_Any_Other_White_background'],
                $profile['ethnicity']['Mixed_Multiple_Ethnic_group'],
                $profile['ethnicity']['Asian_British'],
                $profile['ethnicity']['Arab'],
                $profile['ethnicity']['Any_Other'],
                $profile['ethnicity']['Not_provided'],

            );

//print_r($ethnicity);
            $colours = array('#CD5C5C', '#FF0000', '#FF1493', '#FF6347', '#FF4500', '#FF8C00', '#FFDAB9', '#BDB76B', '#EE82EE', '#FF00FF', '#9400D3', '#4B0082', '#7B68EE', '#7CFC00', '#228B22', '#808000', '#008B8B', '#00FFFF', '#4682B4', '#0000CD', '#DEB887', '#F4A460', '#8B4513', '#800000', '#808080', '#2F4F4F', '#000000',);
            $legend = array('Bangladeshi', 'Black African', 'Black Carribbean', 'Black Other', 'Chinese', 'Indian', 'Pakistani', 'White', 'Asian other', 'Asian or Asian British', 'Black or Black British', 'Mixed White and Asian', 'Mixed White and Black African', 'Mixed White and Black Caribbean', 'Mixed Other Mixed background', 'White British', 'White Irish', 'White Other White', 'White English Welsh Scottish Northern Irish British', 'White Irish', 'White Gypsy or Irish Traveller', 'White Any Other White background', 'Mixed Multiple Ethnic group', 'Asian British', 'Arab', 'Any Other', 'Not provided', 'Unknown');

            makeWithThePretty($ethnicity, $colours, $legend, 'Ethnicity', '150');
            ?>
            <h3>Other courses students are enrolled on in Moodle</h3>
            <?php
            foreach ($profile['Active'] as $item) {
                echo $item . '<br/>';
            }
            ?>

        </td>
    </tr>
</table>
<table>
    <th colspan="3">ICT Profiler Results</th>
    </tr>
    <tr>
        <td>

            <?php
// IT profiler graphs
            $profilerICT = array(
                $profile['profiler']['D1ICTPE'],
                $profile['profiler']['D1ICTE1'],
                $profile['profiler']['D1ICTE2'],
                $profile['profiler']['D1ICTE3'],
                $profile['profiler']['D1ICTL1'],
                $profile['profiler']['D1ICTL2'],
                $profile['profiler']['D1ICTL3'],
                $profile['profiler']['D1ICTNA'],

            );

            $colours = array('#CD5C5C', '#FF0000', '#FF1493', '#FF6347', '#FF4500', '#FF8C00', '#FFDAB9', '#BDB76B');
            $legend = array('PE', 'E1', 'E2', 'E3', 'L1', 'L2', 'L3', 'Not Taken');

            makeWithThePretty($profilerICT, $colours, $legend, 'ICT Diag', '150');
            ?>
        </td>
        <td>
            <?php
            $profilerICT = array(
                $profile['profiler']['R1ICTPE'],
                $profile['profiler']['R1ICTE1'],
                $profile['profiler']['R1ICTE2'],
                $profile['profiler']['R1ICTE3'],
                $profile['profiler']['R1ICTL1'],
                $profile['profiler']['R1ICTL2'],
                $profile['profiler']['R1ICTL3'],
                $profile['profiler']['R1ICTNA'],

            );

            $colours = array('#CD5C5C', '#FF0000', '#FF1493', '#FF6347', '#FF4500', '#FF8C00', '#FFDAB9', '#BDB76B');
            $legend = array('PE', 'E1', 'E2', 'E3', 'L1', 'L2', 'L3', 'Not Taken');

            makeWithThePretty($profilerICT, $colours, $legend, 'ICT R1', '150');
            ?>
        </td>
        <td>
            <?php
            $profilerICT = array(
                $profile['profiler']['R2ICTPE'],
                $profile['profiler']['R2ICTE1'],
                $profile['profiler']['R2ICTE2'],
                $profile['profiler']['R2ICTE3'],
                $profile['profiler']['R2ICTL1'],
                $profile['profiler']['R2ICTL2'],
                $profile['profiler']['R2ICTL3'],
                $profile['profiler']['R2ICTNA'],

            );

            $colours = array('#CD5C5C', '#FF0000', '#FF1493', '#FF6347', '#FF4500', '#FF8C00', '#FFDAB9', '#BDB76B');
            $legend = array('PE', 'E1', 'E2', 'E3', 'L1', 'L2', 'L3', 'Not Taken');

            makeWithThePretty($profilerICT, $colours, $legend, 'ICT R2', '150');

            ?>
        </td>
    </tr>
</table>
<table>
    <tr>
        <th colspan="3">Numbers Profiler Results</th>
    </tr>
    <tr>
        <td>
            <?php
// Numbers graphs

            $profilerICT = array(
                $profile['profiler']['D1NumPE'],
                $profile['profiler']['D1NumE1'],
                $profile['profiler']['D1NumE2'],
                $profile['profiler']['D1NumE3'],
                $profile['profiler']['D1NumL1'],
                $profile['profiler']['D1NumL2'],
                $profile['profiler']['D1NumL3'],
                $profile['profiler']['D1NumNA'],
            );

            $colours = array('#CD5C5C', '#FF0000', '#FF1493', '#FF6347', '#FF4500', '#FF8C00', '#FFDAB9', '#BDB76B');
            $legend = array('PE', 'E1', 'E2', 'E3', 'L1', 'L2', 'L3', 'Not Taken');

            makeWithThePretty($profilerICT, $colours, $legend, 'Nums Diag', '150');
            ?>
        </td>
        <td>
            <?php
            $profilerICT = array(
                $profile['profiler']['R1NumPE'],
                $profile['profiler']['R1NumE1'],
                $profile['profiler']['R1NumE2'],
                $profile['profiler']['R1NumE3'],
                $profile['profiler']['R1NumL1'],
                $profile['profiler']['R1NumL2'],
                $profile['profiler']['R1NumL3'],
                $profile['profiler']['R1NumNA'],
            );

            $colours = array('#CD5C5C', '#FF0000', '#FF1493', '#FF6347', '#FF4500', '#FF8C00', '#FFDAB9', '#BDB76B');
            $legend = array('PE', 'E1', 'E2', 'E3', 'L1', 'L2', 'L3', 'Not Taken');

            makeWithThePretty($profilerICT, $colours, $legend, 'Nums R1', '150');
            ?>
        </td>
        <td>
            <?php
            $profilerICT = array(
                $profile['profiler']['R2NumPE'],
                $profile['profiler']['R2NumE1'],
                $profile['profiler']['R2NumE2'],
                $profile['profiler']['R2NumE3'],
                $profile['profiler']['R2NumL1'],
                $profile['profiler']['R2NumL2'],
                $profile['profiler']['R2NumL3'],
                $profile['profiler']['R2NumNA'],
            );

            $colours = array('#CD5C5C', '#FF0000', '#FF1493', '#FF6347', '#FF4500', '#FF8C00', '#FFDAB9', '#BDB76B');
            $legend = array('PE', 'E1', 'E2', 'E3', 'L1', 'L2', 'L3', 'Not Taken');

            makeWithThePretty($profilerICT, $colours, $legend, 'Nums R2', '150');
            ?>
        </td>
    </tr>
</table>
<table>
    <tr>
        <th colspan="3">Communication Profiler Results</th>
    </tr>
    <tr>
        <td>
            <?php
// Coms graphs

            $profilerICT = array(
                $profile['profiler']['D1ComPE'],
                $profile['profiler']['D1ComE1'],
                $profile['profiler']['D1ComE2'],
                $profile['profiler']['D1ComE3'],
                $profile['profiler']['D1ComL1'],
                $profile['profiler']['D1ComL2'],
                $profile['profiler']['D1ComL3'],
                $profile['profiler']['D1ComNA'],
            );

            $colours = array('#CD5C5C', '#FF0000', '#FF1493', '#FF6347', '#FF4500', '#FF8C00', '#FFDAB9', '#BDB76B');
            $legend = array('PE', 'E1', 'E2', 'E3', 'L1', 'L2', 'L3', 'Not Taken');

            makeWithThePretty($profilerICT, $colours, $legend, 'Coms Diag', '150');
            ?>
        </td>
        <td>
            <?php
            $profilerICT = array(
                $profile['profiler']['R1ComPE'],
                $profile['profiler']['R1ComE1'],
                $profile['profiler']['R1ComE2'],
                $profile['profiler']['R1ComE3'],
                $profile['profiler']['R1ComL1'],
                $profile['profiler']['R1ComL2'],
                $profile['profiler']['R1ComL3'],
                $profile['profiler']['R1ComNA'],
            );

            $colours = array('#CD5C5C', '#FF0000', '#FF1493', '#FF6347', '#FF4500', '#FF8C00', '#FFDAB9', '#BDB76B');
            $legend = array('PE', 'E1', 'E2', 'E3', 'L1', 'L2', 'L3', 'Not Taken');

            makeWithThePretty($profilerICT, $colours, $legend, 'Coms R1', '150');
            ?>
        </td>
        <td>
            <?php
            $profilerICT = array(
                $profile['profiler']['R2ComPE'],
                $profile['profiler']['R2ComE1'],
                $profile['profiler']['R2ComE2'],
                $profile['profiler']['R2ComE3'],
                $profile['profiler']['R2ComL1'],
                $profile['profiler']['R2ComL2'],
                $profile['profiler']['R2ComL3'],
                $profile['profiler']['R2ComNA'],
            );

            $colours = array('#CD5C5C', '#FF0000', '#FF1493', '#FF6347', '#FF4500', '#FF8C00', '#FFDAB9', '#BDB76B');
            $legend = array('PE', 'E1', 'E2', 'E3', 'L1', 'L2', 'L3', 'Not Taken');

            makeWithThePretty($profilerICT, $colours, $legend, 'Coms R2', '150');
            ?>
        </td>
    </tr>
</table>
<?php

?>

<h3>Background</h3>
<textarea class="xxlarge" name="background" id="background"
          style="width: 1063px; height: 165px;"><?php echo $result['background']; ?></textarea>


<h3>Preferred Learning Styles</h3>

<textarea class="xxlarge" name="learning_styles" id="learning_styles"
          style="width: 1063px; height: 165px;"><?php echo $result['preferred_learning_styles']; ?></textarea>

<!--<h3>Level of Key Skills/ Ability</h3>-->
<!--<textarea class="xxlarge" name="level_skills" id="level_skills"-->
<!--          style="width: 1063px; height: 165px;">--><?php //echo $result['level_of_key_skills']; ?><!--</textarea>-->

<h3>Differentiation</h3>
<?php


foreach ($profile['differentiation'] as $item) {
    echo $item;
};


?>

<h2>Support Needs</h2>

<h3>Particular Difficulties/ Special Needs</h3>
<textarea class="xxlarge" name="difficulties" id="difficulties"
          style="width: 1063px; height: 165px;"><?php echo $result['difficulties']; ?></textarea>

<h3>Special Circumstances</h3>
<textarea class="xxlarge" name="special_circumstances"
          id="special_circumstances"
          style="width: 1063px; height: 165px;"><?php echo $result['circumstances']; ?></textarea>

<h3>Confidence/ Group Participation</h3>
<textarea class="xxlarge" name="confidence" id="confidence"
          style="width: 1063px; height: 165px;"><?php echo $result['confidence']; ?></textarea>

<h3>How Differentiation Needs Are Being Met</h3>
<textarea class="xxlarge" name="differentiation_needs"
          id="differentiation_needs"
          style="width: 1063px; height: 165px;"><?php echo $result['differentiation']; ?></textarea>


<h2>use of Facilitators</h2>
<textarea class="xxlarge" name="facilitators"
          id="facilitators"
          style="width: 1063px; height: 165px;"><?php echo $result['facilitators']; ?></textarea>

<h2>Other Relevant Information</h2>
<textarea class="xxlarge" name="other" id="other"
          style="width: 1063px; height: 165px;"><?php echo $result['other']; ?></textarea>

<h2>Completed By</h2>
<table>
    <tr>
        <th> Tutor</th>
        <td><input type="text" id="tutor" name="tutor" value="<?php echo $result['tutor']; ?>"/></td>
        <th>Time/date</th>
        <td><input type="text" id="datepicker" name="date"
                   value="<?php echo date('d-m-Y', strtotime($result['date'])); ?>"/></td>
    </tr>
</table>
<input type="hidden" name="course_id" id="course_id" value="<?php echo $courseId; ?>">
<input type="hidden" name="group_id" id="group_id" value="<?php echo $groupId; ?>">
<input type="submit" class="btn btn-success" name="submit2" value="Submit Changes">
</div>
</form>
<?php

include('bottom_include.php');
$mysqli->close;
//mysql_close($link);

?>

<script>
    $(function () {
        $("#datepicker").datepicker(
            {dateFormat:'dd-mm-yy'}
        );
    });
</script>
