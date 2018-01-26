<?php
$requete_edito = "SELECT * FROM lettreinfo_edito WHERE no_lettre=:no_l";
$res_edito = $connexion->prepare($requete_edito);
$res_edito->execute(array(":no_l" => $no));
$tab_edito = $res_edito->fetch();

if ($tab_edito['mention_permanente']) {
    $requete_info_new_mention = "SELECT corps FROM lettreinfo_edito WHERE no_lettre= 0 AND territoires_id = :t";
    $res_info_new_mention = $connexion->prepare($requete_info_new_mention);
    $res_info_new_mention->execute(array(":t" => $territoire));
    $tab_info_new_mention = $res_info_new_mention->fetch();
} 
?>
<img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-22.jpg" width="650px" height="57" alt="Cette semaine" style="padding-top:20px;padding-bottom:10px;width:650px;" id="edito" />
<table style="width:100%;background-color:white;" border="0" cellpadding="20" cellspacing="0">
    <tr>
        <td>
                <?php
                if(($tab_edito['mention_permanente'] != false) && $tab_edito['avant']){
                    echo "<p>".$tab_info_new_mention['corps']."</p><hr style='margin:10px 0;border:0;border-top:1px solid #E3D6C7;'/>";
                }
                ?>
                <?= $tab_edito['corps'] ?>
                <?php
                if(($tab_edito['mention_permanente'] != false) && !$tab_edito['avant']){
                    echo "<hr style='margin:10px 0;border:0;border-top:1px solid #E3D6C7;'/><p>".$tab_info_new_mention['corps']."</p>";
                }
                ?>
        </td>
    </tr>
</table>
