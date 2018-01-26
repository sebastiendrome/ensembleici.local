<?php
set_time_limit(1000);
header('Content-type: text/plain; charset=utf-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";

//if ($root_site != 'http://localhost/EnsembleIci/') {
    require_once '../gestion/ajax/lettres/config_pear.php';
//}

$requete = "SELECT no_envoi FROM envoilettres";
$tab = execute_requete($requete);
$log = '';

if (sizeof($tab) > 0) {
    $file_denvoi_de_mail =& new Mail_Queue($db_options, $mail_options);
    
//    $req_truncate = "TRUNCATE TABLE mail_queue_insert";
//    $res_truncate = $connexion->prepare($req_truncate);
//    $res_truncate->execute();
    
    $nbmail = 0;
    
    foreach ($tab as $k => $v) {
        // on cherche à remplir la mail queue après récupération des infos lettre
        $no_envoi = $v['no_envoi'];
        $requete_lettre = "SELECT E.contenu_html, E.contenu_txt, L.territoires_id, L.objet, L.no as no_lettre FROM lettreinfo_envoi E, lettreinfo L WHERE L.no_envoi = E.no AND E.no = :no_envoi";
        $tab_lettre = execute_requete($requete_lettre, array(":no_envoi" => $no_envoi));
        $contenu_html = $tab_lettre[0]['contenu_html'];

        if (isset($tab_lettre[0]['territoires_id'])) {
            $territoire = $tab_lettre[0]['territoires_id'];
            
            // récuperétaion des infos territoire
            $requete_territoire = "SELECT nom, mail_newsletter FROM territoires WHERE id = :territoire";
            $monterritoire = execute_requete($requete_territoire,array(":territoire" => $territoire));

            // on recherche tous les destinataires
            $requete_newsletter = "SELECT N.no, N.email, N.code_desinscription_nl as c, 'newsletter' as type, N.no_ville FROM newsletter N, communautecommune_ville V, communautecommune C WHERE N.etat = 1 
                AND V.no_ville = N.no_ville AND V.no_communautecommune = C.no AND C.territoires_id = :territoire";
            $liste_newsletter = execute_requete($requete_newsletter,array(":territoire" => $territoire));

            $requete_newsletter_bis = "SELECT N.no, N.email, N.code_desinscription_nl as c, 'newsletter' as type, N.no_ville FROM newsletterbis N, communautecommune_ville V, communautecommune C WHERE N.etat = 1 
                AND V.no_ville = N.no_ville AND V.no_communautecommune = C.no AND C.territoires_id = :territoire";
            $liste_newsletter_bis = execute_requete($requete_newsletter_bis,array(":territoire" => $territoire));

            $requete_utilisateur = "SELECT U.no, U.email, U.code_desinscription_nl, 'utilisateur' as type, U.no_ville FROM utilisateur U, communautecommune_ville V, communautecommune C WHERE U.newsletter = 1 
                AND V.no_ville = U.no_ville AND V.no_communautecommune = C.no AND C.territoires_id = :territoire";
            $liste_utilisateur = execute_requete($requete_utilisateur,array(":territoire" => $territoire));

//            $tab_liste_envoi = array_merge($liste_newsletter, $liste_newsletter_bis, $liste_utilisateur);
            
            $tab_liste_envoi = array();
            $tab_liste_envoi[0] = array('no' => 0, 'email' => 'stephane.closse@club-internet.fr', 'c' => 'aa', 'type' => 'utilisateur', 'no_ville' => 9568);
            $tab_liste_envoi[1] = array('no' => 1, 'email' => 'stephane.closse@gmail.com', 'c' => 'aa', 'type' => 'utilisateur', 'no_ville' => 9568);
            $tab_liste_envoi[2] = array('no' => 2, 'email' => 'contact@envolinfo.com', 'c' => 'aa', 'type' => 'utilisateur', 'no_ville' => 9568);
            $tab_liste_envoi[3] = array('no' => 3, 'email' => 'contact@aventic.org', 'c' => 'aa', 'type' => 'utilisateur', 'no_ville' => 9568);
            $tab_liste_envoi[4] = array('no' => 4, 'email' => 'contact@ensembleici.fr', 'c' => 'aa', 'type' => 'utilisateur', 'no_ville' => 9568);
            $tab_liste_envoi[4] = array('no' => 5, 'email' => 'olivier.barlet@lespilles.fr', 'c' => 'aa', 'type' => 'utilisateur', 'no_ville' => 9568);
            $tab_liste_envoi[4] = array('no' => 6, 'email' => 'info.vdd@ensembleici.fr', 'c' => 'aa', 'type' => 'utilisateur', 'no_ville' => 9568);
            
            $from = 'Ensemble Ici <'.$monterritoire[0]['mail_newsletter'].'>';
            foreach ($tab_liste_envoi as $k1 => $v1) {
                // ajout dans la table mail_queue_insert
                $email = $v1['email']; $user = $v1['no']; $code = $v1['c']; $type = $v1['type']; $typoff = urlencode($type); $ville = $v1['no_ville'];
//                $req_mq_insert = "INSERT INTO mail_queue_insert(no,tbl) VALUES(:no,:t)";
//                $res_mq_ins = $connexion->prepare($req_mq_insert);
//                $res_mq_ins->execute(array(":no" => $user,":t" => $type));

                // insertion file d'attente mail
                $message = "<html><head></head><body>".preg_replace("#(\[\*\*codoff\*\*\])#i",$code,preg_replace("#(\[\*\*typoff\*\*\])#i",$typoff, preg_replace("#(\[\*\*idv\*\*\])#i",$ville, $contenu_html)))."</body></html>";
                $entetes = array( 'From'    => $from,
                    'To'      => $email,
                    'Subject' => $tab_lettre[0]['objet'],
                    'X-Sender' => '<www.ensembleici.fr>',
                    'X-auth-smtp-user' => 'contact@envolinfo.com',
                    'X-Priority' => "3",
                    'X-Unsubscribe-Web' => '<http://www.ensembleici.fr/desinscription.html?codoff='.$code.'&typoff='.$typoff,
                    'X-Unsubscribe-Email' => '<mailto:unsubscribe@ensembleici.fr>',
                    'X-Mailer' => 'PHP/'.phpversion(),
                    'Content-Type' => 'text/html; charset=utf-8',
                    'Return-path' => "-f".$from 
                );
                //On crée un message valide
                $mime =& new Mail_mime();
                $mime->setHTMLBody($message);
                $corps = $mime->get();
                $new_entetes = $mime->headers($entetes,true);
                $file_denvoi_de_mail->put( $from, $email, $new_entetes, $corps );
                $nbmail++;
            }
        }
        else {
            $log .= "Erreur : pas de lettre en base";
        }
        
        // mise à jour de lettre_info_envoi
        $req_le_envoi = "UPDATE lettreinfo_envoi SET nb_liste = :nb, date_debut = :d WHERE no = :no_envoi";
        $res_le_envoi = $connexion->prepare($req_le_envoi);
        $res_le_envoi->execute(array(":no_envoi" => $no_envoi, ":d" => date('Y-m-d H:i:s'), ":nb" => $nbmail));
        
        // mise à jour des tables agenda, annonces, ...
        $requete_agenda = "SELECT liste_evenement FROM lettreinfo_agenda WHERE no_lettre = :no_lettre";
        $tab_agenda = execute_requete($requete_agenda,array(":no_lettre" => $tab_lettre[0]['no_lettre']));
        if (isset($tab_agenda[0]['liste_evenement'])) {
            $tab_liste_agenda = explode(',', $tab_agenda[0]['liste_evenement']); 
            foreach ($tab_liste_agenda as $k2 => $v2) {
                $req_update_evenement = "UPDATE evenement SET apparition_lettre = apparition_lettre + 1 WHERE no = :no";
                $res_update_evenement = $connexion->prepare($req_update_evenement);
                $res_update_evenement->execute(array(":no" => $v2));
            }
        }
    
        $requete_annonces = "SELECT liste_petiteannonce FROM lettreinfo_petiteannonce WHERE no_lettre = :no_lettre";
        $tab_annonces = execute_requete($requete_annonces,array(":no_lettre" => $tab_lettre[0]['no_lettre']));
        if (isset($tab_annonces[0]['liste_petiteannonce'])) {
            $tab_liste_annonces = explode(',', $tab_annonces[0]['liste_petiteannonce']);
            foreach ($tab_liste_annonces as $k2 => $v2) {
                $req_update_annonces = "UPDATE petiteannonce SET apparition_lettre = apparition_lettre + 1 WHERE no = :no";
                $res_update_annonces = $connexion->prepare($req_update_annonces);
                $res_update_annonces->execute(array(":no" => $v2));
            }
        }
        
        $requete_structure = "SELECT liste_structure FROM lettreinfo_repertoire WHERE no_lettre = :no_lettre";
        $tab_structure = execute_requete($requete_structure,array(":no_lettre" => $tab_lettre[0]['no_lettre']));
        if (isset($tab_structure[0]['liste_structure'])) {
            $tab_liste_structure = explode(',', $tab_structure[0]['liste_structure']); 
            foreach ($tab_liste_structure as $k2 => $v2) {
                $req_update_structure = "UPDATE structure SET apparition_lettre = apparition_lettre + 1 WHERE no = :no";
                $res_update_structure = $connexion->prepare($req_update_structure);
                $res_update_structure->execute(array(":no" => $v2));
            }
        }
        
        $tab[$k]['nbmail'] = $nbmail;
        $nbmail = 0;
    }
    
    // envoi des mails
    $requete_nb_mails = "SELECT COUNT(id) as nb FROM mail_queue";
    $nb_mail_to_send = execute_requete($requete_nb_mails);
    $limit = $nb_mail_to_send[0]['nb'];
    while ($limit > 0) {
        $file_denvoi_de_mail->sendMailsInQueue(3);
        sleep(4);
        $limit = $limit - 3;
    }

    foreach ($tab as $k => $v) {
        // mise à jour finale de lettre_info_envoi
        $no_envoi = $v['no_envoi'];
        $req_le_envoi = "UPDATE lettreinfo_envoi SET nb_envoi = :nb, date_fin = :d WHERE no = :no_envoi";
        $res_le_envoi = $connexion->prepare($req_le_envoi);
        $res_le_envoi->execute(array(":no_envoi" => $no_envoi, ":d" => date('Y-m-d H:i:s'), ":nb" => $v['nbmail']));
    }
    
    // vide la table envoilettre
    $req_truncate2 = "TRUNCATE TABLE envoilettres";
    $res_truncate2 = $connexion->prepare($req_truncate2);
    $res_truncate2->execute();
}

echo $log; 
?>
