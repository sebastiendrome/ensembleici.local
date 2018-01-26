$(function() {
    var uploader3 = new plupload.Uploader({
        runtimes : 'html5,flash',
        browse_button : 'browse3',
        container : 'plupload3',
        url : '../gestion/ajax/partenaires/upload_image.php',
        flash_swf_url : '/plupload/plupload.flash.swf',
        multipart:true, 
        urlstream_upload:true,
        drop_element:'droparea3',
        multipart_params: {directory:'documents'},
        max_file_size:'5mb',
        filters : [
            {title : "Image files", extensions : "jpg,gif,png,jpeg"}
        ]
    });

    uploader3.init();

    uploader3.bind('FilesAdded', function(up, files) {
        $('#progressgen3').html("");
        $('#progressgen3').css('color', '#790000');
        var filelist3 = $('#filelist3');
        for (var i in files) {
            var file = files[i];
        }
        if ((i>0) || $('#filelist3 > *').length > 1)
        {
            alert('Nombre de fichiers trop important');
            for (var j in files) 
            {
                var file2 = files[j];
                $('#' + file2.id).remove();
            }
        }
        else 
        {
            uploader3.start();
            uploader3.refresh(); 
        }
        
    });

    uploader3.bind('UploadProgress', function(up, file) {
        $('#progressgen3').html("Chargement en cours");
    });

    uploader3.bind('Error', function(up, err) {
        alert("Erreur bind :" + err.message);
        uploader3.refresh(); // Reposition Flash/Silverlight
    });

    uploader3.bind('FileUploaded', function(up, file, reponse) {
        var i = 0;
        $.each(reponse, function(data, value){
            if (i == 0) {
                var madata = $.parseJSON(value);
                if (madata.error) {
                    alert("Erreur fileupload : " + madata.message);
                } 
                else {
                    $('#progressgen3').html("Chargement terminé");
                    $('#progressgen3').css('color', '#0000ff');
                    $('#filelist3').html(madata.fichier);
                    var chaine = "<img src='" + madata.source + "'  style='max-height:200px; max-width:200px;' /><br/><br/>";
                    $('#exist_image_name').html(chaine);
                    $('#sel_type_partenaire').attr('disabled', 'disabled');
                }
            }
            i++;
        });

    });
});