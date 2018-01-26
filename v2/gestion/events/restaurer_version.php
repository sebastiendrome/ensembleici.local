<?php
/*****************************************************
Restauration d'une version d'un evenement
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$no_version = intval($_POST['no_item']);
$no_evenement = intval($_POST['no_evenement']);
if (preg_match("/^[A-Za-z\\-\\., \']+$/",$_POST['type_es']))
    $type_es = trim(strtolower($_POST['type_es']));

if (($no_version) && ($no_evenement))
{
	// Mise à jour de l'évènement principal
	
	// Recuperation des informations de l'evenement
	$sql_version="SELECT * FROM evenement_temp WHERE no=:no";
	$res_version = $connexion->prepare($sql_version);
	$res_version->execute(array(':no'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_version);
	$v = $res_version->fetchAll();
	
	// MAJ des infos de l'évt
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
			url_image=:url_image,
			copyright=:copyright,
			site=:site,
			email=:email,
			telephone=:telephone,
			telephone2=:telephone2,
			validation=:validation,
			etat=:etat,
			description=:description,
			description_complementaire=:description_complementaire
		WHERE no=:no";
	$maj_evenement = $connexion->prepare($sql_evenement);
	$maj_evenement->execute(array(
			':titre'=>$v[0]["titre"],
			':sous_titre'=>$v[0]["sous_titre"],
			':no_genre'=>$v[0]["no_genre"],
			':no_structure'=>$v[0]["no_structure"],
			':no_ville'=>$v[0]["no_ville"],
			':nomadresse'=>$v[0]["nomadresse"],
			':adresse'=>$v[0]["adresse"],
			':date_debut'=>$v[0]["date_debut"],
			':date_fin'=>$v[0]["date_fin"],
			':url_image'=>$v[0]["url_image"],
			':copyright'=>$v[0]["copyright"],
			':site'=>$v[0]["site"],
			':email'=>$v[0]["email"],
			':telephone'=>$v[0]["telephone"],
			':telephone2'=>$v[0]["telephone2"],
			':validation'=>$v[0]["validation"],
			':etat'=>$v[0]["etat"],
			':description'=>$v[0]["description"],
			':description_complementaire'=>$v[0]["description_complementaire"],
			':no'=>$no_evenement
	)) or die ("Erreur ".__LINE__." : ".$sql_evenement);
	$nb_update = $maj_evenement->rowCount();

	// Restauration du contact

	// Contacts concernés
	$sql_evenement_contact="SELECT *
				FROM evenement_contact
				WHERE no_evenement=:no";
	$res_evenement_contact = $connexion->prepare($sql_evenement_contact);
	$res_evenement_contact->execute(array(':no'=>$no_evenement)) or die ("Erreur ".__LINE__." : ".$sql_evenement_contact);
	$tab_evenement_contact=$res_evenement_contact->fetchAll();
	$no_contact_evt = $tab_evenement_contact[0]["no_contact"];
	if ($no_contact_evt)
	{
		// Boucle sur les modifications de ce contact
		$sql_ca="SELECT *
			    FROM `contact_modification`
			    WHERE type_referent = 'evenement'
			    AND no_contact=:no_contact
			    AND no_referent_temp=:no_referent_temp";
		$res_ca = $connexion->prepare($sql_ca);
		$res_ca->execute(array(':no_referent_temp'=>$no_version,':no_contact'=>$no_contact_evt)) or die ("Erreur ".__LINE__." : ".$sql_ca);
		$rows_ca=$res_ca->fetchAll();
		$no_contact_temp = $rows_ca[0]["no_contact_temp"];
		if ($no_contact_temp)
		{
			// Infos du contact de la version
			$sql_contact = "SELECT * FROM contact_temp WHERE no=:no";
			$res_contact = $connexion->prepare($sql_contact);
			$res_contact->execute(array(':no'=>$no_contact_temp)) or die ("Erreur ".__LINE__." : ".$sql_contact);
			$contact_version = $res_contact->fetchAll();
			// MAJ de la table contact
			$sql_maj_contact="UPDATE `contact` SET
						nom=:nom,
						email=:email,
						telephone=:telephone
						WHERE no=:no_contact";
			$maj_contact = $connexion->prepare($sql_maj_contact);
			$maj_contact->execute(array(':nom'=>$contact_version[0]['nom'], ':email'=>$contact_version[0]['email'], ':telephone'=>$contact_version[0]['telephone'],':no_contact'=>$no_contact_evt)) or die ("Erreur ".__LINE__." : ".$sql_maj_contact);
		}
	}
	
	// Restauration des tags
	
	// Suppression des tags de l'évènement
	$sql_delete_tag="DELETE FROM `evenement_tag`
				WHERE no_evenement=:no_event";
	$delete_tag = $connexion->prepare($sql_delete_tag);
	$delete_tag->execute(array(':no_event'=>$no_evenement)) or die ("Erreur ".__LINE__." : ".$sql_delete_tag);
	// Récup des tags sauvegardés
	$sql_evenement_activite="SELECT * FROM evenement_tag_temp WHERE no_evenement_temp=:no";
	$res_evenement_activite = $connexion->prepare($sql_evenement_activite);
	$res_evenement_activite->execute(array(':no'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_evenement_activite);
	$tags_version=$res_evenement_activite->fetchAll();
	// Restauration des tags
	for($indice_activite=0; $indice_activite<count($tags_version); $indice_activite++)
	{
		$insertion_evenement_activite = "INSERT INTO evenement_tag (no_evenement, no_tag) VALUES 
		(:no_evenement, :no_tag)";
		$insert = $connexion->prepare($insertion_evenement_activite);
		$insert->execute(array(':no_evenement'=>$no_evenement, ':no_tag'=>$tags_version[$indice_activite]['no_tag'])) or die ("Erreur ".__LINE__." : ".$insertion_evenement_activite);
	}
	
	// Suppression des liaisons de l'évènement
        if ($type_es && $no_evenement)
        {
            $sql_delete_liaison="DELETE FROM `liaisons`
                                    WHERE no_A=:no_event
                                    AND type_A=:type_es";
            $delete_liaison = $connexion->prepare($sql_delete_liaison);
            $delete_liaison->execute(array(':no_event'=>$no_evenement, ':type_es'=>$type_es)) or die ("Erreur ".__LINE__." : ".$sql_delete_liaison);
            
            // Récup des liaisons sauvegardées
            $sql_evenement_liaisons="SELECT * FROM `liaisons_temp` WHERE no_evenement_temp=:no";
            $res_evenement_liaisons = $connexion->prepare($sql_evenement_liaisons);
            $res_evenement_liaisons->execute(array(':no'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_evenement_liaisons);
            $liaisons_version=$res_evenement_liaisons->fetchAll();
            // Restauration des liaisons
            for($indice_liaison=0; $indice_liaison<count($liaisons_version); $indice_liaison++)
            {
                    // Restauration
                    $sql_ajt = "INSERT INTO `liaisons`
                            (`no`, `type_A` , `no_A` , `type_B` , `no_B`, `date_creation`)
                                    VALUES
                            (NULL, :type_A, :no_A, :type_B, :no_B, NOW())";
                    $insert_ajt = $connexion->prepare($sql_ajt);
                    $insert_ajt->execute(array(
                        ':type_A'=>$liaisons_version[$indice_liaison]['type_A'],
                        ':no_A'=>$liaisons_version[$indice_liaison]['no_A'],
                        ':type_B'=>$liaisons_version[$indice_liaison]['type_B'],
                        ':no_B'=>$liaisons_version[$indice_liaison]['no_B']
                    )) or die ("Erreur ".__LINE__." : ".$sql_ajt);

                    // Suppression de la liaison de sauvegarde une fois restaurée
                    $sql_delete_b="DELETE FROM `liaisons_temp` WHERE no=:no_liaison";
                    $delete_b = $connexion->prepare($sql_delete_b);
                    $delete_b->execute(array(':no_liaison'=>$liaisons_version[$indice_liaison]['no'])) or die ("Erreur ".__LINE__." : ".$sql_delete_b);
            }
        }
	
	// Suppression de toutes les sauvegardes
	
 	// Suppression de la sauvegarde de l'evt
	$sql_delete_version="DELETE FROM evenement_temp
				WHERE no=:no_item";
	$delete_version = $connexion->prepare($sql_delete_version);
	$delete_version->execute(array(':no_item'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_delete_version);
	$nb_supp_version = $delete_version->rowCount();

	// Suppression de l'association evt / sauvegarde
	$sql_delete_asso="DELETE FROM evenement_modification
				WHERE no_evenement_temp=:no_item";
	$delete_asso = $connexion->prepare($sql_delete_asso);
	$delete_asso->execute(array(':no_item'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_delete_asso);

	// Suppression des tags de la sauvegarde
	$sql_delete_tag="DELETE FROM evenement_tag_temp
				WHERE no_evenement_temp=:no_item";
	$res_delete_tag = $connexion->prepare($sql_delete_tag);
	$res_delete_tag->execute(array(':no_item'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_delete_tag);

	// Boucle sur les modifications de ce contact
	$sql_ca="SELECT *
		    FROM `contact_modification`
		    WHERE type_referent = 'evenement'
		    AND no_referent_temp=:no_referent_temp";
	$res_ca = $connexion->prepare($sql_ca);
	$res_ca->execute(array(':no_referent_temp'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_ca);
	$rows_ca=$res_ca->fetchAll();
	foreach($rows_ca as $row_ca)
	{
	    // Suppression du contact backup
	    $sql_delete_a="DELETE FROM `contact_temp`
				    WHERE no=:no_contact";
	    $delete_a = $connexion->prepare($sql_delete_a);
	    $delete_a->execute(array(':no_contact'=>$row_ca["no_contact_temp"])) or die ("Erreur ".__LINE__." : ".$sql_delete_a);
	}
	// Suppression de l'association entre la sauvegarde et le contact sauvegarde
	$sql_delete_b="DELETE FROM `contact_modification`
			WHERE no_referent_temp=:no_referent_temp";
	$delete_b = $connexion->prepare($sql_delete_b);
	$delete_b->execute(array(':no_referent_temp'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_delete_b);


if ($nb_update)
{
	echo "ok";
	$_SESSION['message'] .= "Version restaurée avec succès.<br/>";
}

}
?>