<?php
$contenu .= '<div class="captcha">';
	$contenu .= '<input type="text" placeholder="Code de sécurité" id="input_captcha" name="input_captcha" autocomplete="off" />';
	$contenu .= '<img src="img/img_colorize.php?uri=ico_recharger.png&c=68,81,88" class="recharger_captcha infobulle[Recharger l\'image|gauche]" onclick="recharger_captcha(this.nextSibling);" />'; //Pour recharger le captcha
	$contenu .= '<img class="image_captcha infobulle[Ce code nous permet de vérifier que vous nềtes pas un robot|haut]" src="01_include/img_captcha.php?a='.time().'" />';
$contenu .= '</div>';
?>
