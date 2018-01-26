<?php 
// Affichage et gestion de la fonction j'aime

// require_once ('_connect.php');

// attention, ajouter aussi un case au javascript de index.php + ajax_like.php
	$base_lien_like = $_SERVER['HTTP_HOST']."/";
	$dossier_courant = getcwd(); 
	//on regarde si on est sur le dossier test ou pas
	if($dossier_courant=="/home/ensemble/www/00_dev")
		$base_lien_like .= "00_dev/";	
		
		$urlpage = $base_lien_like.$lien;
		
	switch ($type_objet) {
		case "structure":
		$type_cet = "cette structure";
		$type_cet_maj = "Cette structure";
		break;
		case "petiteannonce":
		$type_cet = "cette petite annonce";
		$type_cet_maj = "Cette petite annonce";
		break;
		case "evenement":
		$type_cet = "cet évènement";
		$type_cet_maj = "Cet évènement";
		break;
	}

	//on compte combien de visiteur ont aimé l'occurence
	$sql_compte = "SELECT nb_aime FROM $type_objet WHERE no=:no_occurence";
	$res_compte = $connexion->prepare($sql_compte);
	$res_compte->execute(array(':no_occurence'=>$no_occurence)) or die ("Erreur ".__LINE__." : ".$sql_compte);;
	while($lignes=$res_compte->fetch(PDO::FETCH_OBJ))
	{
		//on prend le nombre de like de l'occurence
		$nb_like = $lignes->nb_aime;
	}
	
	if(isset($page) && $page == "previsu")
	{
		$classe_lien = "ico-like";
		$text_lien_desactive = "Coups de coeur (<span class=\"nb-like\">".$nb_like."</span>)";
		$text_lien_base = "Coups de coeur (".$nb_like.")";
		$text_lien_zero = "Coup de coeur";
		$urlpage = $cette_page;
	}
	else
	{
		$classe_lien = "ico-like-list";
		$text_lien_desactive = "<span class=\"nb-like\">".$nb_like."</span>";
		$text_lien_base = "<span class=\"nb-like\">".$nb_like."</span>";
		$text_lien_zero = "<span class=\"nb-like\"></span>";
	}
	
	//On vérifie si l'utilisateur aime déja ce lien
	$sql_testaime="SELECT id_like FROM `aime` WHERE ip_like='".$_SERVER['REMOTE_ADDR']."' AND no_occurence='".$no_occurence."' AND type_like='$type_objet'";
	$res_testaime = $connexion->prepare($sql_testaime);
	$res_testaime->execute();
	$t_testaime = $res_testaime->rowCount();

	//si il a déja aimé
	if ($t_testaime > 0)
	{
		//lien désactivé
		echo "<a href=\"".$urlpage."\" title=\"$type_cet_maj fait partie<br />de vos coups de coeur\" name=\"".$nb_like."\" class=\"infobulle-l ".$classe_lien." desactive bouton-avecdautres\" id=\"\" rel=\"".$no_occurence."\">".$text_lien_desactive."</a>";
	}
	else
	{
	
		if($nb_like > 0)
		{
			//Si au moins une personne aime, on affiche le nombre de like
			echo "<a href=\"".$urlpage."\" title=\"Coup de coeur\" name=\"".$nb_like."\" class=\"infobulle-l ".$classe_lien." affiche_like bouton-avecdautres\" id=\"$type_objet\" rel=\"".$no_occurence."\">".$text_lien_base."</a>";
		}
		else
		{
			//sinon, on affiche seulement le pouce
			echo "<a href=\"".$urlpage."\" title=\"Coup de coeur\" name=\"".$nb_like."\" class=\"infobulle-l ".$classe_lien." affiche_like bouton-avecdautres\" id=\"$type_objet\" rel=\"".$no_occurence."\">".$text_lien_zero."</a>";
		}
	}
	// Fin bouton j'aime
?>