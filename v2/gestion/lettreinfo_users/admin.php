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

$titrepage = "liste de diffusion";
if (isset($_GET['territoire'])) {
    $territoire = $_GET['territoire'];
}
else {
    $territoire = 1;
}

//On récupère la liste des communautés de commnues, ainsi que leurs villes "capitale".
$req_communaute_commune = "SELECT communautecommune.libelle,communautecommune.no_ville AS no FROM `communautecommune` WHERE territoires_id = ".$territoire;
$res_communaute_commune = $connexion->prepare($req_communaute_commune);
$res_communaute_commune->execute();
$tab_communaute_commune = $res_communaute_commune->fetchAll();

// Lignes à ajouter au header
$chemints=$root_site."js/jquery.tablesorter.min.js";
$ajout_header = <<<AJHE
<script type="text/javascript" src="$chemints"></script>
<script type="text/javascript" src="../../js/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../js/fonction_auto_presentation.js"></script>
<script>
	function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre Ã  jour");xhr=false;}return xhr;}
    $(function(){
    
	    $.tablesorter.defaults.widgets = ['zebra']; 
	    $(".tablesorter").tablesorter({
		sortList: [[0,0]],
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
	
	
	
	 // colorbox sur .iframe
	$(".ecrire_mail").live('click', function() {
		$.fn.colorbox({
		  href:"ecrire_mail.php?e="+escape(this.firstChild.data),
		  width:"550px",
		  onClose : function() { 
				CKEDITOR.instances["corps"].destroy();
		  },
		  onComplete : function() { 
				CKEDITOR.replace('corps',{toolbar:'NewsLetter',uiColor:'#F0EDEA',language:'fr',height:'300',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
				$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	
	// colorbox sur .iframe
	$("#ajouter_mail").live('click', function() {
		$.fn.colorbox({
		  href:"ajouter_mail.php?",
		  width:"610px",
		  onComplete : function() { 
				$(this).colorbox.resize();
		  }
		});
		return false; 
	});
	
	
	var NO_MODIF_COURANT;
	function click_btn_modif(cell,no){
		NO_MODIF_COURANT = no;
		var les_cellules = cell.parentNode.getElementsByTagName("td");
		var lien = les_cellules[0].firstChild;
		var email = lien.firstChild.data;
		var input = document.createElement("input");
			input.value = email;
			input.setAttribute("onkeyup","valide_email(this,event)");
			input.setAttribute("onblur","annule_email(this)");
		lien.parentNode.appendChild(input);
		input.select();
		lien.style.display = "none";
	}
	function valide_email(input,e){
		if(e.keyCode==13){
			var email = input.value;
			if(test_email_valide(email)){
				var lien = input.previousSibling;
				lien.firstChild.data = email;
				input.parentNode.removeChild(input);
				lien.style.display = "inline";
				var no = lien.id.split("_")[1];
				alert("Requête ajax modification BDD WHERE no="+no+" : email="+escape(email));
			}
			else{
				alert("adresse invalide !");
			}
		}
	}
	function annule_email(input){
		var lien = input.previousSibling;
		input.parentNode.removeChild(input);
		lien.style.display = "inline";
	}
	
	function prepare_modif_ville(span,no_courant,no_adresse){
		if(OLD_NO==0){
			OLD_NO = no_courant;
			var select = document.createElement("select");
				select.setAttribute("onchange","select_ville(this,"+no_adresse+");");
				select.setAttribute("onblur","annuler_select_ville(this);");
			for(i=0;i<COMMUNAUTE_COMMUNE_CAPITAL.length;i++){
				var option = document.createElement("option");
					option.value = COMMUNAUTE_COMMUNE_CAPITAL[i]["no"];
					option.appendChild(document.createTextNode(COMMUNAUTE_COMMUNE_CAPITAL[i]["libelle"].replace(/\+/gi," ")));
					if(COMMUNAUTE_COMMUNE_CAPITAL[i]["no"]==OLD_NO){
						option.setAttribute("selected","selected");
					}
				select.appendChild(option);
			}
			span.parentNode.appendChild(select);
			span.style.display = "none";
			select.focus();
		}
	}
	
	function select_ville(select,no_adresse){
		var span = select.previousSibling;
		NEW_NO = select.value;
		span.firstChild.data = select.options[select.selectedIndex].text;
		
		//On met à jour la bdd avec NEW_NO et no_adresse
		var xhr = getXhr();
			xhr.open("POST", "modif_ville_adresse.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("no_ville="+NEW_NO+"&no_adresse="+no_adresse);
		var reponse = eval("("+xhr.responseText+")");
		if(reponse){
			span.style.display = "inline";
			modif_evt_click(span,"prepare_modif_ville(this,"+NEW_NO+","+no_adresse+")");
			select.parentNode.removeChild(select);
			NEW_NO = 0;
			OLD_NO = 0;
		}
		else{
			select.blur();
			alert("une erreur est survenu, réessayez ...");
		}
	}
		function modif_evt_click(el,ligne_evt){
			el.onclick = function(){eval(ligne_evt);};
		}
	
	function annuler_select_ville(select){
		select.previousSibling.style.display = "inline";
		select.parentNode.removeChild(select);
		NEW_NO = 0;
		OLD_NO = 0;
	}
</script>
AJHE;

$ajout_header.= '<script type="text/javascript">var COMMUNAUTE_COMMUNE_CAPITAL = eval("("+unescape("'.urlencode(json_encode($tab_communaute_commune)).'")+")");var OLD_NO=0;var NEW_NO=0;</script>';

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
