<?php

class block_group_targets extends block_base {

    function init() {
        $this->title = get_string('plp_group_targets', 'block_group_targets');
        $this->version = 2004111200;
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {
        global $CFG, $USER, $SITE, $COURSE;
        if ($this->content !== NULL) {
            return $this->content;
        }

//get the course context id - probably not needed

//        print_r($COURSE);

        $courseid = $COURSE->id;
//        echo 'course id' , $courseid;
//        $context = $COURSE->context->id;

// get the proper context 
       $context2 = get_context_instance(CONTEXT_COURSE, $courseid);

//only display the block if the users is a teacher - ie can update the course
        if (has_capability('moodle/course:update', $context2)) {


            $url = $CFG->wwwroot . '/blocks/group_targets/view2.php?courseid=' . $courseid . '&var1=' . $context2->id . '';

            $this->content = new stdClass;
            $this->content->text = '<div style="text-align: center;"><a href="' . $url . '">Update multiple PLPS';
            $this->content->text .= '<img src="' . $CFG->wwwroot . '/blocks/group_targets/images/User-Group-icon.png" width="76" height="76" align=center alt="User-Group-icon"/></a></div>';

            return $this->content;
        } else {
            return $this->content = new stdClass;
            $this->content->text = 'test';
        }
    }

}

// Here's the closing curly bracket for the class definition
// and here's the closing PHP tag from the section above.
?>