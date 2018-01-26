<?php
/*****************************************************
Gestion des évènements
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
require_once('../../01_include/fonction_redim_image.php');

// Vérifications
$id_event = intval($_POST['id_event']);
$mode_ajout = intval($_POST['mode_ajout']);
if (!$id_event){
  // aucun id à modifier
	$_SESSION['message'] .= "Erreur : ".$cc_aucun." à modifier.<br/>";
} else {

    $titre = $_POST['titre'];
    $sous_titre = $_POST["sous_titre"];
    $etat = intval($_POST['etat']);
    $validation = intval($_POST['validation']);
    $validation_ancien = intval($_POST['validation_ancien']);    
    $nb_aime = intval($_POST['nb_aime']);
    $genre = intval($_POST['no_genre']);
    $date_debut_event = $_POST['date_debut'];
    $date_fin_event = $_POST['date_fin'];
	$heure_debut_event = $_POST['heure_debut'];
    $heure_fin_event = $_POST['heure_fin'];
    $site = $_POST['site'];
    $nomadresse = $_POST['nomadresse'];
    $adresse = $_POST['adresse'];
    $no_ville = intval($_POST['id_ville']);
    $telephone = $_POST['telephone'];
    $telephone2 = $_POST['telephone2'];
    $email = $_POST['email'];
    $description = $_POST['description'];
    $description_comp = $_POST['description_complementaire'];
    $copyright = $_POST['copyright'];
    $no_structure = intval($_POST['no_structure']);
    
    $contact_no = intval($_POST['contact_no']);
    $contact_nom = $_POST['contact_nom'];
    $contact_telephone = $_POST['contact_telephone'];
    $contact_email = $_POST['contact_email'];
    $contact_role = intval($_POST['contact_role']);

	// verification de copie 
	$faire_copie = $_POST['faire_copie'];
       
    // Gérer les Tags
  
    if (empty($titre))
    	$_SESSION['message'] .= "Erreur : Aucun titre d'évenement saisi.<br/>";
    if (empty($genre))
    	$_SESSION['message'] .= "Erreur : Aucun genre choisi.<br/>";
    if (empty($no_ville))
    	$_SESSION['message'] .= "Erreur : Aucune ville sélectionnée.<br/>";
    if (preg_match("#([0-9]{2}(?:[0-9]{2})?)/([0-9]{2})/([0-9]{4})#",$date_debut_event))
      $date_debut = datesql($date_debut_event); 
    else
    	$_SESSION['message'] .= "Erreur : Aucune date de début choisie. (format JJ/MM/AAAA)<br/>";
    if (preg_match("#([0-9]{2}(?:[0-9]{2})?)/([0-9]{2})/([0-9]{4})#",$date_fin_event))
      $date_fin = datesql($date_fin_event); 
    else
    	$_SESSION['message'] .= "Erreur : Aucune date de fin choisie. (format JJ/MM/AAAA)<br/>";
		
	// regex de l'heure
	if($heure_debut_event){
		if (preg_match("#^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$#", $heure_debut_event))
		  $heure_debut = $heure_debut_event; 
		else
			$_SESSION['message'] .= "Erreur : L'heure de début n'est pas au format (HH:MM)<br/>";	
	}
	else{$heure_debut=null;}
	
	if($heure_fin_event){
		if (preg_match("#^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$#", $heure_fin_event))
		  $heure_fin = $heure_fin_event; 
		else
			$_SESSION['message'] .= "Erreur : L'heure de fin n'est pas au format (HH:MM)<br/>";		
	}
	else{$heure_fin=null;}
	
	if($_SESSION['message']==null && ($heure_fin!=null) && ($heure_debut!=null) && (($date_debut_event) == ($date_fin_event)) && (strtotime($heure_debut_event) >= strtotime($heure_fin_event)) ){
		$_SESSION['message'] .= "Erreur : L'heure de fin doit être superieur à l'heure de début<br/>";
	}
	
	if($_SESSION['message']==null && ($heure_fin!=null) && ($heure_debut==null)){
		$_SESSION['message'] .= "Erreur : Veuillez aussi indiquer une heure de début<br/>";
	}
	
		
	// l'heure et la date doit etre superieur à la date et heure de debut
	
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
    
    // Validation d'un évènement => Suppression des versions de sauvegarde de cet évènement
    // if (($validation != $validation_ancien)&&($validation==1))
    if (($validation_ancien) && ($validation==1))
    {
		// Boucle sur les contacts de l'évt
		$sql_co="SELECT *
			    FROM `evenement_contact`
			    WHERE no_evenement=:no_event";
		$res_co = $connexion->prepare($sql_co);
		$res_co->execute(array(':no_event'=>$id_event)) or die ("Erreur ".__LINE__." : ".$sql_co);
		$rows_co=$res_co->fetchAll();
		foreach($rows_co as $row_co)
		{
		    // Boucle sur les modifications de ce contact
		    $sql_ca="SELECT *
				FROM `contact_modification`
				WHERE no_contact=:no_contact";
		    $res_ca = $connexion->prepare($sql_ca);
		    $res_ca->execute(array(':no_contact'=>$row_co["no_contact"])) or die ("Erreur ".__LINE__." : ".$sql_ca);
		    $rows_ca=$res_ca->fetchAll();
		    foreach($rows_ca as $row_ca)
		    {
			// Suppression du contact backup
			$sql_delete_a="DELETE FROM `contact_temp`
						WHERE no=:no_contact";
			$delete_a = $connexion->prepare($sql_delete_a);
			$delete_a->execute(array(':no_contact'=>$row_ca["no_contact_temp"])) or die ("Erreur ".__LINE__." : ".$sql_delete_a);
		    }
		    // Suppression de l'association entre le contact et ses modifications
		    $sql_delete_b="DELETE FROM `contact_modification`
				    WHERE no_contact=:no_contact";
		    $delete_b = $connexion->prepare($sql_delete_b);
		    $delete_b->execute(array(':no_contact'=>$row_co["no_contact"])) or die ("Erreur ".__LINE__." : ".$sql_delete_b);
		}
	    
		// Suppression des liaisons associées
		// SELECT pour recupérer les infos de evenement_modification
		$sql_mod="SELECT *
			    FROM `evenement_modification`
			    WHERE no_evenement=:no_event";
		$res_mod = $connexion->prepare($sql_mod);
		$res_mod->execute(array(':no_event'=>$id_event)) or die ("Erreur ".__LINE__." : ".$sql_mod);
		$rows_mod=$res_mod->fetchAll();
		foreach($rows_mod as $row_mod)
		{
		    // Suppression des tags associés à la sauvegarde
		    $sql_delete_tagb="DELETE FROM `evenement_tag_temp`
					    WHERE no_evenement_temp=:no_evenement_temp";
		    $delete_tagb = $connexion->prepare($sql_delete_tagb);
		    $delete_tagb->execute(array(':no_evenement_temp'=>$row_mod['no_evenement_temp'])) or die ("Erreur ".__LINE__." : ".$sql_delete_tagb);
	    
		    // Supprimer les images des evenements sauvegarde si différents de l'img actuelle
		    // $_POST['url_logo']
		    $sql_img_temp="SELECT url_image
				      FROM `evenement_temp`
				      WHERE no=:no_event";
		    $delete_img_temp = $connexion->prepare($sql_img_temp);
		    $delete_img_temp->execute(array(':no_event'=>$row_mod['no_evenement_temp'])) or die ("Erreur ".__LINE__." : ".$sql_img_temp);
		    $tab_delete_img_temp=$delete_img_temp->fetchAll();
		    $fichier_img_temp = $tab_delete_img_temp[0]['url_image'];
		    if (($fichier_img_temp) && (file_exists("../../".$fichier_img_temp)) && ($fichier_img_temp != $_POST['url_logo']))
		      unlink("../../".$fichier_img_temp);
		    
		    // Suppression des images dans le cache
			if ($fichier_img_temp != $_POST['url_logo'])
			{
			    $cacheName = md5($fichier_img_temp).'-'.basename($fichier_img_temp);
			    $chemin_img_mini_t = "../../".$chemin_img_mini.$cacheName;
			    foreach (glob($chemin_img_mini_t."*") as $filename) {
					unlink($filename);
			    }
			}

		    // Suppression des évenements temps
		    $sql_delete_temp="DELETE FROM `evenement_temp`
					    WHERE no=:no_event_temp";
		    $delete_temp = $connexion->prepare($sql_delete_temp);
		    $delete_temp->execute(array(':no_event_temp'=>$row_mod['no_evenement_temp'])) or die ("Erreur ".__LINE__." : ".$sql_delete_temp);

		}
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
	    
	    $sql_evenement = "INSERT INTO `evenement` (
				`no` ,
				`no_genre` ,
				`no_structure` ,
				`no_ville` ,
				`nomadresse` ,
				`adresse` ,
				`date_debut` ,
				`date_fin` ,
				`heure_debut` ,
				`heure_fin` ,
				`titre` ,
				`sous_titre` ,
				`description` ,
				`description_complementaire` ,
				`url_image` ,
				`copyright` ,
				`site` ,
				`email` ,
				`telephone` ,
				`telephone2` ,
				`no_utilisateur_creation` ,
				`date_creation` ,
				`source_nom` ,
				`source_id` ,
				`nb_aime` ,
				`validation` ,
				`etat`
			    ) VALUES (
				:no,
				:no_genre,
				:no_structure,
				:no_ville,
				:nomadresse,
				:adresse,
				:date_debut,
				:date_fin,
				:heure_debut,
				:heure_fin,
				:titre,
				:sous_titre,
				:description,
				:description_complementaire,
				:url_image,
				:copyright,
				:site,
				:email,
				:telephone,
				:telephone2,
				:no_utilisateur_creation,
				NOW(),
				'Admin',
				'',
				:nb_aime,
				:validation,
				:etat
			    )";

	    $insert = $connexion->prepare($sql_evenement);
	    $insert->execute(array(
			    ':no'=>$id_event,
			    ':no_genre'=>$genre,
			    ':no_structure'=>$no_structure,
			    ':no_ville'=>$no_ville,
			    ':nomadresse'=>$nomadresse,
			    ':adresse'=>$adresse,
			    ':date_debut'=>$date_debut,
			    ':date_fin'=>$date_fin,
				':heure_debut'=>$heure_debut,
			    ':heure_fin'=>$heure_fin,
			    ':titre'=>$titre,
			    ':sous_titre'=>$sous_titre,
			    ':description'=>$description,
			    ':description_complementaire'=>$description_comp,
			    ':url_image'=>$url_image,
			    ':copyright'=>$copyright,
			    ':site'=>$site,
			    ':email'=>$email,
			    ':telephone'=>$telephone,
			    ':telephone2'=>$telephone2,
			    ':no_utilisateur_creation'=>$no_utilisateur_creation,
			    ':nb_aime'=>$nb_aime,
			    ':validation'=>$validation,
			    ':etat'=>$etat
	    )) or die ("Erreur 165 : ".$sql_evenement."<br/>".print_r($insert->errorInfo()));
	    
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
					)) or die ("requete ligne 185 : ".$sql_contact);
    		// echo $sql_contact." contact_nom : ".$contact_nom." contact_email : ".$contact_email." telephone : ".$contact_telephone;
		    $no_contact = $connexion->lastInsertId();
		    
		    // Ajout du lien entre l'évt et le contact
		    $sql_contact_evenement = "INSERT INTO evenement_contact (
						no_evenement,
						no_contact,
						no_role
					    ) VALUES (
						:no_evenement,
						:no_contact,
						:no_role
					    )";
		    $insert = $connexion->prepare($sql_contact_evenement);
		    $insert->execute(array(
					   ':no_evenement'=>$id_event,
					   ':no_contact'=>$no_contact,
					   ':no_role'=>$contact_role
					)) or die ("requete ligne 204 : ".$sql_contact_evenement);
	    }
    
	    $_SESSION['message'] .= "Evènement \"$titre\" ajouté avec succès.<br/>";
	}
	else
	{
		if($faire_copie ==0){
			// Requête BDD
			$sql_evenement = "UPDATE `evenement`
			SET 	titre=:titre,
					sous_titre=:sous_titre,
					no_genre=:no_genre,
					no_structure=:no_structure,
					no_ville=:no_ville,
					nomadresse=:nomadresse,
					adresse=:adresse,
					date_debut=:date_debut,
					date_fin=:date_fin,
					heure_debut=:heure_debut,
					heure_fin=:heure_fin,
					url_image=:url_image,
					copyright=:copyright,
					site=:site,
					email=:email,
					telephone=:telephone,
					telephone2=:telephone2,
					validation=:validation,
					etat=:etat,
					nb_aime=:nb_aime,
					description=:description,
					description_complementaire=:description_complementaire
			WHERE no=:no";
			$maj_evenement = $connexion->prepare($sql_evenement);
			$maj_evenement->execute(array(
					':titre'=>$titre,
					':sous_titre'=>$sous_titre,
					':no_genre'=>$genre,
					':no_structure'=>$no_structure,
					':no_ville'=>$no_ville,
					':nomadresse'=>$nomadresse,
					':adresse'=>$adresse,
					':date_debut'=>$date_debut,
					':date_fin'=>$date_fin,
					':heure_debut'=>$heure_debut,
					':heure_fin'=>$heure_fin,
					':url_image'=>$url_image,
					':copyright'=>$copyright,
					':site'=>$site,
					':email'=>$email,
					':telephone'=>$telephone,
					':telephone2'=>$telephone2,
					':validation'=>$validation,
					':etat'=>$etat,
					':nb_aime'=>$nb_aime,
					':description'=>$description,
					':description_complementaire'=>$description_comp,
					':no'=>$id_event
			)) or die ("Erreur 256 : ".$sql_evenement);
		
			// Saisie d'un contact
			if((!empty($contact_no)) || (!empty($contact_nom)) || (!empty($contact_telephone)) || (!empty($contact_email)))
			{

			    // Ajouter le contact
			    if(empty($contact_no))
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
							)) or die ("requete ligne 185 : ".$sql_contact);
				    $no_contact = $connexion->lastInsertId();
				    // Ajout du lien entre l'évt et le contact
				    $sql_contact_evenement = "INSERT INTO evenement_contact (
								no_evenement,
								no_contact,
								no_role
							    ) VALUES (
								:no_evenement,
								:no_contact,
								:no_role
							    )";
				    $insert = $connexion->prepare($sql_contact_evenement);
				    $insert->execute(array(
							   ':no_evenement'=>$id_event,
							   ':no_contact'=>$no_contact,
							   ':no_role'=>$contact_role
							)) or die ("requete ligne 204 : ".$sql_contact_evenement);
			    }
			    else
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
					)) or die ("Erreur 273 : ".$sql_contact);

					// Update du rôle du contact
					$sql_contact_evenement = "UPDATE `evenement_contact` 
					SET no_role = :no_role
					WHERE no_evenement = :no_evenement
					AND no_contact = :no_contact";
					$upd_ce = $connexion->prepare($sql_contact_evenement);
					$upd_ce->execute(array(
					':no_evenement'=>$id_event,
					':no_contact'=>$contact_no,
					':no_role'=>$contact_role
					)) or die ("Erreur 285 : ".$sql_contact_evenement);
			    }
			}
		}
		// Créer un nouveau evenement
		elseif($faire_copie == 1){
		
			 // Variables non déclarées
			$no_utilisateur_creation = intval($_POST['no_utilisateur_creation']);
			
			$rqt="SELECT *
				FROM `evenement`
				ORDER BY no DESC";
			$dernier = $connexion->prepare($rqt);
			$dernier->execute() or die ("Erreur 424 : ".$rqt."<br/>".print_r($dernier->errorInfo()));
			$tab_dernier = $dernier->fetchAll();
			$nouveau_id_event = intval($tab_dernier[0]['no'])+1;
			
			$sql_evenement_insert = "INSERT INTO `evenement` (
				`no` ,
				`no_genre` ,
				`no_structure` ,
				`no_ville` ,
				`nomadresse` ,
				`adresse` ,
				`date_debut` ,
				`date_fin` ,
				`heure_debut` ,
				`heure_fin` ,
				`titre` ,
				`sous_titre` ,
				`description` ,
				`description_complementaire` ,
				`url_image` ,
				`copyright` ,
				`site` ,
				`email` ,
				`telephone` ,
				`telephone2` ,
				`no_utilisateur_creation` ,
				`date_creation` ,
				`source_nom` ,
				`source_id` ,
				`nb_aime` ,
				`validation` ,
				`etat`
			    ) VALUES (
				:no,
				:no_genre,
				:no_structure,
				:no_ville,
				:nomadresse,
				:adresse,
				:date_debut,
				:date_fin,
				:heure_debut,
				:heure_fin,
				:titre,
				:sous_titre,
				:description,
				:description_complementaire,
				:url_image,
				:copyright,
				:site,
				:email,
				:telephone,
				:telephone2,
				:no_utilisateur_creation,
				NOW(),
				'Admin',
				'',
				:nb_aime,
				:validation,
				:etat
			    )";
			
			$insert_nouveau = $connexion->prepare($sql_evenement_insert);			
			$insert_nouveau->execute(array(
				':no'=>$nouveau_id_event,
				':no_genre'=>$genre,
				':no_structure'=>$no_structure,
				':no_ville'=>$no_ville,
				':nomadresse'=>$nomadresse,
				':adresse'=>$adresse,
				':date_debut'=>$date_debut,
				':date_fin'=>$date_fin,
				':heure_debut'=>$heure_debut,
				':heure_fin'=>$heure_fin,
				':titre'=>$titre,
				':sous_titre'=>$sous_titre,
				':description'=>$description,
				':description_complementaire'=>$description_comp,
				':url_image'=>$url_image,
				':copyright'=>$copyright,
				':site'=>$site,
				':email'=>$email,
				':telephone'=>$telephone,
				':telephone2'=>$telephone2,
				':no_utilisateur_creation'=>$no_utilisateur_creation,
				':nb_aime'=>$nb_aime,
				':validation'=>$validation,
				':etat'=>$etat
			)) or die ("Erreur 458 : ".$sql_evenement_insert."<br/>".print_r($insert_nouveau->errorInfo()));
			
			// copier les tags
			// recuperation des anciens tags
				$sql="SELECT *
					FROM `evenement_tag`
					WHERE no_evenement=:no_event";
				$origin_tags = $connexion->prepare($sql);
				$origin_tags->execute(array(':no_event'=>$id_event)) or die ("Erreur 526 : ".$sql."<br/>".print_r($origin_tags->errorInfo()));
				$tab_origin_tags= $origin_tags->fetchAll();
			
			// insert des nouveaux tags	
				$sql_insert_copie_evenement_tag = "INSERT INTO `evenement_tag` (
				`no_evenement`,
				`no_tag` 
			    ) VALUES (
				:no_evenement,
				:no_tag
			    )";
				$destin_tags = $connexion->prepare($sql_insert_copie_evenement_tag);
				
				$compteur=0;
				while($tab_origin_tags[$compteur]){
					$destin_tags->execute(array(':no_evenement'=>$nouveau_id_event,
												':no_tag'=>$tab_origin_tags[$compteur]['no_tag']
												)) or die ("Erreur copier tags : ".$sql_insert_copie_evenement_tag."<br/>".print_r($destin_tags->errorInfo()));
					$compteur++;							
				}
				
			// copier les liaisons
			// recuperation des anciens liaisons
				$sql="SELECT *
					FROM `liaisons`
					WHERE no_A=:no_event
					AND type_A like '%evenement%'";
				$origin_liaisons = $connexion->prepare($sql);
				$origin_liaisons->execute(array(':no_event'=>$id_event)) or die ("Erreur 555 : ".$sql."<br/>".print_r($origin_liaisons->errorInfo()));
				$tab_origin_liaisons= $origin_liaisons->fetchAll();
			
			// insert des nouveaux liaisons	
				$sql_insert_copie_liaisons = "INSERT INTO `liaisons` (				
				`type_A`,
				`no_A`,
				`type_B`,
				`no_B`,
				`date_creation` 
			    ) VALUES (
				:type_A,
				:no_A,
				:type_B,
				:no_B,
				:date_creation
			    )";
				$destin_liaisons = $connexion->prepare($sql_insert_copie_liaisons);
				
				$compteur=0;
				while($tab_origin_liaisons[$compteur]){
					$destin_liaisons->execute(array(':type_A'=>$tab_origin_liaisons[$compteur]['type_A'],
													':no_A'=>$nouveau_id_event,
													':type_B'=>$tab_origin_liaisons[$compteur]['type_B'],
													':no_B'=>$tab_origin_liaisons[$compteur]['no_B'],
													':date_creation'=>$tab_origin_liaisons[$compteur]['date_creation']
												)) or die ("Erreur copier tags : ".$sql_insert_copie_liaisons."<br/>".print_r($destin_liaisons->errorInfo()));
					$compteur++;							
				}
	    
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
						)) or die ("requete ligne 185 : ".$sql_contact);
		
				$no_contact = $connexion->lastInsertId();
				
				// Ajout du lien entre l'évt et le contact
				$sql_contact_evenement = "INSERT INTO evenement_contact (
							no_evenement,
							no_contact,
							no_role
							) VALUES (
							:no_evenement,
							:no_contact,
							:no_role
							)";
				$insert = $connexion->prepare($sql_contact_evenement);
				$insert->execute(array(
						   ':no_evenement'=>$nouveau_id_event,
						   ':no_contact'=>$no_contact,
						   ':no_role'=>$contact_role
						)) or die ("requete ligne 204 : ".$sql_contact_evenement);
			}  
		
		}
	    $_SESSION['message'] .= "Evènement \"$titre\" modifié avec succès.<br/>";
	}
	
	header("location:admin.php");
	exit();
    }
}

header("location:modifajout.php?id=$id_event");
exit();
?>