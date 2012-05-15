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
//echo 'course ref: ' . $_SESSION['course_code_session'];
    $query = "SELECT idnumber FROM mdl_course WHERE id='" . $_SESSION['course_code_session'] . "'";
//    echo $query;
    $result = $mysqli->query($query);

    while ($row = $result->fetch_object()) {
        $_SESSION['course_ref_session'] = $row->idnumber;
    }

    ?>

    <div class="span6">
        <table>
            <tr>
                <th colspan="2">Options</th>
            </tr>
            <tr>
                <td style="vertical-align: middle;">
                    <a href="#" class="addNew"><br/><br/><br/><br/>Unit info</a></p>
</td>
                <td>
                    <?php
                    // Set up the edit button
                    $queryEdit = "SELECT id FROM unit_tracker_courses WHERE course_code='" . $_SESSION['course_ref_session'] . "'";
                    $resultEdit = $mysqli->query($queryEdit);
//                    echo $queryEdit;


                    if ($resultEdit->num_rows >= 1) {

                        while ($rowEdit = $resultEdit->fetch_object()) {
                            echo '<a class="edit" href="edit_units2.php?var1=', $rowEdit->id, '" /><br/><br/><br/><br/>Edit unit details</a/>';
                        }

                    } else {
                        echo '<font color="red"><b>This course hasn\'t been set up for unit tracking. Please contact ICT</b></font>';
                    }
                    ?>

                </td>
            </tr>
        </table>
    </div>

</div>

<!-- Load the main javscript Asynchronously-->
<script id="myscript" type="text/javascript">

    (function () {
        var myscript = document.createElement('script');
        myscript.type = 'text/javascript';
        myscript.src = ('jscripts.js');
        var s = document.getElementById('myscript');
        s.parentNode.insertBefore(myscript, s);
    })();

</script>

<p>

<div id="page">
<div id="layout">
<div class="demo">

<?php

//$courseid = $_GET['courseid'];
//$contextid = $_GET['var1'];
////$status = $_GET['status'];
////$userid = $USER->id;
////
////
////// start a session
////
//if (empty($_SESSION['course_code_session'])) {
//    $_SESSION['course_code_session'] = $courseid;
//    $_SESSION['course_context_session'] = $contextid;
//}

// the order is important here or the correct information isn't loaded into the navigation header

//include('process_forms.php');

//$courseContextId = getCourseContextID( $_SESSION['course_code_session']);

// Get all the students on the course
//echo '<h1> students on the course </h1>';
//echo 'group id is: ' . $group_id;
//select and show all students on this course used roleid 5 to indentify students
// $querystudents = "SELECT a.userid, firstname, lastname FROM {$CFG->prefix}role_assignments a JOIN {$CFG->prefix}user u on a.userid=u.id where contextid='" . $contextid . "' AND a.roleid='5' order by lastname";
$select = "SELECT  distinct u.id, u.firstname, u.lastname, u.idnumber, u.username";
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

$resultStudents = $mysqli->query($querystudents);
$resultStudents2 = $mysqli->query($querystudents);


$check = 0;
while ($row = $resultStudents->fetch_object()) {
    //    $firstname = $row->firstname;
    //    $lastname = $row->lastname;
    //    $learnerref = $row->idnumber;
    //    $moodleId = $row->id;
    //    echo 'l ref: ' . $learnerref;

    // Check the students have an entry in the apprentice tables - else create them

    $queryApp = "SELECT id FROM unit_tracker_user_courses WHERE learner_ref='" . $row->idnumber . "' AND course_code='" . $_SESSION['course_ref_session'] . "'";
    //    echo $queryApp;
    $resultApp = $mysqli->query($queryApp);
    //    $num_rows = $resultApp->num_rows;
    //    echo 'num_rows: ' . $num_rows;
    if ($resultApp->num_rows < 1) {
        echo 'No record';
        echo ' creating a record';
        $check = 1;
        $queryCreate = "INSERT INTO unit_tracker_user_courses (learner_ref, course_code) VALUES ('$row->idnumber', '" . $_SESSION['course_ref_session'] . "')";
        echo $queryCreate;
        $mysqli->query($queryCreate);
    } else {
        //        echo 'Record found';
        // the student exists so pop them into an array to stop having to run the query again
        //        $student = array(
        //            $learnerref,
        //            $firstname,
        //            $lastname
        //        );
        //        array_push($students, $student);
    }

}

//echo 'check is: ', $check;
if ($check == 1) {
    echo '<font color=red>Warning Accounts have had to be created, please refresh the page</font>';
}

// Get the units for the course
//echo 'ref is: '  . $_SESSION['course_ref_session'];
$query = "SELECT id FROM unit_tracker_courses WHERE course_code='" . $_SESSION['course_ref_session'] . "'";
//echo $query;

$result = $mysqli->query($query);

while ($row = $result->fetch_object()) {

    //    echo $row['id'] . ' ' . $row['courseid'] . ' ' . $row['coursename'] . '<br/>';
    $apprenticeCourseId = $row->id;
}

showCurrentlySelectedCourse($CFG, $mysqli);

// Build thes array

echo '<h1>Course Unit/ Subject Tracker *Beta V2*</h1>';

// FIXME break out into an xml service

// Get the course id ion the tables
$query = "SELECT id FROM unit_tracker_courses WHERE course_code='" . $_SESSION['course_ref_session'] . "'";
//echo $query;
$result = $mysqli->query($query);

// count the rows and clear the internal session is a row isn't returned
if ($result->num_rows >= 1) {
    while ($row = $result->fetch_object()) {
        $_SESSION['course_code_internal_session'] = $row->id;
    }
} else {
    $_SESSION['course_code_internal_session'] = '';
}
$course = array();
// Get the units on the course

$queryUnits = "SELECT  u.id, u.name, u.courseid, u.description, m.type, m.colours  FROM unit_tracker_units u LEFT JOIN unit_tracker_marks m ON u.markid=m.id WHERE u.courseid='" . $_SESSION['course_code_internal_session'] . "'";

//echo $queryUnits;
$resultUnits = $mysqli->query($queryUnits);

// loop through and add the units
while ($rowUnits = $resultUnits->fetch_object()) {

    $unit = array(
        "Unit_id" => $rowUnits->id,
        "unit_name" => $rowUnits->name,
        "unit_type" => $rowUnits->type,
        "unit_colours" => $rowUnits->colours,
    );

    //        print_r($unit);

    // add the critiea for each unit to the units array
    $queryCrit = "SELECT c.id, name, type, description, colours FROM unit_tracker_units_criteria c JOIN unit_tracker_marks_criteria mc ON c.markid=mc.id WHERE unitid='" . $rowUnits->id . "'";
//            echo $queryCrit;
    $resultCrit = $mysqli->query($queryCrit);
    while ($rowCrit = $resultCrit->fetch_object()) {
        $critria = array(
            'Crit_id' => $rowCrit->id,
            'Crit_name' => $rowCrit->name,
            'Crit_mark' => $rowCrit->type,
            'Crit_des' => $rowCrit->description,
            "crit_colours" => $rowCrit->colours,
        );
        //        print_r($critria);
        array_push($unit, $critria);
    }

    array_push($course, $unit);
}

//print_r($course);

// Count starts at one to allow of the unit mark column

echo '<table><tr><td>';

echo '</td><td align="center">';

echo '</td></tr></table>';

echo '<table class="table"><tr><th>Show/ hide units</th>';
$col = 1;
foreach ($course as $key => $value) {
    echo '<th class="unit_name"><input type=checkbox name="ucol', $col, '" checked>', $value[unit_name], '</th>';

    $col++;
}
echo '</tr>';


echo '<tr><th>Show/ hide unit criteria</th>';
$col = 1;
foreach ($course as $key => $value) {
    echo '<th class="show_units crit_target"><input type=checkbox name="tcol', $col, '" >', $value[unit_name], '</th>';

    $col++;
}
echo '</tr></table class="table">';

$critCount = 1;
$colId = 1;

echo '<br/>';
echo '<br/>';
echo '<form name="process" method="POST" action="process_tracker2.php">';
echo '<input type="hidden" name="courseCode" value="', $_SESSION['course_ref_session'], '"/>';
echo '<table class="table table-striped" id="examplex" style="text-align: center;">';
echo '<thead><tr><th class="header1">Firstname</th><th class="header1">Lastname</th><th class="header1">Learner Ref</th><th class="header1">Report</th>';


//  print out the criteria headers
$colId = 1;
foreach ($course as $key => $value) {
    echo '<th class="unit_name ucol', $colId, '" name="ucol', $colId, '">' . $value[unit_name] . '</th>';

    $critCol = 1;
    foreach ($value as $iKey => $iValue) {
        // Knock out the duff results and count the number of criterias

        //        if (($iValue[Crit_name] != "U") && ($iValue[Crit_name] != '7') && ($iValue[Crit_name] != 'P') && ($iValue[Crit_name] != '6')) {
        //            $critCount++;
        //
        //            echo '<th class="criteria_name tcol', $colId, '" name="tcol', $colId, '">' . $iValue[Crit_name] . '</th>';
        //        }

        // Knock out the duff results based on stings 1 character long and count the number of criterias
        //        $num_char = strlen($iValue[Crit_name]);
        echo $num_char;
        if (strlen($iValue[Crit_name]) > 1) {
            $critCount++;

            if ($critCol == 1) {
                $critClass = 'crit_target';
                $critCol = 2;
            } elseif ($critCol == 2) {
                $critClass = 'crit_target2';
                $critCol = 1;
            }
            echo '<th class="' . $critClass . ' tcol', $colId, '" name="tcol', $colId, '">' . $iValue[Crit_name] . '</th>';
        }
    }
    $colId++;
}
echo '</tr></thead><tbody>';


// print out the students criteria marks
$colId = 0;
$count = 0;
$colourId = 0;

while ($row = $resultStudents2->fetch_object()) {
    echo '<tr><td><input  type="hidden" name="learner[][firstname]" value="', $row->firstname, '"/>', $row->firstname, '</td><td><input type="hidden" name="learner[][lastname]" value="', $row->lastname, '"/>', $row->lastname, '</td><td><input type="hidden" name="learner[][learner_ref]" value="', $row->idnumber, '"/>', $row->idnumber, '</td>';

    // The report button
    echo '<td><a href="apprentice_report.php?stuID=', $row->idnumber, '"/><img src="./images/report_check.png" width="20px" border="0"/></a></td>';

    // get all the students units and stuff
    $query = "select distinct uc.learner_ref, uc.employer, uc.rep_name, uc.employer_id as employerid ,uc.training_centre, uc.programme_start_date, uc.officer,
c.course_code, c.coursename,
u.id as unit_id, u.name as unit_name, u.description, u.markid,
um.id as mark_id, um.type as mark_type,
uu.target as unit_target, uu.evidence as unit_evidence, uu.user_id,
uuc.id as crit_id, uuc.name as crit_name, uuc.description as crit_description,
usrc.target as crit_target, usrc.evidence as crit_evidence, usrc.user_id,
mc.id as criteria_id, mc.type as criteria_type, mc.colours as criteria_colours,
mm.type as unit_marks, mm.colours as unit_colours
FROM unit_tracker_user_courses uc
LEFT JOIN unit_tracker_courses c ON c.course_code=uc.course_code
LEFT JOIN unit_tracker_units u ON u.courseid=c.id
LEFT JOIN unit_tracker_user_units uu ON u.id=uu.unit_id and uu.user_id=uc.learner_ref
LEFT JOIN unit_tracker_marks mm ON u.markid=mm.id
LEFT JOIN unit_tracker_marks um ON u.markid=um.id LEFT JOIN unit_tracker_units_criteria uuc ON uuc.unitid=u.id
LEFT JOIN unit_tracker_user_criteria usrc ON usrc.criteria_id=uuc.id AND usrc.user_id=uc.learner_ref
LEFT JOIN unit_tracker_marks_criteria mc ON uuc.markid=mc.id

where learner_ref='" . $row->idnumber . "' AND c.course_code='" . $_SESSION['course_ref_session'] . "'";
//                echo $query;
    $resultUnits = $mysqli->query($query) or die ('error;');
    $unitName = '';
    $colId = 0;
    $id = 1;
    $unitId = 1;

//echo 'units';
//    var_dump($resultUnits);

    while ($row2 = $resultUnits->fetch_object()) {
        // echo the unit mark using the unit name to test

        if ($unitName != $row2->unit_name) {
            // use the unit mark header to increment the tcol value
            $colId++;

//
//            $markcol = getMarkColour($row2->unit_id, $row->idnumber, $mysqli, 'unit');
//            echo 'mark; ' , $markcol;
            //echo 'uunitid: ' . $row['unit_id'];
            echo '<td class="ucol', $colId, '" name="ucol', $colId, '" style="background-color:' , getMarkColour($row2->unit_id, $row->idnumber, $mysqli, 'unit') , ';">', getPossbileUnitMarks($row2->unit_marks, $row2->unit_colours, $row2->unit_target, $unitId, 'unit', $row2->unit_id, $row->idnumber, $count), '</td>';
            $unitName = $row2->unit_name;

            $unitId++;
            $count++;

            //reset the colourid to allow the proper stripping
            $colourId = 0;
        }

//        if ($colourId == 0) {
//            $colour = 'crit_target';
//            $colourId = 1;
//        } else {
//            $colour = 'crit_target2';
//            $colourId = 0;
//        }


        // check for empty crit headers and if so don't echo any cells for them
        if (!empty($row2->crit_id)) {
            echo '<td class="tcol', $colId, '" name="tcol', $colId, '" bgcolor="' , getMarkColour($row2->crit_id, $row->idnumber, $mysqli, 'criteria') , '">', getPossbileUnitMarks($row2->criteria_type, $row2->criteria_colours, $row2->crit_target, $id, 'critieria', $row2->crit_id, $row->idnumber, $count), '</td>';
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


echo '<div id="dialog" title="Unit Descriptions">';

echo '<p>Below are details about the units and criteria on the course</p>';
echo '<div id="multiOpenAccordion">';
foreach ($course as $key => $value) {

    accord_first($value['unit_name']);
    foreach ($value as $iKey => $iValue) {
        if (($iValue[Crit_name] != "U") && ($iValue[Crit_name] != '7') && ($iValue[Crit_name] != 'P') && ($iValue[Crit_name] != '6')) {
            echo $iValue[Crit_name], ' ', $iValue[Crit_des], '<br/>';
        }
    }
    accord_last();

}
echo '<br/>';
echo '<br/>';

echo '</div>';
echo '</div>';

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
$mysqli->close;
mysql_close();

?>

