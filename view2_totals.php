<!-- graphs and numbers -->
<div id="totals" xmlns="http://www.w3.org/1999/html">
    <h1>Totals</h1>

<?php if ($targetSet == 1) { ?>

    <table style="text-align: center;  margin-left: auto; margin-right: auto;" class="totals">
        <tr>
            <th>Active Targets</th>
            <th>Targets Achieved</th>
            <th>Targets Withdrawn</th>
        </tr>
        <td><?php echo $activeTargets; ?></td>
        <td><?php echo $targetsAchieved; ?></td>
        <td><?php echo $targetsWithdrawn; ?></td>
        </tr>
    </table>

    <?php
}

if ($reportsSet == 1) {
    ?>
    <table style="text-align: center;  margin-left: auto; margin-right: auto;" class="totals">
        <tr>
            <?php foreach ($reportsArray as $reports => $reportsItem) {
            ?>

            <th colspan='2'><?php echo $reportsItem; ?></th>

            <?php } ?>
        </tr>

        <tr>
            <?php foreach ($reportsArray as $reports => $reportsItem) {
            ?>

            <th>Students with <?php echo $reportsItem; ?></th>
            <th>Total <?php echo $reportsItem; ?></th>
            <?php } ?>
        </tr>
        <tr>
            <?php foreach ($reportsArray as $reports => $reportsItem) {
            ?>
            <td><?php echo ${'studentsWith' . $reportsItem}; ?></td>
            <td><?php echo ${'total' . $reportsItem}; ?></td>
            <?php } ?>
        </tr>
    </table>

<?php } ?>