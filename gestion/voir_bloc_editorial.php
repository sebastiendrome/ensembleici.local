<?php
if ($tab_info_edito[0]['mention_permanente']) {
    $requete_info_new_mention = "SELECT corps FROM lettreinfo_edito WHERE no_lettre= 0 AND territoires_id = :t";
    $res_info_new_mention = $connexion->prepare($requete_info_new_mention);
    $res_info_new_mention->execute(array(":t" => $territoire));
    $tab_info_new_mention = $res_info_new_mention->fetchAll();
} 
?>
<div style="width:660px;margin:auto;border: 1px solid #E3D6C7;padding:10px;background-color:white;">
    <img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-22.jpg" width="650px" height="57" alt="Cette semaine" style="width:650px; height: 57px; margin-left: -10px;" id="edito" />
    <table style="width:100%;background-color:white;" border="0" cellpadding="20" cellspacing="0">
        <tr>
            <td>
                    <?php
                    if(($tab_info_edito[0]['mention_permanente'] != false) && $tab_info_edito[0]['avant']){
                            echo "<p>".$tab_info_new_mention[0]['corps']."</p><hr style='margin:10px 0;border:0;border-top:1px solid #E3D6C7;'/>";
                    }
                    ?>
                    <?= $tab_info_edito[0]['corps'] ?>
                    <?php
                    if(($tab_info_edito[0]['mention_permanente'] != false) && !$tab_info_edito[0]['avant']){
                            echo "<hr style='margin:10px 0;border:0;border-top:1px solid #E3D6C7;'/><p>".$tab_info_new_mention[0]['corps']."</p>";
                    }
                    ?>
            </td>
        </tr>
    </table>
</div>