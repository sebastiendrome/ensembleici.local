<?php
header('Content-type: text/plain; charset=utf-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";

$date_debut = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") + 5,   date("Y")));

$date_debut = '2016-05-18';

//$requete = "SELECT * FROM evenement_favoris F, evenement E WHERE F.no_evenement = E.no AND no_utilisateur = 1349167650";
$requete = "SELECT E.titre, E.sous_titre, E.no, U.email, U.pseudo FROM evenement_favoris F, evenement E, utilisateur U WHERE F.no_evenement = E.no AND F.no_utilisateur = U.no AND date_debut = '".$date_debut."'";
$tab = execute_requete($requete);
if (sizeof($tab) != 0) {
    $MAIL_EXPEDITEUR = 'newsletter@ensembleici.fr';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_header.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $HEADER_MAIL_HTML = curl_exec($ch);
    curl_close($ch);
    //On récupère le footer html
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_footer.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $FOOTER_MAIL_HTML = curl_exec($ch);
    curl_close($ch);
        
    foreach ($tab as $k => $v) {
        $MAIL_DESTINATAIRE = $v->email;
        $OBJET = "ensembleici.fr - Rappel de votre séléction";
        $CONTENU_MAIL_HTML = "Bonjour,<br />vous avez ajouté un événement dans vos favori : ".$v->titre." : ".$v->sous_titre.". Cet événement a lieu dans 5 jours et vous pouvez le consulter sur notre site.<br /><br />Salutations,<br />";
        $CONTENU_MAIL_HTML = $HEADER_MAIL_HTML.$CONTENU_MAIL_HTML.$FOOTER_MAIL_HTML;
        $CONTENU_MAIL_TXT = "Bonjour,\r\nvous avez ajouté un événement dans vos favori : ".$v->titre." : ".$v->sous_titre.". Cet événement a lieu dans 5 jours et vous pouvez le consulter sur notre site\r\n\r\nSalutations,\r\n";
        $UNSUSCRIBE_LINK = "";
        include "../01_include/envoyer_un_mail.php";
    }
}
?>
