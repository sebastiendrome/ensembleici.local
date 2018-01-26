$(function() {
    var uploader2 = new plupload.Uploader({
        runtimes : 'html5,flash',
        browse_button : 'browse2',
        container : 'plupload2',
        url : '../gestion/ajax/publicites/upload.php',
        flash_swf_url : '/plupload/plupload.flash.swf',
        multipart:true, 
        urlstream_upload:true,
        drop_element:'droparea2',
        multipart_params: {directory:'documents'},
        max_file_size:'5mb',
        filters : [
            {title : "Image files", extensions : "jpg,gif,png,jpeg"}
        ],
        resize : {width : 300, height : 250, quality : 100}
    });

    uploader2.init();

    uploader2.bind('FilesAdded', function(up, files) {
        $('#progressgen2').html("");
        $('#progressgen2').css('color', '#790000');
        var filelist2 = $('#filelist2');
        for (var i in files) {
            var file = files[i];
        }
        if ((i>0) || $('#filelist2 > *').length > 1)
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
            uploader2.start();
            uploader2.refresh(); 
        }
        
    });

    uploader2.bind('UploadProgress', function(up, file) {
        $('#progressgen2').html("Chargement en cours");
    });

    uploader2.bind('Error', function(up, err) {
        alert("Erreur bind :" + err.message);
        uploader2.refresh(); // Reposition Flash/Silverlight
    });

    uploader2.bind('FileUploaded', function(up, file, reponse) {
        var i = 0;
        $.each(reponse, function(data, value){
            if (i == 0) {
                var madata = $.parseJSON(value);
                if (madata.error) {
                    alert("Erreur fileupload : " + madata.message);
                } 
                else {
                    $('#progressgen2').html("Chargement terminé");
                    $('#progressgen2').css('color', '#0000ff');
                    $('#filelist2').html(madata.fichier);
                    var chaine = "<img src='" + madata.source + "'  style='max-height:200px;' /><br/><br/>";
                    $('#exist_image_name').html(chaine);
                }
            }
            i++;
        });

    });
});