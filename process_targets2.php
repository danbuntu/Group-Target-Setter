<?php

/*
 * Process the tick boxes and insert the target, person setting the target and deadline
 */

require_once("../../config.php");

global $USER, $CFG, $COURSE;

//print_r($_POST);

$count = 0;
$type = $_POST['type'];
$courseid = $_POST['courseid'];
$userid = $_POST['userid'];
$checkboxes = $_POST['checkbox'];
$target = $_POST['target'];
$title = $_POST['title'];
$date = $_POST['date'];
//$url = $_POST['url'];
//$url2 = $_POST['url2'];
$courserelated = $_POST['course_related'];
$rag = $_POST['rag'];
$radio = $_POST['medal'];
$idTxt = $_POST['message'];
$userName = $_POST['username'];
$b1 = $_POST['b1'];
$b2 = $_POST['b2'];
$b3 = $_POST['b3'];
$s1 = $_POST['s1'];
$s2 = $_POST['s2'];
$s3 = $_POST['s3'];
$g1 = $_POST['g1'];
$g2 = $_POST['g2'];
$g3 = $_POST['g3'];
$JobEmail = 'jobshop@midkent.ac.uk';
//$studentId = $_POST['studentId'];
echo 'type is' . $type;
echo $radio;
//set  the rag colour by status

echo '<h2>Making the changes you requested</h2>';

//switch ($_POST['submit']) {
//    echo 'test';
//    case 'submit_change':
echo 'change submitted';

if ($rag == 'Green') {
    $ragstatus = 0;
} elseif ($rag == 'Amber') {
    $ragstatus = 1;
} elseif ($rag == 'Red') {
    $ragstatus = 2;
}

if ($courserelated == 'ON') {
    $courserelated = '1';
    $relatedcourse = $courseid;
} else {
    $courserelated = '0';
    $relatedcourse = '0';
}


//echo 'date is ' . $date . '<br/>';
$date = strtotime($date);

$currentdate = date('d-m-Y h:i:s');
//echo $currentdate;
$currentdate = strtotime($currentdate);


if ($type == '--Select--') {
    echo '<h3><font color= red>No type set please set some - redirecting you back to the setting page</font></h3>';
    echo '<meta http-equiv="refresh" content="2;url=/blocks/group_targets/view2.php">';
    exit;
}

//check for blank sections
if (($target == '') && ($type != 'Medals') && ($type != 'In Order to Progress to...')) {
    if ($type != 'Employability Passport') {
        echo '<h3><font color= red>No details set please set some - redirecting you back to the setting page</font></h3>';
        echo '<meta http-equiv="refresh" content="2;url=/blocks/group_targets/view2.php">';
        exit;
    }
}

//set targets
if ($type == 'Target') {

    if ($title == '') {
        echo '<h3><font color= red>No target title set please set one - redirecting you back to the target setting page</font></h3>';
        echo '<meta http-equiv="refresh" content="2;url=/blocks/group_targets/view2.php">';
        exit;
    }

    if ($date == '') {
        echo '<h3><font color= red>No date set please set one - redirecting you back to the target setting page</font></h3>';
        echo '<meta http-equiv="refresh" content="2;url=/blocks/group_targets/view2.php">';
        exit;
    }

    $status = '0';
} elseif ($type == 'Progress Review') {
    $status = '0';
} elseif ($type == 'Concern') {
    $status = '1';
} elseif ($type == 'Reason for Status Change') {
    $status = '2';
} elseif ($type == 'Contribution') {
    $status = '3';
}

echo '<h3><font color=red>Processing details and redirecting you to your course page - hold tight</font></h3>';

//set the things
foreach ($checkboxes as $box) {
    // echo $box . 'is checked <br/>';
    //insert queries to add the targets go here based on their id - probably need to get
    echo 'user id is: ' . $userid;
    if ($type == 'Target') {
        $table = 'mdl_ilptarget_posts';
        $insert = "INSERT INTO " . $table . " (setforuserid, setbyuserid, course, courserelated, targetcourse, timecreated, timemodified, deadline, name, targetset, status, format) ";
        $values = "VALUES ('" . $box . "','" . $userid . "','" . $courseid . "','" . $courserelated . "','" . $relatedcourse . "','" . $currentdate . "','" . $currentdate . "','" . $date . "','" . $title . "','" . $target . "','" . $status . "','1')";
    } elseif ($type == 'RAG - Traffic Light') {
        $status = '2';
        //check if a record already exists
        $queryrag = "SELECT * FROM mdl_ilpconcern_status WHERE userid='" . $box . "'";
        $resultrag = mysql_query($queryrag);
        $num_rows = mysql_num_rows($resultrag);
        echo 'num rows' . $num_rows;
        //if a record exists update it else insert a new record
        if ($num_rows >= 1) {
            $insert = "UPDATE mdl_ilpconcern_status ";
            $values = "SET modified='" . $currentdate . "', modifiedbyuser='" . $userid . "', status='" . $ragstatus . "' WHERE userid='" . $box . "'";
        } else {
            $insert = "INSERT INTO mdl_ilpconcern_status (userid, created, modified, modifiedbyuser, status) ";
            $values = "VALUES ('" . $box . "','" . $currentdate . "','" . $currentdate . "','" . $userid . "','" . $ragstatus . "')";
        }
        //Insert a reason for status change
        $table2 = 'mdl_ilpconcern_posts';
        $insert2 = "INSERT INTO " . $table2 . " (setforuserid, setbyuserid, course, courserelated, targetcourse, timecreated, timemodified, deadline, concernset, status, format) ";
        $values2 = "VALUES ('" . $box . "','" . $userid . "','" . $courseid . "','" . $courserelated . "','" . $relatedcourse . "','" . $currentdate . "','" . $currentdate . "','" . $currentdate . "','" . $target . "','" . $status . "','1')";
    } elseif ($type == 'Progression Targets') {
        echo 'prog hit';
        echo 'date is' . $date . ' ';

        $date = date("Y-m-d h:i:s", $date);

        $query = "INSERT INTO progression_targets (studentid, target, setby, date) VALUES ('$box','" . $target . "','" . $userName . "','" . $date . "')";


        mysql_query($query);
        echo 'target set';
    } elseif ($type == 'In Order to Progress to...') {


        // check if the user already has a target

        $queryCheck = "SELECT id FROM progression_single_target WHERE studentid='" . $box . "'";
//        echo $queryCheck;
        $resultCheck = mysql_query($queryCheck);

        if (mysql_num_rows($resultCheck) > 0) {
            $queryProgress = "UPDATE progression_single_target set target='" . $title . "' WHERE studentid='" . $box . "'";
        } else {
            $queryProgress = "INSERT INTO progression_single_target (studentid, target) VALUES ('$box','" . $title . "')";
        }
//        echo $queryProgress . '<br/>';
        mysql_query($queryProgress);


    } elseif ($type == 'Medals') {
        // get students logon name
        $query = "SELECT * FROM mdl_user WHERE id='" . trim($box) . "'";
        echo '</br/>' . $query . '</br/>';
        $result = mysql_query($query);

        while ($row = mysql_fetch_assoc($result)) {
            $logon = $row['username'];
            $studentId = $row['idnumber'];
            echo 'username is: ' . $logon;
        }
        //
        //
        //                mysql_select_db('medals') or die('Unable to select the database');
        //cehck if they already have the medal
        echo 'badge is is: ' . $radio;
        $query = "select * from badges_link b WHERE student_id='" . $studentId . "' AND badge_id='" . $radio . "'";
        echo $query;
        $result = mysql_query($query);
        $num_rows = mysql_num_rows($result);
        echo 'rows is ' . $num_rows;


        if ($num_rows == 0) {

            //                    $query = "SELECT * from badges_link WHERE student_id='" . $studentId . "'";
            //                    echo ' select student ' . $query;
            //                    $result = mysql_query($query);
            //                    while ($row = mysql_fetch_assoc($result)) {
            //                        $id = $row['id'];
            //                        echo 'id is ' . $id . '<br/> ';
            //                    }
            //
            //                    if ($id != '') {
            $query = "INSERT INTO badges_link SET student_id='" . $studentId . "', badge_id='" . $radio . "'";
            echo $query;
            mysql_query($query);
            $id = '';
            //                    }
        }


        mysql_select_db('moodle') or die('Unable to select the database');

    } elseif ($type == 'Employability Passport') {
        // process a the employability checkboxes cos we all know that's  a lot of fun
        echo 'employ loop ';

        // The array to feed into the query
        $vars = array(
            (!empty($b1)) ? " b1 = '1' " : null,
            (!empty($b2)) ? " b2 = '1' " : null,
            (!empty($b3)) ? " b3 = '1' " : null,
            (!empty($s1)) ? " s1 = '1' " : null,
            (!empty($s2)) ? " s2 = '1' " : null,
            (!empty($s3)) ? " s3 = '1' " : null,
            (!empty($g1)) ? " g1 = '1' " : null,
            (!empty($g2)) ? " g2 = '1' " : null,
            (!empty($g3)) ? " g3 = '1' " : null,
        );

        $newvars = array_filter($vars, 'myfilterarray');

        $where = join(" , ", $newvars);

        $queryID = "SELECT idnumber, username, email FROM mdl_user WHERE id='" . trim($box) . "'";
        echo '</br/>' . $queryID . '</br/>';
        $resultID = mysql_query($queryID);
        $num_rows = mysql_num_rows($resultID);
        echo 'num_rows' . $num_rows;

        while ($row6 = mysql_fetch_assoc($resultID)) {
            $studentId = $row6['idnumber'];
            $learnerusername = $row6['username'];

            echo 'username is: ' . $studentId;
        }


        //                Check if the student has a passport record

        $query = "SELECT id FROM passport WHERE learner_ref='" . $studentId . "'";
        $resultNum = mysql_query($query);
        $num_rows = mysql_num_rows($resultNum);
        echo 'passport row: ', $num_rows;
        if ($num_rows < 1) {
            //                    Insert a row
            $query = "INSERT INTO passport (learner_ref) VALUES ('" . $studentId . "')";
            mysql_query($query);
            //                    Update the new row
            $query = "UPDATE passport SET " . $where . " WHERE learner_ref='" . $studentId . "'";
            echo $query;
            mysql_query($query);
        } else {
            $query = "UPDATE passport SET " . $where . " WHERE learner_ref='" . $studentId . "'";
            echo $query;
            mysql_query($query);
        }

        //                 check for bronze completion

        $queryPass = "SELECT * FROM passport WHERE learner_ref='" . $studentId . "'";
        echo $queryPass;
        $resultPass = mysql_query($queryPass);
        $num_rows_pass = mysql_num_rows($resultPass);
        echo 'rows pass ' . $num_rows_pass;

        while ($rowPass = mysql_fetch_assoc($resultPass)) {
            $b1check = $rowPass['b1'];
            $b2check = $rowPass['b2'];
            $b3check = $rowPass['b3'];
            $s1check = $rowPass['s1'];
            $s2check = $rowPass['s2'];
            $s3check = $rowPass['s3'];
            $g1check = $rowPass['g1'];
            $g2check = $rowPass['g2'];
            $g3check = $rowPass['g3'];
        }
        echo 'b1check: ' . $b1check;
        if (($b1check == 1) && ($b2check == 1) && ($b3check == 1)) {
            echo 'all criteria completed';

            // check if an email has already been sent
            $queryCheck = "SELECT bemail FROM passport WHERE learner_ref='" . $studentId . "'";
            $resultCheck = mysql_query($queryCheck);
            echo $queryCheck;

            while ($row = mysql_fetch_assoc($resultCheck)) {
                if (($row['bemail'] != 1) OR (empty($row['bemail']))) {
                    echo 'bemail is ' . $row['bemail'] . 'sending email';
                    sendemail('bronze', 'jobshop', $learnerid, $JobEmail);
                    $queryUP = "UPDATE passport set bcomp='1', bemail='1' WHERE learner_ref='" . $studentId . "'";
                    echo $queryUP;
                    mysql_query($queryUP);
                }
            }
        }

        echo 's1check: ' . $s1check;
        if (($s1check == 1) && ($s2check == 1) && ($s3check == 1)) {
            echo 'all criteria completed';

            // check if an email has already been sent
            $queryCheck = "SELECT semail FROM passport WHERE learner_ref='" . $studentId . "'";
            $resultCheck = mysql_query($queryCheck);
            echo $queryCheck;

            while ($row = mysql_fetch_assoc($resultCheck)) {
                if (($row['semail'] != 1) OR (empty($row['semail']))) {
                    echo 'semail is ' . $row['semail'] . 'sending email';
                    sendemail('silver', 'jobshop', $learnerid, $JobEmail);
                    $queryUP = "UPDATE passport set scomp='1', semail='1' WHERE learner_ref='" . $studentId . "'";
                    echo $queryUP;
                    mysql_query($queryUP);
                }
            }
        }

        echo 'g1check: ' . $g1check;
        if (($g1check == 1) && ($g2check == 1) && ($g3check == 1)) {
            echo 'all criteria completed';

            // check if an email has already been sent
            $queryCheck = "SELECT gemail FROM passport WHERE learner_ref='" . $studentId . "'";
            $resultCheck = mysql_query($queryCheck);
            echo $queryCheck;

            while ($row = mysql_fetch_assoc($resultCheck)) {
                if (($row['gemail'] != 1) OR (empty($row['gemail']))) {
                    echo 'gemail is ' . $row['gemail'] . 'sending email';
                    sendemail('gold', 'jobshop', $learnerid, $JobEmail);
                    $queryUP = "UPDATE passport set gcomp='1', gemail='1' WHERE learner_ref='" . $studentId . "'";
                    echo $queryUP;
                    mysql_query($queryUP);
                }
            }
        }


    } else {
        $table = 'mdl_ilpconcern_posts';
        $insert = "INSERT INTO " . $table . " (setforuserid, setbyuserid, course, courserelated, targetcourse, timecreated, timemodified, deadline, concernset, status, format) ";
        $values = "VALUES ('" . $box . "','" . $userid . "','" . $courseid . "','" . $courserelated . "','" . $relatedcourse . "','" . $currentdate . "','" . $currentdate . "','" . $currentdate . "','" . $target . "','" . $status . "','1')";
    }

    $queryinsert = $insert . $values;
    $queryinsert2 = $insert2 . $values2;

    echo '<h3>Processing target for ' . $box . '</h3><br/>';
    echo $queryinsert . '<br/>';
    echo $queryinsert2 . '<br/>';


    mysql_query($queryinsert);
    mysql_query($queryinsert2);

    $count = $count + 1;
}

echo '<br/>Target set for ' . $count . ' students';
//echo 'groupid' . $_GET['groupid'];
       echo '<meta http-equiv="refresh" content="0;url=/blocks/group_targets/view2.php?group=' . $_POST['groupid']  . '">';


$mysqli->close();

function myfilterarray($var)
{
    return !empty($var) ? $var : null;
}


function sendemail($award, $message, $learnerid, $to)
{

    //    select the message
    if ($message == 'award') {
        $message = "Congratulations you have been awarded the $award award on the employability passport.
http://moodle.midkent.ac.uk/blocks/ilp/view2.php
       .";
        $subject = "You have been awarded the $award award on the  employability passport";
    } elseif ($message == 'jobshop') {
        $subject = "A student has finished all criteria for the $award award";
        $message = "A student has finished all the criteria for the $award award.  Please check
        http://moodle.midkent.ac.uk/blocks/ilp/view2.php?courseid=1&id=" . $learnerid . "";
    }

    $headers = 'From: plp@midkent.ac.uk';

    mail($to, $subject, $message, $headers);
}


?>