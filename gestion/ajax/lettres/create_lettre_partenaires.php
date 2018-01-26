<?php
$requete_liste = "SELECT liste FROM lettreinfo_collectif WHERE no_lettre = :no";
$res_liste = $connexion->prepare($requete_liste);
$res_liste->execute(array(":no" => $no));
$tab_liste = $res_liste->fetch();

$liste = $tab_liste['liste'];

$requete_collectif = "SELECT * FROM collectifs WHERE id IN (".$liste.")";
$res_collectif = $connexion->prepare($requete_collectif);
$res_collectif->execute();
$tab_collectif = $res_collectif->fetchAll(); 


$requete_partenaires_lettre = "SELECT * FROM lettreinfo_partenaireinstitutionnel WHERE no_lettre=:no";
$res_partenaires_lettre = $connexion->prepare($requete_partenaires_lettre);
$res_partenaires_lettre->execute(array(":no" => $no));
$tab_partenaires_lettre = $res_partenaires_lettre->fetch();
$liste_partenaires_lettre = $tab_partenaires_lettre["liste"];
if($liste_partenaires_lettre != ""){
    $requete_partenaires = "SELECT * FROM partenaireinstitutionnel WHERE no IN (".$liste_partenaires_lettre.")";
    $res_partenaires = $connexion->prepare($requete_partenaires);
    $res_partenaires->execute();
    $tab_partenaires = $res_partenaires->fetchAll();
}

if (sizeof($tab_collectif) != 0) { ?>
<div style="width:100%;background-color:white;margin-top:40px;border-top: 1px solid #E3D6C7;font-size:16px;font-weight:bold;color:#E75B54;">
    <span style='margin-left: 20px;'>Le collectif "Ensemble ici" :</span>
</div>
<?php
    foreach ($tab_collectif as $k => $v) { ?>
        <div style="float: left;">
            <a href="<?php echo $v['url']; ?>" title="<?php echo $v['libelle']; ?>">
                <img src="http://www.ensembleici.fr/img/lettreinfo/<?php echo $v['image']; ?>" alt="<?php echo $v['libelle']; ?>" style="max-height: 120px;" />
            </a>
        </div>
    <?php } ?>
        <div style="clear:both"></div>
<?php } ?>

 <?php if (sizeof($tab_partenaires) != 0) { ?>
    <div style="width:100%;background-color:white;margin-top:40px;border-top: 1px solid #E3D6C7;font-size:16px;font-weight:bold;color:#E75B54;">
        <span style='margin-left: 20px;'>Les partenaires :</span> 
    </div>
    <?php
    foreach ($tab_partenaires as $k => $v) { ?>
        <div style="float: left; margin-left: 10px;">
            <a href="<?= $v['url']; ?>" title="<?= $v['libelle']; ?>">
                <img src="<?= $root_site.$v['image']; ?>" alt="<?= $v['libelle']; ?>" style="max-height: 120px;" />
            </a>
        </div>
    <?php } ?>
        <div style="clear:both"></div>    
 <?php } ?>
