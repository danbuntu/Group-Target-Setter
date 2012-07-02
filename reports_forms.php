<?php

// copied from the ilp block
//require the reportentry_mform so we can display the report
//echo ' rep id 1 ', $report_id;
require_once("../../config.php");
require_once($CFG->dirroot . '/blocks/ilp/db/ilp_db.php');
require_once($CFG->dirroot . '/blocks/ilp/classes/ilp_formslib.class.php');
require_once($CFG->dirroot . '/blocks/group_targets/reportentry_mform_groups.php');

?>
<link rel="stylesheet" type="text/css"
      href="<?php $CFG->wwwroot; ?>/theme/styles_debug.php?theme=aardvark&amp;type=parent&amp;subtype=base&amp;sheet=core"/>
<?php

global $USER, $CFG, $SESSION, $PARSER, $PAGE;

//if set get the id of the report
//$report_id	= $PARSER->required_param('report_id',PARAM_INT);

// Meta includes
require_once($CFG->dirroot . '/blocks/ilp/actions_includes.php');

// Include the report permissions file
//require_once($CFG->dirroot.'/blocks/ilp/report_permissions.php');

//$PAGE->set_context();


//echo  ' this: ' . $this->report_id;

if (isset($_GET['report_id'])) {
    $report_id = $_GET['report_id'];
//      echo 'report assinged ' , $report_id;
}

if (isset($_GET['students'])) {
    $students = $_GET['students'];
//    echo 'students assinged ' , $students;
}



//$user_id = '12642';
//$course_id = '1117';
$entry_id = '';


//$PAGE->set_title($SITE->fullname." : ".get_string('blockname','block_ilp')." : ".fullname($plpuser));
//$PAGE->set_heading($SITE->fullname);
//$PAGE->set_pagetype('ilp-entry');
//$PAGE->set_pagelayout('ilp');
//$PAGE->set_url($CFG->wwwroot."/blocks/ilp/actions/edit_reportentry.php",$PARSER->get_params());


// setup the page title and heading
//$SITE	=	$dbc->get_course_by_id(SITEID);
//$PAGE->set_title($SITE->fullname." : ".get_string('blockname','block_ilp')." : ".fullname($plpuser));
//$PAGE->set_heading($SITE->fullname);
//$PAGE->set_pagetype('ilp-entry');
////$PAGE->set_pagelayout('ilp');
//$PAGE->set_url($CFG->wwwroot."/blocks/ilp/actions/edit_reportentry.php",$PARSER->get_params());


// @FIXME this needs to be done on the Moodle way and not hardcoded to the theme  also fix date picker
?>
<!--<link rel="stylesheet" type="text/css" href="--><?php //$CFG->wwwroot; ?><!--/theme/yui_combo.php?3.4.1/build/cssreset/reset.css&amp;3.4.1/build/cssfonts/fonts.css&amp;3.4.1/build/cssgrids/grids.css&amp;3.4.1/build/cssbase/base.css" />-->
<!--<script type="text/javascript" src="--><?php //$CFG->wwwroot; ?><!--/lib/yui/3.4.1/build/yui/yui.js"></script>-->
<!--<script type="text/javascript" src="--><?php //$CFG->wwwroot; ?><!--/theme/yui_combo.php?2.9.0/build/yahoo/yahoo.js&amp;2.9.0/build/event/event.js&amp;2.9.0/build/connection/connection.js&amp;2.9.0/build/dom/dom.js&amp;2.9.0/build/logger/logger.js"></script>-->
<!--<link rel="stylesheet" type="text/css" href="--><?php //$CFG->wwwroot; ?><!--/theme/yui_combo.php?2.9.0/build/assets/skins/sam/skin.css" />-->


<?php

// instantiate the db
$dbc = new ilp_db();

////get the report
$report = $dbc->get_report_by_id($report_id);

//echo '$report variable ' ;


//
//if the report is not found throw an error of if the report has a status of disabled
if (empty($report) || empty($report->status) || !empty($report->deleted)) {
    print_error('reportnotfouund', 'block_ilp');
}


//check if the any of the users roles in the
//current context has the create report capability for this report

//if (empty($access_report_createreports))	{
//    //the user doesnt have the capability to create this type of report entry
//
//    print_error('userdoesnothavecreatecapability','block_ilp');
//}
//
//
//if (!empty($entry_id))	{
//    if (empty($access_report_editreports))	{
//        //the user doesnt have the capability to edit this type of report entry
//
//        print_error('userdoesnothavedeletecapability','block_ilp');
//    }
//}


$reportfields = $dbc->get_report_fields_by_position($report_id);

//we will only attempt to display a report if there are elements in the
//form. if not we will send the user back to the dashboard
//if (empty($reportfields)) {
//send the user back to the dashboard page telling them that the report is not ready for display
//    $return_url = $CFG->wwwroot.'/blocks/ilp/actions/view_main.php?user_id='.$user_id.'&course_id='.$course_id;
//    redirect($return_url, get_string("reportnotready", 'block_ilp'), ILP_REDIRECT_DELAY);
//}


// added the students variable here to pass through the students id numbers
$mform = new    report_entry_mform($report_id, $user_id, $entry_id, $course_id, $students);

//was the form cancelled?
//if ($mform->is_cancelled()) {
////send the user back to dashboard
//    $return_url = $CFG->wwwroot.'/blocks/group_targets/view2.php?course_id='.$course_id.'';
//    redirect($return_url, '', ILP_REDIRECT_DELAY);
//}

//was the form submitted?
// has the form been submitted?
if ($mform->is_submitted()) {
    // check the validation rules
//    echo 'form is submitted';

    if ($mform->is_validated()) {

        //get the form data submitted


        $formdata = $mform->get_data();

//       echo '<br>Formdata<br>';
//        print_r($formdata);

        // process the data


        $success = $mform->process_data($formdata, $students);

        //if saving the data was not successful
        if (!$success) {
            //print an error message
            print_error(get_string("entrycreationerror", 'block_ilp'), 'block_ilp');
        }

        if (!isset($formdata->saveanddisplaybutton)) {
            $return_url = $CFG->wwwroot . '/blocks/group_targets/view2.php?course_id=' . $course_id;
            redirect($return_url, 'Group Report Saved', ILP_REDIRECT_DELAY);
        }
    }
}


if (!empty($entry_id)) {

    //create a entry_data object this will hold the data that will be passed to the form
    $entry_data = new stdClass();

    //get the main entry record
    $entry = $dbc->get_entry_by_id($entry_id);

    if (!empty($entry)) {
        //check if the maximum edit field has been set for this report
        if (!empty($report->maxedit)) {
            //calculate the age of the report entry
            $entryage = time() - $entry->timecreated;

            //if the entry is older than the max editing time
            //then return the user to the
            if ($entryage > $CFG->maxeditingtime) {
                $return_url = $CFG->wwwroot . '/blocks/ilp/actions/view_main.php?user_id=' . $user_id . '&course_id=' . $course_id ;
                redirect($return_url, get_string("maxeditexceed", 'block_ilp'), ILP_REDIRECT_DELAY);
            }

        }


        //get all of the fields in the current report, they will be returned in order as
        //no position has been specified
        $reportfields = $dbc->get_report_fields_by_position($report_id);

        foreach ($reportfields as $field) {

            //get the plugin record that for the plugin
            $pluginrecord = $dbc->get_plugin_by_id($field->plugin_id);

            //take the name field from the plugin as it will be used to call the instantiate the plugin class
            $classname = $pluginrecord->name;

            // include the class for the plugin
            include_once("{$CFG->dirroot}/blocks/ilp/classes/form_elements/plugins/{$classname}.php");

            if (!class_exists($classname)) {
                print_error('noclassforplugin', 'block_ilp', '', $pluginrecord->name);
            }

            //instantiate the plugin class
            $pluginclass = new $classname();

            $pluginclass->load($field->id);

            //create the fieldname
            $fieldname = $field->id . "_field";


            $pluginclass->load($field->id);

            //call the plugin class entry data method
            $pluginclass->entry_data($field->id, $entry_id, $entry_data);
        }
        $reportfields = $dbc->get_report_fields_by_position($report_id);

        foreach ($reportfields as $field) {

            //get the plugin record that for the plugin
            $pluginrecord = $dbc->get_plugin_by_id($field->plugin_id);

            //take the name field from the plugin as it will be used to call the instantiate the plugin class
            $classname = $pluginrecord->name;

            // include the class for the plugin
            include_once("{$CFG->dirroot}/blocks/ilp/classes/form_elements/plugins/{$classname}.php");

            if (!class_exists($classname)) {
                print_error('noclassforplugin', 'block_ilp', '', $pluginrecord->name);
            }

            //instantiate the plugin class
            $pluginclass = new $classname();

            $pluginclass->load($field->id);

            //create the fieldname
            $fieldname = $field->id . "_field";


            $pluginclass->load($field->id);

            //call the plugin class entry data method
            $pluginclass->entry_data($field->id, $entry_id, $entry_data);
        }

        //loop through the plugins and get the data for each one
        $mform->set_data($entry_data);
    }
}


//print_r($mform);

// setup the page title and heading
//$PAGE->set_pagelayout('ilp');

//require edit_reportentry html


//render the form


?>

<div class="ilp yui-skin-sam">
    <?php $mform->display(); ?>

</div>
