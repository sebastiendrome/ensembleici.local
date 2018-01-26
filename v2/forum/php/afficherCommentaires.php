<?php	
	//connexion à la base de données
	require ('_connect.php');
	
	// obtention de la liste de messages	
		$sqlCommentaires= "SELECT *
							FROM message 
							WHERE no_sujet = :no_sujet 
							ORDER BY date_creation DESC";			
			
			$result_messages = $connexion->prepare($sqlCommentaires);
			$result_messages->execute(array(':no_sujet'=>$_POST['no_sujet'])) or die ("requete ligne 17: ".$sqlCommentaires);
			$tab_messages = $result_messages->fetch(PDO::FETCH_ASSOC); 
		

		$retour_valeur = "";						
			$retour_valeur .= "<h3><b>".$tab_messages['titre_message']."   </b>";
				$date_commentaire= explode('-',$tab_messages['date_creation']);								
				$jour_temp= explode(' ',$date_commentaire[2]);
				$jour = $jour_temp[0];
				$mois= $date_commentaire[1];
				$annee= $date_commentaire[0];
				$retour_valeur .= "  <i>".$jour."/".$mois."/".$annee." ".$jour_temp[1]."</i>";
				$retour_valeur .= " par ".$tab_messages['no_utilisateur'];
			$retour_valeur .= "</h3>";
			$retour_valeur .= "<div>";					
				$retour_valeur .= "<p>".$tab_messages['description']."</p>";
			$retour_valeur .= "</div>";		
														
	echo json_encode($retour_valeur);			
?>