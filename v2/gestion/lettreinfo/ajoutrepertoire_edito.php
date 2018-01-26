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
								div_ligne.setAttribute("onclick","select("+les_lignes[i][0]+",this,parent)");
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
function select(no,ligne,d){
	//On ajoute le lien vers cette structure dans ckeditor
		//URL à travailler avec url_rewrite
	var xhr = getXhr();
		xhr.open("POST", "ajax/creer_url.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("t="+escape("structure")+"&n="+no);
	var reponse = eval("("+xhr.responseText+")");
	if(reponse[0]){
		var url = reponse[1];
		var a = '<a href="'+url+'" target="_blank">'+strip_tags(ligne.getElementsByTagName("div")[1].innerHTML)+'</a>';
		CKEDITOR.instances['edito'].insertHtml(a);
	}
	else{
		alert(reponse[1]);
	}
	d.$.colorbox.close();
}

function strip_tags(input, allowed) {
  allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
  return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
    return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
  });
}
</script>
<fieldset>
<label class="labellarge" for="input">Nom ou num&eacute;ro de la structure</label>&nbsp;:&nbsp;<input class="input" id="input" name="input" type="text" size="35" onkeyup="recherche(this)" />
<div id="liste_chargement" style="height:350px;overflow:hidden;"></div>
</fieldset>
