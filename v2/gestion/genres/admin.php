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
