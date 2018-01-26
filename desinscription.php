<?php
$contenu_droite = contenu_colonne_droite("recherche");

$email_user = '';
if (isset($_REQUEST['codoff']) && isset($_REQUEST['typoff'])) {
    if ($_REQUEST['typoff'] == 'utilisateur') {
        $requete = "SELECT email FROM utilisateur WHERE newsletter = 1 AND code_desinscription_nl = '".$_REQUEST['codoff']."'";
    }
    else {
        $requete = "SELECT email FROM newsletter WHERE etat = 1 AND code_desinscription_nl = '".$_REQUEST['codoff']."'";
    }
    $tab = execute_requete($requete);
    if (sizeof($tab) > 0) {
        $email_user = $tab[0]['email'];
    }
}
$contenu = '<div>';
$contenu .= '<p>';
$contenu .= "Vous recevez aujourd'hui les lettres d'informations du site Ensemble Ici et vous souhaitez vous désinscrire.";
$contenu .= '</p>';
$contenu .= '<p>';
$contenu .= "Vous pouvez le faire en renseignant le formulaire ci-dessous.";
$contenu .= '</p>';
$contenu .= '<div id="espace-perso_connexion" style="margin-left: 100px; width:400px;">';
$contenu .= '<div>';
$contenu .= "<p>Saisissez l'adresse email à désinscrire</p>";
$fonction_sortie_connexion = 'charger_page()';
include "01_include/struct_form_desinscription.php";
$contenu .= '</div>';
$contenu .= '</div>';
$contenu .= '</div>';

$ligne1 = array(array("class"=>"grand_moyen","id"=>"colonne_gauche","contenu"=>$contenu),array("class"=>"moyen_petit","id"=>"colonne_droite","contenu"=>$contenu_droite));
$lignes = array(array("lignes"=>$ligne1));
?>
