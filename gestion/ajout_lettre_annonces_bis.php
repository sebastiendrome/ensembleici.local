<?php
$date_debut_lettre = date("Y-m-d");
$date_limite = date('Y-m-d', mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));

$compl_req = '';
if (sizeof($tab_info_annonces) > 0) {
    // lettre à modifier
    $compl_req .= " AND A.no NOT IN (".$tab_info_annonces[0]['liste_petiteannonce_complete'].")";
}

$requete_liste_annonces = "SELECT A.no, A.titre, A.url_image, V.nom_ville, V.nom_ville_url, A.no_ville, A.apparition_lettre FROM petiteannonce A, communautecommune_ville T, 
    communautecommune C, villes V WHERE A.etat=1 AND A.validation = 1 AND A.apparition_lettre<2 AND A.date_fin >= :d_d AND A.date_creation >= :d_l AND V.id = A.no_ville AND T.no_ville = A.no_ville AND 
    T.no_communautecommune = C.no AND C.territoires_id = :t".$compl_req;
$res_liste_annonces = $connexion->prepare($requete_liste_annonces);
$res_liste_annonces->execute(array(":d_d"=>$date_debut_lettre, ":d_l"=>$date_limite, ":t" => $territoire));
$tab_liste_annonces = $res_liste_annonces->fetchAll();

foreach ($tab_liste_annonces as $k => $v) { 
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
    $lien = $root_site."petiteannonce.".$v["nom_ville_url"].".".url_rewrite($v["titre"]).".".$v["no_ville"].".".$v["no"].".html";
    ?>
    <div class="lettre_img" name="item_annonces" data-ref="<?= $v['no'] ?>">
        <div style="text-align: right; width: 140px; height: 20px; font-weight: bolder;">
            <div class="icone_apparition"><?= $v['apparition_lettre'] ?></div>
            <a style="cursor:pointer; font-size:20px; color:#ff00ff" name="add_item_annonces"  data-ref="<?= $v['no'] ?>">+</a>
        </div>
        <?php if ($v['url_image'] != '') { ?>
        <div style="width: 140px; height: 140px; margin-left: 10px;"><img style="max-height: 140px; max-width: 140px; margin-left: <?= $margin_left ?>px; margin-top: <?= $margin_top ?>px" src="<?= $root_site.$v['url_image'] ?>" /></div>
        <?php } else { ?>
        <div style="width: 140px; height: 140px; margin-left: 10px;">&nbsp;</div>
        <?php } ?>
        <div style="height: 30px; font-size: 13px; margin-left: 10px;">
            <a href='<?= $lien ?>' target='_blank'><b><?= $v['titre'] ?></b></a>
            <br/><?= $v['nom_ville'] ?>
        </div>
    </div>
<?php }
?>