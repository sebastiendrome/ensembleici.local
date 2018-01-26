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
//1. On vérifie les paramètres
if(!empty($_POST["contenu"])&&!empty($_POST["no"])&&!empty($_POST["p"])){
	$_POST["contenu"] = urldecode($_POST["contenu"]);
	//2. On vérifie la connexion
	if(est_connecte()){
		//3. On vérifie le pseudo
		$requete_utilisateur = "SELECT pseudo,no_contact FROM utilisateur WHERE no=:no";
		$tab_utilisateur = execute_requete($requete_utilisateur,array(":no"=>$_SESSION["utilisateur"]["no"]));
		if(count($tab_utilisateur)>0&&$tab_utilisateur[0]["pseudo"]!=""){
			//4. On insère le message dans la table de message.
			/*
			$date_courante = date("Y-m-d H:i:s");
			$requete_insert = "INSERT INTO messageForum(contenu,no_utilisateur_creation,date_creation,no_forum,no_message,afficher) VALUES(:c,:utilisateur,:d,:no_type,:no_msg,1)";
			$no_message = execute_requete($requete_insert,array(":c"=>$_POST["contenu"],":utilisateur"=>$_SESSION["utilisateur"]["no"],":d"=>$date_courante,":no_type"=>$_POST["no"],":no_msg"=>((!empty($_POST["no_msg"]))?$_POST["no_msg"]:0)));
			//5. On regarde si l'utilisateur n'est pas encore abonné au fil, on l'abonne
			$requete_est_abonne = "SELECT * FROM message_utilisateur WHERE no_utilisateur=:utilisateur AND no_sujet=:no";
			$param_est_abonne = array(":utilisateur"=>$_SESSION["utilisateur"]["no"],":no"=>$_POST["no"]);
			if(!empty($_POST["no_msg"])){
				$requete_est_abonne .= " AND no_message=:no_msg";
				$param_est_abonne[":no_msg"] = $_POST["no_msg"];
			}
			$tab_est_abonne = execute_requete($requete_est_abonne,$param_est_abonne);
			$inscription_fil = 0;
			if(count($tab_est_abonne)==0&&(!empty($_POST["no_msg"])||$_POST["abonnement"]==1)){ //On abonne s'il n'y a pas d'enregistrement, et qu'il s'agit d'un nouveau message avec abonnement à 1 ou d'un sous message
				$requete_abonnement = "INSERT INTO message_utilisateur(no_utilisateur,no_sujet,no_message,inscrit) VALUES(:utilisateur,:no,:no_msg,1)";
				$param_abonnement = array(":utilisateur"=>$_SESSION["utilisateur"]["no"],":no"=>$_POST["no"],":no_msg"=>((!empty($_POST["no_msg"]))?$_POST["no_msg"]:0));
				execute_requete($requete_abonnement,$param_abonnement);
				if($_POST["abonnement"]==1)
					$inscription_fil = 1;
			}
			if(empty($_POST["no_msg"])){ //Ici on abonne l'utilisateur au message qu'il vient de poster (si ce n'est pas la réponse à un message)
				$requete_abonnement = "INSERT INTO message_utilisateur(no_utilisateur,no_sujet,no_message,inscrit) VALUES(:utilisateur,:no,:no_msg,1)";
				$param_abonnement = array(":utilisateur"=>$_SESSION["utilisateur"]["no"],":no"=>$_POST["no"],":no_msg"=>$no_message);
				execute_requete($requete_abonnement,$param_abonnement);
			}
			//5. On récupère les abonnés au message.
			$requete_abonne = "SELECT utilisateur.email FROM utilisateur JOIN message_utilisateur ON message_utilisateur.no_utilisateur=utilisateur.no WHERE message_utilisateur.no_sujet=:no AND message_utilisateur.no_message=:no_msg AND message_utilisateur.no_utilisateur=:utilisateur AND inscrit=1";
			$tab_abonne = execute_requete($requete_abonne,array(":no"=>$_POST["no"],":no_msg"=>$_POST["no_msg"],":utilisateur"=>$_SESSION["utilisateur"]["no"]));
			//6. On envoie le mail aux abonnés
			
			//7. On envoi les notifications aux administrateurs
			*/
			function formater_objet($o){
				$o = html_entity_decode($o);
				$o = strip_tags($o);

				// $o = preg_replace("#\s([a-z])(\’|\')\s([a-z][a-z]+)#i", ' $1 $2', $o);
				// $o = preg_replace("#\s([a-z])\s(\’|\')\s([a-z][a-z]+)#i", ' $1 $2', $o);
				// $o = preg_replace("#\s([a-z])\s(\’|\')([a-z][a-z]+)#i", ' $1 $2', $o);
				// $o = preg_replace("#\s([a-z])\s([a-z][a-z]+)#i", ' $1 $2', $o);
				$o = str_replace("'"," ",$o);
				$o = str_replace('"',"",$o);
	
			// Pour les accents (a, e, i, o, u)
				$o = preg_replace('#[ãàâä]#iu', 'a', $o); //(&a[a-z]{3,6};)
				$o = preg_replace('#[éèëê]#iu', 'e', $o); //
				$o = preg_replace('#[õòöô]#iu', 'o', $o); //(&o[a-z]{3,6};)
				$o = preg_replace('#[ìîî]#iu', 'i', $o); //(&i[a-z]{3,6};)
				$o = preg_replace('#[ùûü]#iu', 'u', $o); //(&u[a-z]{3,6};)
				$o = preg_replace('#ç#iu', 'c', $o); //(&ccedil;)
	
				$o = trim($o);
				return $o;
			}
			//4. On insère le message dans la table message
			$date_courante = date("Y-m-d H:i:s");
			if($_POST["p"]=="editorial")
				$table = "editorial";
			else if($_POST["p"]=="agenda"||$_POST["p"]=="evenement")
				$table = "evenement";
			else if($_POST["p"]=="structure")
				$table = "structure";
			else if($_POST["p"]=="petiteannonce"||$_POST["p"]=="petite-annonce")
				$table = "petiteannonce";
			else
				$table = "forum";
			$NO = $_POST["no"];
			
			if(empty($_POST["no_msg"])){
				$table_notification = $table;
				$no_notification = $NO;
			}
			else{
				$table_notification = "message";
				$no_notification = $_POST["no_msg"];
			}
			
			$requete_message = "INSERT INTO message(contenu,no_utilisateur_creation,date_creation,no_".$table_notification.",afficher) VALUES(:contenu,:no_utilisateur,:date,:no,1)";
			$param_message = array(":contenu"=>$_POST["contenu"],":no_utilisateur"=>$_SESSION["utilisateur"]["no"],":date"=>$date_courante,":no"=>$no_notification);
			$no_message = execute_requete($requete_message,$param_message);
			
			if(empty($_POST["no_msg"])){ //Ce n'est pas la réponse à un message, on inscrit l'utilisateur au sujet, à l'événement, etc.
				$requete_notification = "SELECT ".$table."_notification.etat FROM ".$table."_notification WHERE ".$table."_notification.no_utilisateur=:no_utilisateur AND ".$table."_notification.no_".$table."=:no";
				$tab_notification = execute_requete($requete_notification,array(":no"=>$_POST["no"],":no_utilisateur"=>$_SESSION["utilisateur"]["no"]));
				
				if(empty($tab_notification)){ //On update alors l'entrée
					$requete_insert = "INSERT INTO ".$table."_notification(no_".$table.",no_utilisateur,etat,date_creation) VALUES(:no,:no_utilisateur,1,:date)";
					execute_requete($requete_insert,array(":no"=>$_POST["no"],":no_utilisateur"=>$_SESSION["utilisateur"]["no"],":date"=>date("Y-m-d H:i:s")));
					$inscription_sujet_complet = true;
				}
				else{
					$inscription_sujet_complet = false;
				}
				
				$no_message_inscription = $no_message;
			}
			else
				$no_message_inscription = $no_notification;
						
			//5. On regard si l'utilisateur n'est pas encore abonné au message concerné (soit le message qu'il vient de poster, soit celui auquel il vient de répondre).
			$requete_notification = "SELECT message_notification.etat FROM message_notification WHERE message_notification.no_utilisateur=:no_utilisateur AND message_notification.no_message=:no";
			$tab_notification = execute_requete($requete_notification,array(":no"=>$no_message_inscription,":no_utilisateur"=>$_SESSION["utilisateur"]["no"]));
			if(empty($tab_notification)){ //On insère alors l'entrée
				$requete_insert = "INSERT INTO message_notification(no_message,no_utilisateur,etat,date_creation) VALUES(:no,:no_utilisateur,1,:date)";
				execute_requete($requete_insert,array(":no"=>$no_message_inscription,":no_utilisateur"=>$_SESSION["utilisateur"]["no"],":date"=>date("Y-m-d H:i:s")));
			}
			
			//6. On envoie le mail aux abonnés
			$champ_titre = ($table!="structure")?"titre":"nom";
			$requete_titre = "SELECT ".$table.".".$champ_titre." AS titre, ".$table.".no_ville, ".$table.".no_utilisateur_creation AS no_utilisateur, villes.nom_ville_maj AS ville FROM ".$table." JOIN villes ON ".$table.".no_ville=villes.id WHERE ".$table.".no=:no";
			$tab_fiche = execute_requete($requete_titre,array(":no"=>$NO));
			
			$url_desinscription_generique = $root_site."desinscription.php?t=".$table_notification."&n=".$no_notification."&u=[**NO_UTILISATEUR**]";
			$url_page = $root_site.$_POST["p"].".".url_rewrite($tab_fiche[0]["ville"]).".".url_rewrite($tab_fiche[0]["titre"]).".".$tab_fiche[0]["no_ville"].".".$NO.".html";
			$url_message = $url_page."#m-".$no_message_inscription;
			$url_supprression = $root_site.'03_ajax/delete_message.php?no='.$no_message;
			
			$objet = "<b>".$_SESSION["utilisateur"]["pseudo"]."</b>";
			if($_POST["p"]!="forum"){ //C'est une fiche
				if($table_notification=="message"){
					$objet .= " a répondu à [**COMMENTAIRE_FICHE**]";
				}
				else
					$objet .= " a ajouté un commentaire";
			}
			else{
				if($table_notification=="message")
					$objet .= " a commenté [**COMMENTAIRE_FORUM**]";
				else
					$objet .= " a posté un message";
			}
			$objet .= " dans : <b>".$tab_fiche[0]["titre"]."</b>";
			//On récupère les header et footer html
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_header.php");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			$HEADER_HTML = curl_exec($ch);
			curl_close($ch);
			//On récupère les header et footer html
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_footer.php");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			$FOOTER_HTML = curl_exec($ch);
			curl_close($ch);
				//6.1. On récupère la liste des abonnés
				$requete_abonnes = "SELECT utilisateur.email, utilisateur.no FROM utilisateur JOIN ".$table_notification."_notification ON ".$table_notification."_notification.no_utilisateur=utilisateur.no WHERE ".$table_notification."_notification.no_".$table_notification."=:no AND ".$table_notification."_notification.etat=1 AND ".$table_notification."_notification.no_utilisateur<>:utilisateur";
				$tab_abonnes = execute_requete($requete_abonnes,array(":no"=>$no_notification,":utilisateur"=>$_SESSION["utilisateur"]["no"]));
				//6.2. Pour chacun d'entre eux, on envoi la notification
				for($i=0;$i<count($tab_abonnes);$i++){
					$url_desinscription = str_replace("[**NO_UTILISATEUR**]",$tab_abonnes[$i]["no"],$url_desinscription_generique);
					
					if($tab_abonnes[$i]["no"]!=$tab_fiche[0]["no_utilisateur_creation"])
						$OBJET = str_replace("[**COMMENTAIRE_FICHE**]","un commentaire auquel vous avez participé",str_replace("[**COMMENTAIRE_FORUM**]","un message",$objet));
					else
						$OBJET = str_replace("[**COMMENTAIRE_FICHE**]","votre commentaire",str_replace("[**COMMENTAIRE_FORUM**]","votre message",$objet));
					$CONTENU_MAIL_HTML = '<p>'.$OBJET.'</p>';
					
					$CONTENU_MAIL_HTML .= '<div style="border:1px solid grey;">'.$_POST["contenu"].'</div>';
					
					$CONTENU_MAIL_HTML .= '<p>';
					$CONTENU_MAIL_HTML .= 'Pour lire la suite ou répondre <a href="'.$url_message.'">cliquez ici</a><br />';
					if($table_notification=="message")
						$CONTENU_MAIL_HTML .= 'Pour ne plus recevoir de notifications par courriel pour ce '.(($table!="forum")?'commentaire':'message').', <a href="'.$url_desinscription.'">cliquez ici</a>';
					else
						$CONTENU_MAIL_HTML .= 'Pour ne plus recevoir les notifications par courriel, désactivez la cloche sur <a href="'.$url_page.'">cette page</a>.';
					$CONTENU_MAIL_HTML .= '</p>';
					
					$CONTENU_MAIL_HTML = $HEADER_HTML.$CONTENU_MAIL_HTML.$FOOTER_HTML;
					
					$OBJET = strip_tags($OBJET);
					$CONTENU_MAIL_TXT .= $OBJET.'\r\n\r\n'.strip_tags($_POST["contenu"]).'\r\n\r\nPour lire la suite ou répondre '.$url_message.'\r\n';
					if($table_notification=="message")
						$CONTENU_MAIL_TXT .= 'Pour ne plus recevoir de notifications par courriel pour ce '.(($table!="forum")?'commentaire':'message').', ouvrez ce lien : '.$url_desinscription.'\r\n';
					else
						$CONTENU_MAIL_TXT .= 'Pour ne plus recevoir les notifications par courriel, désactivez la cloche sur cette page : '.$url_page.'\r\n';
					
					$MAIL_DESTINATAIRE = $tab_abonnes[$i]["email"];
					
					$OBJET = formater_objet($OBJET);
					
					//On envoi le message
					include "../01_include/envoyer_un_mail.php";
				}
			//7. On envoie les notifications aux administrateurs
			$OBJET = str_replace("[**COMMENTAIRE_FICHE**]","un commentaire",str_replace("[**COMMENTAIRE_FORUM**]","un message",$objet));
			$CONTENU_MAIL_HTML = '<p>'.$OBJET.'</p>';
			$CONTENU_MAIL_HTML .= '<p style="border:1px solid grey;">'.$_POST["contenu"].'</p>';
			$CONTENU_MAIL_HTML .= '<p>';
				$CONTENU_MAIL_HTML .= '<a href="'.$url_message.'">[s\'y rendre]</a> - <a href="'.$url_supprression.'">[supprimer]</a>';
			$CONTENU_MAIL_HTML .= '</p>';
			$CONTENU_MAIL_HTML = $HEADER_HTML.$CONTENU_MAIL_HTML.$FOOTER_HTML;
			
			$OBJET = strip_tags($OBJET);
			
			$CONTENU_MAIL_TXT = $OBJET;
			$CONTENU_MAIL_TXT .= '\r\n\r\n'.strip_tags($_POST["contenu"]);
			$CONTENU_MAIL_TXT .= '\r\n\r\n';
			$CONTENU_MAIL_TXT .= 'S\'y rendre : '.$url_message.'\r\nSupprimer : '.$url_supprression;
			
			$OBJET = "[".$_POST["p"]."] [message] ".formater_objet($OBJET);
			
			$MAIL_DESTINATAIRE = $email_forum;
			
			include "../01_include/envoyer_un_mail.php";
			
			
			$reponse = array(	"no_message"=>$no_message,
								"utilisateur"=>$tab_utilisateur[0]["pseudo"],
								"date"=>datefr($date_courante,true),
								"no_contact"=>$tab_utilisateur[0]["no_contact"],
								"inscription_sujet_complet"=>$inscription_sujet_complet);
			$return = array(true,$reponse);
		}
		else{
			$return = array(false,"Vous devez avoir un pseudo pour poster un message");
		}
	}
	else{
		$return = array(false,"Vous devez être connecté pour poster un message");
	}
}
else{
	$return = array(false,"Vous ne pouvez pas poster un message vide");
}
echo json_encode($return);
?>
