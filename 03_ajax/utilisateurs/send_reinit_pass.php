<?php
//1. Initialisation de la session
include "../../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../../01_include/_init_var.php";

$return_code = '0';

if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
    $requete_utilisateur = "SELECT no FROM utilisateur WHERE email=:e";
    $tab_utilisateur = execute_requete($requete_utilisateur,array(":e"=>$_POST["email"]));
    if (!empty($tab_utilisateur)) {
        // l'utilisateur est présent en base, on génère un id aleatoire, on lui envoie un mail et on met à jour la base
        $code_alea = id_aleatoire();
        //On récupère le header html
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
        
        // initialisation du lien 
        $lien_controle = "<a href='http://www.ensembleici.fr/espace-personnel.html?reinit_mdp=".$code_alea."' target='_blank'>http://www.ensembleici.fr/espace-personnel.html?reinit_mdp=".$code_alea."</a>";
        
        $MAIL_EXPEDITEUR = $email_admin;
        $MAIL_DESTINATAIRE = $_POST["email"];
        $OBJET = "ensembleici.fr - Réinitialisation du mot de passe";
        $CONTENU_MAIL_HTML = "Bonjour,<br />Vous venez de solliciter la réinitialisation de votre mot de passe. Pour terminer la procédure, veillez cliquer sur le lien ci-après ou le copier dans votre navigateur : ".$lien_controle."<br /><br />Salutations,<br />";
        $CONTENU_MAIL_HTML = $HEADER_MAIL_HTML.$CONTENU_MAIL_HTML.$FOOTER_MAIL_HTML;
        $CONTENU_MAIL_TXT = "Bonjour,\r\nVous venez de solliciter la réinitialisation de votre mot de passe. Pour terminer la procédure, veillez cliquer sur le lien ci-après ou le copier dans votre navigateur : ".$lien_controle."\r\n\r\nSalutations,\r\n";
        $UNSUSCRIBE_LINK = "";
        include "../../01_include/envoyer_un_mail.php";
        
        // mise à jour de la table 
        $requete_update = "UPDATE utilisateur SET code_reinit_mot_de_passe = :code WHERE (no = :no)";
        $param_update = array(	":no" => $tab_utilisateur[0]["no"], ":code" => $code_alea);
        $mareq = execute_requete($requete_update,$param_update);
    }
    else {
        $return_code = '11';
    }
}
else {
    $return_code = '10';
}

$tab = array(); 
$tab['code'] = $return_code; 
$tab['id'] = $tab_utilisateur[0]["no"]; 
$tab['alea'] = $code_alea; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
