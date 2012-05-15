<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of accord_functions
 *
 * @author dattwood
 */
// functions to use to setup an accordian menu and keep the code tidy
function accord_first($title) {

    echo '<h3 class="ui-widget-header"><a href="javascript:void()">' .$title . '</a></h3>';
       echo '<div>';

}

function accord_open($title) {
    echo '<dl class="accordion">';
    echo '<dt class="selected">' . $title . '</dt>';
    echo '<dd class="open">';
    echo '<div class="bd">';
}

function accord_start($title) {
    print "<dt>$title</dt>";
    echo '<dd class="open">';
    echo '<div class="bd">';
}

function accord_end() {
    echo '</div></dd>';
}

function accord_last() {
    echo '</div>';
}

?>