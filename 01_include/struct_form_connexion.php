<?php
$contenu .= '<form class="formulaire_connexion" onsubmit="return connexion('.((!empty($fonction_sortie_connexion))?"'".$fonction_sortie_connexion."'":'').');" method="post" action="">';
	$contenu .= '<input id="input_email" type="text" placeholder="adresse mail" />';
	$contenu .= '<br />';
	$contenu .= '<input id="input_mdp" type="password" placeholder="mot de passe" />';
	$contenu .= '<br />';
	$contenu .= '<input id="input_connexion" type="submit" value="Connexion" class="ico connexion" />';
	$contenu .= '<br/>';
	$contenu .= '<span id="span_mdp_oublie">Mot de passe oublié"</span>';
$contenu .= '</form>';
?>
