<?php
header('Content-Type: text/plain; charset=UTF-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";
$tab = array();
$return_code = '0';
//0. On vérifie que toutes les variables nescessaires sont renseignées
//if(!empty($_POST["email"])&&!empty($_POST["no_ville"])&&!empty($_POST["captcha"])){
$_POST["email"] = urldecode($_POST["email"]);
$_POST["captcha"] = md5(urldecode($_POST["captcha"]));
//1. On vérifie le format de l'adresse mail
if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
    if($_POST["captcha"]==$_SESSION["sysCaptchaCode"]){
//        $_SESSION["sysCaptchaCode"] = "";
        //2. On vérifie qu'un utilisateur n'existe pas déjà avec cette adresse mail
        $requete_utilisateur = "SELECT no FROM utilisateur WHERE email = :e";
        $tab_utilisateur = execute_requete($requete_utilisateur,array(":e" => $_POST["email"]));
        if(!empty($tab_utilisateur)){
            $maj_user = "UPDATE utilisateur SET newsletter = 0 WHERE no = :no";
            execute_requete($maj_user,array(":no" => $tab_utilisateur[0]['no']));
        }
        $tabliste = explode(',', $_POST['liste']); 
        foreach ($tabliste as $k => $v) {
            $requete_newsletter = "SELECT no,etat FROM newsletter WHERE email = :e AND no_ville = :v";
            $tab_newsletter = execute_requete($requete_newsletter,array(":e" => $_POST["email"], ":v" => $v));
            $code_desinscription_nl = substr(sha1(uniqid()), 0 , 30);
            if (empty($tab_newsletter)) {
                $requete_insert = "INSERT INTO newsletter (email,no_ville,etat,code_desinscription_nl,date_inscription) VALUES (:e,:v,1,:c,CURRENT_TIMESTAMP)";
                execute_requete($requete_insert,array(":e" => $_POST["email"], ":v" => $v, ":c" => $code_desinscription_nl));
            }
            else {
                if ($tab_newsletter[0]["etat"] == 0) {
                    $requete_update = "UPDATE newsletter SET etat=1, code_desinscription_nl=:c WHERE no=:no"; 
                    execute_requete($requete_update,array(":c" => $code_desinscription_nl, ":no" => $tab_newsletter[0]["no"]));
                }
            }
        }
//        }
//		else{
//			//2.1. On regarde si l'adresse correspond à celle de l'utilisateur pour lui indiquer où modifier son abonnement
//			if(est_connecte()&&$_SESSION["utilisateur"]["email"]==$_POST["email"]){
//				$return = array(false,"Modifiez votre abonnement à notre lettre d'information depuis votre espace personnel.");
//			}
//			else{
//				$return = array(false,$_POST["email"]." est déjà utlisée en tant que profil sur ensemble ici");
//			}
//		}
    }
    else {
        $return_code = '10';
//                $return = array(false,"Le code de sécurité n'est pas valide.");
    }
}
else{
    $return_code = '20';
//		$return = array(false,"Veuillez saisir une adresse mail valide");
}
//}
//else{
//	$return = array(false,"Veuillez remplir tous les champs");
//}
$tab['code'] = $return_code;
echo json_encode($tab);
?>
