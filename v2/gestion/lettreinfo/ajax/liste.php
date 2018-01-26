<?php
include "config_pear.php";
require_once('../../../01_include/_connect.php');
$continuer = true;
$les_listes = array(array("table_liste",array("cl�","adresse mail","champ de test")),array("newsletter",array("no","email","etat")),array("utilisateur",array("no","email","newsletter")));
$par_paquet = 100;
if(isset($_POST["no_liste"])&&$_POST["no_liste"]!=""&&$_POST["no_liste"]!=0){
	$no_liste = $_POST["no_liste"];
	if($no_liste<=0||$no_liste>=count($les_listes))
		$continuer = false;
}
else{
	$no_liste = 1;
}
if($continuer){
	if(isset($_POST["no"])&&$_POST["no"]!=""&&$_POST["no"]!=0){
		//On cr�ait une file d'attente
		$file_denvoi_de_mail =& new Mail_Queue($db_options, $mail_options);
		
		$requete_info = "SELECT lettreinfo.objet AS o, lettreinfo_envoi.contenu_html AS c, lettreinfo.no_envoi AS no_e FROM lettreinfo_envoi JOIN lettreinfo ON lettreinfo.no_envoi=lettreinfo_envoi.no WHERE lettreinfo.no=:no";
		$res_info = $connexion->prepare($requete_info);
		$res_info->execute(array(":no"=>$_POST["no"]));
		$tab_info = $res_info->fetchAll();
		$contenu_HTML = $tab_info[0]["c"];
		$objet = $tab_info[0]["o"];
		$no_envoi = $tab_info[0]["no_e"];
		
		$liste = $les_listes[$no_liste];
		$table = $liste[0];
		$les_champs = $liste[1];
		$champ_no = $les_champs[0];
		$champ_mail = $les_champs[1];
		$champ_etat = $les_champs[2];
		$requete = "SELECT ".$table.".".$champ_no." AS n,".$table.".".$champ_mail." AS m,".$table.".".$champ_etat." AS e FROM ".$table." WHERE ".$table.".".$champ_etat."=1 AND ".$table.".".$champ_no." NOT IN(SELECT mail_queue_insert.no FROM mail_queue_insert WHERE tbl=:t) LIMIT ".$par_paquet;
		$res = $connexion->prepare($requete);
		$res->execute(array(":t"=>$table));
		$resultat = $res->fetchAll();
		if(count($resultat)>0){ //S'il reste des adresses dans la liste courante
			for($i=0;$i<count($resultat);$i++){ //On les ins�re dans la liste d'envoi ainsi que dans mail_queue_insert
				$insertion = "INSERT INTO mail_queue_insert(no,tbl) VALUES(:no,:t)";
				$res_ins = $connexion->prepare($insertion);
				$res_ins->execute(array(":no"=>$resultat[$i]["n"],":t"=>$table));
				//On ins�re dans la fille d'attente
					//On cr�ait l'en-t�te
					$from = 'newsletter@africultures.info';
					$destinataire = $resultat[$i]["m"];
					$message = "test<html><head></head><body>".$contenu_HTML."</body></html>";

					$entetes = array( 'From'    => $from,
					   'To'      => $destinataire,
					   'Subject' => $objet,
//					   'X-Sender' => '<www.africultures.info>',
                                            'X-Sender' => '<www.ensembleici.fr>',
//					   'X-auth-smtp-user' => 'newsletter@africultures.info',
                                            'X-auth-smtp-user' => 'contact@envolinfo.com',
					   'X-Priority' => 3,
//					   'X-Unsubscribe-Web' => '<http://www.africultures.com/php/?nav=self&sr=desinscription-lettre>',
//					   'X-Unsubscribe-Email' => '<mailto:unsubscribe@africultures.com>',
                                            'X-Unsubscribe-Web' => '<http://www.ensembleici.fr/desinscription.html?codoff=0&typoff=0',
					   'X-Unsubscribe-Email' => '<mailto:unsubscribe@ensembleici.fr>',
					   'X-Mailer' => 'PHP/'.phpversion(),
					   'Content-Type' => 'text/html; charset=utf-8');
					   // 'Return-path' => "-f".$from 
					//On cr�ait un message valide
					$mime =& new Mail_mime();
					// $mime->setTXTBody($message);
					$mime->setHTMLBody($message);
					$corps = $mime->get();
					$entetes = $mime->headers($entetes,true);
					$file_denvoi_de_mail->put( $from, $destinataire, $entetes, $corps );
			}
			$reponse = array(true,true,$no_liste);
		}
		else{ //Sinon on change de liste
			$no_liste++;
			if($no_liste<count($les_listes)){ //Il reste au moins une liste
				$reponse = array(true,true,$no_liste);
			}
			else{ //Il n'y a plus de listes
				$reponse = array(true,false);
				//On met � jour la nb_liste de lettreinfo_envoi avec getQueueCount();
				$requete = "UPDATE lettreinfo_envoi SET nb_liste=:nb WHERE no=:no";
				$res = $connexion->prepare($requete);
				$res->execute(array(":nb"=>$file_denvoi_de_mail->getQueueCount(),":no"=>$no_envoi));
			}
		}
	}
	else{
		$reponse = array(false,utf8_encode("le numéro de lettre est erroné!"));
	}
}
else{
	$reponse = array(false,utf8_encode("le numéro de liste est erron�!"));
}
echo json_encode($reponse);
?>