<?php
// Affichage du répertoire
$type_objet      = "structure";
$page_home       = false;
$de              = $a = 0;
$lien_filtre_tri = "";
$filtres_actif   = false;
$nb_pages_aff    = 3;
$page_autres     = "";


if (($id_ville) && (!empty($id_ville)))
{
    require_once('01_include/_connect.php');
    require_once('01_include/_var_ensemble.php');

    // On est sur la Homepage ?
    if ((!$tous_struct) && (empty($id_tag)) && (empty($id_vie))&&(!$espace_perso))
        $page_home = true;

    if ($tous_struct)
    {
        // Texte d'introduction sur "tout le repertoire"
        echo "<p id=\"intro_tt\">Retrouvez sur cette page tout le répertoire de ";
        if ($titre_ville)
            echo $titre_ville;
        else
            "la commune sélectionnée";
        echo " (classé par ordre alphabétique).</p>";
    }
    if ((!$espace_perso)&&(!$page_home))
    {
      echo "<div id=\"repertoire\" class=\"blocB\">";
      echo "<h1>Repertoire</h1>";
    }
    else if ($page_home) 
    {
      // Sur Home, bloc repertoire
      echo '<div id="home-structs" class="bloc-home bloc-grs">';
      echo '<div class="titre"></div>';
      echo '<div id="repertoire" class="contenu">';
    }

    // Infos pour calcul de la distance d'après les coordonnées  
    // first-cut bounding box (in degrees)
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
        // Affichage de mes structures dans mon compte
        $id_tag      = "";
        $id_vie      = "";
        $tous_struct = true;
        $sql_struct  = "SELECT S.no AS NumStructure, S.etat, S.nom, S.sous_titre, S.no_statut, T.libelle AS libelle_statut, V.latitude, V.longitude, V.nom_ville_maj, 0 AS Distance
                      FROM  `structure` S,
                      `statut` T,
                      `villes` V
                      WHERE S.no_utilisateur_creation = :no_utilisateur_creation
                      AND S.no_statut = T.no
                      AND S.no_ville = V.id";
        $res_struct  = $connexion->prepare($sql_struct);
        $res_struct->execute(array(
            ':no_utilisateur_creation' => $no_utilisateur_creation
        ));
        $nb_struct_total = $res_struct->rowCount();
    }
    else
    {
        // Page publiques
        // Pagination 
        $rpage = intval($_GET["rp"]);
        if ($rpage < 1)
        {
            $rpage           = 1;
            $sans_pagination = true;
        }
        $start = ($rpage - 1) * $nb_struct_list;
        $limit = " LIMIT $start, $nb_struct_list";
 
        // Page évènement et petites annonces sélectionnée, pour ajouter au lien rewritting
        // AGENDA
        $apage = intval($_GET["ap"]);
        if ($apage) $page_evt = "&ap=" . $apage;
        else $page_evt = "";
        // PETITES ANNONCES
        $ppage = intval($_GET["pp"]);
        if ($ppage) $page_pa = "&pp=" . $ppage;
        else $page_pa = "";
        // FUSION
        $page_autres = $page_evt.$page_pa;
        if ($page_autres != "") $page_autres = substr_replace($page_autres,"?",0,1);


        if ($tous_struct)
        {
            // Supprime le tag selectionné si aucune page choisie
            if ($sans_pagination)
                unset($_SESSION['sous_tag']);
            // Filtre par sous tag
            if ($_POST['sous_tag'])
            {
                $tab_tag_reponse      = $_POST['sous_tag'];
                $_SESSION['sous_tag'] = $_POST['sous_tag'];
            }
            elseif (isset($_SESSION['sous_tag']))
                $tab_tag_reponse = $_SESSION['sous_tag'];
            $nbsstags = count($tab_tag_reponse);
            if ($nbsstags)
            {
                $prems_sstag = true;
                if ($nbsstags > 1)
                    $nbsstags_s = "s";
                $aff_lib_ss_tags = "<p class=\"bloc_sstags_actif\">Thématique$nbsstags_s sélectionnée$nbsstags_s : ";
                for ($indice_tag = 0; $indice_tag < $nbsstags; $indice_tag++)
                {
                    if (!$prems_sstag)
                        $aff_lib_ss_tags .= ", ";
                    $sql_sstag = "SELECT titre FROM sous_tag WHERE no=:no_sous_tag";
                    $res_sstag = $connexion->prepare($sql_sstag);
                    $res_sstag->execute(array(
                        ':no_sous_tag' => $tab_tag_reponse[$indice_tag]
                    ));
                    $tab_sstag    = $res_sstag->fetch(PDO::FETCH_ASSOC);
                    $nb_sstag_rep = $res_sstag->rowCount();
                    if ($nb_sstag_rep)
                    {
                        $aff_lib_ss_tags .= ucfirst($tab_sstag['titre']);
                        if ($nbsstags > 1)
                        {
                            // Gérer le OR
                            if ($prems_sstag)
                                $cond_sstags_sql .= " AND ((O.no_sous_tag = " . $tab_tag_reponse[$indice_tag] . ")";
                            else
                                $cond_sstags_sql .= " OR (O.no_sous_tag = " . $tab_tag_reponse[$indice_tag] . ")";
                        }
                        else
                        {
                            // 1 seul sous tag 
                            $cond_sstags_sql = " AND O.no_sous_tag = " . $tab_tag_reponse[$indice_tag];
                        }
                    }
                    if ($prems_sstag)
                        $prems_sstag = false;
                }
                // Ferme la parenthèse de la condition
                if ($nbsstags > 1)
                    $cond_sstags_sql .= ")";
                // On ajoute les liaisons de table si besoin
                if ($cond_sstags_sql)
                    $cond_sstags_sql .= " AND O.no_structure = S.no";
                if ($nbsstags)
                {
                    $lien_filtre_sstag = $titre_ville_url . "." . $id_ville . ".tout.repertoire.html";
                    $aff_lib_ss_tags .= "<a href=\"$lien_filtre_sstag\" class=\"bloc_sstags_close\" alt=\"Supprimer le filtre thématique\"></a>";
                }
                $aff_lib_ss_tags .= "</p>";
            }
        }
        // Gestion du rayon
        if (isset($_POST['rayon_structs']))
        {
            $le_rayon_structs          = intval($_POST['rayon_structs']);
            $_SESSION['rayon_structs'] = $le_rayon_structs;
        }
        elseif (isset($_SESSION['rayon_structs']))
            $le_rayon_structs = intval($_SESSION['rayon_structs']);
        if (isset($le_rayon_structs))
            $filtres_actif = true; // pr surbrillance bloc
        else
            $le_rayon_structs = 100;
        // Tous les évènements
        if ($le_rayon_structs == 100)
            $cond_rayon_sql_structs = "";
        else
            $cond_rayon_sql_structs = " AND acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre <= " . $le_rayon_structs;
        if (!empty($id_tag))
        {
            if ($est_ss_tag)
            {

                // Supprimé le 13/02/13 sur les 2 requetes : 2 tables ds le FROM : `sous_tag` U, `vie_tag` V,

                // il s'agit d'un sous-tag (pas un tag)
                // Dans le tag sélectionnée
                $sql_struct = "SELECT S.no AS NumStructure, S.etat, S.nom, S.sous_titre, S.no_statut, T.libelle AS libelle_statut, I.latitude, I.longitude, I.nom_ville_maj, 0 AS Distance
                          FROM  `structure` S,
                                  `statut` T,
                                  `structure_sous_tag` O,
                                  `tag_sous_tag` A,
                                  `villes` I
                          WHERE O.no_sous_tag = :id_tag
                              AND S.etat = 1
                              AND S.no_ville = :id_ville
                              AND S.no_statut = T.no
                              AND S.no = O.no_structure
                              AND S.no_ville = I.id
                              AND O.no_sous_tag = A.no_sous_tag
                          GROUP BY S.no";
                // Structures dans les villes proches
                $sql_proche = "SELECT *, 
                         acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre AS Distance
                  FROM (
                    SELECT S.no AS NumStructure, S.etat, S.nom, S.sous_titre, S.no_statut, T.libelle AS libelle_statut, I.latitude, I.longitude, I.nom_ville_maj
                            FROM  `structure` S,
                                  `statut` T,
                                  `structure_sous_tag` O,
                                  `tag_sous_tag` A,
                                  `villes` I
                            WHERE O.no_sous_tag = :id_tag
                              AND S.etat = 1
                              AND latitude>$minLat And latitude<$maxLat
                              AND longitude>$minLon And longitude<$maxLon
                              AND S.no_statut = T.no
                              AND S.no = O.no_structure
                              AND S.no_ville = I.id
                              AND O.no_sous_tag = A.no_sous_tag
                         GROUP BY S.no
                    ) As FirstCut 
                  WHERE acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre < $rayon
                  $cond_rayon_sql_structs";
            }
            else
            {
                // Supprimé le 13/02/13 sur les 2 requetes : 2 tables ds le FROM : `sous_tag` U, `vie_tag` V,

                // il s'agit d'un tag (pas un sous-tag)
                // Dans le tag sélectionnée
                $sql_struct = "SELECT S.no AS NumStructure, S.etat, S.nom, S.sous_titre, S.no_statut, T.libelle AS libelle_statut, I.latitude, I.longitude, I.nom_ville_maj, 0 AS Distance
                          FROM  `structure` S,
                                  `statut` T,
                                  `structure_sous_tag` O,
                                  `tag_sous_tag` A,
                                  `villes` I
                          WHERE A.no_tag = :id_tag
                              AND S.etat = 1
                              AND S.no_ville = :id_ville
                              AND S.no_statut = T.no
                              AND S.no = O.no_structure
                              AND S.no_ville = I.id
                              AND O.no_sous_tag = A.no_sous_tag
                          GROUP BY S.no";
                // Structures dans les villes proches
                $sql_proche = "SELECT *, 
                         acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre AS Distance
                  FROM (
                    SELECT S.no AS NumStructure, S.etat, S.nom, S.sous_titre, S.no_statut, T.libelle AS libelle_statut, I.latitude, I.longitude, I.nom_ville_maj
                            FROM  `structure` S,
                                  `statut` T,
                                  `structure_sous_tag` O,
                                  `tag_sous_tag` A,
                                  `villes` I
                            WHERE A.no_tag = :id_tag
                              AND S.etat = 1
                              AND latitude>$minLat And latitude<$maxLat
                              AND longitude>$minLon And longitude<$maxLon
                              AND S.no_statut = T.no
                              AND S.no = O.no_structure
                              AND S.no_ville = I.id
                              AND O.no_sous_tag = A.no_sous_tag
                         GROUP BY S.no
                    ) As FirstCut 
                  WHERE acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre < $rayon
                  $cond_rayon_sql_structs";
            }
            // Lance la requête pour avoir le nb de structures propres à la ville
            $res_struct_locales = $connexion->prepare($sql_struct);
            $res_struct_locales->execute(array(
                ':id_tag' => $id_tag,
                ':id_ville' => $id_ville
            ));
            $nb_struct_locales = $res_struct_locales->rowCount();
            // Pour calcul nb total de structures
            $sql_struct_total  = "SELECT * FROM (($sql_struct) UNION ($sql_proche)) AS tmp GROUP BY `NumStructure`";
            $res_struct_total  = $connexion->prepare($sql_struct_total);
            $res_struct_total->execute(array(
                ':id_tag' => $id_tag,
                ':id_ville' => $id_ville
            ));
            $nb_struct_total = $res_struct_total->rowCount();
            $nb_pages        = ceil($nb_struct_total / $nb_struct_list);
            // Préparation du lien pour pagination
            if (!empty($id_vie))
                $url_id_vie = "." . $id_vie; // id_vie passée en param pour provenance du tag
            $lien_pagination_url = $titre_ville_url . "." . url_rewrite($titre_nomtag) . ".tag." . $id_ville . "." . $id_tag . $url_id_vie;
            $lien_pagination_alt = $titre_nomtag . " à " . $titre_ville . ", page ";
            // url de destination des forms de filtre / tri (sans pagination)
            $lien_filtre_tri     = $titre_ville_url . "." . url_rewrite($titre_nomtag) . ".tag." . $id_ville . "." . $id_tag . ".html" . $page_autres . "#repertoire";
            // Pour affichage
            $sql_struct          = "SELECT * FROM (($sql_struct) UNION ($sql_proche)) AS tmp GROUP BY `NumStructure` ORDER BY `Distance`,`nom`" . $limit;
            $res_struct          = $connexion->prepare($sql_struct);
            $res_struct->execute(array(
                ':id_tag' => $id_tag,
                ':id_ville' => $id_ville
            ));
        }
        elseif (!empty($id_vie))
        {
          // Dans la vie sélectionnée


                // Supprimé le 13/02/13 sur les 2 requetes : 1 tables ds le FROM : `vie_tag` V,
                // Remis le 08/03 : Nécessité pour les requetes pages vie.
                // + Tentative optimisation avec INNER JOIN

/* 
            $sql_struct         = "SELECT S.no AS NumStructure, S.etat, S.nom, S.sous_titre, S.no_statut, T.libelle AS libelle_statut, I.latitude, I.longitude, I.nom_ville_maj, 0 AS Distance
                      FROM  `structure` S,
                            `structure_sous_tag` O,
                            `tag_sous_tag` A,
                            `vie_tag` V,
                            `statut` T,
                            `villes` I
                      WHERE V.no_vie = :id_vie
                        AND S.etat = 1
                        AND S.no_ville = :id_ville
                        AND S.no = O.no_structure
                        AND O.no_sous_tag = A.no_sous_tag
                        AND A.no_tag = V.no_tag
                        AND S.no_ville = I.id
                        AND S.no_statut = T.no
                   GROUP BY S.no";
*/
            $sql_struct         = "SELECT S.no AS NumStructure, S.etat, S.nom, S.sous_titre, S.no_statut, T.libelle AS libelle_statut, I.latitude, I.longitude, I.nom_ville_maj, 0 AS Distance
                      FROM  `structure` S
                      INNER JOIN `structure_sous_tag` O ON S.no = O.no_structure
                      INNER JOIN `tag_sous_tag` A ON O.no_sous_tag = A.no_sous_tag
                      INNER JOIN `vie_tag` V ON A.no_tag = V.no_tag
                      INNER JOIN `villes` I ON S.no_ville = I.id
                      INNER JOIN `statut` T ON S.no_statut = T.no
                      WHERE V.no_vie = :id_vie
                        AND S.etat = 1
                        AND S.no_ville = :id_ville
                   GROUP BY S.no";
            
            // echo $sql_struct;

            // Lance la requête pour avoir le nb de structures propres à la ville
            $res_struct_locales = $connexion->prepare($sql_struct);
            $res_struct_locales->execute(array(
                ':id_vie' => $id_vie,
                ':id_ville' => $id_ville
            ));
            $nb_struct_locales = $res_struct_locales->rowCount();
            // Structures dans les villes proches
            $sql_proche        = "SELECT *, 
                   acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre AS Distance
           FROM (
              SELECT S.no AS NumStructure, S.etat, S.nom, S.sous_titre, S.no_statut, T.libelle AS libelle_statut, I.latitude, I.longitude, I.nom_ville_maj
                      FROM  `structure` S,
                            `structure_sous_tag` O,
                            `tag_sous_tag` A,
                            `vie_tag` V,
                            `statut` T,
                            `villes` I
                      WHERE V.no_vie = :id_vie
                        AND S.etat = 1
                        AND latitude>$minLat And latitude<$maxLat
                        AND longitude>$minLon And longitude<$maxLon
                        AND S.no = O.no_structure
                        AND O.no_sous_tag = A.no_sous_tag
                        AND A.no_tag = V.no_tag
                        AND S.no_ville = I.id
                        AND S.no_statut = T.no
                   GROUP BY S.no
              ) As FirstCut 
            WHERE acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre < $rayon
            $cond_rayon_sql_structs";
            // Pour calcul nb total de structures
            $sql_struct_total  = "SELECT * FROM (($sql_struct) UNION ($sql_proche)) AS tmp GROUP BY `NumStructure`";
            $res_struct_total  = $connexion->prepare($sql_struct_total);
            $res_struct_total->execute(array(
                ':id_vie' => $id_vie,
                ':id_ville' => $id_ville
            ));
            $nb_struct_total     = $res_struct_total->rowCount();
            $nb_pages            = ceil($nb_struct_total / $nb_struct_list);
            // Préparation du lien pour pagination
            $lien_pagination_url = $titre_ville_url . "." . url_rewrite($nom_url_vie) . "." . $id_ville . "." . $id_vie;
            $lien_pagination_alt = $titre_nomvie . " à " . $titre_ville . ", page ";
            // url de destination des forms de filtre / tri (sans pagination)
            $lien_filtre_tri     = $titre_ville_url . "." . url_rewrite($nom_url_vie) . "." . $id_ville . "." . $id_vie . ".html" . $page_autres . "#repertoire";
            // Pour affichage
            $sql_struct          = "SELECT * FROM (($sql_struct) UNION ($sql_proche)) AS tmp GROUP BY `NumStructure` ORDER BY `Distance`,`nom`" . $limit;
            $res_struct          = $connexion->prepare($sql_struct);
            $res_struct->execute(array(
                ':id_vie' => $id_vie,
                ':id_ville' => $id_ville
            ));
        }
        else
        {
            // DEFAUT : Affichage sur Home + Tout le répertoire
            $page_accueil_ville = true;
            // Dans la ville
            $sql_struct         = "SELECT S.no AS NumStructure, S.etat, S.nom, S.sous_titre, S.no_statut, T.libelle AS libelle_statut, V.latitude, V.longitude, V.nom_ville_maj, 0 AS Distance
                      FROM  `structure` S,
                      `statut` T,
                      `structure_sous_tag` O,
                      `villes` V
                      WHERE S.no_ville = :id_ville
                      AND S.etat = 1
                      AND S.no_statut = T.no
                      AND S.no_ville = V.id
                      AND S.no = O.no_structure
                      $cond_sstags_sql
                      GROUP BY S.nom";
            // Lance la requête pour avoir le nb de structures propres à la ville
            $res_struct_locales = $connexion->prepare($sql_struct);
            $res_struct_locales->execute(array(
                ':id_ville' => $id_ville
            ));
            $nb_struct_locales = $res_struct_locales->rowCount();
            // Structures dans les villes proches
            $sql_proche        = "SELECT *, 
                   acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre AS Distance
            FROM (
              SELECT S.no AS NumStructure, S.etat, S.nom, S.sous_titre, S.no_statut, T.libelle AS libelle_statut, latitude, longitude, nom_ville_maj
              FROM  `structure` S,
                      `statut` T,
                      `structure_sous_tag` O,
                      `villes` V
                      WHERE S.etat = 1
                      AND latitude>$minLat AND latitude<$maxLat
                      AND longitude>$minLon AND longitude<$maxLon
                      AND S.no_statut = T.no
                      AND S.no_ville = V.id
                      AND S.no = O.no_structure
                      $cond_sstags_sql
              ) As FirstCut 
            WHERE acos(sin($lat_ville_rep)*sin(radians(latitude)) + cos($lat_ville_rep)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_rep))*$rayon_terre < $rayon
            $cond_rayon_sql_structs";
            // Pour calcul nb total de structures
            $sql_struct_total  = "SELECT * FROM (($sql_struct) UNION ($sql_proche)) AS tmp GROUP BY `NumStructure`";
            $res_struct_total  = $connexion->prepare($sql_struct_total);
            $res_struct_total->execute(array(
                ':id_ville' => $id_ville
            ));
            $nb_struct_total = $res_struct_total->rowCount();
            $nb_pages        = ceil($nb_struct_total / $nb_struct_list);
            // Préparation du lien pour pagination
            if ($tous_struct)
            {
                $lien_pagination_url = $titre_ville_url . "." . $id_ville . ".tout.repertoire";
                $lien_pagination_alt = " Tout le répertoire de " . $titre_ville . ", page ";
                // url de destination des forms de filtre / tri (sans pagination)
                $lien_filtre_tri     = $titre_ville_url . "." . $id_ville . ".tout.repertoire.html#repertoire";
            }
            else
            {
                // Home
                $lien_pagination_url = $titre_ville_url . "." . $id_ville;
                $lien_pagination_alt = " Structures à " . $titre_ville . ", page ";
                $limit               = " LIMIT 0, $nb_struct_home";
                // url de destination des forms de filtre / tri (sans pagination)
                $lien_filtre_tri     = $titre_ville_url . "." . $id_ville . ".html" . $page_autres . "#repertoire";
            }
            // Pour affichage
            $sql_struct = "SELECT * FROM (($sql_struct) UNION ($sql_proche)) AS tmp GROUP BY `NumStructure` ORDER BY `Distance`,`nom`" . $limit;
            $res_struct = $connexion->prepare($sql_struct);
            $res_struct->execute(array(
                ':id_ville' => $id_ville
            ));
        }
    }
    // Affichage des résultats
    if ($nb_struct_total > 0)
    {
        if ((!$espace_perso) && (!$page_home))
        {
            // Filtres
            if ($filtres_actif)
                $filtres_actif_aff = " filtres_actif";
            echo "<div class=\"filtres$filtres_actif_aff\">";
            // Tri par date / distance et filtre par rayon
            echo "<form name=\"ETristructs\" id=\"ETristructs\" action=\"$lien_filtre_tri\" method=\"post\" accept-charset=\"UTF-8\">

                    Afficher les structures : 

                    <select name=\"rayon_structs\">

                            <option value=\"0\"";
            if ($le_rayon_structs == 0)
                echo " selected";
            echo ">de la localité seulement</option>

                            <option value=\"10\"";
            if ($le_rayon_structs == 10)
                echo " selected";
            echo ">< 10 kms</option>

                                <option value=\"30\"";
            if ($le_rayon_structs == 30)
                echo " selected";
            echo ">< 30 kms</option>

                                <option value=\"100\"";
            if ($le_rayon_structs == 100)
                echo " selected";
            echo ">Toutes</option>

                    </select>

                </form>";
            if ($tous_struct)
            {
                // Affichage des filtre par sous tag
                echo $aff_lib_ss_tags;
                echo "<div class=\"lib_sstags\"><a href=\"\" class=\"bloc_sstags_a\">";
                if ($nbsstags)
                    echo "Changer de thématique";
                else
                    echo "Choisir une thématique";
                echo "</a></div>";
                echo "<div class=\"bloc_sstags\"></div>";
                $js_sstags = "
                        // Chargement du contenu du bloc
                        $('.bloc_sstags').load('ajax_liste_sstags.php', {url:'$lien_filtre_tri'});

                        $('.lib_sstags a').click(function() {
                            $('.bloc_sstags').slideToggle('slow');
                            if ($('.bloc_sstags_a').html()==\"Choisir une thématique\"){
                                $('.bloc_sstags_a').html(\"Fermer le filtre thématique\");
                            } else {
                                $('.bloc_sstags_a').html(\"Choisir une thématique\");
                            }
                            return false;
                        });

                        // case cocher/decocher
                        $('#cocheTout').live('click', function(){
                            var cases = $('#cases').find(':checkbox');
                            if(this.checked)
                            {
                                cases.attr('checked', true);
                                $('#cocheText').html('Tout décocher');
                            }
                            else
                            {
                                cases.attr('checked', false);
                                $('#cocheText').html('Tout cocher');
                            }
                        });";
            }
            // Js pour les filtres / tri
            echo "<script type=\"text/javascript\">
                    $(function(){
                        $(\"form#ETristructs select\").change(function() {
                          $(\"form#ETristructs\").submit();
                        });
                        $js_sstags
                   });
                </script>";
            echo "</div>";
            //  Fin Filtres
            echo "<p class=\"nb_structs\">";
            if ($nb_struct_total > $nb_struct_list)
            {
                // Affichage du nombre de résultats affichés
                $de = $start + 1;
                if ($start == 0)
                    $a = $nb_struct_list;
                else
                    $a = $de + $nb_struct_list - 1;
                if ($a > $nb_struct_total)
                    $a = $nb_struct_total;
                echo "Résultats $de - $a sur $nb_struct_total<br/>";
            }
            // Affiche le nombre d'évenements total
            if ((!$page_accueil_ville) || ($tous_struct))
            {
                // Nombre d'évenements en bas de page sur la page d'accueil
                if ($nb_struct_locales)
                {
                    // Structures locales / proches
                    echo $nb_struct_locales . " structure";
                    if ($nb_struct_locales > 1)
                        echo "s";
                    echo " dans la ville";
                }
                $nb_struct_proches = $nb_struct_total - $nb_struct_locales;
                if ($nb_struct_proches)
                {
                    if ($nb_struct_locales)
                        echo " + " . $nb_struct_proches;
                    else
                    {
                        echo $nb_struct_proches . " structure";
                        if ($nb_struct_proches > 1)
                            echo "s";
                    }
                    echo " proche";
                    if ($nb_struct_proches > 1)
                        echo "s";
                }
            }
            echo "</p>";
            // Pagination
            if (($nb_pages) && ($nb_struct_total > $nb_struct_list))
            {

                // Paramètres passés en GET
                if ($page_autres == "")
                    $page_autres_p = "?rp=";
                else
                    $page_autres_p = $page_autres."&rp=";


                $rpagination = "<div class=\"pagination\">";
                // Lien retour
                if ($rpage > 1)
                {
                    // page précédente <
                    $prevpage = $rpage - 1;
                    $rpagination .= "<a href='" . $lien_pagination_url . ".html" . $page_autres_p . $prevpage . "#repertoire'><</a>";
                    // Lien page 1
                    if ($rpage > $nb_pages_aff + 1)
                        $rpagination .= "<a href='" . $lien_pagination_url . ".html" . $page_autres_p . "1#repertoire'>1</a>";
                }
                if ($rpage - $nb_pages_aff > 2)
                    $rpagination .= " ... ";
                for ($x = ($rpage - $nb_pages_aff); $x < (($rpage + $nb_pages_aff) + 1); $x++)
                {
                    if (($x > 0) && ($x <= $nb_pages))
                    {
                        // page active ?
                        if ($x == $rpage)
                            $rpagination .= "<a href=\"\" class=\"actif\">" . $x . "</a>";
                        else
                            $rpagination .= "<a href=\"" . $lien_pagination_url . ".html" . $page_autres_p . $x . "#repertoire\" title=\"" . $lien_pagination_alt . $x . "\">" . $x . "</a>";
                    }
                }
                if ($rpage < $nb_pages - $nb_pages_aff - 1)
                    $rpagination .= " ... ";
                // Lien suivant et fin
                if ($rpage != $nb_pages)
                {
                    $nextpage = $rpage + 1;
                    // lien dernière page
                    if ($nb_pages - $rpage > $nb_pages_aff)
                        $rpagination .= "<a href='" . $lien_pagination_url . ".html" . $page_autres_p . $nb_pages . "#repertoire'>$nb_pages</a>";

                    $rpagination .= "<a href='" . $lien_pagination_url . ".html" . $page_autres_p . $nextpage . "#repertoire'>></a>";
                }
                $rpagination .= "</div>";
                echo $rpagination;
            }
            echo "<div class=\"clear\"></div>";
        }
        elseif ($page_home)
        {
            // Phrase accueil
            echo "<p class=\"intro\">$nb_struct_home dernières structures ajoutées :</p>";

            // Affiche le nombre total de structures
            if ($nb_struct_locales || $nb_struct_total)
            {
                $nb_struct_proches = $nb_struct_total - $nb_struct_locales;
                $aff_lien_nb_structs = "<a href=\"";
                $aff_lien_nb_structs .= $root_site;
                $aff_lien_nb_structs .= $titre_ville_url . "." . $id_ville . ".tout.repertoire.html";
                $aff_lien_nb_structs .= "\" title=\"Voir tout le répertoire\">";
                $aff_lien_nb_structs .= "<p class=\"nb_structs\">";
                if ($nb_struct_locales)
                {
                    $aff_lien_nb_structs .= $nb_struct_locales . " structures au total dans la ville";
                    if ($nb_struct_proches)
                        $aff_lien_nb_structs .= " + " . $nb_struct_proches . " proches";
                }
                else
                {
                    if ($nb_struct_proches)
                        $aff_lien_nb_structs .= $nb_struct_proches . " structures proches de la ville";
                }
                $aff_lien_nb_structs .= "</p>";
                $aff_lien_nb_structs .= "</a>";
                $aff_lien_nb_structs .= "<div class=\"clear\"></div>";
            }
        }
        while ($t_struct = $res_struct->fetch(PDO::FETCH_ASSOC))
        {
            if ((!empty($t_struct["NumStructure"])) || (!empty($t_struct["nom"])))
            {
                // Nom de la structure coupé à 130 carractères pour le lien
                $titre_pour_lien = coupe_chaine($t_struct["nom"], 130, false);
                // Structure active ?
                if ($t_struct["etat"] == 0)
                    $struct_active = false;
                else
                    $struct_active = true;
                echo "<article><div class=\"une-struct\">";
                
                if ($t_struct["libelle_statut"])
                {
                  // Ajout du genre à l'url
                  $titre_pour_lien = $t_struct["libelle_statut"] . "-" . $titre_pour_lien;

                  if ($page_home)
                      $statutville = " <span> à " . $t_struct["nom_ville_maj"]."</span>";
                      // $statutville = " <span>(" . $t_struct["libelle_statut"] . " à " . $t_struct["nom_ville_maj"].")</span>";
                  else
                  {
                      $statutville = "";
                      echo "<div class=\"genre_ville\">";
                      echo "<div class=\"libelle_statut\">" . $t_struct["libelle_statut"] . "</div>";
                      echo "<div class=\"ville\">" . $t_struct["nom_ville_maj"] . "</div>";
                      echo "</div>";
                  } 
                }
                // Lien vers le détails de la structure 
                if (!empty($id_vie))
                    $url_id_vie = "." . $id_vie; // id_vie passée en param pour provenance
                if (!empty($id_tag))
                    $url_id_tag = "." . $id_tag; // id_tag passée en param pour provenance
                if ($struct_active)
                    $lien = "structure." . $titre_ville_url . "." . url_rewrite($titre_pour_lien) . "." . $id_ville . "." . $t_struct["NumStructure"] . $url_id_vie . $url_id_tag . ".html";
                // Titre

                if ($struct_active)
                {
                  // Sous titre dans l'infobulle
                  if ($page_home)
                  {
                    if ($t_struct["sous_titre"])
                      echo "<a href=\"" . $lien . "\" title=\"".str_replace('"',"'",ucfirst($t_struct["sous_titre"]))."\">";
                    else
                      echo "<a href=\"" . $lien . "\" title=\"Voir en détails\">";
                  }
                  else
                  {
                    echo "<a href=\"" . $lien . "\" title=\"Voir en détails\">";
                  }
                } 

                echo "<h2>" . ucfirst($t_struct["nom"]);
                if (ceil(round($t_struct["Distance"])))
                {
                    $distance_struct = ceil(round($t_struct["Distance"]) / 10) * 10;
                    echo "<span title=\"A moins de $distance_struct kms, à vol d'oiseau\" class=\"infobulle-b\"> (< " . $distance_struct . " km";
                    if ($distance_struct > 1)
                        echo "s";
                    echo ")</span>";
                }

                if ($statutville) echo $statutville; // Statut et ville sur home

                echo "</h2>";
                if ($struct_active)
                    echo "</a>";

                if (!$page_home) 
                {
                  echo "<br clear=\"all\" />";
                  if ($t_struct["sous_titre"])
                      echo "<p><strong>" . ucfirst($t_struct["sous_titre"]) . "</strong>";
                  // Bouton j'aime (parametre : $type_objet)
                  $no_occurence = $t_struct["NumStructure"];
                  //$urlpage = "www.ensembleici.fr/00_dev/".$lien;
                  require('ajout_fav.php');
                  require('ajout_like.php');
                  echo "</p>";
                }

                if ($espace_perso)
                {
                    echo "<div class=\"actions\">";
                    // Alerte pour les structures désactivées
                    if (!$struct_active)
                    {
                        echo "<div title=\"Structure désactivée par l'équipe 'Ensemble ici'\" class=\"boutonrouge  infobulle-b\">Structure désactivée</div>";
                    }
                    // bouton modifier
                    echo '<form method="POST" action="auto_previsu.php?type=structure" class="formA">
                <input name="no_fiche" value="' . $t_struct["NumStructure"] . '" type="hidden">
                <input name="source" value="espaceperso" type="hidden">
                <input name="type_fiche" value="structure" type="hidden">
                <button type="submit" class="boutonbleu ico-modifier">Modifier</button>
                </form>';
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
            echo $aff_lien_nb_structs;
            echo "<a href=\"";
            echo $root_site;
            echo $titre_ville_url.".".$id_ville.".tout.repertoire.html";
            echo "\" title=\"Voir tout le répertoire\" class=\"boutonbleu ico-agenda\">Tout le répertoire</a>";
            echo "<a href=\"ajouter_une_structure.html\" title=\"Ajouter une structure\" class=\"boutonbleu ico-ajout \">Ajouter une structure</a>";
          echo '</div>';

        }

    }
    else
    {
        echo "<div id=\"message\">";
        echo "Aucune structure enregistrée.";
        echo "</div>"; // actions
        if ($page_home) echo "</div>"; // home-structs
    }

    if ((!$espace_perso)&&(!$page_home))
    {
        echo $rpagination;
        echo "<div class=\"clear\"></div>";
        echo "</div>"; // Repertoire
        echo "<!-- Actions sur repertoire local -->";
        echo "<div class=\"actions\">";
        // Lien "toutes les structures"
        if ((!$tous_struct) && ((!empty($id_ville)) && (!empty($titre_ville_url))))
        {
            if ($nb_struct_locales > 1)
            {
                echo "<a href=\"";
                echo $root_site;
                echo $titre_ville_url . "." . $id_ville . ".tout.repertoire.html";
                // Nombre de structures sur la page d'accueil
                echo "\" title=\"Voir TOut le répertoire : " . $nb_struct_locales . " structures dans la commune $titre_ville\" class=\"boutonbleu ico-agenda\">Tout le répertoire</a>";
            }
        }
        echo "<a href=\"ajouter_une_structure.html\" title=\"Ajouter une structure\" class=\"boutonbleu ico-ajout\">Ajouter une structure</a>";
        echo "</div>"; // actions
        echo "<br/>";
    }
    else if ($page_home){
        echo "</div>"; // Fin home-structs      
    }
}

?>