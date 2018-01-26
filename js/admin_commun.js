$(function() {
    // GESTION UTILISATEURS
    var date_min = new Date();
    $( "#BDDdate_debut" ).datepicker({ minDate: date_min, dateFormat: "dd/mm/yy"});
    $( "#BDDdate_fin" ).datepicker({ minDate: date_min, dateFormat: "dd/mm/yy"});
    $( "#li_inp_date_debut" ).datepicker({ minDate: date_min, dateFormat: "dd/mm/yy"});
    $('#date_fin_pdf').datepicker({ minDate: date_min, dateFormat: "dd/mm/yy"});
    $('#publicite_debut').datepicker({ minDate: date_min, dateFormat: "dd/mm/yy"});
    $('#publicite_fin').datepicker({ minDate: date_min, dateFormat: "dd/mm/yy"});
    $('input[name=btn_del_utilsateur]').on('click', function(e) {
        e.preventDefault();
        if ($(this).data('possible') == 0) {
            $('#body_mod_infos').html("Vous ne pouvez pas supprimer un utilisateur ayant créé des fiches");
            $('#modal_infos').modal(); return;
        }
        $('#id_delete_user').html($(this).data('ref'));
        $('#modal_delete_user').modal(); return;
    });
    
    $('#btn_valid_delete_user').on('click', function(e) {
        e.preventDefault();
        var post = {
            id : $('#id_delete_user').html()
        };

        var url = $('#base_url').html() + 'gestion/ajax/utilisateurs/delete_user.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $(location).attr('href', $(location).attr('href'));
            }
            else {
                $('#modal_delete_user').modal('hide');
                $('#body_mod_infos').html("Impossible de supprimer l'utilisateur");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('input[name=btn_active_utilisateur]').on('click', function(e) {
        e.preventDefault();
        var etat = $(this).data('etat');
        var id = $(this).data('ref');
        var post = {
            id : $(this).data('ref'), 
            etat : $(this).data('etat')
        };

        var url = $('#base_url').html() + 'gestion/ajax/utilisateurs/active_user.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                if (etat == 1) {
                    $('#' + id).removeClass('actif');
                    $('#' + id).data('etat', '0');
                }
                else {
                    $('#' + id).addClass('actif');
                    $('#' + id).data('etat', '1');
                }
            }
            else {
                $('#body_mod_infos').html("Impossible de modifier l'utilisateur");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#btn_add_user').on('click', function(e) {
        e.preventDefault();
        if ($('#user_email').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir une adresse email pour ajouter un utilisateur");
            $('#modal_infos').modal(); return;
        }
        if ($('#BDDno_ville').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir une ville pour ajouter un utilisateur");
            $('#modal_infos').modal(); return;
        }
        if ($('#user_password').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir un mot de passe pour ajouter un utilisateur");
            $('#modal_infos').modal(); return;
        }
        if ($('#user_password2').val() == '') {
            $('#body_mod_infos').html("Vous devez confirmer le mot de passe pour ajouter un utilisateur");
            $('#modal_infos').modal(); return;
        }
        if ($('#user_password').val() != $('#user_password2').val()) {
            $('#body_mod_infos').html("Les mots de passe ne sont pas identiques");
            $('#modal_infos').modal(); return;
        }
        
        var post = {
            email : $('#user_email').val(), 
            ville : $('#BDDno_ville').val(), 
            pass : $('#user_password').val(), 
            news : $('#sel_user_lettre option:selected').val(),
            droits : $('#sel_user_droits option:selected').val()
        };

        var url = $('#base_url').html() + 'gestion/ajax/utilisateurs/add_user.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#body_mod_infos').html("L'utilisateur a bien été ajouté");
                $('#modal_infos').modal();
            } 
            else {
                if (reponse.code == 2) {
                    $('#body_mod_infos').html("L'adresse email saisie est incorrecte");
                    $('#modal_infos').modal();
                }
                else {
                    $('#body_mod_infos').html("Une erreur est survenue lors de l'ajout");
                    $('#modal_infos').modal();
                }
            }
        }, 'json');
    });
    
    $('#btn_update_user').on('click', function(e) {
        e.preventDefault();
        if ($('#user_email').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir une adresse email pour modifier un utilisateur");
            $('#modal_infos').modal(); return;
        }
        if ($('#BDDno_ville').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir une ville pour modifier un utilisateur");
            $('#modal_infos').modal(); return;
        }
        var post = {
            email : $('#user_email').val(), 
            ville : $('#BDDno_ville').val(), 
            news : $('#sel_user_lettre option:selected').val(),
            droits : $('#sel_user_droits option:selected').val(), 
            no : $('#user_no').html()
        };

        var url = $('#base_url').html() + 'gestion/ajax/utilisateurs/update_user.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#body_mod_infos').html("L'utilisateur a bien été modifié");
                $('#modal_infos').modal();
            } 
            else {
                if (reponse.code == 2) {
                    $('#body_mod_infos').html("L'adresse email saisie est incorrecte");
                    $('#modal_infos').modal();
                }
                else {
                    $('#body_mod_infos').html("Une erreur est survenue lors de la modification");
                    $('#modal_infos').modal();
                }
            }
        }, 'json');
    });
    
    $('#btn_filtre_user').on('click', function(e) {
        e.preventDefault();
        var url = $('#base_url').html() + 'gestion/?page=utilisateur&no=';
        if ($('#filtre_email').val() != '') {
            url += "&mail=" + $('#filtre_email').val(); 
        }
        if ($('#filtre_ville').val() != '') {
            url += "&ville=" + $('#filtre_ville').val();
        }
        $(location).attr('href', url);
    });
    
    $('#sel_tri_user').on('change', function(e) {
        e.preventDefault();
        var url = $('#base_url').html() + 'gestion/?page=utilisateur&no=';
        url += '&tri=' + $('#sel_tri_user option:selected').val();
        $(location).attr('href', url);
    });
    
    
    // GESTION DES ABONNES
    $('input[name=btn_del_abonne]').on('click', function(e) {
        e.preventDefault();
        $('#id_delete_abonne').html($(this).data('ref'));
        $('#modal_delete_abonne').modal(); return;
    });
    
    $('#btn_valid_delete_abonne').on('click', function(e) {
        e.preventDefault();
        var post = {
            id : $('#id_delete_abonne').html()
        };

        var url = $('#base_url').html() + 'gestion/ajax/abonnes/delete_user.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $(location).attr('href', $(location).attr('href'));
            }
            else {
                $('#modal_delete_abonne').modal('hide');
                $('#body_mod_infos').html("Impossible de supprimer l'abonné");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('input[name=btn_active_abonne]').on('click', function(e) {
        e.preventDefault();
        var etat = $(this).data('etat');
        var id = $(this).data('ref');
        var post = {
            id : $(this).data('ref'), 
            etat : $(this).data('etat')
        };

        var url = $('#base_url').html() + 'gestion/ajax/abonnes/active_user.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                if (etat == 1) {
                    $('#' + id).removeClass('actif');
                    $('#' + id).data('etat', '0');
                }
                else {
                    $('#' + id).addClass('actif');
                    $('#' + id).data('etat', '1');
                }
            }
            else {
                $('#body_mod_infos').html("Impossible de modifier l'abonné");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#btn_filtre_abonne').on('click', function(e) {
        e.preventDefault();
        var url = $('#base_url').html() + 'gestion/?page=abonnes&no=';
        if ($('#abonnes_filtre_email').val() != '') {
            url += "&mail=" + $('#abonnes_filtre_email').val(); 
        }
        if ($('#abonnes_filtre_ville').val() != '') {
            url += "&ville=" + $('#abonnes_filtre_ville').val();
        }
        $(location).attr('href', url);
    });
    
    $('#sel_tri_abonne').on('change', function(e) {
        e.preventDefault();
        var url = $('#base_url').html() + 'gestion/?page=abonnes&no=';
        url += '&tri=' + $('#sel_tri_abonne option:selected').val();
        $(location).attr('href', url);
    });
    
    $('#btn_add_abonne').on('click', function(e) {
        e.preventDefault();
        if ($('#user_email').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir une adresse email pour ajouter un abonné");
            $('#modal_infos').modal(); return;
        }
        if ($('#BDDno_ville').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir une ville pour ajouter un abonné");
            $('#modal_infos').modal(); return;
        }
        
        var post = {
            email : $('#user_email').val(), 
            ville : $('#BDDno_ville').val()
        };

        var url = $('#base_url').html() + 'gestion/ajax/abonnes/add_user.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#body_mod_infos').html("L'utilisateur a bien été ajouté");
                $('#modal_infos').modal();
            } 
            else {
                if (reponse.code == 2) {
                    $('#body_mod_infos').html("L'adresse email saisie est incorrecte");
                    $('#modal_infos').modal();
                }
                else {
                    $('#body_mod_infos').html("Une erreur est survenue lors de l'ajout");
                    $('#modal_infos').modal();
                }
            }
        }, 'json');
    });
    
    $('#btn_update_abonne').on('click', function(e) {
        e.preventDefault();
        if ($('#user_email').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir une adresse email pour modifier un utilisateur");
            $('#modal_infos').modal(); return;
        }
        if ($('#BDDno_ville').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir une ville pour modifier un utilisateur");
            $('#modal_infos').modal(); return;
        }
        var post = {
            email : $('#user_email').val(), 
            ville : $('#BDDno_ville').val(), 
            no : $('#user_no').html()
        };

        var url = $('#base_url').html() + 'gestion/ajax/abonnes/update_user.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#body_mod_infos').html("L'utilisateur a bien été modifié");
                $('#modal_infos').modal();
            } 
            else {
                if (reponse.code == 2) {
                    $('#body_mod_infos').html("L'adresse email saisie est incorrecte");
                    $('#modal_infos').modal();
                }
                else {
                    $('#body_mod_infos').html("Une erreur est survenue lors de la modification");
                    $('#modal_infos').modal();
                }
            }
        }, 'json');
    });
    
    // GESTION DES TAGS
    $('#btn_filtre_tag').on('click', function(e) {
        e.preventDefault();
        var url = $('#base_url').html() + 'gestion/?page=tag&no=';
        if ($('#filtre_titre').val() != '') {
            url += "&titre=" + $('#filtre_titre').val(); 
        }
        if ($('#sel_filtre_vie option:selected').val() != '') {
            url += "&ville=" + $('#sel_filtre_vie option:selected').val();
        }
        $(location).attr('href', url);
    });
    
    $('#btn_add_tag').on('click', function(e) {
        e.preventDefault();
        if ($('#libelle_tag').val() == '') {
            $('#body_mod_infos').html("Le libellé du tag ne peut pas être vide");
            $('#modal_infos').modal(); return;
        }
        if ($('#type_tag option:selected').val() == -1) {
            $('#body_mod_infos').html("Le type du tag doit être sélectionné");
            $('#modal_infos').modal(); return;
        }
        var post = {
            libelle : $('#libelle_tag').val(), 
            type : $('#type_tag option:selected').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/tags/add_tag.php';
        $.post(url, post, function(reponse){
            $('#body_mod_infos').html("Le tag a bien été ajouté");
            $('#modal_infos').modal();
        }, 'json');
    });
    
    $('#btn_update_tag').on('click', function(e) {
        e.preventDefault();
        if ($('#libelle_tag').val() == '') {
            $('#body_mod_infos').html("Le libellé du tag ne peut pas être vide");
            $('#modal_infos').modal(); return;
        }
        var post = {
            libelle : $('#libelle_tag').val(), 
            type : $('#type_tag option:selected').val(), 
            no : $('#tag_no').html()
        };
        var url = $('#base_url').html() + 'gestion/ajax/tags/update_tag.php';
        $.post(url, post, function(reponse){
            $('#body_mod_infos').html("Le tag a bien été modifié");
            $('#modal_infos').modal();
        }, 'json');
    });
    
    $('input[name=btn_delete_tag]').on('click', function(e) {
        e.preventDefault();
        if ($(this).data('ref') == -1) {
            $('#body_mod_infos').html("Vous ne pouvez pas supprimer un tag lié à des fiches");
            $('#modal_infos').modal(); return;
        }

        var post = {
            no : $(this).data('ref')
        };
        var url = $('#base_url').html() + 'gestion/ajax/tags/delete_tag.php';
        $.post(url, post, function(reponse){
            $(location).attr('href', $(location).attr('href'));
        }, 'json');
    });
    
    $('#user_ville').on('keyup', function(e) {
        e.preventDefault();
        rechercher_ville(this);
    });
    
    // GESTION DES BLOCS
    $('#btn_update_bloc').on('click', function(e) {
        e.preventDefault();
        tinymce.triggerSave(true, true);
        
        var post = {
            no : $('#bloc_no').html(), 
            titre : $('#titre_bloc').val(), 
            contenu : $('#contenu_bloc').val()
        };

        var url = $('#base_url').html() + 'gestion/ajax/blocs/update_bloc.php';
        $.post(url, post, function(reponse){
             $('#body_mod_infos').html("Le bloc a bien été mis à jour");
            $('#modal_infos').modal();
        }, 'json');
        
    });
    
    
    // GESTION DES STATUTS STRUCTURES
    $('#btn_add_statut').on('click', function(e) {
        e.preventDefault();
        if ($('#libelle_statut').val() == '') {
            $('#body_mod_infos').html("Le libellé du statut ne peut pas être vide");
            $('#modal_infos').modal(); return;
        }
        var post = {
            libelle : $('#libelle_statut').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/statuts/add_statut.php';
        $.post(url, post, function(reponse){
            $('#body_mod_infos').html("Le statut a bien été ajouté");
            $('#modal_infos').modal();
        }, 'json');
    });
    
    $('#btn_update_statut').on('click', function(e) {
        e.preventDefault();
        if ($('#libelle_statut').val() == '') {
            $('#body_mod_infos').html("Le libellé du statut ne peut pas être vide");
            $('#modal_infos').modal(); return;
        }
        var post = {
            no : $('#statut_no').html(),
            libelle : $('#libelle_statut').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/statuts/update_statut.php';
        $.post(url, post, function(reponse){
            $('#body_mod_infos').html("Le statut a bien été modifié");
            $('#modal_infos').modal();
        }, 'json');
    });
    
    $('input[name=btn_delete_statut]').on('click', function(e) {
        e.preventDefault();
        if ($(this).data('ref') == -1) {
            $('#body_mod_infos').html("Vous ne pouvez pas supprimer un statut lié à des structures");
            $('#modal_infos').modal(); return;
        }

        var post = {
            no : $(this).data('ref')
        };
        var url = $('#base_url').html() + 'gestion/ajax/statuts/delete_statut.php';
        $.post(url, post, function(reponse){
            $(location).attr('href', $(location).attr('href'));
        }, 'json');
    });
    
    // GESTION DES GENRES D'EVENEMENTS
    $('#btn_add_genre').on('click', function(e) {
        e.preventDefault();
        if ($('#libelle_genre').val() == '') {
            $('#body_mod_infos').html("Le libellé du genre ne peut pas être vide");
            $('#modal_infos').modal(); return;
        }
        var post = {
            libelle : $('#libelle_genre').val(), 
            type : $('#type_genre option:selected').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/genres/add_genre.php';
        $.post(url, post, function(reponse){
            $('#body_mod_infos').html("Le genre a bien été ajouté");
            $('#modal_infos').modal();
        }, 'json');
    });
    
    $('#btn_update_genre').on('click', function(e) {
        e.preventDefault();
        if ($('#libelle_genre').val() == '') {
            $('#body_mod_infos').html("Le libellé du statut ne peut pas être vide");
            $('#modal_infos').modal(); return;
        }
        var post = {
            no : $('#genre_no').html(),
            libelle : $('#libelle_genre').val(), 
            type : $('#type_genre option:selected').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/genres/update_genre.php';
        $.post(url, post, function(reponse){
            $('#body_mod_infos').html("Le genre a bien été modifié");
            $('#modal_infos').modal();
        }, 'json');
    });
    
    $('input[name=btn_delete_genre]').on('click', function(e) {
        e.preventDefault();
        if ($(this).data('ref') == -1) {
            $('#body_mod_infos').html("Vous ne pouvez pas supprimer un genre lié à des fiches");
            $('#modal_infos').modal(); return;
        }

        var post = {
            no : $(this).data('ref')
        };
        var url = $('#base_url').html() + 'gestion/ajax/genres/delete_genre.php';
        $.post(url, post, function(reponse){
            $(location).attr('href', $(location).attr('href'));
        }, 'json');
    });
    
    // GESTION DES PUBLICITES
    $('input[name=btn_delete_publicite]').on('click', function(e) {
        e.preventDefault();
        $('#id_delete_publicite').html($(this).data('ref'));
        $('#modal_delete_publicite').modal('show');
    });
    
    $('#btn_valid_delete_publicite').on('click', function(e) {
        e.preventDefault();
        var post = {
            no : $('#id_delete_publicite').html()
        };
        var url = $('#base_url').html() + 'gestion/ajax/publicites/delete.php';
        $.post(url, post, function(reponse){
            $(location).attr('href', $(location).attr('href'));
        }, 'json');
    });
    
    $('input[name=rad_type_pub]').on('change', function(e) {
        e.preventDefault();
        var option = $('input[name=rad_type_pub]:checked').val();
        if (option == 1) {
            $('#li_format_carre').show(); $('#li_format_rectangle').hide(); $('#li_format_carre2').hide();
            $('#plupload2bis').hide(); $('#plupload2').show(); $('#plupload2ter').hide();
            $('#filelist2').html(''); $('#filelist2bis').html(''); $('#filelist2ter').html(''); $('#exist_image_name').html('');
        }
        else {
            if (option == 2) {
                $('#li_format_rectangle').show(); $('#li_format_carre').hide(); $('#li_format_carre2').hide();
                $('#plupload2').hide(); $('#plupload2bis').show(); $('#plupload2ter').hide();
                $('#filelist2').html(''); $('#filelist2bis').html(''); $('#filelist2ter').html(''); $('#exist_image_name').html('');
            }
            else {
                $('#li_format_carre2').show(); $('#li_format_carre').hide();$('#li_format_rectangle').hide();
                $('#plupload2').hide(); $('#plupload2bis').hide(); $('#plupload2ter').show();
                $('#filelist2').html(''); $('#filelist2bis').html(''); $('#filelist2ter').html(''); $('#exist_image_name').html('');
            }
        }
    });
    
    $('#btn_add_publicite').on('click', function(e) {
        e.preventDefault();
        if ($('#publicite_titre').val() == '') {
            $('#body_mod_infos').html("Le titre de la publicité doit être renseigné.");
            $('#modal_infos').modal(); return;
        }
        if ($('#publicite_debut').val() == '') {
            $('#body_mod_infos').html("La date de début de validité de la publicité doit être renseignée.");
            $('#modal_infos').modal(); return;
        }
        if ($('#publicite_fin').val() == '') {
            $('#body_mod_infos').html("La date de fin de validité de la publicité doit être renseignée.");
            $('#modal_infos').modal(); return;
        }
        if (($('#filelist2').html() == '') && ($('#filelist2bis').html() == '') && ($('#filelist2ter').html() == '')) {
            $('#body_mod_infos').html("Pour ajouter la publicité, une image doit être ajoutée. ");
            $('#modal_infos').modal(); return;
        }
        else {
            var fichier = ''; 
            if ($('#filelist2').html() != '') {
                fichier = $('#filelist2').html();
            }
            else {
                if ($('#filelist2bis').html() != '') {
                    fichier = $('#filelist2bis').html();
                }
                else {
                    fichier = $('#filelist2ter').html();
                }
            }
        }
        var post = {
            titre : $('#publicite_titre').val(), 
            debut : $('#publicite_debut').val(), 
            fin : $('#publicite_fin').val(), 
            fichier : fichier, 
            type : $('input[name=rad_type_pub]:checked').val(), 
            site : $('#publicite_site').val(), 
            vente : $('#publicite_vente option:selected').val(), 
            page : $('#publicite_page option:selected').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/publicites/add.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#body_mod_infos').html("La publicité a bien été ajoutée");
                $('#modal_infos').modal();
            }
            else {
                $('#body_mod_infos').html("La publicité n'a pa pu être ajoutée");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#btn_update_publicite').on('click', function(e) {
        e.preventDefault();
        if ($('#publicite_titre').val() == '') {
            $('#body_mod_infos').html("Le titre de la publicité doit être renseigné.");
            $('#modal_infos').modal(); return;
        }
        if ($('#publicite_debut').val() == '') {
            $('#body_mod_infos').html("La date de début de validité de la publicité doit être renseignée.");
            $('#modal_infos').modal(); return;
        }
        if ($('#publicite_fin').val() == '') {
            $('#body_mod_infos').html("La date de fin de validité de la publicité doit être renseignée.");
            $('#modal_infos').modal(); return;
        }
        var post = {
            no : $('#publicite_no').html(), 
            titre : $('#publicite_titre').val(), 
            debut : $('#publicite_debut').val(), 
            fin : $('#publicite_fin').val(), 
            site : $('#publicite_site').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/publicites/update.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#body_mod_infos').html("La publicité a bien été modifiée");
                $('#modal_infos').modal();
            }
            else {
                $('#body_mod_infos').html("La publicité n'a pa pu être modifiée");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    // GESTION PARAMETRE TERRITOIRE
    $('input[name=btn_del_diaporama]').on('click', function(e) {
        e.preventDefault();
        $('#id_delete_diapo').html($(this).data('ref'));
        $('#modal_delete_diapo').modal('show');
        
    });
    
    $('#btn_valid_delete_diapo').on('click', function(e) {
        e.preventDefault();
        var post = {
            id : $('#id_delete_diapo').html()
        };
        var url = $('#base_url').html() + 'gestion/ajax/diaporamas/delete.php';
        $.post(url, post, function(reponse){
            $(location).attr('href', $(location).attr('href'));
        }, 'json');
    });
    
    $('#btn_insert_diapo').on('click', function(e) {
        e.preventDefault();
        var post = {
            nom : $('#filelist4').html()
        };
        var url = $('#base_url').html() + 'gestion/ajax/diaporamas/add.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $(location).attr('href', $(location).attr('href'));
            }
            else {
                $('#body_mod_infos').html("Impossible d'ajouter l'image");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#btn_update_url_territoire').on('click', function(e) {
        e.preventDefault();
        if ($('#territoire_facebook').val() == '') {
            $('#body_mod_infos').html("L'url facebook ne peut pas être vide");
            $('#modal_infos').modal(); return;
        }
        if ($('#territoire_newsletter').val() == '') {
            $('#body_mod_infos').html("L'adresse mail d'envoi de la newsletter ne peut pas être vide");
            $('#modal_infos').modal(); return;
        }
        var post = {
            facebook : $('#territoire_facebook').val(), 
            newsletter : $('#territoire_newsletter').val(), 
            code_ua : $('#territoire_code_ua').val()
        };

        var url = $('#base_url').html() + 'gestion/ajax/territoires/update_url.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $(location).attr('href', $(location).attr('href'));
            }
            else {
                $('#body_mod_infos').html("Impossible de modifier les paramètres");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    // GESTION DES FICHES
    $('input[name=btn_active_fiche]').on('click', function(e) {
        e.preventDefault();
        var etat = $(this).data('etat');
        var id = $(this).data('ref');
        var page = $(this).data('page');
        var post = {
            id : $(this).data('ref'), 
            etat : $(this).data('etat'), 
            page : page
        };

        var url = $('#base_url').html() + 'gestion/ajax/fiches/active_fiche.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                if (etat == 1) {
                    $('#' + id).removeClass('actif');
                    $('#' + id).data('etat', '0');
                }
                else {
                    $('#' + id).addClass('actif');
                    $('#' + id).data('etat', '1');
                }
            }
            else {
                $('#body_mod_infos').html("Impossible de modifier la fiche");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('input[name=btn_del_fiche]').on('click', function(e) {
        e.preventDefault();
        $('#id_delete_fiche').html($(this).data('ref'));
        $('#page_delete_fiche').html($(this).data('page'));
        $('#modal_delete_fiche').modal(); return;
    });
    
    $('#btn_valid_delete_fiche').on('click', function(e) {
        e.preventDefault();
        var post = {
            id : $('#id_delete_fiche').html(), 
            page : $('#page_delete_fiche').html()
        };

        var url = $('#base_url').html() + 'gestion/ajax/fiches/delete_fiche.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $(location).attr('href', $(location).attr('href'));
            }
            else {
                $('#modal_delete_fiche').modal('hide');
                $('#body_mod_infos').html("Impossible de supprimer la fiche");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('a[name=item_sous_menu_admin]').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('ref');
        var hauteur = $('#' + id).offset().top - 10;
        $('html,body').animate({scrollTop:hauteur},1000);
    });
    
    $('#btn_add_fiche').on('click', function(e) {
        e.preventDefault();
        var page = $(this).data('page'); 
        if ((page == 'structure') && ($('#BDDnom').val() == '')) {
            $('#body_mod_infos').html("Vous devez saisir un nom de structure");
            $('#modal_infos').modal(); return;
        } else {
            if ($('#BDDtitre').val() == '') {
                $('#body_mod_infos').html("Vous devez saisir un titre pour la fiche");
                $('#modal_infos').modal(); return;
            }
        }
        if ($('#BDDno_genre option:selected').val() == '0') {
            $('#body_mod_infos').html("Vous devez saisir un genre pour votre fiche");
            $('#modal_infos').modal(); return;
        }
        if ($('#BDDdate_debut').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir une date de début pour votre fiche");
            $('#modal_infos').modal(); return;
        }
        if ($('#BDDno_ville').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir une ville pour la fiche");
            $('#modal_infos').modal(); return;
        }
        var afficher_signature = 0;
        if ($('#BDDafficher_signature').is(':checked') ) {
            afficher_signature = 1;
        }
        
        var post = {
            page : page, 
            user : $('#BDDno_utilisateur_creation').val(), 
            image : $('#filelist').html(), 
            contact : $('#BDDno_contact').html(),
            afficher_signature : afficher_signature
        };
        tinymce.triggerSave(true, true);
        
        if ($('#no_fiches_liees').html() != '') {
            post['liaisons']  = $('#no_fiches_liees').html();
        }

        $('#contenu_fiche input').each(function() {
            if (($(this).val() != '') && ($(this).attr('id') != 'undefined') && ($(this).attr('type') == 'text')) {
                var champ = $(this).attr('id').replace("BDD","");
                var value = $(this).val();
                
                if (champ != 'sous_titre') {
                    post[champ]= value;
                }
                else {
                    post['soustitre']= value;
                }
            } else {
                if ($(this).attr('id') == 'BDDno_ville') {
                    post['no_ville'] = $(this).val();
                }
            }
        });
        
        $('#contenu_fiche select').each(function() {
            var champ = $(this).attr('id').replace("BDD","");
            var value = $('#' + $(this).attr('id') + ' option:selected').val();
            post[champ]= value;
        });
        
        $('#contenu_fiche textarea').each(function() {
            if ($(this).val() != '') {
                var champ = $(this).attr('id').replace("BDD","");
                var value = $(this).val();
                post[champ]= value;
            }
        });
        
        if ($('#tags_select').html() != '') {
            var tags = ''; var prems = 1;
            $('#tags_select').find('input[type=checkbox]').each(function() {
                if (prems == 1) {
                    prems = 0;
                } 
                else {
                    tags += ',';
                }
                tags += $(this).attr('id').split("_")[1];
            });
            post['tags'] = tags;
        }
        var url = $('#base_url').html() + 'gestion/ajax/fiches/add_fiche.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#body_mod_infos').html("Votre fiche a bien été insérée.");
                $('#modal_infos').modal();
//                $('#contenu_fiche input').empty();
//                $('#contenu_fiche textarea').empty();
            }
            else {
                $('#body_mod_infos').html("Un problème est survenu lors de l'enregistrement. Veuillez essayer de nouveau ultérieurement.");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#btn_validate_fiche').on('click', function(e) {
        e.preventDefault();
        $('#btn_validate_fiche').addClass('hide');
        var id = $(this).data('ref');
        var page = $(this).data('page');
        var post = {
            id : id, 
            page : page
        };

        var url = $('#base_url').html() + 'gestion/ajax/fiches/validate_fiche.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#body_mod_infos').html("La fiche a bien été validée");
                $('#modal_infos').modal();
            }
            else {
                $('#body_mod_infos').html("Impossible de valider la fiche");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#btn_update_fiche').on('click', function(e) {
        e.preventDefault();
        var page = $(this).data('page'); 
        if ((page == 'structure') && ($('#BDDnom').val() == '')) {
            $('#body_mod_infos').html("Vous devez saisir un nom de structure");
            $('#modal_infos').modal(); return;
        } else {
            if ($('#BDDtitre').val() == '') {
                $('#body_mod_infos').html("Vous devez saisir un titre pour la fiche");
                $('#modal_infos').modal(); return;
            }
        }
        if ($('#BDDno_ville').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir une ville pour la fiche");
            $('#modal_infos').modal(); return;
        }
        var afficher_signature = 0;
        if ($('#BDDafficher_signature').is(':checked') ) {
            afficher_signature = 1;
        }
        
        var post = {
            no : $('#BDDno').val(), 
            page : page, 
            user : $('#BDDno_utilisateur_creation').val(), 
            afficher_signature : afficher_signature
        };
        tinymce.triggerSave(true, true);

        if (($('#exist_image_name').data('init') == 0)) {
            post['image'] = $('#filelist').html();
        }
        $('#contenu_fiche input').each(function() {
            if (($(this).val() != '') && ($(this).attr('id') != 'undefined') && ($(this).attr('type') == 'text')) {
                var champ = $(this).attr('id').replace("BDD","");
                var value = $(this).val();
                
                if (champ != 'sous_titre') {
                    post[champ]= value;
                }
                else {
                    post['soustitre']= value;
                }
            } else {
                if ($(this).attr('id') == 'BDDno_ville') {
                    post['no_ville'] = $(this).val();
                }
            }
        });
        
        $('#contenu_fiche select').each(function() {
            var champ = $(this).attr('id').replace("BDD","");
            var value = $('#' + $(this).attr('id') + ' option:selected').val();
            post[champ]= value;
        });
        
        $('#contenu_fiche textarea').each(function() {
            if ($(this).val() != '') {
                var champ = $(this).attr('id').replace("BDD","");
                var value = $(this).val();
                post[champ]= value;
            }
        });
        
        if ($('#tags_select').html() != '') {
            var tags = ''; var prems = 1;
            $('#tags_select').find('input[type=checkbox]').each(function() {
                if (prems == 1) {
                    prems = 0;
                } 
                else {
                    tags += ',';
                }
                tags += $(this).attr('id').split("_")[1];
            });
            post['tags'] = tags;
        }
        var url = $('#base_url').html() + 'gestion/ajax/fiches/update_fiche.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#body_mod_infos').html("Votre fiche a bien été modifiée.");
                $('#modal_infos').modal();
            }
            else {
                $('#body_mod_infos').html("Un problème est survenu lors de l'enregistrement. Veuillez essayer de nouveau ultérieurement.");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#btn_del_img_fiche').on('click', function(e) {
        e.preventDefault();
        $('#exist_image_name').html('');
        $('#plupload').removeClass('hide');
        $('#exist_image_name').data('init', 0);
    });

    $('#ville').on('keyup', function(e) {
        e.preventDefault();
        rechercher_ville(this);
    });
    
    $('#BDDnom_contact').on('keyup', function(e) {
        e.preventDefault();
        if ($(this).val().length > 2) {
            var post = {
                name : $(this).val()
            };
            var url = $('#base_url').html() + 'gestion/ajax/fiches/search_contact.php';
            $.post(url, post, function(reponse){
                $('#recherche_contact_liste').empty();
                $.each(reponse, function(index, value) {
                    var chaine = '<div class="elem_item_contact" name="elem_item_contact" data-ref="' + value.no + '">' + value.nom + '</div>'; 
                    $('#recherche_contact_liste').append(chaine); 
                });
            }, 'json');
        } 
        else {
            $('#recherche_contact_liste').empty();
        }
    });
    
    $('#BDDemail_contact').on('focus', function(e) {
        e.preventDefault();
        $('#recherche_contact_liste').empty();
    });
    $('#BDDtelephone_contact').on('focus', function(e) {
        e.preventDefault();
        $('#recherche_contact_liste').empty();
    });
    $('#BDDtelephone2_contact').on('focus', function(e) {
        e.preventDefault();
        $('#recherche_contact_liste').empty();
    });
    
    $(document).on('click', 'div[name=elem_item_contact]', function(e) {
        e.preventDefault();
        
        var post = {
            ref : $(this).data('ref')
        };
        $('#BDDnom_contact').val($(this).html()); $('#BDDno_contact').html($(this).data('ref'));
        var url = $('#base_url').html() + 'gestion/ajax/fiches/search_infos_contact.php';
        $.post(url, post, function(reponse){
            var tab = ['06', '07'];
            $.each(reponse, function(index, value) {
                if (value.type == 1) {
                    if (tab.indexOf(value.valeur.substring(0,2)) != -1) {
                        $('#BDDtelephone2_contact').val(value.valeur);
                    }
                    else {
                        $('#BDDtelephone_contact').val(value.valeur);
                    }
                }
                else {
                    if (value.type == 2) {
                        $('#BDDemail_contact').val(value.valeur);
                    }
                }
            });
        }, 'json');
        $('#recherche_contact_liste').empty();
    });
    
    $(document).on('keyup', '#nom_liaison_fiche', function(e) {
        e.preventDefault();
        if ($('#select_liaison_fiche option:selected').val() == -1) {
            $('#body_mod_infos').html("Vous devez saisir le type de fiche avant d'effectuer la recherche");
            $('#modal_infos').modal(); $('#nom_liaison_fiche').val(''); return;
        }
        var post = {
            type : $('#select_liaison_fiche option:selected').val(), 
            nom : $('#nom_liaison_fiche').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/fiches/search_fiche.php';
        $.post(url, post, function(reponse){
            $('#affiche_recherche_liaison').html(reponse.html);
            $('#affiche_recherche_liaison').show();
        }, 'json');
    });
    
    $(document).on('click', 'a[name=a_link_fiche]', function(e) {
        e.preventDefault();
        if ($('#noms_fiches_liees').html() == '') {
            $('#noms_fiches_liees').html($(this).data('titre') + ' (' + $(this).data('type') + ')');
            $('#no_fiches_liees').html($(this).data('ref') + '-' + $(this).data('type'));
            $('#noms_fiches_liees').removeClass('hide');
        }
        else {
            $('#noms_fiches_liees').append(', ' + $(this).data('titre') + ' (' + $(this).data('type') + ')');
            $('#no_fiches_liees').append(',' + $(this).data('ref') + '-' + $(this).data('type'));
        }
    });
    
    
    // LETTRES D'INFOS
    $('input[name=btn_noupdate_lettre]').on('click', function(e) {
        e.preventDefault();
        $('#body_mod_infos').html("Il ne vous est pas possible de modifier une lettre ayant été envoyée");
        $('#modal_infos').modal();
    });
    
    $('input[name=btn_del_lettre]').on('click', function(e) {
        e.preventDefault();
        $('#id_delete_lettre').html($(this).data('ref'));
        $('#modal_delete_lettre').modal();
    });
    
    $('#btn_valid_delete_lettre').on('click', function(e) {
        e.preventDefault();
        var post = {
            ref : $('#id_delete_lettre').html()
        };
        var url = $('#base_url').html() + 'gestion/ajax/lettres/delete_lettre.php';
        $.post(url, post, function(reponse){
           alert(reponse); 
        });
    });
    
    $('a[name=a_li]').on('click', function(e) {
        e.preventDefault();
        var ref = $(this).data('ref');
        $('div[name=li_bloc]').each(function(index, value) {
            $(this).hide();
        }); 
        $('#li_' + ref).show();
    });
    
    if ($('#li_no_bloc').html() != '') {
        $('div[name=li_bloc]').each(function(index, value) {
            $(this).hide();
        }); 
        $('#li_' + $('#li_no_bloc').html()).show();
        $('#li_menu_add').show();
    }
    if ($('#is_menu_envoi').html() == 1) {
        $('#li_menu_envoi').show();
    }
    else {
        $('#li_menu_envoi').hide();
    }
    
    $('#li_valid_etape_generalites').on('click', function(e) {
        e.preventDefault();
        // ajout de la nouvelle lettre d'info
        if ($('#li_inp_titre').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir un titre pour la lettre avant de valider cette étape");
            $('#modal_infos').modal(); return;
        }
        if ($('#li_inp_date_debut').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir une date de début pour la lettre avant de valider cette étape");
            $('#modal_infos').modal(); return;
        }
        var post = {
            objet : $('#li_inp_titre').val(), 
            date_debut : $('#li_inp_date_debut').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/lettres/new_lettre.php';
        $.post(url, post, function(reponse){
           switch(reponse.code) {
               case '0' : var url = $('#li_url').html() + '&no=' + reponse.no; $(location).attr('href', url); break;
               case '10' : $('#body_mod_infos').html("La lettre ne peut pas être créée. Vérifiez que votre session est encore valide."); 
                            $('#modal_infos').modal(); break;
               default : $('#body_mod_infos').html("La lettre ne peut pas être créée. Veuillez essayer de nouveau."); 
                            $('#modal_infos').modal(); break;
           } 
        }, 'json');
    });
    $('#li_valid_etape_generalites_bis').on('click', function(e) {
        e.preventDefault();
        $('#li_menu_add').show();
        $('#li_generalites').hide();
        $('#li_pdf').show();
    });
    
    $('#li_generate_file_pdf').on('click', function(e) {
        e.preventDefault();
        var agenda = 0;
        if ($('#li_ckb_agenda').is(":checked")) {
            agenda = 1;
        }
        var annonces = 0;
        if ($('#li_ckb_annonces').is(":checked")) {
            annonces = 1;
        }
        if ((annonces == 0) && (agenda == 0)) {
            $('#body_mod_infos').html("Pour générer des fichiers PDF, vous devez cocher une des cases du formulaire."); 
            $('#modal_infos').modal(); return;
        }
        var post = {
            no : $(this).data('ref'), 
            agenda : agenda, 
            annonces : annonces, 
            datefin : $('#date_fin_pdf').val()
        };
        $('#modal_loader').modal('show');
        var url = $('#base_url').html() + 'gestion/ajax/lettres/generate_pdf.php';
        $.post(url, post, function(reponse){
            $('#modal_loader').modal('hide');
            if (reponse.code == 0) {
                $('#body_mod_infos').html("Les fichiers PDF ont bien été générés. Vous pouvez les consulter et valider l'étape"); 
                $('#modal_infos').modal();
                if (agenda == 1) {
                    $('#agenda_view_pdf').find('a').html(reponse.agenda);
                    $('#agenda_view_pdf').find('a').attr('href', $('#base_url').html() + '02_medias/14_lettreinfo_pdf_agenda/' + reponse.agenda);
                    $('#agenda_view_pdf').show();
                }
                if (annonces == 1) {
                    $('#annonces_view_pdf').find('a').html(reponse.annonces);
                    $('#annonces_view_pdf').find('a').attr('href', $('#base_url').html() + '02_medias/15_lettreinfo_pdf_annonces/' + reponse.annonces);
                    $('#annonces_view_pdf').show();
                }
            } 
            else {
                $('#body_mod_infos').html("Un problème est survenu lors de la génération des fichiers PDF"); 
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#li_valid_etape_pdf').on('click', function(e) {
        e.preventDefault();
        // ajout des pdf à la nouvelle lettre d'info
        if (($('#annonces_view_pdf').find('a').html() == '') && ($('#agenda_view_pdf').find('a').html() == '')) {
            $('#body_mod_infos').html("Ne valider cette étape qu'après avoir généré les fichiers. Si vous ne souhaitez pas ajouter les fichiers PDF à votre lettre, passez à l'étape suivante."); 
            $('#modal_infos').modal(); return;
        }
        var agenda = ''; 
        if ($('#agenda_view_pdf').find('a').html()) {
            agenda = $('#agenda_view_pdf').find('a').html();
        }
        var annonces = ''; 
        if ($('#annonces_view_pdf').find('a').html()) {
            annonces = $('#annonces_view_pdf').find('a').html();
        }
        var post = {
            no : $(this).data('ref'), 
            agenda : agenda, 
            annonces : annonces
        };
        $('#modal_loader').modal('show');
        var url = $('#base_url').html() + 'gestion/ajax/lettres/valid_pdf.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                var url_bloc = $(location).attr('href').split('&bloc')[0];
                $(location).attr('href', url_bloc + '&bloc=pdf');
            }
            else {
                $('#modal_loader').modal('hide');
                $('#body_mod_infos').html("L'étape n'a pas pu être validée");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#li_creation_pdf').on('click', function(e) {
        e.preventDefault();
        var post = {
            no : $(this).data('ref')
        };
        var url = $('#base_url').html() + 'gestion/ajax/lettres/delete_pdf.php';
        $.post(url, post, function(reponse){
            var url_bloc = $(location).attr('href').split('&bloc')[0];
            $(location).attr('href', url_bloc + '&bloc=pdf');
        });
    });
    
    $('#li_valid_etape_editorial').on('click', function(e) {
        e.preventDefault();
        // ajout de la nouvelle lettre d'info
        tinymce.triggerSave(true, true);
        if ($('#li_inp_edito').val() == '') {
            $('#body_mod_infos').html("Veuillez saisir un éditorial afin de valider cette étape");
            $('#modal_infos').modal(); return;
        }
        var mention = 0;
        if ($('#li_ckb_mention').is(":checked")) {
            mention = 1;
        }
        var post = {
            no : $(this).data('ref'),
            edito : $('#li_inp_edito').val(), 
            mention : mention
        };
        $('#modal_loader').modal('show');
        var url = $('#base_url').html() + 'gestion/ajax/lettres/valid_editorial.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                var url_bloc = $(location).attr('href').split('&bloc')[0];
                $(location).attr('href', url_bloc + '&bloc=editorial');
            }
            else {
                $('#modal_loader').modal('hide');
                $('#body_mod_infos').html("L'éditorial n'a pas pu être validé");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#li_creation_editorial').on('click', function(e) {
        e.preventDefault();
        // ajout de la nouvelle lettre d'info
        var post = {
            no : $(this).data('ref')
        };
        $('#modal_loader').modal('show');
        var url = $('#base_url').html() + 'gestion/ajax/lettres/delete_editorial.php';
        $.post(url, post, function(reponse){
            var url_bloc = $(location).attr('href').split('&bloc')[0];
            $(location).attr('href', url_bloc + '&bloc=editorial');
        });
    });
    
    $('#li_valid_etape_agenda').on('click', function(e) {
        e.preventDefault();
        // ajout dee infos agenda
        var liste = ''; var prem = 1;
        $('#liste_item_agenda').find('div[name=item_agenda]').each(function() {
            if (prem == 1) {
                prem = 0; 
            }
            else {
                liste += ','; 
            }
            liste += $(this).data('ref');
        });
        
        var post = {
            no : $(this).data('ref'), 
            liste : liste
        };
        $('#modal_loader').modal('show');
        var url = $('#base_url').html() + 'gestion/ajax/lettres/valid_agenda.php';
        $.post(url, post, function(reponse){
            var url_bloc = $(location).attr('href').split('&bloc')[0];
            $(location).attr('href', url_bloc + '&bloc=agenda');
        });
    });
    
    $('#li_creation_agenda').on('click', function(e) {
        e.preventDefault();
        var post = {
            no : $(this).data('ref')
        };
        $('#modal_loader').modal('show');
        var url = $('#base_url').html() + 'gestion/ajax/lettres/delete_agenda.php';
        $.post(url, post, function(reponse){
            var url_bloc = $(location).attr('href').split('&bloc')[0];
            $(location).attr('href', url_bloc + '&bloc=agenda');
        });
    });
    
    $(document).on('click', 'a[name=del_item_agenda]', function(e) {
        e.preventDefault();
        var ref = $(this).data('ref');
        var div = '<div class="lettre_img" name="item_agenda" data-ref="' + ref + '">';
        div += $(this).parent().parent().html().replace('del_item_agenda', 'add_item_agenda').replace('>X<', '>+<');
        div += '</div>';
        $( "#liste_item_agenda_bis" ).append(div);
        $(this).parent().parent().remove();
    });
    
    $('#li_boite_etape_agenda').on('click', function(e) {
        e.preventDefault();
        var post = {
            no : $(this).data('ref')
        }; 
        var url = $('#base_url').html() + 'gestion/ajax/lettres/search_boite_agenda.php';
        $.post(url, post, function(reponse){
            $( "#content_boite_agenda").html('');
            $.each(reponse, function(index, value) {
                var div = '<div class="lettre_img" name="item_agenda" data-ref="' + value.no + '">'; 
                div += '<div style="text-align: right; width: 140px; height: 20px; font-weight: bolder;">';
                div += '<div class="icone_apparition">' + value.apparition + '</div>';
                div += '<a style="cursor:pointer; font-size:20px; color:#ff00ff" name="add_item_agenda" data-ref="' + value.no + '">+</a>';
                div += '</div>';
                if (value.url_image != '') {
                    div += '<div style="width: 140px; height: 140px; margin-left: 10px;">';
                    div += '<img style="max-height: 140px; max-width: 140px; margin-left: ' + value.margin_left + 'px; margin-top: ' + value.margin_top + 'px" src="' + value.url_image + '" />';
                    div += '</div>';
                }
                else {
                    div += '<div style="width: 140px; height: 140px; margin-left: 10px;">&nbsp;</div>';
                }
                div += '<div style="height: 70px; font-size: 13px; margin-left: 10px; text-align: center;">';
                div += '<b><a target="_blank" href="' + value.lien + '">' + value.titre + '</a></b><br/>' + value.nom_ville + ' - ' + value.date_debut + '<br/><b>' + value.libelle + '</b>';
                div += '</div>';
                div += '</div>';
                $( "#content_boite_agenda").append(div);
                
            }); 
        }, 'json');
        var fin = '<div style="clear:both;"></div>';
        $( "#content_boite_agenda").append(fin);
        $('#modal_boite_agenda').modal('show');
    });
    
    $(document).on('click', 'a[name=add_item_agenda]', function(e) {
        var ref = $(this).data('ref');
        var div = '<div class="lettre_img" name="item_agenda" data-ref="' + ref + '">'; 
        div += $(this).parent().parent().html().replace('add_item_agenda', 'del_item_agenda').replace('color:#ff00ff', '').replace('>+<', '>x<'); 
        div += '</div>';  
        $( "#liste_item_agenda" ).append(div);
        $(this).parent().parent().remove();
    });
    
    // GESTION DES ANNONCES
    $(document).on('click', 'a[name=del_item_annonces]', function(e) {
        e.preventDefault();
        var ref = $(this).data('ref');
        var div = '<div class="lettre_img" name="item_annonces" data-ref="' + ref + '">';
        div += $(this).parent().parent().html().replace('del_item_annonces', 'add_item_annonces').replace('>X<', '>+<');
        div += '</div>';
        $( "#liste_item_annonces_bis" ).append(div);
        $(this).parent().parent().remove();
    });
    
    $('#li_valid_etape_annonces').on('click', function(e) {
        e.preventDefault();
        // ajout dee infos agenda
        var liste = ''; var prem = 1;
        $('#liste_item_annonces').find('div[name=item_annonces]').each(function() {
            if (prem == 1) {
                prem = 0; 
            }
            else {
                liste += ','; 
            }
            liste += $(this).data('ref');
        });
        
        var post = {
            no : $(this).data('ref'), 
            liste : liste
        };
        $('#modal_loader').modal('show');
        var url = $('#base_url').html() + 'gestion/ajax/lettres/valid_annonces.php';
        $.post(url, post, function(reponse){
            var url_bloc = $(location).attr('href').split('&bloc')[0];
            $(location).attr('href', url_bloc + '&bloc=annonces');
        });
    });
    
    $('#li_creation_annonces').on('click', function(e) {
        e.preventDefault();
        var post = {
            no : $(this).data('ref')
        };
        var url = $('#base_url').html() + 'gestion/ajax/lettres/delete_annonces.php';
        $('#modal_loader').modal('show');
        $.post(url, post, function(reponse){
            var url_bloc = $(location).attr('href').split('&bloc')[0];
            $(location).attr('href', url_bloc + '&bloc=annonces');
        });
    });
    
    $('#li_boite_etape_annonces').on('click', function(e) {
        e.preventDefault();
        var post = {
            no : $(this).data('ref')
        }; 
        $.ajaxSetup( { "async": false } );
        $( "#content_boite_annonces").html('');
        var url = $('#base_url').html() + 'gestion/ajax/lettres/search_boite_annonces.php';
        $.post(url, post, function(reponse){
            $.each(reponse, function(index, value) {
                var div = '<div class="lettre_img" name="item_annonces" data-ref="' + value.no + '">'; 
                div += '<div style="text-align: right; width: 140px; height: 20px; font-weight: bolder;">';
                div += '<div class="icone_apparition">' + value.apparition + '</div>';
                div += '<a style="cursor:pointer; font-size:20px; color:#ff00ff" name="add_item_annonces" data-ref="' + value.no + '">+</a>';
                div += '</div>';
                if (value.url_image != '') {
                    div += '<div style="width: 140px; height: 140px; margin-left: 10px;">';
                    div += '<img style="max-height: 140px; max-width: 140px; margin-left: ' + value.margin_left + 'px; margin-top: ' + value.margin_top + 'px" src="' + value.url_image + '" />';
                    div += '</div>';
                }
                else {
                    div += '<div style="width: 140px; height: 140px; margin-left: 10px;">&nbsp;</div>';
                }
                div += '<div style="height: 30px; font-size: 13px; margin-left: 10px;">';
                div += '<b><a target="_blank" href="' + value.lien + '">' + value.titre + '</a></b><br/>' + value.nom_ville + '</div>';
                div += '</div>';
                $( "#content_boite_annonces").append(div);
                
            }); 
        }, 'json');
        var fin = '<div style="clear:both;"></div>';
        $( "#content_boite_annonces").append(fin);
        $('#modal_boite_annonces').modal('show');
    });
    
    $(document).on('click', 'a[name=add_item_annonces]', function(e) {
        var ref = $(this).data('ref');
        var div = '<div class="lettre_img" name="item_annonces" data-ref="' + ref + '">'; 
        div += $(this).parent().parent().html().replace('add_item_annonces', 'del_item_annonces').replace('color:#ff00ff', '').replace('>+<', '>x<'); 
        div += '</div>';  
        $( "#liste_item_annonces" ).append(div);
        $(this).parent().parent().remove();
    });
    
    // GESTION DES STRUCTURES 
    $(document).on('click', 'a[name=del_item_structures]', function(e) {
        e.preventDefault();
        $(this).parent().parent().remove();
    });
    
    $('#li_boite_etape_structures').on('click', function(e) {
        e.preventDefault();
        $('#inp_key_structure').val('');
        $('#modal_boite_search_structures').modal('show');
    });
    $('#btn_search_boite_etape_structures').on('click', function(e) {
        e.preventDefault();
        if ($('#inp_key_structure').val() == '') {
            alert("Vous devez saisir une chaîne de caractères pour afficher des structures correspondantes"); return;
        }
        var post = {
            key : $('#inp_key_structure').val()
        }; 
        $.ajaxSetup( { "async": false } );
        $( "#content_boite_structures").html("");
        var url = $('#base_url').html() + 'gestion/ajax/lettres/search_boite_structures.php';
        $.post(url, post, function(reponse){
            $('#modal_boite_search_structures').modal('hide');
            $.each(reponse, function(index, value) {
                var div = '<div class="lettre_img" name="item_structures" data-ref="' + value.no + '">'; 
                div += '<div style="text-align: right; width: 140px; height: 20px; font-weight: bolder;">';
                div += '<div class="icone_apparition">' + value.apparition + '</div>';
                div += '<a style="cursor:pointer; font-size:20px; color:#ff00ff" name="add_item_structures" data-ref="' + value.no + '">+</a>';
                div += '</div>';
                if (value.url_logo != '') {
                    div += '<div style="width: 140px; height: 140px; margin-left: 10px;">';
                    div += '<img style="max-height: 140px; max-width: 140px; margin-left: ' + value.margin_left + 'px; margin-top: ' + value.margin_top + 'px" src="' + value.url_logo + '" />';
                    div += '</div>';
                }
                else {
                    div += '<div style="width: 140px; height: 140px; margin-left: 10px;">&nbsp;</div>';
                }
                div += '<div style="height: 30px; font-size: 13px; margin-left: 10px;">';
                div += '<b>' + value.nom + '</b><br/>' + value.nom_ville + '<br/><b>' + value.libelle + '</b></div>';
                div += '</div>';
                $( "#content_boite_structures").append(div);
            }); 
        }, 'json');
        var fin = '<div style="clear:both;"></div>';
        $( "#content_boite_structures").append(fin);
        $('#modal_boite_structures').modal('show');
    });
    
    $(document).on('click', 'a[name=add_item_structures]', function(e) {
        var ref = $(this).data('ref');
        var div = '<div class="lettre_img" name="item_structures" data-ref="' + ref + '">'; 
        div += $(this).parent().parent().html().replace('add_item_structures', 'del_item_structures').replace('color:#ff00ff', '').replace('>+<', '>x<'); 
        div += '</div>';  
        $( "#liste_item_structures" ).append(div);
        $(this).parent().parent().remove();
    });
    
    $('#li_valid_etape_structures').on('click', function(e) {
        e.preventDefault();
        var liste = ''; var prem = 1;
        $('#liste_item_structures').find('div[name=item_structures]').each(function() {
            if (prem == 1) {
                prem = 0; 
            }
            else {
                liste += ','; 
            }
            liste += $(this).data('ref');
        });
        
        var post = {
            no : $(this).data('ref'), 
            liste : liste
        };
        $('#modal_loader').modal('show');
        var url = $('#base_url').html() + 'gestion/ajax/lettres/valid_structures.php';
        $.post(url, post, function(reponse){
            var url_bloc = $(location).attr('href').split('&bloc')[0];
            $(location).attr('href', url_bloc + '&bloc=structures');
        });
    });
    
    $('#li_creation_structures').on('click', function(e) {
        e.preventDefault();
        var post = {
            no : $(this).data('ref')
        };
        var url = $('#base_url').html() + 'gestion/ajax/lettres/delete_structures.php';
        $('#modal_loader').modal('show');
        $.post(url, post, function(reponse){
            var url_bloc = $(location).attr('href').split('&bloc')[0];
            $(location).attr('href', url_bloc + '&bloc=structures');
        });
    });
    
    $(document).on('click', 'a[name=del_item_collectif]', function(e) {
        e.preventDefault();
        $(this).parent().parent().remove();
    });
    
    $(document).on('click', 'a[name=del_item_partenaires]', function(e) {
        e.preventDefault();
        $(this).parent().parent().remove();
    });
    
    $('#li_valid_etape_partenaires').on('click', function(e) {
        e.preventDefault();
        var liste_collectif = ''; var prem = 1;
        $('#liste_item_collectif').find('div[name=item_collectif]').each(function() {
            if (prem == 1) {
                prem = 0; 
            }
            else {
                liste_collectif += ','; 
            }
            liste_collectif += $(this).data('ref');
        });
        
        var liste_partenaires = ''; var prem = 1;
        $('#liste_item_partenaires').find('div[name=item_partenaires]').each(function() {
            if (prem == 1) {
                prem = 0; 
            }
            else {
                liste_partenaires += ','; 
            }
            liste_partenaires += $(this).data('ref');
        });
        
        var post = {
            no : $(this).data('ref'), 
            liste_collectif : liste_collectif, 
            liste_partenaires : liste_partenaires
        };
        $('#modal_loader').modal('show');
        var url = $('#base_url').html() + 'gestion/ajax/lettres/valid_partenaires.php';
        $.post(url, post, function(reponse){
            var url_bloc = $(location).attr('href').split('&bloc')[0];
            $(location).attr('href', url_bloc + '&bloc=partenaires');
        });
    });
    $('#li_creation_partenaires').on('click', function(e) {
        e.preventDefault();
        var post = {
            no : $(this).data('ref')
        };
        var url = $('#base_url').html() + 'gestion/ajax/lettres/delete_partenaires.php';
        $('#modal_loader').modal('show');
        $.post(url, post, function(reponse){
            var url_bloc = $(location).attr('href').split('&bloc')[0];
            $(location).attr('href', url_bloc + '&bloc=partenaires');
        });
    });
    
    $('#li_add_partenaire').on('click', function(e) {
        e.preventDefault();
        $('#modal_ajout_partenaire').modal('show');
    });
    
    $('#btn_valid_add_partenaire').on('click', function(e) {
        e.preventDefault();
        if ($('#inp_nom_partenaire').val() == '') {
            alert('Veuillez saisir le nom du partenaire'); return;
        };
        if ($('#filelist3').html() == '') {
            alert('Veuillez charger un visuel pour le partenaire'); return;
        }
        var post = {
            nom : $('#inp_nom_partenaire').val(), 
            image : $('#filelist3').html(), 
            site : $('#inp_site_partenaire').val(), 
            type : $('#sel_type_partenaire option:selected').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/partenaires/add.php';
        $.post(url, post, function(reponse){
            var url_bloc = $(location).attr('href').split('&bloc')[0];
            $(location).attr('href', url_bloc + '&bloc=partenaires');
        });
    });
    
    $('#sel_type_partenaire').on('change', function(e) {
        e.preventDefault();
        var post = {
            type : $('#sel_type_partenaire option:selected').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/partenaires/update_cookie.php';
        $.post(url, post, function(reponse){
            
        });
    });
    
    $('#li_update_mention').on('click', function(e) {
        e.preventDefault();
        tinymce.triggerSave(true, true);
        var post = {
            mention : $('#li_inp_mention').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/lettres/update_mention.php';
        $('#modal_loader').modal('show');
        $.post(url, post, function(reponse){
            $('#modal_loader').modal('hide');
            if (reponse.code == 0) {
                $('#body_mod_infos').html("La mention permanente a bien été modifiée.");
                $('#modal_infos').modal();
            }
            else {
                $('#body_mod_infos').html("La mention n'a pas pu être modifiée. Veuillez essayer de nouveau.");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#link_hors_territoire').on('click', function(e) {
        e.preventDefault();
        $(location).attr('href', $(location).attr('href') + '&ht=1');
    });
    
    $('#li_creation_lettre').on('click', function(e) {
        e.preventDefault();
        var post = {
            ref : $(this).data('ref'),
            mail : $('#lettre_test_email').val()
        }; 
        var url = $('#base_url').html() + 'gestion/ajax/lettres/create.php';
        $('#modal_loader').modal('show');
        $.post(url, post, function(reponse){
            if (reponse.code == '0') {
                var url_bloc = $(location).attr('href').split('&bloc')[0];
                $(location).attr('href', url_bloc + '&bloc=confirm&mail=' + $('#lettre_test_email').val() + '&rep=' + reponse.repertoire);
            }
        }, 'json');
    });
    
    $('#li_validation_lettre').on('click', function(e) {
        e.preventDefault();
        var post = {
            ref : $(this).data('ref')
        }; 
        var url = $('#base_url').html() + 'gestion/ajax/lettres/insert_envoi.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                var url_bloc = $(location).attr('href').split('&bloc')[0];
                $(location).attr('href', url_bloc + '&bloc=fin');
            }
            else {
                $('#body_mod_infos').html("Un problème est survenu lors de la programmation de l'envoi.");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#li_annulation_lettre').on('click', function(e) {
        e.preventDefault();
        var post = {
            ref : $(this).data('ref')
        }; 
        var url = $('#base_url').html() + 'gestion/ajax/lettres/cancel.php';
        $.post(url, post, function(reponse){
            if (reponse.code == '0') {
                var url_bloc = $(location).attr('href').split('&bloc')[0];
                $(location).attr('href', url_bloc + '&bloc=agenda');
            }
            else {
                $('#body_mod_infos').html("Un problème est survenu lors de l'annulation de la lettre");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#link_super_admin').on('click', function(e) {
        e.preventDefault();
        $('#modal_super_admin').modal('show');
    });
    
    $('#btn_valid_superadmin').on('click', function(e) {
        e.preventDefault();
        var post = {
            pass : $('#pass_sadmin').val()
        }; 
        var url = $('#base_url').html() + 'gestion/ajax/territoires/acces_super_admin.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                var url = $(location).attr('href').split('territoire')[0];
                url += 'sadmin';
                $(location).attr('href', url);
            }
            else {
                $('#modal_super_admin').modal('hide');
                $('#body_mod_infos').html("Le mot de passe est incorrect");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#retour_super_admin').on('click', function(e) {
        e.preventDefault();
        var url = $(location).attr('href').split('sadmin')[0];
        url += 'territoire&no=';
        $(location).attr('href', url);
    });
    
    $('#link_add_territoire').on('click', function(e) {
        e.preventDefault();
        $('#add_territoire_body1').show(); $('#add_territoire_footer1').show();
        $('#add_territoire_body2').hide(); $('#add_territoire_footer2').hide();
        $('#modal_add_territoire').modal('show');
    });
    
    $('a[name=del_territoire_ville]').on('click', function(e) {
        e.preventDefault();
        $('#id_delete_ville').html($(this).data('ref'));
        $('#modal_delete_ville').modal();
    });
    
    $('#btn_add_territoire_next').on('click', function(e) {
        e.preventDefault();
        if ($('#inp_add_territoire_nom').val() == '') {
            alert ("Le nom du territoire doit être renseigné"); return;
        }
        if ($('#inp_add_territoire_email').val() == '') {
            alert ("L'adresse email du premier adminstrateur doit être renseigné"); return;
        }
        if ($('#inp_add_territoire_code').val() == '') {
            alert ("Le code postal de la première commune du territoire doit être renseigné"); return;
        }
        if ($('#inp_add_territoire_pays').val() == '') {
            alert ("Le nom du bassin de vie qui va apparaitre dans la newsletter doit être renseigné"); return;
        }
        var post = {
            code : $('#inp_add_territoire_code').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/territoires/recherche_ville_super.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#add_territoire_liste').html(reponse.chaine);
            }
            else {
                $('#add_territoire_liste').html("Aucune commune n'a été trouvée pour le code postal saisi");
            }
            $('#add_territoire_body2').show(); $('#add_territoire_footer2').show();
            $('#add_territoire_body1').hide(); $('#add_territoire_footer1').hide();
        }, 'json');
    });
    
    $('#btn_valid_add_territoire').on('click', function(e) {
        e.preventDefault();
        var post = {
            ville : $('input[name=add_territoire_ville_super]:checked').val(), 
            nom : $('#inp_add_territoire_nom').val(),
            email : $('#inp_add_territoire_email').val(), 
            pays : $('#inp_add_territoire_pays').val()
        }
        var url = $('#base_url').html() + 'gestion/ajax/territoires/create_territoire.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $(location).attr('href', $(location).attr('href'));
            }
            else {
                $('#modal_add_territoire').modal('show');
                $('#body_mod_infos').html("Le territoire n'a pas pu être ajouté");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#btn_valid_delete_ville').on('click', function(e) {
        e.preventDefault();
        var post = {
            ref : $('#id_delete_ville').html()
        }; 
        var url = $('#base_url').html() + 'gestion/ajax/territoires/delete_ville.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $(location).attr('href', $(location).attr('href'));
            }
            else {
                $('#modal_delete_ville').modal('hide');
                $('#body_mod_infos').html("La ville n'a pas pu être supprimée");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#btn_insert_ville').on('click', function(e) {
        e.preventDefault();
        $('#resultats_territoire_cont').html('');
        $('#resultats_territoire').hide();
        var post = {
            code : $('#insert_territoire_code').val(), 
            ville : $('#insert_territoire_ville').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/territoires/recherche_ville.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#resultats_territoire_cont').html(reponse.chaine);
                $('#resultats_territoire').show();
            }
            else {
                $('#body_mod_infos').html("Votre recherche ne donne aucun résultat");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $('#btn_valid_insert_ville').on('click', function(e) {
        e.preventDefault();
        var liste = ''; var prems = 1;
        $('input[name=add_territoire_ville]').each(function(){
            if ($(this).is(':checked')) {
                if (prems == 1) {
                    prems = 0;
                    liste += $(this).data('ref');
                }
                else {
                    liste += ',' + $(this).data('ref');
                }
            }
        });
        if (liste == '') {
            $('#body_mod_infos').html("Vous devez sélectionner des communes pour les ajouter à votre territoire");
            $('#modal_infos').modal(); return;
        }
        else {
            var post = {
                liste : liste, 
                communaute : $('#sel_territoire_communaute option:selected').val()
            };
            var url = $('#base_url').html() + 'gestion/ajax/territoires/add_ville.php';
            $.post(url, post, function(reponse){
                if (reponse.code == 0) {
                    $(location).attr('href', $(location).attr('href'));
                }
                else {
                    $('#body_mod_infos').html("Les communes n'ont pas pu être renseignées");
                    $('#modal_infos').modal();
                }
            }, 'json');
        }
    });
    
    $('#btn_add_new_communaute').on('click', function(e) {
        e.preventDefault();
        if ($('#inp_new_communaute').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir le libellé du bassin de vie");
            $('#modal_infos').modal(); return;
        }
        if ($('#inp_new_communaute_ville').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir le nom de la commune principale du bassin de vie");
            $('#modal_infos').modal(); return;
        }
        var post = {
            ville : $('#inp_new_communaute_ville').val(), 
            communaute : $('#inp_new_communaute').val()
        };
        var url = $('#base_url').html() + 'gestion/ajax/territoires/add_communaute.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $(location).attr('href', $(location).attr('href'));
            }
            else {
                $('#body_mod_infos').html("Le bassin de vie n'a pas pu être ajouté");
                $('#modal_infos').modal();
            }
        }, 'json');
    });
});