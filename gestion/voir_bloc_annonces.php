<?php
$requete_liste = "SELECT liste_petiteannonce_valide FROM lettreinfo_petiteannonce WHERE no_lettre=:no_l";
$res_liste = $connexion->prepare($requete_liste);
$res_liste->execute(array(":no_l" => $NO));
$tab_liste = $res_liste->fetchAll();
$liste_valide = $tab_liste[0]["liste_petiteannonce_valide"];
if($liste_valide!=""){
    $requete_petiteannonce = "SELECT PA.no AS pa_no_pa,PA.url_image,PA.titre,PA.monetaire,PA.no_ville AS pa_no_ville, villes.nom_ville_maj AS ville, villes.nom_ville_url 
        FROM petiteannonce PA JOIN villes ON villes.id=PA.no_ville WHERE PA.no IN (".$liste_valide.")";
    $res_petiteannonce = $connexion->prepare($requete_petiteannonce);
    $res_petiteannonce->execute(array(":no_l"=>$NO));
    $liste_petiteannonce = $res_petiteannonce->fetchAll();
}
?>
<div style="width:670px;margin:auto;border: 1px solid #E3D6C7;background-color:white;">
    <img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-28.jpg" width="650px" height="57px" alt="Petites annonces" style="width:650px; height: 57px;" id="petite_annonce" />
    <table style="width:100%;background-color:white;" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-align:center">
            Un aperçu des petites annonces partagées sur <a href="http://www.ensembleici.fr">www.ensembleici.fr</a> ! <br/>&nbsp;
            </td>
        </tr>
        <tr>
            <td>
                <table style="width:100%;" border="0" cellpadding="0" cellspacing="0">
                <?php
                //On place les structures
                foreach ($liste_petiteannonce as $k => $v) {
//                for($i=0; $i<count($liste_petiteannonce); $i++) {

                    // Préparation du lien
                    $lien = $root_site."petiteannonce.".$v["nom_ville_url"].".".url_rewrite($v["titre"]).".".$v["pa_no_ville"].".".$v["pa_no_pa"].".html";

                    echo "<tr style='border:none;height:40px;border-bottom:1px solid #F0EDEA;border-top:1px solid #F0EDEA;'><td style='width:30px;'></td>";
                    echo "<td><a href='".$lien."' style='text-decoration:none;' target='_blank'>";
                    ?>
                    <span style="color:#b9ba35;font-size:16px;font-weight:bold;"><?php echo $v["titre"]; ?></span>
                    <span><?php if ($v["monetaire"]) echo "&nbsp;&nbsp;&nbsp;<img src='".$root_site."img/monetaire.png' width='17px' height='17px' style='width:17px;height:17px;' title='Annonce monétaire' />"; ?></span>
                        </a></td>
                        <td style="color:#445158;text-align:center;">
                                <?php echo "<a href='".$lien."' style='text-decoration:none;' target='_blank'>"; ?>
                                <span style="font-weight:bold;"><?php echo $v["ville"]; ?></span><br/>
                        </a></td>
                        <td style="width:30px;"></td>
                    </tr>
                        <?php
                }
                ?>
                <tr>
                        <td style="padding-bottom:10px;padding-top:30px;text-align:center;" colspan="4"><br/>
                        <?php
                                //$lien_ajouter = $root_site."ajouter_une_petiteannonce.html";
                                $lien_ajouter = $root_site."espace-personnel.petite-annonce.html";
                                echo "<a href='".$lien_ajouter."' style='text-decoration:none;' target='_blank'><img src='".$root_site."img/lettreinfo/ajouter_annonce.jpg' alt='Ajouter une annonce' /></a>";
                        ?>
                        </td>
                </tr>
                </table>
            </td>
        </tr>
</table>
</div>

