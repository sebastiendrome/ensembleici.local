$(function() {
    var uploader = new plupload.Uploader({
        runtimes : 'html5,flash',
        browse_button : 'browse',
        container : 'plupload',
        url : '../gestion/ajax/fiches/upload_photo_fiche.php',
        flash_swf_url : '/plupload/plupload.flash.swf',
        multipart:true, 
        urlstream_upload:true,
        drop_element:'droparea',
        multipart_params: {directory:'documents'},
        max_file_size:'5mb',
        filters : [
            {title : "Image files", extensions : "jpg,gif,png,jpeg"}
        ],
        resize : {width : 800, height : 800, quality : 90}
    });

    uploader.init();

    uploader.bind('FilesAdded', function(up, files) {
        $('#progressgen').html("");
        $('#progressgen').css('color', '#790000');
        var filelist = $('#filelist');
        for (var i in files) {
            var file = files[i];
        }
        if ((i>0) || $('#filelist > *').length > 1)
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
            uploader.start();
            uploader.refresh(); 
        }
        
    });

    uploader.bind('UploadProgress', function(up, file) {
        $('#progressgen').html("Chargement en cours");
    });

    uploader.bind('Error', function(up, err) {
        alert("Erreur bind :" + err.message);
        uploader.refresh(); // Reposition Flash/Silverlight
    });

    uploader.bind('FileUploaded', function(up, file, reponse) {
        var i = 0;
        $.each(reponse, function(data, value){
            if (i == 0) {
                var madata = $.parseJSON(value);
                if (madata.error) {
                    alert("Erreur fileupload : " + madata.message);
                } 
                else {
                    $('#progressgen').html("Chargement terminé");
                    $('#progressgen').css('color', '#0000ff');
                    $('#filelist').html(madata.fichier);
                    var chaine = "<img src='" + madata.source + "'  style='max-height:200px; max-width:200px;' /><br/><br/>";
                    $('#exist_image_name').html(chaine);
                }
            }
            i++;
        });

    });
});