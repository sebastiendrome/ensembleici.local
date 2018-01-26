<span id='base_url' class='hide'><?= $root_site_prod ?></span>
<span id='current_url' class='hide'><?= $_SERVER['REQUEST_URI'] ?></span>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_infos">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Informations</h4>
      </div>
      <div class="modal-body" id="body_mod_infos">
        
      </div>
      <div class="modal-footer">
        <a class="btn btn-danger" data-dismiss="modal">Fermer</a>
      </div>
    </div>
  </div>
</div>

<!-- GESTION UTILISATEUR -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal_delete_user">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion utilisateur</h4>
      </div>
      <div class="modal-body" id="">
        Voulez-vous vraiment supprimer cet utilisateur ? 
      </div>
        <span id="id_delete_user" class="hide"></span>
      <div class="modal-footer">
        <a class="btn btn-primary" id="btn_valid_delete_user">Valider</a>
        <a class="btn btn-danger" data-dismiss="modal">Annuler</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_delete_abonne">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion Abonnés</h4>
      </div>
      <div class="modal-body" id="">
        Voulez-vous vraiment supprimer cet abonné ? 
      </div>
        <span id="id_delete_abonne" class="hide"></span>
      <div class="modal-footer">
        <a class="btn btn-primary" id="btn_valid_delete_abonne">Valider</a>
        <a class="btn btn-danger" data-dismiss="modal">Annuler</a>
      </div>
    </div>
  </div>
</div>

<!-- GESTION FICHE -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal_delete_fiche">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion fiches</h4>
      </div>
      <div class="modal-body" id="">
        Voulez-vous vraiment supprimer cette fiche ? 
      </div>
        <span id="id_delete_fiche" class="hide"></span>
        <span id="page_delete_fiche" class="hide"></span>
      <div class="modal-footer">
        <a class="btn btn-primary" id="btn_valid_delete_fiche">Valider</a>
        <a class="btn btn-danger" data-dismiss="modal">Annuler</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_delete_lettre">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion lettres d'infos</h4>
      </div>
      <div class="modal-body" id="">
          <b>Attention, cette action est irréversible et la lettre ne sera plus accessible par quiconque ! <br/>
          Voulez-vous vraiment supprimer cette lettre d'infos ? </b>
      </div>
        <span id="id_delete_lettre" class="hide"></span>
      <div class="modal-footer">
        <a class="btn btn-primary" id="btn_valid_delete_lettre">Valider</a>
        <a class="btn btn-danger" data-dismiss="modal">Annuler</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_boite_agenda">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion bloc agenda</h4>
      </div>
      <div class="modal-body" id="">
            <b>Pour chaque événement, cliquez sur le petit + pour l'ajouter à la lettre d'infos</b>
            <div id='content_boite_agenda'>
            </div>
      </div>
        <div style='clear:both'></div>
      <div class="modal-footer">
        <a class="btn btn-danger" data-dismiss="modal">Fermer</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_boite_annonces">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion bloc annonces</h4>
      </div>
      <div class="modal-body" id="">
            <b>Pour chaque annonce, cliquez sur le petit + pour l'ajouter à la lettre d'infos</b>
            <div id='content_boite_annonces'>
            </div>
      </div>
        <div style='clear:both'></div>
      <div class="modal-footer">
        <a class="btn btn-danger" data-dismiss="modal">Fermer</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_boite_search_structures">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion bloc répertoire</h4>
      </div>
      <div class="modal-body" id="">
            <b>Veuillez entrer un mot clé pour afficher les structures correspondantes. Seuls les 20 premiers résultats seront affichés donc les termes de recherche doivent être discriminants.</b>
            <form class="form-horizontal" role="form" style='margin-left:25px;'>
                <div class="form-group">
                    <div class="col-sm-12" style='text-align:center;'>
                        <label for="inp_key_structure" class="col-sm-3 control-label">Mots clés</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="inp_key_structure" value="">
                        </div>
                    </div>
                </div>
            </form>
      </div>
      <div class="modal-footer">
          <a class="btn btn-primary" id="btn_search_boite_etape_structures">Chercher</a>
        <a class="btn btn-danger" data-dismiss="modal">Fermer</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_boite_structures">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion bloc répertoire</h4>
      </div>
      <div class="modal-body" id="">
            <b>Pour chaque structure, cliquez sur le petit + pour l'ajouter à la lettre d'infos</b>
            <div id='content_boite_structures'>
            </div>
      </div>
        <div style='clear:both'></div>
      <div class="modal-footer">
        <a class="btn btn-danger" data-dismiss="modal">Fermer</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_ajout_partenaire">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion partenaires</h4>
      </div>
      <div class="modal-body" id="">
          <b>A l'aide de ce formulaire, vous pouvez ajouter un partenaire ou membre du collectif</b>
            <form class="form-horizontal" role="form" style='margin-left:25px;'>
                <div class="form-group">
                    <div class="col-sm-12" style='text-align:center;'>
                        <label for="" class="col-sm-3 control-label">* Type</label>
                        <div class="col-sm-6">
                            <select id='sel_type_partenaire'>
                                <option value='1'>Partenaire</option>
                                <option value ='2'>Membre du collectif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12" style='text-align:center;'>
                        <label for="inp_nom_partenaire" class="col-sm-3 control-label">* Nom</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="inp_nom_partenaire" value="" placeholder="Nom du partenaire ou membre du collectif">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12" style='text-align:center;'>
                        <label class="col-sm-3 control-label">* Image</label>
                        <div class="col-sm-6" id="plupload3">
                            <div id="browse3"><a class="btn btn-success">Charger le visuel</a></div>
                        </div>
                        <div id='progressgen3' class='col-sm-12' style='color:#790000; font-weight: bolder;'></div>
                        <div id='filelist3' class='hide'></div>
                        <div id='exist_image_name'></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12" style='text-align:center;'>
                        <label for="inp_site_partenaire" class="col-sm-3 control-label">Site</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="inp_site_partenaire" value="" placeholder="Site du partenaire ou membre du collectif">
                        </div>
                    </div>
                </div>
            </form>
      </div>
      <div class="modal-footer">
          <a class="btn btn-primary" id="btn_valid_add_partenaire">Ajouter</a>
        <a class="btn btn-danger" data-dismiss="modal">Fermer</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_loader">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body" id="">
          <div id='ajaxload'></div>
      </div>
        <div style='text-align: center; font-size: 16px; font-weight: bolder; margin-bottom: 20px;'>
            Veuillez patienter pendant la mise à jour
        </div>
        <div style='clear:both'></div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_delete_diapo">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion diaporama</h4>
      </div>
      <div class="modal-body" id="">
          <b>Souhaitez vous réellement supprimer cette image du diaporama en haut du site ?</b>
      </div>
        <span id="id_delete_diapo" class="hide"></span>
      <div class="modal-footer">
        <a class="btn btn-primary" id="btn_valid_delete_diapo">Valider</a>
        <a class="btn btn-danger" data-dismiss="modal">Annuler</a>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_delete_publicite">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion des publicités</h4>
      </div>
      <div class="modal-body" id="">
          <b>Souhaitez vous réellement supprimer cette publicité du site ?</b>
      </div>
        <span id="id_delete_publicite" class="hide"></span>
      <div class="modal-footer">
        <a class="btn btn-primary" id="btn_valid_delete_publicite">Valider</a>
        <a class="btn btn-danger" data-dismiss="modal">Annuler</a>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_super_admin">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Accès Super Admin</h4>
      </div>
      <div class="modal-body" id="">
          <b>Pour accéder au compte Super Admin, veuillez saisir le mot de passe ci-dessous</b>
          <input type="password" id="pass_sadmin" value="" />
      </div>
      <div class="modal-footer">
        <a class="btn btn-primary" id="btn_valid_superadmin">Valider</a>
        <a class="btn btn-danger" data-dismiss="modal">Annuler</a>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_delete_ville">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion territoire</h4>
      </div>
      <div class="modal-body" id="">
          Voulez-vous vraiment supprimer cette ville du territoire ? 
      </div>
        <span id="id_delete_ville" class="hide"></span>
      <div class="modal-footer">
        <a class="btn btn-primary" id="btn_valid_delete_ville">Valider</a>
        <a class="btn btn-danger" data-dismiss="modal">Annuler</a>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_add_territoire">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestion territoires</h4>
      </div>
      <div class="modal-body" id="">
          <div id="add_territoire_body1">
              <b>Pour ajouter un territoire, veuillez saisir les informations ci-dessous</b>
            <form class="form-horizontal" role="form" style='margin-left:25px;'>
                <div class="form-group">
                      <div class="col-sm-12" style='text-align:center;'>
                          <label class="col-sm-5 control-label">* Nom</label>
                          <div class="col-sm-6">
                              <input type="text" class="form-control" id="inp_add_territoire_nom" value="" placeholder="Nom du territoire">
                          </div>
                      </div>
                </div>
                <div class="form-group">
                      <div class="col-sm-12" style='text-align:center;'>
                          <label class="col-sm-5 control-label">* Adresse email administrateur</label>
                          <div class="col-sm-6">
                              <input type="text" class="form-control" id="inp_add_territoire_email" value="" placeholder="Adresse email admin">
                          </div>
                      </div>
                </div>
                <div class="form-group">
                      <div class="col-sm-12" style='text-align:center;'>
                          <label class="col-sm-5 control-label">* Code postal de la première commune à ajouter</label>
                          <div class="col-sm-6">
                              <input type="text" class="form-control" id="inp_add_territoire_code" value="" placeholder="Code postal">
                          </div>
                      </div>
                </div>
                <div class="form-group">
                      <div class="col-sm-12" style='text-align:center;'>
                          <label class="col-sm-5 control-label">* Nom du bassin de vie affiché dans la newsletter</label>
                          <div class="col-sm-6">
                              <input type="text" class="form-control" id="inp_add_territoire_pays" value="" placeholder="Bassin de vie">
                          </div>
                      </div>
                  </div>
            </form>
          </div>
          <div id="add_territoire_body2" style="display: none;">
              <b>Sélectionnez la commune principale du territoire</b>
              <div id="add_territoire_liste">
              </div>
          </div>
      </div>
      <div class="modal-footer">
          <div id="add_territoire_footer1">
                <a class="btn btn-success" id="btn_add_territoire_next">Poursuivre</a>
                <a class="btn btn-danger" data-dismiss="modal">Annuler</a>
          </div>
          <div id="add_territoire_footer2" style="display: none;">
                <a class="btn btn-primary" id="btn_valid_add_territoire">Valider</a>
                <a class="btn btn-danger" data-dismiss="modal">Annuler</a>
          </div>
      </div>
    </div>
  </div>
</div>