<?php
	// include header
	$titre_page = "Plan du site";
	$meta_description = "Plan du site internet : Ensemble ici : Tous acteurs de la vie locale.";
  require ('01_include/_var_ensemble.php');
  require ('01_include/_connect.php');

	/* $ajout_header = <<<AJHE
AJHE;*/
	include ('01_include/structure_header.php');

?>
      <div id="colonne2">

	<h2>Ensemble ici : Plan du site</h2>

<ul>
  <li><a href="identification.html" title="Identification"></a>Identification</li>
  <li><a href="espace_personnel.html" title="Espace personnel">Espace personnel</a></li>
  <li><a href="ajouter_une_structure.html" title="Ajouter une structure">Ajouter une structure</a></li>
  <li><a href="ajouter_un_evenement.html" title="Ajouter un évenement">Ajouter un évenement</a></li>
  <li><a href="ajouter_une_petiteannonce.html" title="Ajouter une petite annonce">Ajouter une petite annonce</a></li>
  <li><a href="explications.html" title="Qu'est ce que ensemble ici ?">Qu'est ce que le projet ensemble ici ?</a></li>
  <li><a href="faire_un_don.html" title="Faire un don">Faire un don</a></li>
  <li><a href="partenaires.html" title="Partenaires">Partenaires</a></li>
  <li><a href="plan_site.html" title="Plan du site">Plan du site</a></li>
  <li><a href="contact.html" title="Contactez-nous">Contactez-nous</a></li>
  <li><a href="signaler_un_abus.html" title="Signaler un abus">Signaler un abus</a></li>
  <li><a href="mentions_legales.html" title="Mentions légales">Mentions légales</a></li>
</ul>

<?php

  $sql_villes="
  SELECT *
  FROM (
    (SELECT V.* FROM `petiteannonce` PA, `villes` V WHERE PA.etat=1 AND PA.no_ville=V.id)
       UNION
    (SELECT V.* FROM `evenement` E, `villes` V WHERE E.date_fin>=CURDATE() AND E.etat=1 AND E.no_ville=V.id)
       UNION
    (SELECT V.* FROM `structure` S, `villes` V WHERE S.etat=1 AND S.no_ville=V.id)
  ) AS U1
  GROUP BY U1.id";

  $r_villes = $connexion->prepare($sql_villes);
  $r_villes->execute();
  $nb_villes = $r_villes->rowCount();
  
  echo "<p>".$nb_villes." villes ont des évènements ou des structures.</p><ul>";

  while($ville = $r_villes->fetch(PDO::FETCH_ASSOC))
  {
      // buis-les-baronnies.9424.choix.html
      $lienville = $ville["nom_ville_url"].".".$ville["id"].".choix.html";
      echo "<li><a href=\"$lienville\" title=\"".$ville["nom_ville_maj"]."\">".$ville["nom_ville_maj"].", ".$ville["code_postal"]."</a>";
      echo "<ul>";
  
    // Evènements
    $sql_evt="SELECT no FROM `evenement` E
		WHERE E.no_ville=:noville
		AND E.date_fin>=CURDATE()
		AND E.etat=1";
    $r_evt = $connexion->prepare($sql_evt);
    $r_evt->bindValue(':noville', $ville["id"], PDO::PARAM_INT);
    $r_evt->execute();
    $nb_evts = $r_evt->rowCount();
    if ($nb_evts)
    {
      $lienville_evt = $ville["nom_ville_url"].".".$ville["id"].".tout.agenda.html";
      echo "<li><a href=\"$lienville_evt\" title=\"Agenda (évènements) de ".$ville["nom_ville_maj"]."\">Agenda de ".$ville["nom_ville_maj"].", ".$ville["code_postal"]."</a></li>";
    }
    
    // structures
    $sql_str="SELECT no FROM `structure` S
		WHERE S.no_ville=:noville
		AND S.etat=1";
    $r_str = $connexion->prepare($sql_str);
    $r_str->bindValue(':noville', $ville["id"], PDO::PARAM_INT);
    $r_str->execute();
    $nb_struct = $r_str->rowCount();
    if ($nb_struct)
    {
      $lienville_str = $ville["nom_ville_url"].".".$ville["id"].".tout.repertoire.html";
      echo "<li><a href=\"$lienville_str\" title=\"Répertoire (structures) de ".$ville["nom_ville_maj"]."\">Répertoire de ".$ville["nom_ville_maj"].", ".$ville["code_postal"]."</a></li>";
    }
    
    // Petites annonces
    $sql_pa="SELECT no FROM `petiteannonce` PA
    WHERE PA.no_ville=:noville
    AND PA.etat=1";
    $r_pa = $connexion->prepare($sql_pa);
    $r_pa->bindValue(':noville', $ville["id"], PDO::PARAM_INT);
    $r_pa->execute();
    $nb_pa = $r_pa->rowCount();
    if ($nb_pa)
    {
      $lienville_pa = $ville["nom_ville_url"].".".$ville["id"].".toutes.petites-annonces.html";
      echo "<li><a href=\"$lienville_pa\" title=\"Petites annonces de ".$ville["nom_ville_maj"]."\">Petites annonces de ".$ville["nom_ville_maj"].", ".$ville["code_postal"]."</a></li>";
    }

    echo "</ul></li>";
  }


?>
</ul>


      </div>
    
<?php
	// Colonne 3
	// $affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');
	
	// Footer
	include ('01_include/structure_footer.php');
?>