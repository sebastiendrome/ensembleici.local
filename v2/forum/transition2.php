<?php	
	session_name("EspacePerso");
	session_start();
	header('Content-Type: text/html; charset=UTF-8');
	include "../01_include/_var_ensemble.php";	   

	/*
	if ($_SESSION['UserConnecte'] != ""){
		echo "Utilisateur connecté</br>";		
	}
	else{
			echo "Utilisateur déconnecté";
    }
    */

	$no_utilisateur = $_SESSION['UserConnecte_id'];		
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="fr"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="fr"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="fr"> <![endif]-->
<!--[if gt IE 8]><!--> 

<html class="no-js" lang="fr"> <!--<![endif]-->
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<title>Forum Transition</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	    <link rel="stylesheet" type="text/css" href="css/forum.css">
		<link rel="stylesheet" type="text/css" href="../css/adminstyle.css">		
        <link rel="stylesheet" type="text/css" href="../css/style.css">
		<link rel="stylesheet" type="text/css" href="../css/formulaires.css">
		<link href="http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,700" type="text/css" rel="stylesheet"></link>
		<script type="text/javascript" src="js/commentaires.js"></script>	
		<script type="text/javascript" src="js/checkemail.js"></script>	
        <script type="text/javascript" src="js/functions.js"></script>	
	</head>
	<BODY>	

<?php			
		// Connexion à la base de données
			require ('php/_connect.php');		
			require ('php/fonctions.php');
			
			// no_sujet
			if(isset($_POST['no_sujet'])){
				$no_sujet = $_POST['no_sujet'];
			}
			elseif(isset($_GET['id_ville'])){
				$no_sujet = $_GET['id_ville'];
			}
			else{
				// si no hay un sujet asignado se hace una redireccion
				$no_sujet = 1;			
			}

		// verification des droits de l'utilisateur	
		$sql = "SELECT *
			FROM `utilisateur` 
			WHERE no = :no_utilisateur";			
				
		$result_utilisateur = $connexion->prepare($sql);
		$result_utilisateur->execute(array(':no_utilisateur'=>$no_utilisateur)) or die ("requete ligne 25: ".$sql);
		$tab_utilisateur = $result_utilisateur->fetchAll(PDO::FETCH_ASSOC);	
		if($tab_utilisateur[0]['droits'] != null){
			$droits = $tab_utilisateur[0]['droits'];			
		}
		else{$droits = 'N';}
			
		// Pour la redirection
			$_SESSION['connexion_redirect_page'] = "http://www.ensembleici.fr/forum/transition.php";
            
         echo "<div class=\"entete\">";
            echo "<div class=\"bandeau\"></div>";               
			echo "<div class=\"login\">";
			if ($_SESSION['UserConnecte'] == ""){
				// login	
				echo"<form id=\"EDconnexion\" class=\"formA marginlogin\" accept-charset=\"UTF-8\" method=\"post\" action=\"../01_include/connexion_espacePerso.php\" name=\"EDconnexion\"  onSubmit=\"return jsVerifConnex()\" >                   
						<fieldset>							
									<label for=\"login\"> Email  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  </label>
									<input id=\"login\" class=\"validate[required,custom[email]]\" type=\"text\" value=\"\" name=\"login\"></input></br>							
									<label for=\"mdp\"> Mot de passe </label>
									<input id=\"mdp\" class=\"validate[required]\" type=\"password\" value=\"\" name=\"mdp\"></input>					
								<div style=\"margin-left:50px;\">
									<input  type=\"submit\" class=\"boutonbleu ico-login\"  value=\"Connexion\"></input>
									&nbsp;&nbsp;<a href=\"http://www.ensembleici.fr/identification.html\" target=\"_blank\" >S'inscrire</a>
								</div>
						</fieldset>
					</form>"; 			
					
			}else{	
				 
				echo"<form  method=\"post\" class=\"formA marginlogin\" action=\"php/deconnexion.php\" name=\"deconnexion\"><fieldset>";
				echo $_SESSION['UserConnecte_email']."</br></br>";   
				echo "<input type=\"hidden\" name=\"redirec\" id=\"redirec\" value=\"http://www.ensembleici.fr/forum/transition.php\"/> ";
				echo "<input type=\"submit\" class=\"boutonbleu ico-login\"  name=\"deconnexion\" value=\"Déconnexion\"></input>";
				echo "</fieldset></form>";
				
				//On regarde maintenant si l'utilisateur est inscrit au sujet (checkbox)
				$sql_checked = "SELECT *
				FROM message_utilisateur 
				WHERE no_utilisateur = :no_utilisateur
				AND no_sujet = :no_sujet AND no_message=0 AND inscrit=1";			
					
				$result_checked = $connexion->prepare($sql_checked);
				$result_checked->execute(array(':no_utilisateur'=>$no_utilisateur, ':no_sujet'=>$no_sujet)) or die ("requete ligne 25: ".$sql_checked);
				$tab_checked = $result_checked->fetchAll();	
				echo '<input type="checkbox" id="input_abonne" name="abonne"'.((count($tab_checked)>0)?' checked="checked"':'').' value="valor1" onclick="jsInscriptionDesinscriptionDesAlerts('.$no_utilisateur.', '.$no_sujet.', 0, this.checked);" /><label for="input_abonne">&nbsp; Recevoir un mail lorsqu\'un &nbsp;<br />message est ajouté dans ce forum &nbsp;&nbsp; </label><br />';
				// fin checkbox
		   }             
			echo "</div>";	
		echo "</div>"; 	
			
			/*if(!isset($_POST['no_sujet'])){
				$no_sujet = 1;
			}*/
					
			$rqt="SELECT *
					FROM `sujet`
					WHERE  no=:no";
			$result_sujet = $connexion->prepare($rqt);
			$result_sujet->execute(array(':no'=>$no_sujet)) or die ("Erreur 100 : ".$rqt."<br/>".print_r($result_sujet->errorInfo()));
			$tab_sujet= $result_sujet->fetchAll();
			
			$titre_sujet = $tab_sujet[0]['titre'];
			$description_sujet = $tab_sujet[0]['description'];			
			
		// TITRE ET DESCRIPTION DU FILS
		
		echo "<div class=\"sujet\" ><br /><br /><br />";			
			echo "<div class=\"descriptionsujet\">";			
				echo "<div class=\"titrefils\">".nlToBr($titre_sujet) ."</div></br>";
				echo "<div style=\"float:left;\"><IMG SRC=\"images/transition.jpg\" ALT=\"Transition\" TITLE=\"Transition\" WIDTH=\"175\" HEIGHT=\"175\"></div>";
				echo "<div class=\"soustitre\">".nlToBr($description_sujet) ."</div></br>";	
			echo "</div><br />";	
			echo "<div class=\"clear\" ></div>";	
		echo "</div>";
		
		echo "<div class=\"divcenter\">";
		
			if ($_SESSION['UserConnecte'] != ""){
				// ajouter un commentaire						
					echo "<form method=POST action=\"transition.php?envoi=ok\" onSubmit=\"return jsVerifCommentaireCkeditor()\">";
						echo "<br><span style=\"display: block; padding-left: 27%;\"><span style=\"width: 700px; position: relative; display: block;\">Titre: <input name=\"titre_message\" type=\"text\" id=\"titre_message\" size=\"55\" value=\"\"/>&nbsp;&nbsp; Auteur : <input name=\"pseudo\" type=\"text\" id=\"pseudo\" size=\"18\" value=\"\"/></span></span>";
						echo "<br><br><span style=\"display: block; padding-left: 30%;\"><span style=\"width: 700px; position: relative; display: block;\"><textarea name=\"description\"  id=\"description\"></textarea></span></span><br>";						
						echo "<input type=\"hidden\" name=\"no_utilisateur\" id=\"no_utilisateur\" value=\"".$_SESSION['UserConnecte_id']."\"/> ";
						echo "<input type=\"hidden\" name=\"no_sujet\" id=\"no_sujet\" value=\"".$no_sujet."\"/> ";
						echo "<input type=\"hidden\" name=\"utilisateur_connecte\" id=\"utilisateur_connecte\" value=\"".$_SESSION['UserConnecte']."\"/> ";
						echo "&nbsp;&nbsp;&nbsp;<input  type=\"submit\" name=\"AjouterCommentaire\" class=\"boutonbleu ico-ajout\" value=\"Ajouter\"> "; //  onclick jsAjouterCommentaire(no_fichier, cible) enviar email  c  y tipo button
					echo "</form>";	
			}else{
				echo "Merci de vous connecter pour participer à la discussion";
			}
										
			// Recuperation des données du formulaire et insertion
			if ($_GET['envoi']=='ok'){
                            
				if ($_GET['messagemessage']!=null){
					$messagemessage = $_GET['messagemessage'];
					$noaccess=$messagemessage;
				} else{
					$messagemessage=null;
					$noaccess='';
				}
				
				$no_sujet = $_POST['no_sujet'];
				$titre_message = "titre_message";
				$titre_message .= $noaccess;
				$titre_message = $_POST[$titre_message];
				
				$no_utilisateur = $_POST['no_utilisateur'.$noaccess];
				$pseudo = $_POST['pseudo'.$noaccess];				
				$description_message =  $_POST['description'.$noaccess];
				if ($_GET['reponse']=='ok'){$description_message =  $_POST['descriptionReponse'.$noaccess];}			 
				 
				// insertion		
				$sql = "INSERT INTO `message` (
					`no_sujet`,
					`no_utilisateur`,
					`description`,
					`titre_message`,
					`message_message`,					
					`pseudo`
					) VALUES (
					:no_sujet,
					:no_utilisateur,
					:description,
					:titre_message,
					:message_message,
					:pseudo
					)";

				$insert = $connexion->prepare($sql);
				$insert->execute(array(
					':no_sujet'=>$no_sujet,
					':no_utilisateur'=>$no_utilisateur,
					':description'=>$description_message,
					':titre_message'=>$titre_message,
					':message_message'=>$messagemessage,
					':pseudo'=>$pseudo
				)) or die ("Erreur 60 : ".$sql."<br/>".print_r($insert->errorInfo()));
				
				if ($insert){
					$no_message_insert = $connexion->lastInsertId();
					
					// On vérifie que le couple no_utilisateur,no_message existe dans la tableau utilisateur_message (abonnés au sujet/message)
					include "php/verifier_inscrit_sujet.php";
					// On envoi ensuite l'email aux inscrits au message/sujet.
					include "php/envoyer_mail.php";					
				}
			}
			// fin d'insertion
					                                					
			// Recuperation des données du formulaire et modification
			if ($_GET['modif']=='ok'){
                $nomodif= $_GET['nomodif'];                
				$pseudo = $_POST['modifpseudo'.$nomodif];
                $description=  $_POST['modifdescript'.$nomodif];
				$titre=  $_POST['modiftitre'.$nomodif];

                // update		
                $sql = "UPDATE `message` 
				SET pseudo = :pseudo,
				description = :description,
                titre_message = :titre_message
				WHERE no_message = :no_message";
			    $update = $connexion->prepare($sql);
			    $update->execute(array(
				':pseudo'=>$pseudo,
				':description'=>$description,
                ':titre_message'=>$titre,
				':no_message'=>$nomodif
			    )) or die ("Erreur ".__LINE__." : ".$sql);   
				
				if ($update){
					//envoi de l'email de modification
					$sujet = "ensembleici.fr - Ajout d'un commentaire";
					$mail_exp = $email_admin.", olivier@africultures.com, forum@ensembleici.fr,"; 
					$mail_from = $_SESSION['UserConnecte_email'];
					$message = "Bonjour,<br>
								Voici le message qui vient de être modifié.
								<br/><br/>
								Forum : Transition<br>
								&nbsp;&nbsp;&nbsp;- no message : <b>".$nomodif."</b><br>
								&nbsp;&nbsp;&nbsp;- Titre : <b>".$titre."</b><br>
								&nbsp;&nbsp;&nbsp;- E-mail : <b>".$_SESSION['UserConnecte_email']."</b><br>
								&nbsp;&nbsp;&nbsp;- Par : <b>".$pseudo."</b><br><br><br>								
								&nbsp;&nbsp;".$description."								
								<br/><br/><br/>";
					$message = $emails_header.$message.$emails_footer;
					$boundary = "-----=" . md5( uniqid ( rand() ) );
					$headers = "From: $mail_from \n"; 
					$headers .="Reply-To:".$mail_exp.""."\n"; 
					$headers .= "X-Mailer: PHP/".phpversion()."\n";
					$headers .= "MIME-Version: 1.0\n";
					$headers .= "Content-Type: text/html; charset='Iso-8859-1'; boundary=\"$boundary\"";
					$headers .='Content-Transfer-Encoding: quoted-printable';
					$mail=strtolower($mail_exp);
					mail($mail,$sujet,$message,$headers);
				}				
			}
            // fin de modification                               
                    
            // Suppresion
			if ($_GET['sup']=='ok'){
				$nosup= $_POST['no_message'];  
				$no_sujet_sup = 1;	
 
				$sqlsup = "UPDATE `message` 
				SET no_sujet = :no_sujet_sup
				WHERE no_message = :no_message
				OR message_message = :no_message";
			    $update = $connexion->prepare($sqlsup);
			    $update->execute(array(
				':no_message'=>$nosup,
				':no_sujet_sup'=>$no_sujet_sup
			    )) or die ("Erreur ".__LINE__." : ".$sqlsup);  				
			}
            // fin de Suppresion               
                    
			// commentaires
				$sql = "SELECT *
							FROM `message` 
							WHERE no_sujet = :no_sujet
							AND message_message IS NULL
							ORDER BY date_creation DESC";			
				
				$result_messages = $connexion->prepare($sql);
				$result_messages->execute(array(':no_sujet'=>$no_sujet)) or die ("requete ligne 227: ".$sql);
				$tab_messages = $result_messages->fetchAll(PDO::FETCH_ASSOC);					
			
					echo"<div class=\"decaler\">";
						$index=0;				
						while($tab_messages[$index]){	 // hilo principal
							// no_message actuel
							$no_message = $tab_messages[$index]['no_message'];
                            $no_message_principal =  $no_message;
							$id_bouton= "bouton_repondre".$no_message;	
							$id_div = "rep".$no_message;
							$titre_message = $tab_messages[$index]['titre_message'];
							
							echo "</br></br><div class=\"affichage_commentaires\" id=\"affichage_commentaires".$no_message."\">";	
								echo "<div class=\"entete_commentaire\">";
									echo "<div class=\"titre_commentaire\">".$tab_messages[$index]['titre_message']."</div>";		
									$date_message= explode(' ',$tab_messages[$index]['date_creation']);								
									$date_temp= explode('-',$date_message[0]);
									$jour = $date_temp[2];
									$mois= $date_temp[1];
									$annee= $date_temp[0];
									$date= $jour."-".$mois."-".$annee;													
									echo "<div >Par ".$tab_messages[$index]['pseudo'].", le ".$date." à ".$date_message[1]." </div>";
								echo "</div>";	          
                                
                                //on regarde si l'utilisateur est inscrit à ce message.
								$req_estInscrit = "SELECT * FROM message_utilisateur WHERE no_utilisateur=:nou AND no_message=:nom AND no_sujet=:nos AND inscrit=1";
								$res_estInscrit = $connexion->prepare($req_estInscrit);
								$res_estInscrit->execute(array(":nou"=>$no_utilisateur,":nos"=>$no_sujet,":nom"=>$no_message));
								$tab_estInscrit = $res_estInscrit->fetchAll();
                                
                                if ($_SESSION['UserConnecte_id'] == $tab_messages[$index]['no_utilisateur'] || $droits == 'A'){
                                    $no_message = $tab_messages[$index]['no_message'];
                                    $id_boutonmodif = 'boutonmodif'.$no_message;
                                    $id_divmodif = 'idmodif'.$no_message;								
									echo "<div  class=\"bouton_repondre\"><input type=\"submit\" class=\"boutonbleu ico-modifier\" id=\"".$id_boutonmodif."\" value=\"Modifier\" onclick=\"jsAfficherFormulaire_modifsPrincipal('".$id_boutonmodif."','".$id_divmodif."','".$no_message."','". rawurlencode(htmlspecialchars($tab_messages[$index][pseudo],ENT_QUOTES))."','".htmlspecialchars(nlToBrSimple($tab_messages[$index][description]),ENT_QUOTES)."','".rawurlencode(htmlspecialchars($tab_messages[$index][titre_message],ENT_QUOTES))."')\"></div>"; 
                                    echo "<div class=\"decaler10\" id=\"".$id_divmodif."\"></div>";
                                    
                                    $id_boutonsupr= 'boutonsupr'.$no_message;
                                    $id_divsupr= 'idsupr'.$no_message;                                    
                                    echo "<div  class=\"bouton_repondre\"><form method=POST action=\"transition.php?sup=ok\"   onSubmit=\"return confirm('Voulez-vous supprimer ce message et toutes leurs réponses? ')\" >";		
                                        echo "<input type=\"hidden\" name=\"no_message\" id=\"no_message\" value=\"".$no_message."\"/>";
                                        echo "&nbsp;&nbsp;&nbsp;<input  class=\"boutonbleu ico-supprimer delete-evtstruct\" name=\"SuprCommentaire\" type=\"submit\" value=\"Supprimer\">";
                                    echo "</form></div>";				                                       
                                }								
								echo "<div class=\"decaler10\"></br>".$tab_messages[$index]['description']."</br></div><div></br></div>";		
                                
                                // sub requete pour un theme determiné
                                    $subsql = "SELECT *
                                                FROM `message` 
                                                WHERE no_sujet = :no_sujet
                                                AND message_message = :no_message
                                                ORDER BY date_creation ASC";			
                                    
                                    $subresult_messages = $connexion->prepare($subsql);
                                    $subresult_messages->execute(array(':no_sujet'=>$no_sujet,':no_message'=>$no_message)) or die ("requete ligne 276: ".$sql);
                                    $subtab_messages = $subresult_messages->fetchAll(PDO::FETCH_ASSOC);	  
                                    
                                    $subindex=0;				
                                    while($subtab_messages[$subindex]){	            // hilo respuesta                                      
                                        echo "</br><div class=\"tabreponse\" >";		
                                                echo "</br>           -----------------------------------------------------------------                      </br>";		
                                                $date_message= explode(' ',$subtab_messages[$subindex]['date_creation']);								
                                                $date_temp= explode('-',$date_message[0]);
                                                $jour = $date_temp[2];
                                                $mois= $date_temp[1];
                                                $annee= $date_temp[0];
                                                $date= $jour."-".$mois."-".$annee;												
                                                echo "<div class=\"petitTitre\">Par ".$subtab_messages[$subindex]['pseudo'].", le ".$date." à ".$date_message[1]." </div>";     
                                                echo "</br></br>".$subtab_messages[$subindex]['description']."</br>";
												
												$no_message = $subtab_messages[$subindex]['no_message'];
												$id_boutonmodif = 'boutonmodif'.$no_message;
												$id_divmodif = 'idmodif'.$no_message;
												if ($_SESSION['UserConnecte_id'] == $subtab_messages[$subindex]['no_utilisateur'] || $droits == 'A'){
                                                    echo "<div  class=\"bouton_repondre\"><input type=\"submit\" class=\"boutonbleu ico-modifier\" id=\"".$id_boutonmodif."\" value=\"Modifier\" onclick=\"jsAfficherFormulaire_modifs('".$id_boutonmodif."','".$id_divmodif."','".$no_message."','".rawurlencode(htmlspecialchars($subtab_messages[$subindex][pseudo],ENT_QUOTES))."','".htmlspecialchars(nlToBrSimple($subtab_messages[$subindex][description]),ENT_QUOTES)."','". rawurlencode(htmlspecialchars($subtab_messages[$subindex][titre_message],ENT_QUOTES))."')\"></div>"; 
                                                    echo "<div class=\"decaler10\" id=\"".$id_divmodif."\"></div>";
                                                    
                                                    $id_boutonsupr= 'boutonsupr'.$no_message;
                                                    $id_divsupr= 'idsupr'.$no_message;                                                   
                                                    echo "<div  class=\"bouton_repondre\"><form method=POST action=\"transition.php?sup=ok\"   onSubmit=\"return confirm('Voulez-vous supprimer ce message?')\" >";		
                                                        echo "<input type=\"hidden\" name=\"no_message\" id=\"no_message\" value=\"".$no_message."\"/>";
                                                        echo "&nbsp;&nbsp;&nbsp;<input  class=\"boutonbleu ico-supprimer delete-evtstruct\" name=\"SuprCommentaire\" type=\"submit\" value=\"Supprimer\">";
                                                    echo "</form></div>";
												}												
                                        echo "</div>";
                                        $subindex++;
                                    }                          
                                // fin subconsulta                
                                
								if ($_SESSION['UserConnecte'] != ""){									
									echo "<br /><br /><br /><div  class=\"bouton_repondre\"><input id=\"".$id_bouton."\" type=\"button\" class=\"boutonbleu ico-fleche\" value=\"Répondre\" onclick=\"jsAfficherFormulaire_reponse('".$id_bouton."','".$id_div."',".$no_message_principal.",".$_SESSION['UserConnecte_id'].",'".rawurlencode(htmlspecialchars($titre_message,ENT_QUOTES))."',".$no_sujet.")\"></div>"; 
									echo '<br/><div style="text-align:center;"><input type="checkbox"'.((count($tab_estInscrit)==0)?'':' checked="checked"').' id="inscription_message'.$no_message.'" onclick="jsInscriptionDesinscriptionDesAlerts('.$no_utilisateur.', '.$no_sujet.', '.$no_message.', this.checked)" /><label for="inscription_message'.$no_message.'">M\'avertir des réponses apportées à ce message</label></div>';
									echo "<div class=\"decaler10\" id=\"".$id_div."\"></div>";
								}
							echo "</div>";	
							$index++;
						}	
					echo"</div>";	
		echo "</div>";
?>	
		<script type="text/javascript" src="../js/ckeditor/ckeditor.js"></script><script type="text/javascript">

		  window.onload = function()
		  {
			if(CKEDITOR.instances["description"]){
				CKEDITOR.instances["description"].destroy();
			}
			CKEDITOR.replace('description',{toolbar:'AutoA',uiColor:'#F0EDEA',language:'fr',width:'650',height:'200',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
		  }

		 </script>		 
	</BODY>	
</html>
