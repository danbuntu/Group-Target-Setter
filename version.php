<?php
/**
 * @package   Group Target Setter 2.0
 * @author    Dan Attwood <Dan.Attwood@midkent.ac.uk>
 * @author    Nathan Friend <n.friend@cant-col.ac.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2012051800;
$plugin->requires = 2011120500;
$plugin->cron = 0;
$plugin->component = 'plugintype_grouptargetsetter';
$plugin->maturity = MATURITY_ALPHA;
$plugin->release = '2.0 (Build: 2012051800)';

$plugin->dependencies = array('block_ilp' => 2012030104);
?>
 
