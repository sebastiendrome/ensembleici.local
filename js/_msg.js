/******
Cette fonction permet d'afficher un message Ã  l'Ã©cran.
elle fonctionne avec le fichier _msg.css et le fichier _f.js
******/
var _MESSAGE_ZINDEX = 105;
function message(contenu,duree){
	if(typeof(duree)=="object"){ //C'est un tableau d'informations
		var infos = duree;
		duree = (typeof(infos["duree"])=="undefined")?0:infos["duree"];
		if(typeof(infos["pos"])!="undefined"){
			if(typeof(infos["pos"]["x"])!="undefined")
				var x = infos["pos"]["x"];
			if(typeof(infos["pos"]["y"])!="undefined")
				var y = infos["pos"]["y"];
		}
		if(typeof(infos["width"])!="undefined"){
			var width = infos["width"];
		}
		if(typeof(infos["max-width"])!="undefined"){
			var maxWidth = infos["max-width"];
		}
		var supprimer_filtre = (typeof(infos["filtre"])!="undefined"&&infos["filtre"])?true:false;
		var blocage_id = (typeof(infos["id"])!="undefined"&&element(infos["id"])!=null)?true:false;
		var premier_plan = (typeof(infos["devant"])!="undefined"&&infos["devant"])?true:false;
	}
	else if(typeof(duree)!="integer"){
		var duree = 3.5;
		var infos = false;
	}
	else
		infos = false;
	
	if(!blocage_id){
		duree *= 1000; //seconde->milliseconde
	
		var time = new Date().getTime();
		var id_msg = (typeof(infos["id"])=="undefined")?("message_"+time):infos["id"];
	
		var div = document.createElement("div");
			div.onmouseover = function(){this.style.zIndex=_MESSAGE_ZINDEX++;};
			if(typeof(contenu)=="string")
				div.innerHTML = '<div>'+decodeURIComponent(contenu)+'</div>';
			else if(typeof(contenu)=="object")
				div.appendChild(contenu);
			
			div.className = "message invisible"+((typeof(infos["class"])!="undefined"&&infos["class"]!=null&&infos["class"]!="")?' '+infos["class"]:'');
			div.id = id_msg;
			div.style.zIndex = _MESSAGE_ZINDEX++;
			
		document.body.appendChild(div);
	
		if(typeof(infos["ne_pas_fermer"])=="undefined"||!infos["ne_pas_fermer"]){
			var img_croix = document.createElement("img");
				img_croix.className = "fermer";
				img_croix.src = "img/img_colorize.php?uri=ico_delete.png&c=133,144,151";
				img_croix.onmouseover = function(){this.src="img/img_colorize.php?uri=ico_delete.png&c=FE0000";};
				img_croix.onmouseout = function(){this.src="img/img_colorize.php?uri=ico_delete.png&c=133,144,151";};
				ajoute_evenement(img_croix,"click",'supprime_message("'+id_msg+'"'+((supprimer_filtre)?",true":"")+')');
			div.appendChild(img_croix);
		}
		else
			var img_croix = null;
	
	
		if(infos!=false){
			if(typeof(infos["btn"])!="undefined"){
				var les_btns = infos["btn"];
				var div_btn = document.createElement("div");
					div.firstChild.appendChild(div_btn);
				for(var i=0;i<les_btns.length;i++){
					var input = document.createElement("input");
						input.type = "button";
						input.value = les_btns[i]["value"];
						if(typeof(les_btns[i]["class"])!="undefined")
							input.className = les_btns[i]["class"];
						les_btns[i]["click"] = les_btns[i]["click"].replace(/(fermer|close|quitter|exit|quit)/gi, "supprime_message('"+id_msg+"'"+((supprimer_filtre)?",true":"")+")");
						/*if(=="fermer"||les_btns[i]["click"]=="close"||les_btns[i]["click"]=="exit"||les_btns[i]["click"]=="quit"||les_btns[i]["click"]=="quitter")
							les_btns[i]["click"]="supprime_message('"+id_msg+"')";*/
						ajoute_evenement(input,"click",les_btns[i]["click"]);
					div_btn.appendChild(input);
				}
			}
			
			if(duree>0)
				div.className += " fadeOver";
			else{
				if(img_croix!=null){
					img_croix.onmouseover = function(){this.src="img/img_colorize.php?uri=ico_delete.png&c=FE0000";set_opacity(this.parentNode,90);};
					img_croix.onmouseout = function(){this.src="img/img_colorize.php?uri=ico_delete.png&c=133,144,151";set_opacity(this.parentNode,100);};
					img_croix.onclick = function(){supprime_message(this.parentNode.id,supprimer_filtre);this.onmouseout=function(){this.src="img/img_colorize.php?uri=ico_delete.png&c=133,144,151";}};
				}
			}
			
			if(premier_plan){
				div.style.zIndex=_MESSAGE_ZINDEX+1000;
				div.onmouseover = null;
			}
			
		}
		else{
			div.className += " fadeOver";
		}
		
		
		if(typeof(width)!="undefined"){
			div.style.width = width+"px";
			div.style.maxWidth = 100+"%";
		}
		
		if(typeof(maxWidth)!="undefined"){
			div.style.maxWidth = maxWidth+"px";
			div.style.width = 100+"%";
		}
	
		if(typeof(x)=="undefined"){
			var x = (ecran()["x"]/2-largeur(div)/2);
		}
		else
			x -= largeur(div)/2;
	
		if(typeof(y)=="undefined"){
			var y = getScrollPosition()["y"]+(ecran()["y"]/2-hauteur(div)/2);
		}
		else
			y -= hauteur(div)/2;
		
		if(y<getScrollPosition()["y"]+10)
			y=getScrollPosition()["y"]+10;
		else if(y>getScrollPosition()["y"]+ecran()["y"]-hauteur(div)-10)
			y = getScrollPosition()["y"]+ecran()["y"]-hauteur(div)-10;
		if(x<10)
			x = 10;
		else if(x>ecran["x"]-largeur(div)-10)
			x = ecran["x"]-largeur(div)-10;
		
		//div.style.top = ecran()["y"]+100+"px";
		div.style.top = -ecran()["y"]-100+"px";
		div.style.left = x+"px";
	
		setTimeout("affiche_message('"+id_msg+"',"+duree+","+y+")",200);
	}
	else{
		clignotter_message(infos["id"]);
	}
}

function affiche_message(id,duree,y){
	deplace_message(id,y);
	element(id).className = element(id).className.replace(" invisible","");
	if(duree>0)
		setTimeout("supprime_message('"+id+"')",duree);
}

var TIMEOUT_SUPPRESSION_MESSAGE = new Array();
function supprime_message(id,supprimer_filtre){
	set_opacity(element(id),0);
	TIMEOUT_SUPPRESSION_MESSAGE[id] = setTimeout("element('"+id+"').parentNode.removeChild(element('"+id+"'))",500);
	if(typeof(supprimer_filtre)!="undefined"&&supprimer_filtre)
		filtre(false,true);
}

function deplace_message(el,pos){
	if(typeof(el)=="string")
		el = element(el);
	el.style.top = pos+"px";
}

var INFOBULLES = new Array();
var KILL_INFOBULLES = new Array();
function infobulle(el,contenu,position){
	if(typeof(el.no_infobulle)=="undefined"||el.no_infobulle==null){
		if(typeof(el.infobulle)=="undefined"||el.infobulle==null||element(el.infobulle)==null){
		//if(typeof(INFOBULLES[el])=="undefined"||!INFOBULLES[el]){
			var time = new Date().getTime();
			var id_div = "infobulle_"+time;
			el.infobulle = id_div;
			//On rÃ©cupÃ¨re les positions x et y de l'Ã©lÃ©ment.
			//var x = gauche_absolute(el);
			//var y = haut_absolute(el);
		
			var pos = offsetAbs(el);
			var x = pos["left"];
			//var y = pos["top"]-hauteur("header");
			var y = pos["top"];
		
			/*var conteneur = document.createElement("div");
				conteneur.className = "infobulleConteneur";
			el.parentNode.appendChild(conteneur);
			conteneur.appendChild(el);*/
	
			var div = document.createElement("div");
				div.innerHTML = decodeURIComponent(contenu);
				div.className = "infobulle_";
				div.id = id_div;
				div.style.zIndex = _MESSAGE_ZINDEX+1000;
				set_opacity(div,0);
			/*if(dans_element(el,element("body"))){
				var conteneur = element("body");
			}
			else if(dans_element(el,element("boite_outil"))){
				var conteneur = element("boite_outil");
			}
			else{
				var conteneur = element("body").parentNode;
			}*/
			var conteneur = document.body;
			conteneur.appendChild(div);
		
				var fleche = document.createElement("div");
					fleche.className = "fleche";
				div.appendChild(fleche);
		
			x += (largeur(el)/2-largeur(div)/2);
			y += (hauteur(el)/2-hauteur(div)/2);
		
			
		
			//var tab_pos = position.split(" ");
			pos_ok = false;
			/*if(conteneur==element("body")){
				var y_max = ecran()["y"]-hauteur("header")+element("body").scrollTop-20-hauteur(div);
				var y_min = element("body").scrollTop+20;
			}
			else{
				var y_max = ecran()["y"]-20-hauteur(div);
				var y_min = 20;
			}*/
			var y_max = getScrollPosition()["y"]+ecran()["y"]-10-hauteur(div);
			var y_min = getScrollPosition()["y"]+10;
		
		
			var x_min = 20;
			var x_max = ecran()["x"]-20;
			var tab_pos_interdites = new Array();
			var cpt_secure = 0;
			do{
				//alert(position);
				var tab_pos = position.split(" ");
				var les_positions = infobulle_xy(x,y,tab_pos,div,el);
			
				if(les_positions["y"]>y_max){ //L'infobulle dÃ©borde en bas.
					if(dans_tab("bas",tab_pos)){ //Elle est sensÃ© Ãªtre placÃ© en bas
						tab_pos_interdites[tab_pos_interdites.length] = "bas";
						if(!dans_tab("haut",tab_pos_interdites)){ // On la place donc en haut (si celle ci n'est pas encore interdite)
							position = "haut";
						}
						else{ //Sinon, on ne peut pas la placer en haut (seulement en haut)
							if(tab_pos.length>1){ //S'il y a plusieurs positions, on remplace alors bas par haut
								position = position.replace("bas","haut");
							}
							else{ //Sinon, on ajoute un cÃ´tÃ© (droite ou gauche)
								position += " gauche";
							}
						}
					}
					else{ //Elle est donc sensé se situer soit à  gauche, soit Ã  droite
						if(!dans_tab("haut",tab_pos_interdites)){
							if(tab_pos.length==1)
								tab_pos_interdites[tab_pos_interdites.length] = position;
							position = "haut";
						}
						else
							position += " haut";
					}
				}
				else if(les_positions["y"]<y_min){ //L'infobulle dÃ©borde en haut
					if(dans_tab("haut",tab_pos)){ //Elle est sensÃ© Ãªtre placÃ© en haut
						tab_pos_interdites[tab_pos_interdites.length] = "haut";
						if(!dans_tab("bas",tab_pos_interdites)){ // On la place donc en bas (si celle ci n'est pas encore interdite)
							position = "bas";
						}
						else{ //Sinon, on ne peut pas la placer en bas (seulement en bas)
							if(tab_pos.length>1){ //S'il y a plusieurs positions, on remplace alors haut par bas
								position = position.replace("haut","bas");
							}
							else{ //Sinon, on ajoute un cÃ´tÃ© (droite ou gauche)
								position += " gauche";
							}
						}
					}
					else{ //Elle est donc sensÃ© se situer soit Ã  gauche, soit Ã  droite
						if(!dans_tab("bas",tab_pos_interdites)){
							if(tab_pos.length==1)
								tab_pos_interdites[tab_pos_interdites.length] = position;
							position = "bas";
						}
						else
							position += " bas";
					}
				}
				else if(les_positions["x"]<x_min){ //L'infobulle dÃ©borde Ã  gauche
					if(dans_tab("gauche",tab_pos)){ //Elle est sensÃ© Ãªtre placÃ© Ã  gauche
						tab_pos_interdites[tab_pos_interdites.length] = "gauche";
						if(!dans_tab("droite",tab_pos_interdites)){ // On la place donc Ã  droite (si celle ci n'est pas encore interdite)
							position = "droite";
						}
						else{ //Sinon, on ne peut pas la placer Ã  droite (seulement Ã  droite)
							if(tab_pos.length>1){ //S'il y a plusieurs positions, on remplace alors gauche par droite
								position = position.replace("gauche","droite");
							}
							else{ //Sinon, on ajoute un cÃ´tÃ© (haut ou bas)
								position += " haut";
							}
						}
					}
					else{ //Elle est donc sensÃ© se situer soit en haut, soit en bas
						if(!dans_tab("droite",tab_pos_interdites)){
							if(tab_pos.length==1)
								tab_pos_interdites[tab_pos_interdites.length] = position;
							position = "droite";
						}
						else
							position += " droite";
					}
				}
				else if(les_positions["x"]+largeur(div)>x_max){ //L'infobulle dÃ©borde Ã  droite.
					if(dans_tab("droite",tab_pos)){ //Elle est sensÃ© Ãªtre placÃ© Ã  droite
						tab_pos_interdites[tab_pos_interdites.length] = "droite";
						if(!dans_tab("gauche",tab_pos_interdites)){ // On la place donc Ã  gauche (si celle ci n'est pas encore interdite)
							position = "gauche";
						}
						else{ //Sinon, on ne peut pas la placer Ã  gauche (seulement Ã  gauche)
							if(tab_pos.length>1){ //S'il y a plusieurs positions, on remplace alors droite par gauche
								position = position.replace("droite","gauche");
							}
							else{ //Sinon, on ajoute un cÃ´tÃ© (haut ou bas)
								position += " haut";
							}
						}
					}
					else{ //Elle est donc sensÃ© se situer soit en haut, soit en bas				
						if(!dans_tab("gauche",tab_pos_interdites)){
							if(tab_pos.length==1)
								tab_pos_interdites[tab_pos_interdites.length] = position;
							position = "gauche";
						}
						else
							position += " gauche";
					}
				}
				else{
					pos_ok = true;
					x = les_positions["x"];
					y = les_positions["y"];
				}
				cpt_secure++;
			} while(!pos_ok&&cpt_secure<4);
		
		
			if(tab_pos.length==1){
				if(position=="haut"||position=="top"){
					fleche.className += " bas";
					fleche.style.left = largeur(div)/2-10+"px";
					y -= 15;
				}
				else if(position=="bas"||position=="bottom"){
					fleche.className += " haut";
					fleche.style.left = largeur(div)/2-10+"px";
					y += 15;
				}
				else if(position=="gauche"||position=="left"){
					fleche.className += " droite";
					fleche.style.top = hauteur(div)/2-10+"px";
					x -= 15;
				}
				else if(position=="droite"||position=="right"){
					fleche.className += " gauche";
					fleche.style.top = hauteur(div)/2-10+"px";
					x += 15;
				}
			}
			else{
				if((dans_tab("gauche",tab_pos)||dans_tab("left",tab_pos))&&(dans_tab("haut",tab_pos)||dans_tab("top",tab_pos))){
					div.style.borderRadius = "5px 5px 0px 5px";
					x -= 5;
					y -= 5;
				}
				else if((dans_tab("droite",tab_pos)||dans_tab("right",tab_pos))&&(dans_tab("haut",tab_pos)||dans_tab("top",tab_pos))){
					div.style.borderRadius = "5px 5px 5px 0px";
					x += 5;
					y -= 5;
				}
				else if((dans_tab("gauche",tab_pos)||dans_tab("left",tab_pos))&&(dans_tab("bas",tab_pos)||dans_tab("bottom",tab_pos))){
					div.style.borderRadius = "5px 0px 5px 5px";
					x -= 5;
					y += 5;
				}
				else if((dans_tab("droite",tab_pos)||dans_tab("right",tab_pos))&&(dans_tab("bas",tab_pos)||dans_tab("bottom",tab_pos))){
					div.style.borderRadius = "0px 5px 5px 5px";
					x += 5;
					y += 5;
				}
			}
		
		
		
			div.style.left = x+"px";
			div.style.top = y+"px";
		
			//ajoute_evenement(conteneur,"mouseout",';kill_infobulle(this,"'+id_div+'");');
			//addEventListener(el, function(){ kill_infobulle(this,"'+id_div+'"); }, false); 
			ajoute_evenement2(el,"mouseout",'kill_infobulle(this);');

			//INFOBULLES[el] = true;
			//KILL_INFOBULLES[id_div] = id_div;
			KILL_INFOBULLES[id_div] = setTimeout('set_opacity(element("'+id_div+'"),100);',50);
		}
		else{
			id_div = el.infobulle;
			/*var conteneur = document.createElement("div");
			el.parentNode.appendChild(conteneur);
			conteneur.appendChild(el);*/
			if(KILL_INFOBULLES[id_div]!=false){
				clearTimeout(KILL_INFOBULLES[id_div]);
				KILL_INFOBULLES[id_div] = false;
			}
			set_opacity(element(id_div),100);
			/*	alert(1);
			alert(el.mouseout);
			alert(2);*/
			//ajoute_evenement(el.parentNode,"mouseout",'kill_infobulle(this,"'+id_div+'");');
			/*
			el.removeEventListener("mouseout",eval);
			ajoute_evenement2(el,"mouseout",'kill_infobulle(this,"'+id_div+'");');*/
		}
		return element(id_div);
	}
	else
		return false;
}

	function infobulle_xy(x,y,position,div,el){
		//alert(position);
		if(dans_tab("haut",position)||dans_tab("top",position)){
			y -= (hauteur(div)/2+hauteur(el)/2);
		}
		else if(dans_tab("bas",position)||dans_tab("bottom",position)){
			y += hauteur(div)/2+hauteur(el)/2;
		}
		
		if(dans_tab("gauche",position)||dans_tab("left",position)){
			x -= (largeur(div)/2+largeur(el)/2);
		}
		else if(dans_tab("droite",position)||dans_tab("right",position)){
			x += largeur(div)/2+largeur(el)/2;
		}
		//return {"x":x,"y":y-somme_scrollTop(el)};
		return {"x":x,"y":y};
	}
	
function kill_infobulle(el){
//function kill_infobulle(el,id_div){
	//alert(el.onmouseout);
	//el.onmouseout = el.onmouseout.replace(/kill_infobulle\(this,"infobulle_[0-9]+"\)/gi,'');
	//el.infobulle = null;
	
	/*el.parentNode.appendChild(el.firstChild);
	el.parentNode.removeChild(el);*/
	id_div = el.infobulle;
	if(KILL_INFOBULLES[id_div]!=false){
		clearTimeout(KILL_INFOBULLES[id_div]);
		KILL_INFOBULLES[id_div] = false;
	}
	
	if(element(id_div)!=null){
		el.removeEventListener("mouseout",eval);
		
		set_opacity(element(id_div),0);
		KILL_INFOBULLES[id_div] = setTimeout("element('"+id_div+"').parentNode.removeChild(element('"+id_div+"'));KILL_INFOBULLES['"+id_div+"'] = false;",500);
	}
}


function haut_absolute2(el,h,old_position){
	if(typeof(el)=="string")
		el = element(el);
	if(typeof(h)=="undefined")
		h = 0;
	if(typeof(old_position)=="undefined")
		old_position = false;
	if(el.id!="body"){
		var p = getStyle(el, "position");
		//old_position = ;
		//alert(el+" : "+((typeof(el.id)!="undefined")?el.id:" - ")+" : "+haut(el));
		return haut_absolute(el.parentNode,(((old_position=="relative"||old_position=="absolute")&&p!="absolute"&&p!="relative")?h:h+haut(el)),p);
	}
	else
		return h;
}
function gauche_absolute(el,g,old_position){
	if(typeof(el)=="string")
		el = element(el);
	if(typeof(g)=="undefined")
		g = 0;
	if(typeof(old_position)=="undefined")
		old_position = false;
	if(el.id!="body"){
		var p = getStyle(el, "position");
		//old_position = ;
		//alert(el+" : "+((typeof(el.id)!="undefined")?el.id:" - ")+" : "+haut(el));
		//alert(el+" : "+((typeof(el.id)!="undefined")?el.id:" - ")+" : "+gauche(el));
		return gauche_absolute(el.parentNode,((p=="relative"||p=="absolute")?g:g+gauche(el)),p);
	}
	else
		return g;
}


function clignotter_message(id,retour){
	if(typeof(retour)=="undefined")
		retour = false;
	var div = element(id);
	if(!retour){
		apply_transform(div,"scale(1.1,1.1)");
		div.style.zIndex = _MESSAGE_ZINDEX++;
		div.style.boxShadow = "0px 0px 1.2em #FFFFFF,1px 1px 1em 0.2em #589E47"; //vert
		setTimeout('clignotter_message("'+id+'",true);',300);
		setTimeout('clignotter_message_boxShadow("'+id+'",1)',100);
	}
	else{
		apply_transform(div,"scale(1,1)");
	}
}

function clignotter_message_boxShadow(id,passage){
	var div = element(id);
	if(passage==1){
		div.style.boxShadow = "0px 0px 1.2em #FFFFFF,1px 1px 1em 0.2em #4e4e4e"; //gris
		setTimeout('clignotter_message_boxShadow("'+id+'",2)',100);
	}
	else if(passage==2){
		div.style.boxShadow = "0px 0px 1.2em #FFFFFF,1px 1px 1em 0.2em #589E47"; //vert
		setTimeout('clignotter_message_boxShadow("'+id+'",3)',100);
	}
	else if(passage==3){
		div.style.boxShadow = "0px 0px 1.2em #FFFFFF,1px 1px 1em 0.2em #4e4e4e"; //gris
	}
}
