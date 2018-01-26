$(function() {
    var date_min = new Date();
    if ($( "#BDDdate_debut" ).hasClass('vide')) {
        $( "#BDDdate_debut" ).removeClass('vide');
    }
    if ($( "#BDDdate_fin" ).hasClass('vide')) {
        $( "#BDDdate_fin" ).removeClass('vide');
    }
    $( "#BDDdate_debut" ).datepicker({ minDate: date_min, dateFormat: "dd/mm/yy"});
    $( "#BDDdate_fin" ).datepicker({ minDate: date_min, dateFormat: "dd/mm/yy"});

    if ($('#affiche_newsletter').html() == 1) {
        $('#img_open_newsletter').trigger('click');
    }
    var int_slide = setInterval(function () {
        var num = $('span[name=slide_edito][class=actif]').data('ref'); 
        if (num == 3) {
           num = 1; 
        }
        else {
            num++;
        }
        $('span[name=slide_edito][data-ref=' + num + ']').trigger('click');
    }, '7000');
    
    $('#home_editorial_bloc').on('mouseleave', function(e) {
        e.preventDefault();
        int_slide = setInterval(function () {
            var num = $('span[name=slide_edito][class=actif]').data('ref'); 
            if (num == 3) {
               num = 1; 
            }
            else {
                num++;
            }
            $('span[name=slide_edito][data-ref=' + num + ']').trigger('click');
        }, '7000');
    });
    
    $('#home_editorial_bloc').on('mouseover', function(e) {
        e.preventDefault();
        clearInterval(int_slide);
    });
    
    $(document).on('keyup', '#nom_liaison_fiche', function(e) {
//    $('#nom_liaison_fiche').on('keyup', function(e) {
        e.preventDefault();
        if ($('#select_liaison_fiche option:selected').val() == -1) {
            $('#body_mod_infos').html("Vous devez saisir le type de fiche avant d'effectuer la recherche");
            $('#modal_infos').modal(); $('#nom_liaison_fiche').val(''); return;
        }
        var post = {
            type : $('#select_liaison_fiche option:selected').val(), 
            nom : $('#nom_liaison_fiche').val()
        };
        var url = $('#base_url').html() + '03_ajax/search_fiche.php';
        $.post(url, post, function(reponse){
            $('#affiche_recherche_liaison').html(reponse.html);
            $('#affiche_recherche_liaison').show();
        }, 'json');
    });
    
    $(document).on('click', 'a[name=a_link_fiche]', function(e) {
        e.preventDefault();
        var titre = $(this).data('titre');
        var post = {
            type : $('#select_liaison_fiche option:selected').val(), 
            no_lie : $(this).data('ref'), 
            no_origine : $('#liaison_no').html(), 
            page_origine : $('#liaison_page').html()
        };
        var url = $('#base_url').html() + '03_ajax/add_liaison.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                var chaine = '<div>' + reponse.type + ' : ' + titre + '</div>';
                $('#div_fiches_liees').append(chaine);
                $('#espace_fiches_liees').show();
                $('#affiche_recherche_liaison').hide();
            }
        }, 'json');
    });
    
    $(document).on('click', 'a[name=delete_liaison]', function(e) {
        e.preventDefault();
        var objet = $(this);
        var post = {
            table : $(this).data('table'), 
            no_lie : $(this).data('ref'), 
            no_origine : $('#liaison_no').html()
        };
        var url = $('#base_url').html() + '03_ajax/delete_liaison.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                objet.parent().remove();
            }
        }, 'json');
    });
    
    
    if ($('#reinit_mdp').html() != '') {
        // mot de passe à réinitialiser
        var post = {
            code : $('#reinit_mdp').html()
        };
        var url = $('#base_url').html() + '03_ajax/utilisateurs/search_reinit.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#email_reinit_pass').html(reponse.email);
                $('#id_reinit_pass').html(reponse.id);
                $('#message_pass_reinit').html('');
                $('#message_pass_reinit').attr('class', 'hide');
                $('#btn_valid_new_pass').removeClass('hide');
                $('#modal_pass_reinit').modal();
            }
        }, 'json');
    }
    
    $('#btn_valid_new_pass').on('click', function(e) {
        e.preventDefault();
        $('#message_pass_reinit').html('');
        $('#message_pass_reinit').attr('class', 'hide'); 
        if ($('#new_pass_reinit').val() == '') {
            $('#message_pass_reinit').html("Vous devez saisir le nouveau mot de passe avant de valider");
            $('#message_pass_reinit').attr('class', 'alert alert-danger'); return;
        }
        // mise à jour du mot de passe
        var post = {
            password : $('#new_pass_reinit').val(), 
            id : $('#id_reinit_pass').html(), 
            email : $('#email_reinit_pass').html()
        };
        var url = $('#base_url').html() + '03_ajax/utilisateurs/reinit_password.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#message_pass_reinit').html("Le mot de passe a bien été réinitialisé. Vous pouvez donc vous connecter");
                $('#message_pass_reinit').attr('class', 'alert alert-success');
                $('#btn_valid_new_pass').addClass('hide');
            }
            else {
                $('#message_pass_reinit').html("Un problème est survenu lors de la modification du mot de passe");
                $('#message_pass_reinit').attr('class', 'alert alert-danger');
            }
        }, 'json');
    });
    
    $(document).on('click', '#span_mdp_oublie', function(e) {
        e.preventDefault();
        $('#btn_valid_pass_oublie').removeClass('hide');
        $('#message_pass_oublie').html('');
        $('#message_pass_oublie').attr('class', 'hide');
        $('#modal_pass_oublie').modal();
    });
    
    $('#btn_valid_pass_oublie').on('click', function(e) {
        e.preventDefault();
        $('#message_pass_oublie').html('');
        $('#message_pass_oublie').attr('class', 'hide');
        if ($('#pass_oublie_email').val() == '') {
            $('#message_pass_oublie').html("Veuillez renseigner votre adresse email pour la réinitialisation du mot de passe");
            $('#message_pass_oublie').attr('class', 'alert alert-danger'); return;
        }
        var post = {
            email : $('#pass_oublie_email').val()
        };
        var url = $('#base_url').html() + '03_ajax/utilisateurs/send_reinit_pass.php';
        $.post(url, post, function(reponse){
            if (reponse.code == '0') {
                $('#message_pass_oublie').html("Un mail vient de vous être envoyé. Veuillez cliquer sur le lien contenu dans cet email pour réinitialiser votre mot de passe");
                $('#message_pass_oublie').attr('class', 'alert alert-success');
                $('#btn_valid_pass_oublie').addClass('hide');
            }
            else {
                switch (reponse.code) {
                    case '10' : $('#message_pass_oublie').html("Le format de l'adresse email est incorrect"); break;
                    case '11' : $('#message_pass_oublie').html("Cette adresse email n'est pas présente dans notre base"); break;
                    default : break;
                }
                $('#message_pass_oublie').attr('class', 'alert alert-danger');
            }
        }, 'json');
    });
    
    $(document).on('click', '#mdp', function(e) {
        var post = {
            id : $('#user_id').html()
        };
        var url = $('#base_url').html() + '03_ajax/utilisateurs/search_infos.php';
        $.post(url, post, function(reponse){
            if (reponse.code == '0') {
                if (reponse.newsletter == 1) {
                    $('#update_infos_news').attr('checked', 'checked');
                }
                $('#update_infos_pseudo').val(reponse.pseudo);
                $('#update_infos_pseudo').removeClass('vide');
                $('#update_infos_mail').val(reponse.email);
                $('#update_infos_mail').removeClass('vide');
                $('#update_infos_password').val("monpass");
                $('#update_infos_password').removeClass('vide');
                $('#update_infos_ville').val(reponse.nom_ville);
                $('#update_infos_ville').removeClass('vide');
                $('#BDDno_ville_update').val(reponse.no_ville);
                $('#btn_valid_update_infos_pers').removeClass('hide');
                $('#message_update_infos_pers').html('');
                $('#message_update_infos_pers').attr('class', 'hide');
                $('#modal_update_infos_pers').modal();
            }
        }, 'json');
        
    });
    
    $('#btn_valid_update_infos_pers').on('click', function(e) {
        e.preventDefault();
        $('#message_update_infos_pers').html('');
        $('#message_update_infos_pers').attr('class', 'hide');
        
        if ($('#update_infos_mail').val() == '') {
            $('#message_update_infos_pers').html("Votre adresse email doit obligatoirement être renseignée");
            $('#message_update_infos_pers').attr('class', 'alert alert-danger'); return;
        }
        if ($('#update_infos_password').val() == '') {
            $('#message_update_infos_pers').html("Le mot de passe ne peut pas être vide");
            $('#message_update_infos_pers').attr('class', 'alert alert-danger'); return;
        }
        var news = 0; 
        if ($('#update_infos_news').is(':checked')) {
            news = 1;
        }
        var post = {
            id : $('#user_id').html(), 
            pseudo : $('#update_infos_pseudo').val(), 
            email : $('#update_infos_mail').val(), 
            password : $('#update_infos_password').val(), 
            no_ville : $('input[name=update_infos_noville]').val(), 
            news : news
        };
        var url = $('#base_url').html() + '03_ajax/utilisateurs/update_infos.php';
        $.post(url, post, function(reponse){
            if (reponse.code == '0') {
                $('#message_update_infos_pers').html("Vos informations ont bien été mises à jour");
                $('#message_update_infos_pers').attr('class', 'alert alert-success');
            }
            else {
                switch (reponse.code) {
                    case '1' : $('#message_update_infos_pers').html("Le format de l'adresse email est incorrect"); break;
                    default : break;
                }
                $('#message_update_infos_pers').attr('class', 'alert alert-danger');
            }
        }, 'json');
    });
    
    $('#input_desinscription').on('click', function(e) {
        e.preventDefault();
        if ($('#input_email_desinscription').val() == '') {
            $('#body_mod_infos').html("Vous devez saisir l'adresse email à désinscrire");
            $('#modal_infos').modal(); return;
        }
        var post = {
            email : $('#input_email_desinscription').val()
        };
        var url = $('#base_url').html() + '03_ajax/utilisateurs/remove_newsletter.php';
        $.post(url, post, function(reponse){
            switch (reponse.code) {
                case '0' : $('#body_mod_infos').html("Votre désinscription a bien été prise en compte. Si vous souhaitez vous inscrire de nouveau ultérieurement, vous pourrez le faire sur notre site."); break;
                case '1' : $('#body_mod_infos').html("L'adresse saisie n'est pas une adresse email conforme"); break;
                case '2' : $('#body_mod_infos').html("L'adresse email saisie n'est pas présente dans notre base"); break;
                default : $('#body_mod_infos').html("Un erreur est survenue lors de la désinscription. Merci d'essayer ultérieurement."); break;
            }
            $('#modal_infos').modal();
        }, 'json');
    });
    
    $('#btn_valid_signalement').on('click', function(e) {
        e.preventDefault();
        $('#message_signalement').html('');
        $('#message_signalement').attr('class', 'hide');
        if ($('#motif_signalement').val() == '') {
            $('#message_signalement').html("Vous devez saisir un motif pour signaler un contenu inapproprié");
            $('#message_signalement').attr('class', 'alert alert-danger'); return;
        }
        var post = {
            page : $('#page_signalement').html(), 
            id : $('#id_signalement').html(), 
            motif : $('#motif_signalement').val(), 
            nom : $('#nom_signalement').html()
        };
        var url = $('#base_url').html() + '03_ajax/utilisateurs/add_signalement.php';
        $.post(url, post, function(reponse){
            if (reponse.code == '0') {
                $('#message_signalement').html("Votre signalement a bien été pris en compte et nous allons traiter votre demande dans les meilleurs délais.");
                $('#message_signalement').attr('class', 'alert alert-success');
                $('#motif_signalement').val('');
                $('#btn_valid_signalement').addClass('hide');
            }
            else {
                $('#message_signalement').html("Un problème est survenu lors du signalement. Veuillez réessayer ultérieurement.");
                $('#message_signalement').attr('class', 'alert alert-danger');
            }
        }, 'json');
    });
    
    $(document).on('click', '#valid_desactiver', function(e) {
        e.preventDefault();
        var post = {
            page : $(this).data('page'), 
            id : $(this).data('ref') 
        };
        var url = $('#base_url').html() + '03_ajax/utilisateurs/desactiver_fiche.php';
        $.post(url, post, function(reponse){
            if (reponse.code == '0') {
                $(location).attr('href', $(location).attr('href'));
            } 
            else {
                switch (reponse.code) {
                    case '10' : $('#body_mod_infos').html("Vous devez être connecté pour désactiver une fiche."); break;
                    default : $('#body_mod_infos').html("Un erreur est survenue lors de la désactivation. Merci d'essayer ultérieurement."); break;
                }
                $('#modal_infos').modal();
            }
        }, 'json');
    });
    
    $(document).on('click', '#btn_valid_abo', function(e) {
        e.preventDefault();
        var liste = ''; var prems = 1;
        $('input[name=inp_abo]').each(function(){
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
        $.ajaxSetup( { "async": false } );
        if (liste == '') {
            $('#body_mod_infos').html("Vous devez cocher au minimum un territoire pour vous abonner à une newsletter.");
            $('#modal_infos').modal(); return;
        }
        if ($('#input_email').val() == '') {
            $('#body_mod_infos').html("Vous devez renseigner votre adresse email vous abonner à une newsletter.");
            $('#modal_infos').modal(); return;
        }
        var post = {
            liste : liste, 
            email : $('#input_email').val(), 
            captcha : $('#input_captcha').val()
        };
        var url = $('#base_url').html() + '03_ajax/newsletter.php';
        $.post(url, post, function(reponse){
            switch (reponse.code) {
                case '0' : $('#body_mod_infos').html("Votre inscription aux newsletters a bien été prise en compte"); supprime_message("colorbox",true);  break;
                case '10' : $('#body_mod_infos').html("Le code de sécurité n'est pas valide."); break;
                case '20' : $('#body_mod_infos').html("L'adresse email saisie n'est pas valide."); break;
            }
        }, 'json');
        $('#modal_infos').modal();
    });
    
    $(document).on('click', '#voir_liaisons', function(e) {
        e.preventDefault();
        $('#section_liaisons').show();
    });
});

function displayFB() {
    var ref = "https://www.facebook.com/sharer.php?u=" + $(location).attr('href') + "&t=" + $('#article_partage_FB').data('title'); 
    window.open(ref, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=700');
}
function displayTW() {
    var ref = "https://twitter.com/share?url=" + $(location).attr('href') + "&text=A découvrir sur Ensemble Ici : " + $('#article_partage_TW').data('title');
    window.open(ref, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=700');
}
function displayGP() {
    var ref = "https://plus.google.com/share?url=" + $(location).attr('href') + "&hl=fr";
    window.open(ref, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=700');
}
function signaler_article() {
    $('#message_signalement').html('');
    $('#message_signalement').attr('class', 'hide');
    $('#btn_valid_signalement').removeClass('hide');
    var post = {
        page : $('#signaler_article').data('page'), 
        id : $('#signaler_article').data('id')
    };
    var url = $('#base_url').html() + '03_ajax/utilisateurs/signalement.php';
    $.post(url, post, function(reponse){
        if (reponse.code == '0') {
            $('#nom_signalement').html(reponse.titre);
            $('#id_signalement').html(reponse.id);
            $('#page_signalement').html(reponse.page);
            $('#modal_signalement').modal();
        } 
        else {
            switch (reponse.code) {
                case '10' : $('#body_mod_infos').html("Vous devez être connecté pour effectuer un signalement."); break;
                default : $('#body_mod_infos').html("Un erreur est survenue lors du signalement. Merci d'essayer ultérieurement."); break;
            }
            $('#modal_infos').modal();
        }
        
    }, 'json');
}