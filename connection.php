<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$server = '10.0.100.70';

$link = mssql_connect($server, 'plp', 'XXXXX');


if (!$link) {
    die('something went wrong with the connecting to Correo mssql database');
}


//select the database to use
//$select = mssql_select_db('NGReports');
$select = mssql_select_db('NG');

?>
