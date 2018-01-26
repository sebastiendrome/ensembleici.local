$(function() {
    $('#ajout_lettre').on('click', function(e) {
        e.preventDefault();
        $(location).attr('href', 'modifajout.php?ajout=1&territoire=' + $('#sel_territoire option:selected').val());
    });
    
    $('#sel_territoire_newsletter').on('change', function(e) {
        e.preventDefault();
        var id = $('#sel_territoire_newsletter option:selected').val();
        $(location).attr('href', 'admin.php?&territoire=' + id);
    });
    
    $('#sel_territoire_structure').on('change', function(e) {
        e.preventDefault();
        var id = $('#sel_territoire_structure option:selected').val();
        $(location).attr('href', 'admin.php?territoire=' + id);
    });
    
    $('#sel_territoire_events').on('change', function(e) {
        e.preventDefault();
        var id = $('#sel_territoire_events option:selected').val();
        $(location).attr('href', 'admin.php?territoire=' + id);
    });
    
    $('#sel_territoire_annonce').on('change', function(e) {
        e.preventDefault();
        var id = $('#sel_territoire_annonce option:selected').val();
        $(location).attr('href', 'admin.php?territoire=' + id);
    });
    
    $('#sel_territoire').on('change', function(e) {
        e.preventDefault();
        var id = $('#sel_territoire option:selected').val();
        $(location).attr('href', 'admin.php?territoire=' + id);
    });
    
    $('#generation_pdf_lettreinfo').on('click', function(e) {
        e.preventDefault();
        var agenda = 0;
        if ($('#ckb_gen_pdf_agenda').is(':checked')) {
            agenda = 1;
        }
        var annonce = 0;
        if ($('#ckb_gen_pdf_annonces').is(':checked')) {
            annonce = 1;
        }
        var post = {
            agenda : agenda, 
            annonce : annonce, 
            id : $(this).data('id')
        }; 
        var url = $('#base_url').html() + '03_ajax/lettresinfos/php/generate_pdf.php';
        $.post(url, post, function(reponse){
            if (reponse.nb == 0) {
                alert("Aucun fichier PDF n'a été généré");
            }
            else {
                $('#view_generation_pdf_lettreinfo').show();
                if (reponse.is_agenda == 1) {
                    $('#view_generation_pdf_agenda').show();
                    $('#link_pdf_agenda').attr('href', "http://www.ensembleici.fr/02_medias/14_lettreinfo_pdf_agenda/" + reponse.agenda);
                    $('#name_pdf_agenda').html(reponse.agenda);
                }
                if (reponse.is_annonces == 1) {
                    $('#view_generation_pdf_annonces').show();
                    $('#link_pdf_annonces').attr('href', "http://www.ensembleici.fr/02_medias/15_lettreinfo_pdf_annonces/" + reponse.annonces);
                    $('#name_pdf_annonces').html(reponse.annonces);
                }
            }
        }, 'json');
    });
    
    $('#validate_generation_pdf_lettreinfo').on('click', function(e) {
        e.preventDefault();
        if (($('#name_pdf_agenda').html() == '') && ($('#name_pdf_annonces').html() == '')) {
            alert("Aucun fichier n'a été ajouté à la lettre d'infos"); return;
        }
        var post = {
            agenda : $('#name_pdf_agenda').html(), 
            annonce : $('#name_pdf_annonces').html(), 
            id : $(this).data('id')
        }; 
        var url = $('#base_url').html() + '03_ajax/lettresinfos/php/update_pdf_lettreinfo.php';
        $.post(url, post, function(reponse){
            if (reponse.code == 0) {
                $('#view_generation_pdf_lettreinfo').hide();
                $('#generation_pdf_lettreinfo').hide();
                $('#liste_pdf_agenda').hide();
                $('#liste_pdf_annonces').hide();
                $('#message_generation_pdf_lettreinfo').show();
            }
            else {
                alert("Une erreur s'est produite et aucun fichier n'a été ajouté à la lettre d'infos"); return;
            }
        }, 'json');
    });
    
    lastjq('#add_partenaire').on('click', function(e) {
        e.preventDefault();
        lastjq('#logo_new_part').val('');
        lastjq('#nom_new_part').val('');
        lastjq('#site_new_part').val('');
        lastjq('#message_new_part').removeClass('alert alert-error').addClass('hide');
        lastjq('#modal_add_partenaire').modal();
    });
    
    $('#logo_new_part').on('change',function(event){
        var Data = new FormData();
        Data.append('logo_new_part',$('#logo_new_part')[0].files[0]);

        $.ajax({
            url: $('#base_url').html() + '03_ajax/partenaires/upload.php',
            data: Data,
            processData: false,
            contentType: false,
            type:'POST',
            success: function(data){
                var madata = $.parseJSON(data);
                if (madata.error) {
                    alert("Erreur fileupload : " + madata.message);
                    $('#logo_new_part').val('');
                }
            },
            error: function(data){
                var madata = $.parseJSON(data);
                alert(madata.message);
                if (madata.error) {
                    alert("Erreur fileupload : " + madata.message);
                    $('#logo_new_part').val('');
                }
            }
        })
      });
  
    $('#btn_valid_new_part').on('click', function(e) {
        e.preventDefault();
        $('#message_new_part').removeClass('alert alert-error').addClass('hide');
        if ($('#nom_new_part').val() == '') {
            $('#message_new_part').html("Le nom du partenaire doit être renseigné"); 
            $('#message_new_part').removeClass('hide').addClass('alert alert-danger');
            return;
        }
        if ($('#logo_new_part').val() == '') {
            $('#message_new_part').html("Le logo du partenaire doit être renseigné"); 
            $('#message_new_part').removeClass('hide').addClass('alert alert-danger');
            return;
        }
        
        var post = {
            nom : $('#nom_new_part').val(), 
            site : $('#site_new_part').val(), 
            logo : $('#logo_new_part').val(), 
            territoire : $('#add_partenaire').data('id')
        };
        var url = $('#base_url').html() + '03_ajax/partenaires/add.php';
        $.post(url, post, function(reponse){
            $(location).attr('href', $(location).attr('href'));
        });
    });
    
});

