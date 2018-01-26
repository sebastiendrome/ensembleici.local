<?php
if(isset($_POST["nb"])&&$_POST["nb"]!=""&&$_POST["nb"]!=0){
	include "config_pear.php";
	require_once('../../../01_include/_connect.php');
	$file_denvoi_de_mail =& new Mail_Queue($db_options, $mail_options);
	echo ($_POST["nb"]-$file_denvoi_de_mail->getQueueCount());
}
else{
	echo "false";
}
?>