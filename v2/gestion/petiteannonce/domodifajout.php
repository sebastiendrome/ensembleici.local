<?php
/*****************************************************
Traitement de l'ajout ou modification
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
require_once('../../01_include/fonction_redim_image.php');

// Vérifications
$id_item = intval($_POST['id_item']);
$mode_ajout = intval($_POST['mode_ajout']);
if (!$id_item){
  // aucun id à modifier
	$_SESSION['message'] .= "Erreur : ".$cc_aucun." à modifier.<br/>";
} else {
    $titre = $_POST['titre'];
    $etat = intval($_POST['etat']);
    $validation = intval($_POST['validation']);
    $validation_ancien = intval($_POST['validation_ancien']);
    $nb_aime = intval($_POST['nb_aime']);
    $date_fin_item = $_POST['date_fin'];
    $site = $_POST['site'];
    $no_ville = intval($_POST['id_ville']);
    $description = $_POST['description'];
    $monetaire = intval($_POST['monetaire']);
    $prix = floatval(str_replace(",",".",$_POST['prix'])); // Remplace les , par des .
    $rayonmax = intval($_POST['rayonmax']);
    $afficher_mob = intval($_POST['afficher_mob']);
    $afficher_tel = intval($_POST['afficher_tel']);

    $contact_no = intval($_POST['contact_no']);
    $contact_nom = $_POST['contact_nom'];
    $contact_telephone = $_POST['contact_telephone'];
    $contact_mobile = $_POST['contact_mobile'];
    $contact_email = $_POST['contact_email'];

    // Gérer les Tags
    if (empty($titre))
    	$_SESSION['message'] .= "Erreur : Aucun titre ".$cc_de." saisi.<br/>";
    if (empty($no_ville))
    	$_SESSION['message'] .= "Erreur : Aucune ville sélectionnée.<br/>";
    if (preg_match("#([0-9]{2}(?:[0-9]{2})?)/([0-9]{2})/([0-9]{4})#",$date_fin_item))
      $date_fin = datesql($date_fin_item); 
    else
    	$_SESSION['message'] .= "Erreur : Aucune date de fin choisie. (format JJ/MM/AAAA)<br/>";
	
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
		$url_image = str_replace("../../","",$tab_upload_img[2]);	
	    else
		$_SESSION['message'] .= "Erreur dans l'envoi de l'illustration : ".$tab_upload_img[1]."<br/>";
    }
    // Pas d'image uploadée, on garde l'ancienne
    if (!$url_image) $url_image = $_POST['url_logo'];
    
    if (!isset($_SESSION['message'])) {

	if ($mode_ajout)
	{
	    // Variables non déclarées
	    $no_utilisateur_creation = intval($_POST['no_utilisateur_creation']);
	    
	    $sql_elt = "INSERT INTO `petiteannonce` (
				`no` ,
				`no_ville` ,
				`date_fin` ,
				`titre` ,
				`description` ,				
				`url_image` ,
				`site` ,
				`afficher_tel` ,
				`afficher_mob` ,
				`no_utilisateur_creation` ,
				`date_creation` ,
				`monetaire` ,
				`prix` ,
				`rayonmax` ,
				`validation` ,
				`etat`
			    ) VALUES (
				:no,
				:no_ville,
				:date_fin,
				:titre,
				:description,
				:url_image,
				:site,
				:afficher_tel,
				:afficher_mob,
				:no_utilisateur_creation,
				NOW(),
				:monetaire,
				:prix,
				:rayonmax,
				:validation,
				:etat
			    )";
	    $insert = $connexion->prepare($sql_elt);
	    $insert->execute(array(
			    ':no'=>$id_item,
			    ':no_ville'=>$no_ville,
			    ':date_fin'=>$date_fin,
			    ':titre'=>$titre,
			    ':description'=>$description,
			    ':url_image'=>$url_image,
			    ':site'=>$site,
			    ':afficher_tel'=>$afficher_tel,
			    ':afficher_mob'=>$afficher_mob,
			    ':no_utilisateur_creation'=>$no_utilisateur_creation,
			    ':monetaire'=>$monetaire,
			    ':prix'=>$prix,
			    ':rayonmax'=>$rayonmax,
			    ':validation'=>$validation,
			    ':etat'=>$etat
	    )) or die ("Erreur ".__LINE__." : ".$sql_elt."<br/>".print_r($insert->errorInfo()));

	    // Ajouter le contact
	    if($contact_nom!="" || $contact_email!="" || $contact_telephone!="" || $contact_mobile!="")
	    {
		    // Ajout du contact
		    $sql_contact = "INSERT INTO contact (
					nom,
					email,
					telephone,
					mobile
				    ) VALUES (
					:nom,
					:email,
					:telephone,
					:mobile
				    )";
		    $insert = $connexion->prepare($sql_contact);
		    $insert->execute(array(
					   ':nom'=>$contact_nom,
					   ':email'=>$contact_email,
					   ':telephone'=>$contact_telephone,
					   ':mobile'=>$contact_mobile
					)) or die ("Erreur ".__LINE__." : ".$sql_contact);

		    $no_contact = $connexion->lastInsertId();

		    // Ajout du lien entre l'évt et le contact
		    $sql_contact_elt = "INSERT INTO petiteannonce_contact (
						no_petiteannonce,
						no_contact
					    ) VALUES (
						:no_petiteannonce,
						:no_contact
					    )";
		    $insert = $connexion->prepare($sql_contact_elt);
		    $insert->execute(array(
					   ':no_petiteannonce'=>$id_item,
					   ':no_contact'=>$no_contact
					)) or die ("Erreur ".__LINE__." : ".$sql_contact_elt);
	    }

	    $_SESSION['message'] .= "Petite annonce \"$titre\" ajoutée avec succès.<br/>";
	}
	else
	{
	    // Requête BDD
	    $sql_elt = "UPDATE `petiteannonce`
		SET 	titre=:titre,
			    no_ville=:no_ville,
			    date_fin=:date_fin,
			    url_image=:url_image,
			    site=:site,
			    afficher_tel=:afficher_tel,
			    afficher_mob=:afficher_mob,
			    validation=:validation,
			    etat=:etat,
			    monetaire=:monetaire,
			    prix=:prix,
			    rayonmax=:rayonmax,
			    description=:description
		WHERE no=:no";
	    $maj_evenement = $connexion->prepare($sql_elt);
	    $maj_evenement->execute(array(
			    ':titre'=>$titre,
			    ':no_ville'=>$no_ville,
			    ':date_fin'=>$date_fin,
			    ':url_image'=>$url_image,
			    ':site'=>$site,
			    ':afficher_tel'=>$afficher_tel,
			    ':afficher_mob'=>$afficher_mob,
			    ':validation'=>$validation,
			    ':etat'=>$etat,
			    ':monetaire'=>$monetaire,
			    ':prix'=>$prix,
			    ':rayonmax'=>$rayonmax,
			    ':description'=>$description,
			    ':no'=>$id_item
	    )) or die ("Erreur ".__LINE__." : ".$sql_elt);

	    // Saisie d'un contact
	    if((!empty($contact_no)) || (!empty($contact_nom)) || (!empty($contact_telephone)) || (!empty($contact_email)) || (!empty($contact_mobile)))
	    {

		    // Un contact déjà existant ?
		    if(!empty($contact_no))
		    {
			    // Update des coordonnées du contact
			    $sql_contact = "UPDATE `contact` 
				SET nom = :nom,
				email = :email,
				telephone = :telephone,
				mobile = :mobile
				WHERE no = :no_contact";
			    $upd_c = $connexion->prepare($sql_contact);
			    $upd_c->execute(array(
				':nom'=>$contact_nom,
				':email'=>$contact_email,
				':telephone'=>$contact_telephone,
				':mobile'=>$contact_mobile,
				':no_contact'=>$contact_no
			    )) or die ("Erreur ".__LINE__." : ".$sql_contact);
		    }
		    else
		    {
		    	// Ajoute le contact dans la base
			    $sql_contact = "INSERT INTO contact (
						nom,
						email,
						telephone,
						mobile
					    ) VALUES (
						:nom,
						:email,
						:telephone,
						:mobile
					    )";
			    $insert = $connexion->prepare($sql_contact);
			    $insert->execute(array(
						   ':nom'=>$contact_nom,
						   ':email'=>$contact_email,
						   ':telephone'=>$contact_telephone,
						   ':mobile'=>$contact_mobile
						)) or die ("Erreur ".__LINE__." : ".$sql_contact);

			    $no_contact = $connexion->lastInsertId();

			    // Ajout du lien entre l'évt et le contact
			    $sql_contact_elt = "INSERT INTO petiteannonce_contact (
							no_petiteannonce,
							no_contact
						    ) VALUES (
							:no_petiteannonce,
							:no_contact
						    )";
			    $insert = $connexion->prepare($sql_contact_elt);
			    $insert->execute(array(
						   ':no_petiteannonce'=>$id_item,
						   ':no_contact'=>$no_contact
						)) or die ("Erreur ".__LINE__." : ".$sql_contact_elt);
		    }

	    }

	    $_SESSION['message'] .= "Petite annonce \"$titre\" modifiée avec succès.<br/>";
	}

	header("location:admin.php");
	exit();
    }
}

header("location:modifajout.php?id=$id_item");
exit();
?>