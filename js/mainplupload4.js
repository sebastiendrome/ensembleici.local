$(function() {
    var uploader4 = new plupload.Uploader({
        runtimes : 'html5,flash',
        browse_button : 'browse4',
        container : 'plupload4',
        url : '../gestion/ajax/diaporamas/upload_photo.php',
        flash_swf_url : '/plupload/plupload.flash.swf',
        multipart:true, 
        urlstream_upload:true,
        drop_element:'droparea4',
        multipart_params: {directory:'documents'},
        max_file_size:'1mb',
        filters : [
            {title : "Image files", extensions : "jpg,gif,png,jpeg"}
        ]
    });

    uploader4.init();

    uploader4.bind('FilesAdded', function(up, files) {
        $('#progressgen4').html("");
        $('#progressgen4').css('color', '#790000');
        var filelist4 = $('#filelist4');
        for (var i in files) {
            var file = files[i];
        }
        if ((i>0) || $('#filelist4 > *').length > 1)
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
            uploader4.start();
            uploader4.refresh(); 
        }
        
    });

    uploader4.bind('UploadProgress', function(up, file) {
        $('#progressgen4').html("Chargement en cours");
    });

    uploader4.bind('Error', function(up, err) {
        alert("Erreur bind :" + err.message);
        uploader4.refresh(); // Reposition Flash/Silverlight
    });

    uploader4.bind('FileUploaded', function(up, file, reponse) {
        var i = 0;
        $.each(reponse, function(data, value){
            if (i == 0) {
                var madata = $.parseJSON(value);
                if (madata.error) {
                    $('#progressgen4').html("");
                    alert("Erreur fileupload : " + madata.message);
                } 
                else {
                    $('#progressgen4').html("Chargement terminé");
                    $('#progressgen4').css('color', '#0000ff');
                    $('#filelist4').html(madata.fichier);
                    var chaine = "<img src='" + madata.source + "' /><br/><br/>";
                    $('#exist_image_name4').html(chaine);
                    $('#plupload4').hide(); 
                    $('#valid_plupload4').show();
                }
            }
            i++;
        });

    });
});