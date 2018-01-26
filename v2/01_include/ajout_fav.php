<?php
session_name("EspacePerso");
session_start();

/*
$base_lien_fav = $_SERVER['HTTP_HOST']."/";
$dossier_courant = getcwd(); 
//on regarde si on est sur le dossier test ou pas
if($dossier_courant=="/home/ensemble/www/00_dev")
	$base_lien_fav .= "00_dev/";	*/
//$urlpage = $base_lien_fav.$lien;

$urlpage = $root_site.$lien;
	
//attention, ajouter aussi un case au javascript de index.php + ajax_like.php
switch ($type_objet) {
	case "structure":
	$type_cet = "cette structure";
	$type_cet_maj = "Cette structure";
	break;
	case "evenement":
	$type_cet = "cet évènement";
	$type_cet_maj = "Cet évènement";
	break;
	case "petiteannonce":
	$type_cet = "cette petite annonce";
	$type_cet_maj = "Cette petite annonce";
	break;
}

if(isset($page) && $page == "previsu")
{
	$classe_lien = "ico-fav";
	$texte = "Archive";
	$texte_desactive = "Archive";
	$urlpage = $cette_page;
}
else
{
	$classe_lien = "ico-fav-list";
	$texte = "";
}

// Vérification si l'internaute est connecté ou non
// Si aucune information de session, on indique au membre qu'il faut se connecter
if(!@$_SESSION['UserConnecte'])
{
	echo "<a href=\"$urlpage\" title=\"Archiver $type_cet\" class=\"infobulle-l $classe_lien connect bouton-avecdautres\" id=\"$type_objet\" rel=\"\">$texte</a>";
}
else //sinon on affiche les liens
{
	//On vérifie si à déja l'objet en favori ou non
	$sql_testfav="SELECT id_fav FROM `favori` WHERE no_utilisateur='".$_SESSION['UserConnecte_id']."' AND no_occurence='$no_occurence' AND type_fav='$type_objet'";
	$res_testfav = $connexion->prepare($sql_testfav);
	$res_testfav->execute();
	$t_testfav = $res_testfav->rowCount();
	
	//si il est déja dans les favoris
	if ($t_testfav > 0)
	{
		//lien désactivé
		echo "<a href=\"$urlpage\" title=\"$type_cet_maj est dans vos archives\" id\"$type_objet\" class=\"infobulle-list $classe_lien desactive supprime bouton-avecdautres\" id=\"$type_objet\" rel=\"$no_occurence\">$texte_desactive</a>";
	}
	else
	{
		echo "<a href=\"$urlpage\" title=\"Archiver $type_cet\" class=\"infobulle-l $classe_lien bouton-avecdautres\" id=\"$type_objet\" rel=\"$no_occurence\">$texte</a>";
	}
}

?>