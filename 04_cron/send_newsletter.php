<?php
set_time_limit(540);
header('Content-type: text/plain; charset=utf-8');
//1. Initialisation de la session
include "/home/ensemble/01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "/home/ensemble/01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "/home/ensemble/01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "/home/ensemble/01_include/_init_var.php";

require_once '/home/ensemble/gestion/ajax/lettres/config_pear.php';


$file_denvoi_de_mail =& new Mail_Queue($db_options, $mail_options);
// envoi des mails
$requete_nb_mails = "SELECT COUNT(id) as nb FROM mail_queue";
$nb_mail_to_send = execute_requete($requete_nb_mails);
$limit = $nb_mail_to_send[0]['nb'];
if ($limit > 0) {
    if ($limit > 180) {
        $limit = 180; 
    }
    while ($limit > 0) {
        $file_denvoi_de_mail->sendMailsInQueue(1);
        sleep(3);
        $limit = $limit - 1;
    }
}

echo $limit;

//    foreach ($tab as $k => $v) {
//        // mise à jour finale de lettre_info_envoi
//        $no_envoi = $v['no_envoi'];
//        $req_le_envoi = "UPDATE lettreinfo_envoi SET nb_envoi = :nb, date_fin = :d WHERE no = :no_envoi";
//        $res_le_envoi = $connexion->prepare($req_le_envoi);
//        $res_le_envoi->execute(array(":no_envoi" => $no_envoi, ":d" => date('Y-m-d H:i:s'), ":nb" => $v['nbmail']));
//    }
    

//echo $log; 
?>
