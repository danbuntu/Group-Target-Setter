<?php
/**
 * This class provides a mform that previews the entry form
 *
 * @copyright &copy; 2011 University of London Computer Centre
 * @author http://www.ulcc.ac.uk, http://moodle.ulcc.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package ILP
 * @version 2.0
 */

class report_entry_mform extends ilp_moodleform {


    public 		$course_id;
    public		$report_id;
    public 		$user_id;
    public		$dbc;

    /**
     * TODO comment this
     */
    function __construct($report_id,$user_id,$entry_id=null,$course_id=null, $students) {

        global $CFG;

        $this->course_id	=	$course_id;
        $this->report_id	=	$report_id;
        $this->user_id		=	$user_id;
        $this->entry_id		=	$entry_id;

        $this->dbc			=	new ilp_db();

//        print_r($this);

//        echo ' dbc ';
//        print_r($this->dbc);

        $query_string	=	"?report_id={$report_id}&amp;user_id={$user_id}";

//        echo 'query string is: ' . $query_string;

        if (!empty($entry_id)) $query_string	.= "&amp;entry_id={$entry_id}";
        if (!empty($course_id)) $query_string	.= "&amp;course_id={$course_id}";

//         echo 'students ' . $students;
        // call the parent constructor  and pass it the report id variable to get latter
        parent::__construct("{$CFG->wwwroot}/blocks/group_targets/reports_forms.php?report_id=" . $report_id . "&students=" . $students);
    }


    /**
     * TODO comment this
     */
    function definition() {
        global $USER, $CFG;

        // include the assmgr db
        require_once($CFG->dirroot.'/blocks/ilp/db/ilp_db.php');

        $dbc = new ilp_db;

        $mform =& $this->_form;

        //get all of the fields in the current report, they will be returned in order as
        //no position has been specified
        $reportfields		=	$this->dbc->get_report_fields_by_position($this->report_id);

        $report				=	$this->dbc->get_report_by_id($this->report_id);
        $user				=	$this->dbc->get_user_by_id($this->user_id);



        //create a new fieldset
//        $mform->addElement('html', '<fieldset id="reportfieldset" class="clearfix ilpfieldset">');
//        $mform->addElement('html', '<legend class="ftoggler">'.$title.'</legend>');

        $desc	=	html_entity_decode($report->description);

        $mform->addElement('html', '<div class="descritivetext">'.$desc.'</div>');

        $mform->addElement('hidden', 'entry_id',$this->entry_id);
        $mform->setType('entry_id', PARAM_INT);

        $mform->addElement('hidden', 'report_id',$this->report_id);
        $mform->setType('report_id', PARAM_INT);

        $mform->addElement('hidden', 'user_id',$this->user_id);
        $mform->setType('user_id', PARAM_INT);

        $mform->addElement('hidden', 'course_id',$this->course_id);
        $mform->setType('course_id', PARAM_INT);

        if (!empty($reportfields)) {

            foreach ($reportfields as $field) {

                //get the plugin record that for the plugin
                $pluginrecord	=	$dbc->get_plugin_by_id($field->plugin_id);

                //take the name field from the plugin as it will be used to call the instantiate the plugin class
                $classname = $pluginrecord->name;

                // include the class for the plugin
                include_once("{$CFG->dirroot}/blocks/ilp/classes/form_elements/plugins/{$classname}.php");

                if(!class_exists($classname)) {
                    print_error('noclassforplugin', 'block_ilp', '', $pluginrecord->name);
                }

                //instantiate the plugin class
                $pluginclass	=	new $classname();

                $pluginclass->load($field->id);

                //call the plugins entry_form function which will add an instance of the plugin
                //to the form
                $pluginclass->entry_form($mform);
            }

        }

        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'));
//        $buttonarray[] = &$mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

        //close the fieldset
        $mform->addElement('html', '</fieldset>');
    }


    /**
     * TODO comment this
     */
    function process_data($data, $students) {


        $studentArray = unserialize($students);


        echo 'student array: ';
           print_r($students);



        // add the loop here for multiple users from the session?

       foreach ($studentArray as $student) {


         echo 'processing';

        global	$CFG,$USER;

        //no need to process data as this is just a preview of the final form

        //get the id of the report
        $report_id	=	$data->report_id;

        //get the id of the entry  if known
        $entry_id	=	$data->entry_id;



        //get the id of the user
        $user_id	=	$data->user_id;

        //get the id of the course
        $course_id	=	$data->course_id;
        $result = true;

        if (empty($entry_id)) {
            //create the entry
            $entry					=	new stdClass();
            $entry->report_id		=	$report_id;
            $entry->creator_id		=	$USER->id;
            $entry->user_id			=	$student;
            //TODO: do we need to save course ?
            //$entry->course

            $entry_id	=	$this->dbc->create_entry($entry);

        } else {
            //update the entry
            //as there is nothing to update but we want the entries timemodifed
            //to be updated we will just re-add the report_id
            $entry					=	new stdClass();
            $entry->id				=	$entry_id;
            $entry->report_id		=	$report_id;
            if (!$this->dbc->update_entry($entry)) $result = false;

        }


        //get all of the fields in the current report, they will be returned in order as
        //no position has been specified
        $reportfields		=	$this->dbc->get_report_fields_by_position($report_id);

        foreach ($reportfields as $field) {

            //get the plugin record that for the plugin
            $pluginrecord	=	$this->dbc->get_plugin_by_id($field->plugin_id);

            //take the name field from the plugin as it will be used to call the instantiate the plugin class
            $classname = $pluginrecord->name;

            // include the class for the plugin
            include_once("{$CFG->dirroot}/blocks/ilp/classes/form_elements/plugins/{$classname}.php");

            if(!class_exists($classname)) {
                print_error('noclassforplugin', 'block_ilp', '', $pluginrecord->name);
            }

            //instantiate the plugin class
            $pluginclass	=	new $classname();

            $pluginclass->load($field->id);

            //call the plugins entry_form function which will add an instance of the plugin
            //to the form
            if ($pluginclass->is_processable())	{
                if (!$pluginclass->entry_process_data($field->id,$entry_id,$data)) $result = false;
            }
        }


       }
        return $result;

    }

    /**
     * TODO comment this
     */
    function definition_after_data() {

    }





}
