<?php
 /* Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 27/04/11
 * Time: 14:26
 * Loads the form to allow the sending of texts to students
 */

?>
<div id="multiOpenAccordion">
    <?php accord_first('Send a Text message');

    // Filter for messages
    // Get the Messages
    $query = "SELECT DISTINCT type FROM moodle.txts";
    $result = mysql_query($query);

    echo '<select name="message_type" id="message_type" class="message_type">';
    echo '<option selected="selected">--Select Type--</option>';
    while ($row = mysql_fetch_assoc($result)) {

        $message_type = $row['type'];
        echo '<option value="' . $message_type . '">' . $message_type . '</option>';
    }
    echo '</select>';

    // Get the Messages
//    $query = "SELECT * FROM moodle.txts WHERE type=" . $type . "";

//  $result = mysql_query($query);
    echo '</br>';

    echo '<select name="message" id="message" class="message">';
    echo '<option selected="selected">--Select Message--</option>';
//  while ($row = mysql_fetch_assoc($result)) {
//        echo '<option ' . $row['idtxts'] . '>' . $row["message"] . '</option>';
//  }
    echo '</select>';
    echo '</br>';
    echo '<input type="submit" name="submit" value="send txt"" />';


    accord_last(); ?>
</div>
