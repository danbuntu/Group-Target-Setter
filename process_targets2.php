<?php

/*
 * Process the tick boxes and insert the target, person setting the target and deadline
 */

require_once("../../config.php");

global $USER, $CFG, $COURSE, $DB;

print_r($_POST);

$count = 0;
$courseid = $_POST['courseid'];
$userid = $_POST['userid'];
$courserelated = $_POST['course_related'];
$rag = $_POST['rag'];
$JobEmail = 'jobshop@midkent.ac.uk';
//$studentId = $_POST['studentId'];
//echo 'type is' . $type;
//echo $radio;
//set  the rag colour by status

echo '<h2>Making the changes you requested</h2>';


echo 'change submitted';
//echo 'date is ' . $date . '<br/>';
$date = strtotime($date);

$currentdate = date('d-m-Y h:i:s');
//echo $currentdate;
$currentdate = strtotime($currentdate);

echo '<h3><font color=red>Processing details and redirecting you to your course page - hold tight</font></h3>';


$checkboxes = $_POST['checkbox'];
$checkboxes = urldecode($checkboxes);
$checkboxes = unserialize($checkboxes);

//$checkboxes = urldecode(unserialize($_POST['checkbox']));
echo 'checkboxes ';
print_r($checkboxes);

//set the things
foreach ($checkboxes as $box) {
    // echo $box . 'is checked <br/>';
    //insert queries to add the targets go here based on their id - probably need to get
    echo 'user id is: ' . $userid;

    if ($_POST['type'] == 'badges') {
        // get students logon name
        $query = "SELECT * FROM mdl_user WHERE id='" . trim($box) . "'";
//        echo '</br/>' . $query . '</br/>';
        $result = $DB->get_records_sql($query);

        foreach ($result as $row) {
            $logon = $row->username;
            $studentId = $row->idnumber;
            echo 'username is: ' . $logon;
        }
        //
        //                mysql_select_db('medals') or die('Unable to select the database');
        //cehck if they already have the medal
        echo 'badge is is: ' . $_POST['medal'];
        $query = "SELECT * FROM mdl_badges_link b WHERE student_id='" . $studentId . "' AND badge_id='" . $_POST['medal'] . "'";
//        echo $query;
        $result = $DB->get_records_sql($query);
//        $num_rows = count($result);
//        echo 'rows is ' . $num_rows;

        if (count($result) == 0) {
            $query = "INSERT INTO mdl_badges_link SET student_id='" . $studentId . "', badge_id='" . $_POST['medal'] . "'";
//            echo $query;
            $DB->execute($query);
            $id = '';
        }


//        mysql_select_db('moodle') or die('Unable to select the database');

    } elseif ($_POST['type'] == 'rag') {
        // get students logon name
        echo $USER->id;
        updateStatus($_POST['rag'], $studentId, $USER->id);

    } elseif ($_POST['type'] == 'Employability Passport') {
        echo 'a hit';
        // process a the employability checkboxes cos we all know that's  a lot of fun
        echo 'employ loop ';

        // The array to feed into the query
        $vars = array(
            (!empty($_POST['b1'])) ? " b1 = '1' " : null,
            (!empty($_POST['b2'])) ? " b2 = '1' " : null,
            (!empty($_POST['b3'])) ? " b3 = '1' " : null,
            (!empty($_POST['s1'])) ? " s1 = '1' " : null,
            (!empty($_POST['s2'])) ? " s2 = '1' " : null,
            (!empty($_POST['s3'])) ? " s3 = '1' " : null,
            (!empty($_POST['g1'])) ? " g1 = '1' " : null,
            (!empty($_POST['g2'])) ? " g2 = '1' " : null,
            (!empty($_POST['g3'])) ? " g3 = '1' " : null,
        );

        $newvars = array_filter($vars, 'myfilterarray');;
        $where = join(" , ", $newvars);

        $queryID = "SELECT idnumber, username, email FROM mdl_user WHERE id='" . trim($box) . "'";
//        echo '</br/>' . $queryID . '</br/>';
        $resultID = $DB->get_records_sql($queryID);

        // @FIXME redundant code - join sql abosve with that below
        foreach ($resultID as $row6) {
            $studentId = $row6->idnumber;
            $learnerusername = $row6->username;

        }


        //                Check if the student has a passport record

        $query = "SELECT id FROM mdl_passport WHERE learner_ref='" . $studentId . "'";
//        echo $query;
        $resultNum = $DB->get_records_sql($query);
        print_r($resultNum);
//        echo 'passport row: ', $num_rows;
        if (empty($resultNum)) {
            //                    Insert a row
            $query = "INSERT INTO mdl_passport (learner_ref) VALUES ('" . $studentId . "')";
//            echo $query, '<br>';
            $DB->execute($query);
            //                    Update the new row
            $query = "UPDATE mdl_passport SET " . $where . " WHERE learner_ref='" . $studentId . "'";
//            echo $query, '<br>';
            $DB->execute($query);
        } else {
            $query = "UPDATE mdl_passport SET " . $where . " WHERE learner_ref='" . $studentId . "'";
//            echo $query, '<br>';
            $DB->execute($query);
        }

        //                 check for bronze completion

        $queryPass = "SELECT * FROM mdl_passport WHERE learner_ref='" . $studentId . "'";
//        echo $queryPass;
        $resultPass = $DB->get_records_sql($queryPass);
//        $num_rows_pass = count($resultPass);
//        echo 'rows pass ' . $num_rows_pass;

        foreach ($resultPass as $rowPass) {
            $b1check = $rowPass->b1;
            $b2check = $rowPass->b2;
            $b3check = $rowPass->b3;
            $s1check = $rowPass->s1;
            $s2check = $rowPass->s2;
            $s3check = $rowPass->s3;
            $g1check = $rowPass->g1;
            $g2check = $rowPass->g2;
            $g3check = $rowPass->g3;
        }
        echo 'b1check: ' . $b1check;
        if (($b1check == 1) && ($b2check == 1) && ($b3check == 1)) {
            echo 'all criteria completed';

            // @FIXME combine three loops below into one loop

            // check if an email has already been sent
            $queryCheck = "SELECT bemail FROM mdl_passport WHERE learner_ref='" . $studentId . "'";
//            echo $query;
            $resultCheck = $DB->get_records_sql($queryCheck);
//            echo $queryCheck;

            while ($row = mysql_fetch_assoc($resultCheck)) {
                if (($row['bemail'] != 1) OR (empty($row['bemail']))) {
                    echo 'bemail is ' . $row['bemail'] . 'sending email';
                    sendemail('bronze', 'jobshop', $learnerid, $JobEmail);
                    $queryUP = "UPDATE mdl_passport set bcomp='1', bemail='1' WHERE learner_ref='" . $studentId . "'";
//                    echo $queryUP;
                    $DB->execute($queryUP);
                }
            }
        }

        echo 's1check: ' . $s1check;
        if (($s1check == 1) && ($s2check == 1) && ($s3check == 1)) {
            echo 'all criteria completed';

            // check if an email has already been sent
            $queryCheck = "SELECT semail FROM mdl_passport WHERE learner_ref='" . $studentId . "'";
            $resultCheck = $DB->get_records_sql($queryCheck);
//            echo $queryCheck;

            while ($row = mysql_fetch_assoc($resultCheck)) {
                if (($row['semail'] != 1) OR (empty($row['semail']))) {
                    echo 'semail is ' . $row['semail'] . 'sending email';
                    sendemail('silver', 'jobshop', $learnerid, $JobEmail);
                    $queryUP = "UPDATE mdl_passport set scomp='1', semail='1' WHERE learner_ref='" . $studentId . "'";
//                    echo $queryUP;
                    $DB->execute($queryUP);
                }
            }
        }

        echo 'g1check: ' . $g1check;
        if (($g1check == 1) && ($g2check == 1) && ($g3check == 1)) {
            echo 'all criteria completed';

            // check if an email has already been sent
            $queryCheck = "SELECT gemail FROM mdl_passport WHERE learner_ref='" . $studentId . "'";
            $resultCheck = $DB->get_records_sql($queryCheck);
//            echo $queryCheck;

            while ($row = mysql_fetch_assoc($resultCheck)) {
                if (($row['gemail'] != 1) OR (empty($row['gemail']))) {
                    echo 'gemail is ' . $row['gemail'] . 'sending email';
                    sendemail('gold', 'jobshop', $learnerid, $JobEmail);
                    $queryUP = "UPDATE mdl_passport set gcomp='1', gemail='1' WHERE learner_ref='" . $studentId . "'";
//                    echo $queryUP;
                    $DB->execute($queryUP);
                }
            }
        }


    }

    echo '<h3>Processing target for ' . $box . '</h3><br/>';

    $count = $count + 1;
}

echo '<br/>Target set for ' . $count . ' students';
//echo 'groupid' . $_GET['groupid'];
//       echo '<meta http-equiv="refresh" content="0;url=/blocks/group_targets/view2.php?group=' . $_POST['groupid']  . '">';
$backurl = $CFG->wwwroot . "/blocks/group_targets/view2.php?courseid='" . $courseid . "'";
echo $backurl;
redirect($backurl, 'MTG Saved Sending you back the group target setter', '0');

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

function updateStatus($status, $student_id, $setbyuserid)
{
    global $DB;
    $table = 'block_ilp_user_status';
    $result = $DB->get_records($table, array('user_id' => $student_id));

    // build the dataobject
    foreach ($result as $item) {

        echo 'id: ' . $item->id;
        $record = new stdClass();
        $record->id = $item->id;
        $record->user_modified_id = $setbyuserid;
        $record->parent_id = $status;
        $record->timemodified = time();
        // update the database
        $DB->update_record($table, $record);

    }
}

?>