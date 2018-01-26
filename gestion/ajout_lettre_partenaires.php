<?php             
$requete_liste_partenaires = "SELECT * FROM partenaireinstitutionnel WHERE territoires_id = :t AND libelle <> ''";
$res_liste_partenaires = $connexion->prepare($requete_liste_partenaires);
$res_liste_partenaires->execute(array(":t" => $territoire));
$tab_liste_partenaires = $res_liste_partenaires->fetchAll();

foreach ($tab_liste_partenaires as $k => $v) { 
    $taille = getimagesize($root_serveur.$v['image']);
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
    <div class="lettre_img" name="item_partenaires" data-ref="<?= $v['no'] ?>">
        <div style="text-align: right; width: 140px; height: 20px; font-weight: bolder;">
            <a style="cursor:pointer;" name="del_item_partenaires"  data-ref="<?= $v['no'] ?>">X</a>
        </div>
        <?php if ($v['image'] != '') { ?>
        <div style="width: 140px; height: 140px; margin-left: 10px;"><img style="max-height: 140px; max-width: 140px; margin-left: <?= $margin_left ?>px; margin-top: <?= $margin_top ?>px" src="<?= $root_site.$v['image'] ?>" /></div>
        <?php } else { ?>
        <div style="width: 140px; height: 140px; margin-left: 10px;">&nbsp;</div>
        <?php } ?>
        <div style="height: 70px; font-size: 13px; margin-left: 10px; text-align: center;" title='<?= $v['libelle'] ?>'><b><?= $v['libelle'] ?></b></div>
    </div>
<?php }
?>
<div style="clear: both"></div>