<?php
/*****************************************************
Affichage d'une version d'un évenement
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

if (!$no_item)
{
  if ($_GET['no_item'])
    $no_item = intval($_GET['no_item']);
}
$no_evenement = intval($_GET['no_evenement']);

if ($no_item && $no_evenement)
{

// Recuperation des infos de la modification (date, par qui)
$sql_modifs = "SELECT * FROM `evenement_modification` M,
			     `utilisateur` U
			    WHERE
				M.no_utilisateur = U.no
			    AND no_evenement_temp=:no_version
			    AND no_evenement=:no_evenement";
$res_modifs = $connexion->prepare($sql_modifs);
$res_modifs -> execute(array(':no_version'=>$no_item,':no_evenement'=>$no_evenement)) or die ("Erreur ".__LINE__." : ".$sql_modifs);
$modif=$res_modifs->fetchAll();

// Recuperation des infos de la sauvegarde
$sql_versions = "SELECT *, T.no AS no_version FROM `evenement_temp` T,
			       `utilisateur` U,
			       `villes` V,
			       `genre` G
			    WHERE
				T.no_utilisateur_creation = U.no
			    AND V.id = T.no_ville
			    AND G.no = T.no_genre
			    AND T.no=:no_version";
$res_versions = $connexion->prepare($sql_versions);
$res_versions -> execute(array(':no_version'=>$no_item)) or die ("Erreur ".__LINE__." : ".$sql_versions);
$version=$res_versions->fetchAll();

// Recuperation des tags de la sauvegarde
$sql_tag_version="SELECT * FROM `evenement_tag_temp` E,
				`tag` T
				WHERE
				    E.no_tag = T.no
				AND no_evenement_temp=:no_version";
$res_tag_version = $connexion->prepare($sql_tag_version);
$res_tag_version->execute(array(':no_version'=>$no_item)) or die ("Erreur ".__LINE__." : ".$sql_tag_version);
$tags_version = $res_tag_version->fetchAll();

// Recuperation des liaisons de la sauvegarde
$sql_liaison_version="SELECT * FROM `liaisons_temp` L
				WHERE no_temp=:no_version";
$res_liaison_version = $connexion->prepare($sql_liaison_version);
$res_liaison_version->execute(array(':no_version'=>$no_item)) or die ("Erreur ".__LINE__." : ".$sql_liaison_version);
$liaisons_version = $res_liaison_version->fetchAll();

// Contact associé à cette sauvegarde
$sql_evenement_contact="SELECT *
			FROM contact_modification
			WHERE type_referent = 'evenement'
			AND no_referent_temp=:no_version";
$res_evenement_contact = $connexion->prepare($sql_evenement_contact);
$res_evenement_contact->execute(array(':no_version'=>$no_item)) or die ("Erreur ".__LINE__." : ".$sql_evenement_contact);
$tab_evenement_contact=$res_evenement_contact->fetchAll();

$sql_contact = "SELECT * FROM contact_temp WHERE no=:no";
$res_contact = $connexion->prepare($sql_contact);
$res_contact->execute(array(':no'=>$tab_evenement_contact[0]['no_contact_temp'])) or die ("Erreur 53 : ".$sql_contact);
$contact_version = $res_contact->fetchAll();

// Recuperation des infos de l'évt source
$sql_evt = "SELECT * FROM `evenement` E,
			  `utilisateur` U,
			  `villes` V,
			  `genre` G			  
		     WHERE
			E.no=:no_evenement
			AND E.no_utilisateur_creation = U.no
			AND V.id = E.no_ville
			AND G.no = E.no_genre";
$res_evt = $connexion->prepare($sql_evt);
$res_evt -> execute(array(':no_evenement'=>$no_evenement)) or die ("Erreur ".__LINE__." : ".$sql_evt);
$evt=$res_evt->fetchAll();

// Recuperation des tags de l'évt source
$sql_tag_evenement="SELECT no_tag FROM `evenement_tag` WHERE no_evenement=:no_evenement";
$res_tag_evenement = $connexion->prepare($sql_tag_evenement);
$res_tag_evenement->execute(array(':no_evenement'=>$no_evenement)) or die ("Erreur ".__LINE__." : ".$sql_tag_evenement);
$tags_evt = $res_tag_evenement->fetchAll(PDO::FETCH_COLUMN, 0);

// Recuperation du contact de l'évt source
$sql_evenement_contact_or="SELECT * FROM evenement_contact WHERE no_evenement=:no";
$res_evenement_contact_or = $connexion->prepare($sql_evenement_contact_or);
$res_evenement_contact_or->execute(array(':no'=>$no_evenement)) or die ("Erreur ".__LINE__." : ".$sql_evenement_contact_or);
$tab_evenement_contact_or=$res_evenement_contact_or->fetchAll();
$sql_contact_or = "SELECT * FROM contact WHERE no=:no";
$res_contact_or = $connexion->prepare($sql_contact_or);
$res_contact_or->execute(array(':no'=>$tab_evenement_contact_or[0]['no_contact'])) or die ("Erreur ".__LINE__." : ".$sql_contact_or);
$contact_evt = $res_contact_or->fetchAll();

// Recupération du rôle (non sauvegardé)
$sql_role = "SELECT libelle FROM role WHERE no=:no_role";
$res_role = $connexion->prepare($sql_role);
$res_role->execute(array(':no_role'=>$tab_evenement_contact_or[0]['no_role'])) or die ("Erreur ".__LINE__." : ".$sql_role);
$tab_role=$res_role->fetchAll();

?>

<h4>Version avant modification par <?php echo $modif[0]["email"];?> le <?php echo datefr($modif[0]["date_modification"],$avecheure=true);?>
</h4>

<div class="note">En rouge : Champs modifiés par rapport à la version actuellement en ligne.</div>
 
<form name="EDconnexion" id="form-aff-version" action="" method="post" class="formA">
<fieldset>
  <legend>Généralités</legend>
  <li<?php if ($evt[0]["titre"] != $version[0]["titre"]) echo " class='modifie'"; ?>><label for="nom">Titre :</label>
    	<input type="text" name="titre" size="55" class="input verouille" value="<?php echo $version[0]["titre"]; ?>" /></li>
  <li<?php if ($evt[0]["sous_titre"] != $version[0]["sous_titre"]) echo " class='modifie'"; ?>><label for="sous_titre">Sous-titre : </label>
    	<input type="text" name="sous_titre" size="55" class="input verouille" value="<?php echo $version[0]["sous_titre"]; ?>" /></li>
  <li><label for="id_event">Numéro : </label>
    	<input type="text" name="id_event" size="7" class="input verouille" value="<?php echo $version[0]["no_version"]; ?>" readonly /></li>
  <li<?php if ($evt[0]["libelle"] != $version[0]["libelle"]) echo " class='modifie'"; ?>><label for="titre_genre">Genre : </label>
    	<input type="text" name="titre_genre" size="55" class="input verouille" value="<?php echo $version[0]["libelle"]; ?>" /></li>
    <li<?php if ($evt[0]["date_debut"] != $version[0]["date_debut"]) echo " class='modifie'"; ?>><label for="date_debut">Date de début :</label>
    <input type="text" size="7"  name="date_debut" id="date_debut" value="<?php echo datefr($version[0]["date_debut"]) ?>" class="input verouille"></li>
    <li<?php if ($evt[0]["date_fin"] != $version[0]["date_fin"]) echo " class='modifie'"; ?>><label for="date_fin">Date de fin :</label>
    <input type="text" size="7"  name="date_fin" id="date_fin" value="<?php echo datefr($version[0]["date_fin"]) ?>" class="input verouille"></li>
    <li<?php if ($evt[0]["site"] != $version[0]["site"]) echo " class='modifie'"; ?>><label for="site">Site internet : </label>
    <input type="text" name="site" value="<?php echo $version[0]["site"] ?>" size="35" class="input verouille"></li>
</fieldset>

<fieldset>
  <legend>Tags</legend>
  <li><label>Tags : <sup>(Vie)</sup></label>
  <div class="chps_non_input avecliste">
	<?php
	    // Affiche les tags
	    if (count($tags_version))
	    {
	      echo "<ul>";
	      foreach ($tags_version as $tag) {
		  $no_tag = $tag['no'];
		  echo "<li";
		  
		  // Tag modifié ?
		  if (in_array($no_tag,$tags_evt))
		  {
		    echo ">";
		    // On supprime la ligne du tableau
		    $key = array_search($no_tag,$tags_evt);
		    unset($tags_evt[$key]);
		  }
		  else
		    echo " class='modifie'>";

		  echo $tag["titre"];
	  
		  // Dans quelles vies ?
		  $sql_vie="SELECT libelle
			      FROM `vie_tag` I, `vie` V
			      WHERE no_tag=:no_tag
			      AND I.no_vie = V.no";
		  $res_vie = $connexion->prepare($sql_vie);
		  $res_vie->execute(array(':no_tag'=>$no_tag)) or die ("Erreur ".__LINE__." : ".$sql_vie);
		  $tab_vie=$res_vie->fetchAll();
		  echo " <sup>( ";
		  foreach ($tab_vie as $vie) {
		      echo $vie["libelle"]." ";
		  }
		  echo ")</sup>";
		  echo "</li>";
	      }
	      echo "</ul>";

	      // Anciens tags pas dans cette version
	      if (count($tags_evt))
	      {
		echo "</div></li>";
		echo "<li><label>Tags supprimés : </label><div class=\"chps_non_input avecliste\"><ul>";
		// print_r($tags_evt);
		foreach ($tags_evt as $tags_restant) {
		  $sql_tr="SELECT titre FROM `tag` WHERE no=:no_tag";
		  $res_tr = $connexion->prepare($sql_tr);
		  $res_tr->execute(array(':no_tag'=>$tags_restant)) or die ("Erreur ".__LINE__." : ".$sql_tr);
		  $tag_restant=$res_tr->fetchColumn();
		  echo "<li class='modifie'>";
		  echo $tag_restant;
		  echo "</li>";
		}
		echo "</ul>";
	      }
	      
	    }

	?>
    </div>
</li>
</fieldset>

<fieldset>
  <legend>Liaisons</legend>
	<?php
	    // Affiche les liaisons
	    if (count($liaisons_version))
	    {
	      echo "<ul>";
	      foreach ($liaisons_version as $liaison) {

		// Cet evt est en A ou en B ?
		if (($liaison["type_A"]==$type_source)&&($liaison["no_A"]==$no_evenement))
		{
		      $type_aff = $liaison["type_B"];
		      $no_aff = $liaison["no_B"];
		}
		else
		{
		      $type_aff = $liaison["type_A"];
		      $no_aff = $liaison["no_A"];			
		}


		if ($type_actif!=$type_aff)
		{
		  // Début d'un nouveau type
		  $type_actif = $type_aff;
		  switch ($type_actif)
		  {
		     case "evenement" :
			$type_actif_affiche = "Evènement(s)";
			$url_lien = "/events/";
		     break;
		     case "structure" :
			$type_actif_affiche = "Structure(s)";
			$url_lien = "/structures/";
		     break;
		  }
		  // ce n'est pas le 1er
		  if ($i) echo "</li></ul></div>";
		  echo "<div class=\"clear\">
		  <label>".$type_actif_affiche." :<sup>(N°)</sup></label>
		  <div class=\"chps_non_input avecliste\">
		  <ul>\n";
		}
		
		  // Récupère le nom de la liaison
		  $sql_nl="SELECT * FROM ".$type_aff."
			    WHERE no=:no_lie";
		  $res_nl = $connexion->prepare($sql_nl);
		  $res_nl->execute(array(':no_lie'=>$no_aff)) or die ("Erreur ".__LINE__." : ".$sql_nl);
		  $liaison_nom=$res_nl->fetch(PDO::FETCH_ASSOC);
		  
		  
		  // Champs Titre ou nom selon les tables
		  if (isset($liaison_nom["titre"]))
		    $liaison_nom_aff = $liaison_nom["titre"];
		  else
		    $liaison_nom_aff = $liaison_nom["nom"];
	  
		  echo "<li>";
		if ($liaison_nom_aff)
		  echo "<a href=\"..".$url_lien."modifajout.php?id=".$no_aff."\" target=\"_blank\">".$liaison_nom_aff."</a> <sup>(".$no_aff.")</sup>";
		  echo "</li>";
		  
		  $i++;
	      }
	      if ($i) echo "</ul></div></div>";
	    }
	?>
</fieldset>

<fieldset>
  <legend>Lieu</legend>
    <li<?php if ($evt[0]["nomadresse"] != $version[0]["nomadresse"]) echo " class='modifie'"; ?>><label>Nom du lieu :</label>
    <input size="35" type="text" name="nomadresse" value="<?php echo $version[0]["nomadresse"]; ?>" class="input verouille"></li>
    <li<?php if ($evt[0]["adresse"] != $version[0]["adresse"]) echo " class='modifie'"; ?>><label>Adresse :</label>
    <input type="text" name="adresse" value="<?php echo $version[0]["site"] ?>" size="35" class="input verouille"></li>

    <li<?php if ($evt[0]["no_ville"] != $version[0]["no_ville"]) echo " class='modifie'"; ?>><label>Ville :</label> <input type="text" name="ville" value="<?php echo
    $version[0]['nom_ville_maj']." (".$version[0]['code_postal'].")";?>" size="35" class="input verouille" /></li>
    
    <li<?php if ($evt[0]["telephone"] != $version[0]["telephone"]) echo " class='modifie'"; ?>><label>T&eacute;l&eacute;phone principal :</label>
    <input type="text" name="telephone" value="<?php echo $version[0]["telephone"] ?>" class="input verouille"></li>
    <li<?php if ($evt[0]["telephone2"] != $version[0]["telephone2"]) echo " class='modifie'"; ?>><label>Mobile :</label>
    <input type="text" name="telephone2" value="<?php echo $version[0]["telephone2"] ?>" class="input verouille"></li>
    <li<?php if ($evt[0]["email"] != $version[0]["email"]) echo " class='modifie'"; ?>><label>Email :</label>
    <input type="text" name="email" value="<?php echo $version[0]["email"] ?>" size="35" class="input verouille"></li>

</fieldset>

<fieldset>
  <legend>Contact</legend>

    <li<?php if ($contact_evt[0]["nom"] != $contact_version[0]["nom"]) echo " class='modifie'"; ?>><label for="contact_nom">Personne &agrave; contacter :</label>
      <input type="text" size="50" name="contact_nom" value="<?php echo $contact_version[0]["nom"] ?>" class="input verouille">
    </li>
    <li><label for="contact_role">R&ocirc;le du contact : <sup>Non sauvegardé</sup></label>
    <input type="text" size="50" name="contact_role" value="<?php echo $tab_role[0]['libelle'] ?>" class="input verouille">
    </li>
    <li<?php if ($contact_evt[0]["telephone"] != $contact_version[0]["telephone"]) echo " class='modifie'"; ?>><label>T&eacute;l&eacute;phone contact :</label>
    <input type="text" size="50" name="contact_telephone" value="<?php echo $contact_version[0]["telephone"] ?>" class="input verouille"></li>
    <li<?php if ($contact_evt[0]["email"] != $contact_version[0]["email"]) echo " class='modifie'"; ?>><label>Email contact :</label>
    <input type="text" size="50" name="contact_email" value="<?php echo $contact_version[0]["email"] ?>" size="35" class="input verouille"></li>
</fieldset>

<fieldset>
  <legend>Descriptions</legend>
<li<?php if ($evt[0]["description"] != $version[0]["description"]) echo " class='modifie'"; ?>><label for="description">Description : </label>
    	<div class="bloc_html" ><?php echo $version[0]["description"]; ?></div></li>
<li<?php if ($evt[0]["description_complementaire"] != $version[0]["description_complementaire"]) echo " class='modifie'"; ?>><label for="description_complementaire">Description complémentaire : </label>
    	<div class="bloc_html" ><?php echo $version[0]["description_complementaire"]; ?></div></li>
</fieldset>

<fieldset>
  <legend>Illustration</legend>
<li<?php if ($evt[0]["url_image"] != $version[0]["url_image"]) echo " class='modifie'"; ?>><label>Illustration : </label>
  <div class="chps_non_input">
<?php
  $illustration = $version[0]["url_image"];
  // Affiche l'image
  if ($illustration)
  {
    echo "<input type=\"hidden\" name=\"url_logo\" value=\"".$illustration."\"/>\n";
    echo "<div><div class=\"illustr\">";
    
    if (strpos($illustration, "http://www.culture-provence-baronnies.fr") !== false)
    {
	// image distante
	echo "<a href=\"".$illustration."\" class=\"agrandir\">";
	echo "<img src=\"".$illustration."\" width=\"150\" />";	
    }
    else
    {
	// Image locale
	if (fichier_existant($root_site.$illustration))
	{
	  // Image existante dans ce dossier
	  // echo "<a href=\"".$root_site.$illustration."\" class=\"agrandir\">";
	  echo "<img src=\"".$root_site."miniature.php?uri=".$illustration."&method=fit&w=150&h=150\" />";
	}
	else
	{
	  // Image inexistante => on test dans un autre dossier
	  if ($root_site == $root_site_dev)
	    $autre_root_site = $root_site_prod;
	  else
	    $autre_root_site = $root_site_dev;
	  // echo "<a href=\"".$autre_root_site.$illustration."\" class=\"agrandir\">";
	  echo "<img src=\"".$autre_root_site."miniature.php?uri=".$illustration."&method=fit&w=150&h=150\" />";
	}
    }
    echo "</div>";
    echo "</div>\n";

  }
  else
    echo "Aucune.";
?></div>    	
</li>
    <li<?php if ($evt[0]["copyright"] != $version[0]["copyright"]) echo " class='modifie'"; ?>><label for="copyright">Copyright :</label>
    <input type="text" name="copyright" value="<?php echo $version[0]["copyright"] ?>" size="35" class="input verouille"></li>
</fieldset>

</form>

<?php
}
?>