<?php
$contenu .= '<form class="formulaire_inscription" onsubmit="return inscription();" method="post" action="">';
	$contenu .= '<input id="input_email" type="text" placeholder="adresse mail" />';
	$contenu .= '<br />';
	$contenu .= '<input id="input_email_verification" type="text" placeholder="répétez votre adresse mail" onpaste="return false;" />';
	$contenu .= '<br />';
	$contenu .= '<input id="input_pseudo" type="text" placeholder="Nom d\'utilisateur (facultatif)" />';
	$contenu .= '<br />';
	$contenu .= '<input id="input_mdp" type="password" placeholder="mot de passe"/>';
	$contenu .= '<br />';
	$contenu .= '<input type="text" id="recherche_ville" autocomplete="off" class="recherche_ville" placeholder="code postal, ville" name="code postal, ville" />';
	$contenu .= '<input type="hidden" id="input_no_ville" class="recherche_ville" name="no_ville" />';
	$contenu .= '<br />';
	$contenu .= '<div id="recherche_ville_liste"><div></div></div>';
	$contenu .= '<br />';
	include "struct_captcha.php";
	$contenu .= '<br />';
	$contenu .= '<input id="input_inscription" type="submit" value="Connexion" class="ico connexion" />';
$contenu .= '</form>';
?>
