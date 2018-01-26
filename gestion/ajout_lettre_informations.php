<?php
if ($NO != -1) {
    $menu_envoi = 1;
    // recherche des étapes de la lettre
    $requete_info_agenda = "SELECT * FROM lettreinfo_agenda WHERE no_lettre=:no";
    $res_info_agenda = $connexion->prepare($requete_info_agenda);
    $res_info_agenda->execute(array(":no"=>$NO));
    $tab_info_agenda = $res_info_agenda->fetchAll();
    if ((sizeof($tab_info_agenda) > 0) && ($tab_info_agenda[0]['liste_evenement_valide'] != '')) {
        $agenda_valide = 0;
    }
    else {
        $agenda_valide = 1;
        $menu_envoi = 0;
    }
    
    $requete_info_edito = "SELECT * FROM lettreinfo_edito WHERE no_lettre=:no";
    $res_info_edito = $connexion->prepare($requete_info_edito);
    $res_info_edito->execute(array(":no"=>$NO));
    $tab_info_edito = $res_info_edito->fetchAll();
    if ((sizeof($tab_info_edito) > 0) && ($tab_info_edito[0]['etape_valide'] == 1)) {
        $edito_valide = 0;
    }
    else {
        $edito_valide = 1;
        $menu_envoi = 0;
    }
    
    $requete_info_annonces = "SELECT * FROM lettreinfo_petiteannonce WHERE no_lettre=:no";
    $res_info_annonces = $connexion->prepare($requete_info_annonces);
    $res_info_annonces->execute(array(":no"=>$NO));
    $tab_info_annonces = $res_info_annonces->fetchAll();
    if ((sizeof($tab_info_annonces) > 0) && ($tab_info_annonces[0]['liste_petiteannonce_valide'] != '')) {
        $annonce_valide = 0;
    }
    else {
        $annonce_valide = 1;
        $menu_envoi = 0;
    }
    
    $requete_info_repertoire = "SELECT * FROM lettreinfo_repertoire WHERE no_lettre=:no";
    $res_info_repertoire = $connexion->prepare($requete_info_repertoire);
    $res_info_repertoire->execute(array(":no"=>$NO));
    $tab_info_repertoire = $res_info_repertoire->fetchAll();
    if (sizeof($tab_info_repertoire) > 0) {
        $repertoire_valide = 0;
    }
    else {
        $repertoire_valide = 1;
        $menu_envoi = 0;
    }
    
    $requete_info_publicite = "SELECT * FROM lettreinfo_publicite WHERE no_lettre=:no";
    $res_info_publicite = $connexion->prepare($requete_info_publicite);
    $res_info_publicite->execute(array(":no"=>$NO));
    $tab_info_publicite = $res_info_publicite->fetchAll();
    if (sizeof($tab_info_publicite) > 0) {
        $publicite_valide = 0;
    }
    else {
        $publicite_valide = 1;
    }
    
    $pdf_valide = 1;
    if (($malettre['pdf_agenda'] != '') || ($malettre['pdf_annonces'])) {
        $pdf_valide = 0;
    }
    
    $requete_info_partenaire = "SELECT * FROM lettreinfo_partenaireinstitutionnel WHERE no_lettre=:no";
    $res_info_partenaire = $connexion->prepare($requete_info_partenaire);
    $res_info_partenaire->execute(array(":no"=>$NO));
    $tab_info_partenaire = $res_info_partenaire->fetchAll();
    if (sizeof($tab_info_partenaire) > 0) {
        $partenaire_valide = 0;
    }
    else {
        $partenaire_valide = 1;
        $menu_envoi = 0;
    }
    
    // recherche de la metnion permanente
    $requete_info_mention = "SELECT corps FROM lettreinfo_edito WHERE no_lettre= 0 AND territoires_id = :t";
    $res_info_mention = $connexion->prepare($requete_info_mention);
    $res_info_mention->execute(array(":t" => $territoire));
    $tab_info_mention = $res_info_mention->fetchAll();
    
    $madate_debut = substr($malettre['date_debut'], 8, 2).'/'.substr($malettre['date_debut'], 5, 2).'/'.substr($malettre['date_debut'], 0, 4);
}

ob_start();
?>
<span id='li_url' class='hide'><?= $root_site.'gestion/?page=lettre-information' ?></span>
<span id="li_no_bloc" class="hide"><?= isset($_GET['bloc']) ? $_GET['bloc'] : '' ?></span>
<span id="is_menu_envoi" class="hide"><?= $menu_envoi ?></span>
<div style="text-align: center; font-weight: bolder; font-size: 16px; margin-top: 25px;">
    &Eacute;tapes <span class="couleur_etape_valide_nopad">validées</span> ou <span class="couleur_etape_nonvalide_nopad">en attente de validation</span> : 
    <span class="<?= ($pdf_valide) ? 'couleur_etape_nonvalide' : 'couleur_etape_valide' ?>">PDF</span> | 
    <span class="<?= ($edito_valide) ? 'couleur_etape_nonvalide' : 'couleur_etape_valide' ?>">&Eacute;DITORIAL</span> | 
    <span class="<?= ($agenda_valide) ? 'couleur_etape_nonvalide' : 'couleur_etape_valide' ?>">AGENDA</span> | 
    <span class="<?= ($annonce_valide) ? 'couleur_etape_nonvalide' : 'couleur_etape_valide' ?>">ANNONCES</span> | 
    <span class="<?= ($repertoire_valide) ? 'couleur_etape_nonvalide' : 'couleur_etape_valide' ?>">R&Eacute;PERTOIRE</span> | 
    <span class="<?= ($partenaire_valide) ? 'couleur_etape_nonvalide' : 'couleur_etape_valide' ?>">PARTENAIRES</span> | 
    <span class="<?= ($publicite_valide) ? 'couleur_etape_nonvalide' : 'couleur_etape_valide' ?>">PUBLICIT&Eacute;S</span>
</div>
<div class="container">
    <div id="li_generalites" class='li_container' style="display: block;" name="li_bloc">
        <h2 class="h2">Généralités</h2>
        <div class="row">
            <form class="form-horizontal" role="form">
                <div class="form-group">
                    <label for="li_inp_titre" class="col-sm-3 control-label">Titre de la lettre</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="li_inp_titre" value="<?= ($NO != -1) ? $malettre['objet'] : '' ?>" <?= ($NO != -1) ? 'disabled="disabled"' : '' ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="li_inp_date_debut" class="col-sm-3 control-label">Date de début</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="li_inp_date_debut" value="<?= ($NO != -1) ? $madate_debut : date('d/m/Y') ?>" <?= ($NO != -1) ? 'disabled="disabled"' : '' ?>>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12" style='text-align:center;'>
                        <?php if ($NO == -1) { ?>
                        <a class="btn btn-success" id="li_valid_etape_generalites">Créer une nouvelle lettre</a>
                        <?php } else { ?>
                        <a class="btn btn-success" id="li_valid_etape_generalites_bis">&Eacute;tape suivante</a>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="li_pdf" class='li_container' style="display: none;" name="li_bloc">
        <h2 class="h2">Fichiers PDF</h2>
        <div class="row">
            <form class="form-horizontal" role="form" style='margin-left:25px;'>
                <?php if ($pdf_valide) { ?>
                <div class="checkbox col-sm-9">
                    <label>
                        <input type="checkbox" id="li_ckb_agenda"> Générer le fichier PDF <b>Agenda</b> et inclure le lien dans la newsletter<br/>
                        Date limite d'affichage pour les évènements <input type='text' id='date_fin_pdf' />
                    </label>
                </div>
                <div class="checkbox col-sm-9">
                    <label>
                        <input type="checkbox" id="li_ckb_annonces"> Générer le fichier PDF <b>Petites annonces</b> et inclure le lien dans la newsletter
                    </label>
                </div>
                
                <div style="display:none;" id="agenda_view_pdf" class="col-sm-12">
                    <br/>
                    Fichier PDF agenda généré : <a target="_blank"></a>
                </div>
                <div style="display:none;" id="annonces_view_pdf" class="col-sm-12">
                    <br/>
                    Fichier PDF annonces généré : <a target="_blank"></a>
                </div>
                <div class="form-group">
                    <div class="col-sm-12" style='text-align:center;'>
                        <a class="btn btn-primary" id="li_generate_file_pdf" data-ref="<?= $NO ?>">Générer les fichiers</a>
                        <a class="btn btn-success" id="li_valid_etape_pdf" data-ref="<?= $NO ?>">Valider l'&eacute;tape</a>
                    </div>
                </div>
                <?php } else { ?>
                <?php include 'voir_bloc_pdf.php'; ?>
                <br/><br/>
                <div class="form-group">
                    <div class="col-sm-12" style='text-align:center;'>
                        <a class="btn btn-primary" id="li_creation_pdf" data-ref="<?= $NO ?>">Retour en mode création</a>
                    </div>
                </div>
                <?php } ?>
            </form>
        </div>
    </div>
    <div id="li_editorial" class='li_container' style="display: none;" name="li_bloc">
        <h2 class="h2">&Eacute;ditorial</h2>
        <div class="row">
            <form class="form-horizontal" role="form" style='margin-left:25px;'>
                <?php if ($edito_valide) {  ?>
                <div class="form-group">
                    <label for="li_inp_edito" class="col-sm-2 control-label">Editorial</label>
                    <div class="col-sm-9">
                        <textarea id="li_inp_edito"><?= (isset($tab_info_edito[0]['corps'])) ? $tab_info_edito[0]['corps'] : '' ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="li_inp_mention" class="col-sm-2 control-label">Mention permanente</label>
                    <div class="col-sm-9">
                        <textarea id="li_inp_mention"><?= (isset($tab_info_mention[0]['corps'])) ? $tab_info_mention[0]['corps'] : '' ?></textarea>
                    </div>
                </div>
                <div class="checkbox col-sm-6 col-sm-offset-2">
                    <label>
                        <input type="checkbox" id="li_ckb_mention" checked="checked"> Inclure la mention permanente
                    </label>
                </div>
                <div class="form-group">
                    <div class="col-sm-12" style='text-align:center; margin-top: 15px;'>
                        <a class="btn btn-warning" id="li_update_mention" data-ref="<?= $NO ?>">Modifier la mention permanente</a>
                        <a class="btn btn-success" id="li_valid_etape_editorial" data-ref="<?= $NO ?>">Valider l'&eacute;tape</a>
                    </div>
                </div>
                <?php } else { ?>
                    <h3 class="h3">Bloc éditorial</h3>
                    <?php include 'voir_bloc_editorial.php'; ?>
                    <br/><br/>
                    <div class="form-group">
                        <div class="col-sm-12" style='text-align:center;'>
                            <a class="btn btn-primary" id="li_creation_editorial" data-ref="<?= $NO ?>">Retour en mode création</a>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div> 
    <div id="li_agenda" class='li_container' style="display: none;" name="li_bloc">
        <h2 class="h2">Agenda</h2>
        <div class="row">
            <form class="form-horizontal" role="form" style='margin-left:25px;margin-right:25px'>
                <?php if ($agenda_valide) { ?>
                    <h4 style="margin-left:20px;">&Eacute;vénements pris en compte</h4>
                    <div id="liste_item_agenda">
                    <?php include 'ajout_lettre_agenda.php'; ?>
                    </div>
                    <div style="clear: both"></div>
                    <hr>
                    <div id="liste_item_agenda_bis" style="background-color:#dddddd; padding-top: 10px; padding-bottom: 10px;">
                        <h4 style="margin-left:20px;">&Eacute;vénements non pris en compte</h4>
                    <?php if (sizeof($tab_info_agenda) > 0) { include 'ajout_lettre_agenda_bis.php'; } ?>
                    </div>
                    <div style="clear: both"></div>
                    <br/><br/>
                    <div class="form-group">
                        <div class="col-sm-12" style='text-align:center;'>
                            <a class="btn btn-warning" id="li_boite_etape_agenda" data-ref="<?= $NO ?>">Ajouter un événement</a>
                            <a class="btn btn-success" id="li_valid_etape_agenda" data-ref="<?= $NO ?>">Valider l'&eacute;tape</a>
                        </div>
                    </div>
                <?php } else { ?>
                    <h3 class="h3">Bloc agenda</h3>
                    <?php include 'voir_bloc_agenda.php'; ?>
                    <br/><br/>
                    <div class="form-group">
                        <div class="col-sm-12" style='text-align:center;'>
                            <a class="btn btn-primary" id="li_creation_agenda" data-ref="<?= $NO ?>">Retour en mode création</a>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div> 
    <div id="li_annonces" class='li_container' style="display: none;" name="li_bloc">
        <h2 class="h2">Annonces</h2>
        <div class="row">
            <form class="form-horizontal" role="form" style='margin-left:25px;'>
                <?php if ($annonce_valide) { ?>
                    <h4 style="margin-left:20px;">Annonces prises en compte</h4>
                    <div id="liste_item_annonces">
                        <?php include 'ajout_lettre_annonces.php'; ?>
                    </div>
                    <div style="clear: both"></div>
                    <hr>
                    <div id="liste_item_annonces_bis" style="background-color:#dddddd; padding-top: 10px; padding-bottom: 10px;">
                        <h4 style="margin-left:20px;">Annonces non prises en compte</h4>
                        <?php if (sizeof($tab_info_annonces) > 0) {  include 'ajout_lettre_annonces_bis.php'; } ?>
                    </div>
                    <br/><br/>
                    <div class="form-group">
                        <div class="col-sm-12" style='text-align:center;'>
                            <a class="btn btn-warning" id="li_boite_etape_annonces" data-ref="<?= $NO ?>">Ajouter une annonce</a>
                            <a class="btn btn-success" id="li_valid_etape_annonces" data-ref="<?= $NO ?>">Valider l'&eacute;tape</a>
                        </div>
                    </div>
                <?php } else { ?>
                    <h3 class="h3">Bloc annonces</h3>
                    <?php include 'voir_bloc_annonces.php'; ?>
                    <br/><br/>
                    <div class="form-group">
                        <div class="col-sm-12" style='text-align:center;'>
                            <a class="btn btn-primary" id="li_creation_annonces" data-ref="<?= $NO ?>">Retour en mode création</a>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div> 
    <div id="li_structures" class='li_container' style="display: none;" name="li_bloc">
        <h2 class="h2">Structures</h2>
        <div class="row">
            <form class="form-horizontal" role="form" style='margin-left:25px;'>
                <?php if ($repertoire_valide) { ?>
                    <h3 class='h3'>Choix des structures</h3>
                    <div id="liste_item_structures">
                        <?php include 'ajout_lettre_structures.php'; ?>
                    </div>
                    <br/><br/>
                    <div class="form-group">
                        <div class="col-sm-12" style='text-align:center;'>
                            <a class="btn btn-warning" id="li_boite_etape_structures" data-ref="<?= $NO ?>">Promouvoir une structure</a>
                            <a class="btn btn-success" id="li_valid_etape_structures" data-ref="<?= $NO ?>">Valider l'&eacute;tape</a>
                        </div>
                    </div>
                <?php } else { ?>
                    <h3 class="h3">Bloc répertoire</h3>
                    <?php include 'voir_bloc_structures.php'; ?>
                    <br/><br/>
                    <div class="form-group">
                        <div class="col-sm-12" style='text-align:center;'>
                            <a class="btn btn-primary" id="li_creation_structures" data-ref="<?= $NO ?>">Retour en mode création</a>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div> 
    <div id="li_partenaires" class='li_container' style="display: none;" name="li_bloc">
        <h2 class="h2">Partenaires</h2>
        <div class="row">
            <form class="form-horizontal" role="form" style='margin-left:25px;'>
                <?php if ($partenaire_valide) { ?>
                    <h3 class='h3'>Choix des partenaires</h3>
                    <div id="liste_item_partenaires">
                        <?php include 'ajout_lettre_partenaires.php'; ?>
                    </div>
                    <br/><br/>
                    <h3 class='h3'>Choix des structures du collectif</h3>
                    <div id="liste_item_collectif">
                        <?php include 'ajout_lettre_collectif.php'; ?>
                    </div>
                    <br/><br/>
                    <div class="form-group">
                        <div class="col-sm-12" style='text-align:center;'>
                            <a class="btn btn-primary" id="li_add_partenaire" data-ref="<?= $NO ?>">Ajouter un partenaire</a>
                            <a class="btn btn-success" id="li_valid_etape_partenaires" data-ref="<?= $NO ?>">Valider l'&eacute;tape</a>
                        </div>
                    </div>
                <?php } else { ?>
                    <h3 class="h3">Bloc partenaire</h3>
                    <?php include 'voir_bloc_partenaires.php'; ?>
                    <br/><br/>
                    <div class="form-group">
                        <div class="col-sm-12" style='text-align:center;'>
                            <a class="btn btn-primary" id="li_creation_partenaires" data-ref="<?= $NO ?>">Retour en mode création</a>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div> 
    <div id="li_publicites" class='li_container' style="display: none;" name="li_bloc">
        <h2 class="h2">Publicités</h2>
        <div class="row">
            <form class="form-horizontal" role="form" style='margin-left:25px;'>
                <?php if ($publicite_valide) { ?>
                    <h3 class='h3'>Choix des publicités</h3>
                    <div id="liste_item_publicites">
                        <?php include 'ajout_lettre_publicites.php'; ?>
                    </div>
                    <br/><br/>
                    <div class="form-group">
                        <div class="col-sm-12" style='text-align:center;'>
                            <a class="btn btn-success" id="li_valid_etape_publicites" data-ref="<?= $NO ?>">Valider l'&eacute;tape</a>
                        </div>
                    </div>
                <?php } else { ?>
                    <h3 class="h3">Bloc publicités</h3>
                    <?php include 'voir_bloc_publicites.php'; ?>
                    <br/><br/>
                    <div class="form-group">
                        <div class="col-sm-12" style='text-align:center;'>
                            <a class="btn btn-primary" id="li_creation_publicites" data-ref="<?= $NO ?>">Retour en mode création</a>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div> 
    <div id="li_envoi" class='li_container' style="display: none;" name="li_bloc">
        <h2 class="h2">Envoi de la lettre</h2>
        <div class="row">
            <div class="col-sm-12" style='margin-left:50px;'>
                <div style='font-size:16px; font-weight: bolder;'>
                Toutes les étapes ont été validées. Vous pouvez valider la lettre et procéder à un test d'envoi. 
                Une fois validé, vous pourrez également consulter la lettre à partir du navigateur. 
                </div>
                <br/>
                <form class="form-horizontal" role="form" style='margin-left:25px;'>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Adresse email de test</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="lettre_test_email" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12" style='text-align:center;'>
                          <a class="btn btn-primary" id="li_creation_lettre" data-ref="<?= $NO ?>">Valider la lettre et envoyer le test</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> 
    <div id="li_confirm" class='li_container' style="display: none;" name="li_bloc">
        <h2 class="h2">Envoi de la lettre</h2>
        <div class="row">
            <div class="col-sm-12" style='margin-left:50px;'>
                <?php if (isset($_GET['mail']) &&($_GET['mail'] != '')) { ?>
                <div id='info_envoi_lettre'>
                    Un test a été envoyé sur l'adresse email <?= $_GET['mail'] ?>
                </div>
                <?php } ?>
                <div>
                    Vous pouvez visualiser <a href="<?= $root_site.'02_medias/10_lettreinfo/'.$_GET['rep'].'/index.php' ?>" target="_blank" id="info_lien_lettre" style="text-decoration:underline">la lettre sur votre navigateur</a> 
                </div>
                <br/>
                <div>Si cette lettre vous convient, elle sera envoyée à <?= $nb_envois_lettre ?> abonnés sinon vous pouvez revenir en mode création sur la lettre</div>
                <br/>
            </div>
            
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="col-sm-12" style='text-align:center;'>
                        <a class="btn btn-warning" id="li_annulation_lettre" data-ref="<?= $NO ?>">Retour en mode création</a>
                        <a class="btn btn-primary" id="li_validation_lettre" data-ref="<?= $NO ?>">Valider l'envoi</a>
                    </div>
                    <br/>
                </div>
            </div>
        </div>
    </div> 
    <div id="li_fin" class='li_container' style="display: none;" name="li_bloc">
        <h2 class="h2">Envoi de la lettre</h2>
        <div class="row">
            <div class="col-sm-12" style='margin-left:50px;'>
                La programmation de l'envoi a bien été effectuée pour la nuit prochaine.<br/>
            </div>
        </div>
    </div> 
</div>

<?php
$contenu = ob_get_clean();
?>
