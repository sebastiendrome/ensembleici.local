<?php
include "config_pear.php";
require_once('../../../01_include/_connect.php');
$file_denvoi_de_mail =& new Mail_Queue($db_options, $mail_options);
echo $file_denvoi_de_mail->getQueueCount();
?>