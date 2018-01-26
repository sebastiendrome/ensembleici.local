<?php	
	//connexion à la base de données
	require ('_connect.php');
	
		$tab_param = array();
		$tab_param[':no_sujet']=$_POST['no_sujet'];
		$tab_param[':no_utilisateur']=$_POST['no_utilisateur'];
		$tab_param[':titre']=urldecode($_POST['titre']);
		$tab_param[':texte']=urldecode($_POST['texte']);		

		print_r($tab_param);
		
		// insertion de une ligne dans la table messages								
		$sql="INSERT INTO message(no_sujet, no_utilisateur, titre_message, description) 
			VALUES(:no_sujet, :no_utilisateur, :titre_message, :description)";			
							
		$result_insert_message = $connexion->prepare($sql);			
		$result_insert_message->execute($tab_param) or die ("requete ligne 22 : ".$sql);
?>