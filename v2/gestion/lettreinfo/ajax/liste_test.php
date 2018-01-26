<?php
include "config_pear.php";
require_once('../../../01_include/_connect.php');
$continuer = true;
$les_listes = array(array("table_liste",array("clé","adresse mail","champ de test","codoff","idv")),array("newsletter",array("no","email","etat","code_desinscription_nl","no_ville")),array("utilisateur",array("no","email","newsletter","code_desinscription_nl","no_ville")),array("newsletterbis",array("no","email","etat","code_desinscription_nl","no_ville")));
//$mails_test = array("","samuel@africultures.com","franck@africultures.com");
$par_paquet = 100;
//Pour le jeu d'essai
//$TABLE_MAILS = array();
//$TABLE_MAILS["newsletter"][0] = array("n"=>1,"m"=>"contact@ensembleici.fr","e"=>1,"c"=>"abc","v"=>9568);
//$TABLE_MAILS["newsletter"][1] = array("n"=>2,"m"=>"contact.vdd@ensemnleici.fr","e"=>1,"c"=>"def","v"=>9568);
//$TABLE_MAILS["newsletter"][2] = array("n"=>3,"m"=>"olivier.barlet@lespilles.fr","e"=>0,"c"=>"ghi","v"=>9568);
//$TABLE_MAILS["newsletter"][3] = array("n"=>4,"m"=>"olivier@africultures.com","e"=>1,"c"=>"jkl","v"=>9568);
//$TABLE_MAILS["newsletter"][4] = array("n"=>5,"m"=>"stephane.closse@gmail.com","e"=>1,"c"=>"jkl","v"=>9568);
//$TABLE_MAILS["utilisateur"][0] = array("n"=>1,"m"=>"stephane.closse@club-internet.fr","e"=>1,"c"=>"mno","v"=>9568);
//$TABLE_MAILS["utilisateur"][1] = array("n"=>3,"m"=>"contact@episteme-web.com","e"=>0,"c"=>"pqr","v"=>9568);
//$TABLE_MAILS["utilisateur"][2] = array("n"=>4,"m"=>"etude@envolino.com","e"=>1,"c"=>"stu","v"=>9568);
//$TABLE_MAILS["utilisateur"][3] = array("n"=>5,"m"=>"contact@aventic.org","e"=>1,"c"=>"vwx","v"=>9568);
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
		
		$requete_info = "SELECT lettreinfo.objet AS o, lettreinfo_envoi.contenu_html AS c, lettreinfo.no_envoi AS no_e, lettreinfo.territoires_id FROM lettreinfo_envoi JOIN lettreinfo ON lettreinfo.no_envoi=lettreinfo_envoi.no WHERE lettreinfo.no=:no";
		$res_info = $connexion->prepare($requete_info);
		$res_info->execute(array(":no"=>$_POST["no"]));
		$tab_info = $res_info->fetchAll();
		$contenu_HTML = $tab_info[0]["c"];
		$objet = $tab_info[0]["o"];
                $territoire = $tab_info[0]["territoires_id"];
		$no_envoi = $tab_info[0]["no_e"];
		
		$liste = $les_listes[$no_liste];
		$table = $liste[0];
		$les_champs = $liste[1];
		$champ_no = $les_champs[0];
		$champ_mail = $les_champs[1];
		$champ_etat = $les_champs[2];
		$champ_ville = $les_champs[4];
		$champ_codoff = $les_champs[3];
		$typoff = urlencode($table);
		
//		$requete = "SELECT ".$table.".".$champ_no." AS n,".$table.".".$champ_mail." AS m,".$table.".".$champ_etat." AS e,".$table.".".$champ_ville." AS v,".$table.".".$champ_codoff." AS c FROM ".$table." WHERE ".$table.".".$champ_etat."=1 AND ".$table.".".$champ_no." NOT IN(SELECT mail_queue_insert.no FROM mail_queue_insert WHERE tbl=:t) ORDER BY ".$table.".".$champ_no." LIMIT ".$par_paquet;
                $requete = "SELECT ".$table.".".$champ_no." AS n,".$table.".".$champ_mail." AS m,".$table.".".$champ_etat." AS e,".$table.".".$champ_ville." AS v,".$table.".".$champ_codoff." AS c 
                        FROM ".$table.", communautecommune_ville V, communautecommune C  
                        WHERE ".$table.".".$champ_etat."=1 AND V.no_ville = ".$table.".no_ville AND V.no_communautecommune = C.no AND C.territoires_id = :territoire   
                            AND ".$table.".".$champ_no." NOT IN(SELECT mail_queue_insert.no FROM mail_queue_insert WHERE tbl=:t) ORDER BY ".$table.".".$champ_no." LIMIT ".$par_paquet;
		$res = $connexion->prepare($requete);
		$res->execute(array(":t"=>$table, ":territoire" => $territoire)) or die("erreur");
		$resultat = $res->fetchAll();
		/**
		Pour je jeu d'essai
		**/
//		 $resultat = $TABLE_MAILS[$table];
		
		
		if(count($resultat)>0){ //S'il reste des adresses dans la liste courante
			for($i=0;$i<count($resultat);$i++){ //On les insère dans la liste d'envoi ainsi que dans mail_queue_insert
				$insertion = "INSERT INTO mail_queue_insert(no,tbl) VALUES(:no,:t)";
				$res_ins = $connexion->prepare($insertion);
				$res_ins->execute(array(":no"=>$resultat[$i]["n"],":t"=>$table));
				
				$codoff = $resultat[$i]["c"];
				$idv = $resultat[$i]["v"];
				
				//On insère dans la fille d'attente
					//On créé l'en-tête
                                        if ($territoire == 1) {
                                            $from = 'Ensemble Ici <newsletter@ensembleici.fr>';
                                        }
                                        else {
                                            $from = 'Ensemble Ici <lettre.vdd@ensembleici.fr>';
                                        }
					if($table!="newsletter") //On retire le lien "non inscrit"
						$message = "<html><head></head><body>".preg_replace("#(\[\*\*codoff\*\*\])#i",$codoff,preg_replace("#(\[\*\*typoff\*\*\])#i",$typoff, preg_replace("#(\[\*\*idv\*\*\])#i",$idv, preg_replace("#<\!-- \[\*\*NON_INSCRIT\*\*\] -->(.*)<\!-- \[\*\*FIN_NON_INSCRIT\*\*\] -->#i","",$contenu_HTML))))."</body></html>";
					else
						$message = "<html><head></head><body>".preg_replace("#(\[\*\*codoff\*\*\])#i",$codoff,preg_replace("#(\[\*\*typoff\*\*\])#i",$typoff, preg_replace("#(\[\*\*idv\*\*\])#i",$idv, $contenu_HTML)))."</body></html>";
					// if($table=="utilisateur"){
						// $message = preg_replace("#<\!--\[NON_INSCRIT\]-->.*<\!--\[FIN_NON_INSCRIT\]-->#i","",$message);
					// }
                                        if ($territoire == 1) {
                                            $entetes = array( 'From'    => $from,
                                               'To'      => $resultat[$i]["m"],
                                               'Subject' => $objet,
                                               'X-Sender' => '<www.ensembleici.fr>',
//                                               'X-auth-smtp-user' => 'newsletter@ensembleici.fr',
                                                'X-auth-smtp-user' => 'contact@envolinfo.com',
                                               'X-Priority' => "3",
                                               'X-Unsubscribe-Web' => '<http://www.ensembleici.fr/desinscription.html?codoff='.$codoff.'&typoff='.$typoff,
                                               'X-Unsubscribe-Email' => '<mailto:unsubscribe@ensembleici.fr>',
                                               'X-Mailer' => 'PHP/'.phpversion(),
                                               'Content-Type' => 'text/html; charset=utf-8',
                                               'Return-path' => "-f".$from );
                                        }
                                        else {
                                            $entetes = array( 'From'    => $from,
                                               'To'      => $resultat[$i]["m"],
                                               'Subject' => $objet,
                                               'X-Sender' => '<www.ensembleici.fr>',
//                                               'X-auth-smtp-user' => 'lettre.vdd@ensembleici.fr',
                                                'X-auth-smtp-user' => 'contact@envolinfo.com',
                                               'X-Priority' => "3",
                                               'X-Unsubscribe-Web' => '<http://www.ensembleici.fr/desinscription.html?codoff='.$codoff.'&typoff='.$typoff,
                                               'X-Unsubscribe-Email' => '<mailto:unsubscribe@ensembleici.fr>',
                                               'X-Mailer' => 'PHP/'.phpversion(),
                                               'Content-Type' => 'text/html; charset=utf-8',
                                               'Return-path' => "-f".$from );
                                        }
                                        
					//On crée un message valide
					$mime =& new Mail_mime();
					// $mime->setTXTBody($message);
					$mime->setHTMLBody($message);
					$corps = $mime->get();
					$entetes = $mime->headers($entetes,true);
					$file_denvoi_de_mail->put( $from, $resultat[$i]["m"], $entetes, $corps );
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
		$reponse = array(false,utf8_encode("le numéro de lettre est erroné !"));
	}
}
else{
	$reponse = array(false,utf8_encode("le numéro de liste est erroné!"));
}
echo json_encode($reponse);
?>