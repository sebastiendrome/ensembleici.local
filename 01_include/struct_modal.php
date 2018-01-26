<span id='base_url' class='hide'><?= $root_site_prod ?></span>
<span class='hide' id='reinit_mdp'><?= (isset($_REQUEST['reinit_mdp'])) ? $_REQUEST['reinit_mdp'] : '' ?></span>
<span id='user_id' class='hide'><?= (isset($_SESSION['utilisateur']['no'])) ? $_SESSION['utilisateur']['no'] : '' ?></span>
<span id='affiche_newsletter' class='hide'><?= (isset($_REQUEST['t']) && ($_REQUEST['t'] == 'abonnez_vous')) ? '1' : '0' ?></span>
<span class="hide" id="url_don"><?= isset($_SESSION['utilisateur']['url_don']) ? $_SESSION['utilisateur']['url_don'] : '' ?></span>
<span class="hide" id="url_adhesion"><?= isset($_SESSION['utilisateur']['url_adhesion']) ? $_SESSION['utilisateur']['url_adhesion'] : '' ?></span>
<span class="hide" id="cookie_bandeau"><?= isset($_COOKIE['cookie_bandeau']) ? 1 : 0 ?></span>

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
        <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal_pass_oublie">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Gestion du mot de passe</h4>
            </div>
            <div class="modal-body" id="body_mod_pass_oublie">
                Afin de réinitialiser votre mot de passe, veuillez saisir l'adresse email de votre compte. Nous vous enverrons alors un email contenant un lien 
                sur lequel vous devrez cliquer pour modifier votre mot de passe. <br/><br/>
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="pass_oublie_email" class="col-sm-3 control-label">Adresse email</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="pass_oublie_email" placeholder='Veuillez saisir votre adresse email'>
                        </div>
                    </div>     
                </form>
            </div>
            <div id="message_pass_oublie"></div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_valid_pass_oublie">Recevoir le mail de réinitialisation</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_pass_reinit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Gestion du mot de passe</h4>
            </div>
            <div class="modal-body" id="body_mod_pass_oublie">
                Pour terminer le processus de réinitialisation, merci de bien vouloir saisir votre nouveau mot de passe <br/><br/>
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="new_pass_reinit" class="col-sm-5 control-label">Nouveau mot de passe</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="new_pass_reinit" placeholder='Veuillez saisir votre nouveau mot de passe'>
                        </div>
                    </div>     
                </form>
            </div>
            <div id="message_pass_reinit"></div>
            <span class='hide' id='email_reinit_pass'></span>
            <span class='hide' id='id_reinit_pass'></span>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_valid_new_pass">Valider</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_update_infos_pers">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Gestion de vos informations personnelles</h4>
            </div>
            <div class="modal-body" id="body_update_infos_pers">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="update_infos_pseudo" class="col-sm-3 control-label">Pseudo</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="update_infos_pseudo">
                        </div>
                    </div> 
                    <div class="form-group">
                        <label for="update_infos_mail" class="col-sm-3 control-label">Adresse email</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="update_infos_mail">
                        </div>
                    </div> 
                    <div class="form-group">
                        <label for="update_infos_mail" class="col-sm-3 control-label">Mot de passe</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="update_infos_password">
                        </div>
                    </div> 
                    <div class="form-group">
                        <label for="update_infos_ville" class="col-sm-3 control-label">Ville</label>
                        <div class="col-sm-9">
                            <!--<input type="text" class="form-control" id="update_infos_mail">-->
                            <input type="text" id="update_infos_ville" title="" value="" class="recherche_ville2" />
                            <input type="hidden" name="update_infos_noville" id="BDDno_ville_update" value="" /><br/>
                            <div id="recherche_ville_liste2"><div></div></div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <div class="col-sm-2">
                        </div>
                        <div class="col-sm-10">
                            <label>
                                <input type="checkbox" id="update_infos_news"> Je souhaite recevoir la newsletter
                            </label>
                        </div>
                    </div> 
                </form>
            </div>
            <div id="message_update_infos_pers"></div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_valid_update_infos_pers">Modifier mes informations</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_add_partenaire">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Ajout d'un partenaire</h4>
            </div>
            <div class="modal-body" id="">
                <form class="form-horizontal" role="form" id='form_add_part' action='' method='post' enctype='multipart/form-data'>
                    <div class="form-group">
                        <label for="" class="col-sm-5 control-label">* Nom</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="nom_new_part" placeholder='Nom du partenaire'>
                        </div>
                    </div>   
                    <div class="form-group">
                        <label for="" class="col-sm-5 control-label">Site</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="site_new_part" placeholder="Copier l'adresse du site avec http://">
                        </div>
                    </div>   
                    <div class="form-group">
                        <label for="" class="col-sm-5 control-label">* Logo</label>
                        <div class="col-sm-7">
                            <input type="file" name="logo_new_part" id="logo_new_part">
                            <input type="hidden" name="MAX_FILE_SIZE" value="100000">
                        </div>
                    </div>   
                </form>
                <div id='message_new_part' class='hide'></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_valid_new_part">Valider</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_signalement">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Signalement de contenu inapproprié</h4>
            </div>
            <div class="modal-body" id="">
                Pour signaler <span id="nom_signalement" style="font-weight: bolder; font-style: italic;"></span> comme inapproprié, veuillez en saisir le motif ci-dessous et cliquer sur le bouton signaler <br/>
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Motif du signalement</label>
                        <div class="col-sm-9">
                            <textarea id="motif_signalement" style="width: 400px; height: 100px;" placeholder="Veuillez saisir le motif du signalement"></textarea>
                        </div>
                    </div>     
                </form>
            </div>
            <span id="id_signalement" class="hide"></span>
            <span id="page_signalement" class="hide"></span>
            <div id="message_signalement"></div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_valid_signalement">Signaler</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_duplicate_fiche">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Informations</h4>
      </div>
      <div class="modal-body" id="body_mod_infos">
        <b>Vous allez dupliquer la fiche et une nouvelle fiche avec des informations identiques va vous être proposée.</b><br/>
        Vous pourrez alors modifier chacune des informations puis publier la fiche.<br/><br/>
        La fiche dupliquée ne peut pas être annulée mais pour la rendre visible, vous devrez impérativement la publier. Si ce n'est pas le cas, vous 
        la retrouverez en brouillon dans votre espace personnel 
          <span style="display: none;" id="duplicate_fiche_ref"></span>
          <span style="display: none;" id="duplicate_fiche_page"></span>
          <span style="display: none;" id="duplicate_fiche_url"></span>
      </div>
      <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btn_valid_duplicate_fiche">Dupliquer la fiche</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal_soutien">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Soutien à l'association</h4>
      </div>
      <div class="modal-body">
        Félicitations ! Vous venez de prendre part à un média participatif ! Vous voilà désormais acteur de votre vie locale !<br/><br/>
        Ensemble Ici est une initiative citoyenne, entièrement indépendante, qui ne peut vivre qu'en groupant nos énergies mais aussi nos finances.<br/><br/>
        N'oubliez donc pas d'adhérer à l'association ou de faire un don pour que cet outil alternatif perdure et se développe.<br/>
        Si chaque personne qui lit ce message donnait 1€/mois, nous pourrions être autonome techniquement, en communication et commencer à autofinancer les 
        animations locales d’Ensemble Ici, la coordination et l’essaimage pour qu’Ensemble Ici existe dans d’autres territoires ruraux de France.<br/><br/>
        Merci à vous.
      </div>
      <div class="modal-footer">
          Ensemble Ici continue demain grâce à <a href='<?= $_SESSION['utilisateur']['url_don'] ?>' target='_blank' class='btn btn-success'>mon soutien</a> ​ 
      </div>
    </div>
  </div>
</div>