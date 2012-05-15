<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dattwood
 * Date: 04/05/11
 * Time: 13:10
 * To change this template use File | Settings | File Templates
 * Process the selected text message and send it to the selected students
 */

require_once("../../config.php");

$message = $_POST['comments'];
$checkboxes = $_POST['checkbox'];
$url2 = $_POST['url'];

//echo $url2;
//echo '<h1> Sending the message/ messages</h1>';

//echo $message;
//print_r($checkboxes);
$pattern = '/\{|\}/';


// Test the message to see if it still contain sections that need to be edited - marked with {}

preg_match($pattern, $message);

//print_r($out);


if (preg_match($pattern, $message)) {
    echo 'matches found';
    echo '<br/>';
    echo '<h2>You still have parts of the message that need to be altered. Please try again</h2>';
    echo '<meta http-equiv="refresh" content="2;url=' . $url . '">';
} else {
//    echo 'no problems. Sending your messages';


    // Get the mobile numbers for the students

    foreach ($checkboxes as $box) {


        $query = 'SELECT * FROM mdl_user WHERE id="' . $box . '"';
//        echo $query;
        $result = mysql_query($query);

        while ($row = mysql_fetch_assoc($result)) {
//            echo 'firstname is: ' . $row["firstname"];
//            echo ' mobile is: ' . $row['phone2'];
            $number = '+44' . substr($row['phone2'], 1);
//            echo ' number to send is: ' . $number;

            //send out the actual mesages

            //echo $message;

            $xml = "<?xml version='1.0' ?>
        <Request>
        <Authentication>
        <Username><![CDATA[MidKC_Admin]]></Username>
        <Password><![CDATA[axwn2gwc]]></Password>
        </Authentication>
        <Message>
        <MessageText><![CDATA[" . $message . "]]></MessageText>
        <Phone><![CDATA[" . urlencode($number) . "]]></Phone>
        <Type>1</Type>
        <MessageDate>1234567890</MessageDate>
        <UniqueID><![CDATA[Just an ID]]></UniqueID>
        <From><![CDATA[Dattwood]]></From>
        </Message>
        </Request>";

            //
//            header("Content-type: text/xml");
            //        echo $xml;
            $url = 'http://www.txttools.co.uk/connectors/XML/xml.jsp';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "XMLPOST=$xml");

            $data = curl_exec($ch);
            curl_close($ch);
            //        if(curl_errno($ch))
            //            print curl_error($ch);
            //        else
            //            curl_close($ch);
            //        //
        }
    }

//    exit;
echo '<table style="margin-left: auto; margin-right: auto;"><tr><td style="text-align: center;">';
   echo '<h3>Messages Sent</h3>';
//echo $url2;
//    echo '<meta http-equiv="Refresh" content="10;URL="' . $url . '/>';
//echo $url2;
    echo '<a href="' . $url2 . '">Click here to go back to the group target page</a>';
echo '</td></tr></table>';


}


?>