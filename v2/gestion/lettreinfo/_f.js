function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre à  jour");xhr=false;}return xhr;}
function element(id){return document.getElementById(id);}
function hauteur(e){if(typeof(e)=="string")e=element(e);return e.offsetHeight;}
function largeur(e){if(typeof(e)=="string")e=element(e);return e.offsetWidth;}
function gauche(e){if(typeof(e)=="string")e=element(e);return e.offsetLeft;}
function haut(e){if(typeof(e)=="string")e=element(e);return e.offsetTop;}
function vide(e){if(typeof(e)=="string")e=element(e);if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}
function set_attribute(e,a,v){e.setAttribute(a,v);}
function ecran(){if(document.body){return {"x":(document.body.clientWidth),"y":(document.body.clientHeight)};}else{return {"x":(window.innerWidth),"y":(window.innerHeight)};}}
function style(e,s,v){
	if(typeof(e)=="string")
		var f="document.getElementById('"+e+"').style."+s+"='"+v+"'";
	else
		var f="document.getElementById('"+e.id+"').style."+s+"='"+v+"'";
	eval(f);
}
function scroll(){ return {"x":(document.documentElement && document.documentElement.scrollLeft) || window.pageXOffset || self.pageXOffset || document.body.scrollLeft,"y":(document.documentElement && document.documentElement.scrollTop) || window.pageYOffset || self.pageYOffset || document.body.scrollTop};}
function est_dans_tab(v,t){
	var i = 0;
	while(i<t.length&&t[i]!=v){
		i++
	}
	return !(i==t.length);
}
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
	// alert(typeof(el.style["filter"]));
	// alert(el.style["filter"]);
	if(typeof(el.style["filter"])!="undefined"&&el.style["filter"]!="")
		return (el.style["filter"].split("=")[1].split(")")[0]); //Ou sinon on enlève le dernier caractère
	else if(typeof(el.style["-moz-opacity"])!="undefined"&&el.style["-moz-opacity"]!="")
		return (el.style["-moz-opacity"]*100);
	else if(typeof(el.style["-khtml-opacity"])!="undefined"&&el.style["-khtml-opacity"]!="")
		return (el.style["-khtml-opacity"]*100);
	else if(typeof(el.style["opacity"])!="undefined"&&el.style["opacity"]!="")
		return (el.style["opacity"]*100);
		// return "cono";
}

function dans_tab(val,tab){
	var i = 0;
	while(i<tab.length&&tab[i]!=val){
		i++;
	}
	return !(i==tab.length);
}

function dans_liste(v,l,s){
	if(typeof(s)=="undefined")
		s = ",";
	return (dans_tab(v,l.split(s)));
}

function del_liste(v,l,s){
	if(typeof(s)=="undefined")
		s = ",";
	return (del_tab(v,l.split(s)).join(s));
}

function add_liste(v,l,s){
	if(typeof(s)=="undefined")
		s = ",";
	if(!dans_liste(v,l,s))
		return l+s+v;
	else
		return l;
}

function size_liste(l,s){
	if(typeof(s)=="undefined")
		s = ",";
	return l.split(s).length;
}

function del_tab(val,tab){
	var tab_2 = new Array();
	var i = 0;
	while(i<tab.length&&tab[i]!=val){
		tab_2[tab_2.length] = tab[i];
		i++;
	}
	for(j=i+1;j<tab.length;j++){
		tab_2[tab_2.length] = tab[j];
	}
	return tab_2;
}
function copier_tab(tab){
	var tab_return = new Array();
	for(i=0;i<tab.length;i++){
		tab_return[tab_return.length]=tab[i];
	}
	return tab_return;
}
function placePhotoDansCadre(photo,cadre){
	cadre.style.position = "relative";
	cadre.style.overflow = "hidden";
	photo.style.position = "absolute";
	photo.style.visibility = "hidden";
	var hauteurPhoto = photo.offsetHeight;
	var largeurPhoto = photo.offsetWidth;
	var hauteurCadre = cadre.offsetHeight;
	var largeurCadre = cadre.offsetWidth;
	var rapportHauteur = hauteurCadre/hauteurPhoto;
	var rapportLargeur = largeurCadre/largeurPhoto;
	if(rapportHauteur>1){
		//L'image est alors plus petite que le cadre sur la hauteur:
			//On ne l'étire pas, on la centre.
		photo.style.top = hauteurCadre/2 - hauteurPhoto/2+"px";
		photo.style.left = largeurCadre/2 - largeurPhoto/2+"px";
		//On règle maintenant la largeur
		/*if(rapportLargeur>1){
			//L'image est alors plus large que le cadre
				//On la rogne sur la largeur
			photo.style.left = largeurCadre/2 - largeurPhoto/2+"px";
		}
		else if (rapportLargeur<1){
			//L'image est moins large que le cadre
				//On la centre sur la largeur
				photo.style.left = largeurCadre/2 - largeurPhoto/2+"px";
		}
		else{
			
		}*/
	}
	else{
		//L'image est alors plus grande que le cadre sur la hauteur:
			//On peut alors selon la largeur la rogner, ou la retrecir sur la hauteur
			if(rapportHauteur>rapportLargeur){
				//L'image est alors plus large que longue:
					//On la retreci sur la hauteur puis on la rogne sur la largeur
				photo.style.height = hauteurCadre+"px";
				largeurPhoto = photo.offsetWidth;
				photo.style.left = largeurCadre/2 - largeurPhoto/2+"px";
				photo.style.top = 0+"px";
			}
			else if(rapportHauteur<rapportLargeur){
				//L'image est alors plus haute que large:
					//On la retreci sur la largeur puis on la rogne sur la hauteur
				photo.style.width = largeurCadre+"px";
				hauteurPhoto = photo.offsetHeight;
				photo.style.top = hauteurCadre/2 - hauteurPhoto/2+"px";
				photo.style.left = 0+"px";
			}
			else{
				//L'image est a le même rapport que le cadre:
					//On la retreci à la taille du cadre.
					photo.style.height = hauteurCadre+"px";
					photo.style.width = largeurCadre+"px";
					photo.style.top = 0+"px";
					photo.style.left = 0+"px";
			}
	}
	photo.style.visibility = "visible";
	cadre.style.backgroundColor = "transparent";
}