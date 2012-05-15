<?php
//echo 'test';
$mysqli = new mysqli('localhost', 'root', '88Boom!', 'XXXXXXXX');

if ($mysqli->errno) {
    echo 'error connecting' . $mysqli->error;
}

?>