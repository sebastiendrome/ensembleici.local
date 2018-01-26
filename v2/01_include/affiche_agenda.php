<?php
// Affichage de l'agenda
$type_objet 	= "evenement";
$page_home 		= false;
$de 	= $a 	= 0;
$nb_pages_aff 	= 3;
$i 				= 1; // Compteur tailles d'images sur page home
$page_autres	= "";

if (($id_ville)&&(!empty($id_ville)))
{
	require_once ('01_include/_connect.php');
	require_once ('01_include/_var_ensemble.php');
	
	// On est sur la Homepage ?
	if ((!$tous_evts)&&(empty($id_tag))&&(empty($id_vie))&&(!$espace_perso))
		$page_home = true;

	if ($tous_evts)
	{
		// Texte d'introduction sur "tout l'agenda"
		echo "<p id=\"intro_tt\">Retrouvez sur cette page tout l’agenda de ";
		
		if ($titre_ville) echo $titre_ville;
		else "la commune sélectionnée";
		echo ".</p>";
		// echo " (classé chronologiquement).</p>";
	}

	
	if ((!$espace_perso)&&(!$page_home))
	{
		echo "<div id=\"agendalocal\" class=\"blocB\">";
		echo "<h1>Agenda</h1>";
	}
	else if ($page_home) 
	{
		// Sur Home, bloc agenda
		echo '<div id="home-evts" class="bloc-home bloc-grs">';
		echo '<div class="titre"></div>';
		echo '<div class="contenu">';
	}

	// Infos pour calcul de la distance d'après les coordonnées
	// first-cut bounding box (in degrees)
	$maxLat = $lat_ville + rad2deg($rayon/$rayon_terre);
	$minLat = $lat_ville - rad2deg($rayon/$rayon_terre);
	// compensate for degrees longitude getting smaller with increasing latitude
	$maxLon = $lon_ville + rad2deg($rayon/$rayon_terre/cos(deg2rad($lat_ville)));
	$minLon = $lon_ville - rad2deg($rayon/$rayon_terre/cos(deg2rad($lat_ville)));
	// convert origin of filter circle to radians
	$lat_ville_age = deg2rad($lat_ville);
	$lon_ville_age = deg2rad($lon_ville);
	
	if (($espace_perso)&&(intval($no_utilisateur_creation)))
	{
		// Affichage de mes évts dans mon compte
		$id_tag = "";
		$id_vie = "";
		$tous_evts = true;
		// Evts de l'utilisateur
		$sql_evt="SELECT E.no, E.etat, E.titre, E.date_debut, E.date_fin, E.heure_debut hd1, E.heure_fin as hf1, E.url_image, E.sous_titre, E.description, G.libelle AS libelle_genre, V.latitude, V.longitude, V.nom_ville_maj, 0 AS Distance, 0 AS OrdreVilleProche
                FROM  `evenement` E,
                `genre` G,
                `villes` V
                WHERE E.no_utilisateur_creation = :no_utilisateur_creation";
		// Annonce ou Evt ?
		
		if ($estAnnonce)
                        $sql_evt .= " AND G.type_genre = 'A'";
		else
                        $sql_evt .= " AND G.type_genre = 'E'";
		$sql_evt .= " AND E.no_genre = G.no
                AND E.no_ville = V.id
                GROUP BY E.no";
		$r_evt = $connexion->prepare($sql_evt);
		$r_evt->execute(array(':no_utilisateur_creation'=>$no_utilisateur_creation));
		$nb_evts_total = $r_evt->rowCount();
	}
	else
	{
		// Page publiques
		// Pagination 
		$apage = intval($_GET["ap"]);
		if (!$apage) $apage=1;
		$start = ($apage-1)*$nb_evts_list;
		$limit = " LIMIT $start, $nb_evts_list";

        // Page évènement et struct sélectionnée, pour ajouter au lien rewritting
        // REPERTOIRE
        $rpage = intval($_GET["rp"]);
        if ($rpage) $page_struct = "&rp=".$rpage;
        else $page_struct = "";
        // PETITES ANNONCES
        $ppage = intval($_GET["pp"]);
        if ($ppage) $page_pa = "&pp=" . $ppage;
        else $page_pa = "";
        // FUSION
        $page_autres = $page_struct.$page_pa;
        if ($page_autres != "") $page_autres = substr_replace($page_autres,"?",0,1);


		// Gestion du tri (date ou distance) sécurisé par url_rewrite
		// OrdreVilleProche pour mettre les evts de la ville en début
		if ($_POST['tri_evts'])
		{
			$le_tri_evts = url_rewrite($_POST['tri_evts']);
			$_SESSION['tri_evts'] = $le_tri_evts;
		}
		elseif ($_SESSION['tri_evts']) $le_tri_evts = url_rewrite($_SESSION['tri_evts']);
	
		if ($page_home) $le_tri_evts = "distance";

		if ($le_tri_evts=="distance")
			//$tri = " ORDER BY `OrdreVilleProche`,`Distance`,`date_fin`";
			$tri = " ORDER BY `Distance`,`date_fin`,`date_debut`";
		else
			//$tri = " ORDER BY `OrdreVilleProche`,`date_fin`,`Distance`";
			$tri = " ORDER BY `date_fin`,`date_debut`,`Distance`";
		// Gestion du rayon
		
		if (isset($_POST['rayon_evts']))
		{
			$le_rayon_evts = intval($_POST['rayon_evts']);
			$_SESSION['rayon_evts'] = $le_rayon_evts;
			// echo "ok".$le_rayon_evts;
		}

		elseif (isset($_SESSION['rayon_evts']))            $le_rayon_evts = intval($_SESSION['rayon_evts']);
		else $le_rayon_evts = 99999; //$le_rayon_evts = 30; modification max
		
		if (($le_rayon_evts)&&($le_rayon_evts!=100))            $filtres_actif = $filtres_actif_rayon = true;
		// pr surbrillance bloc
		else            $le_rayon_evts = 100;
		// Tous
		// Home => Toujours Tous les évènements
		
		if ($page_home)
                        $le_rayon_evts = 100;
		// De la localité seulement
		
		if ($le_rayon_evts==99999)            $le_rayon_evts = 0;
		// Tous les évènements
		
		if ($le_rayon_evts == 100)            $cond_rayon_sql = "";
		else            $cond_rayon_sql = " AND acos(sin($lat_ville_age)*sin(radians(latitude)) + cos($lat_ville_age)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_age))*$rayon_terre <= ".$le_rayon_evts;
		// Gestion de la date "à partir du..."
		
		if (isset($_POST['a_partir_du']))
		{
			// Vérif syntaxe saisie
			
			if(!preg_match('`^(((0[1-9])|(1\d)|(2\d)|(3[0-1]))\/((0[1-9])|(1[0-2]))\/(\d{2}))$`',$_POST['a_partir_du']))
			{
				// pas bon => date du jour
				$le_a_partir_du = date("d/m/y");
			}
			else
			{
				$le_a_partir_du = $_POST['a_partir_du'];
				$_SESSION['a_partir_du'] = $le_a_partir_du;
			}

		}

		elseif (isset($_SESSION['a_partir_du']))            $le_a_partir_du = $_SESSION['a_partir_du'];
		else            $le_a_partir_du = date("d/m/y");
		// Home => Toujours la date du jour
		
		if ($page_home)
                        $le_a_partir_du = date("d/m/y");
		
		if ($le_a_partir_du == date("d/m/y"))
		{
			unset($_SESSION['a_partir_du']);
			$cond_apartirdu_sql = " AND E.date_fin>=CURDATE()";
		}
		else
		{
			// Date choisie différente de la date du jour
			$filtres_actif = true;
			// pr surbrillance bloc
			$dc_annee = substr($le_a_partir_du,6,2);
			$dc_mois = substr($le_a_partir_du,3,2);
			$dc_jour = substr($le_a_partir_du,0,2);
			// Année sur 4 chiffres
			
			if ($dc_annee>90)                $dc_annee = "19".$dc_annee;
			else                $dc_annee = "20".$dc_annee;
			$date_cond = $dc_annee."-".$dc_mois."-".$dc_jour;
			$cond_apartirdu_sql = " AND E.date_fin>='".$date_cond."'";
		}

		
		if (!empty($id_tag))
		{
			// Dans le tag sélectionnée
			$sql_evt="SELECT E.no, E.etat, E.titre, E.date_debut, E.date_fin, E.heure_debut hd1, E.heure_fin as hf1, E.url_image, E.sous_titre, E.description, G.libelle AS libelle_genre, V.latitude, V.longitude, V.nom_ville_maj, 0 AS Distance, 0 AS OrdreVilleProche
                  FROM  `evenement` E,
                        `evenement_tag` S,
                        `genre` G,
                        `villes` V
                WHERE S.no_tag = :id_tag
                  AND E.no_ville = :id_ville
                  AND E.etat = 1
                  $cond_apartirdu_sql
                  AND E.no_genre = G.no
                  AND E.no = S.no_evenement
                  AND E.no_ville = V.id
                  GROUP BY E.no";
			// Lance la requête pour avoir le nb d'évts propres à la ville
			$r_evts_locaux = $connexion->prepare($sql_evt);
			$r_evts_locaux->execute(array(':id_tag'=>$id_tag,':id_ville'=>$id_ville));
			$nb_evts_locaux = $r_evts_locaux->rowCount();
			// Evenements dans les villes proches
			$sql_proche = "SELECT *, 
               acos(sin($lat_ville_age)*sin(radians(latitude)) + cos($lat_ville_age)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_age))*$rayon_terre AS Distance, 1 AS OrdreVilleProche
        FROM (
          SELECT E.no, E.etat, E.titre, E.date_debut, E.date_fin, E.heure_debut hd1, E.heure_fin as hf1, E.url_image, E.sous_titre, E.description, G.libelle AS libelle_genre, V.latitude, V.longitude, V.nom_ville_maj
          FROM  `evenement` E,
                  `evenement_tag` S,
                  `genre` G,
                  `villes` V
                WHERE E.no_ville != :id_ville
                  $cond_rayon_sql
                  AND S.no_tag = :id_tag
                  AND E.etat = 1
                  $cond_apartirdu_sql
                  AND latitude>$minLat And latitude<$maxLat
                  AND longitude>$minLon And longitude<$maxLon
                  AND E.no_genre = G.no
                  AND E.no = S.no_evenement
                  AND E.no_ville = V.id
          ) As FirstCut 
        WHERE acos(sin($lat_ville_age)*sin(radians(latitude)) + cos($lat_ville_age)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_age))*$rayon_terre < $rayon
        $cond_rayon_sql";
			// Pour calcul nb total d'évts
			$sql_evt_total = "($sql_evt) UNION ($sql_proche)";
			$r_evt_total = $connexion->prepare($sql_evt_total);
			$r_evt_total->execute(array(':id_tag'=>$id_tag,':id_ville'=>$id_ville));
			$nb_evts_total = $r_evt_total->rowCount();
			$nb_pages = ceil($nb_evts_total/$nb_evts_list);
			// Préparation du lien pour pagination
			
			if (!empty($id_vie)) $url_id_vie = ".".$id_vie;
			// id_vie passée en param pour provenance du tag
			$lien_pagination_url = $titre_ville_url.".".url_rewrite($titre_nomtag).".tag.".$id_ville.".".$id_tag.$url_id_vie;
			$lien_pagination_alt = $titre_nomtag." à ".$titre_ville.", page ";
			// url de destination des forms de filtre / tri (sans pagination)
			$lien_filtre_tri = $titre_ville_url.".".url_rewrite($titre_nomtag).".tag.".$id_ville.".".$id_tag.".html";
			// Pour affichage
			$sql_evt = "($sql_evt) UNION ($sql_proche)".$tri.$limit;
			$r_evt = $connexion->prepare($sql_evt);
			$r_evt->execute(array(':id_tag'=>$id_tag,':id_ville'=>$id_ville));
		}

		elseif (!empty($id_vie))
		{
			// Dans la vie sélectionnée
			$sql_evt="SELECT E.no, E.etat, E.titre, E.date_debut, E.date_fin, E.heure_debut hd1, E.heure_fin as hf1, E.url_image, E.sous_titre, E.description, G.libelle AS libelle_genre, I.latitude, I.longitude, I.nom_ville_maj, 0 AS Distance, 0 AS OrdreVilleProche
                  FROM  `evenement` E,
                        `evenement_tag` S,
                        `vie_tag` V,
                        `villes` I,
                        `genre` G
                WHERE V.no_vie = :id_vie
                  AND E.no_ville = :id_ville
                  AND E.etat = 1
                  $cond_apartirdu_sql
                  AND E.no_genre = G.no
                  AND E.no = S.no_evenement
                  AND S.no_tag = V.no_tag
                  AND E.no_ville = I.id
                  GROUP BY E.no";
			// Lance la requête pour avoir le nb d'évts propres à la ville
			$r_evts_locaux = $connexion->prepare($sql_evt);
			$r_evts_locaux->execute(array(':id_vie'=>$id_vie,':id_ville'=>$id_ville));
			$nb_evts_locaux = $r_evts_locaux->rowCount();
			// Evenements dans les villes proches
			$sql_proche = "SELECT *, 
               acos(sin($lat_ville_age)*sin(radians(latitude)) + cos($lat_ville_age)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_age))*$rayon_terre AS Distance, 1 AS OrdreVilleProche
        FROM (
          SELECT E.no, E.etat, E.titre, E.date_debut, E.date_fin, E.heure_debut hd1, E.heure_fin as hf1, E.url_image, E.sous_titre, E.description, G.libelle AS libelle_genre, V.latitude, V.longitude, V.nom_ville_maj
          FROM  `evenement` E,
                  `evenement_tag` S,
                  `genre` G,
                  `vie_tag` I,
                  `villes` V
                WHERE E.no_ville != :id_ville
                  AND I.no_vie = :id_vie
                  AND E.etat = 1
                  $cond_apartirdu_sql
                  AND latitude>$minLat And latitude<$maxLat
                  AND longitude>$minLon And longitude<$maxLon
                  AND E.no_genre = G.no
                  AND E.no = S.no_evenement
                  AND S.no_tag = I.no_tag
                  AND E.no_ville = V.id
          ) As FirstCut 
        WHERE acos(sin($lat_ville_age)*sin(radians(latitude)) + cos($lat_ville_age)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_age))*$rayon_terre < $rayon
        $cond_rayon_sql";
			// Pour calcul nb total d'évts
			$sql_evt_total = "($sql_evt) UNION ($sql_proche)";
			$r_evt_total = $connexion->prepare($sql_evt_total);
			$r_evt_total->execute(array(':id_vie'=>$id_vie,':id_ville'=>$id_ville));
			$nb_evts_total = $r_evt_total->rowCount();
			$nb_pages = ceil($nb_evts_total/$nb_evts_list);
			// Préparation du lien pour pagination
			$lien_pagination_url = $titre_ville_url.".".url_rewrite($nom_url_vie).".".$id_ville.".".$id_vie;
			$lien_pagination_alt = $titre_nomvie." à ".$titre_ville.", page ";
			// url de destination des forms de filtre / tri (sans pagination)
			$lien_filtre_tri = $titre_ville_url.".".url_rewrite($nom_url_vie).".".$id_ville.".".$id_vie.".html";
			// Pour affichage
			$sql_evt = "($sql_evt) UNION ($sql_proche)".$tri.$limit;
			$r_evt = $connexion->prepare($sql_evt);
			$r_evt->execute(array(':id_vie'=>$id_vie,':id_ville'=>$id_ville));
		}
		elseif ($tous_evts)
		{
			// Affichage sur Tout l'agenda
			$page_accueil_ville = true;

			// Filtrage par tag
			if (isset($_POST['cond_tag']))
			{
				$la_cond_tag = intval($_POST['cond_tag']);
				$_SESSION['cond_tag'] = $la_cond_tag;
			}

			elseif ($_SESSION['cond_tag']) $la_cond_tag = intval($_SESSION['cond_tag']);

			if ($la_cond_tag) $filtres_actif = true;
			// pr surbrillance bloc
			if ($la_cond_tag)
			{
				$cond_tag_sql = " AND S.no_tag = :id_tag";
				$cond_tag_val = $la_cond_tag;
			}
			else
			{
				$cond_tag_sql = $cond_tag_val = "";
			}

			// Dans la ville
			$sql_evt="SELECT E.no, E.etat, E.titre, E.date_debut, E.date_fin, E.heure_debut hd1, E.heure_fin as hf1, E.url_image, E.sous_titre, E.description, G.libelle AS libelle_genre, V.latitude, V.longitude, V.nom_ville_maj, 0 AS Distance, 0 AS OrdreVilleProche
                  FROM  `evenement` E,
                  `evenement_tag` S,
                  `genre` G,
                  `villes` V
                WHERE E.no_ville = :id_ville
                  $cond_tag_sql
                  AND E.etat = 1
                  $cond_apartirdu_sql
                  AND E.no_genre = G.no
                  AND E.no_ville = V.id
                  AND E.no = S.no_evenement
                  GROUP BY E.no";
			// Lance la requête pour avoir le nb d'évts propres à la ville
			$r_evts_locaux = $connexion->prepare($sql_evt);
			$r_evts_locaux->bindValue(':id_ville', $id_ville, PDO::PARAM_INT);

			if ($cond_tag_val) $r_evts_locaux->bindValue(':id_tag', $cond_tag_val, PDO::PARAM_INT);
			$r_evts_locaux->execute();
			$nb_evts_locaux = $r_evts_locaux->rowCount();
			// Evenements dans les villes proches
			$sql_proche = "SELECT *, 
               acos(sin($lat_ville_age)*sin(radians(latitude)) + cos($lat_ville_age)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_age))*$rayon_terre AS Distance, 1 AS OrdreVilleProche
        FROM (
          SELECT E.no, E.etat, E.titre, E.date_debut, E.date_fin, E.heure_debut hd1, E.heure_fin as hf1, E.url_image, E.sous_titre, E.description, G.libelle AS libelle_genre, V.latitude, V.longitude, V.nom_ville_maj
          FROM  `evenement` E,
                  `evenement_tag` S,
                  `genre` G,
                  `villes` V
                WHERE E.no_ville != :id_ville
                  $cond_tag_sql
                  $cond_rayon_sql
                  AND E.etat = 1
                  $cond_apartirdu_sql
                  AND latitude>$minLat AND latitude<$maxLat
                  AND longitude>$minLon AND longitude<$maxLon
                  AND E.no_genre = G.no
                  AND E.no_ville = V.id
                  AND E.no = S.no_evenement
          ) As FirstCut 
        WHERE acos(sin($lat_ville_age)*sin(radians(latitude)) + cos($lat_ville_age)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_age))*$rayon_terre < $rayon
        $cond_rayon_sql";
			// Pour calcul nb total d'évts
			$sql_evt_total = "($sql_evt) UNION ($sql_proche)";
			$r_evt_total = $connexion->prepare($sql_evt_total);
			$r_evt_total->bindValue(':id_ville', $id_ville, PDO::PARAM_INT);

			if ($cond_tag_val)            $r_evt_total->bindValue(':id_tag', $cond_tag_val, PDO::PARAM_INT);
			$r_evt_total->execute();
			$nb_evts_total = $r_evt_total->rowCount();
			$nb_pages = ceil($nb_evts_total/$nb_evts_list);

			// Préparation du lien pour pagination
			$lien_pagination_url = $titre_ville_url.".".$id_ville.".tout.agenda";
			$lien_pagination_alt = " Tout l'agenda de ".$titre_ville.", page ";
			// url de destination des forms de filtre / tri (sans pagination)
			$lien_filtre_tri = $titre_ville_url.".".$id_ville.".tout.agenda.html";

			// Pour affichage
			$sql_evt = "($sql_evt) UNION ($sql_proche)".$tri.$limit;
			$r_evt = $connexion->prepare($sql_evt);
			$r_evt->bindValue(':id_ville', $id_ville, PDO::PARAM_INT);

			if ($cond_tag_val)            $r_evt->bindValue(':id_tag', $cond_tag_val, PDO::PARAM_INT);
			$r_evt->execute();
		}
		else
		{
			// DEFAUT : Affichage sur Home (seulement les evts avec image, les premiers random et le top 3 des plus likés ensuite)
			$page_accueil_ville = true;

			$limit = " LIMIT 0, $nb_evts_home";

			// Dans la ville
			$sql_evt="SELECT E.no, E.etat, E.titre, E.date_debut, E.date_fin,  E.heure_debut hd1, E.heure_fin as hf1, E.url_image, E.sous_titre, E.description, G.libelle AS libelle_genre, V.latitude, V.longitude, V.nom_ville_maj, 0 AS Distance, 1 AS ordrelike, 0 AS OrdreVilleProche
                  FROM  `evenement` E,
                  `evenement_tag` S,
                  `genre` G,
                  `villes` V
                WHERE E.no_ville = :id_ville
                  AND E.etat = 1
                  AND E.url_image <> ''
                  AND E.date_fin<=DATE_ADD(E.date_debut, INTERVAL 31 DAY)
                  $cond_apartirdu_sql
                  AND E.no_genre = G.no
                  AND E.no_ville = V.id
                  AND E.no = S.no_evenement
                  GROUP BY E.no";

			// Lance la requête pour avoir le nb d'évts propres à la ville
			$r_evts_locaux = $connexion->prepare($sql_evt);
			$r_evts_locaux->bindValue(':id_ville', $id_ville, PDO::PARAM_INT);
			$r_evts_locaux->execute();
			$nb_evts_locaux = $r_evts_locaux->rowCount();

			if ($nb_evts_locaux >= $nb_evts_home)
			{
				// nb d'evt suffisant
				$nb_evts_total = $nb_evts_locaux;
				$sql_evt .= " ORDER BY RAND()".$limit;
			}
			else
			{
				// Evenements dans les villes proches
				$sql_proche = "SELECT *,
                   acos(sin($lat_ville_age)*sin(radians(latitude)) + cos($lat_ville_age)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_age))*$rayon_terre AS Distance, 1 AS ordrelike, 1 AS OrdreVilleProche
            FROM (
              SELECT E.no, E.etat, E.titre, E.date_debut, E.date_fin, E.heure_debut hd1, E.heure_fin as hf1, E.url_image, E.sous_titre, E.description, G.libelle AS libelle_genre, V.latitude, V.longitude, V.nom_ville_maj
              FROM  `evenement` E,
                      `evenement_tag` S,
                      `genre` G,
                      `villes` V
                    WHERE E.no_ville != :id_ville
                      $cond_tag_sql
                      $cond_rayon_sql
                      AND E.url_image <> ''
                      AND E.date_fin<=DATE_ADD(E.date_debut, INTERVAL 31 DAY)
                      AND E.etat = 1
                      $cond_apartirdu_sql
                      AND latitude>$minLat AND latitude<$maxLat
                      AND longitude>$minLon AND longitude<$maxLon
                      AND E.no_genre = G.no
                      AND E.no_ville = V.id
                      AND E.no = S.no_evenement
              ) As FirstCut 
            WHERE acos(sin($lat_ville_age)*sin(radians(latitude)) + cos($lat_ville_age)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_age))*$rayon_terre < $rayon
            $cond_rayon_sql";
				// Pour calcul nb total d'évts
				$sql_evt_total = "($sql_evt) UNION ($sql_proche)";
				$r_evt_total = $connexion->prepare($sql_evt_total);
				$r_evt_total->bindValue(':id_ville', $id_ville, PDO::PARAM_INT);
				$r_evt_total->execute();
				$nb_evts_total = $r_evt_total->rowCount();

				// Les requêtes jointes
				$sql_evt = "($sql_evt) UNION ($sql_proche) ORDER BY Distance,RAND()".$limit;
			}

			/* // Pour affichage
			$r_evt = $connexion->prepare($sql_evt);
			$r_evt->bindValue(':id_ville', $id_ville, PDO::PARAM_INT);
			$r_evt->execute();*/

			// Top 3 des plus likés
			$sql_evt_like="SELECT E.no, E.etat, E.titre,  E.heure_debut hd1, E.heure_fin as hf1,  E.date_debut, E.date_fin, E.url_image, E.sous_titre, E.description, G.libelle AS libelle_genre, V.latitude, V.longitude, V.nom_ville_maj, 0 AS Distance, 2 AS ordrelike
				FROM  `evenement` E,
				`evenement_tag` S,
				`genre` G,
				`villes` V
			      WHERE E.etat = 1
				AND E.url_image <> ''
				AND E.date_fin<=DATE_ADD(E.date_debut, INTERVAL 31 DAY)
				$cond_apartirdu_sql
				AND E.no_genre = G.no
				AND E.no_ville = V.id
				AND E.no = S.no_evenement
				GROUP BY E.no
				ORDER BY E.nb_aime DESC
				LIMIT 0,3";

				// echo $sql_evt_like;

			// Lance la requête du top 3
			$r_evts_likes = $connexion->prepare($sql_evt_like);
			$r_evts_likes->execute();
			$ta_evts_likes = $r_evts_likes->fetchAll(PDO::FETCH_ASSOC);

			// Pour affichage
			$r_evt = $connexion->prepare($sql_evt);
			$r_evt->bindValue(':id_ville', $id_ville, PDO::PARAM_INT);
			$r_evt->execute();
			// $nb_evts_home = $r_evt->rowCount();
		}
	}

	// Filtres
	if ((!$espace_perso)&&(!$page_home))
	{
		// Filtre
		if ($filtres_actif) $filtres_actif_aff = " filtres_actif";
		echo "<div class=\"filtres$filtres_actif_aff\">";
		// Tri par date / distance et filtre par rayon
		echo "<form name=\"ETri\" id=\"ETri\" action=\"$lien_filtre_tri\" method=\"post\" accept-charset=\"UTF-8\" class=\"formA\">
            Afficher les évènements : 
            <select name=\"rayon_evts\"".($filtres_actif_rayon?" class=\"filtre-actif\"":
		"").">
                    <option value=\"99999\"";
		
		if ($le_rayon_evts==99999) echo " selected";
		echo ">de la localité seulement</option>
                    <option value=\"10\"";
		
		if ($le_rayon_evts==10) echo " selected";
		echo ">< 10 kms</option>
                        <option value=\"30\"";
		
		if ($le_rayon_evts==30) echo " selected";
		echo ">< 30 kms</option>
                        <option value=\"100\"";
		
		if ($le_rayon_evts==100) echo " selected";
		
		if (!$le_a_partir_du) $le_a_partir_du = date("d/m/y");
		echo ">Tous</option>
            </select>
            à partir du : 
            <input name=\"a_partir_du\" class=\"input a_partir_du".(isset($_SESSION['a_partir_du'])?" filtre-actif":
		"")."\" value=\"$le_a_partir_du\">
            <br/>Triés par : 
            <select name=\"tri_evts\">
                    <option value=\"distance\"";
		
		if ($le_tri_evts=="distance") echo " selected";
		echo ">Distance</option>
                    <option value=\"date\"";
		
		if ($le_tri_evts!="distance") echo " selected";
		echo ">Date</option>
            </select>
        </form>"; //(ville)

		echo "<script type=\"text/javascript\">
            $(function(){
                $(\"form#ETri select\").change(function() {
                  $(\"form#ETri\").submit();
                });
                /* jQuery UI date picker français */
                $.datepicker.regional['fr'] = {
                        closeText: 'x',
                        prevText: '&#x3c;Préc',
                        nextText: 'Suiv&#x3e;',
                        currentText: 'Aujourd\'hui',
                        monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
                        'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
                        monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun',
                        'Jul','Aoû','Sep','Oct','Nov','Déc'],
                        dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
                        dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
                        dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
                        weekHeader: 'Sm',
                        dateFormat: 'dd/mm/yy',
                        firstDay: 1,
                        isRTL: false,
                        showMonthAfterYear: false,
                        yearSuffix: ''};
                $.datepicker.setDefaults($.datepicker.regional['fr']);
                $(\".a_partir_du\").datepicker({
                        dateFormat:\"dd/mm/y\",
                        defaultDate: \"+1w\",
                        minDate:0,
                        showButtonPanel: true,
                        changeMonth: true,
                        autoSize: true,
                        onSelect: function (dateText, inst) {
                            $(this).parent('form').submit();
                        }
                });
            });
        </script>";
		// Filtre par Tag
		
		if ($tous_evts)
		{
			// Récupère la liste des tags
			$sql_tag="SELECT DISTINCT no, titre
                        FROM `evenement_tag` E, `tag` T
                        WHERE E.no_tag = T.no
                        ORDER BY T.titre";
			$res_tag = $connexion->prepare($sql_tag);
			$res_tag->execute();
			
			if ($cond_tag_sql) $class_cond_tag = "class=\"cond_tag_active\"";
			echo "<form name=\"ECondTag\" id=\"ECondTag\" $class_cond_tag action=\"$lien_filtre_tri\" method=\"post\" accept-charset=\"UTF-8\">
                Thématique : 
                <select name=\"cond_tag\">
                    <option value=\"0\"";
			
			if (!$la_cond_tag) echo " selected";
			echo ">Toutes</option>\n";
			while($t_tag = $res_tag->fetch(PDO::FETCH_ASSOC))
			{
				
				if ((!empty($t_tag["no"]))||(!empty($t_tag["titre"])))
				{
					echo "<option value=\"".$t_tag["no"]."\"";
					
					if ($la_cond_tag==$t_tag["no"]) echo " selected";
					echo ">".$t_tag["titre"]."</option>\n";
				}

			}

			echo "</select>
            </form>";
			echo "<script type=\"text/javascript\">
                $(function(){
                    $(\"form#ECondTag select\").change(function() {
                      $(\"form#ECondTag\").submit();
                    });
                });
            </script>";
		}

		echo "</div>";
		// Fin Filtre
		echo "<p class=\"nb_evts\">";
		
		if ($nb_evts_total>$nb_evts_list)
		{
			// Affichage du nombre de résultats affichés
			$de = $start +1;
			
			if ($start==0)                $a = $nb_evts_list;
			else                $a = $de+$nb_evts_list-1;
			
			if ($a>$nb_evts_total)                $a = $nb_evts_total;
			echo "Résultats $de - $a sur $nb_evts_total<br/>";
		}

		// Affiche le nombre d'évenements total
		
		if ((!$page_accueil_ville)||($tous_evts))
		{
			// Nombre d'évenements en bas de page sur la page d'accueil
			if ($nb_evts_locaux)
			{
				// Evenements locaux / proches
				echo $nb_evts_locaux." évènement";
				if ($nb_evts_locaux>1) echo "s";
				echo " dans la ville";
			}

			$nb_evts_proches = $nb_evts_total - $nb_evts_locaux;
			
			if ($nb_evts_proches)
			{
				if ($nb_evts_locaux)
					echo " + ".$nb_evts_proches;
				else
				{
					echo $nb_evts_proches." évènement";
					if ($nb_evts_proches>1) echo "s";
				}
				echo " proche";
				if ($nb_evts_proches>1) echo "s";
			}
		}

		echo "</p>";
		// Pagination

		if (($nb_pages)&&($nb_evts_total>$nb_evts_list))
		{

            // Paramètres passés en GET
            if ($page_autres == "")
                $page_autres_p = "?ap=";
            else
                $page_autres_p = $page_autres."&ap=";


			$apagination = "<div class=\"pagination\">";
			// Lien retour

			if ($apage > 1)
			{
				// page précédente <
				$prevpage = $apage - 1;
                $apagination .= "<a href='" . $lien_pagination_url . ".html" . $page_autres_p . $prevpage . "#agendalocal'><</a>";
				// Lien page 1

				if ($apage > $nb_pages_aff + 1) 
                    $apagination .= "<a href='" . $lien_pagination_url . ".html" . $page_autres_p . "1#agendalocal'>1</a>";
			}

			if ($apage-$nb_pages_aff > 2) $apagination .= " ... ";

			for ($x = ($apage - $nb_pages_aff); $x < (($apage + $nb_pages_aff) + 1); $x++)
			{

				if (($x > 0) && ($x <= $nb_pages))
				{
                    // page active ?
                    if ($x == $apage)
                        $apagination .= "<a href=\"\" class=\"actif\">" . $x . "</a>";
                    else
                        $apagination .= "<a href=\"" . $lien_pagination_url . ".html" . $page_autres_p . $x . "#agendalocal\" title=\"" . $lien_pagination_alt . $x . "\">" . $x . "</a>";
				}

			}

			if ($apage<$nb_pages-$nb_pages_aff-1) $apagination .= " ... ";
			// Lien suivant et fin

			if ($apage != $nb_pages)
			{
				$nextpage = $apage + 1;

				// lien dernière page
                if ($nb_pages - $apage > $nb_pages_aff)
                    $apagination .= "<a href='" . $lien_pagination_url . ".html" . $page_autres_p . $nb_pages . "#agendalocal'>$nb_pages</a>";

                $apagination .= "<a href='" . $lien_pagination_url . ".html" . $page_autres_p . $nextpage . "#agendalocal'>></a>";

			}

			$apagination .= "</div>";
			echo $apagination;
		}

		echo "<div class=\"clear\"></div>";
	} // Fin Filtres sur pages autres que espace_perso et page_home
	elseif ($page_home)
	{
		// Affiche le nombre total d"évènements
		
		if ($nb_evts_locaux || $nb_evts_total)
		{
			$nb_evts_proches = $nb_evts_total - $nb_evts_locaux;
			$aff_lien_nb_evts = "<a class=\"lien_nb_evts\" href=\"".$root_site;
			$aff_lien_nb_evts .= $titre_ville_url.".".$id_ville.".tout.agenda.html";
			$aff_lien_nb_evts .= "\" title=\"Voir tout l'agenda\">";
			if ($nb_evts_locaux)
			{
				$aff_lien_nb_evts .= $nb_evts_locaux." évènements dans la ville";
				if ($nb_evts_proches) $aff_lien_nb_evts .= "<br/>et ".$nb_evts_proches." proches";
			}
			else
			{
				
				if ($nb_evts_proches) $aff_lien_nb_evts .= $nb_evts_proches." évènements proches de la ville";
			}

			$aff_lien_nb_evts .= "</a>";
		}
	} // Fin Filtres sur home

	// Affichage des résultats
	if ($nb_evts_total>0)
	{
		// Les résultats principaux (tableau)
		$ta_evts = $r_evt->fetchAll(PDO::FETCH_ASSOC);

		// TOP 3 sur la Homepage : Fusionne les 2 tableaux de résultat
		if ($ta_evts_likes && $page_home)
			$ta_evts = array_merge($ta_evts,$ta_evts_likes);

		foreach($ta_evts as $t_evts)
		{
			$imgsrc = "";

			// Début if titre et no
			if ((!empty($t_evts["no"]))||(!empty($t_evts["titre"])))
			{
				// Nom de l'evt coupé à 130 carractères pour le lien
				$titre_pour_lien = coupe_chaine($t_evts["titre"],130,false);
				// Affichage sur la home page (graphique)
				if ($page_home) 
				{
					// vérifie que l'image existe bien
					$image = $t_evts["url_image"];
					if(strlen($image)>0)
					{
						// image distante ?
						if (strpos($image, "http://www.culture-provence-baronnies.fr") !== false)
						{
							$imgsrc = $image;
							$image = "";
							$image_locale = false;
						}
						else
						{
						// Image locale ?
							// Image stockée sur la version de dev ou sur le site en prod

		// Image locale
		if (fichier_existant($root_site.$image))
		{
			// Image existante dans ce dossier
			$root_site_d = $root_site;
		}
		else
		{
		  // Image inexistante => on test dans un autre dossier
		  if ($root_site == $root_site_dev)
		    $root_site_d = $root_site_prod;
		  else
		    $root_site_d = $root_site_dev;

			// Image toujours non existante ? => image par défaut
			if (!fichier_existant($root_site_d.$image))
				$image = "img/logo-ensembleici_fb.jpg";
		}

		// Image locale ok ?
		if ($image)
		{
			$imgsrc = $root_site_d."miniature.php?uri=".$t_evts["url_image"]."&method=fit";
			$image_locale = true;
		}
	}
}

					// L'image existe, on affiche
					if ($imgsrc)
					{
						// Determine la taille de l'image
						if (($i == 1)||($i == 4))
						{
							// grande
							$img_taille = 'w=300&h=220';
							$img_class = "img-gd";
							$nb_car_titre = 80;
						}
						else
						{
							// petite
							$img_taille = 'w=150&h=100';
							$img_class = "img-pt";
							$nb_car_titre = 30;
						}
						if ($image_locale) $imgsrc .= "&".$img_taille;

						// genre pour l'url
						if ($t_evts["libelle_genre"])
							$titre_pour_lien = $t_evts["libelle_genre"]."-".$titre_pour_lien;

						// Bg du top 3
						if ($i == 5)
							echo '<div id="top3"></div>';

						// Préparation du lien
						$lien = "evenement.".$titre_ville_url.".".url_rewrite($titre_pour_lien).".".$id_ville.".".$t_evts["no"].".html";

						echo '<div class="home-img-evt-cont '.$img_class.' img'.$i.'">';

							echo "<a href=\"".$lien."\" title=\"Voir ".str_replace('"',"'",$t_evts["titre"])."\">";
								echo '<div class="img">';
									echo "<img src=\"".$imgsrc."\" alt=\"".str_replace('"',"'",$t_evts["titre"])."\" />";
								echo '</div>';
							echo '</a>';

							// Date
							$t_evts["date_debut"];
							$annee = substr($t_evts["date_debut"],0,4);
							$moisfr = array("","janv","févr","mars","avr","mai","juin","juill","août","sept","oct","nov","déc");
							$moisfrlong = array("","janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre");						
							$moisa = intval(substr($t_evts["date_debut"],5,2));
							$mois = $moisfr[$moisa];
							$moislong = $moisfrlong[$moisa];
							$jour = substr($t_evts["date_debut"],8,2);
							echo '<div class="date">';
								echo "<span class=\"jr\">$jour</span> <span class=\"ms\">$mois</span> <span class=\"an\">$annee</span>";
							echo '</div>';

							// Description
							echo '<div class="des">';
								echo "<a href=\"".$lien."\" title=\"Voir ".str_replace('"',"'",$t_evts["titre"])."\">";
								if ($t_evts["date_debut"])
								{
								
									if (($t_evts["date_fin"])&&($t_evts["date_debut"]!=$t_evts["date_fin"]))
									{
										echo "<span>Du ".datefr($t_evts["date_debut"])." au ".datefr($t_evts["date_fin"])." : </span>";
									}
									else
									{
										echo "<span>".$jour." ".$moislong." ".$annee." : </span>";
									}
								}
								if(strlen($t_evts["titre"])>$nb_car_titre)
									echo coupe_chaine(strip_tags(ucfirst($t_evts["titre"])),$nb_car_titre,true);
								else 
									echo ucfirst($t_evts["titre"]);

								if ($t_evts["libelle_genre"])
									echo " - ".$t_evts["libelle_genre"];

								if ($t_evts["nom_ville_maj"] != $titre_ville)
									echo " à ".$t_evts["nom_ville_maj"];

								echo "<br/>";
	
								// Liens favoris
								echo '<div class="liens_favlike">';
									$no_occurence = $t_evts["no"];
									$type_objet = "evenement";
									require ('ajout_fav.php');
									require ('ajout_like.php');
								echo '</div>';

								echo '</a>';
							echo '</div>'; // Fin description

						echo "</div>"; // Fin bloc home-img-evt-cont

					} // Fin L'image existe
					$i++; // Compteur pour tailles d'images
				}
				else // Affichage sur les pages non Home
				{
					// Evenement actif ?
					if ($t_evts["etat"]==0) $evt_actif = false;
					else $evt_actif = true;
					// Evenement expiré ?
					$date_auj = new DateTime();
					$date_auj->setTime(0,0);
					$date_fin = new DateTime($t_evts["date_fin"]);
					
					if ($date_auj <= $date_fin)
						$evt_expire = false;
					else
						$evt_expire = true;
					echo "<article><div class=\"un-event\">";
					
					if ($t_evts["libelle_genre"])
					{
						echo "<div class=\"genre_ville\">";
						echo "<div class=\"libelle_genre\">".$t_evts["libelle_genre"]."</div>";
						echo "<div class=\"ville\">".$t_evts["nom_ville_maj"]."</div>";
						echo "</div>";
						// Ajout du genre à l'url
						$titre_pour_lien = $t_evts["libelle_genre"]."-".$titre_pour_lien;
					}

					// Lien vers le détails de l'évenement. (evts activés, non expirés)
					
					if (!empty($id_vie)) $url_id_vie = ".".$id_vie;
					// id_vie passée en param pour provenance
					
					if (!empty($id_tag)) $url_id_tag = ".".$id_tag;
					// id_tag passée en param pour provenance
					
					$titre_ville = url_rewrite($t_evts["nom_ville_maj"]);
					if ($evt_actif && !$evt_expire)
						$lien = "evenement.".$titre_ville.".".url_rewrite($titre_pour_lien).".".$id_ville.".".$t_evts["no"].$url_id_vie.$url_id_tag.".html";
					
					if ($evt_actif && !$evt_expire)
						echo "<a href=\"".$lien."\" title=\"Voir en détails\">";
					// Titre
					echo "<h2>";
					
					// inisialisation des variables
					// $heured1="";
					// $heuref1="";
					
					// if($t_evts["hd1"]!=null && $t_evts["hd1"]!=""){
						// $heured1=" à ".substr($t_evts["hd1"],0 , -3)." ";
						// $heured2=" de ".substr($t_evts["hd1"],0 , -3)." ";
					// }
					// else{
						// $heured1="";
					// }
					
					// if($t_evts["hf1"]!=null && $t_evts["hf1"]!=""){
						// $heuref1=" à ".substr($t_evts["hf1"],0 , -3)." ";
					// }
					// else{
						// $heuref1="";
					// }
					
					if ($t_evts["date_debut"])
					{
						
						if (($t_evts["date_fin"])&&($t_evts["date_debut"]!=$t_evts["date_fin"]))
						{
							echo "<span>Du ".datefr($t_evts["date_debut"])." au ".datefr($t_evts["date_fin"])." : </span>";//$heured2.$heuref1.
						}
						else
						{							
							echo "<span>".datefr($t_evts["date_debut"])." : </span>"; //$heuref1
						}

					}

					echo ucfirst($t_evts["titre"])."</h2>";
					// Bouton j'aime et bouton favoris (parametre : $type_objet)
					echo "<br clear=\"right\" />";
					$no_occurence = $t_evts["no"];
					require ('ajout_fav.php');
					require ('ajout_like.php');
					
					if ($evt_actif && !$evt_expire)                echo "</a>";
					// Image
					$image = $t_evts["url_image"];
					
					if(strlen($image)>0)
					{
						
						if (strpos($image, "http://www.culture-provence-baronnies.fr") !== false)
						{
							// image distante
							echo "<div class=\"illustr\"><a href=\"".$lien."\" title=\"Voir en détails\">";
							echo "<img src=\"".$image."\" alt=\"".str_replace('"',"'",$t_evts["titre"])."\" width=\"80\" />";
							echo "</a></div>";
						}
						else
						{
							// Image locale
							// Image stockée sur la version de dev ou sur le site en prod
							$fileUrl = $root_site.$image;
							$AgetHeaders = @get_headers($fileUrl);

							if (preg_match("|200|", $AgetHeaders[0]))
							{
								// fichier existant
								$root_site_d = $root_site;
							}
							else
							{
								// fichier non existant => on essaie l'autre chemin

								if ($root_site == $root_site_dev)                                    $root_site_d = $root_site_prod;
								else                                    $root_site_d = $root_site_dev;
								// Image toujours non existante ?
								$fileUrl = $root_site_d.$image;
								$AgetHeaders = @get_headers($fileUrl);

								if (!(preg_match("|200|", $AgetHeaders[0])))
								{
									// fichier inexistant => vide
									$image = "";
								}

							}

							if ($image)
							{
								echo "<div class=\"illustr\"><a href=\"".$lien."\" title=\"Voir en détails\">";
								echo "<img src=\"".$root_site_d."miniature.php?uri=".$t_evts["url_image"]."&method=fit&w=80\" alt=\"".str_replace('"',"'",$t_evts["titre"])."\" width=\"80\" />";
								echo "</a></div>";
							}
						}
					}

					echo "<p><strong>".ucfirst($t_evts["sous_titre"])."</strong></p>";
					$maDescript = strip_word_html($t_evts["description"], $allowed_tags = '<br><br/><br />');
					echo "<p>".coupe_chaine(strip_tags($maDescript),250,true)."</p>";
					// Afficher 250 carractères de la description
					// Faire la gestion du lien
					echo "<div class=\"actions\">";
					// bouton distance, arrondit à la dizaine supérieure

					if (ceil(round($t_evts["Distance"])))
					{
						$distance_evt = ceil(round($t_evts["Distance"]) / 10)*10;
						echo "<div title=\"A moins de $distance_evt kms, à vol d'oiseau\" class=\"boutonrouge ico-distance-rge infobulle-b\">< ".$distance_evt." kms</div>";
					}

					
					if ($espace_perso)
					{
						// Alerte pour les évts désactivés
						
						if (!$evt_actif)
						{
							echo "<div title=\"Evènement désactivé par l'équipe 'Ensemble ici'\" class=\"boutonrouge  infobulle-b\">Evènement désactivé</div>";
						}

						// Alerte pour les évts expirés
						
						if ($evt_expire)
						{
							echo "<div title=\"Evènement expiré\" class=\"boutonrouge  infobulle-b\">Evènement expiré</div>";
						}

						// bouton modifier
						echo '<form method="POST" action="auto_previsu.php?type=';
						
						if ($estAnnonce)                    echo 'annonce';
						else                    echo 'evenement';
						echo '" class="formA">
		                <input name="no_fiche" value="'.$t_evts["no"].'" type="hidden">
		                <input name="source" value="espaceperso" type="hidden">
		                <input name="type_fiche" value="evenement" type="hidden">
		                <button type="submit" class="boutonbleu ico-modifier">Modifier</button>
		                </form>';
					}
					else
					{
						echo "<a href=\"".$lien."\" title=\"Voir en détails\" class=\"boutonrouge ico-loupe-rge\">Voir</a>";
					}

					echo "</div>";
					// actions          
					echo "</div></article>";
					// un-event
				} // Fin affichage non Home
			} // Fin if titre et no 
		} // Fin while

		// Sur Home, Fin bloc agenda
		if ($page_home) 
		{
			// Bloc Les plus likés
			echo '<div class="toplike">
				<span>Top 3</span><br/>des évènements <br/>les plus aimés :
			</div>';

			// Liens Voir tout et ajouter

			echo '<div class="liens">';
				echo $aff_lien_nb_evts;
				echo "<a href=\"";
				echo $root_site;
				echo $titre_ville_url.".".$id_ville.".tout.agenda.html";
				echo "\" title=\"Voir tout l'agenda\" class=\"boutonbleu ico-agenda\">Tout l'agenda</a>";
				echo "<a href=\"ajouter_un_evenement.html\" title=\"Ajouter un évenement\" class=\"boutonbleu ico-ajout btonajour\">Ajouter un évenement</a>";
			echo '</div>';

			echo '</div></div>';
			echo '<script type="text/javascript">
				$(function() {
					// hover evt
					$(".home-img-evt-cont").mouseenter(function(){
						$(this).find(".des").delay(100).slideToggle("fast");
						$(this).find(".date").delay(100).animate({width: "toggle"});
					}).mouseleave(function(){
						$(this).find(".des").slideToggle("fast");
						$(this).find(".date").animate({width: "toggle"});
					});
				});
			</script>';
		}
	}
	else
	{
		echo "<div id=\"message\">";
		
		if ($estAnnonce)            echo 'Aucune annonce';
		else            echo 'Aucun évenement';
		echo " actuellement.</div>";
		// actions
	}

	
	if ((!$espace_perso)&&(!$page_home))
	{
		echo $apagination;
		echo "<div class=\"clear\"></div>";
		echo "</div>";
		// agendalocal
		echo "<!-- Actions sur agenda local -->
    <div class=\"actions\">";
		// Lien "tous les évts"
		
		if ((!$tous_evts)&&(!empty($id_ville))&&(!empty($titre_ville_url)))
		{
			echo "<a href=\"";
			echo $root_site;
			echo $titre_ville_url.".".$id_ville.".tout.agenda.html";
			echo "\" title=\"Voir tout l'agenda\" class=\"boutonbleu ico-agenda\">Tout l'agenda</a>";
		}

		echo "<a href=\"ajouter_un_evenement.html\" title=\"Ajouter un évenement\" class=\"boutonbleu ico-ajout\">Ajouter un évenement</a>";
		echo "</div>";
		// actions
		echo "<br/>";
	}

}

?>
