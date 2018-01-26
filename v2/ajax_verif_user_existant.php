<?php
// Vérifie que l'utilisateur n'est pas pris (pour changement d'identifiant)
require_once('01_include/_var_ensemble.php');
require_once('01_include/_connect.php');

/* RETURN VALUE */
$validateId=$_GET['fieldId'];
$login=strtolower(trim($_REQUEST['fieldValue']));
$mail_1_verif = strtolower(trim($_REQUEST['extraData']));

$arrayToJs = array();
$arrayToJs[0] = $validateId;

if ($login != $mail_1_verif)
{
	// Test si l'adresse email existe dans la base
	$sql_utilisateurs="SELECT * FROM $table_user WHERE email like :email";
	$res_utilisateurs = $connexion->prepare($sql_utilisateurs);
	$res_utilisateurs->execute(array(':email'=>$login)) or die ("requete ligne 39 : ".$sql_utilisateurs);
	$tab_utilisateur=$res_utilisateurs->fetchAll();
	// email déjà existant
	if(count($tab_utilisateur)>0)
		$arrayToJs[1] = false;
	else
		$arrayToJs[1] = true;
}
else
{
	$arrayToJs[1] = true;
	$arrayToJs[2] = "Email inchangé";
}
echo json_encode($arrayToJs);
?>


