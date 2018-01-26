function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre Ã   jour");xhr=false;}return xhr;}
function element(id){return document.getElementById(id);}
function hauteur(e){if(typeof(e)=="string")e=element(e);return (e!=null)?e.offsetHeight:0;}
function largeur(e){if(typeof(e)=="string")e=element(e);return (e!=null)?e.offsetWidth:0;}
function gauche(e){if(typeof(e)=="string")e=element(e);return (e!=null)?e.offsetLeft:0;}
function haut(e){if(typeof(e)=="string")e=element(e);return (e!=null)?e.offsetTop:0;}
function vide(e){if(typeof(e)=="string")e=element(e);if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}
function set_attribute(e,a,v){e.setAttribute(a,v);}
function time(){
	var d = new Date();
	return d.getTime();
}
// function ecran(){if(document.body){return {"x":(document.body.clientWidth),"y":(document.body.clientHeight)};}else{return {"x":(window.innerWidth),"y":(window.innerHeight)};}}
function ecran(){return {"x":(window.innerWidth||document.body.clientWidth),"y":(window.innerHeight||document.body.clientHeight)};}
function style(e,s,v){
	if(typeof(e)=="string")
		var f="document.getElementById('"+e+"').style."+s+"='"+v+"'";
	else
		var f="document.getElementById('"+e.id+"').style."+s+"='"+v+"'";
	eval(f);
}
function documentScroll(){ return {"x":(document.documentElement && document.documentElement.scrollLeft) || window.pageXOffset || self.pageXOffset || document.body.scrollLeft,"y":(document.documentElement && document.documentElement.scrollTop) || window.pageYOffset || self.pageYOffset || document.body.scrollTop};}
function set_documentScroll(y,x){
	if(typeof(x)=="undefined") x = 0;
	if(document.documentElement&&document.documentElement.scrollTop){ //Tout
		document.documentElement.scrollTop = y;
		document.documentElement.scrollLeft = x;
	}
	else if(window.scroll){ //Chrome
		window.scroll(x,y);
	}
	else{
		window.scrollTo(x,y,'smooth');
	}
		
}
function est_dans_tab(v,t){
	var i = 0;
	while(i<t.length&&t[i]!=v){
		i++
	}
	return !(i==t.length);
}
function tab_index(v,t){
	var i = 0;
	while(i<t.length&&t[i]!=v){
		i++
	}
	return (i<t.length)?i:-1;
}
function array_merge(tab1,tab2){
	var tab = new Array();
	for(var i=0;i<tab1.length;i++){
		tab[tab.length] = tab1[i];
	}
	for(var i=0;i<tab2.length;i++){
		tab[tab.length] = tab2[i];
	}
	return tab;
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
		return (el.style["filter"].split("=")[1].split(")")[0]); //Ou sinon on enlÃ¨ve le dernier caractÃ¨re
	else if(typeof(el.style["-moz-opacity"])!="undefined"&&el.style["-moz-opacity"]!="")
		return (el.style["-moz-opacity"]*100);
	else if(typeof(el.style["-khtml-opacity"])!="undefined"&&el.style["-khtml-opacity"]!="")
		return (el.style["-khtml-opacity"]*100);
	else if(typeof(el.style["opacity"])!="undefined"&&el.style["opacity"]!="")
		return (el.style["opacity"]*100);
		// return "cono";
}

function dans_element(el,p){
	return (el!=null)?((el!=p)?dans_element(el.parentNode,p):true):false;
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
	var _i;
	for(_i=0;_i<tab.length;_i++){
		tab_return[tab_return.length]=tab[_i];
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
			//On ne l'Ã©tire pas, on la centre.
		photo.style.top = hauteurCadre/2 - hauteurPhoto/2+"px";
		photo.style.left = largeurCadre/2 - largeurPhoto/2+"px";
		//On rÃ¨gle maintenant la largeur
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
				//L'image est a le mÃªme rapport que le cadre:
					//On la retreci Ã  la taille du cadre.
					photo.style.height = hauteurCadre+"px";
					photo.style.width = largeurCadre+"px";
					photo.style.top = 0+"px";
					photo.style.left = 0+"px";
			}
	}
	photo.style.visibility = "visible";
	cadre.style.backgroundColor = "transparent";
}

function date_fr(d){
	var d_temp = d.split(" ");
	if(d_temp.length>1) //date et heure
		var h = d_temp[1];
	else
		var h = false;
	d_temp = d_temp[0].split("-");
	if(h!=false)
		var date = d_temp[2]+"/"+d_temp[1]+"/"+d_temp[0]+" Ã  "+h;
	else
		var date = d_temp[2]+"/"+d_temp[1]+"/"+d_temp[0];
	return date;
}

function date_sql(d){
	var d_temp = d.split(" Ã  ");
	if(d_temp.length>1){ //date et heure
		var h = d_temp[1];
	}
	else{
		var h = false;
	}
	d_temp = d_temp[0].split("/");
	if(h!=false)
		var date = d_temp[2]+"-"+d_temp[1]+"-"+d_temp[0]+" "+h;
	else
		var date = d_temp[2]+"-"+d_temp[1]+"-"+d_temp[0];
	return date;
}

function ajoute_evenement(el,ev,act){
	var reg_ev = new RegExp("event","gi");
	if(reg_ev.test(act)){
		act = act.replace(reg_ev,"e");
		if(ev=="mouseover")
			el.onmouseover = function(e){eval(act)};
		else if(ev=="mouseout")
			el.onmouseout = function(e){eval(act)};
		else if(ev=="click")
			el.onclick = function(e){eval(act)};
		else if(ev=="change")
			el.onchange = function(e){eval(act)};
		else if(ev=="blur")
			el.onblur = function(e){eval(act)};
		else if(ev=="keyup")
			el.onkeyup = function(e){eval(act)};
	}
	else{
		if(ev=="mouseover")
			el.onmouseover = function(){eval(act)};
		else if(ev=="mouseout")
			el.onmouseout = function(){eval(act)};
		else if(ev=="click")
			el.onclick = function(){eval(act)};
		else if(ev=="change")
			el.onchange = function(){eval(act)};
		else if(ev=="blur")
			el.onblur = function(){eval(act)};
		else if(ev=="keyup")
			el.onkeyup = function(e){eval(act)};
	}
}

function ajoute_evenement2(el,type,act){
	el.addEventListener(type, function(){ eval(act); }, false); 
}

function getByClassName(el,name){
	var e = el.childNodes;
	// alert(e.length);
	var i = 0;
	var continuer = true;
	while(i<e.length&&continuer){
		// alert(e[i].className);
		if(typeof(e[i].className)!="undefined"&&dans_tab(name,e[i].className.split(" ")))
			continuer = false;
		else
			i++;
	}
	return (i<e.length)?e[i]:false;
}
function touche_entree(e){
	if(e.keyCode==13)
		return true;
	else
		return false;
}
function analyse_reponse_ajax(rep){
	if(rep["erreur"]==1){
		affiche_filtre_connexion();
		retire_filtre_chargement();
	}
	return (rep["erreur"]==0);
}
function writeInConsole (text) {
    if (typeof console !== 'undefined') {
        console.log(text);    
    }
    else {
        alert(text);    
    }
}
function getScrollPosition(){
	return {"x":((document.documentElement && document.documentElement.scrollLeft) || window.pageXOffset || self.pageXOffset || document.body.scrollLeft),"y":((document.documentElement && document.documentElement.scrollTop) || window.pageYOffset || self.pageYOffset || document.body.scrollTop)};
}
function getMousePosition(event){
	var e = event || window.event;
	var scroll = getScrollPosition();
	return {"x":(e.clientX + scroll["x"] - document.body.clientLeft),"y":(e.clientY + scroll["y"] - document.body.clientTop)};
}
function offsetAbs(element) {
    var top = 0, left = 0;
    do {
        top += element.offsetTop  || 0;
        left += element.offsetLeft || 0;
        element = element.offsetParent;
    } while(element);

    return {
        "top": top,
        "left": left
    };
}
/****
COMPATIBILITE I.E.
http://www.miasmatech.net/scripts/accueil/permalink.php?post_id=33
****/
function getChildIndex(el){
	var p = el.parentNode.childNodes;
	var i = 0;
	while(i<p.length&&p[i]!=el){
		i++;
	}
	return (i<p.length)?i:false;
}
function getStyle(oElm, strCssRule){
    var strValue = "";
    if(document.defaultView && document.defaultView.getComputedStyle){
        strValue = document.defaultView.getComputedStyle(oElm, "").getPropertyValue(strCssRule);
    }
    else if(oElm.currentStyle){
        strCssRule = strCssRule.replace(/\-(\w)/g, function (strMatch, p1){
            return p1.toUpperCase();
        });
        strValue = oElm.currentStyle[strCssRule];
    }
    return strValue;
}
function apply_transform(el,transform){
	el.style.webkitTransform = transform;
	el.style.MozTransform = transform;
	el.style.msTransform = transform;
	el.style.OTransform = transform;
	el.style.transform = transform;
}
function tableau(t){
	var tf = new Array();
	for(var it=0;it<t.length;it++){
		tf[tf.length] = t[it];
	}
	return tf;
}

function createCSSSelector(selector, style){
    if(!document.styleSheets) {
        return;
    }

    if(document.getElementsByTagName("head").length == 0) {
        return;
    }

    var stylesheet;
    var mediaType;
    if(document.styleSheets.length > 0) {
        for( i = 0; i < document.styleSheets.length; i++) {
            if(document.styleSheets[i].disabled) {
                continue;
            }
            var media = document.styleSheets[i].media;
            mediaType = typeof media;

            if(mediaType == "string") {
                if(media == "" || (media.indexOf("screen") != -1)) {
                    styleSheet = document.styleSheets[i];
                }
            } else if(mediaType == "object") {
                if(media.mediaText == "" || (media.mediaText.indexOf("screen") != -1)) {
                    styleSheet = document.styleSheets[i];
                }
            }

            if( typeof styleSheet != "undefined") {
                break;
            }
        }
    }

    if( typeof styleSheet == "undefined") {
        var styleSheetElement = document.createElement("style");
        styleSheetElement.type = "text/css";

        document.getElementsByTagName("head")[0].appendChild(styleSheetElement);

        for( i = 0; i < document.styleSheets.length; i++) {
            if(document.styleSheets[i].disabled) {
                continue;
            }
            styleSheet = document.styleSheets[i];
        }

        var media = styleSheet.media;
        mediaType = typeof media;
    }

    if(mediaType == "string") {
        for( i = 0; i < styleSheet.rules.length; i++) {
            if(styleSheet.rules[i].selectorText && styleSheet.rules[i].selectorText.toLowerCase() == selector.toLowerCase()) {
                styleSheet.rules[i].style.cssText = style;
                return;
            }
        }

        styleSheet.addRule(selector, style);
    } else if(mediaType == "object") {
        for( i = 0; i < styleSheet.cssRules.length; i++) {
            if(styleSheet.cssRules[i].selectorText && styleSheet.cssRules[i].selectorText.toLowerCase() == selector.toLowerCase()) {
                styleSheet.cssRules[i].style.cssText = style;
                return;
            }
        }

        styleSheet.insertRule(selector + "{" + style + "}", styleSheet.cssRules.length);
    }
}

function somme_scrollTop(el,fin){
	var fin_secure = (document.body!=null)?document.body:document.documentElement;
	alert(fin_secure);
	if(typeof(fin)=="undefined")
		fin = fin_secure;
	var somme = 0;
	while(el!=fin_secure&&el!=fin){
		somme += el.scrollTop;
		el = el.parentNode;
	}
	somme += el.parentNode.scrollTop;
	return somme;
}



