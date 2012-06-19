<div id="medals_div">

    <?php
    $badgeCount == 0;
//    mysql_select_db('medals') or die('Unable to select the database');
    $querymedals = "SELECT id, name, icon, description, category FROM mdl_badges";
    $resultsBadges = $DB->get_records_sql($querymedals);

    $num_rows = count($resultsBadges);
    //echo 'num rows: ' . $num_rows;
    echo '<h3>Select Badges</h3>';
//        echo '**Warning the student must have manual mtg set on the flightplan for medals to work**';
    echo '<table>';
    foreach ($resultsBadges as $row) {

        if ($badgeCount == 0) {
            echo '<tr><td>' . $row->name . ' ';
            echo '</td><td><img src="' . $CFG->wwwroot . '/blocks/ilp/custom/pix/badges/' . $row->icon . '.png"/></td>';
            echo '<td>';
            echo '<input type="radio" name="medal" value="' . $row->id . '"   />';
            echo '</td>';

        } else {
            echo '<td width="20px"></td><td>' . $row->name . ' ';
            echo '</td><td><img src="' . $CFG->wwwroot . '/blocks/ilp/custom/pix/badges/' . $row->icon . '.png"/></td>';

            echo '<td>';
            //<input type="checkbox" id="checkbox_medal" name="checkbox_medal[]" value="' . $row['id'] . '" />';
            echo '<input type="radio" name="medal" value="' . $row->id . '"   />';
            echo '</td></tr>';
        }
        //        echo 'badge count is ' . $badgeCount;
        if ($badgeCount == 0) {
            $badgeCount = 1;
        } elseif ($badgeCount == 1) {
            $badgeCount = 0;
        }
    }
    echo '</table>';
    ?>
</div>