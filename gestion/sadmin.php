<?php
$menu = "<div></div>";

$requete_territoire = "SELECT * FROM territoires ORDER BY id";
$les_territoires = execute_requete($requete_territoire);

ob_start(); 
?>
<div class="bloc">
    <div style="text-align:right;"><a style="cursor:pointer;" id="retour_super_admin">Gestion territoire</a></div>
</div>
<div class="bloc">
    <div>
        <h3>Gestion des territoires</h3>
        <div>
            <h4>Liste des territoires</h4>
            <table>
                <thead>
                    <tr style="height: 40px; border-bottom: 1px solid #cccccc">
                        <th>Nom</th>
                        <th>Newsletter</th>
                        <th>Facebook</th>
                        <th>Code Analytics</th>
                        <th>Date de démarrage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($les_territoires as $k => $v) {
                        $date_dem = substr($v['date_demarrage'], 8, 2).'/'.substr($v['date_demarrage'], 5, 2).'/'.substr($v['date_demarrage'], 0, 4);
                        ?>
                        <tr style="height: 40px; border-bottom: 1px dotted #cccccc">
                            <td><?= $v['nom'] ?></td>
                            <td><?= $v['mail_newsletter'] ?></td>
                            <td><?= urldecode($v['facebook']) ?></td>
                            <td><?= $v['code_ua'] ?></td>
                            <td><?= $date_dem ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div style="text-align: center; margin-top: 25px;">
                <a class="btn btn-primary" id="link_add_territoire">Ajouter un territoire</a>
            </div>
        </div>
    </div>
</div>
<?php
$contenu = ob_get_clean();

$page = array("menu"=>$menu,"contenu"=>$contenu);
?>
