$(function() {
    //1. On récupère toutes les images du diaporama
    var post = {
    };
    var les_images = new Array();
    var url = $('#base_url').html() + '03_ajax/search_diaporama.php';
    $.ajaxSetup( { "async": false } );
    $.post(url, post, function(reponse){
        $.each(reponse, function(index, value) {
            les_images.push(value.nom);
        });
    }, 'json');

    //2. On les places dans la div diaporama.
    var diaporama = element("diaporama");
    for(var i=0;i<les_images.length;i++){
            var img = document.createElement("img");
                    img.src = 'img/diapo-index/' + les_images[i];
                    img.alt = "Ensemble ici";
            diaporama.appendChild(img);
    }
//    start_diaporama();
    setInterval(function () {
        set_opacity(element("diaporama").lastChild,0);
        setTimeout('element("diaporama").insertBefore(element("diaporama").lastChild,element("diaporama").firstChild);set_opacity(element("diaporama").firstChild,100)',1000);
    }, '5000');


//    function start_diaporama(){
//        alert('ccc');
//        //1. On réduit l'opacité du lastChild.
//        set_opacity(element("diaporama").lastChild,0);
//        //2. On passe le lastChild en firstChild
//        setTimeout('element("diaporama").insertBefore(element("diaporama").lastChild,element("diaporama").firstChild);set_opacity(element("diaporama").firstChild,100)',1000);
//        setTimeout('start_diaporama()',5000);
//    }
    
});

