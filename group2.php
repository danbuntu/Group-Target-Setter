<?php
include('top_include.php');
include('pie_chart_function.php');
global $CFG, $COURSE, $USER, $DB;
?>
<div class="container">
    <?php topbar('Group Profile', $navItems); ?>
</div>

<div class="container">

    <?php
    if ($type == null) {
        $type = $_SESSION['course_code_session'];
    }

    ?>
    <?php
    include('course_select_dropdown2.php');
    ?>

</div>

<div>

    <?php

    $resultcourse = $DB->get_records('course', array('id' => $_SESSION['course_code_session']));

    foreach ($resultcourse as $row) {
        $courseId = $row->id;
        $groupId = $_SESSION['course_group_session'];
        $fullname = $row->fullname;
        $code = $row->idnumber;
        $courseid = $row->id;
    }
    ?>
    <div class="noprint">
        <?php
        showCurrentlySelectedCourse($CFG, $DB);
        ?>
    </div>
</div>

<?php
//// get the course context
//$context = context_course::instance($type);
//// get the enrolled students for the course
//$students = get_enrolled_users($context, null, 0);

//echo 'context session is: ' . $_SESSION['course_context_session'];

$domain = $_SERVER['HTTP_HOST'];
//select and show all students on this course used roleid 5 to indentify studentsserver
// knocks out students with no idnumber in Moodle as this stops the look up against NG
$select = "SELECT  u.idnumber";
$from = " FROM {$CFG->prefix}role_assignments a JOIN {$CFG->prefix}user u on a.userid=u.id LEFT JOIN {$CFG->prefix}groups_members gm ON gm.userid=a.userid";
$where = " WHERE contextid='" . $_SESSION['course_context_session'] . "'";
$and = " AND  idnumber !='' AND a.roleid='5' order by lastname";
if ($_SESSION['course_group_session'] == 'All groups') {
    $andgroup = " ";
} elseif ($_SESSION['course_group_session'] != 'All groups') {
    $andgroup = " AND gm.groupid='" . $_SESSION['course_group_session'] . "'";
}

$querystudents = $select . $from . $where . $andgroup . $and;
//echo $querystudents;

$students = $DB->get_records_sql($querystudents);

//  print_r($students);

$num_students = count($students) + 1;

// re-work the students array to get just the idnumbers
$studentArray = array();
foreach ($students as $student) {
    $studentArray[] = $student->idnumber;
}


$profile = $client->__soapCall("getGroupProfile", array($studentArray));

// get the  instance

$year = date('y');
$year2 = $year + 1;

$currentInstance = $year . "/" . $year + 1;

// Load up the details if they already exists for the course & group

if ($_SESSION['course_group_session'] == 'All groups') {
    $and = " AND group_id is NULL";
} else {
    $and = " AND group_id='" . $_SESSION['course_group_session'] . "'";
}

//echo '<br/>';
$query = "SELECT * FROM {$CFG->prefix}group_profiles WHERE course_id='" . $_SESSION['course_code_session'] . "' " . $and . "";
//echo $query;
$result = $DB->get_record_sql($query);
//$result = $result->fetch_array();


?>
<div class="well">
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
                    if ($result->site == 'Medway') {
                        echo '<option selected="Yes">Medway</option>';
                    } else {
                        echo '<option>Medway</option>';
                    }

                    if ($result->site == 'Maidstone') {
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
</div>

<div class="well">
<h3>Graphs</h3>
Please note that all graph figures are academic year based. If the figures don't add up you've probably got students on
your course who are no longer enrolled in the college.

<table style="width: 100%;">
    <tr>
        <td>

            <?php $gender = array(
            '0' => '0',
            'Male  (' . $profile['male'] . ')' => $profile['male'],
            'Female (' . $profile['female'] . ')' => $profile['female'],
            'Unknown (' . $profile['unknown'] . ')' => $profile['unknown'],
        );
            $colours = "'#FF2A00', '#FFCC00', '#000000'";

            pieChart($gender, 'Gender', $colours);

            $age = array(
                '0' => '0',
                '< 16 (' . $profile['ageUnder16'] . ')' => $profile['ageUnder16'],
                '16 > 18 (' . $profile['age16'] . ')' => $profile['age16'],
                '19 > 21 (' . $profile['age19'] . ')' => $profile['age19'],
                '22 > 29 (' . $profile['age22'] . ')' => $profile['age22'],
                '30 > 39 (' . $profile['age30'] . ')' => $profile['age30'],
                '40 > 49 (' . $profile['age40'] . ')' => $profile['age40'],
                '50 + (' . $profile['age50'] . ')' => $profile['age50'],
            );
            $colours = "'#FF6600', '#FFCC00', '#FFFF00', '#33FF66', '#33CC33', '#339900', '#FF0000'";
            pieChart($age, 'Ages', $colours);

            ?>

            <?php
            $ethnicity = array(
                '0' => '0',
                'Bangladeshi (' . $profile['ethnicity']['Bangladeshi'] . ')' => $profile['ethnicity']['Bangladeshi'],
                'Black African (' . $profile['ethnicity']['Black_African'] . ')' => $profile['ethnicity']['Black_African'],
                'Black Carribbean (' . $profile['ethnicity']['Black_Carribbean'] . ')' => $profile['ethnicity']['Black_Carribbean'],
                'Black Other (' . $profile['ethnicity']['Black_Other'] . ')' => $profile['ethnicity']['Black_Other'],
                'Chinese (' . $profile['ethnicity']['Chinese'] . ')' => $profile['ethnicity']['Chinese'],
                'Indian (' . $profile['ethnicity']['Indian'] . ')' => $profile['ethnicity']['Indian'],
                'Pakistani (' . $profile['ethnicity']['Pakistani'] . ')' => $profile['ethnicity']['Pakistani'],
                'White (' . $profile['ethnicity']['White'] . ')' => $profile['ethnicity']['White'],
                'Asian other (' . $profile['ethnicity']['Asian_other'] . ')' => $profile['ethnicity']['Asian_other'],
                'Asian or Asian British (' . $profile['ethnicity']['Asian_or_Asian_British'] . ')' => $profile['ethnicity']['Asian_or_Asian_British'],
                'Black or Black British (' . $profile['ethnicity']['Black_or_Black_British'] . ')' => $profile['ethnicity']['Black_or_Black_British'],
                'Mixed White and Asian (' . $profile['ethnicity']['Mixed_White_and_Asian'] . ')' => $profile['ethnicity']['Mixed_White_and_Asian'],
                'Mixed White and Black African (' . $profile['ethnicity']['Mixed_White_and_Black_African'] . ')' => $profile['ethnicity']['Mixed_White_and_Black_African'],
                'Mixed White and Black Caribbean (' . $profile['ethnicity']['Mixed_White_and_Black_Caribbean'] . ')' => $profile['ethnicity']['Mixed_White_and_Black_Caribbean'],
                'Mixed_Other_Mixed_background (' . $profile['ethnicity']['Mixed_Other_Mixed_background'] . ')' => $profile['ethnicity']['Mixed_Other_Mixed_background'],
                'White British (' . $profile['ethnicity']['White_British'] . ')' => $profile['ethnicity']['White_British'],
                'White Irish (' . $profile['ethnicity']['White_Irish'] . ')' => $profile['ethnicity']['White_Irish'],
                'White Other White (' . $profile['ethnicity']['White_Other_White'] . ')' => $profile['ethnicity']['White_Other_White'],
                'White English Welsh Scottish Northern Irish British (' . $profile['ethnicity']['White_English_Welsh_Scottish_Northern_Irish_British'] . ')' => $profile['ethnicity']['White_English_Welsh_Scottish_Northern_Irish_British'],
                'White Irish (' . $profile['ethnicity']['White_Irish'] . ')' => $profile['ethnicity']['White_Irish'],
                'White Gypsy or Irish Traveller (' . $profile['ethnicity']['White_Gypsy_or_Irish_Traveller'] . ')' => $profile['ethnicity']['White_Gypsy_or_Irish_Traveller'],
                'White Any Other White background (' . $profile['ethnicity']['White_Any_Other_White_background'] . ')' => $profile['ethnicity']['White_Any_Other_White_background'],
                'Mixed Multiple Ethnic group (' . $profile['ethnicity']['Mixed_Multiple_Ethnic_group'] . ')' => $profile['ethnicity']['Mixed_Multiple_Ethnic_group'],
                'Asian British (' . $profile['ethnicity']['Asian_British'] . ')' => $profile['ethnicity']['Asian_British'],
                'Arab (' . $profile['ethnicity']['Arab'] . ')' => $profile['ethnicity']['Arab'],
                'Any Other  (' . $profile['ethnicity']['Any_Other'] . ')' => $profile['ethnicity']['Any_Other'],
                'Not provided (' . $profile['ethnicity']['Not_provided'] . ')' => $profile['ethnicity']['Not_provided'],
            );

//print_r($ethnicity);
            $colours = "'#CD5C5C', '#FF0000', '#FF1493', '#FF6347', '#FF4500', '#FF8C00', '#FFDAB9', '#BDB76B', '#EE82EE', '#FF00FF', '#9400D3', '#4B0082', '#7B68EE', '#7CFC00', '#228B22', '#808000', '#008B8B', '#00FFFF', '#4682B4', '#0000CD', '#DEB887', '#F4A460', '#8B4513', '#800000', '#808080', '#2F4F4F', '#000000'";
            // $legend = array('Bangladeshi', 'Black African', 'Black Carribbean', 'Black Other', 'Chinese', 'Indian', 'Pakistani', 'White', 'Asian other', 'Asian or Asian British', 'Black or Black British', 'Mixed White and Asian', 'Mixed White and Black African', 'Mixed White and Black Caribbean', 'Mixed Other Mixed background', 'White British', 'White Irish', 'White Other White', 'White English Welsh Scottish Northern Irish British', 'White Irish', 'White Gypsy or Irish Traveller', 'White Any Other White background', 'Mixed Multiple Ethnic group', 'Asian British', 'Arab', 'Any Other', 'Not provided', 'Unknown');

            pieChart($ethnicity, 'Ethnicity', $colours);
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <h3>Other courses students are enrolled on in Moodle</h3>
            <?php

//           var_dump($profile);

            foreach ($profile['Active'] as $item) {
                echo $item . '<br/>';
            }
            ?>

        </td>
    </tr>
</table>
<table style="width: 100%;">
    <th colspan="3"><h3>ICT Profiler Results</h3></th>
    </tr>
    <tr>
        <td>

            <?php

            $colours = "'#CD5C5C', '#FF0000', '#FF1493', '#FF6347', '#FF4500', '#FF8C00', '#FFDAB9', '#BDB76B'";
// IT profiler graphs
            $profilerICT = array(
                '0' => '0',
                'PE  (' . $profile['profiler']['D1ICTPE'] . ')' => $profile['profiler']['D1ICTPE'],
                'E1 (' . $profile['profiler']['D1ICTE1'] . ')' => $profile['profiler']['D1ICTE1'],
                'E2 (' . $profile['profiler']['D1ICTE2'] . ')' => $profile['profiler']['D1ICTE2'],
                'E3 (' . $profile['profiler']['D1ICTE3'] . ')' => $profile['profiler']['D1ICTE3'],
                'L1 (' . $profile['profiler']['D1ICTL1'] . ')' => $profile['profiler']['D1ICTL1'],
                'L2 (' . $profile['profiler']['D1ICTL2'] . ')' => $profile['profiler']['D1ICTL2'],
                'L3 (' . $profile['profiler']['D1ICTL3'] . ')' => $profile['profiler']['D1ICTL3'],
                'Not Taken  (' . $profile['profiler']['D1ICTNA'] . ')' => $profile['profiler']['D1ICTNA'],

            );

            pieChart($profilerICT, 'ICT Diag', $colours);
            ?>
        </td>
        <td>
            <?php
            $profilerICT = array(
                '0' => '0',
                'PE  (' . $profile['profiler']['R1ICTPE'] . ')' => $profile['profiler']['R1ICTPE'],
                'E1 (' . $profile['profiler']['R1ICTE1'] . ')' => $profile['profiler']['R1ICTE1'],
                'E2 (' . $profile['profiler']['R1ICTE2'] . ')' => $profile['profiler']['R1ICTE2'],
                'E3 (' . $profile['profiler']['R1ICTE3'] . ')' => $profile['profiler']['R1ICTE3'],
                'L1 (' . $profile['profiler']['R1ICTL1'] . ')' => $profile['profiler']['R1ICTL1'],
                'L2 (' . $profile['profiler']['R1ICTL2'] . ')' => $profile['profiler']['R1ICTL2'],
                'L3 (' . $profile['profiler']['R1ICTL3'] . ')' => $profile['profiler']['R1ICTL3'],
                'Not Taken  (' . $profile['profiler']['R1ICTNA'] . ')' => $profile['profiler']['R1ICTNA'],
            );

            pieChart($profilerICT, 'ICT R1', $colours);
            ?>
        </td>
        <td>
            <?php
            $profilerICT = array(
                '0' => '0',
                'PE  (' . $profile['profiler']['R2ICTPE'] . ')' => $profile['profiler']['R2ICTPE'],
                'E1 (' . $profile['profiler']['R2ICTE1'] . ')' => $profile['profiler']['R2ICTE1'],
                'E2 (' . $profile['profiler']['R2ICTE2'] . ')' => $profile['profiler']['R2ICTE2'],
                'E3 (' . $profile['profiler']['R2ICTE3'] . ')' => $profile['profiler']['R2ICTE3'],
                'L1 (' . $profile['profiler']['R2ICTL1'] . ')' => $profile['profiler']['R2ICTL1'],
                'L2 (' . $profile['profiler']['R2ICTL2'] . ')' => $profile['profiler']['R2ICTL2'],
                'L3 (' . $profile['profiler']['R2ICTL3'] . ')' => $profile['profiler']['R2ICTL3'],
                'Not Taken  (' . $profile['profiler']['R2ICTNA'] . ')' => $profile['profiler']['R2ICTNA'],
            );

            pieChart($profilerICT, 'ICT R2', $colours);

            ?>
        </td>
    </tr>
</table>
<table style="width: 100%;">
    <tr>
        <th colspan="3"><h3>Numbers Profiler Results</h3></th>
    </tr>
    <tr>
        <td>
            <?php
// Numbers graphs

            $profilerICT = array(
                '0' => '0',
                'PE (' . $profile['profiler']['D1NumPE'] . ')' => $profile['profiler']['D1NumPE'],
                'E1 (' . $profile['profiler']['D1NumE1'] . ')' => $profile['profiler']['D1NumE1'],
                'E2 (' . $profile['profiler']['D1NumE2'] . ')' => $profile['profiler']['D1NumE2'],
                'E3 (' . $profile['profiler']['D1NumE3'] . ')' => $profile['profiler']['D1NumE3'],
                'L1 (' . $profile['profiler']['D1NumL1'] . ')' => $profile['profiler']['D1NumL1'],
                'L2 (' . $profile['profiler']['D1NumL2'] . ')' => $profile['profiler']['D1NumL2'],
                'L3 (' . $profile['profiler']['D1NumL3'] . ')' => $profile['profiler']['D1NumL3'],
                'Not Taken (' . $profile['profiler']['D1NumNA'] . ')' => $profile['profiler']['D1NumNA'],
            );

            pieChart($profilerICT, 'Nums Diag', $colours);
            ?>
        </td>
        <td>
            <?php
            $profilerICT = array(
                '0' => '0',
                'PE (' . $profile['profiler']['R1NumPE'] . ')' => $profile['profiler']['R1NumPE'],
                'E1 (' . $profile['profiler']['R1NumE1'] . ')' => $profile['profiler']['R1NumE1'],
                'E2 (' . $profile['profiler']['R1NumE2'] . ')' => $profile['profiler']['R1NumE2'],
                'E3 (' . $profile['profiler']['R1NumE3'] . ')' => $profile['profiler']['R1NumE3'],
                'L1 (' . $profile['profiler']['R1NumL1'] . ')' => $profile['profiler']['R1NumL1'],
                'L2 (' . $profile['profiler']['R1NumL2'] . ')' => $profile['profiler']['R1NumL2'],
                'L3 (' . $profile['profiler']['R1NumL3'] . ')' => $profile['profiler']['R1NumL3'],
                'Not Taken (' . $profile['profiler']['R1NumNA'] . ')' => $profile['profiler']['R1NumNA'],
            );

            pieChart($profilerICT, 'Nums R1', $colours);
            ?>
        </td>
        <td>
            <?php
            $profilerICT = array(
                '0' => '0',
                'PE (' . $profile['profiler']['R2NumPE'] . ')' => $profile['profiler']['R2NumPE'],
                'E1 (' . $profile['profiler']['R2NumE1'] . ')' => $profile['profiler']['R2NumE1'],
                'E2 (' . $profile['profiler']['R2NumE2'] . ')' => $profile['profiler']['R2NumE2'],
                'E3 (' . $profile['profiler']['R2NumE3'] . ')' => $profile['profiler']['R2NumE3'],
                'L1 (' . $profile['profiler']['R2NumL1'] . ')' => $profile['profiler']['R2NumL1'],
                'L2 (' . $profile['profiler']['R2NumL2'] . ')' => $profile['profiler']['R2NumL2'],
                'L3 (' . $profile['profiler']['R2NumL3'] . ')' => $profile['profiler']['R2NumL3'],
                'Not Taken (' . $profile['profiler']['R2NumNA'] . ')' => $profile['profiler']['R2NumNA'],
            );

            pieChart($profilerICT, 'Nums R2', $colours);
            ?>
        </td>
    </tr>
</table>

<table style="width: 100%;">
    <tr>
        <th colspan="3"><h3>Communication Profiler Results</h3></th>
    </tr>
    <tr>
        <td>
            <?php
// Coms graphs

            $profilerICT = array(
                '0' => '0',
                'PE (' . $profile['profiler']['D1ComPE'] . ')' => $profile['profiler']['D1ComPE'],
                'E1 (' . $profile['profiler']['D1ComE1'] . ')' => $profile['profiler']['D1ComE1'],
                'E2 (' . $profile['profiler']['D1ComE2'] . ')' => $profile['profiler']['D1ComE2'],
                'E3 (' . $profile['profiler']['D1ComE3'] . ')' => $profile['profiler']['D1ComE3'],
                'L1 (' . $profile['profiler']['D1ComL1'] . ')' => $profile['profiler']['D1ComL1'],
                'L2 (' . $profile['profiler']['D1ComL2'] . ')' => $profile['profiler']['D1ComL2'],
                'L3 (' . $profile['profiler']['D1ComL3'] . ')' => $profile['profiler']['D1ComL3'],
                'Not Taken (' . $profile['profiler']['D1ComNA'] . ')' => $profile['profiler']['D1ComNA'],
            );


            pieChart($profilerICT, 'Coms Diag', $colours);
            ?>
        </td>
        <td>
            <?php
            $profilerICT = array(
                '0' => '0',
                'PE (' . $profile['profiler']['R1ComPE'] . ')' => $profile['profiler']['R1ComPE'],
                'E1 (' . $profile['profiler']['R1ComE1'] . ')' => $profile['profiler']['R1ComE1'],
                'E2 (' . $profile['profiler']['R1ComE2'] . ')' => $profile['profiler']['R1ComE2'],
                'E3 (' . $profile['profiler']['R1ComE3'] . ')' => $profile['profiler']['R1ComE3'],
                'L1 (' . $profile['profiler']['R1ComL1'] . ')' => $profile['profiler']['R1ComL1'],
                'L2 (' . $profile['profiler']['R1ComL2'] . ')' => $profile['profiler']['R1ComL2'],
                'L3 (' . $profile['profiler']['R1ComL3'] . ')' => $profile['profiler']['R1ComL3'],
                'Not Taken (' . $profile['profiler']['R1ComNA'] . ')' => $profile['profiler']['R1ComNA'],
            );


            pieChart($profilerICT, 'Coms R1', $colours);
            ?>
        </td>
        <td>
            <?php
            $profilerICT = array(
                '0' => '0',
                'PE (' . $profile['profiler']['R2ComPE'] . ')' => $profile['profiler']['R2ComPE'],
                'E1 (' . $profile['profiler']['R2ComE1'] . ')' => $profile['profiler']['R2ComE1'],
                'E2 (' . $profile['profiler']['R2ComE2'] . ')' => $profile['profiler']['R2ComE2'],
                'E3 (' . $profile['profiler']['R2ComE3'] . ')' => $profile['profiler']['R2ComE3'],
                'L1 (' . $profile['profiler']['R2ComL1'] . ')' => $profile['profiler']['R2ComL1'],
                'L2 (' . $profile['profiler']['R2ComL2'] . ')' => $profile['profiler']['R2ComL2'],
                'L3 (' . $profile['profiler']['R2ComL3'] . ')' => $profile['profiler']['R2ComL3'],
                'Not Taken (' . $profile['profiler']['R2ComNA'] . ')' => $profile['profiler']['R2ComNA'],
            );

            pieChart($profilerICT, 'Coms R2', $colours);
            ?>
        </td>
    </tr>
</table>
</div>
<?php

?>
<div class="well">
    <h3>Background</h3>
    <textarea class="xxlarge" name="background" id="background"
              style="width: 1063px; height: 165px;"><?php echo $result->background; ?></textarea>


    <h3>Preferred Learning Styles</h3>

    <textarea class="xxlarge" name="learning_styles" id="learning_styles"
              style="width: 1063px; height: 165px;"><?php echo $result->preferred_learning_styles; ?></textarea>

</div>
<div class="well">
    <h3>Differentiation</h3>
    <?php


    foreach ($profile['differentiation'] as $item) {
        echo $item;
        echo '<hr>';
    };

    ?>
</div>
<div class="well">
    <h2>Support Needs</h2>

    <h3>Particular Difficulties/ Special Needs</h3>
    <textarea class="xxlarge" name="difficulties" id="difficulties"
              style="width: 1063px; height: 165px;"><?php echo $result->difficulties; ?></textarea>

    <h3>Special Circumstances</h3>
    <textarea class="xxlarge" name="special_circumstances"
              id="special_circumstances"
              style="width: 1063px; height: 165px;"><?php echo $result->circumstances; ?></textarea>

    <h3>Confidence/ Group Participation</h3>
    <textarea class="xxlarge" name="confidence" id="confidence"
              style="width: 1063px; height: 165px;"><?php echo $result->confidence; ?></textarea>

    <h3>How Differentiation Needs Are Being Met</h3>
    <textarea class="xxlarge" name="differentiation_needs"
              id="differentiation_needs"
              style="width: 1063px; height: 165px;"><?php echo $result->differentiation; ?></textarea>


    <h2>use of Facilitators</h2>
    <textarea class="xxlarge" name="facilitators"
              id="facilitators"
              style="width: 1063px; height: 165px;"><?php echo $result->facilitators; ?></textarea>
</div>
<div class="well">
    <h2>Other Relevant Information</h2>
    <textarea class="xxlarge" name="other" id="other"
              style="width: 1063px; height: 165px;"><?php echo $result->other; ?></textarea>


</div>
<div class="well">
    <h2>Completed By</h2>
    <table style="width: 100%;">
        <tr>
            <th> Tutor</th>
            <td><input type="text" id="tutor" name="tutor" value="<?php echo $result->tutor; ?>"/></td>
            <th>Time/date</th>

            <?php if (!empty($result->date)) { ?>}
            <td><input type="text" id="datepicker" name="date"
                       value="<?php echo date('d-m-Y', strtotime($result->date)); ?>"/></td>
            <td>
                <?php } else { ?>
            <td><input type="text" id="datepicker" name="date"
                       value="<?php echo date('d-m-Y'); ?>"/></td>
            <td>
        <?php
        }
            ?>
            <input type="hidden" name="course_id" id="course_id" value="<?php echo $courseId; ?>">
            <input type="hidden" name="group_id" id="group_id" value="<?php echo $groupId; ?>">
            <button type="submit" class="btn btn-success" name="submit2" value=""><i class="icon-white icon-ok"></i>
                Save Report
            </button>

            </form>
        </td>
        </tr>
    </table>
</div>
</div>
<?php

include('bottom_include.php');

?>

<script>
    $(function () {
        $("#datepicker").datepicker(
            {dateFormat:'dd-mm-yy'}
        );
    });
</script>
