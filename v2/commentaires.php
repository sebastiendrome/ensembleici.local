<?php	
	session_name("EspacePerso");
	session_start();	
	/*	echo $_SESSION['connexion_redirect_page']."</br>";
	echo $_SESSION['UserConnecte']."</br>" ;
	echo $_SESSION['UserConnecte_email']."</br>";
	echo $_SESSION['UserConnecte_id']."</br>";	*/    

	if ($_SESSION['UserConnecte'] != ""){
	//	echo "Utilisateur connecté</br>";		
	}
	else{
//		echo "Utilisateur déconnecté";
    }

	//echo $_SESSION['UserConnecte_id'];
	$no_utilisateur = $_SESSION['UserConnecte_id'];	
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>       
		<title>Commentaires</title>
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
		// Pour la redirection
			$_SESSION['connexion_redirect_page'] = "http://www.ensembleici.fr/forum/commentaires.php";
            
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
								<div class=\"boutons\">
									<input  type=\"submit\" class=\"boutonbleu ico-login\"  value=\"Connexion\"></input>
									&nbsp;&nbsp;<a href=\"http://www.ensembleici.fr/identification.html\" target=\"_blank\" >S'inscrire</a>
								</div>
						</fieldset>
					</form>"; 			
					
			}else{	
				 
				echo"<form  method=\"post\" class=\"formA marginlogin\" action=\"php/deconnexion.php\" name=\"deconnexion\"><fieldset>";
				echo $_SESSION['UserConnecte_email']."</br></br>";   
				echo "<input type=\"submit\" class=\"boutonbleu ico-login\"  name=\"deconnexion\" value=\"Déconnexion\"></input>";
				echo "</fieldset></form>";
		   }             
			echo "</div>";	
		echo "</div>"; 	
			
		// Temporel: sujet 0	o recuperarlo del hilo anterior por post
			if(!isset($_POST['no_sujet'])){
				$no_sujet = 0;
			}
					
			$rqt="SELECT *
					FROM `sujet`
					WHERE  no=:no";
			$result_sujet = $connexion->prepare($rqt);
			$result_sujet->execute(array(':no'=>$no_sujet)) or die ("Erreur 77 : ".$rqt."<br/>".print_r($result_sujet->errorInfo()));
			$tab_sujet= $result_sujet->fetchAll();
			
			$titre_sujet = $tab_sujet[0]['titre'];
			$description_sujet = $tab_sujet[0]['description'];			
			
		// TITRE ET DESCRIPTION DU FILS
		
		echo "<div>";
			echo "<div class=\"titrefils\">".$titre_sujet ."</div></br>";
			echo "<div class=\"soustitre\">".$description_sujet ."</div></br>";	
		echo "</div>";
		
		echo "<div class=\"divcenter\">";
		
			if ($_SESSION['UserConnecte'] != ""){
				// ajouter un commentaire						
					echo "<form method=POST action=\"commentaires.php?envoi=ok\" onSubmit=\"return jsVerifCommentaireCkeditor()\">";
						echo "<br>Titre: <input name=\"titre_message\" type=\"text\" id=\"titre_message\"  value=\"\"/>&nbsp;&nbsp; Auteur : <input name=\"pseudo\" type=\"text\" id=\"pseudo\"  value=\"\"/>";
						echo "<br><br><span style=\"display: block; padding-left: 30%;\"><span style=\"width: 700px; position: relative; display: block;\"><textarea class=\"ckeditor\" name=\"description\"  id=\"description\" rows=\"6\" cols=\"20\" cols=\"10\"></textarea></span></span><br>";						
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
				// $description_message =  nlToBr($_POST['description'.$noaccess]);
				 $description_message =  $_POST['description'.$noaccess];
				 
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
			}
					                                					
			// Recuperation des données du formulaire et modif
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
			}
            // fin de modif                               
                    
            // Suppresion
			if ($_GET['sup']=='ok'){
                $nosup= $_POST['no_message'];           				 
	
                $sqlp = "DELETE FROM `message`    
				WHERE no_message = :no_message
                OR message_message = :no_message";
			    $sup= $connexion->prepare($sqlp);
			    $sup->execute(array(
				':no_message'=>$nosup
			    )) or die ("Erreur ".__LINE__." : ".$sqlp);                 
			}
            // fin de modif               
                    
			// commentaires
				$sql = "SELECT *
							FROM `message` 
							WHERE no_sujet = :no_sujet
							AND message_message IS NULL
							ORDER BY date_creation DESC";			
				
				$result_messages = $connexion->prepare($sql);
				$result_messages->execute(array(':no_sujet'=>$no_sujet)) or die ("requete ligne 161: ".$sql);
				$tab_messages = $result_messages->fetchAll(PDO::FETCH_ASSOC);					
			
					echo"<div class=\"decaler\">";
						$index=0;				
						while($tab_messages[$index]){	
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
									// echo "<br>heure: ".$no_message;								
									echo "Par ".$tab_messages[$index]['pseudo'].", le ".$date." à ".$date_message[1]." ";
								echo "</div>";	          
                                
                                if ($_SESSION['UserConnecte_id'] == $tab_messages[$index]['no_utilisateur']){
                                    $no_message = $tab_messages[$index]['no_message'];
                                    $id_boutonmodif = 'boutonmodif'.$no_message;
                                    $id_divmodif = 'idmodif'.$no_message;
                                    echo "<div  class=\"bouton_repondre\"><input id=\"".$id_boutonmodif."\" type=\"submit\" class=\"boutonbleu ico-modifier\" value=\"Modifier\" onclick='jsAfficherFormulaire_modifsPrincipal(\"".$id_boutonmodif."\",\"".$id_divmodif."\",\"".$no_message."\",\"". $tab_messages[$index][pseudo]."\",\"".nlToBr($tab_messages[$index][description])."\",\"". $tab_messages[$index][titre_message]."\")'></div>"; 
                                    echo "<div class=\"decaler10\" id=\"".$id_divmodif."\"></div>";
                                    
                                    $id_boutonsupr= 'boutonsupr'.$no_message;
                                    $id_divsupr= 'idsupr'.$no_message;                                    
                                    echo "<div  class=\"bouton_repondre\"><form method=POST action=\"commentaires.php?sup=ok\"   onSubmit=\"return confirm('Voulez-vous supprimer ce message et toutes leurs réponses? ')\" >";		
                                        echo "<input type=\"hidden\" name=\"no_message\" id=\"no_message\" value=\"".$no_message."\"/>";
                                        echo "&nbsp;&nbsp;&nbsp;<input  class=\"boutonbleu ico-supprimer delete-evtstruct\" name=\"SuprCommentaire\" type=\"submit\" value=\"Supprimer\">";
                                    echo "</form></div>";				                                       
                                }
								
								echo "<div class=\"decaler10\"></br>".nlToBr($tab_messages[$index]['description'])."</br></br></div>";		
                                
                                // subconsulta para un tema determinado
                                    $subsql = "SELECT *
                                                FROM `message` 
                                                WHERE no_sujet = :no_sujet
                                                AND message_message = :no_message
                                                ORDER BY date_creation ASC";			
                                    
                                    $subresult_messages = $connexion->prepare($subsql);
                                    $subresult_messages->execute(array(':no_sujet'=>$no_sujet,':no_message'=>$no_message)) or die ("requete ligne 195: ".$sql);
                                    $subtab_messages = $subresult_messages->fetchAll(PDO::FETCH_ASSOC);	  
                                    
                                    $subindex=0;				
                                    while($subtab_messages[$subindex]){	                                                 
                                        echo "</br><div class=\"tabreponse\" >";	
                                                //echo "<div class=\"titre_commentaire\">".$subtab_messages[$subindex]['titre_message']."</div>";		
                                                echo "</br>           -----------------------------------------------------------------                      </br>";		
                                                $date_message= explode(' ',$subtab_messages[$subindex]['date_creation']);								
                                                $date_temp= explode('-',$date_message[0]);
                                                $jour = $date_temp[2];
                                                $mois= $date_temp[1];
                                                $annee= $date_temp[0];
                                                $date= $jour."-".$mois."-".$annee;												
                                                echo "Par ".$subtab_messages[$subindex]['pseudo'].", le ".$date." à ".$date_message[1]." ";                                                
                                                echo "</br></br>".nlToBr($subtab_messages[$subindex]['description'])."</br>";
												
												$no_message = $subtab_messages[$subindex]['no_message'];
												$id_boutonmodif = 'boutonmodif'.$no_message;
												$id_divmodif = 'idmodif'.$no_message;
												
												if ($_SESSION['UserConnecte_id'] == $subtab_messages[$subindex]['no_utilisateur']){
                                                    echo "<div  class=\"bouton_repondre\"><input type=\"submit\" class=\"boutonbleu ico-modifier\" id=\"".$id_boutonmodif."\" value=\"Modifier\" onclick='jsAfficherFormulaire_modifs(\"".$id_boutonmodif."\",\"".$id_divmodif."\",\"".$no_message."\",\"". $subtab_messages[$subindex][pseudo]."\",\"".nlToBr( $subtab_messages[$subindex][description])."\",\"". $subtab_messages[$subindex][titre_message]."\")'   ></div>"; 
                                                    echo "<div class=\"decaler10\" id=\"".$id_divmodif."\"></div>";
                                                    
                                                    $id_boutonsupr= 'boutonsupr'.$no_message;
                                                    $id_divsupr= 'idsupr'.$no_message;                                                   
                                                    echo "<div  class=\"bouton_repondre\"><form method=POST action=\"commentaires.php?sup=ok\"   onSubmit=\"return confirm('Voulez-vous supprimer ce message?')\" >";		
                                                        echo "<input type=\"hidden\" name=\"no_message\" id=\"no_message\" value=\"".$no_message."\"/>";
                                                        echo "&nbsp;&nbsp;&nbsp;<input  class=\"boutonbleu ico-supprimer delete-evtstruct\" name=\"SuprCommentaire\" type=\"submit\" value=\"Supprimer\">";
                                                    echo "</form></div>";
												}
												
                                        echo "</div>";
                                        $subindex++;
                                    }                          
                                // fin subconsulta                
                                
								if ($_SESSION['UserConnecte'] != ""){									
									echo "<br /><br /><br /><div  class=\"bouton_repondre\"><input id=\"".$id_bouton."\" type=\"submit\" class=\"boutonbleu ico-fleche\" value=\"Répondre\" onclick='jsAfficherFormulaire_reponse(\"".$id_bouton."\",\"".$id_div."\",\"".$no_message_principal."\",\"".$_SESSION['UserConnecte_id']."\",\"".$titre_message."\")'></div>";   
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
			//CKEDITOR.instances['description'].destroy();
			CKEDITOR.replace('description',{toolbar:'AutoA',uiColor:'#F0EDEA',language:'fr',width:'520',height:'200',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
		  }
		 </script>		 
	</BODY>	
</html>