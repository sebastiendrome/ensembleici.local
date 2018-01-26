$(function() {
    var uploader2ter = new plupload.Uploader({
        runtimes : 'html5,flash',
        browse_button : 'browse2ter',
        container : 'plupload2ter',
        url : '../gestion/ajax/publicites/upload.php',
        flash_swf_url : '/plupload/plupload.flash.swf',
        multipart:true, 
        urlstream_upload:true,
        drop_element:'droparea2ter',
        multipart_params: {directory:'documents'},
        max_file_size:'5mb',
        filters : [
            {title : "Image files", extensions : "jpg,gif,png,jpeg"}
        ],
        resize : {width : 300, height : 600, quality : 100}
    });

    uploader2ter.init();

    uploader2ter.bind('FilesAdded', function(up, files) {
        $('#progressgen2ter').html("");
        $('#progressgen2ter').css('color', '#790000');
        var filelist2ter = $('#filelist2ter');
        for (var i in files) {
            var file = files[i];
        }
        if ((i>0) || $('#filelist2ter > *').length > 1)
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
            uploader2ter.start();
            uploader2ter.refresh(); 
        }
        
    });

    uploader2ter.bind('UploadProgress', function(up, file) {
        $('#progressgen2ter').html("Chargement en cours");
    });

    uploader2ter.bind('Error', function(up, err) {
        alert("Erreur bind :" + err.message);
        uploader2ter.refresh(); // Reposition Flash/Silverlight
    });

    uploader2ter.bind('FileUploaded', function(up, file, reponse) {
        var i = 0;
        $.each(reponse, function(data, value){
            if (i == 0) {
                var madata = $.parseJSON(value);
                if (madata.error) {
                    alert("Erreur fileupload : " + madata.message);
                } 
                else {
                    $('#progressgen2ter').html("Chargement terminé");
                    $('#progressgen2ter').css('color', '#0000ff');
                    $('#filelist2ter').html(madata.fichier);
                    var chaine = "<img src='" + madata.source + "'  style='max-height:200px;' /><br/><br/>";
                    $('#exist_image_name').html(chaine);
                }
            }
            i++;
        });

    });
});