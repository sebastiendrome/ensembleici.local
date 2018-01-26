<?php
/*****************************************************
Gestion des tags
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Affichage du message s'il existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);

// Lignes à ajouter au header
$chemints=$root_site."js/jquery.tablesorter.min.js";
$ajout_header = <<<AJHE
<script type="text/javascript" src="$chemints"></script>
<script>
    $(function(){
    
	    $.tablesorter.defaults.widgets = ['zebra']; 
	    $(".tablesorter").tablesorter({
		sortList: [[1,0]],
		headers: {5:{sorter: false}}
	    });

	    $(".delete").click(function() {
                if($('#modesupp').is(':checked')) {
                    if(confirm("Etes-vous sur de vouloir supprimer $cc_cettemin et toutes ses associations ?" )) {
                        var id = $(this).attr("id");
                        delete_item(id);
                    }
                } else {
                    return false;
                }
	    });
	    
	    function delete_item(id)
	    {
		    $('#ajax-supp').fadeIn();
		    var commentContainer = $("#"+id).parents('tr:first');
		    var string = "no=" + id;
		    
		    $.ajax({
		       type: "POST",
		       url: "supp.php",
		       data: string,
		       cache: false,
		       success: function(retour){
			    $('#ajax-supp').fadeOut();
                            if (retour=="ok") {
                                commentContainer.fadeOut("slow", function(){\$(this).remove();} );
                            }
                            $(".message").load("../inc-message.php");
                            $('.message').slideDown("slow");
		      }
		    });
		    return false;
	    }
    });

    function confirm_supp(url){
      if(confirm("Etes-vous sur de vouloir supprimer $cc_cettemin ?" )) document.location.href = url;
      return false; 
    }
	
	//Ajouté par Sam, pour inscription/désinscription à la newsletter
	function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre Ã  jour");xhr=false;}return xhr;}
	var CHANGEMENT_COURANT = new Array();
	function change_abonnement(btn,no){
		if(typeof(CHANGEMENT_COURANT[no])=="undefined"||!CHANGEMENT_COURANT[no]){
			CHANGEMENT_COURANT[no] = true;
			var nouv_src = btn.src.substring(0,btn.src.lastIndexOf("_")+1);
			var on_off = btn.src.substring(btn.src.lastIndexOf("/")+1,btn.src.length);
				on_off = on_off.substring(0,on_off.lastIndexOf(".")).split("_")[1];
			if(on_off=="on"){
				//On désactive no
				v=0;
				nouv_src+="off.png"
			}
			else{
				//On active no
				v=1;
				nouv_src+="on.png"
			}
			var xhr = getXhr();
				xhr.open("POST", "modif_abonnement_nl.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send("v="+v+"&n="+no);
			if(xhr.responseText=="true")
				btn.src = nouv_src;
			else
				alert("une erreur est survenue !");
			CHANGEMENT_COURANT[no] = false;
		}
	}
</script>
AJHE;

include "../inc-header.php";
?>

<div id="ajax-supp">
	<img src="../../img/image-loader.gif" alt="Suppression en cours" /><br/>
	Suppression en cours...
</div>


<?php

include ('aff.php');

include "../inc-footer.php";
?>
