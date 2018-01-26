<?php
/*****************************************************
Restauration d'une version d'une structure
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$no_version = intval($_POST['no_item']);
$no_structure = intval($_POST['no_structure']);
if (preg_match("/^[A-Za-z\\-\\., \']+$/",$_POST['type_es']))
    $type_es = trim(strtolower($_POST['type_es']));

if (($no_version) && ($no_structure))
{ 
	// Mise à jour de la structure principal
	
	// Recuperation des informations de l'structure
	$sql_version="SELECT * FROM structure_temp WHERE no=:no";
	$res_version = $connexion->prepare($sql_version);
	$res_version->execute(array(':no'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_version);
	$v = $res_version->fetchAll();
	
	// MAJ des infos de la structure
	$sql_structure = "UPDATE `structure`
		SET 	nom=:nom,
			sous_titre=:sous_titre,
			no_statut=:no_statut,
			description=:description,
			url_logo=:url_logo,
			copyright=:copyright,
			site_internet=:site_internet,
			facebook=:facebook,
			nomadresse=:nomadresse,
			adresse=:adresse,
			adresse_complementaire=:adresse_complementaire,
			telephone=:telephone,
			telephone2=:telephone2,
			fax=:fax,
			email=:email,
			no_ville=:no_ville
		WHERE no=:no";
	$maj_structure = $connexion->prepare($sql_structure);
	$maj_structure->execute(array(
			':nom'=>$v[0]["nom"],
			':sous_titre'=>$v[0]["sous_titre"],
			':no_statut'=>$v[0]["no_statut"],
			':description'=>$v[0]["description"],
			':url_logo'=>$v[0]["url_logo"],
			':copyright'=>$v[0]["copyright"],
			':site_internet'=>$v[0]["site_internet"],
			':facebook'=>$v[0]["facebook"],
			':nomadresse'=>$v[0]["nomadresse"],
			':adresse'=>$v[0]["adresse"],
			':adresse_complementaire'=>$v[0]["adresse_complementaire"],
			':telephone'=>$v[0]["telephone"],
			':telephone2'=>$v[0]["telephone2"],
			':fax'=>$v[0]["fax"],
			':email'=>$v[0]["email"],
			':no_ville'=>$v[0]["no_ville"],
			':no'=>$no_structure
	)) or die ("Erreur ".__LINE__." : ".$sql_structure);
	$nb_update = $maj_structure->rowCount();

	// Restauration du contact

	// Contacts concernés
	$sql_structure_contact="SELECT *
				FROM structure_contact
				WHERE no_structure=:no";
	$res_structure_contact = $connexion->prepare($sql_structure_contact);
	$res_structure_contact->execute(array(':no'=>$no_structure)) or die ("Erreur ".__LINE__." : ".$sql_structure_contact);
	$tab_structure_contact=$res_structure_contact->fetchAll();
	$no_contact_evt = $tab_structure_contact[0]["no_contact"];
	if ($no_contact_evt)
	{
		// Boucle sur les modifications de ce contact
		$sql_ca="SELECT *
			    FROM `contact_modification`
			    WHERE type_referent = 'structure'
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
	
	// Suppression des tags de la structure
	$sql_delete_tag="DELETE FROM `structure_sous_tag`
				WHERE no_structure=:no_structure";
	$delete_tag = $connexion->prepare($sql_delete_tag);
	$delete_tag->execute(array(':no_structure'=>$no_structure)) or die ("Erreur ".__LINE__." : ".$sql_delete_tag);
	// Récup des tags sauvegardés
	$sql_structure_activite="SELECT * FROM structure_sous_tag_temp WHERE no_structure_temp=:no";
	$res_structure_activite = $connexion->prepare($sql_structure_activite);
	$res_structure_activite->execute(array(':no'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_structure_activite);
	$tags_version=$res_structure_activite->fetchAll();
	// Restauration des tags
	for($indice_activite=0; $indice_activite<count($tags_version); $indice_activite++)
	{
		$insertion_structure_activite = "INSERT INTO structure_sous_tag (no_structure, no_sous_tag) VALUES (:no_structure, :no_sous_tag)";
		$insert = $connexion->prepare($insertion_structure_activite);
		$insert->execute(array(':no_structure'=>$no_structure, ':no_sous_tag'=>$tags_version[$indice_activite]['no_sous_tag'])) or die ("Erreur ".__LINE__." : ".$insertion_structure_activite);
	}
	
	// Suppression des liaisons de l'évènement
        if ($type_es && $no_structure)
        {
            $sql_delete_liaison="DELETE FROM `liaisons`
                                    WHERE no_A=:no_es
                                    AND type_A=:type_es";
            $delete_liaison = $connexion->prepare($sql_delete_liaison);
            $delete_liaison->execute(array(':no_es'=>$no_structure, ':type_es'=>$type_es)) or die ("Erreur ".__LINE__." : ".$sql_delete_liaison);
            
            // Récup des liaisons sauvegardées
            $sql_es_liaisons="SELECT * FROM `liaisons_temp` WHERE no_temp=:no";
            $res_es_liaisons = $connexion->prepare($sql_es_liaisons);
            $res_es_liaisons->execute(array(':no'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_es_liaisons);
            $liaisons_version=$res_es_liaisons->fetchAll();
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
	$sql_delete_version="DELETE FROM structure_temp
				WHERE no=:no_item";
	$delete_version = $connexion->prepare($sql_delete_version);
	$delete_version->execute(array(':no_item'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_delete_version);
	$nb_supp_version = $delete_version->rowCount();

	// Suppression de l'association evt / sauvegarde
	$sql_delete_asso="DELETE FROM structure_modification
				WHERE no_structure_temp=:no_item";
	$delete_asso = $connexion->prepare($sql_delete_asso);
	$delete_asso->execute(array(':no_item'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_delete_asso);

	// Suppression des tags de la sauvegarde
	$sql_delete_tag="DELETE FROM structure_sous_tag_temp
				WHERE no_structure_temp=:no_item";
	$res_delete_tag = $connexion->prepare($sql_delete_tag);
	$res_delete_tag->execute(array(':no_item'=>$no_version)) or die ("Erreur ".__LINE__." : ".$sql_delete_tag);

	// Boucle sur les modifications de ce contact
	$sql_ca="SELECT *
		    FROM `contact_modification`
		    WHERE type_referent = 'structure'
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