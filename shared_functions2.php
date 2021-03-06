<?php
/**
 * Created by JetBrains PhpStorm.
 * User: DATTWOOD
 * Date: 07/12/11
 * Time: 13:29
 * To change this template use File | Settings | File Templates.
 */


function topbar($menuItem)
{

    // Contains the nav bar items
    $navItems = array(
        'Group Target Setter' => '/blocks/group_targets/view2.php',
        'Change Target Status' => '/blocks/group_targets/set2.php',
        'Group Profile' => '/blocks/group_targets/group2.php',
        'Unit Tracker' => '/blocks/group_targets/tracker2.php',
//        'Import Feeds' => $siteUrl . '/jobshop/feeds/process_feeds.php',
//        'View Applications' => $siteUrl . '/jobshop/processing/index.php',
//        'Featured Jobs' => $siteUrl . '/jobshop/slider/index.php',
    );

    echo '<div class="noprint">';
    echo '<div class="navbar navbar-fixed-top">';
    echo '<div class="navbar-inner">';
    echo '<div class="container-fluid">';
    echo '<a class="brand" href="#">Group Target Setter v2.0</a>';
    echo '<ul class="nav">';
    //print_r($navItems);
    foreach ($navItems as $key => $item) {

        //        echo $key . ' ' . $item;

        if ($menuItem != $key) {
            $active = '';
        } elseif ($menuItem == $key) {
            $active = ' class="active"';
        }

        echo '<li  ', $active, '><a href="', $item, '">', $key, '</a></li>';
    }

    echo '</ul>';
    //    echo '<p class="pull-right">Logged in as <a href="#">username</a></p>';';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

}


function showCurrentlySelectedCourse($CFG, $DB)
{

    $resultcourse = $DB->get_record('course', array('id' => $_SESSION['course_code_session'] ));

        $fullname = $resultcourse->fullname;

    echo '<b><a class="btn btn-success" href="' . $CFG->wwwroot . '/course/view.php?id=' . $_SESSION['course_code_session'] . '">The currently selected course is ' . preg_replace("/[^a-zA-Z0-9\s]/", "", $fullname) . '</a></b>';
}

function getCourseContextID($courseId, $DB)
{

    $result = $DB->get_record('context' , array('instanceid' => $courseId, 'contextlevel' => '50'));
    return $result->id;
}

// Get the date 1 month ago
function getDateMonth()
{
    $dateMonth = strtotime("-1 month");
    return $dateMonth;
}

// Gets all reviews for the students and order them. They are then combined in the view to give only the last review
function getLastReview($whereSetForUserid2, $DB)
{

    //@still not convinced this is the best way but at least it only returns one row per student
     $query = "SELECT e.user_id, firstname, lastname,e.timecreated FROM mdl_block_ilp_entry e
JOIN mdl_user u ON u.id=e.creator_id
WHERE  report_id='3' AND (" . $whereSetForUserid2 . ")
GROUP BY e.user_id
 ORDER BY  e.timecreated DESC";

    $result = $DB->get_records_sql($query);
    $reviewer = array();
    foreach ($result as $row) {
        $reviewer[] = array(
            'id' => $row->user_id,
            'lastreview' => $row->firstname . ' ' . $row->lastname,
            'lastreviewdate' => date("d-m-Y", $row->timecreated),
        );
    }

//    echo '<br>Last Review<br>';
//    print_r($reviewer);

    return $reviewer;
}

// Get the students review, concern etc numbers
function getReviews2($whereSetForUserid, $DB, $reportsArray)
{
      // build the query
    $query2 = '';
      foreach ($reportsArray as $key => $item) {
            $query2 =  $query2 . " SUM(IF(report_id = '" . $key . "',1,0)) AS '" . $item . "',";
      }

    // knock of the last comma
    $query2 = substr($query2, 0, -1);

    $query1 = "SELECT u.id, ";
    $from = " FROM mdl_user u";
    $join = " LEFT JOIN mdl_block_ilp_entry e ON u.id=e.user_id";
    $where = " WHERE " . $whereSetForUserid . "";
    $group = " GROUP BY u.id";

    $query = $query1 . $query2 . $from . $join . $where . $group;
//        echo $query;
    $result = $DB->get_records_sql($query);

    $reviews = array();

//    print_r($result);

    foreach ($result as $key => $row) {
        // loop through the array to build the reviews array from the available options
               $reviews[$key] =  $row;
    }

//    echo '<br>reviews<br>';
    $reviews = object2Array($reviews);
//    print_r($reviews);

    return $reviews;
}



 	function object2Array($d)
     {
         if (is_object($d))
         {
             $d = get_object_vars($d);
         }

         if (is_array($d))
         {
             return array_map(__FUNCTION__, $d);
         }
         else
         {
             return $d;
         }
     }


function getRag2($whereSetForUserid, $DB)
{

     $query = "SELECT * FROM mdl_block_ilp_user_status e WHERE " . $whereSetForUserid . "";

//    $query = $query . $from . $join . $where;
//    $result = $mysqli->query($query);
    //    echo $query;

    $result = $DB->get_records_sql($query);

    $rag = array();
    foreach ($result as $row) {
        $rag[] = array(
            'id' => $row->user_id,
            'ragstatus' => $row->parent_id,
        );
    }

//    echo '<br>RAG<br>';
//    print_r($rag);

    return $rag;

}

// Get the students targets
function getTargets2($whereSetForUserid, $DB, $targetId)
{

//    $query = "SELECT u.id, setforuserid, status, p.timemodified  ,SUM(IF(status = '0',1,0)) AS 'tobe', SUM(IF(status = '1',1,0)) AS 'achieved'  , SUM(IF(status = '3',1,0)) AS 'withdrawn'";
//    $from = " FROM mdl_user u  ";
//    $join = " LEFT JOIN mdl_ilptarget_posts p ON u.id=p.setforuserid ";
//    $where = "WHERE " . $whereSetForUserid . "";
//    $group = " GROUP BY u.id";
//    $query = $query . $from . $join . $where . $group;

$query = "SELECT u.id, pi.name, pi.passfail, SUM(IF(pi.passfail = '0',1,0)) AS 'tobe', SUM(IF(pi.passfail = '2',1,0)) AS 'achieved'  , SUM(IF(pi.passfail = '3',1,0)) AS 'withdrawn'
FROM mdl_user u
LEFT JOIN mdl_block_ilp_entry e ON u.id=e.user_id
LEFT JOIN mdl_block_ilp_plu_ste_ent pe on e.id=pe.entry_id
LEFT JOIN mdl_block_ilp_plu_ste_items pi on pe.parent_id=pi.id
WHERE e.report_id='" . $targetId . "' and " . $whereSetForUserid . "
 GROUP BY u.id";
//         echo $query;
    $result = $DB->get_records_sql($query);
    $targets = array();
    foreach ($result as $row) {

        $targets[] = array(

            'id' => $row->id,
            'tobe' => $row->tobe,
            'achieved' => $row->achieved,
            'withdrawnTarget' => $row->withdrawn,
        );
    }


//    print_r($targets);
    return $targets;
}


function getMTGS2($whereSetForUserid, $DB)
{
    $query = "SELECT u.id, m.mtg, m.tutor_mtg, m.qca, m.mis_mtg ";
    $from = " FROM mdl_user u ";
    $join = " LEFT JOIN mdl_mtg m ON u.idnumber=m.student_id ";
    $where = "WHERE " . $whereSetForUserid . "";
    $query = $query . $from . $join . $where;
//            echo $query;
    $result = $DB->get_records_sql($query);
    $mtgArray = array();
   foreach ($result as $row) {
        $mtgArray[] = array(
            'id' => $row->id,
            'mtg' => $row->mtg,
            'tutor_mtg' => $row->tutor_mtg,
            'qca' => $row->qca,
            'mis_mtg' => $row->mis_mtg,
        );
    }

//    echo '<br>MTGS<br>';
//        print_r($mtgArray);

    return $mtgArray;
}


function getFlightplanScores($studentId, $DB)
{

    if ($studentId != '') {
        // select the last 4 reviews{}

        $query = "SELECT score FROM mdl_flightplan WHERE student_id='" . trim($studentId) . "' ORDER BY date DESC LIMIT 4";
//           echo $query;
        $result = $DB->get_records_sql($query);

        $num_rows = count($result);

        $review1 = 0;
        $review2 = 0;
        $review3 = 0;
        $review4 = 0;

        $i = $num_rows;
       foreach ($result as $row) {

            ${review . $i} = $row->score;
            $i--;
        }

        $r2 = compareReviews($review1, $review2);
        $r3 = compareReviews($review2, $review3);
        $r4 = compareReviews($review3, $review4);

        $reviews = array(
            $review1, $r2, $review2, $r3, $review3, $r4, $review4
        );

        return $reviews;
    }
}

// Compare the two reviews and set the colour to denote improvement or worsening
function compareReviews($one, $two)
{
    if (($one == '') or ($two == '')) {
        $reviewGraphic = '<img src="./images/list-remove.png" height="20px" width="20px" title="noscore">';
    } else {
        if ($two < $one) {
            //            $reviewColour = 'red';
            $reviewGraphic = '<img src="./images/go-down.png" height="20px" width="20px" title="down">';
        } elseif ($two > $one) {
            //            $reviewColour = 'green';
            $reviewGraphic = '<img src="./images/go-up.png" height="20px" width="20px" title="up">';
        } elseif ($two == $one) {
            //            $reviewColour = 'amber';
            $reviewGraphic = '<img src="./images/go-next.png" height="20px" width="20px" title="nochange">';
        }
    }


    //    array_push($reviews, $reviewColour);
    return $reviewGraphic;
}


function checkIfTrue($value)
{
    if ($value == 1) {
        echo '<img src="./images/tick-icon.png" height="20px" width="20px" alt="tick"/>';
    } elseif ($value == 2) {
        echo 'N/A';
    } else {
        echo '<img src="./images/delete-icon.png" height="20px" width="20px" alt="cross"/>';
    }
}

function checkIfTrueWithNo($value)
{
    if ($value == 'no') {

    } elseif ($value == 2) {
        echo 'N/A';
    } else {
        echo '<img src="./images/delete-icon.png" height"20px" width="20px"/>';
    }
}


// Get the medals awarded to the student
function getMedals($studentNumber, $mysqli)
{
    //    include('moodle_connection_mysqli.php');
    //    $query = "SELECT * FROM medals.students s JOIN medals.badges_link b ON b.student_id=s.id JOIN medals.badges bb ON bb.id=b.badge_id WHERE s.students_name='" . $username . "'";
    $query = "SELECT * FROM badges_link  JOIN badges ON badges_link.badge_id=badges.id  where student_id='" . $studentNumber . "'";

    echo $query;
    $result = $mysqli->query($query);
    $badges = array();
    while ($row = $result->fetch_object()) {
        $icon = $row->icon;
        array_push($badges, $icon);
    }
    $mysqli->close();
    //    print_r($badges);
    return $badges;

}

function mergeArrays($values, $colours, $legend)
{
    $merged = array();
    for ($i = 0; $i < count($values); ++$i) {
        $merged[$i] = array($values[$i], $colours[$i], $legend[$i]);
    }

    return $merged;
}

function graphWithMerged($merged)
{
    echo '<table>';
    foreach ($merged as $item) {
        if ($item[0] != 0) {
            echo '<tr>';
            echo '<th style="color:' . $item[1] . ';">' . $item[2] . ' (' . $item[0] . ')</th>';
            echo '</tr>';
        }
    }

    echo '</table>';


}

function makeWithThePretty($values, $colours, $legend, $title, $size)
{
    echo '<table><tr><td>';
    $legendDuff = '';
    makePieChart3($values, $legendDuff, $colours, $title, $size);
    echo '</td><td>';
    // Merge the three arrays
    $merged = mergeArrays($values, $colours, $legend);
    // echo the legend
    graphWithMerged($merged);

    echo '</td></tr></table>';
}

function getPossbileUnitMarks($gradesArray, $coloursArray, $current, $id, $unit, $critId, $learnerId, $colId)
{
    $grades = explode(',', $gradesArray);
    $colours = explode(',', $coloursArray);

//        print_r($grades);   /
    echo '<br/>';
    //print_r($colours);

    $combined = array_combine($grades, $colours);
    //print_r($combined);

    echo '<input type="hidden" name="unitName[', $colId, '][id]" value="', $critId, '"/>';
    echo '<input type="hidden" name="unitName[', $colId, '][learnerId]" value="', $learnerId, '"/>';
    echo '<input type="hidden" name="unitName[', $colId, '][unit]" value="', $unit, '"/>';

    echo '<select class="select_list small" name="unitName[', $colId, '][select]">';

    echo '<option>-</option>';

    foreach ($combined as $key => $item) {
        $selected = '';
        $colour = '';

        if (trim($current) == trim($key)) {
            $selected = 'selected="selected"';
            $colour = $item;
        }
        echo '"<option ', $selected, ' style="color:' . $item . '" >' . $key . '</option>';
    }

    echo '</select>';
    //    return $colour;
}


// Get the colour for the mark
function getColour($unitId, $value, $type, $DB, $CFG)
{

    // Check the type to use the right query
    if ($type == 'units') {

        $query = "SELECT type, colours FROM  {$CFG->prefix}unit_tracker_units u
    JOIN {$CFG->prefix}unit_tracker_marks mu ON mu.id=u.markid
    WHERE u.id='" . $unitId . "'";

    } elseif ($type == 'criteria') {
        $query = "SELECT type, colours FROM  {$CFG->prefix}unit_tracker_units_criteria u
    JOIN {$CFG->prefix}unit_tracker_marks_criteria mu ON mu.id=u.markid
    WHERE u.id='" . $unitId . "'";
    }
    //echo $query;
    $result = $DB->get_records_sql($query);

    foreach ($result as $row) {

        $grades = explode(',', $row->type);
        $colours = explode(',', $row->colours);
        //        print_r($grades);
        $combined = array_combine($grades, $colours);
        //        print_r($combined);
    }

    foreach ($combined as $key => $item) {
        //        echo '-' . $key;
        //        echo '-' . $item;
        //        echo '-' . $value;
        if (trim($value) == trim($key)) {
            echo 'hit on ' . $item;
            $colour = $item;
        }
    }
    return trim($colour);
}

function getMarkColour($unitId, $userId, $DB, $type, $CFG)
{

    if ($type == 'unit') {
        $table = "{$CFG->prefix}unit_tracker_user_units";
        $query = "SELECT colour FROM " . $table . " WHERE unit_id='" . $unitId . "' AND moodle_id='" . $userId . "'";
    } elseif ($type == 'criteria') {
        // check the colour isn't black as it will return black
        $table = "{$CFG->prefix}unit_tracker_user_criteria";
        $where = " criteria_id ='";
        $query = "SELECT colour FROM " . $table . " WHERE " . $where . $unitId . "' AND moodle_id='" . $userId . "'";


    }

//echo $query;

    $result = $DB->get_records_sql($query);
    foreach ($result as $row) {
        if (!empty($row->colour)) {
            $colour = $row->colour;
        } else {
            $colour = 'white';
        }
    }
    if (!empty($colour)) {
    return $colour;
    }

}



function passportMedals($award, $parts) {

    if ($award == 'bronze') {
        echo '<img src="./images/Medal-Bronze-icon.png" height"20px" width="20px"/>';
        echo '<br/>' . $parts;

    } elseif ($award == 'silver') {
            echo '<img src="./images/Medal-Silver-icon.png" height"20px" width="20px"/>';
        echo '<br/>' . $parts;
        } elseif ($award == 'gold') {
                    echo '<img src="./images/Medal-Gold-icon.png" height"20px" width="20px"/>';
        echo '<br/>' . $parts;
                } else {
        echo 'None<br/>' . $parts;
    }

}
?>