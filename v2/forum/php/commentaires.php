<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>       
		<title>Commentaires</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	    <link rel="stylesheet" type="text/css" href="csstest.css">
		<link rel="stylesheet" type="text/css" href="csstest2.css">	
		<script type="text/javascript" src="js/commentaires.js"></script>	
		<script type="text/javascript" src="js/checkemail.js"></script>	
	</head>

	<BODY>	

<?php			
		//connexion à la base de données
			require ('php/_connect.php');
			
			
		// Temporal: sujet 0	o recuperarlo del hilo anterior por post
			if(!isset($_POST['no_sujet'])){
				$no_sujet = 0;
				// echo "carga nosujet a ".$no_sujet;
			}
					
			$rqt="SELECT *
					FROM `sujet`
					WHERE  no=:no";
			$result_sujet = $connexion->prepare($rqt);
			$result_sujet->execute(array(':no'=>$no_sujet)) or die ("Erreur 24 : ".$rqt."<br/>".print_r($result_sujet->errorInfo()));
			$tab_sujet= $result_sujet->fetchAll();
			
			$titre_sujet = $tab_sujet[0]['titre'];
			$description_sujet = $tab_sujet[0]['description'];			
			
		// TITULO Y DESCRIPCION DEL HILO		
			echo $titre_sujet ."</br>";
			echo $description_sujet ."</br>";
			
		// login

			
		// ajouter un commentaire						
			echo "<form method=POST action=\"commentaires.php\">"; 
				echo "<br>Titre du commentaire: <input name=\"titre_message\" type=\"text\" id=\"titre_message\"  value=\"\"/>";
				echo "<br><br>Texte: <br><textarea name=\"description\"  id=\"description\" rows=\"6\" cols=\"80\"></textarea><br>";
				echo "<input type=\"hidden\" name=\"no_utilisateur\" id=\"no_utilisateur\" value=\"".$no_utilisateur."\"/> ";
				echo "<input type=\"hidden\" name=\"no_sujet\" id=\"no_sujet\" value=\"".$no_sujet."\"/> ";
				echo "<br><input name=\"AjouterCommentaire\" TYPE=\"button\" onclick=\"javascript:jsAjouterCommentaire(".$_POST['no_fichier']." ,'affichage_commentaires')\" value=\"Ajouter un commentaire\"> "; // despues meter aki el onclick
			echo "</form>";					
				
		// recuperacion de los datos del formulario e insercion
		if (isset($_POST['description']) && $_POST['description']!=""){
			$no_sujet = $_POST['no_sujet'];
			$titre_message = $_POST['titre_message'];
			$description_message = $_POST['description'];
			$no_utilisateur = $_POST['no_utilisateur'];
		
		// insertion		
			$sql = "INSERT INTO `message` (
				`no_sujet`,
				`no_utilisateur`,
				`description`,
				`titre_message`		
				) VALUES (
				:no_sujet,
				:no_utilisateur,
				:description,
				:titre_message				
			    )";

			$insert = $connexion->prepare($sql);
			$insert->execute(array(
			    ':no_sujet'=>$no_sujet,
				':no_utilisateur'=>$no_utilisateur,
				':description'=>$description_message,
			    ':titre_message'=>$titre_message			    
			)) or die ("Erreur 60 : ".$sql."<br/>".print_r($insert->errorInfo()));
		}
				
		// commentaires
			$sql = "SELECT *
						FROM `message` 
						WHERE no_sujet = :no_sujet
						ORDER BY date_creation desc";			
			
			$result_messages = $connexion->prepare($sql);
			$result_messages->execute(array(':no_sujet'=>$no_sujet)) or die ("requete ligne 70: ".$sql);
			$tab_messages = $result_messages->fetchAll(PDO::FETCH_ASSOC);
			
				$index=0;				
				while($tab_messages[$index]){					
					echo "<h2>".$tab_messages[$index]['titre_message']."</h2></br>";		
					// falta meter el nombre del usario aki recuperandolo de la tabla utilisateur
					echo "<br>par: ".$tab_messages[$index]['no_utilisateur']."</br>&nbsp;&nbsp;";
					echo "<br>date: ".$tab_messages[$index]['date_creation']."</br>";
					// $date_video= explode('-',$tab_commentaires[$index]['date_creation']);								
					// $jour_temp= explode(' ',$date_video[2]);
					// $jour = $jour_temp[0];
					// $mois= $date_video[1];
					// $annee= $date_video[0];
					// echo "<TR><TD><br>date: ".$jour." - ".$mois." - ".$annee;
					// echo "<br>heure: ".$jour_temp[1];					
					
					echo "</br>".$tab_messages[$index]['description']."</br>";					
					echo "</br>&nbsp;</br>--</br>&nbsp;</br>";	
					$index++;
				}					
?>	
	</BODY>	
</html>
	