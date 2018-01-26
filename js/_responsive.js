function resize(actif){
	if(typeof(actif)=="undefined")
		actif = false;
	if(TIMEOUT_RESIZE!=false){
		clearTimeout(TIMEOUT_RESIZE);
		TIMEOUT_RESIZE = false;
	}
	if(actif){
		console.log(largeur("header")+" : "+LARGEUR_MINIMUM_MENU);
		if(largeur("header")<LARGEUR_MINIMUM_MENU){ //SmallCss
			petit_ecran();
		}
		else{
			grand_ecran();
		}
		resize_bloc();
		
		/*console.log("jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj");
		if(element("colonne_droite")!=null){
			var lesBlocsDroites = element("colonne_droite").firstChild.firstChild.firstChild.childNodes;
			//getElementsByTagName("bloc_colonne_droite");
			console.log(lesBlocsDroites);
			var nb_affiche = lesBlocsDroites.length;
			console.log(nb_affiche);
		
			while(hauteur("colonne_droite")>hauteur("colonne_gauche")&&nb_affiche>1){
				lesBlocsDroites[(nb_affiche-1)].style.display = "none";
				nb_affiche--;
				console.log("hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh");
			}
		}*/
	}
	else{
		//LARGEUR_ECRAN = ecran()["x"];
		if(TIMEOUT_RESIZE!=false){
			clearTimeout(TIMEOUT_RESIZE);
		}
		TIMEOUT_RESIZE = setTimeout("resize(true);",200);
	}
}
	var POUR_CE_CONNARD_DE_SAFARI_DE_MERDE = false;
	function grand_ecran(){
		var link = get_link_cssBig();
		if(!link){ //Le fichier n'est pas appelé
			var link = document.createElement("link")
				link.rel = "stylesheet";
				link.type = "text/css";
				link.href = "css/_cssBig.css";
				link.onload = function(){grand_ecran_charge()};
				/*
				Si le navigateur est un putain de safari DE MERDE!!!!
				Vu que onload ne marche pas b on va créer une usine à gaz qui va attendre le chargement de la css.
				*/
				if(navigator.userAgent.indexOf("Safari")>-1||navigator.userAgent.indexOf("safari")>-1){
					POUR_CE_CONNARD_DE_SAFARI_DE_MERDE = false;
					var nb_link = document.styleSheets.length;
					POUR_CE_CONNARD_DE_SAFARI_DE_MERDE = setTimeout('grand_ecran_charge_safari_de_merde('+nb_link+')',100);
				}
			document.getElementsByTagName("head")[0].appendChild(link);
		}
		else
			regler_menu_notSmartphone();
	}
		function grand_ecran_charge(){
			//On s'assure que le menu est bien smartphone
			regler_menu_notSmartphone();
			//On redimmensionne les blocs
			//resize_bloc();
			//On enlève la mention chargement au body
			if(document.body.className.indexOf("chargement")>-1)
				document.body.className = document.body.className.replace("chargement","");
		}
			function grand_ecran_charge_safari_de_merde(nb){
				var nb_link = document.styleSheets.length;
				if(nb_link>nb){
					POUR_CE_CONNARD_DE_SAFARI_DE_MERDE = false;
					grand_ecran_charge();
				}
				else{
					POUR_CE_CONNARD_DE_SAFARI_DE_MERDE = setTimeout('grand_ecran_charge_safari_de_merde('+nb+')',100);
				}
			}
			
	function petit_ecran(){
		console.log("liiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii");
		var link = get_link_cssBig();
		if(link!=false) //Le fichier existe
			link.parentNode.removeChild(link);
			//element("menuSmartphone_pages").style.maxHeight = (ecran()["y"]-5)+"px";
		regler_menu_smartphone();
		if(document.body.className.indexOf("chargement")>-1)
			document.body.className = document.body.className.replace("chargement","");
	}

function get_link_cssBig(){
	var links = document.getElementsByTagName("link");
	var i=0;
	while(i<links.length&&links[i]["href"].indexOf("_cssBig.css")<0){
		i++;
	}
	return (i<links.length)?links[i]:false;
}

function regler_menu_smartphone(){
	//FILTRE
	if(element("filtre").parentNode!=element("page"))
		element("page").appendChild(element("filtre"));
		
	//MENU UTILISATEUR
	if(element("menu_utilisateur").parentNode!=element("page").parentNode)
		element("page").parentNode.insertBefore(element("menu_utilisateur"),element("page"));
	var les_items = element("menu_utilisateur").getElementsByTagName("a");
	for(var i=0;i<les_items.length;i++){
		if(typeof(les_items[i].className)!="undefined"&&les_items[i].className.indexOf("item_menu_utilisateur")>-1){
			if(les_items[i].firstChild==null){
				var div = document.createElement("div");
					div.appendChild(document.createTextNode(decodeURIComponent(les_items[i].contenu_infobulle)));
				les_items[i].appendChild(div);
				les_items[i].no_infobulle = 1;
			}
		}
	}
		
	//MENU PAGE
	if(element("menu").parentNode!=element("menuSmartphone_pages"))
		element("menuSmartphone_pages").appendChild(element("menu"));
	element("menuSmartphone_pages").style.width = (largeur("menuSmartphone")-largeur("menuSmartphone_utilisateur"))+"px";
	if(element("menu_retour")==null){
		var div = document.createElement("div");
			div.id = "menu_retour";
			div.className = "item_menu gris";
			var div_ = document.createElement("div");
			div.appendChild(div_);
			
			vide(element("menu_home"));
			var div_accueil = document.createElement("div");
				div_accueil.appendChild(document.createTextNode("Accueil"));
			element("menu_home").appendChild(div_accueil);
		//element("menu_home").firstChild.appendChild(document.createTextNode("Accueil"));
		//largeur("menu_home");
		element("menu").insertBefore(div,element("menu").firstChild);
		
		/*if(PAGE_COURANTE=="accueil")
			element("menu").style.top = -60+"px";
		else if(PAGE_COURANTE=="agenda")
			element("menu").style.top = -120+"px";
		else if(PAGE_COURANTE=="forum")
			element("menu").style.top = -180+"px";
		else if(PAGE_COURANTE=="structure")
			element("menu").style.top = -240+"px";
		else if(PAGE_COURANTE=="petiteannonce")
			element("menu").style.top = -300+"px";
		else if(PAGE_COURANTE=="editorial")
			element("menu").style.top = -360+"px";*/
		//element("menu").className = PAGE_COURANTE;
		var les_items = element("menu").childNodes;
		for(var i=0;i<les_items.length;i++){
			if(typeof(les_items[i].className)!="undefined"&&dans_tab("item_menu",les_items[i].className.split(" "))&&les_items[i].id!="menu_home"){
				les_items[i].style.paddingRight = 0+"px";
				les_items[i].style.paddingLeft = 0+"px";
			}
		}
		menu_site_smartphone(true);
	}
	element("fleche_deplier").style.left = ((largeur(element("fleche_deplier").parentNode)/2)-largeur("fleche_deplier")/2)+"px";
	
	menu_utilisateur_smartphone(true);
	
	scrolling();
}

function regler_menu_notSmartphone(){
	//ON FERME D'ABBORD LES MENUS EVENTUELLEMENT OUVERTS
	menu_utilisateur_smartphone(true);
	menu_site_smartphone(true);
	//FILTRE
	if(element("filtre").parentNode!=element("page").parentNode)
		element("page").parentNode.appendChild(element("filtre"));
	//MENU UTILISATEUR
	if(element("menu_utilisateur").parentNode!=element("page"))
		element("page").appendChild(element("menu_utilisateur"));
	var les_items = element("menu_utilisateur").getElementsByTagName("a");
	for(var i=0;i<les_items.length;i++){
		if(typeof(les_items[i].className)!="undefined"&&les_items[i].className.indexOf("item_menu_utilisateur")>-1){
//			vide(les_items[i]);
			les_items[i].no_infobulle = null;
		}
	}
		
	//MENU PAGE	
	if(element("menu").parentNode!=element("page"))
		element("page").insertBefore(element("menu"),element("contenu"));
	if(element("menu_retour")!=null){
		element("menu_retour").parentNode.removeChild(element("menu_retour"));
		element("menu_home").firstChild.removeChild(element("menu_home").firstChild.lastChild);
		//element("menu").style.top = 0+"px";
	}
	var largeur_tt_menu = 0;
	var paddingMin = 20;
	var les_items = element("menu").childNodes;
	var nb_menu_reglage = 0;
	for(var i=0;i<les_items.length;i++){
		if(typeof(les_items[i].className)!="undefined"&&dans_tab("item_menu",les_items[i].className.split(" "))){
			largeur_tt_menu += largeur(les_items[i]);
			if(les_items[i].id!="menu_home"){
				nb_menu_reglage++;
				var padding_item = ((typeof(les_items[i].style.paddingLeft)!="undefined"&&les_items[i].style.paddingLeft!=""&&les_items[i].style.paddingLeft!="none")?(parseInt(les_items[i].style.paddingLeft)):0)+((typeof(les_items[i].style.paddingRight)!="undefined"&&les_items[i].style.paddingRight!=""&&les_items[i].style.paddingRight!="none")?(parseInt(les_items[i].style.paddingRight)):0);
				largeur_tt_menu -= padding_item; //On retire la taille du padding (pour avoir vraiment la taille minimum de l'item)
				largeur_tt_menu += 2*paddingMin; //On ajoute la taille du padding minimum (on veut vraiment la taille minimum de l'item)
			}
		}
	}
	LARGEUR_MINIMUM_MENU = largeur_tt_menu;
	var taille_restante = largeur("menu")-LARGEUR_MINIMUM_MENU;
	if(taille_restante<0){
		console.log("bug");
	}
	else{
		//taille_restante += 2*paddingMin*nb_menu_reglage;
		var new_padding = Math.floor(taille_restante/nb_menu_reglage/2)+paddingMin;
			var new_padding2 = (taille_restante/nb_menu_reglage/2)+paddingMin;
		var px_supplementaire = Math.floor((taille_restante+2*paddingMin*nb_menu_reglage)-(nb_menu_reglage*2*new_padding));
		for(var i=0;i<les_items.length;i++){
			if(typeof(les_items[i].className)!="undefined"&&dans_tab("item_menu",les_items[i].className.split(" "))&&les_items[i].id!="menu_home"){
				les_items[i].style.paddingRight = new_padding+"px";
				if(i==les_items.length-1)
					new_padding += px_supplementaire;
				les_items[i].style.paddingLeft = new_padding+"px";
			}
		}
		while(haut(les_items[les_items.length-1])>haut(les_items[0])){
			new_padding--;
			les_items[les_items.length-1].style.paddingLeft = new_padding+"px";
		}
		//TODO Système ci-dessus un peu usine à gaz (le dernier while permet de regler un bug que je n'ai pas pu identifier sous chrome et ie : le menu forum faisait souvent 1 pixel de plus ce qui le descendait en dessous des autres menus
	}
	
	scrolling();
}

function resize_bloc(){
	//On récupère les lignes
	//console.log("---------------------------------------------------------");
	
	//var lignes = element("contenu_page").getElementsByClassName("row");
	var lignes = getElementsByClassName(element("contenu_page"),"row");
	
	for(var i=0;i<lignes.length;i++){
		var largeur_ligne = largeur(lignes[i]);
			//if(largeur_ligne%2!=0)
				//largeur_ligne++;
		var largeur_blocs_ligne = 0;
		if(dans_tab("multi_row",lignes[i].className.split(" ")))
			lignes[i].className = lignes[i].className.replace(" multi_row","");
		//On récupère les blocs de chaque ligne
		//var blocs = lignes[i].getElementsByClassName("bloc");
		var blocs = getElementsByClassName(lignes[i],"bloc");
		
		for(var j=0;j<blocs.length;j++){
			//On calcul la taille des blocs
			largeur_blocs_ligne += largeur(blocs[j]);
			//console.log("bloc: "+j+" -> "+largeur(blocs[j]));
			//console.log("somme: "+j+" -> "+largeur_blocs_ligne);
		}
		//console.log(i+" -> "+largeur_blocs_ligne+" - "+largeur_ligne);
		if(largeur_blocs_ligne>largeur_ligne)
			lignes[i].className = lignes[i].className+" multi_row";
	}
	if(TIMEOUT_RESIZE_CONTENUS!=false){
		clearTimeout(TIMEOUT_RESIZE_CONTENUS);
		TIMEOUT_RESIZE_CONTENUS = false;
	}
	TIMEOUT_RESIZE_CONTENUS = setTimeout("reajuste_tous_contenus();",200);
}

function reajuste_tous_contenus(){
	// Seulement pour la page d'accueil
	if(typeof(reajuste_tous_contenu_home)=="function"){
		reajuste_tous_contenu_home();
	}
	
	//1. les images
	var imgs = element("contenu_page").getElementsByTagName("img");
	for(var i=0;i<imgs.length;i++){
		if(dans_tab("image",imgs[i].parentNode.className.split(" "))){
			img_load(imgs[i]);
		}
	}
}

function img_load(img){
	if(img.style.position!="relative")
		img.style.position = "relative";
	
	if(dans_tab("4/3",img.parentNode.className.split(" ")))
		var rapport_courant = 4/3;
	else if(dans_tab("16/9",img.parentNode.className.split(" ")))
		var rapport_courant = 16/9;
	else if(dans_tab("carre",img.parentNode.className.split(" "))||dans_tab("1/1",img.parentNode.className.split(" ")))
		var rapport_courant = 1;
	else
		var rapport_courant = RAPPORT_IMAGE;

	var x_max = largeur(img.parentNode); //Largeur du cadre
	var y_max = Math.floor(x_max/rapport_courant); //Nouvelle hauteur du cadre
		var y_minHeight = (isNaN(parseInt(getStyle(img.parentNode,"min-height"))))?0:parseInt(getStyle(img.parentNode,"min-height"));
		y_max = Math.max(y_minHeight,y_max);
	img.parentNode.style.height = y_max +"px";

	//On centre maintenant l'image dans ce cadre
	var x = img.width;
	var y = img.height;

	var new_x = x_max;
	var new_y = Math.floor(new_x*y/x);
	if(new_y<y_max){
		new_y = y_max;
		new_x = Math.floor(new_y*x/y);
	}

	img.style.width = new_x+"px";
	img.style.height = new_y+"px";
	img.style.left = Math.floor(x_max/2-new_x/2)+"px";
	img.style.top = Math.floor(y_max/2-new_y/2)+"px";
	
	img.parentNode.className = img.parentNode.className.replace(" invisible"," visible");
}


