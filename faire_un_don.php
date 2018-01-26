<?php
$contenu_droite = contenu_colonne_droite("autres_pages");
//On récupère le titre et le contenu du bloc "animation"
$territoire = 1;
if (isset($_SESSION["utilisateur"]["territoire"])) {
    $territoire = $_SESSION["utilisateur"]["territoire"];
}
$requete = "SELECT titre, contenu FROM contenu_blocs WHERE ref=17 AND etat=1 AND territoires_id = ".$territoire;
$tab_animation = execute_requete($requete);
$contenu = (count($tab_animation)>0)?$tab_animation[0]["contenu"]:"";

//$contenu .= '<h2>Faire un don en ligne (sécurisé par Paypal)<sup>*</sup></h2>';
//$contenu .= '<p>Cliquez sur le bouton ci-dessous pour être redirigé(e) vers la page Paypal et effectuer votre don.</p>';
//$contenu .= '<form class="faire_un_don" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">';
//	$contenu .= '<input type="hidden" name="cmd" value="_s-xclick">';
//	$contenu .= '<input type="hidden" name="hosted_button_id" value="W5LHPNJCCAKUE">';
//	$contenu .= '<p class="boutons">';
//		$contenu .= '<input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !" />';
//		$contenu .= '<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1" />';
//	$contenu .= '</p>';
//$contenu .= '</form>';
//$contenu .= '<p>Notez qu\'il n\'est pas obligatoire de posséder un compte paypal pour effectuer un don.<br />';
//$contenu .= 'Si vous souhaitez effectuer un don anonyme, saisissez les mots "don anonyme" dans le petit encars "Vous souhaitez nous laisser un message ?" lors de la confirmation de votre don.</p>';
//$contenu .= '<p><sup>*</sup> des frais sont prélevés par Paypal sur vos dons effectués en ligne (25 centimes + 1,4% à 3,4% du montant). L\'entreprise Paypal se rémunère ainsi pour ce service fourni gratuitement à ensembleici.fr.</p>';

$ligne1 = array(array("class"=>"grand_moyen","id"=>"colonne_gauche","contenu"=>$contenu),array("class"=>"moyen_petit","id"=>"colonne_droite","contenu"=>$contenu_droite));
$lignes = array(array("lignes"=>$ligne1));
?>
