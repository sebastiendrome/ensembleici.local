$(function() {
    var uploader2bis = new plupload.Uploader({
        runtimes : 'html5,flash',
        browse_button : 'browse2bis',
        container : 'plupload2bis',
        url : '../gestion/ajax/publicites/upload.php',
        flash_swf_url : '/plupload/plupload.flash.swf',
        multipart:true, 
        urlstream_upload:true,
        drop_element:'droparea2bis',
        multipart_params: {directory:'documents'},
        max_file_size:'5mb',
        filters : [
            {title : "Image files", extensions : "jpg,gif,png,jpeg"}
        ],
        resize : {width : 728, height : 90, quality : 100}
    });

    uploader2bis.init();

    uploader2bis.bind('FilesAdded', function(up, files) {
        $('#progressgen2bis').html("");
        $('#progressgen2bis').css('color', '#790000');
        var filelist2bis = $('#filelist2bis');
        for (var i in files) {
            var file = files[i];
        }
        if ((i>0) || $('#filelist2bis > *').length > 1)
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
            uploader2bis.start();
            uploader2bis.refresh(); 
        }
        
    });

    uploader2bis.bind('UploadProgress', function(up, file) {
        $('#progressgen2bis').html("Chargement en cours");
    });

    uploader2bis.bind('Error', function(up, err) {
        alert("Erreur bind :" + err.message);
        uploader2bis.refresh(); // Reposition Flash/Silverlight
    });

    uploader2bis.bind('FileUploaded', function(up, file, reponse) {
        var i = 0;
        $.each(reponse, function(data, value){
            if (i == 0) {
                var madata = $.parseJSON(value);
                if (madata.error) {
                    alert("Erreur fileupload : " + madata.message);
                } 
                else {
                    $('#progressgen2bis').html("Chargement terminé");
                    $('#progressgen2bis').css('color', '#0000ff');
                    $('#filelist2bis').html(madata.fichier);
                    var chaine = "<img src='" + madata.source + "'  style='max-height:200px;' /><br/><br/>";
                    $('#exist_image_name').html(chaine);
                }
            }
            i++;
        });

    });
});