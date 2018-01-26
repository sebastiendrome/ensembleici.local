<?php
if(!empty($_POST["no_contact"])){
	$contenu .= '<form class="formulaire_contact" onsubmit="return envoyer_courriel();" method="post" action="">';
		$contenu .= '<input id="input_no_contact" name="input_no_contact" type="hidden" value="'.$_POST["no_contact"].'" />';
		$contenu .= '<input type="text" placeholder="Votre nom" id="input_contact_libelle" name="input_contact_libelle" />';
		$contenu .= '<br />';
		$contenu .= '<input id="input_email_expediteur" name="input_email_expediteur" type="text" placeholder="votre adresse mail"'.((est_connecte())?' value="'.$_SESSION["utilisateur"]["email"].'"':'').' />';
		$contenu .= '<br />';
		$contenu .= '<textarea id="textarea_contenu_mail" name="textarea_contenu_mail" title="Contenu de votre message ..."></textarea>';
		$contenu .= '<br />';
		include "struct_captcha.php";
		$contenu .= '<input type="submit" value="Envoyer" class="ico fleche" />';
	$contenu .= '</form>';
}
else{
	$contenu .= '<p>Une erreur s\'est produite, veuillez réessayer</p>';
}
?>
