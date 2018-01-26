<?php
$requete_liste = "SELECT liste_evenement_valide FROM lettreinfo_agenda WHERE no_lettre=:no_l";
$res_liste = $connexion->prepare($requete_liste);
$res_liste->execute(array(":no_l"=>$NO));
$tab_liste = $res_liste->fetchAll();
$liste_valide = $tab_liste[0]["liste_evenement_valide"];
if($liste_valide!=""){
    $requete_agenda = "SELECT E.no AS evt_no_evt,E.url_image,E.titre,E.sous_titre,E.date_debut,E.date_fin, E.no_ville AS evt_no_ville, genre.libelle AS genre, 
        genre.type_genre AS a_e, villes.nom_ville_maj AS ville, villes.nom_ville_url, IFNULL(communautecommune_ville.no_communautecommune,0) AS no_cc, 
        IFNULL(communautecommune.libelle,'[]') AS lib_cc FROM evenement E JOIN villes ON villes.id=E.no_ville JOIN genre ON genre.no=E.no_genre 
        LEFT JOIN communautecommune_ville ON communautecommune_ville.no_ville = villes.id LEFT JOIN communautecommune ON communautecommune_ville.no_communautecommune = communautecommune.no 
        WHERE E.no IN (".$liste_valide.") ORDER BY lib_cc,E.date_debut,E.date_fin,villes.nom_ville_maj";
    $res_agenda = $connexion->prepare($requete_agenda);
    $res_agenda->execute(array(":no_l"=>$NO));
    $liste_agenda = $res_agenda->fetchAll();
}
?>
<div style="width:670px;margin:auto;border: 1px solid #E3D6C7;background-color:white;">
    <img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-24.jpg" width="650px" height="57" alt="Evènements" style="width:650px; height: 57px;" id="evenement" />
    <table style="width:100%;background-color:white; margin-left: 0px;" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table style="width:100%;" border="0" cellpadding="0" cellspacing="0">
                <?php
                //On place les événéments
                $communaute_commune_courante = -1;
                $colonne = 0; // colonne du td
                $premiere_cc = true; // premiere comunauté de communes

                
                foreach ($liste_agenda as $k => $v) {
//                for($i=0;$i<count($liste_agenda);$i++) {
                        $colonne++;
                        if ($colonne > 3) $colonne = 1;

                        if($communaute_commune_courante != $v["no_cc"]) {
                                if ($colonne != 1)
                                {
                                        // complete la ligne
                                        $reste_col = 3 - $colonne; //[Modification Sam 04/07/13] (il y avait écrit 4 - $colonne) or il n'y a que 3 colonnes
                                        while ($reste_col) {
                                                echo "<td width='33%' style='width:33%;padding:10px;border-bottom:1px solid #E3D6C7;'>&nbsp;</td>";
                                                $reste_col--;
                                        }
                                        echo "</tr>";
                                        $colonne = 1;
                                }

                                $communaute_commune_courante = $v["no_cc"];
                                if ($premiere_cc) { 
                                    $bordcc = "2"; 
                                    $premiere_cc = false; 
                                } else { 
                                    $bordcc = "1";
                                }
                                echo '<tr><td colspan="3" style="height:40px;background-color:#F0EDEA;border-bottom:2px solid #E3D6C7;border-top:'.$bordcc.'px solid #E3D6C7;font-weight:bold;color:#445158;padding-left:20px;padding-top:3px;font-size:17px;text-transform:uppercase;">';
                                if($v["lib_cc"] != "[]") { 
                                    echo trim($v["lib_cc"]);
                                } else { 
                                    echo "Ailleurs dans le coin";
                                }
                                echo '</td></tr>';
                        }

                        // Préparation du lien
                        $lien = $root_site."evenement.".$v["nom_ville_url"].".".url_rewrite($v["titre"]).".".$v["evt_no_ville"].".".$v["evt_no_evt"].".html";

                        // Nouvelle ligne ?
                        if ($colonne == 1) {
                            echo "<tr>";
                        }

                        if ($colonne == 3) {
                                $borderl = "";
                        } else {
                            $borderl = "border-right:1px solid #E3D6C7;";
                        }

                        echo "<td width='33%' style='width:33%;vertical-align:top;padding:10px;border-bottom:1px solid #E3D6C7;".$borderl."'>";
                        echo "<a href=\"".$lien."\" style=\"text-decoration:none;\" target=\"_blank\">";

                        // conteneur de l'img
                        echo "<div style='width:196px;height:120px;overflow:hidden;vertical-align:middle;'>";

                        // Si une image existe, on la place.
                        if(($v["url_image"] != "") && ($v["url_image"] != null)) {
                            if(substr($v["url_image"],0,7) != "http://") {
                                $v["url_image"] = $root_site.$v["url_image"];
                            }

                            // Dimmensions img
                            list($largeur,$hauteur) = getimagesize($v["url_image"]);
                            if($largeur!=0&&$hauteur!=0){
                                $largeur_img = 196;
                                $hauteur_img = 120;

                                $new_largeur = $largeur_img;
                                $new_hauteur = $new_largeur*$hauteur/$largeur;
                                if($new_hauteur>$largeur_img){
                                        $new_hauteur = $largeur_img;
                                        $new_largeur = $new_hauteur*$largeur/$hauteur;
                                }
                                // centrer horizontalement
                                if($new_largeur<$largeur_img)
                                        $margin_x = floor(($largeur_img-$new_largeur)/2);
                                else
                                        $margin_x = 0;

                                // centrer vertivalement (en + ou en -)
                                $margin_y = floor(($hauteur_img-$new_hauteur)/2);
                            }
                            else{
                                $v["url_image"] = $root_site."img/logo-ensembleici_nl.jpg";
                                $new_hauteur = 120;
                                $new_largeur = 196;
                            }

                        ?>
                            <img src="<?= $v["url_image"]; ?>" style="width:<?= floor($new_largeur); ?>px;height:<?= floor($new_hauteur); ?>px;margin:<?= $margin_y; ?>px auto 0 <?= $margin_x; ?>px;" />
                        <?php
                        }
                        else {
                                // Image toujours non existante ? => image par défaut
                                echo "<img src='".$root_site."img/logo-ensembleici_nl.jpg' style='width:196px;height:120px;margin:0;' />";
                        }
                        echo "</div><br/>" // fin img
                        ?>

                        <span style="font-size:16px;font-weight:bold;color:#E75B54;"><?php echo $v["titre"]; ?></span>
                        <br/><span style="color:#445158;"><?php echo $v["genre"]; ?> , <?php echo $v["ville"]; ?></span><br/>
                        <strong style="color:black;font-weight:bold;font-size:11px;"><?php echo affiche_date_evt($v["date_debut"],$v["date_fin"]); ?></strong>
                        </a></td>

                        <?php
                        if ($colonne == 3) {
                            echo "</tr>";
                        }

                } // Fin for

                if ($colonne < 3) {
                    // complete la ligne
                    $reste_col = 3 - $colonne;
                    while ($reste_col) {
                        echo "<td width='33%' style='width:33%;padding:30px;border-bottom:1px solid #E3D6C7;font-style:italic; color:#E16A0C'><a style='font-style:italic;color:#E16A0C;text-decoration:none;display: block;width:100%' href='http://www.ensembleici.fr'>Retrouvez l'agenda complet sur www.ensembleici.fr...</a></td>";
                        $reste_col--;
                    }
                    echo "</tr>";				
                }

                        ?>
                </table>
            </td>
        </tr>
        <tr>
                <td style="padding-bottom:10px;padding-top:30px;text-align:center;"><br/>
                <?php
                    $lien_marches = $root_site."lettreinfos.marche-brocante.tag.[**idv**].1356529681.1.html";
                    echo "<a href=\"".$lien_marches."\" style=\"text-decoration:none;\" target=\"_blank\"><img src=\"".$root_site."img/lettreinfo/voir_marches.jpg\" alt=\"Voir les marchés\" width=\"266px\" style=\"width:266px;margin:0;\" /></a>";
                    $lien_agenda = $root_site."lettreinfos.[**idv**].tout.agenda.html";
                    echo "<a href=\"".$lien_agenda."\" style=\"text-decoration:none;\" target=\"_blank\"><img src=\"".$root_site."img/lettreinfo/voir_agenda.jpg\" alt=\"Voir tout l'agenda\" width=\"216px\" style=\"width:216px;margin:0;\" /></a>";
                    $lien_ajouter = $root_site."espace-personnel.agenda.html";
                    echo "<br/><br/><a href=\"".$lien_ajouter."\" style=\"text-decoration:none;\" target=\"_blank\"><img src=\"".$root_site."img/lettreinfo/ajouter_evenement.jpg\" alt=\"Ajouter un événement\" /></a>";
                ?>
                </td>
        </tr>
</table>
</div>

