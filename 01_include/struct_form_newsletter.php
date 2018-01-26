<?php
$requete = "SELECT id, nom, ville FROM territoires WHERE mail_newsletter IS NOT NULL";
$tablettres = execute_requete($requete); 

$contenu .= '<form class="formulaire_newsletter">';
$contenu .= "<div>Vous pouvez désormais vous inscrire à plusieurs lettres d'informations couvrant des territoires différents. Cochez les cases correspondant aux territoires qui vous intéressent et valider votre inscription.</div>";
$contenu .= '<br/>';
        foreach ($tablettres as $k => $v) {
            $contenu .= "<div style='float:left;width:260px;'>"; 
            $contenu .= "<input type='checkbox' name='inp_abo' data-ref='".$v['ville']."' />"; 
            $contenu .= "<span style='margin-left:15px;'>Territoire ".$v['nom']."</span>";
            $contenu .= "</div>";
        }
        $contenu .= "<div style='clear:both;'></div><br/>";
	$contenu .= '<input id="input_email" type="text" placeholder="Votre adresse mail" />';
	$contenu .= '<br />';
        
	//$contenu .= '<label for="">Votre pays de référence : </label>';
//	$contenu .= creer_input_communautescommunes();
	$contenu .= '<br />';
	include "struct_captcha.php";
	$contenu .= '<br />';
	$contenu .= '<input id="btn_valid_abo" type="button" value="Inscription à nos lettres" class="ico lettre" />';
$contenu .= '</form>';
?>
