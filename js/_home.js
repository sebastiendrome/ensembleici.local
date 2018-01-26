function reajuste_tous_contenu_home(){
	if(element("home_editorial")!=null){
		var zone = element("home_editorial_bloc");
		var imgs = zone.getElementsByTagName("img");
		var link = get_link_cssBig();
		if(!link){ //4/3
			zone.style.height = Math.floor(largeur(zone)*3/4)+"px";
			for(var ii=0;ii<imgs.length;ii++){
				if(dans_tab("16/9", imgs[ii].parentNode.className.split(" ")))
					imgs[ii].parentNode.className = imgs[ii].parentNode.className.replace("16/9","4/3");
			}
		}
		else{ //16/9
			zone.style.height = Math.floor(largeur(zone)*9/16)+"px";
			for(var ii=0;ii<imgs.length;ii++){
				if(dans_tab("4/3", imgs[ii].parentNode.className.split(" ")))
					imgs[ii].parentNode.className = imgs[ii].parentNode.className.replace("4/3","16/9");
			}
		}
	}
	if(element("home_agenda")!=null){
		/***
			Les événements à venir
		****/
		var zone = element("zone_bloc_evenement_accueil");
		var zone_top3 = element("home_agenda_top3_bloc");
		var larg_max = largeur(zone);
		if(dans_tab("zone_petite",zone.className.split(" "))){
			var larg_bloc_max = parseInt(getStyle(zone.firstChild,"max-width"));
			if(larg_bloc_max<larg_max){ //On peut changer de format.
				if(larg_max>=800){ //Grand
					console.log("On passe de petit à grand");
					zone.className = zone.className.replace("zone_petite","zone_grande");
					zone_top3.className = zone_top3.className.replace("zone_petite","zone_grande");
					
					zone.firstChild.nextSibling.appendChild(zone.lastChild.previousSibling.firstChild);
					zone.removeChild(zone.lastChild.previousSibling);
				}
				else{ //Moyen
					console.log("On passe de petit à moyen");
					zone.className = zone.className.replace("zone_petite","zone_moyenne");
					zone_top3.className = zone_top3.className.replace("zone_petite","zone_moyenne");
				}
			}
		}
		else if(dans_tab("zone_moyenne",zone.className.split(" "))){
			var larg_bloc_min = parseInt(getStyle(zone.firstChild,"min-width"))+parseInt(getStyle(zone.firstChild.nextSibling,"min-width"));
			var larg_bloc_max = parseInt(getStyle(zone.firstChild,"max-width"))+parseInt(getStyle(zone.firstChild.nextSibling,"max-width"));
			if(larg_bloc_min>larg_max){ //On passe au petit
				console.log("On passe de moyen à petit");
				zone.className = zone.className.replace("zone_moyenne","zone_petite");
				zone_top3.className = zone_top3.className.replace("zone_moyenne","zone_petite");
			}
			else{
				if(larg_bloc_max<larg_max){ //On passe au grand
					console.log("On passe de moyen à grand");
					zone.className = zone.className.replace("zone_moyenne","zone_grande");
					zone_top3.className = zone_top3.className.replace("zone_moyenne","zone_grande");
					
					zone.firstChild.nextSibling.appendChild(zone.lastChild.previousSibling.firstChild);
					zone.removeChild(zone.lastChild.previousSibling);
				}
			}
		}
		else if(dans_tab("zone_grande",zone.className.split(" "))){
			var larg_bloc_min = parseInt(getStyle(zone.firstChild,"min-width"))+parseInt(getStyle(zone.firstChild.nextSibling,"min-width"))+parseInt(getStyle(zone.lastChild,"min-width"));
			if(larg_max<larg_bloc_min){ //On peut changer de format
				if(larg_max<500){ //Petit
					console.log("On passe de grande à petit");
					zone.className = zone.className.replace("zone_grande","zone_petite");
					zone_top3.className = zone_top3.className.replace("zone_grande","zone_petite");
					
					var bloc = document.createElement("div");
						bloc.className = "bloc_evenement_accueil bloc_milieu";
						bloc.appendChild(zone.firstChild.nextSibling.lastChild);
					zone.insertBefore(bloc,zone.lastChild);
				}
				else{ //Moyen
					console.log("On passe de grande à moyen");
					zone.className = zone.className.replace("zone_grande","zone_moyenne");
					zone_top3.className = zone_top3.className.replace("zone_grande","zone_moyenne");
					
					var bloc = document.createElement("div");
						bloc.className = "bloc_evenement_accueil bloc_milieu";
						bloc.appendChild(zone.firstChild.nextSibling.lastChild);
					zone.insertBefore(bloc,zone.lastChild);
				}
			}
		}
		
		/***
		Le top 3
		**/
		/*var zone = element("home_agenda_top3_bloc");
		var larg_max = largeur(zone);
		if(dans_tab("zone_petite",zone.className.split(" "))){
			
		}*/
	}
}
