/************************************************************************
* Prépare le fichier recherche_queries selon le type de champ demandé en*
* lui envoyant un ou deux mots clefs de recherche. Retourne les         *
* différents résultats en parsant l'XML de retour.                       *
************************************************************************/
function auto_recherche(keyword, keyword2, champs)
{
	var keyword2 = keyword2 || ""; //l'argument devient optionnel
	if (keyword == "" && keyword2 == "") 
		alert('Vous devez remplir au moins le premier champs');
	else
	{	
		var xhr = null;    
		if (window.XMLHttpRequest) 
		{ 
		    xhr = new XMLHttpRequest();
		}
	
		else if (window.ActiveXObject) 
		{
		    xhr = new ActiveXObject("Microsoft.XMLHTTP");
		}
	
		//on définit l'appel de la fonction au retour serveur
		xhr.onreadystatechange = function() 
		{ 
		    if(xhr.readyState == 4 && xhr.status == 200)
		    {
		    	list_results(xhr, keyword, keyword2, champs);
		    }
		    
		    else 
		    {	
		    	document.getElementById("resultats").innerHTML = "<img  src='./img/image-loader.gif' alt='load' />";
		    }
		};
		
		//on appelle le fichier .php en lui envoyant les variables en get
		xhr.open("GET", "./01_include/recherche_queries.php?champs="+champs+"&keyword="+keyword+"&keyword2="+keyword2, true);
		xhr.send(null);
	}
	return false;
}


/************************************************************************
* Parse le XML renvoyé et affiche les résultats                         *
************************************************************************/
function list_results(xhr, keyword, keyword2, champs)
{

	var keyword2 = keyword2 || ""; //l'argument devient optionnel
	var contents = ""; //contenu retour
	
	//récupération des noeuds XML
	var docXML= xhr.responseXML;
	var items = docXML.getElementsByTagName("item");
	
	// decompilation des resultats XML
	for (i=0;i<items.length;i++)
	{	
		//récupération des données
		notemp = items[i].getElementsByTagName('no')[0];
		no = notemp.childNodes[0];
		
		titretemp = items[i].getElementsByTagName('titre')[0];
		titre = titretemp.childNodes[0];
		
		autotemp = items[i].getElementsByTagName('auto')[0];
		auto = autotemp.childNodes[0];
		
		url_imgtemp = items[i].getElementsByTagName('url_img')[0];
		url_img = url_imgtemp.childNodes[0];
		
		//creation de l'url de la fiche
		var url = "auto_previsu.php?type="+champs;

		contents += "<form method='POST' target='_blank' action='"+url+"' class='formA'><fieldset>";
		contents += "<input name='no_fiche' value='"+no.nodeValue+"' type='hidden' />";
		contents += "<input name='auto_fiche' value='"+auto.nodeValue+"' type='hidden' />";
		contents += "<input name='type_fiche' value='"+champs+"' type='hidden' />";
		contents += "<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\"><tr><td>&nbsp;</td><td valign=\"middle\">"+titre.nodeValue+"<br/>"+auto.nodeValue+"</td></tr></table><br/>";
		//if (auto.nodeValue == 1) contents += "<p class='enlight'>fiche propriétaire</p><br/>";
		//else contents += "<p class='enlight'>fiche en libre accès</p><br/>";
		contents += "<center><button type='submit' class='boutonbleu ico-fleche'>Voir la Fiche</button></center>";
		contents += "</fieldset></form><br/><br/>";
	}
	
	//si retours vides
	if (champs=="evenement")
		champs_accentue = "évènement";
	else if (champs=="annonce")
	{
		champs_accentue = "stage / atelier / cours";
		champs = "evenement";
	}
	else if (champs=="structure")
		champs_accentue = champs;
	else if (champs=="petiteannonce")
	{
		champs_accentue = "petite annonce";
		champs = "petiteannonce";
	}
	
	if (contents == "") contents = "<p class='enlight'>Aucun R&eacute;sultat pour votre recherche, vous pouvez cr&eacute;er votre "+champs_accentue+".</p><br/>";
	
	//ajout lien création direct
	contents += "<div><form action='auto_"+champs+"_etape1.php' method='POST' name='creer' class='auto'>";
	contents +=	"<input type='hidden' name='no_fiche' value='0' />";
	contents +=	"<input type='hidden' name='no_fiche_temp' value='0' />";
	if (champs_accentue=="stage / atelier / cours")
		contents += "<input type='hidden' name='annonce' value='1' />";
	contents +=	"<input type='hidden' name='keyword' value=\""+keyword+"\" />";
	contents +=	"<input type='hidden' name='keyword2' value=\""+keyword2+"\" />";
	contents +=	"<center><button type='submit' class='boutonrouge ico-ajout-rge'>Cr&eacute;er votre "+champs_accentue+"</button></center>";
	contents +=	"</form></div>";
	
	document.getElementById("resultats").innerHTML = contents;
	
}