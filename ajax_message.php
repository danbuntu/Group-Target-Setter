<?php
 /* Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 27/04/11
 * Time: 14:26
 * ajax part to load in the messages based on the selected message type
  * Taken from http://www.9lessons.info/2010/08/dynamic-dependent-select-box-using.html */

require_once("../../config.php");
if($_POST["message_type"])
{
$id=$_POST["message_type"];
$sql=mysql_query("select * from txts where type='$id'");
while($row=mysql_fetch_array($sql))
{
$id=$row['idtxts'];
$data=$row['message'];
echo '<option value="'.$id.'">'.$data.'</option>';
}
}
?>