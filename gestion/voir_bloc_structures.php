<?php
$requete_liste = "SELECT liste_structure_valide FROM lettreinfo_repertoire WHERE no_lettre=:no_l";
$res_liste = $connexion->prepare($requete_liste);
$res_liste->execute(array(":no_l"=>$NO));
$tab_liste = $res_liste->fetchAll();
$liste_valide = $tab_liste[0]["liste_structure_valide"];
if($liste_valide!=""){
    $requete_structure = "SELECT S.no AS st_no_st,S.url_logo,S.nom,S.sous_titre,S.no_ville AS st_no_ville,statut.libelle AS statut, villes.nom_ville_maj AS ville, 
        villes.nom_ville_url FROM structure S JOIN statut ON statut.no=S.no_statut JOIN villes ON villes.id=S.no_ville WHERE S.no IN (".$liste_valide.")";
    $res_structure = $connexion->prepare($requete_structure);
    $res_structure->execute(array(":no_l"=>$NO));
    $liste_structures = $res_structure->fetchAll();
?>
<div style="width:670px;margin:auto;border: 1px solid #E3D6C7;background-color:white;">
    <img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-30.jpg" width="650" height="57" alt="Répertoire" style="width:650px; height: 57px;" id="repertoire" />
    <table style="width:100%;background-color:white;" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-align:center">
            Un aperçu du répertoire partagé sur <a href="http://www.ensembleici.fr">www.ensembleici.fr</a> ! <br/>&nbsp;
            </td>
        </tr>
        <tr>
            <td>
                <table style="width:100%;" border="0" cellpadding="0" cellspacing="0">
                <?php
                //On place les structures
                foreach ($liste_structures as $k => $v) {
//                for($i=0;$i<count($liste_structures);$i++) {

                    // Préparation du lien
                    $lien = $root_site."structure.".$v["nom_ville_url"].".".url_rewrite($v["nom"]).".".$v["st_no_ville"].".".$v["st_no_st"].".html";
                    echo "<tr style='border:none;height:50px;border-bottom:1px solid #F0EDEA;border-top:1px solid #F0EDEA;'><td style='width:30px;'></td>";
                    echo "<td><a href='".$lien."' style='text-decoration:none;' target='_blank'>"; ?>
                    <span style="color:#F6AE48;font-size:16px;font-weight:bold;"><?php echo $v["nom"]; ?></span><br/>
                    <span><?php echo $v["sous_titre"]; ?></span></a></td>
                    <td style="color:#445158;text-align:center;">
                    <?php echo "<a href='".$lien."' style='text-decoration:none;' target='_blank'>"; ?>
                            <strong style="font-weight:bold;"><?= $v["ville"]; ?></strong><br/>
                            <?php if ($v["statut"] != "Autre") { ?><span><?= $v["statut"]; ?></span><?php } ?>
                    </a></td>
                    <td style="width:30px;"></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td style="padding-bottom:10px;padding-top:30px;text-align:center;" colspan="4"><br/>
                    <?php
                            //$lien_ajouter = $root_site."ajouter_une_structure.html";
                            $lien_ajouter = $root_site."espace-personnel.structure.html";
                            echo "<a href='".$lien_ajouter."' style='text-decoration:none;' target='_blank'><img src='".$root_site."img/lettreinfo/ajouter_structure.jpg' alt='Ajouter une structure' /></a>";
                    ?>
                    </td>
                </tr>
                </table>
        </td>
        </tr>
</table>
</div>
<?php } ?>