<?php

// Based on a hacked around version of blocks/ilp/classes/dashboard/tabs/ilp_dashboard_reports_tab
// Mostly commenting out sections that aren't used and removing the extends part of the class declaration to stop errors

//require the ilp_plugin.php class
require_once($CFG->dirroot . '/blocks/ilp/db/ilp_db.php');
require_once($CFG->dirroot . '/blocks/ilp/classes/dashboard/ilp_dashboard_tab.php');

class get_targets
{

    public $student_id;
    public $filepath;
    public $linkurl;
    public $selectedtab;
    public $role_ids;
    public $capability;


    function __construct($student_id = null, $course_id = null)
    {
        global $CFG, $USER, $PAGE;

        //$this->linkurl				=	$CFG->wwwroot.$_SERVER["SCRIPT_NAME"]."?user_id=".$student_id."&course_id={$course_id}";

        $this->linkurl = $CFG->wwwroot . "/blocks/ilp/actions/view_main.php?user_id=" . $student_id . "&course_id={$course_id}";

        $this->student_id = $student_id;

        $this->course_id = $course_id;

        $this->selectedtab = false;

    }


    function display($report_id, $state_id)
    {
        global $CFG, $PAGE, $USER, $OUTPUT, $PARSER;
        $dbc = new ilp_db();
        $pluginoutput = "";
//        echo 'test in reports ' . $this->testing;
        if ($dbc->get_user_by_id($this->student_id)) {

            ob_start();


            if ($report = $dbc->get_report_by_id($report_id)) {

                if ($report->status == ILP_ENABLED) {

                    $icon = (!empty($report->binary_icon)) ? $CFG->wwwroot . "/blocks/ilp/iconfile.php?report_id=" . $report->id : $CFG->wwwroot . "/blocks/ilp/pix/icons/defaultreport.gif";


                    $reportname = $report->name;
                    //get all of the fields in the current report, they will be returned in order as
                    //no position has been specified
                    $reportfields = $dbc->get_report_fields_by_position($report_id);

                    $reporticon = (!empty($report->iconfile)) ? '' : '';


                    //does this report give user the ability to add comments
                    $has_comments = (!empty($report->comments)) ? true : false;

                    //this will hold the ids of fields that we dont want to display
                    $dontdisplay = array();


                    //does this report allow users to say it is related to a particular course
                    $has_courserelated = (!$dbc->has_plugin_field($report_id, 'ilp_element_plugin_course')) ? false : true;

                    if (!empty($has_courserelated)) {
                        $courserelated = $dbc->has_plugin_field($report_id, 'ilp_element_plugin_course');
                        //the should not be anymore than one of these fields in a report
                        foreach ($courserelated as $cr) {
                            $dontdisplay[] = $cr->id;
                            $courserelatedfield_id = $cr->id;
                        }
                    }


                    //get all of the users roles in the current context and save the id of the roles into
                    //an array
                    $role_ids = array();

                    $authuserrole = $dbc->get_role_by_name(ILP_AUTH_USER_ROLE);
                    if (!empty($authuserrole)) $role_ids[] = $authuserrole->id;

                    if ($roles = get_user_roles($PAGE->context, $USER->id)) {
                        foreach ($roles as $role) {
                            $role_ids[] = $role->roleid;
                        }
                    }

                    // blank out the state id if not filter is selected to make it display all targets
                    if ($state_id == 0) {
                        $state_id = '';
                    }
                    //get all of the entries for this report
                    $reportentries = $dbc->get_user_report_entries($report_id, $this->student_id, $state_id);

                    //create the entries list var that will hold the entry information
                    $entrieslist = array();


                    if (!empty($reportentries)) {
                        foreach ($reportentries as $entry) {


                            // output a hidden field for with the report id in - used to process any changes to the report



                            //TODO: is there a better way of doing this?
                            //I am currently looping through each of the fields in the report and get the data for it
                            //by using the plugin class. I do this for two reasons it may lock the database for less time then
                            //making a large sql query and 2 it will also allow for plugins which return multiple values. However
                            //I am not naive enough to think there is not a better way!

                            $entry_data = new stdClass();

                            //get the creator of the entry
                            $creator = $dbc->get_user_by_id($entry->creator_id);

                            //get comments for this entry
                            $comments = $dbc->get_entry_comments($entry->id);

                            //
                            $entry_data->creator = (!empty($creator)) ? fullname($creator) : get_string('notfound', 'block_ilp');
                            $entry_data->created = userdate($entry->timecreated);
                            $entry_data->modified = userdate($entry->timemodified);
                            $entry_data->user_id = $entry->user_id;
                            $entry_data->entry_id = $entry->id;

                            if ($has_courserelated) {
                                $coursename = false;
                                $crfield = $dbc->get_report_coursefield($entry->id, $courserelatedfield_id);
                                if (empty($crfield) || empty($crfield->value)) {
                                    $coursename = get_string('allcourses', 'block_ilp');
                                } else if ($crfield->value == '-1') {
                                    $coursename = get_string('personal', 'block_ilp');
                                } else {
                                    $crc = $dbc->get_course_by_id($crfield->value);
                                    if (!empty($crc)) $coursename = $crc->shortname;
                                }
                                $entry_data->coursename = (!empty($coursename)) ? $coursename : '';
                            }


                            foreach ($reportfields as $field) {

                                //get the plugin record that for the plugin
                                $pluginrecord = $dbc->get_plugin_by_id($field->plugin_id);

                                //take the name field from the plugin as it will be used to call the instantiate the plugin class
                                $classname = $pluginrecord->name;

//                                echo $classname;

                                // include the class for the plugin
                                include_once("{$CFG->dirroot}/blocks/ilp/classes/form_elements/plugins/{$classname}.php");

                                if (!class_exists($classname)) {
                                    print_error('noclassforplugin', 'block_ilp', '', $pluginrecord->name);
                                }

                                //instantiate the plugin class
                                $pluginclass = new $classname();

                                if ($pluginclass->is_viewable() != false) {
                                    $pluginclass->load($field->id);

                                    //call the plugin class entry data method
                                    $pluginclass->view_data($field->id, $entry->id, $entry_data);
                                } else {
                                    $dontdisplay[] = $field->id;
                                }

                            }

                            include($CFG->dirroot . '/blocks/group_targets/target_display_deadline_check.php');
                            echo '<div class="alert alert-success reports-container-alert"><i class="icon-upload"> </i>  Select this target: <input type="checkbox" name="report[]" class="checkbox" id="report" value="' .  $entry->id . '"/></div>';

                        }
                    } else {

                        echo get_string('nothingtodisplay');

                    }

                }
                //end new if

            }

            $pluginoutput = ob_get_contents();

            ob_end_clean();

        } else {
            $pluginoutput = get_string('studentnotfound', 'block_ilp');
        }


        return $pluginoutput;
    }


    /**
     * Adds the string values from the tab to the language file
     *
     * @param    array &$string the language strings array passed by reference so we
     * just need to simply add the plugins entries on to it
     */
    function language_strings(&$string)
    {
        $string['ilp_dashboard_reports_tab'] = 'entries tab';
        $string['ilp_dashboard_reports_tab_name'] = 'Reports';
        $string['ilp_dashboard_entries_tab_overview'] = 'Overview';
        $string['ilp_dashboard_entries_tab_lastupdate'] = 'Last Update';
        $string['ilp_dashboard_reports_tab_default'] = 'Default report';

        return $string;
    }

}
