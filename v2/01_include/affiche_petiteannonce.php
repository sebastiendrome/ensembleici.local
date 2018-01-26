<?php
// Affichage des petites annonces
$type_objet      = "petiteannonce";
$page_home       = false;
$lien_filtre_tri = "";
$lien            = "";
$filtres_actif   = false;
$nb_pages_aff    = 3;
$de     = $a     = 0;
$page_autres     = "";

// Eviter erreur sur espace perso
if (!$titre_ville_url) $titre_ville_url = "a"; 

if (($id_ville) && (!empty($id_ville)))
{
    require_once('01_include/_connect.php');
    require_once('01_include/_var_ensemble.php');

    // On est sur la Homepage ?
    if ((!$tous_pa) && (empty($id_tag)) && (empty($id_vie))&&(!$espace_perso))
        $page_home = true;

    if ($tous_pa)
    {
        // Texte d'introduction sur "tout le repertoire"
        echo "<p id=\"intro_tt\">Retrouvez sur cette page les petites annonces de ";
        if ($titre_ville)
            echo $titre_ville;
        else
            "la commune sélectionnée";
        echo " (classées par ordre alphabétique).</p>";
    }
    if ((!$espace_perso)&&(!$page_home))
    {
      echo "<div id=\"pa\" class=\"blocB\">";
	  // ancre
		echo "<a name=\"petitesannonces\"></a>";
      echo "<h1 class=\"pa\">Petites annonces</h1>";
    }
    else if ($page_home) 
    {
      // Sur Home, bloc Petites annonces
      echo '<div id="home-pa" class="bloc-home bloc-grs">';
      echo '<div class="titre"></div>';
      echo '<div id="pa" class="contenu">';
    }

    // first-cut bounding box (in degrees)
    // Infos pour calcul de la distance d'après les coordonnées  
    $maxLat        = $lat_ville + rad2deg($rayon / $rayon_terre);
    $minLat        = $lat_ville - rad2deg($rayon / $rayon_terre);
    // compensate for degrees longitude getting smaller with increasing latitude
    $maxLon        = $lon_ville + rad2deg($rayon / $rayon_terre / cos(deg2rad($lat_ville)));
    $minLon        = $lon_ville - rad2deg($rayon / $rayon_terre / cos(deg2rad($lat_ville)));
    // convert origin of filter circle to radians
    $lat_ville_rep = deg2rad($lat_ville);
    $lon_ville_rep = deg2rad($lon_ville);
    if (($espace_perso) && (intval($no_utilisateur_creation)))
    {
        // Affichage de mes petites annonces dans mon compte
        $id_tag      = "";
        $id_vie      = "";
        $tous_pa = true;
        $sql_pa  = "SELECT P.no AS NumPa,  P.date_creation AS datecreation, P.etat, P.titre, P.monetaire, P.prix, P.url_image, P.rayonmax, P.date_fin, V.latitude, V.longitude, V.nom_ville_maj, 0 AS Distance
                      FROM  `petiteannonce` P,
                      `villes` V
                      WHERE P.no_utilisateur_creation = :no_utilisateur_creation
                      AND P.no_ville = V.id";
        $res_pa  = $connexion->prepare($sql_pa);
        $res_pa->execute(array(
            ':no_utilisateur_creation' => $no_utilisateur_creation
        ));
        $nb_pa_total = $res_pa->rowCount();
    }
    else
    {
        // Page publiques
        // Pagination 
        $ppage = intval($_GET["pp"]);
        if ($ppage < 1)
        {
            $ppage           = 1;
            $sans_pagination = true;
        }
        $start = ($ppage - 1) * $nb_pa_list;
        $limit = " LIMIT $start, $nb_pa_list";

        // Page évènement et struct sélectionnée, pour ajouter au lien rewritting
        // REPERTOIRE
        $rpage = intval($_GET["rp"]);
        if ($rpage) $page_struct = "&rp=".$rpage;
        else $page_struct = "";
        // AGENDA
        $apage = intval($_GET["ap"]);
        if ($apage) $page_evt = "&ap=" . $apage;
        else $page_evt = "";
        // FUSION
        $page_autres = $page_evt.$page_struct;
        if ($page_autres != "") $page_autres = substr_replace($page_autres,"?",0,1);

        // Filtre : Monétaire ou non
        if (isset($_POST['monetaire_pa']))
        {
            $monetaire_pa          = intval($_POST['monetaire_pa']);
            $_SESSION['monetaire_pa'] = $monetaire_pa;
        }
        elseif (isset($_SESSION['monetaire_pa']))
            $monetaire_pa = intval($_SESSION['monetaire_pa']);
        if (isset($monetaire_pa))
            $filtres_actif = true; // pr surbrillance bloc
        else
            $monetaire_pa = 999;
        
		// Filtre : Date ou Distance
        if (isset($_POST['date_pa']))
        {
            $date_pa = intval($_POST['date_pa']);
            $_SESSION['date_pa'] = $date_pa;
        }
        elseif (isset($_SESSION['date_pa']))
            $date_pa = intval($_SESSION['date_pa']);
        if (isset($date_pa))
            $filtres_actif = true; // pr surbrillance bloc
        else
            $date_pa = 1;		
		
		// Toutes
        if ($monetaire_pa == 999)
            $cond_monetaire_sql_pa = "";
        else
            $cond_monetaire_sql_pa = " AND P.monetaire = " . $monetaire_pa;

        // Gestion du rayon
        if (isset($_POST['rayon_pa']))
        {
            $le_rayon_pa          = intval($_POST['rayon_pa']);
            $_SESSION['rayon_pa'] = $le_rayon_pa;
        }
        elseif (isset($_SESSION['rayon_pa']))
            $le_rayon_pa = intval($_SESSION['rayon_pa']);
        if (isset($le_rayon_pa))
            $filtres_actif = true; // pr surbrillance bloc
        else
            $le_rayon_pa = 50;
        // Toutes
        if ($le_rayon_pa == 999)
            $cond_rayon_sql_pa = "";
        else
            $cond_rayon_sql_pa = " AND acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre <= " . $le_rayon_pa;

        // Aucun filtre sur la homepage
        if ($page_home) $cond_monetaire_sql_pa = "";
        if ($page_home) $cond_rayon_sql_pa = "";

        // Gestion de la date d'expiration des pa
        $cond_date_sql = " AND P.date_fin>=NOW()";
        
        if (!empty($id_tag))
        {
          // Dans le tag sélectionnée
          $sql_pa = "SELECT P.no AS NumPa,  P.date_creation AS datecreation, P.etat, P.titre, P.monetaire, P.prix, P.url_image, P.rayonmax, P.date_fin, I.latitude, I.longitude, I.nom_ville_maj, 0 AS Distance
                    FROM  `petiteannonce` P,
                          `petiteannonce_tag` O,
                          `tag` T,
                          `villes` I
                    WHERE O.no_tag = :id_tag
                        AND P.etat = 1
                        $cond_date_sql
                        $cond_monetaire_sql_pa
                        AND P.no_ville = :id_ville
                        AND O.no_petiteannonce = P.no
                        AND O.no_tag = T.no
                        AND P.no_ville = I.id
                    GROUP BY P.no";
          // Structures dans les villes proches
          $sql_proche = "SELECT *, 
                   acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre AS Distance
            FROM (
              SELECT P.no AS NumPa,  P.date_creation AS datecreation, P.etat, P.titre, P.monetaire, P.prix, P.url_image, P.rayonmax, P.date_fin, I.latitude, I.longitude, I.nom_ville_maj
                      FROM  `petiteannonce` P,
                            `petiteannonce_tag` O,
                            `tag` T,
                            `villes` I
                      WHERE O.no_tag = :id_tag
                        AND P.etat = 1
                        $cond_date_sql
                        $cond_monetaire_sql_pa
                        AND latitude>$minLat And latitude<$maxLat
                        AND longitude>$minLon And longitude<$maxLon
                        AND O.no_petiteannonce = P.no
                        AND O.no_tag = T.no
                        AND P.no_ville = I.id
                   GROUP BY P.no
              ) As FirstCut
            WHERE acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre < $rayon
            $cond_rayon_sql_pa";
            
            // Lance la requête pour avoir le nb de pa propres à la ville
            $res_pa_locales = $connexion->prepare($sql_pa);
            $res_pa_locales->execute(array(
                ':id_tag' => $id_tag,
                ':id_ville' => $id_ville
            ));
            $nb_pa_locales = $res_pa_locales->rowCount();
            // Pour calcul nb total de pa
            $sql_pa_total  = "SELECT * FROM (($sql_pa) UNION ($sql_proche)) AS tmp GROUP BY `NumPa`";
            $res_pa_total  = $connexion->prepare($sql_pa_total);
            $res_pa_total->execute(array(
                ':id_tag' => $id_tag,
                ':id_ville' => $id_ville
            ));
            $nb_pa_total = $res_pa_total->rowCount();
            $nb_pages        = ceil($nb_pa_total / $nb_pa_list);
            // Préparation du lien pour pagination
            if (!empty($id_vie))
                $url_id_vie = "." . $id_vie; // id_vie passée en param pour provenance du tag
            $lien_pagination_url = $titre_ville_url . "." . url_rewrite($titre_nomtag) . ".tag." . $id_ville . "." . $id_tag . $url_id_vie;
            $lien_pagination_alt = $titre_nomtag . " à " . $titre_ville . ", page ";
            // url de destination des forms de filtre / tri (sans pagination)
            $lien_filtre_tri     = $titre_ville_url . "." . url_rewrite($titre_nomtag) . ".tag." . $id_ville . "." . $id_tag . ".html" . $page_autres . "#petitesannonces";
            // Pour affichage
			if($date_pa == 1) $tri_par_date="ORDER BY `datecreation` desc,`Distance`,`titre`";
			else $tri_par_date="ORDER BY `Distance`,`titre`";
            $sql_pa          = "SELECT * FROM (($sql_pa) UNION ($sql_proche)) AS tmp GROUP BY `NumPa` ".$tri_par_date." ".$limit;
            $res_pa          = $connexion->prepare($sql_pa);
            $res_pa->execute(array(
                ':id_tag' => $id_tag,
                ':id_ville' => $id_ville
            ));
        }
        elseif (!empty($id_vie))
        {
            // Dans la vie sélectionnée
            $sql_pa         = "SELECT P.no AS NumPa,  P.date_creation AS datecreation, P.etat, P.titre, P.monetaire, P.prix, P.url_image, P.rayonmax, P.date_fin, I.latitude, I.longitude, I.nom_ville_maj, 0 AS Distance
                      FROM  `petiteannonce` P,
                            `petiteannonce_tag` O,
                            `vie_tag` V,
                            `villes` I
                      WHERE V.no_vie = :id_vie
                        AND P.etat = 1
                        $cond_date_sql
                        $cond_monetaire_sql_pa
                        AND P.no_ville = :id_ville
                        AND P.no = O.no_petiteannonce
                        AND O.no_tag = V.no_tag
                        AND P.no_ville = I.id
                   GROUP BY P.no";
            
            // Lance la requête pour avoir le nb de pa propres à la ville
            $res_pa_locales = $connexion->prepare($sql_pa);
            $res_pa_locales->execute(array(
                ':id_vie' => $id_vie,
                ':id_ville' => $id_ville
            ));
            $nb_pa_locales = $res_pa_locales->rowCount();
            // Structures dans les villes proches
            $sql_proche        = "SELECT *, 
                   acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre AS Distance
           FROM (
              SELECT P.no AS NumPa,  P.date_creation AS datecreation, P.etat, P.titre, P.monetaire, P.prix, P.url_image, P.rayonmax, P.date_fin, I.latitude, I.longitude, I.nom_ville_maj
                      FROM  `petiteannonce` P,
                            `petiteannonce_tag` O,
                            `vie_tag` V,
                            `villes` I
                      WHERE V.no_vie = :id_vie
                        AND P.etat = 1
                        $cond_date_sql
                        $cond_monetaire_sql_pa
                        AND latitude>$minLat And latitude<$maxLat
                        AND longitude>$minLon And longitude<$maxLon
                        AND O.no_petiteannonce = P.no
                        AND O.no_tag = V.no_tag
                        AND P.no_ville = I.id
                   GROUP BY P.no
              ) As FirstCut 
            WHERE acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre < $rayon
            $cond_rayon_sql_pa";
            // Pour calcul nb total de pa
            $sql_pa_total  = "SELECT * FROM (($sql_pa) UNION ($sql_proche)) AS tmp GROUP BY `NumPa`";
            $res_pa_total  = $connexion->prepare($sql_pa_total);
            $res_pa_total->execute(array(
                ':id_vie' => $id_vie,
                ':id_ville' => $id_ville
            ));
            $nb_pa_total     = $res_pa_total->rowCount();
            $nb_pages            = ceil($nb_pa_total / $nb_pa_list);
            // Préparation du lien pour pagination
            $lien_pagination_url = $titre_ville_url . "." . url_rewrite($nom_url_vie) . "." . $id_ville . "." . $id_vie;
            $lien_pagination_alt = $titre_nomvie . " à " . $titre_ville . ", page ";
            // url de destination des forms de filtre / tri (sans pagination)
            $lien_filtre_tri     = $titre_ville_url . "." . url_rewrite($nom_url_vie) . "." . $id_ville . "." . $id_vie . ".html" . $page_autres . "#petitesannonces";
            // Pour affichage
			if($date_pa == 1) $tri_par_date="ORDER BY `datecreation` desc,`Distance`,`titre`";
			else $tri_par_date="ORDER BY `Distance`,`titre`";
            $sql_pa          = "SELECT * FROM (($sql_pa) UNION ($sql_proche)) AS tmp GROUP BY `NumPa` ".$tri_par_date." ".$limit;
            $res_pa          = $connexion->prepare($sql_pa);
            $res_pa->execute(array(
                ':id_vie' => $id_vie,
                ':id_ville' => $id_ville
            ));
        }
        else
        {
            // DEFAUT : Toutes les petites annonces  //Affichage sur Home 
            $page_accueil_ville = true;
            // Dans la ville
            $sql_pa         = "SELECT P.no AS NumPa,  P.date_creation AS datecreation, P.etat, P.titre, P.monetaire, P.prix, P.url_image, P.rayonmax, P.date_fin, V.latitude, V.longitude, V.nom_ville_maj, 0 AS Distance
                      FROM  `petiteannonce` P,
                            `petiteannonce_tag` O,
                            `villes` V
                      WHERE P.no_ville = :id_ville
                      AND P.etat = 1
                      $cond_date_sql
                      $cond_monetaire_sql_pa
                      AND P.no_ville = V.id
                      $cond_sstags_sql
                      GROUP BY P.titre";
            // 
            // Lance la requête pour avoir le nb de pa propres à la ville
            $res_pa_locales = $connexion->prepare($sql_pa);
            $res_pa_locales->execute(array(
                ':id_ville' => $id_ville
            ));
            $nb_pa_locales = $res_pa_locales->rowCount();

            // pa dans les villes proches
            $sql_proche        = "SELECT *, 
                          acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre AS Distance
                      FROM (
                        SELECT P.no AS NumPa,  P.date_creation AS datecreation, P.etat, P.titre, P.monetaire, P.prix, P.url_image, P.rayonmax, P.date_fin, latitude, longitude, nom_ville_maj
                        FROM  `petiteannonce` P,
                              `petiteannonce_tag` O,
                              `villes` V
                                WHERE P.etat = 1
                                $cond_date_sql
                                $cond_monetaire_sql_pa
                                AND latitude>$minLat AND latitude<$maxLat
                                AND longitude>$minLon AND longitude<$maxLon
                                AND P.no_ville = V.id
                                $cond_sstags_sql
                      ) As FirstCut 
                      WHERE acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre < $rayon
                      AND acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre <= rayonmax 
                      $cond_rayon_sql_pa";

            // Pour calcul nb total de pa
            $sql_pa_total  = "SELECT * FROM (($sql_pa) UNION ($sql_proche)) AS tmp GROUP BY `NumPa`";
            $res_pa_total  = $connexion->prepare($sql_pa_total);
            $res_pa_total->execute(array(
                ':id_ville' => $id_ville
            ));
            $nb_pa_total = $res_pa_total->rowCount();
            $nb_pages        = ceil($nb_pa_total / $nb_pa_list);

            // Préparation du lien pour pagination
            if ($tous_pa)
            {
                $lien_pagination_url = $titre_ville_url . "." . $id_ville . ".toutes.petites-annonces";
                $lien_pagination_alt = " Toutes les annonces de " . $titre_ville . ", page ";
                // url de destination des forms de filtre / tri (sans pagination)
                $lien_filtre_tri     = $titre_ville_url . "." . $id_ville . ".toutes.petites-annonces.html#petitesannonces";
            }
            else
            {
                // Home
                $lien_pagination_url = $titre_ville_url . "." . $id_ville;
                $lien_pagination_alt = " Petites annonces à " . $titre_ville . ", page ";
                $limit               = " LIMIT 0, $nb_pa_home";
                // url de destination des forms de filtre / tri (sans pagination)
                $lien_filtre_tri     = $titre_ville_url . "." . $id_ville . ".html" . $page_autres . "#petitesannonces";
            }
            // Pour affichage
			if($date_pa == 1) $tri_par_date="ORDER BY `datecreation` desc,`Distance`,`titre`";
			else $tri_par_date="ORDER BY `Distance`,`titre`";
            $sql_pa = "SELECT * FROM (($sql_pa) UNION ($sql_proche)) AS tmp GROUP BY `NumPa` ".$tri_par_date." ".$limit;
            $res_pa = $connexion->prepare($sql_pa);
            $res_pa->execute(array(
                ':id_ville' => $id_ville
            ));
        }
    }
    // echo $sql_pa;

        if ((!$espace_perso) && (!$page_home))
        {
            // Filtres
            if ($filtres_actif)
                $filtres_actif_aff = " filtres_actif";
            echo "<div class=\"filtres$filtres_actif_aff\">";
            // Tri par date / distance et filtre par rayon
            echo "<form name=\"ETripa\" id=\"ETripa\" action=\"$lien_filtre_tri\" method=\"post\" accept-charset=\"UTF-8\">
                    Afficher les petites annonces : ";

                // Filtre rayon
                echo "<select name=\"rayon_pa\">
                  <option value=\"0\"";
                    if ($le_rayon_pa == 0)
                        echo " selected";
                    echo ">de la localité seulement</option>

                  <option value=\"10\"";
                    if ($le_rayon_pa == 10)
                        echo " selected";
                    echo ">< 10 kms</option>

                  <option value=\"50\"";
                    if ($le_rayon_pa == 50)
                        echo " selected";
	
                    echo ">< 50 kms</option>

                  <option value=\"999\"";
                    if ($le_rayon_pa == 999)
                        echo " selected";
                    echo ">Toutes</option>
                </select>";

                // Filtre monétaire
                echo " Monétaires : <select name=\"monetaire_pa\">
                  <option value=\"0\"";
                    if ($monetaire_pa == 0)
                        echo " selected";
                    echo ">Non</option>

                  <option value=\"1\"";
                    if ($monetaire_pa == 1)
                        echo " selected";
                    echo ">Oui</option>

                  <option value=\"999\"";
                    if ($monetaire_pa == 999)
                        echo " selected";
                    echo ">Toutes</option>
                </select>";
				
				 // Filtre date/distance
                echo " </br>Trier par : <select name=\"date_pa\">                  

                  <option value=\"1\"";
                    if ($date_pa == 1) 
                        echo " selected";
                    echo ">Date</option>
					
                  <option value=\"0\"";
                    if (($date_pa == 0))
                        echo " selected";
                    echo ">Distance</option>
					
                </select>

            </form>";

            // Js pour les filtres / tri
            echo "<script type=\"text/javascript\">
                    $(function(){
                        $(\"form#ETripa select\").change(function() {
                          $(\"form#ETripa\").submit();
                        });
                        $js_sstags
                   });
                </script>";
            echo "</div>";
            //  Fin Filtres
        // }

            echo "<p class=\"nb_pas\">";
            if ($nb_pa_total > $nb_pa_list)
            {
                // Affichage du nombre de résultats affichés
                $de = $start + 1;
                if ($start == 0)
                    $a = $nb_pa_list;
                else
                    $a = $de + $nb_pa_list - 1;
                if ($a > $nb_pa_total)
                    $a = $nb_pa_total;
                echo "Résultats $de - $a sur $nb_pa_total<br/>";
            }
            // Affiche le nombre de pa total
            if ((!$page_accueil_ville) || ($tous_pa))
            {
                // Nombre de pa en bas de page sur la page d'accueil
                if ($nb_pa_locales)
                {
                    // Petites annonces locales / proches
                    echo $nb_pa_locales . " petite";
                    if ($nb_pa_locales > 1) echo "s";
                    echo " annonce";
                    if ($nb_pa_locales > 1) echo "s";

                    echo " dans la ville";
                }
                $nb_pa_proches = $nb_pa_total - $nb_pa_locales;
                if ($nb_pa_proches)
                {
                    if ($nb_pa_locales)
                        echo " + " . $nb_pa_proches;
                    else
                    {
                        echo $nb_pa_proches . " petite";
                        if ($nb_pa_proches > 1) echo "s";
                        echo " annonce";
                        if ($nb_pa_proches > 1) echo "s";
                    }
                    echo " proche";
                    if ($nb_pa_proches > 1)
                        echo "s";
                }
            }
            echo "</p>";

            // Pagination
            if (($nb_pages) && ($nb_pa_total > $nb_pa_list))
            {

                // Paramètres passés en GET
                if ($page_autres == "")
                    $page_autres_p = "?pp=";
                else
                    $page_autres_p = $page_autres."&pp=";

                $ppagination = "<div class=\"pagination\">";
                // Lien retour
                if ($ppage > 1)
                {
                    // page précédente <
                    $prevpage = $ppage - 1;
                    // ajoute aux param de pagination
                    $ppagination .= "<a href='" . $lien_pagination_url . ".html" . $page_autres_p . $prevpage . "#petitesannonces'><</a>";
                    // Lien page 1
                    if ($ppage > $nb_pages_aff + 1)
                        $ppagination .= "<a href='" . $lien_pagination_url . ".html" . $page_autres_p . "1#petitesannonces'>1</a>";
                }
                if ($ppage - $nb_pages_aff > 2)
                    $ppagination .= " ... ";
                for ($x = ($ppage - $nb_pages_aff); $x < (($ppage + $nb_pages_aff) + 1); $x++)
                {
                    if (($x > 0) && ($x <= $nb_pages))
                    {
                        // page active ?
                        if ($x == $ppage)
                            $ppagination .= "<a href=\"\" class=\"actif\">" . $x . "</a>";
                        else
                            $ppagination .= "<a href=\"" . $lien_pagination_url . ".html" . $page_autres_p . $x . "#petitesannonces\" title=\"" . $lien_pagination_alt . $x . "\">" . $x . "</a>";
                    }
                }
                if ($ppage < $nb_pages - $nb_pages_aff - 1)
                    $ppagination .= " ... ";
                // Lien suivant et fin
                if ($ppage != $nb_pages)
                {
                    $nextpage = $ppage + 1;
                    // lien dernière page
                    if ($nb_pages - $ppage > $nb_pages_aff)
                        $ppagination .= "<a href='" . $lien_pagination_url . ".html" . $page_autres_p . $nb_pages . "#petitesannonces'>$nb_pages</a>";
                    $ppagination .= "<a href='" . $lien_pagination_url . ".html" . $page_autres_p . $nextpage . "#petitesannonces'>></a>";
                }


                $ppagination .= "</div>";
                echo $ppagination;
            }
            echo "<div class=\"clear\"></div>";
        }
        elseif ($page_home)
        {
            // Phrase accueil
			
			$sql_pa = "SELECT P.no AS NumPa, P.etat, P.titre, P.monetaire, P.prix, P.url_image, P.rayonmax, P.date_fin, V.latitude, V.longitude, V.nom_ville_maj, 0 AS Distance
					FROM  `petiteannonce` P,
						`petiteannonce_tag` O,
						`villes` V
					WHERE P.etat = 1
					$cond_date_sql
					$cond_monetaire_sql_pa
					AND P.no_ville = V.id
					$cond_sstags_sql
					GROUP BY P.titre
					ORDER BY P.date_creation DESC limit 4";
			$res_pa = $connexion->prepare($sql_pa);        
			$res_pa->execute();	
			$aff_nb_home = $res_pa->rowCount();
			
            if ($nb_pa_total > $nb_pa_home)
              $aff_nb_home = $nb_pa_home;
            else
              $aff_nb_home = $nb_pa_total;

            if ($aff_nb_home > 1)
                echo "<p class=\"intro\">".$aff_nb_home." dernières petites annonces ajoutées :</p>";
            else
                echo "<p class=\"intro\">Dernière petite annonce ajoutée :</p>";

            // Affiche le nombre total de structures
            if ($nb_pa_locales || $nb_pa_total)
            {
                $nb_pa_proches = $nb_pa_total - $nb_pa_locales;
                $aff_lien_nb_pas = "<a href=\"";
                $aff_lien_nb_pas .= $root_site;
                $aff_lien_nb_pas .= $titre_ville_url . "." . $id_ville . ".toutes.petites-annonces.html";
                $aff_lien_nb_pas .= "\" title=\"Voir tout les petites annonces\">";
                $aff_lien_nb_pas .= "<p class=\"nb_pas\">";
                if ($nb_pa_locales)
                {
                    $aff_lien_nb_pas .= $nb_pa_locales." petite";
                    if ($nb_pa_locales > 1) $aff_lien_nb_pas .= "s";
                    $aff_lien_nb_pas .= " annonce";
                    if ($nb_pa_locales > 1) $aff_lien_nb_pas .= "s";
					$aff_lien_nb_pas .= " dans la ville";

                    if ($nb_pa_proches)
                        $aff_lien_nb_pas .= " + " . $nb_pa_proches . " proche";
                    if ($nb_pa_proches > 1) $aff_lien_nb_pas .= "s";
                }
                else
                {
                    if ($nb_pa_proches)
                        $aff_lien_nb_pas .= $nb_pa_proches . " petites annonces proches de la ville";
                }
                $aff_lien_nb_pas .= "</p>";
                $aff_lien_nb_pas .= "</a>";
                $aff_lien_nb_pas .= "<div class=\"clear\"></div>";
            }
        }

    // Affichage des résultats    
    if ($nb_pa_total > 0)
    {

        while ($t_pa = $res_pa->fetch(PDO::FETCH_ASSOC))
        {
            if ((!empty($t_pa["NumPa"])) || (!empty($t_pa["titre"])))
            {

                // Image
                if (!$page_home)
                {
                    $image = $t_pa["url_image"];
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
                            $image = "";
                    }

                    // Image locale ok ?
                    if ($image)
                        $imgsrc = $root_site_d."miniature.php?uri=".$t_pa["url_image"]."&method=fit&w=50&h=40";
                }

                // Nom de la structure coupé à 130 carractères pour le lien
                $titre_pour_lien = coupe_chaine($t_pa["titre"], 130, false);
                // Structure active ?
                if ($t_pa["etat"] == 0)
                    $pa_active = false;
                else
                    $pa_active = true;
                echo "<article><div class=\"une-pa\">";

                // Lien vers le détails de la petite annonce 
                if (!empty($id_vie))
                    $url_id_vie = "." . $id_vie; // id_vie passée en param pour provenance
                if (!empty($id_tag))
                    $url_id_tag = "." . $id_tag; // id_tag passée en param pour provenance
                if ($pa_active)
                    $lien = "petiteannonce." . $titre_ville_url . "." . url_rewrite($titre_pour_lien) . "." . $id_ville . "." . $t_pa["NumPa"] . $url_id_vie . $url_id_tag . ".html";


                if (($image) && (!$page_home))
                {
                    echo "<div class=\"illustr\"><a href=\"".$lien."\" title=\"Voir en détails\">";
                    echo "<img src=\"".$imgsrc."\" alt=\"".str_replace('"',"'",ucfirst($t_pa["titre"]))."\" width=\"50\" />";
                    echo "</a></div>";
                }

                // Titre
                echo "<a href=\"" . $lien . "\" title=\"Voir en détails\">";


                $statutville = "";
                
                if ((!$page_home) && (!$espace_perso))
                {
                    echo "<div class=\"genre_ville\">";
                    echo "<div class=\"ville\">" . $t_pa["nom_ville_maj"] . "</div>";
    
                    if (ceil(round($t_pa["Distance"])))
                    {+
                        $distance_struct = ceil(round($t_pa["Distance"]) / 10) * 10;
                        echo "<span title=\"A moins de $distance_struct kms, à vol d'oiseau\" class=\"infobulle-b\">< " . $distance_struct . " km";
                        if ($distance_struct > 1)
                            echo "s";
                        echo "</span>";
                    }
    
                    echo "</div>";
                }

                echo "<h2>" . ucfirst($t_pa["titre"]);

                if ($page_home)
                    $statutville = " <span> à " . $t_pa["nom_ville_maj"]."</span>";
                if ($statutville) echo $statutville; // ville sur home

                if ((!$page_home)&&(!$espace_perso))
                {
                  $no_occurence = $t_pa["NumPa"]; // Pour favoris
                  //echo "<div class=\"clear\">";
                  // Bouton favoris (paramètre : $type_objet)
                  require('ajout_fav.php');
                  //echo "</div>";
                }

                if ($t_pa["monetaire"]) {
                    echo " <img src=\"img/monetaire.png\" class=\"ico-monetaire infobulle-pa\" title=\"";
                    if(($t_pa["prix"])&&($t_pa["prix"]!="0.00"))
                        echo FormatPrix($t_pa["prix"]);
                    else
                        echo "Annonce monétaire";
                    echo "\" />";

                    echo "<script type=\"text/javascript\">$(document).ready(function(){
                        $('.infobulle-pa').poshytip({
                            showTimeout:0,
                            hideTimeout:0,
                            timeOnScreen:0,
                            className: 'infobulle-tip',
                            alignTo: 'target',
                            alignX: 'center',
                            alignY: 'bottom',
                            offsetX: 2,
                            offsetY: 9
                        });
                    });</script>"; 
                }

                echo "</h2>";
                if ($pa_active)
                    echo "</a>";

                if ($espace_perso)
                {
                    // Bouton favoris (paramètre : $type_objet)
                    // $no_occurence = $t_pa["NumPa"];
                    // require('ajout_fav.php');

                    // echo "<div class=\"actions\">";
                    echo "<div style=\"text-align: right;\">";
                    // Alerte pour les petite annonces désactivées
                    if (!$pa_active)
                    {
                        echo "<div title=\"Petite annonce désactivée par l'équipe 'Ensemble ici'\" class=\"boutonrouge  infobulle-b\">Petite annonce désactivée</div>";
                    }
                    // boutton modifier
                    echo '<form method="POST" action="auto_petiteannonce_etape1.php" class="formA">
                        <input name="no_orig" value="' . $t_pa["NumPa"] . '" type="hidden">
                        <input type="hidden" name="no_fiche" value="0">
                        <input type="hidden" name="no_fiche_temp" value="0">
                        <input name="mode_modification" value="1" type="hidden">
                        <button type="submit" class="boutonbleu ico-modifier">Modifier</button>
                    </form>';
                  
					// boutton activer/desactiver
						echo "<script type=\"text/javascript\" src=\"js/ajax_functions.js\"></script>";
						
						if($t_pa["etat"]==0){$bouttonnom="Activer";}
						else{$bouttonnom="Désactiver";}
						
						$opac = 0;
						if($t_pa["etat"]==0){$opac = 100;}  
						
						echo "<div class=\"boutonbleu ico-fleche\" style=\"\" rel=\"".$t_pa["etat"]."\" id=\"".$t_pa["NumPa"]."\" title=\"Activer / Désactiver l'état de la petite annonce\" onclick=\"modif_etat(".$t_pa["NumPa"].");set_opacity(".$t_pa["NumPa"].",".$opac.")\">".$bouttonnom."</div>";
                                      					
						echo "<script type=\"text/javascript\">set_opacity(".$t_pa["NumPa"].",".$opac.");</script>";   
                    
                    echo "</div>";
                }
                echo "<div class=\"clear\"></div>";
                echo "</div></article>";
            }
        } // Fin While

        // Sur Home, Fin bloc repertoire
        if ($page_home) 
        {

          echo '</div>'; // Fin .contenu

          // Liens Voir tout et ajouter
          echo '<div class="liens">';
            echo $aff_lien_nb_pas;
            echo "<a href=\"";
            echo $root_site;
            echo $titre_ville_url.".".$id_ville.".toutes.petites-annonces.html";
            echo "\" title=\"Voir toutes les petites annonces\" class=\"boutonbleu ico-fleche\">Toutes les annonces</a>";
            echo "<a href=\"ajouter_une_petiteannonce.html\" title=\"Ajouter une petite annonce\" class=\"boutonbleu ico-ajout \">Ajouter une annonce</a>";
          echo '</div>';

        }

    }
    else
    {
        echo "<div id=\"message\">";
        echo "Aucune petite annonce enregistrée.";
        echo "</div>"; // actions
    }

    if ((!$espace_perso)&&(!$page_home))
    {
        echo $ppagination;
        echo "<div class=\"clear\"></div>";
        echo "</div>"; // pa
        echo "<!-- Actions sur petites annonces -->";
        echo "<div class=\"actions\">";
        // Lien "toutes les pa"
        if ((!$tous_pa) && ((!empty($id_ville)) && (!empty($titre_ville_url))))
        {
            if ($nb_pa_locales > 1)
            {
                echo "<a href=\"";
                echo $root_site;
                echo $titre_ville_url . "." . $id_ville . ".toutes.petites-annonces.html";
                // Nombre de structures sur la page d'accueil
                echo "\" title=\"Voir les : " . $nb_pa_locales . " petites annonces dans la commune $titre_ville\" class=\"boutonbleu ico-agenda\">Toutes les petites annonces</a>";
            }
        }
        echo "<a href=\"ajouter_une_petiteannonce.html\" title=\"Ajouter une petite annonce\" class=\"boutonbleu ico-ajout\">Ajouter une petite annonce</a>";
        echo "</div>"; // actions
        echo "<br/>";
    }
    else if ($page_home){
        echo "</div>"; // Fin home-pa      
    }
}

?>
