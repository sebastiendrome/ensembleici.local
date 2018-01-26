<?php
/**
Ce fichier met à jour les options de suivis de l'envoi de la lettre no
**/
require_once('../../../01_include/_connect.php');
if(isset($_POST["no"])&&$_POST["no"]!=null&&$_POST["no"]!=""&&$_POST["no"]!=0){
	//On récupère no_envoi qui correspond à no_lettre
	$requete_lettre = "SELECT no_envoi FROM lettreinfo WHERE no=:no";
	$res_lettre = $connexion->prepare($requete_lettre);
	$res_lettre->execute(array(":no"=>$no)) or die("erreur requête ligne 116 : ".$requete_lettre);
	$tab_lettre = $res_lettre->fetchAll();
	$no_envoi = $tab_lettre[0]["no_envoi"];
	//On modifie la ligne qui correspond à no_envoi
	$requete_update = "UPDATE lettreinfo_envoi SET adresse_suivi=:a,mail_suivi=:m_s,mail_fin=:m_f,seconde_suivi=:s_s WHERE no=:no";
	$res_update = $connexion->prepare($requete_update);
	$res_update->execute(array(":no"=>$no_envoi,":a"=>$_POST["a"],":m_s"=>$_POST["m_s"],":m_f"=>$_POST["m_f"],":s_s"=>$_POST["s_s"])) or die("erreur requête ligne 116 : ".$requete_update);
	$reponse = array(true,utf8_encode("options de suivi enregistrées"));
}
else{
	$reponse = array(false,utf8_encode("une erreur est survenue !"));
}
echo json_encode($reponse);
?>