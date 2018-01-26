function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre à  jour");xhr=false;}return xhr;}
function vide(e){if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}

function modif_etat(id){
	
	var cible = document.getElementById(id);
	var etat = cible.getAttribute("rel");
	//alert(etat);
	var action = "act";	
	if (etat=="1") {action = "desact";}
	var xhr = getXhr();	
	
	xhr.open("GET", "01_include/espace_active_petiteannonce.php?id="+id+"&action="+action, false);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xhr.send(null);	
	
	if (cible != null){
		if (action=="act"){
			cible.firstChild.data = "Désactiver";
			cible.setAttribute("rel",1);
		}
		else{
			cible.firstChild.data = "Activer";	
			cible.setAttribute("rel",0);
		}		
	}	
	return false;	
}		

function element(id){return document.getElementById(id);}

function set_opacity(id, opacity){
    if(typeof(id)=="string")
        el = element(id);
    else
        el = id;
    el.style["filter"] = "alpha(opacity="+opacity+")";
    el.style["-moz-opacity"] = opacity/100;
    el.style["-khtml-opacity"] = opacity/100;
    el.style["opacity"] = opacity/100;
    return true;
}

function get_opacity(id){
    if(typeof(id)=="string")
        el = element(id);
    else
        el = id;
    if(typeof(el.style["filter"])!="undefined"&&el.style["filter"]!="")
        return (el.style["filter"].split("=")[1].split(")")[0]); //Ou sinon on enlève le dernier caractère
    else if(typeof(el.style["-moz-opacity"])!="undefined"&&el.style["-moz-opacity"]!="")
        return (el.style["-moz-opacity"]*100);
    else if(typeof(el.style["-khtml-opacity"])!="undefined"&&el.style["-khtml-opacity"]!="")
        return (el.style["-khtml-opacity"]*100);
    else if(typeof(el.style["opacity"])!="undefined"&&el.style["opacity"]!="")
        return (el.style["opacity"]*100);
}