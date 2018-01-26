<?php
$menu = "<div></div>";

$requete_territoire = "SELECT * FROM territoires WHERE id = :t";
$mon_territoire = execute_requete($requete_territoire,array(":t" => $territoire));
        
$requete_diaporama = "SELECT * FROM diaporamas WHERE territoires_id = :t AND valide = 1 ORDER BY ordre ASC";
$tab_diaporama = execute_requete($requete_diaporama,array(":t" => $territoire));

$requete_communes = "SELECT V.id, V.nom_ville_maj as nom_ville, V.code_postal FROM communautecommune C, communautecommune_ville T, villes V WHERE C.territoires_id = :t 
                    AND C.no = T.no_communautecommune AND T.no_ville = V.id ORDER BY V.code_postal ASC, V.nom_ville ASC";
$tab_communes = execute_requete($requete_communes,array(":t" => $territoire));

$requete_communaute = "SELECT no, libelle FROM communautecommune WHERE territoires_id = :t";
$tab_communaute = execute_requete($requete_communaute,array(":t" => $territoire));

ob_start(); 
?>
<div class="bloc">
    <div style="text-align:right;"><a style="cursor:pointer;" id="link_super_admin">Super Admin</a></div>
</div>
<div class="bloc">
    <div>
        <h3>Facebook, newsletter</h3>
        <form class="form-horizontal" role="form">
            <div class="form-group">
                <label class="col-sm-4 control-label">URL Facebook</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="territoire_facebook" placeholder="URL facebook du territoire" value='<?= ($mon_territoire[0]['facebook'] != '') ? $mon_territoire[0]['facebook'] : '' ?>'>
                    <br/>
                    <span style='margin-left: 15px;'>L'url doit débuter par http:// ou https:// pour être prise en compte de manière correcte</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Adresse email d'envoi de la newsletter</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="territoire_newsletter" placeholder="Adresse email d'envoi de la newsletter" value='<?= ($mon_territoire[0]['mail_newsletter'] != '') ? $mon_territoire[0]['mail_newsletter'] : '' ?>'>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Code google analytics (UA-xxxxx)</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="territoire_code_ua" placeholder="Code google analytics" value='<?= ($mon_territoire[0]['code_ua'] != '') ? $mon_territoire[0]['code_ua'] : '' ?>'>
                </div>
            </div>
            <div class="form-group" style="text-align:center;">
                <div class="col-sm-10">
                    <a id="btn_update_url_territoire" class="btn btn-primary">Enregistrer les modifications</a>
                </div>
            </div>
        </form>
    </div>
    <div>
        <h3>Gestion du diaporama en bandeau d'en-tête</h3>
        <div style="width: 600px; margin: 0 auto;">
            <table>
<!--                <tr class='titre'>
                    <td style="font-weight:bolder;">Image</td>
                    <td style="font-weight:bolder;">Ordre</td>
                    <td class="action"></td>
                </tr>-->
                <?php foreach ($tab_diaporama as $k => $v) { ?>
                <tr>
                    <td style="padding: 10px;"><img src="<?= $root_site ?>img/diapo-index/<?= $v['nom'] ?>" style="height: 100px;" /></td>
                    <!--<td><?= $v['ordre'] ?></td>-->
                    <td class="action_utilisateur">
                        <input type="button" name="btn_del_diaporama" data-ref="<?= $v['id'] ?>" class="etiquette_suppression2" />
                    </td>
                </tr>
                <?php } ?>
            </table>
            <div style='text-align: center; margin-top: 20px;' id="plupload4">
                <a class='btn btn-success' id="browse4">Ajouter une image</a><br/>
                <b>Attention : pour être valable, les images ajoutées doivent être au format 693 x 183</b>
                <div id='progressgen4' class='col-sm-12' style='color:#790000; font-weight: bolder;'></div>
                <div id='filelist4' class='hide'></div>
            </div>
            <div id="valid_plupload4" style='text-align: center;display: none;'>
                <div id='exist_image_name4'></div>
                <a class='btn btn-primary' id="btn_insert_diapo">Valider l'ajout de l'image</a><br/>
            </div>
            
        </div>
    </div>
    <div>
        <h3>Gestion des communes du territoire</h3>
        <div>
            <?php foreach ($tab_communes as $k => $v) { ?>
            <div style="float: left; width: 33%; height: 30px;">
                <a style="cursor: pointer; color: #cc0000" data-ref="<?= $v['id'] ?>" name="del_territoire_ville">X</a>
                <span style="margin-left: 15px;"><?= $v['code_postal'].' '.$v['nom_ville'] ?></span>
            
            </div>

            <?php } ?>
            <div style="clear: both;"></div>
        </div>
        <br/><br/>
        <h3>Ajout de bassins de vie pour le territoire</h3>
        <div>
            Bassins de vie existants : <ul>
            <?php foreach ($tab_communaute as $k => $v) { ?>
                <li><?= $v['libelle'] ?></li>
            <?php } ?>
            </ul>
        </div>
        <div>
            <b>Ajouter un bassin de vie : </b> 
            <input type="text" placeholder="Bassin de vie" id="inp_new_communaute" /> 
            <input type="text" placeholder="Ville principale" id="inp_new_communaute_ville" /> 
            <a id="btn_add_new_communaute" class="btn btn-primary">Ajouter le bassin de vie</a>
        </div>
        <br/><br/>
        <h3>Ajout de communes pour le territoire</h3>
        <b>Ne seront affichées que les commune ne faisant pas partie d'un autre territoire déclaré</b>
        <div id="resultats_territoire" style="display:none;">
            <div id="resultats_territoire_cont">
                
            </div>
            <br/>
            <div style='text-align: center'>
                Sélectionner le bassin de vie auquel associer les communes
                <select id="sel_territoire_communaute">
                    <?php foreach ($tab_communaute as $k => $v) { ?>
                    <option value="<?= $v['no'] ?>"><?= $v['libelle'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <br/>
            <div style='text-align: center'>
                <a class='btn btn-success' id="btn_valid_insert_ville">Ajouter les communes sélectionnées</a><br/>
            </div>
        </div>
        <br/>
        <form class="form-horizontal" role="form">
            <div class="form-group">
                <label class="col-sm-4 control-label">Code postal</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="insert_territoire_code" placeholder="Code postal" maxlength="5" >
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Nom de commune</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="insert_territoire_ville" placeholder="Nom de commune" >
                </div>
            </div>
            <div class="form-group" style="text-align:center;">
                <div class="col-sm-10">
                    <a class='btn btn-primary' id="btn_insert_ville">Rechercher</a><br/>
                </div>
            </div>
        </form>
        
        
    </div>
</div>
<?php
$contenu = ob_get_clean();

$page = array("menu"=>$menu,"contenu"=>$contenu);
?>
