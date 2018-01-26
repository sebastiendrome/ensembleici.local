<?php
$date_debut = time();
$num_jour_courant = date("N");
$nb_jour_dimanche = 7-$num_jour_courant;
//On calcul le timestamp du dimanche qui arrive
$date_fin = $date_debut + ($nb_jour_dimanche+7)*24*60*60;
$date_debut_lettre = date("Y-m-d");
$date_fin_lettre = date("Y-m-d",$date_fin);

$compl_req .= " AND E.no NOT IN (".$tab_info_agenda[0]['liste_evenement_complete'].")";
$requete_liste_agenda = "SELECT E.no, E.titre, E.url_image, E.date_debut, V.nom_ville, G.libelle, V.nom_ville_url, E.no_ville, E.apparition_lettre FROM evenement E, 
    communautecommune_ville T, communautecommune C, villes V, genre G WHERE E.etat=1 AND E.validation = 1 AND E.titre NOT LIKE '%hebdomadaire%' AND E.no_genre<>24 
    AND E.apparition_lettre<2 AND E.date_debut<=:d_f AND E.date_fin>:d_d AND V.id = E.no_ville AND T.no_ville = E.no_ville AND T.no_communautecommune = C.no 
    AND C.territoires_id = :t AND E.no_genre = G.no AND E.no NOT IN (".$tab_info_agenda[0]['liste_evenement_complete'].")";
$res_liste_agenda = $connexion->prepare($requete_liste_agenda);
$res_liste_agenda->execute(array(":d_d"=>$date_debut_lettre,":d_f"=>$date_fin_lettre, ":t" => $territoire));
$tab_liste_agenda = $res_liste_agenda->fetchAll();

foreach ($tab_liste_agenda as $k => $v) { 
    $date_debut = substr($v['date_debut'], 8,2).'/'.substr($v['date_debut'], 5,2).'/'.substr($v['date_debut'], 0,4);
    $taille = getimagesize($root_serveur.$v['url_image']);
    $largeur = $taille[0];
    $hauteur = $taille[1];
    if ($largeur > $hauteur) {
        // marge en haut
        $ratio = $largeur / 140;
        $newhauteur = $hauteur / $ratio; 
        $margin_top = (140 - $newhauteur) / 2;
        $margin_left = 0;
    }
    else {
        // marge gauche
        $ratio = $hauteur / 140;
        $newlargeur = $largeur / $ratio; 
        $margin_left = (140 - $newlargeur) / 2;
        $margin_top = 0;
    }
    $lien = $root_site."evenement.".$v["nom_ville_url"].".".url_rewrite($v["titre"]).".".$v["no_ville"].".".$v["no"].".html";
    ?>
    <div class="lettre_img" name="item_agenda" data-ref="<?= $v['no'] ?>">
        <div style="text-align: right; width: 140px; height: 20px; font-weight: bolder;">
            <div class="icone_apparition"><?= $v['apparition_lettre'] ?></div>
            <a style="cursor:pointer; font-size:20px; color:#ff00ff;" name="add_item_agenda"  data-ref="<?= $v['no'] ?>">+</a>
            <div style="clear:both"></div>
        </div>
        <?php if ($v['url_image'] != '') { ?>
        <div style="width: 140px; height: 140px; margin-left: 10px;">
            <img style="max-height: 140px; max-width: 140px; margin-left: <?= $margin_left ?>px; margin-top: <?= $margin_top ?>px" src="<?= $root_site.$v['url_image'] ?>" />
        </div>
        <?php } else { ?>
        <div style="width: 140px; height: 140px; margin-left: 10px;">&nbsp;</div>
        <?php } ?>
        <div style="height: 70px; font-size: 13px; margin-left: 10px; text-align: center;" title='<?= $v['titre'] ?>'>
            <b><a href='<?= $lien ?>' target='_blank'><?= substr($v['titre'],0,20) ?></a></b>
            <br/><?= $v['nom_ville'].' - '.$date_debut ?><br/><b><?= $v['libelle'] ?></b>
        </div>
    </div>
<?php }
?>