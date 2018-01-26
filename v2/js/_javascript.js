function scrolling(){
	//On fait en sorte que la colonne1 (menu de gauche) reste toujours sur l'écran.
	var t_css = (!isNaN(parseInt(element("colonne1").style.top)))?parseInt(element("colonne1").style.top):30; //30 c'est la position top par défaut
	var t_defaut = haut("colonne1")+haut("contenu")-t_css;
	//var scroll_defaut = haut("colonne1")+haut("colonne1").parentNode-t_css;
	var scroll = (document.documentElement)?document.documentElement.scrollTop:document.body.scrollTop;
	if(scroll>t_defaut){
		var top = (scroll-t_defaut)+30;
		//var top_max = hauteur("hautpage");
		if(top+hauteur("colonne1")>hauteur("contenu"))
			top = hauteur("contenu")-hauteur("colonne1");
		element("colonne1").style.top = top+"px";
	}
	else{
		element("colonne1").style.top = 30+"px";
	}
	//On appelle l'éventuelle fonction existante sur une page.
	if(typeof(scrolling_page)=="function"){
		scrolling_page();
	}
}
function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre Ã   jour");xhr=false;}return xhr;}
function element(id){return document.getElementById(id);}
function hauteur(e){if(typeof(e)=="string")e=element(e);return e.offsetHeight;}
function largeur(e){if(typeof(e)=="string")e=element(e);return e.offsetWidth;}
function gauche(e){if(typeof(e)=="string")e=element(e);return e.offsetLeft;}
function haut(e){if(typeof(e)=="string")e=element(e);return e.offsetTop;}
function vide(e){if(typeof(e)=="string")e=element(e);if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}
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
function ajoute_evenement(el,ev,act){
	if(ev=="mouseover")
		el.onmouseover = function(){eval(act)};
	else if(ev=="mouseout")
		el.onmouseout = function(){eval(act)};
	else if(ev=="click")
		el.onclick = function(){eval(act)};
}
