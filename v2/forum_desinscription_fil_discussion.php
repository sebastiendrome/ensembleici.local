<?php
	//desinscrire à un utilisateur
		
	require ('_connect.php');
	
	$no_utilisateur = $_GET['no_utilisateur'];
	$no_ville =  $_GET['no_ville'];
	$no_message =  $_GET['messagemessage'];
	$nom_ville =  $_GET['nom_ville'];
	
	// Suppresion   
	$sqlp = "UPDATE `message_utilisateur` 
				SET inscrit = 0				
				WHERE no_sujet = :no_sujet
				AND no_utilisateur = :no_utilisateur
				AND no_message = :no_message";	
		
		$sup= $connexion->prepare($sqlp);
		$sup->execute(array(
		':no_sujet'=>$no_ville,
		':no_utilisateur'=>$no_utilisateur,
		':no_message'=>$no_message		
		)) or die ("Erreur ".__LINE__." : ".$sqlp);   	
		
	//	echo "Vous venez de vous désisncrire de la liste de diffusion";
		
	//	echo "<a href=\"http://www.ensembleici.fr/forum/?id_ville=".$no_ville."&nom_ville=".$nom_ville."#inscription_message".$no_message."\">";

	session_name("EspacePerso");
	session_start();
	// include header
	$titre_page = "Désinscription du forum";
	$meta_description = "Désinscription du fil de discussion du forum.";

	include ('01_include/structure_header.php');

?>
<div id="colonne2">

<h1>Désinscription du fil de discussion du forum</h1>

<p>
<?
echo "Vous ne recevrai plus les alerts d'un fil de discussion de ".$nom_ville.".";
?>
</p>
<br/>		    
      </div>
    
<?php
	// Colonne 3
	$affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');
	
	// Footer
	include ('01_include/structure_footer.php');
?>