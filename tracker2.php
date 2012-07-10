<?php
include('top_include.php');

?>
<div class="container">
    <?php topbar('Unit Tracker'); ?>
</div>


<div class="container-fluid">
    <?php

    include('course_select_dropdown2.php');
//    echo 'ref: ' . $_SESSION['course_ref_session'];


    // Get all the course code
    $result = $DB->get_records('course', array('id' => $_SESSION['course_code_session']));

    foreach ($result as $row) {

        $courseId = $row->id;
        $courseName = $row->fullname;
        $courseCode = $row->idnumber;
        $_SESSION['course_ref_session'] = $row->idnumber;
    }
    ?>

    <div class="span4">
        <table class="table">
            <tr>
                <th colspan="2">Options</th>
            </tr>
            <tr>
                <td>
                    <?php
                    // Set up the edit button
//                    $queryEdit = "SELECT id FROM unit_tracker_courses WHERE course_code='" . $_SESSION['course_ref_session'] . "'";
                    $resultEdit = $DB->get_records('unit_tracker_courses', array('moodle_id' => $_SESSION['course_code_session']));
//                    echo $queryEdit;


                    if (count($resultEdit) >= 1) {
                        echo '<b>The course exists in the tracker</b><br>';
                     ?>
           <br> <a class="btn btn-warning" href="edit_units2_2.php?courseId=<?php echo $courseId; ?>" >
                <i class="icon-pencil icon-white"></i> Show / Edit Units & Criterias
                            </a>
                              <?php
                    } else {
                        echo '<font color="red"><b>This course hasn\'t been set up for unit tracking. Please wait while we set it up</b></font>';

//                        if (!empty($_SESSION['course_ref_session'])) {
                        echo '<p>The course code for this course is: ', $_SESSION['course_ref_session'];
                        // insert a record into the unit_tracker for the course
                        $query = "INSERT INTO {$CFG->prefix}unit_tracker_courses (course_code, coursename, moodle_id) VALUES ('" . $courseCode . "','" . $courseName . "','" . $courseId . "') ";

//                            echo $query;
                        $DB->execute($query);
                        // refresh the page
                        echo '<meta http-equiv="refresh" content="1">';
//                        }
                    }

                    ?>

                </td>
            </tr>
        </table>

    </div>
</div>

<div class="noprint">
    <?php
    showCurrentlySelectedCourse($CFG, $DB);
    ?>
</div>


<p>

<?php

// set the group id
if ($_SESSION['course_group_session'] == 'All groups') {
    $groupId = 0;
} else {
    $groupId = $_SESSION['course_group_session'];
}

// get the course context
$context = context_course::instance($courseId);
// get the enrolled students for the course
$students = get_enrolled_users($context, null, $groupId);

$check = 0;
foreach ($students as $row) {

// check if the students are already logged inot the unit tracker and if not add them and refresh the page
    $queryApp = "SELECT id FROM {$CFG->prefix}unit_tracker_user_courses WHERE moodle_id='" . $row->id . "' AND course_moodle_id='" . $courseId . "'";
    $resultApp = $DB->get_records_sql($queryApp);

    if (count($resultApp) < 1) {

        $check = 1;
        $queryCreate = "INSERT INTO {$CFG->prefix}unit_tracker_user_courses (learner_ref, course_code, moodle_id, course_moodle_id) VALUES ('$row->idnumber', '" . $_SESSION['course_ref_session'] . "','" . $row->id . "','" . $courseId . "')";
//        echo $queryCreate;
        $DB->execute($queryCreate);
    }

}

// If students have needed to be added to the unit tracker refresh the page
if ($check == 1) {
    echo '<div class="alert alert-error"<i class="icon-star"></i> Warning Accounts have had to be created, refreshing the page</div>';
    echo '<meta http-equiv="refresh" content="1">';
}

?>

            <h1>Course Unit/ Subject Tracker 2 *Beta V2*</h1>

<div class="container-fluid">


<?php

// Get the units on the course
$course = array();

$queryUnits = "SELECT  u.id, u.name, u.courseid, u.description, m.type, m.colours  FROM {$CFG->prefix}unit_tracker_units u LEFT JOIN {$CFG->prefix}unit_tracker_marks m ON u.markid=m.id WHERE u.courseid='" . $courseId . "'";

$resultUnits = $DB->get_records_sql($queryUnits);

// loop through and add the units
foreach ($resultUnits as $rowUnits) {

    $unit = array(
        "Unit_id" => $rowUnits->id,
        "unit_name" => $rowUnits->name,
        "unit_type" => $rowUnits->type,
        "unit_colours" => $rowUnits->colours,
    );

    // add the critiea for each unit to the units array
    $queryCrit = "SELECT c.id, name, type, description, colours FROM {$CFG->prefix}unit_tracker_units_criteria c JOIN {$CFG->prefix}unit_tracker_marks_criteria mc ON c.markid=mc.id WHERE unitid='" . $rowUnits->id . "'";
//           echo $queryCrit;
    $resultCrit = $DB->get_records_sql($queryCrit);
    if (count($resultCrit) > 0) {

        foreach ($resultCrit as $rowCrit) {
            $critria = array(
                'Crit_id' => $rowCrit->id,
                'Crit_name' => $rowCrit->name,
                'Crit_mark' => $rowCrit->type,
                'Crit_des' => $rowCrit->description,
                "crit_colours" => $rowCrit->colours,
            );

            array_push($unit, $critria);
        }
    }

    array_push($course, $unit);
}

// Count starts at one to allow of the unit mark column

echo '<table class="table"><tr><td>';

echo '</td><td align="center">';

echo '</td></tr></table>';

echo '<table class="table"><tr><th>Show/ hide units</th>';
$col = 1;
foreach ($course as $key => $value) {
    echo '<th class="unit_name"><input type=checkbox name="ucol', $col, '" checked>', $value['unit_name'], '</th>';

    $col++;
}
echo '</tr>';


echo '<tr><th>Show/ hide unit criteria</th>';
$col = 1;
foreach ($course as $key => $value) {
    echo '<th class="show_units crit_target"><input type=checkbox name="tcol', $col, '" >', $value['unit_name'], '</th>';

    $col++;
}
echo '</tr></table class="table">';

$critCount = 1;
$colId = 1;

echo '<br/>';
echo '<br/>';
echo '<form name="process" method="POST" action="process_tracker2.php">';
echo '<input type="hidden" name="courseCode" value="', $_SESSION['course_ref_session'], '"/>';
echo '<table class="table table-striped" id="tracker" style="text-align: center;">';
echo '<thead><tr><th class="header1">Firstname</th><th class="header1">Lastname</th><th class="header1">Learner Ref</th><th class="header1">Report</th>';


//  print out the criteria headers
$colId = 1;
foreach ($course as $key => $value) {
    echo '<th class="unit_name ucol', $colId, '" name="ucol', $colId, '">' . $value['unit_name'] . '</th>';

    $critCol = 1;
    foreach ($value as $iKey => $iValue) {
        // Knock out the duff results and count the number of criterias

        // Knock out the duff results based on stings 1 character long and count the number of criterias
        //        $num_char = strlen($iValue[Crit_name]);
//        echo $num_char;
        if (strlen($iValue['Crit_name']) > 1) {
            $critCount++;

            if ($critCol == 1) {
                $critClass = 'crit_target';
                $critCol = 2;
            } elseif ($critCol == 2) {
                $critClass = 'crit_target2';
                $critCol = 1;
            }
            echo '<th class="' . $critClass . ' tcol', $colId, '" name="tcol', $colId, '">' . $iValue['Crit_name'] . '</th>';
        }
    }
    $colId++;
}
echo '</tr></thead><tbody>';


// print out the students criteria marks
$colId = 0;
$count = 0;
$colourId = 0;

//print_r($students);

foreach ($students as $row) {
    echo '<tr><td><input  type="hidden" name="learner[][firstname]" value="', $row->firstname, '"/>', $row->firstname, '</td><td><input type="hidden" name="learner[][lastname]" value="', $row->lastname, '"/>', $row->lastname, '</td><td><input type="hidden" name="learner[][learner_ref]" value="', $row->id, '"/>', $row->idnumber, '</td>';

    // The report button
    echo '<td><a href="apprentice_report.php?stuID=', $row->idnumber, '"/><img src="./images/report_check.png" width="20px" border="0"/></a></td>';

    // get all the students units and stuff
    // Has to have u.id as the first option as this is a unqiue unit id, else moodle groups everything into one row

    $query = "SELECT  u.id, uc.learner_ref, uc.employer, uc.rep_name, uc.employer_id as employerid ,uc.training_centre, uc.programme_start_date, uc.officer,
c.course_code, c.coursename,
u.id as unit_id, u.name as unit_name, u.description, u.markid,
um.id as mark_id, um.type as mark_type,
uu.target as unit_target, uu.evidence as unit_evidence, uu.user_id,
uuc.id as crit_id, uuc.name as crit_name, uuc.description as crit_description,
usrc.target as crit_target, usrc.evidence as crit_evidence, usrc.user_id,
mc.id as criteria_id, mc.type as criteria_type, mc.colours as criteria_colours,
mm.type as unit_marks, mm.colours as unit_colours
FROM {$CFG->prefix}unit_tracker_user_courses uc
LEFT JOIN {$CFG->prefix}unit_tracker_courses c ON c.course_code=uc.course_code
LEFT JOIN {$CFG->prefix}unit_tracker_units u ON u.courseid=c.moodle_id
LEFT JOIN {$CFG->prefix}unit_tracker_user_units uu ON u.id=uu.unit_id and uu.moodle_id=uc.moodle_id
LEFT JOIN {$CFG->prefix}unit_tracker_marks mm ON u.markid=mm.id
LEFT JOIN {$CFG->prefix}unit_tracker_marks um ON u.markid=um.id LEFT JOIN {$CFG->prefix}unit_tracker_units_criteria uuc ON uuc.unitid=u.id
LEFT JOIN {$CFG->prefix}unit_tracker_user_criteria usrc ON usrc.criteria_id=uuc.id AND usrc.moodle_id=uc.moodle_id
LEFT JOIN {$CFG->prefix}unit_tracker_marks_criteria mc ON uuc.markid=mc.id

where uc.moodle_id='" . $row->id . "' AND c.moodle_id='" . $courseId . "'";
//                echo '<br>' ,  $query , '<br>';


    $resultUnits = $DB->get_records_sql($query);

    $unitName = '';
    $colId = 0;
    $id = 1;
    $unitId = 1;


//    print_r($resultUnits);

    foreach ($resultUnits as $row2) {
        // echo the unit mark using the unit name to test
//         echo ' unit name: ' . $row2->unit_name;
        if ($unitName != $row2->unit_name) {
            // use the unit mark header to increment the tcol value
            $colId++;

//
            echo '<td class="ucol', $colId, '" name="ucol', $colId, '" style="background-color:', getMarkColour($row2->unit_id, $row->id, $DB, 'unit', $CFG), ';">', getPossbileUnitMarks($row2->unit_marks, $row2->unit_colours, $row2->unit_target, $unitId, 'unit', $row2->unit_id, $row->id, $count), '</td>';
            $unitName = $row2->unit_name;

            $unitId++;
            $count++;

            //reset the colourid to allow the proper stripping
            $colourId = 0;
        }


        // check for empty crit headers and if so don't echo any cells for them
        if (!empty($row2->crit_id)) {
            echo '<td class="tcol', $colId, '" name="tcol', $colId, '" bgcolor="', getMarkColour($row2->crit_id, $row->id, $DB, 'criteria', $CFG), '">', getPossbileUnitMarks($row2->criteria_type, $row2->criteria_colours, $row2->crit_target, $id, 'critieria', $row2->crit_id, $row->id, $count), '</td>';
        }
        $id++;
        $count++;
    }

    $colId++;
    echo '</tr>';

}

echo '</tbody></table>';

//echo '<input type="submit" id="submit" name="submit" value="submit"/>';

echo '<input class="btn btn-large btn-success" id="save" type="submit" name="submit" value="Save Changes"/>';
echo '</form>';
?>
<br/>
<br/>
<?php
// clear the arrays

unset($units);
unset($course);
unset($unit);
unset($critria);
unset($course);

?>
</div>
</div>
</div>

<?php
include('bottom_include.php');

?>

<script>


    // Drive the checkboxes to hide the unit detials
    $(window).load(function () {
        $("input:checkbox:not(:checked)").each(function () {
            var column = "table ." + $(this).attr("name");
            $(column).hide();
        });

        $("input:checkbox").click(function () {
            var column = "table ." + $(this).attr("name");
            $(column).toggle();
        });
    });

</script>