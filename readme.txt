Group Target Setter Notes - Dan Attwood for Midkent College 2011


Abstract

The Group target setter module expands the ULCC Ilp modules and adds the ability to set targets for all groups in a course, adjust targets set my a tutor and gives an overview of students in a course/ group.

The Block makes heavy use of Jquery (I didn't get on with YUI and as the Moodle docs say - deal with it). The main jquery scrips are called by jquery_imports.php. This could be editied to point to a global jquery install.


Install

copy the group target setter module into the modules folder and do the normal check for Notifications.

The blocks can now be added to course as needed. We add it a sticky block so that it appeared on all courses. If has a check that only displays the block to user that have the ability to edit the course (ie are tutors)


Usages notes

The change target status block will only show targets that have set for a student. The block will also shows targets set for students that have been removed from a course. This is because a student might have been transferred to a different course, but as the target still appears on their PLP it is still counted as relevant. It's up to the tutor to withdraw or close these targets.


Modification- technical details
 
The course report grid uses jquery.datatables which is able to theme an existing html table. Search for '// Begin laying out the table' in view.php will take you the start on this laying out.  The code following it grabs information for each students from a number of different places and begins to format test it for inclusion in the grid.

report_functions.php contained all the functions to create the table. This can be knocked out and changed as needed.



