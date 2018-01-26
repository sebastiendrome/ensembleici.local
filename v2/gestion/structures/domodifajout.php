<?php
/*****************************************************
Gestion des structures
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
require_once('../../01_include/fonction_redim_image.php');

// Vérifications
$id_structure = intval($_POST['id_structure']);
$mode_ajout = intval($_POST['mode_ajout']);
if (!$id_structure){
  // aucun id à modifier
	$_SESSION['message'] .= "Erreur : ".$cc_aucun." à modifier.<br/>";
} else {

    $nom = $_POST['nom'];
    $sous_titre = $_POST["sous_titre"];
    $etat = intval($_POST['etat']);
    $validation = intval($_POST['validation']);
    $nb_aime = intval($_POST['nb_aime']);
    $statut = intval($_POST['no_statut']);
    $site_internet = $_POST['site_internet'];
    $facebook = $_POST['facebook'];
    $nomadresse = $_POST['nomadresse'];
    $adresse = $_POST['adresse'];
    $adresse_complementaire = $_POST['adresse_complementaire'];
    $no_ville = intval($_POST['id_ville']);
    $telephone = $_POST['telephone'];
    $telephone2 = $_POST['telephone2'];
    $fax = $_POST['fax'];
    $email = $_POST['email'];
    $description = $_POST['description'];
    $copyright = $_POST['copyright'];
    
    $contact_no = intval($_POST['contact_no']);
    $contact_nom = $_POST['contact_nom'];
    $contact_telephone = $_POST['contact_telephone'];
    $contact_email = $_POST['contact_email'];
    $contact_role = intval($_POST['contact_role']);
       
    // Gérer les Tags
  
    if (empty($nom))
    	$_SESSION['message'] .= "Erreur : Aucun nom de structure saisi.<br/>";
    if (empty($statut))
    	$_SESSION['message'] .= "Erreur : Aucun statut choisi.<br/>";
    if (empty($no_ville))
    	$_SESSION['message'] .= "Erreur : Aucune ville sélectionnée.<br/>";
    if (!empty($email))
    {
	if (!valid_email($email))
	    $_SESSION['message'] .= "Erreur : Adresse e-mail incorrecte.<br/>";
    }
    if (!empty($contact_email))
    {
	if (!valid_email($contact_email))
	    $_SESSION['message'] .= "Erreur : Adresse e-mail du contact incorrecte.<br/>";
    }

    // Upload d'une image
    if(is_uploaded_file($_FILES["illustration"]["tmp_name"]))
    {
	    $tab_upload_img=array();
	    $tab_upload_img=redimensionne_img('illustration', "../../".$chemin_img, 1, 800, 800);
	    if($tab_upload_img[0]==0)
		$url_logo = str_replace("../../","",$tab_upload_img[2]);	
	    else
		$_SESSION['message'] .= "Erreur dans l'envoi de l'illustration : ".$tab_upload_img[1]."<br/>";
    }
    // Pas d'image uploadée, on garde l'ancienne
    if (!$url_logo) $url_logo = $_POST['url_logo'];

    if (!isset($_SESSION['message'])) {
	if ($mode_ajout)
	{
	    // Variables non déclarées
	    $no_utilisateur_creation = intval($_POST['no_utilisateur_creation']);
	    
	    $sql_structure = "INSERT INTO `structure` (
				`no` ,
				`nom` ,
				`sous_titre` ,
				`no_statut` ,
				`description` ,
				`url_logo` ,
				`copyright` ,
				`site_internet` ,
				`facebook` ,
				`no_utilisateur_creation` ,
				`date_creation` ,
				`nomadresse` ,
				`adresse` ,
				`adresse_complementaire` ,
				`telephone` ,
				`telephone2` ,
				`fax` ,
				`email` ,
				`no_ville` ,
				`validation` ,
				`nb_aime` ,
				`etat`
			    ) VALUES (
				:no,
				:nom,
				:sous_titre,
				:no_statut,
				:description,
				:url_logo,
				:copyright,
				:site_internet,
				:facebook,
				:no_utilisateur_creation,
				NOW(),
				:nomadresse,
				:adresse,
				:adresse_complementaire,
				:telephone,
				:telephone2,
				:fax,
				:email,
				:no_ville,
				:validation,
				:nb_aime,
				:etat
			    )";
	    $insert = $connexion->prepare($sql_structure);
	    $insert->execute(array(
			    ':no'=>$id_structure,
			    ':nom'=>$nom,
			    ':sous_titre'=>$sous_titre,
			    ':no_statut'=>$statut,
			    ':description'=>$description,
			    ':url_logo'=>$url_logo,
			    ':copyright'=>$copyright,
			    ':site_internet'=>$site_internet,
			    ':facebook'=>$facebook,
			    ':no_utilisateur_creation'=>$no_utilisateur_creation,
			    ':nomadresse'=>$nomadresse,
			    ':adresse'=>$adresse,
			    ':adresse_complementaire'=>$adresse_complementaire,
			    ':telephone'=>$telephone,
			    ':telephone2'=>$telephone2,
			    ':fax'=>$fax,
			    ':email'=>$email,
			    ':no_ville'=>$no_ville,
			    ':validation'=>$validation,
			    ':nb_aime'=>$nb_aime,
			    ':etat'=>$etat
	    )) or die ("Erreur 145 : ".$sql_structure);
	    
	    // Ajouter le contact
	    if($contact_nom!="" || $contact_email!="" || $contact_telephone!="")
	    {
		    // Ajout du contact
		    $sql_contact = "INSERT INTO contact (
					nom,
					email,
					telephone
				    ) VALUES (
					:nom,
					:email,
					:telephone
				    )";
		    $insert = $connexion->prepare($sql_contact);
		    $insert->execute(array(
					   ':nom'=>$contact_nom,
					   ':email'=>$contact_email,
					   ':telephone'=>$contact_telephone
					)) or die ("requete ligne 183 : ".$sql_contact);
    
		    $no_contact = $connexion->lastInsertId();
		    
		    // Ajout du lien entre la structure et le contact
		    $sql_contact_structure = "INSERT INTO structure_contact (
						no_structure,
						no_contact,
						no_role
					    ) VALUES (
						:no_structure,
						:no_contact,
						:no_role
					    )";
		    $insert = $connexion->prepare($sql_contact_structure);
		    $insert->execute(array(
					   ':no_structure'=>$id_structure,
					   ':no_contact'=>$no_contact,
					   ':no_role'=>$contact_role
					)) or die ("requete ligne 184 : ".$sql_contact_structure);
	    }
    
    
	    $_SESSION['message'] .= "Structure \"$nom\" ajoutée avec succès.<br/>";
	}
	else
	{
	    // Requête BDD
	    $sql_structure = "UPDATE `structure`
		SET 	nom=:nom,
			    sous_titre=:sous_titre,
			    no_statut=:no_statut,
			    no_ville=:no_ville,
			    nomadresse=:nomadresse,
			    adresse=:adresse,
			    adresse_complementaire=:adresse_complementaire,
			    url_logo=:url_logo,
			    copyright=:copyright,
			    site_internet=:site_internet,
			    facebook=:facebook,
			    email=:email,
			    telephone=:telephone,
			    telephone2=:telephone2,
			    fax=:fax,
			    validation=:validation,
			    etat=:etat,
			    nb_aime=:nb_aime,
			    description=:description
		WHERE no=:no";
	    $maj_structure = $connexion->prepare($sql_structure);
	    $maj_structure->execute(array(
			    ':nom'=>$nom,
			    ':sous_titre'=>$sous_titre,
			    ':no_statut'=>$statut,
			    ':no_ville'=>$no_ville,
			    ':nomadresse'=>$nomadresse,
			    ':adresse'=>$adresse,
			    ':adresse_complementaire'=>$adresse_complementaire,
			    ':url_logo'=>$url_logo,
			    ':copyright'=>$copyright,
			    ':site_internet'=>$site_internet,
			    ':facebook'=>$facebook,
			    ':email'=>$email,
			    ':telephone'=>$telephone,
			    ':telephone2'=>$telephone2,
			    ':fax'=>$fax,
			    ':validation'=>$validation,
			    ':etat'=>$etat,
			    ':nb_aime'=>$nb_aime,
			    ':description'=>$description,
			    ':no'=>$id_structure
	    )) or die ("Erreur 139 : ".$sql_structure);
    
	    // Saisie d'un contact
	    if((!empty($contact_no)) || (!empty($contact_nom)) || (!empty($contact_telephone)) || (!empty($contact_email)))
	    {
		    // Update des coordonnées du contact
		    $sql_contact = "UPDATE `contact` 
			SET nom = :nom,
			email = :email,
			telephone = :telephone
			WHERE no = :no_contact";
		    $upd_c = $connexion->prepare($sql_contact);
		    $upd_c->execute(array(
			':nom'=>$contact_nom,
			':email'=>$contact_email,
			':telephone'=>$contact_telephone,
			':no_contact'=>$contact_no
		    )) or die ("Erreur 139 : ".$sql_contact);
		    
		    // Update du rôle du contact
		    $sql_contact_structure = "UPDATE `structure_contact` 
			SET no_role = :no_role
			WHERE no_structure = :no_structure
			AND no_contact = :no_contact";
		    $upd_ce = $connexion->prepare($sql_contact_structure);
		    $upd_ce->execute(array(
			':no_structure'=>$id_structure,
			':no_contact'=>$contact_no,
			':no_role'=>$contact_role
		    )) or die ("Erreur 151 : ".$sql_contact_structure);
	    }
    
	    $_SESSION['message'] .= "Structure \"$nom\" modifié avec succès.<br/>";
	}
	
	header("location:admin.php");
        exit();
    }
}

header("location:modifajout.php?id=$id_structure");
exit();
?>