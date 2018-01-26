<?php
include "config_pear.php";
require_once('../../../01_include/_connect.php');
//On créait une file d'attente
$file_denvoi_de_mail =& new Mail_Queue($db_options, $mail_options);
//Dans un premier temps on vide la liste
while ($mail = $file_denvoi_de_mail->get()){
	$result = $file_denvoi_de_mail->deleteMail($mail->getId());
}
$delete = "TRUNCATE TABLE `mail_queue_insert`";
$res = $connexion->prepare($delete);
$res->execute();
?>