<?php
// check if there is data sent through GET, with key "pag"
// effectively acts as a shim to catch the report type and allow the correct form to be shown and the ilp v2 to be passed the correct checkbox data

//print_r($_POST);

include('settings.php');
include('top_include.php');
topbar('Group Target Setter');

require_once("../../config.php");

global $USER, $CFG, $SESSION, $PARSER, $PAGE;

?>

<div class="span12">

    <?php
    if ($_POST['reports'] == '--Select a report--') {
        echo '<h3><font color= red>No type set please set some - redirecting you back to the setting page</font></h3>';
        echo '<meta http-equiv="refresh" content="2;url=/blocks/group_targets/view2.php">';
        exit;
    } elseif (empty($_POST['checkbox'])) {
        echo '<h3><font color= red>No students selected you must select some</font></h3>';
        echo '<meta http-equiv="refresh" content="2;url=/blocks/group_targets/view2.php">';
        exit;
    } else {

        ?>

        <a class="btn btn-primary" href="<?php $CFG->wwwroot; ?>/blocks/group_targets/view2.php"><i
            class="icon-chevron-left icon-white"></i> Back to the target setter grid</a>
        <h1>Enter your Report</h1>

        <?php

        if (isset($_POST['reports'])) {

//            echo $_POST['reports'];
            $report_id = $_POST['reports'];


            if ($_POST['reports'] == 'badges') {
                formheader();
                include('badges.php');
                formfooter($_POST['checkbox'], 'badges');

            } elseif ($_POST['reports'] == 'passport') {
                formheader();
                include('employability.php');
                formfooter($_POST['checkbox'], 'Employability Passport');

            } elseif ($_POST['reports'] == 'rag') {
                formheader();
                include('rag.php');
                formfooter($_POST['checkbox'], 'rag');
            } else {

                $report_id = $_POST['reports'];

                $students = serialize($_POST['checkbox']);

                include('reports_forms.php');

            }

        } else {
            // if no $_GET with key "pag", returns a error message
            echo 'Error: Invalid data';
        }
        ?>


        <?php } ?>
</div>

<?php
function formheader()
{
    echo '<div class="span10">';
    echo '<form name="process" method="POST" action="process_targets2.php" >';
}

function formfooter($studentsIn, $type)
{
    $students = urlencode(serialize($studentsIn));
//    echo $students;
    echo '<input type="hidden" name="checkbox" id="checkbox" value="', $students . '">';
    echo '<input type="hidden" name="type" id="type" value="' . $type . '">';
    echo '<input type="hidden" name="userid" value="', $USER->id, '"/>';
    echo '<input type="submit" value="Submit">';
    echo '</form>';
    echo '</div>';
}

?>