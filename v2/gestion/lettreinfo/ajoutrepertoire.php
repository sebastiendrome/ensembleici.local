<?php
/*****************************************************
Gestion des tags associés
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
$no_lettre = $_GET["no_lettre"];
?>

    <h3>Ajouter une structure</h3>
    
<script type="text/javascript" src="../../js/fonction_auto_presentation.js"></script>
<script type="text/javascript">
// function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre Ã  jour");xhr=false;}return xhr;}
// function vide(e){if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}
/*
	$(function() { 
		$('#submit').click(function() {
			$('#form-ajout-repertoire').hide(0);
			var formData = $('form#form-ajout-repertoire').serialize();
			$.ajax({
				type : 'POST',
				url : 'doajoutrepertoire.php',
				dataType : 'json',
				data: formData,
				success : function(data){
					// Fermer la colorbox
	  				parent.jQuery.fn.colorbox.close();
				},
				error:function (xhr, ajaxOptions, thrownError){
					alert(xhr.status);
					alert(thrownError);
				}
			});
			return false;
		});
	});


function list_results(xhr, keyword)
{
	var contents = ""; //contenu retour
	//récupération des noeuds XML
	var docXML= xhr.responseXML;
	var items = docXML.getElementsByTagName("item");
	// decompilation des resultats XML
	contents += "<table border='0' cellpadding='3' cellspacing='3'>";
	for (i=0;i<items.length;i++)
	{	
		if(i%3==0 || i==0)
		{
			if(i>0)
			{
				contents += "</tr>";
			}
			contents += "<tr>";
		}
		//récupération des données
		notemp = items[i].getElementsByTagName('no')[0];
		no = notemp.childNodes[0];
		titretemp = items[i].getElementsByTagName('titre')[0];
		titre = titretemp.childNodes[0];
		contents += "<td><input name='sous_tag[]' value='"+no.nodeValue+"' type='checkbox' /> "+titre.nodeValue.charAt(0).toUpperCase() + titre.nodeValue.substring(1).toLowerCase()+"<br/></td>";
	}
	
	contents += "</table>";
	document.getElementById("resultats").innerHTML = contents;

	// Redimmensionne la box
	$('#ajoutrepertoire').colorbox.resize();

}
*/
var XHR_SAM = false;
function recherche(input){
	if(XHR_SAM!=false){
		XHR_SAM.abort();
		XHR_SAM = false;
	}
	var pos_y = document.getElementById("colorbox").offsetTop;
	var zone = document.getElementById("liste_chargement");
		zone.style.offsetTop = pos_y+"px";
	//On récupère la valeur de l'input.
	var v = input.value;
	if(!isNaN(v)&&(parseInt(v)+"").length==(v+"").length)
		var n=1;
	else
		var n=0;
	XHR_SAM = getXhr();
		XHR_SAM.onreadystatechange = function(){
			if(XHR_SAM.readyState == 4){
				if(XHR_SAM.status == 200){
					var les_lignes = eval("("+XHR_SAM.responseText+")");
					vide(zone);
					if(les_lignes[0]!=false){
						var hr = document.createElement("hr");
							hr.style.margin = 1+"px";
						zone.appendChild(hr);
						for(i=0;i<les_lignes.length;i++){
							var div_ligne = document.createElement("div");
								div_ligne.style.padding = 8+"px";
								div_ligne.style.cursor = "default";
								div_ligne.setAttribute("onmouseover","this.style.backgroundColor='#f0edea'");
								div_ligne.setAttribute("onmouseout","this.style.backgroundColor='transparent'");
								div_ligne.setAttribute("onclick","select("+les_lignes[i][0]+",parent)");
								div_ligne.innerHTML = les_lignes[i][1];
							zone.appendChild(div_ligne);
							zone.appendChild(hr.cloneNode(true));
						}
					}
					else{
						zone.appendChild(document.createTextNode("aucune correspondance"));
					}
				}
			}
		};
	XHR_SAM.open("POST", "ajax/recherche_repertoire.php", true);
	XHR_SAM.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	XHR_SAM.send("m="+escape(v)+"&n="+n);
}
function select(no,d){
	//On ajoute no à la liste des répertoires de la semaine
	var xhr = getXhr();
		xhr.open("POST", "ajax/modif_liste_complete.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("e=structure&no="+no+"&act=add&no_l=<?php echo $no_lettre; ?>");
	var reponse = eval("("+xhr.responseText+")");
	if(reponse)
		d.$.colorbox.close();
	else
		alert("une erreur est survenue");
}
</script>
<fieldset>
<label class="labellarge" for="input">Nom ou num&eacute;ro de la structure</label>&nbsp;:&nbsp;<input class="input" id="input" name="input" type="text" size="35" onkeyup="recherche(this)" />
<div id="liste_chargement" style="height:350px;overflow:hidden;"></div>
</fieldset>
