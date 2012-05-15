<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

?>

<script type="text/javascript" src="./menu/ddlevelsmenu.js"></script>
<link rel="stylesheet" href="./menu/ddlevelsmenu-topbar.css" />
<!--<link rel="stylesheet" type="text/css" href="jquery/css/redmond/jquery-ui-1.8.11.custom.css"/>-->
<!--Drop Down Menu 1 HTML-->

<?php
////get the course name
//if ($type == '') {
//$type = $_GET['courseid'];
//
//}
        $querycoursename = "SELECT fullname FROM {$CFG->prefix}course WHERE id='" . $_SESSION['course_code_session'] . "'";

//echo $querycoursename;

        $resultcourse = mysql_query($querycoursename);

           while ($row = mysql_fetch_assoc($resultcourse)) {
            $fullname = $row['fullname'];
        }
        ?>

<!--<div id="ddtopmenubar" class="mattblackmenu">-->
<!--<ul>-->
<!--<li>--><?php //echo '<a href="view.php?courseid=' . $courseid .  '&var1=' . $contextid . '">Set multiple PLPs and view course reports</a>' ?><!-- </li>-->
<!--<li>--><?php //echo '<a href="tracker.php?courseid=' . $courseid .  '&var1=' . $contextid . '">Course Unit Tracker</a>' ?><!-- </li>-->
<!--<li>--><?php //echo '<a href="set.php?courseid=' . $courseid .  '&var1=' . $contextid . '">Change Target Status</a>' ?><!-- </li>-->
<!--<li>--><?php //echo '<b><a href="' . $CFG->wwwroot . '/course/view.php?id=' . $type . '">Curently selected course is ' . $fullname . '</a></b>' ?><!--</li>-->
<!--</ul>-->
<!--</div>-->

    <div id="ddtopmenubar" class="mattblackmenu">
<ul>
<li><?php echo '<a href="view.php">Set multiple PLPs and view course reports</a>' ?> </li>
<li><?php echo '<a href="tracker.php">Course Unit/ Subject Tracker</a>' ?> </li>
<li><?php echo '<a href="set.php">Change Target Status</a>' ?> </li>
<li><?php echo '<b><a href="' . $CFG->wwwroot . '/course/view.php?id=' .  $_SESSION['course_code_session'] . '">Curently selected course is ' . $fullname . '</a></b>' ?></li>
</ul>
</div>

<script type="text/javascript">
ddlevelsmenu.setup("ddtopmenubar", "topbar") //ddlevelsmenu.setup("mainmenuid", "topbar|sidebar")
</script>