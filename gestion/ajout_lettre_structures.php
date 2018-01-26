<?php
$date_debut_structure = time() - 8*24*60*60;
$date_structure= date("Y-m-d",$date_debut_structure);
                
//$requete_liste_structures = "SELECT S.no, S.nom, S.sous_titre, S.url_logo, V.nom_ville, F.libelle FROM structure S, communautecommune_ville T, communautecommune C, villes V, statut F WHERE S.etat=1 AND S.apparition_lettre<2 AND S.date_creation >= :d_d AND V.id = S.no_ville AND T.no_ville = S.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t AND F.no = S.no_statut";
$requete_liste_structures = "SELECT S.no, S.nom, S.sous_titre, S.url_logo, V.nom_ville, F.libelle, S.apparition_lettre FROM structure S, communautecommune_ville T, communautecommune C, villes V, statut F WHERE S.etat=1 AND S.apparition_lettre<2 AND S.date_creation >= :d_d AND V.id = S.no_ville AND T.no_ville = S.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t AND F.no = S.no_statut";
$res_liste_structures = $connexion->prepare($requete_liste_structures);
$res_liste_structures->execute(array(":d_d"=>$date_structure, ":t" => $territoire));
$tab_liste_structures = $res_liste_structures->fetchAll();
foreach ($tab_liste_structures as $k => $v) { 
    $taille = getimagesize($root_serveur.$v['url_logo']);
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
    ?>
    <div class="lettre_img" name="item_structures" data-ref="<?= $v['no'] ?>">
        <div style="text-align: right; width: 140px; height: 20px; font-weight: bolder;">
            <div class="icone_apparition"><?= $v['apparition_lettre'] ?></div>
            <a style="cursor:pointer;" name="del_item_structures" data-ref="<?= $v['no'] ?>">X</a>
        </div>
        <?php if ($v['url_logo'] != '') { ?>
        <div style="width: 140px; height: 140px; margin-left: 10px;"><img style="max-height: 140px; max-width: 140px; margin-left: <?= $margin_left ?>px; margin-top: <?= $margin_top ?>px" src="<?= $root_site.$v['url_logo'] ?>" /></div>
        <?php } else { ?>
        <div style="width: 140px; height: 140px; margin-left: 10px;">&nbsp;</div>
        <?php } ?>
        <div style="height: 30px; font-size: 13px; margin-left: 10px; text-align: center">
            <b><?= $v['nom'] ?></b><br/>
                <?= $v['nom_ville'] ?><br/><b><?= $v['libelle'] ?></b>
        </div>
    </div>
<?php }
?>
<div style="clear: both"></div>