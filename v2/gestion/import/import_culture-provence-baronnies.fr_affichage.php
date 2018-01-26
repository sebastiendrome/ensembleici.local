<?php
session_name("EspacePerso");
session_start();
require_once "config.php";
ini_set('soap.wsdl_cache_enabled', 0);

echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />
';
try {
	$client = new SoapClient('http://www.culture-provence-baronnies.fr/websrv_events.wsdl', array('encoding'=>'UTF-8'));
	$oReturn =  $client ->getEvents("africultures","soap!2605");
	if(is_string($oReturn)){
		echo $oReturn;
	}else{
		foreach($oReturn->Array as $ligne){
			echo "ID unique SMBP: ".$ligne->string[0]."<br />";
			echo "Titre: ".$ligne->string[1]."<br />";
			echo "Lien site web: ".$ligne->string[2]."<br />";
			echo "Description: ".$ligne->string[3]."<br />";
			echo "Réservation obligatoire: ".$ligne->string[4]."<br />";
			echo "Tel. reservation: ".$ligne->string[5]."<br />";
			echo "Tarif: ".$ligne->string[6]."<br />";
			echo "Date debut: ".$ligne->string[7]."<br />";
			echo "Date fin: ".$ligne->string[8]."<br />";
			echo "Adr1: ".$ligne->string[9]."<br />";
			echo "Adr2: ".$ligne->string[10]."<br />";
			echo "Adr3: ".$ligne->string[11]."<br />";
			echo "Code postal: ".$ligne->string[12]."<br />";
			echo "Commune: ".$ligne->string[13]."<br />";
			echo "Latitude: ".$ligne->string[14]."<br />";
			echo "Longitude: ".$ligne->string[15]."<br />";
			echo "Piece jointe: ".$ligne->string[16]."<br />";
			echo "Image: ".$ligne->string[17]."<br />------------------------<br />";
		}
	}
} catch (SoapFault $fault) {
    echo 'Erreur : '.$fault;
}
?>