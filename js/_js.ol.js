window.onpopstate = function(){charger_page();}; //<- Bouton précédent et suivant géré en ajax pour les nouveaux navigateurs
function onload(){
	//1. On lance le diaporama.
	charge_diaporama();
	//2. On prépare les infobulles, etc.
	parcours_recursif();
	resize(true);
	if(ID_VILLE>0)
		filtre(false,true);
	else
		fenetre_ville(true);
	setTimeout('afficher_page_fin()',100);
	//On charge le bandeau colorbox (pour ne pas que la calcul de la taille de la première colorbox soit faussé)
	charge_image("http://www.ensembleici.fr/img/bandeau-colorbox.png");
	//setTimeout("element('header').style.width=100+'%';alert('yo');",5000);
}
	function charge_image(url){
		var img = document.createElement("img");
			img.src = url;
	}

function connexion(){
	filtre();
	var xhr = getXhr();
		xhr.open("POST", "gestion/ajax/connexion.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("email="+encodeURIComponent(element("input_email").value)+"&mdp="+encodeURIComponent(element("input_mdp").value));
	var connexion = eval("("+xhr.responseText+")");
	if(connexion[0]){
		charger_page();
		filtre(false);
	}
	else{
		message(connexion[1]);
		filtre(false);
	}
	return false;
}
function deconnexion(){
	var xhr = getXhr();
		xhr.open("POST", "gestion/ajax/deconnexion.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send(null);
	charger_page();
}
/*function filtre(laisser_ferme){
	if(typeof(laisser_ferme)=="undefined")
		laisser_ferme = false;
	if(laisser_ferme||element("filtre").className=="actif")
		element("filtre").className = "";
	else
		element("filtre").className = "actif";
}*/

function filtre(ouvrir,persistant){
	if(typeof(ouvrir)=="undefined")
		ouvrir = true;
	if(typeof(persistant)=="undefined")
		persistant = false;
	if(ouvrir){ //On ouvre le filtre
		element("filtre").className = "actif";
		if(persistant)
			element("filtre").className += " persistant";
	}
	else{ //On ferme le filtre
		if(!dans_tab("persistant",element("filtre").className.split(" "))||persistant)
			element("filtre").className = "";
	}
}


function menu_site_smartphone(laisser_ferme){
	if(typeof(laisser_ferme)=="undefined")
		laisser_ferme = false;
	if(!dans_tab("ouvert",element("menuSmartphone_pages").className.split(" "))&&!laisser_ferme){
		menu_utilisateur_smartphone(true);
		filtre(!laisser_ferme);
		element("menuSmartphone_pages").className = element("menuSmartphone_pages").className+" ouvert";
		element("menuSmartphone_pages").style.width = (largeur("menuSmartphone")-largeur("menuSmartphone_utilisateur"))+"px";
	}
	else{
		filtre(!laisser_ferme);
		element("menuSmartphone_pages").className = element("menuSmartphone_pages").className.replace(" ouvert","");
		element("menuSmartphone_pages").style.width = (largeur("menuSmartphone")-largeur("menuSmartphone_utilisateur"))+"px";
		//element("menu").className = PAGE_COURANTE;
	}
}

var TIMEOUT_AFFICHER_PAGE = false;
function choix_page(el,e){
	if(typeof(history.pushState)!="undefined"){ //Système compatible
		if(typeof(e)!="undefined")
			e.preventDefault();
		PAGE_COURANTE = el.id.split("_")[1];
		history.pushState({ path: this.path }, '', el.href);
		charger_page();
		return false;
	}
	else{ //Lien normal
		return true;
	}
}


var XHR_CHARGEMENT_PAGE = false;
	function charger_page(){
		barre_chargement(0);
		if(XHR_CHARGEMENT_PAGE!=false){
			XHR_CHARGEMENT_PAGE.abort();
			XHR_CHARGEMENT_PAGE = false;
		}
		
		var parametres = getParametresURL();
		
		var zone_affichage = element("contenu_page");
		/*if(TIMEOUT_AFFICHER_PAGE!=false){
			clearTimeout(TIMEOUT_AFFICHER_PAGE);
			TIMEOUT_AFFICHER_PAGE = false;
		}
		set_opacity(zone_affichage,0);*/
		
		if(element("colonne_gauche")!=null){
			var colonne_gauche = element("colonne_gauche");
			var colonne_droite = element("colonne_droite");
		}
		else{
			var colonne_gauche = element("home_editorial");
			var colonne_droite = element("home_editorial_ei");
		}
		
		//if(dans_tab("visible",colonne_gauche.className.split(" ")))
			colonne_gauche.className = colonne_gauche.className.replace("visible","invisible");
		//else
			//colonne_gauche.className += " invisible";
		//if(dans_tab("visible",colonne_droite.firstChild.firstChild.lastChild.className.split(" ")))
			colonne_droite.firstChild.firstChild.lastChild.className = colonne_droite.firstChild.firstChild.lastChild.className.replace("visible","invisible");
		//else
			//colonne_droite.firstChild.firstChild.lastChild.className += " invisible";
		
		var les_lignes = copier_tab(element("contenu_page").childNodes);
		for(var i=1;i<les_lignes.length;i++){ //On laisse la première ligne (colonne gauche, colonne droite)
			if(dans_tab("visible",les_lignes[i].className.split(" ")))
				les_lignes[i].className = les_lignes[i].className.replace("visible","invisible")
			else
				les_lignes[i].className += " invisible";
		}
		
		if(get_link_cssBig()!=false)
			remonter_progressif();
		
		/*
		if(type!=""){
			colonne_gauche.id = "colonne_gauche";
			colonne_droite.id = "colonne_droite";
			var home = false;
		}
		else{
			colonne_gauche.id = "home_editorial";
			colonne_droite.id = "home_editorial_ei";
			var home = true;
		}*/
		
		/*
		//MODIFIE LE 1er fevr. 2015
		if(element("zone_fichiers")!=null){
			if(element("liste_audio")!=null){ //S'il y a un fichier audio
				if(element("zone_fichiers_persistant")!=null)
					element("zone_fichiers_persistant").parentNode.removeChild(element("zone_fichiers_persistant"));
				//if(isPaused)
				var w = SC.Widget(element("zone_fichiers").lastChild.id);
				console.log(w);
				console.log(w.getPosition());
				console.log(w.isPaused());
				colonne_droite.firstChild.firstChild.insertBefore(element("zone_fichiers"),colonne_droite.firstChild.firstChild.firstChild);
				element("zone_fichiers").lastChild.id += "_persistant";
				element("zone_fichiers").id += "_persistant";
			}
		}*/
		
		
		/*
		if(type!=""){ //Affichage liste ou fiche
			//S'il n'y a pas de colonne_gauche et droite, on supprime tout le contenu et on les créait
			if(element("colonne_gauche")!=null)
				vide(element("colonne_gauche"));
			else{ //Il faut créer colonne_gauche et colonne_droite
				vide("contenu_page");
				element("contenu_page").appendChild(document.createTextNode("colonne_gauche + colonne_droite"));
			}
			//vide("zone_affichage");
		}
		else{ //Accueil
			//S'il y a des colonnes gauche ou droite, on supprime tout le contenu afin de réafficher l'accueil
			vide("contenu_page");
			element("contenu_page").appendChild(document.createTextNode("accueil"));
		}*/
		//On génère la chaine de paramètre
		var params = "";
		if(typeof(parametres["libelle_ville"])!="undefined")
			params += ((params!="")?"&":"")+"nom_ville="+parametres["libelle_ville"];
		if(typeof(parametres["numero_ville"])!="undefined")
			params += ((params!="")?"&":"")+"id_ville="+parametres["numero_ville"];
		if(typeof(parametres["type"])!="undefined")
			params += ((params!="")?"&":"")+"p="+parametres["type"];
		if(typeof(parametres["sous_page"])!="undefined")
			params += ((params!="")?"&":"")+"sous_page="+parametres["sous_page"];
		if(typeof(parametres["etape"])!="undefined")
			params += ((params!="")?"&":"")+"etape="+parametres["etape"];
		if(typeof(parametres["titre_fiche"])!="undefined")
			params += ((params!="")?"&":"")+"titre="+parametres["titre_fiche"];
		if(typeof(parametres["no_fiche"])!="undefined")
			params += ((params!="")?"&":"")+"no="+parametres["no_fiche"];
		if(typeof(parametres["num_page"])!="undefined")
			params += ((params!="")?"&":"")+"np="+parametres["num_page"];
		if(typeof(parametres["date"])!="undefined")
			params += ((params!="")?"&":"")+"du="+parametres["date"];
		if(typeof(parametres["distance"])!="undefined")
			params += ((params!="")?"&":"")+"dist="+parametres["distance"];
		if(typeof(parametres["tri"])!="undefined")
			params += ((params!="")?"&":"")+"tri="+parametres["tri"];
			
		if(typeof(parametres["q"])!="undefined")
			params += ((params!="")?"&":"")+"q="+parametres["q"];
		//On récupère maintenant les lignes
		XHR_CHARGEMENT_PAGE = getXhr();
		XHR_CHARGEMENT_PAGE.onreadystatechange = function(){
			if(XHR_CHARGEMENT_PAGE.readyState == 4){
				if(XHR_CHARGEMENT_PAGE.status == 200){
					var reponse = eval("("+XHR_CHARGEMENT_PAGE.responseText+")");
					XHR_CHARGEMENT_PAGE = false;
					var les_lignes = copier_tab(element("contenu_page").childNodes);
					for(var i=1;i<les_lignes.length;i++){ //On laisse la première ligne (colonne gauche, colonne droite)
						les_lignes[i].parentNode.removeChild(les_lignes[i]);
					}
					element("menu").className = parametres["type"];
					element("contenu").className = parametres["type"];
					afficher_page(element("contenu_page"),reponse);
					barre_chargement(4);
					//filtre_chargement(false);
				}
				else{
					setTimeout('charger_page();',300);
				}
			}
			else
				barre_chargement(XHR_CHARGEMENT_PAGE.readyState);
		};
		XHR_CHARGEMENT_PAGE.open("POST", "03_ajax/charger_page.php", false);
		XHR_CHARGEMENT_PAGE.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		XHR_CHARGEMENT_PAGE.send(params);
		
	}
	
	function afficher_page(zone,lignes){
		//alert("tamerelagrossepute");
		
		/*var nb_item = lignes["count"];
		var nb_item_total = lignes["count_total"];
			lignes = lignes["liste"];*/
		
		var ligne_1 = zone.firstChild;
		var colonne_gauche = ligne_1.firstChild;
		var colonne_droite = colonne_gauche.nextSibling;
		vide(colonne_gauche.firstChild.firstChild);
		 //On vide la div du contenu de colonne_droite
		var bloc_1 = lignes[0]["lignes"][0];
		var bloc_2 = lignes[0]["lignes"][1];
		
		colonne_gauche.className = "bloc invisible "+bloc_1["class"];
		colonne_gauche.id = bloc_1["id"];
		colonne_gauche.firstChild.firstChild.innerHTML = bloc_1["contenu"];
				if(typeof(bloc_1["titre"])!="undefined"&&bloc_1["titre"]){
					var div_titre = document.createElement("div");
						div_titre.className = "titre_bloc";
					colonne_gauche.firstChild.firstChild.appendChild(div_titre);
				}
		
		
		vide(colonne_droite.firstChild.firstChild.lastChild);
		//if(element("zone_fichiers_persistant")!=null){
			/*var les_divs = colonne_droite.firstChild.firstChild.childNodes;
			for(var i=1;i<les_divs.length;i++){
				les_divs[i].parentNode.removeChild(les_divs[i]);
			}*/
			vide(colonne_droite.firstChild.firstChild.lastChild)
		//}
		/*else{
			vide(colonne_droite.firstChild.firstChild);
		}*/
		if(bloc_2["id"]!="home_editorial_ei"||element("zone_fichiers_persistant")==null){
			/*var div = document.createElement("div");
				div.innerHTML = bloc_2["contenu"];
			colonne_droite.firstChild.firstChild.appendChild(div);*/
			colonne_droite.firstChild.firstChild.lastChild.innerHTML = bloc_2["contenu"];
		}
		colonne_droite.id = bloc_2["id"];
		colonne_droite.className = "bloc "+bloc_2["class"];
		
		for(var i=1;i<lignes.length;i++){
			var row = document.createElement("div");
				row.className = "row invisible"+((typeof(lignes[i]["class"])!="undefined"&&lignes[i]["class"]!="")?" "+lignes[i]["class"]:"");
			var les_blocs = lignes[i]["lignes"];
			for(var j=0;j<les_blocs.length;j++){
				var div = document.createElement("div");
					div.className = "bloc "+les_blocs[j]["class"];
					div.id = les_blocs[j]["id"];
					var div_ = document.createElement("div");
					var div__ = document.createElement("div");
					div.appendChild(div_);
					div_.appendChild(div__);
					div__.innerHTML = les_blocs[j]["contenu"];
					if(typeof(les_blocs[j]["titre"])!="undefined"&&les_blocs[j]["titre"]){
						var div_titre = document.createElement("div");
							div_titre.className = "titre_bloc";
						div__.appendChild(div_titre);
					}
				row.appendChild(div);
			}
			zone.appendChild(row);
		}
		parcours_recursif(zone);
		resize(true);
		init_page();
		setTimeout('afficher_page_fin()',100);
		//TIMEOUT_AFFICHER_PAGE = setTimeout('set_opacity(element("'+zone.id+'"),100);TIMEOUT_AFFICHER_PAGE=false;',100);
	}
	function afficher_page_fin(){
		var ligne_1 = element("contenu_page").firstChild;
		var colonne_g = ligne_1.firstChild;
		var colonne_d = colonne_g.nextSibling;
		colonne_d.firstChild.firstChild.lastChild.className = colonne_d.firstChild.firstChild.lastChild.className.replace("invisible","visible");
		colonne_g.className = colonne_g.className.replace("invisible","visible");
		var les_lignes = copier_tab(element("contenu_page").childNodes);
		for(var i=1;i<les_lignes.length;i++){ //On laisse la première ligne (colonne gauche, colonne droite)
			les_lignes[i].className = les_lignes[i].className.replace("invisible","visible");
		}
	}
	
	function init_page(){
		if(element("liste_audio")!=null)
			element("liste_audio").firstChild.click();
	}
	
function fenetre_ville(empecher_fermeture){
	if(typeof(empecher_fermeture)=="undefined")
		empecher_fermeture = false;
	filtre(true,empecher_fermeture);
	//On récupère le contenu pour ville
	var xhr = getXhr();
		xhr.open("POST", "01_include/struct_colorBox.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("colorbox=ville"+((empecher_fermeture)?'&ne_pas_fermer=1':''));
	colorbox(xhr.responseText,empecher_fermeture);
}
function fenetre_connexion(empecher_fermeture){
	if(typeof(empecher_fermeture)=="undefined")
		empecher_fermeture = false;
	filtre(true,empecher_fermeture);
	//On récupère le contenu pour la connexion
	var xhr = getXhr();
		xhr.open("POST", "01_include/struct_colorBox.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("colorbox=connexion"+((empecher_fermeture)?'&ne_pas_fermer=1':''));
	colorbox(xhr.responseText,empecher_fermeture);
}

/*
function colorbox(){
	var div = document.createElement("div");
		var header = document.createElement("img");
			header.src = "http://www.ensembleici.fr/img/bandeau-colorbox.png";
		div.appendChild(header);
		var p = document.createElement("p");
			p.appendChild(document.createTextNode("Pour naviguer sur le site, nous vous invitons à choisir une commune."));
		var h3 = document.createElement("h3");
			h3.appendChild(document.createTextNode("Rechercher une commune"));
		div.appendChild(p);
		div.appendChild(h3);
		var formulaire = document.createElement("div");
			formulaire.className = "gris";
			var input = document.createElement("input");
				input.id = "recherche_ville_input";
				input.type = "text";
				input.className = "vide";
				input.value = "code postal, ville";
				input.title = "code postal, ville";
				input.onfocus = function(){input_focus(this);};
				input.onblur = function(){input_blur(this);};
				input.onkeyup = function(){rechercher_ville(this);};
			formulaire.appendChild(input);
		div.appendChild(formulaire);
		var div_result = document.createElement("div");
			div_result.id = "recherche_ville_liste";
			div_result.appendChild(document.createElement("div"));
		div.appendChild(div_result);
	return message(div,{"max-width":516,"width":"100%","id":"fenetre_ville"});
}*/

function colorbox(contenu,empecher_fermeture){
	//1. On regarde si colorbox n'existe pas déjà
	if(element("colorbox")!=null){
		//2.1. On créait alors la colorbox avec le contenu correspondant
		var div = element("colorbox");
		div.firstChild.innerHTML = contenu;
	}
	else
		var div = message(contenu,{"max-width":600,"id":"colorbox","ne_pas_fermer":empecher_fermeture,"filtre":true});
	parcours_recursif(div);
	return div;
}

var XHR_RECHERCHE_VILLE = false;
function rechercher_ville(input){
	var texte = input.value;
	if(texte.length>=2&&texte!=input.title){
		/*if(XHR_RECHERCHE_MESSAGE!=false){
			clearTimeout(XHR_RECHERCHE_MESSAGE);
			XHR_RECHERCHE_MESSAGE = false;
		}*/
		//On recherche le texte.
		if(XHR_RECHERCHE_VILLE!=false){
			XHR_RECHERCHE_VILLE.abort();
		}
		XHR_RECHERCHE_VILLE = getXhr();
		XHR_RECHERCHE_VILLE.onreadystatechange = function(){
			if(XHR_RECHERCHE_VILLE.readyState == 4){
				if(XHR_RECHERCHE_VILLE.status == 200){
					var reponse = eval("("+XHR_RECHERCHE_VILLE.responseText+")");
					XHR_RECHERCHE_VILLE = false;
					creer_resultat_recherche_ville(reponse);
				}
			}
		};
		XHR_RECHERCHE_VILLE.open("POST", "03_ajax/recherche_ville_cp.php", true);
		XHR_RECHERCHE_VILLE.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		XHR_RECHERCHE_VILLE.send("m="+texte);
	}
	else{
		/*creer_resultat_recherche(false,libelle);
		if(XHR_RECHERCHE_MESSAGE!=false){
			clearTimeout(XHR_RECHERCHE_MESSAGE);
			XHR_RECHERCHE_MESSAGE = false;
		}*/
	}
}

function creer_resultat_recherche_ville(villes){
	var l = element("recherche_ville_liste").firstChild;
	vide(l);
	for(var i=0;i<villes.length;i++){
		var ligne = document.createElement("div");
			ligne.className = "recherche_ville_ligne";
			ligne.appendChild(document.createTextNode(villes[i]["cp"]+" - "+villes[i]["libelle"]));
			ajoute_evenement(ligne,"click",'selectionner_ville('+villes[i]["no"]+',"'+villes[i]["url"]+'","'+villes[i]["libelle"]+'")');
		l.appendChild(ligne);
	}
}

	function selectionner_ville(no,url,libelle){
		//On modifie la ville en session et cookie
		var xhr = getXhr();
		xhr.open("POST", "03_ajax/modifier_ville.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("no="+no);
		
		var param_url = getParametresURL();
		
		//On récupère les paramètres de l'url TODO (utiliser plutot getParam développé après)
		/*var regex_url = /^http:\/\/www\.ensembleici\.fr\/00_dev_sam\/(([a-z-]+)\.([0-9]+)(\.(editorial|agenda|forum|petites-annonces|structure)(\.([a-z-]+)\.([0-9]+))?)?\.html)?$/gi;
		console.log(url);
		console.log(regex_url);
		var param_url = regex_url.exec(document.URL);
		console.log(param_url);
		var libelle_ville = param_url[2];
		var numero_ville = param_url[3];
		var type = param_url[5];
		var titre_fiche = param_url[7];
		var no_fiche = param_url[8];*/
		//On met à jour la barre de menu (liens)
		element("menu_home").href = "http://www.ensembleici.fr/00_dev_sam/"+url+"."+no+".html";
		element("menu_editorial").href = "http://www.ensembleici.fr/00_dev_sam/"+url+"."+no+".editorial.30km.html";
		element("menu_agenda").href = "http://www.ensembleici.fr/00_dev_sam/"+url+"."+no+".agenda.html";
		element("menu_petiteannonce").href = "http://www.ensembleici.fr/00_dev_sam/"+url+"."+no+".petite-annonce.30km.html";
		element("menu_repertoire").href = "http://www.ensembleici.fr/00_dev_sam/"+url+"."+no+".structure.html";
		element("menu_forum").href = "http://www.ensembleici.fr/00_dev_sam/"+url+"."+no+".forum.30km.html";
		
		//On met à jour la barre de sous menu (libelle)
		element("h1ville").firstChild.data = libelle;
		
		//On recréait la nouvelle url (dans le navigateur)
		var new_url = "http://www.ensembleici.fr/00_dev_sam/";
			new_url += url+"."+no;
			if(typeof(param_url["type"])!="undefined"&&param_url["type"]!=""){
				new_url += "."+param_url["type"];
				if(typeof(param_url["no_fiche"])!="undefined"&&param_url["no_fiche"]!="")
					new_url += "."+param_url["titre_fiche"]+"."+param_url["no_fiche"];
			}
			new_url += ".html";
		history.pushState({ path: this.path }, '', new_url);
		
		//On recharge le contenu de la page sur laquelle l'utilisateur se trouve
		charger_page();
		supprime_message("colorbox",true);
		//filtre(false,true);
	}
	

function menu_utilisateur_smartphone(laisser_ferme){
	if(typeof(laisser_ferme)=="undefined")
		laisser_ferme = false;
	if(!dans_tab("menu_utilisateur_ouvert",element("page").className.split(" "))&&!laisser_ferme){
		menu_site_smartphone(true);
		filtre(!laisser_ferme);
		element("page").className += " menu_utilisateur_ouvert";
		element("menu_utilisateur").className += " ouvert";
		/*element("menu_utilisateur").className += " ouvert";
		element("contenu").className += " ouvert";*/
	}
	else{
		filtre(!laisser_ferme);
		
		element("page").className = element("page").className.replace(" menu_utilisateur_ouvert","");
		element("menu_utilisateur").className = element("menu_utilisateur").className.replace(" ouvert","");
		/*
		element("menu_utilisateur").className = element("menu_utilisateur").className.replace(" ouvert","");
		element("contenu").className = element("contenu").className.replace(" ouvert");*/
	}
}

function filtre_click(){
	//On ferme le menu utilisateur ou le menu du site
	menu_utilisateur_smartphone(true);
	menu_site_smartphone(true);
	if(element("colorbox")!=null&&!dans_tab("persistant",element("filtre").className.split(" ")))
		supprime_message("colorbox");
}

function input_focus(input){
	if(input.value==input.title){
		input.value = "";
		input.className = input.className.replace(" vide","");
	}
}
function input_blur(input){
	if(input.value==input.title||input.value==""){
		input.className = input.className+" vide";
		input.value = input.title;
	}
}

var INPUT_RECHERCHE_VILLE_BLUR = false;
function input_recherche_ville_blur(input){
	//if(element("BDDno_ville").value!=0) vide(element("recherche_ville_liste").firstChild);
	input_blur(input);
	if(INPUT_RECHERCHE_VILLE_BLUR!=false)
		clearTimeout(INPUT_RECHERCHE_VILLE_BLUR);
	//INPUT_RECHERCHE_VILLE_BLUR = setTimeout('if(element("BDDno_ville").value!=0)element("recherche_ville_liste").className="vide"',200);
}
function input_recherche_ville_focus(input){
	rechercher_ville(input);
	input_focus(input);
	if(INPUT_RECHERCHE_VILLE_BLUR!=false){
		clearTimeout(INPUT_RECHERCHE_VILLE_BLUR);
		INPUT_RECHERCHE_VILLE_BLUR=false;
	}
	element("recherche_ville_liste").className = "";
}


/*function diaporama_editorial(){

}*/

function diaporama_editorial_next(){
	console.log("droite");
	var zone = element("home_editorial_bloc");
	if(zone.className=="editorial_1"){
		zone.className = "editorial_2";
	}
	else if(zone.className=="editorial_2"){
		zone.className = "editorial_3";
	}
}
function diaporama_editorial_previous(){
	console.log("gauche");
	var zone = element("home_editorial_bloc");
	if(zone.className=="editorial_2"){
		zone.className = "editorial_1";
	}
	else if(zone.className=="editorial_3"){
		zone.className = "editorial_2";
	}
}
function select_editorial(num){
	element("home_editorial_bloc").className = "editorial_"+num;
	var boules = element("home_editorial_boules").childNodes;
	for(var i=0;i<boules.length;i++){
		if((num-1)!=i)
			boules[i].className = "";
		else
			boules[i].className = "actif";
	}
}

function parcours_recursif(depart,el){
	if(typeof(depart)=="undefined")
		depart = document.body;
	if(typeof(el)=="undefined")
		el = depart;
	//1. On regarde la class Name de l'Ã©lÃ©ment.
	prepare_element(el);

	if(el.firstChild){
		parcours_recursif(depart,el.firstChild);
	}
	else{ //On est au bout d'une branche, on vas commencer la remontÃ©.
		if(el.nextSibling){
			parcours_recursif(depart,el.nextSibling);
		}
		else{
			while(el!=depart&&!el.nextSibling){
				el = el.parentNode;
			}
			if(el!=depart) //on a trouvÃ© un nextSibling dans un parent
				parcours_recursif(depart,el.nextSibling);
			else //On a tout remontÃ© sans trouver de nouvel Ã©lÃ©ment.
				null;
		}
	}
}

function prepare_element(el){
	if(el.className!="undefined"&&el.className!=""){
		var reg_infobulle = /infobulle(\[.+\])?/gi;
		if(reg_infobulle.test(el.className)){ //L'Ã©lÃ©ment Ã  une infobulle.
			//var reg_infobulle = /infobulle(\[([\d\s\w'"\\]*)(|(haut|bas|gauche|droite|left|top|bottom|right))?\])?/gi;
			var reg_infobulle = /infobulle(\[(.+)\])?/gi;
			var valeurs = reg_infobulle.exec(el.className);
			var continuer = true;
			//On capture les paramÃ¨tres de l'infobulle (si le deuxiÃ¨me est manquant, on met par dÃ©faut Ã  droite).
			if(valeurs.length>2&&typeof(valeurs[2])!="undefined"&&valeurs[2]!=""){
				var contenu = valeurs[2].split("|");
				if(contenu.length>1){ //Tout est dans le crochet.
					var texte = contenu[0];
					var position = contenu[1];
				}
				else{	//On regarde le title
					if(typeof(el.title)!="undefined"&&el.title!=""){
						var texte = el.title;
						var position = contenu[0];
					}
					else{
						continuer = false;
					}
				}
			}
			else{
				if(typeof(el.title)!="undefined"&&el.title!=""){
					var texte = el.title;
					var position = "droite";
				}
				else{
					continuer = false;
				}
			}
			if(continuer){
				el.title = "";
				console.log(texte);
				el.contenu_infobulle = encodeURIComponent(texte);
				el.className = el.className.replace(/infobulle(\[(.+)\])?/gi,"");
				ajoute_evenement(el,"mouseover",'if(typeof(infobulle)=="function"){infobulle(this,"'+texte+'","'+encodeURIComponent(position)+'")}');
			}
		}
	}
	
	if(typeof(el.tagName)!="undefined"&&el.tagName.toLowerCase()=="input"){ //INPUTS
		if(el.id=="input_distance")
			change_distance(el,true);
			
		if(el.type=="text"){
			el.onfocus = function(){input_focus(this);};
			el.onblur = function(){input_blur(this);};
			if(dans_tab("recherche",el.className.split(" "))){
				//el.onkeyup = function(){rechercher(this);}; TODO (recherche rapide)
				el.onblur = function(){input_recherche_blur(this)};
				el.onfocus = function(){input_recherche_focus(this);};
			}
			else if(dans_tab("recherche_ville",el.className.split(" "))){
				el.onkeyup = function(){rechercher_ville(this);};
				el.onblur = function(){input_recherche_ville_blur(this)};
				el.onfocus = function(){input_recherche_ville_focus(this);};
			}
			else if(dans_tab("recherche_tag",el.className.split(" "))){
				el.onkeyup = function(){rechercher_tag(this);};
				el.onblur = function(){input_recherche_tag_blur(this)};
				el.onfocus = function(){input_recherche_tag_focus(this);};
			}
			else if(dans_tab("heure",el.className.split(" "))){
				el.onblur = function(){input_heures_blur(this)};
			}
			else if(el.className.indexOf("calendrier")>-1){
				_calendrier_init(el);
			}
			input_blur(el);
		}
		else if(el.type=="password"){
			el.onfocus = function(){input_focus(this);};
			el.onblur = function(){input_blur(this);};
			input_blur(el);
		}
		else if(el.type=="button"){
			if(dans_tab("recherche",el.className.split(" ")))
				el.onclick = function(){rechercher(this.previousSibling);};
			if(el.id=="colorbox_connexion")
				el.onclick = function(){fenetre_connexion((this.className=="ne_pas_fermer"));};
			if(el.id=="colorbox_ville")
				el.onclick = function(){fenetre_ville((this.className=="ne_pas_fermer"));};
		}
		else if(el.type=="file"&&el.id!=""&&dans_tab("fichier",el.className.split(" "))){
			/***
				1. On récupère les paramètres.
			*/
				var reg_poids = /poids\[([0-9]*)\|([0-9]*)\]/gi;
				var reg_type = /type\[(image|pdf)\]/gi;
				var reg_url = /url\[([a-z0-9\._\/-]+)\]/gi;
				//var rogner = el.className.index;
				var poids = reg_poids.exec(el.className);
				if(poids!=null&&poids.length==3){
					var poids_min = poids[1];
					var poids_max = poids[2];
				}
				else{
					var poids_min = false;
					var poids_max = false;
				}
				var type = reg_type.exec(el.className);
				if(type!=null&&type.length==2){
					var type = type[1];
				}
				else
					var type = false;
				var url = reg_url.exec(el.className);
				if(url!=null&&url.length==2){
					var url = url[1];
				}
				else
					var url = "";
				var div_insert = el.nextSibling;
				var parent = el.parentNode;
				var id = el.id;
				var input_name = el.name;
			/***
				2. On créait le visuel.
			*/
			var div_img_temoin = document.createElement("div");
				div_img_temoin.className = "input_file";
				div_img_temoin.id = "visuel_"+id;
				var div_overflow_img_temoin = document.createElement("div");
					div_overflow_img_temoin.className = "input_image";
					var img_temoin = document.createElement("img");
						img_temoin.className = "input_image_visuel";
						img_temoin.onload = function(){formulaire_image_load(this);};
					var btn_changer = document.createElement("div");
						btn_changer.className = "input_image_remplacer";
					var img_legende = document.createElement("span");
						img_legende.className = "input_image_legende";
					var filtre_chargement = document.createElement("div");
						filtre_chargement.className = "filtre_chargement actif";
						filtre_chargement.id = "filtre_"+id;
						var img_chargement = document.createElement("img");
							img_chargement.src = "../img/logo_ei_simple.png";
						filtre_chargement.appendChild(img_chargement);
			div_img_temoin.appendChild(div_overflow_img_temoin);
				div_overflow_img_temoin.appendChild(img_temoin);
				div_overflow_img_temoin.appendChild(btn_changer);
				div_overflow_img_temoin.appendChild(img_legende);
				div_overflow_img_temoin.appendChild(filtre_chargement);
			if(div_insert!=null)
				parent.insertBefore(div_img_temoin,div_insert);
			else
				parent.appendChild(div_img_temoin);
			ajoute_evenement(div_img_temoin,"click",'if(!FORMULAIRE_FILE_DELETE_CLICK){element("fichier_courant_'+id+'").click();}else{FORMULAIRE_FILE_DELETE_CLICK=false;}');
			rotation_chargement("filtre_"+id);
			
			
			if(!dans_tab("sans_supression",el.className.split(" "))){
				var btn_delete = document.createElement("img");
					btn_delete.className = "fermer";
					if(url!="")
						btn_delete.src = "../img/img_colorize.php?uri=ico_delete.png&c=E5E5E5";
					else{
						btn_delete.src = "../img/img_colorize.php?uri=ico_delete.png&c=4E4E4E";
						btn_delete.onmouseout = function(){this.src="../img/img_colorize.php?uri=ico_delete.png&c=4E4E4E";};
					}
					//ajoute_evenement(btn_delete,'click','formulaire_file_delete("'+el.id+'","'+type+'",'+((temps_reel)?"true":"false")+',"'+page+'","'+before_ajax+'","'+after_ajax+'",'+((!func_envoi_formulaire)?func_envoi_formulaire:'"'+func_envoi_formulaire+'"')+')');
					ajoute_evenement(btn_delete,'click','FORMULAIRE_FILE_DELETE_CLICK=true;');
				div_img_temoin.appendChild(btn_delete);
				//NOOO ajoute_evenement(btn_delete,"mouseover",'this.src="03_interface/img_colorize.php?uri=ico_delete.png&c=FE0000";infobulle(this,"Supprimer l\'image","bas gauche")');
			}
			
			/***
				3. On créait l'input caché qui en contiendra la valeur
			*/
			var input = document.createElement("input");
				input.type = "hidden";
				input.value = url;
				input.id = id;
				input.name = input_name;
				//label.htmlFor = "fichier_courant_"+el.id;
			if(div_insert!=null)
				parent.insertBefore(input,div_insert);
			else
				parent.appendChild(input);
			el.id = "_"+el.id; //C'est maintenant l'input caché qui possède cet id, alors on le remplace.
			//el = input;
			
			/***
				4. On créait le formulaire invisible permettant l'envoi du fichier en ajax.
			*/
			var formulaire_fichier = document.createElement("form");
				formulaire_fichier.style.display = "none";
				formulaire_fichier.enctype = "multipart/form-data";
				formulaire_fichier.method = "post";
				formulaire_fichier.onsubmit = function(){return false;};
			if(div_insert!=null)
				parent.insertBefore(formulaire_fichier,div_insert);
			else
				parent.appendChild(formulaire_fichier);
			
				var input_file = document.createElement("input");
					input_file.id = "fichier_courant_"+id;
					input_file.name = "fichier_courant";
					input_file.type = "file";
				formulaire_fichier.appendChild(input_file);
				ajoute_evenement(input_file,"change",'formulaire_file(this,"'+type+'",false);');
			
				if(poids_max!=false){
					var input_max = document.createElement("input");
						input_max.name = "fichier_courant_poids_max";
						input_max.type = "hidden";
						input_max.value = parseInt(poids_max)*1000;
					formulaire_fichier.appendChild(input_max);
				}
				if(poids_min!=false){
					var input_min = document.createElement("input");
						input_min.name = "fichier_courant_poids_min";
						input_min.type = "hidden";
						input_min.value = parseInt(poids_min)*1000;
					formulaire_fichier.appendChild(input_min);
				}
			
			if(type!=false){
				var input_accept = document.createElement("input");
					input_accept.name = "fichier_courant_accept";
					input_accept.type = "hidden";
					input_accept.value = type;
				formulaire_fichier.appendChild(input_accept);
				if(type=="image"){
					if(url!="")
						btn_changer.appendChild(document.createTextNode("Modifier l'image"));
					else
						btn_changer.appendChild(document.createTextNode("Ajouter une image"));
					img_legende.className += " image";
					input_file.accept = "image/*";
					img_temoin.src = (url!="")?"http://www.ensembleici.fr/"+url:"../img/img_colorize.php?uri=aucune_image.png&c=4e4e4e";
					/*TODO SUPPRESSION
					if(typeof(form[i]["supprimer"])=="boolean"&&form[i]["supprimer"]&&typeof(form[i]["value"])=="string"&&form[i]["value"]!="")
						ajoute_evenement(btn_delete,"mouseover",'this.src="03_interface/img_colorize.php?uri=ico_delete.png&c=FE0000";infobulle(this,"Supprimer l\'image","bas gauche")');
				*/
				}
				else if(type=="pdf"){
					if(url!="")
						btn_changer.appendChild(document.createTextNode("Modifier le pdf"));
					else
						btn_changer.appendChild(document.createTextNode("Ajouter un pdf"));
					img_legende.className += " pdf";
					input_file.accept = "application/pdf";
					img_temoin.src = "../img/img_colorize.php?uri=ico_pdf.png&c=216,36,28";
					/*TODO SUPPRESSION
					if(typeof(form[i]["supprimer"])=="boolean"&&form[i]["supprimer"]&&typeof(form[i]["value"])=="string"&&form[i]["value"]!="")
						ajoute_evenement(btn_delete,"mouseover",'this.src="03_interface/img_colorize.php?uri=ico_delete.png&c=FE0000";infobulle(this,"Supprimer le fichier pdf","bas gauche")');
					*/
					/*var span_nom_fichier = document.createElement("span");
						span_nom_fichier.className = "nom_fichier";
						span_nom_fichier.appendChild(document.createTextNode(form[i]["value"]));
					div_overflow_img_temoin.appendChild(span_nom_fichier);*/
				}
				 //AUTRES EXTENSIONS DE FICHIERS À AJOUTER AU FUR ET À MESURE
				 // TODO
				else{
					if(url!="")
						btn_changer.appendChild(document.createTextNode("Modifier le fichier"));
					else
						btn_changer.appendChild(document.createTextNode("Ajouter un fichier"));
					img_legende.className += " fichier";
					
					/*TODO SUPPRESSION
					if(typeof(form[i]["supprimer"])=="boolean"&&form[i]["supprimer"]&&typeof(form[i]["value"])=="string"&&form[i]["value"]!="")
						ajoute_evenement(btn_delete,"mouseover",'this.src="03_interface/img_colorize.php?uri=ico_delete.png&c=FE0000";infobulle(this,"Supprimer le fichier pdf","bas gauche")');
				*/
				}
			}
			else{
				btn_changer.appendChild(document.createTextNode("Modifier le fichier"));
				img_legende.className += " fichier";
			}
			//http://www.sudplanete.net/kit.admin.dev.l-evenement-qui-tue#presentation
		}
	}
	else if(typeof(el.tagName)!="undefined"&&el.tagName.toLowerCase()=="textarea"){ //EDITEUR DE TEXTE
		//CKEDITOR.replace(el.id,{language:'fr',uiColor: '#EDEDED',skin:'office2013'});
		//console.log("editor : "+el.id);
		//if(typeof(CKEDITOR.instances[el.id])!="undefined")
		//	CKEDITOR.instances[el.id].destroy();
		if(el.id!=""&&dans_tab("editeur",el.className.split(" "))){
			var reg_height = /height\[([0-9]+(?:px|%))\]/gi;
				var height = reg_height.exec(el.className);
			var param_ck = {language:'fr',uiColor: '#EDEDED'};
			if(height!=null&&height.length==2)
				param_ck["height"] = height[1];console.log(param_ck);
			CKEDITOR.replace(el.id,param_ck);
		}
		else{
			el.onfocus = function(){input_focus(this);};
			el.onblur = function(){input_blur(this);};
			input_blur(el);
		}
	}
	//Lien internes (sans les ancres)
	if(typeof(el.tagName)!="undefined"&&el.tagName.toLowerCase()=="a"&&el.href.indexOf("http://www.ensembleici.fr")>-1&&el.href.indexOf("#")==-1&&el.target!="_blank"){
		el.onclick = function(e){choix_page(this,e);};
	}
	else if(typeof(el.tagName)!="undefined"&&el.tagName.toLowerCase()=="a"&&el.href.indexOf("http://www.ensembleici.fr")==-1&&el.target!="_blank"){
		el.target = "_blank";
	}
}

/*function prepare_element(el){
	if(el.className!="undefined"&&el.className!=""){
		var reg_infobulle = /infobulle(\[.+\])?/gi;
		if(reg_infobulle.test(el.className)){ //L'Ã©lÃ©ment Ã  une infobulle.
			//var reg_infobulle = /infobulle(\[([\d\s\w'"\\]*)(|(haut|bas|gauche|droite|left|top|bottom|right))?\])?/gi;
			var reg_infobulle = /infobulle(\[(.+)\])?/gi;
			var valeurs = reg_infobulle.exec(el.className);
			var continuer = true;
			//On capture les paramÃ¨tres de l'infobulle (si le deuxiÃ¨me est manquant, on met par dÃ©faut Ã  droite).
			if(valeurs.length>2&&typeof(valeurs[2])!="undefined"&&valeurs[2]!=""){
				var contenu = valeurs[2].split("|");
				if(contenu.length>1){ //Tout est dans le crochet.
					var texte = contenu[0];
					var position = contenu[1];
				}
				else{	//On regarde le title
					if(typeof(el.title)!="undefined"&&el.title!=""){
						var texte = el.title;
						var position = contenu[0];
					}
					else{
						continuer = false;
					}
				}
			}
			else{
				if(typeof(el.title)!="undefined"&&el.title!=""){
					var texte = el.title;
					var position = "droite";
				}
				else{
					continuer = false;
				}
			}
			if(continuer){
				el.title = "";
				console.log(texte);
				el.contenu_infobulle = encodeURIComponent(texte);
				el.className = el.className.replace(/infobulle(\[(.+)\])?/gi,"");
				ajoute_evenement(el,"mouseover",'if(typeof(infobulle)=="function"){infobulle(this,"'+texte+'","'+encodeURIComponent(position)+'")}');
			}
		}
	}
	if(typeof(el.tagName)!="undefined"&&el.tagName.toLowerCase()=="input"){
		if(el.id=="input_distance")
			change_distance(el,true);
	}
	//Lien internes
	if(typeof(el.tagName)!="undefined"&&el.tagName.toLowerCase()=="a"&&el.href.indexOf("http://www.ensembleici.fr")>-1){
		el.onclick = function(e){choix_page(this,e);};
	}
}*/

function scrolling(){
	console.log("scroll");
	var link = get_link_cssBig();
	if(link!=false){ //Le fichier existe (MENU GRAND ECRAN)
		console.log("scroll_grand");
		var scroll = getScrollPosition()["y"];
		//On règle la position du menu de gauche, la position du menu d'en haut
		//MENU HAUT
		var defaut_top = haut("menu")-parseInt(getStyle(element("menu"),"top"));
		if(scroll>defaut_top){
			element("menu").style.top = scroll-defaut_top+"px";
			element("menu").style.boxShadow = "0px 0px 20px -10px rgba(0, 0, 0, 1)";
			if(element("exprimez_vous")!=null){
				var defautForum_top = offsetAbs(element("exprimez_vous"))["top"]-parseInt(getStyle(element("exprimez_vous"),"top"));
				if(scroll+hauteur("menu")>defautForum_top)
					element("exprimez_vous").style.top = scroll+hauteur("menu")-defautForum_top+"px";
				else
					element("exprimez_vous").style.top = null;
			}
		}
		else{
			element("menu").style.top = null;
			element("menu").style.boxShadow = "none";
		}
		//SOUS MENU
		/*var hauteur_offset = haut("contenu");
		console.log("h : "+hauteur_offset)
		var hauteur_modif = (!isNaN(parseInt(element("barressmenu").style.top)))?parseInt(element("barressmenu").style.top):0;
		var scroll_menu = scroll+hauteur("menu")-hauteur("barressmenu");
		//var scroll_menu_descente = scroll_menu;
		
		if(scroll_menu>hauteur_offset){
			//Si descente
			if(hauteur_modif<scroll_menu-hauteur_offset)
				element("barressmenu").style.top = scroll_menu-hauteur_offset+"px";
			//Si montée
			else{
				scroll_menu += hauteur("barressmenu");
				element("barressmenu").style.top = scroll_menu-hauteur_offset+"px";
			}
		}
		else{
			element("barressmenu").style.top = 0+"px";
		}*/
		//MENU GAUCHE
		var defaut_top = 305;
		var scroll_menu = scroll+hauteur("menu")+21; //21 : espace entre le menu du haut et ce menu-ci
		if(scroll_menu>defaut_top){
			if(scroll_menu+hauteur("menu_utilisateur")>haut("footer")-30) //30 c'est juste un "margin" qu'on laisse entre menu utilisateur et footer
				scroll_menu = haut("footer")-30-hauteur("menu_utilisateur");
			element("menu_utilisateur").style.top = scroll_menu+"px";
		}
		else{
			element("menu_utilisateur").style.top = null;
		}
	}
	else{ //MENU PETIT ECRAN
		console.log("scroll_petit");
		var scroll = element("page").scrollTop;
		if(!isNaN(parseInt(element("menu_utilisateur").style.top))) element("menu_utilisateur").style.top = null;
		if(!isNaN(parseInt(element("menu").style.top))) element("menu").style.top = null;
	}
	//Enfin, on regle la position du filtre
	element("filtre").style.top = scroll+"px";
}


function getScrollPosition(){
return {"x":((document.documentElement && document.documentElement.scrollLeft) || window.pageXOffset || self.pageXOffset || document.body.scrollLeft),"y":((document.documentElement && document.documentElement.scrollTop) || window.pageYOffset || self.pageYOffset || document.body.scrollTop)};
}

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

function scroll_progressif(scroll_fin,pas,timer){
if(getScrollPosition()["y"]>scroll_fin+pas){
	set_documentScroll(getScrollPosition()["y"]-pas);
	setTimeout('scroll_progressif('+scroll_fin+')',timer);
}
else if(getScrollPosition()["y"]<scroll_fin-pas){
	set_documentScroll(getScrollPosition()["y"]+pas);
	setTimeout('scroll_progressif('+scroll_fin+')',timer);
}
else{
	set_documentScroll(scroll_fin);
}
}

function remonter_progressif(){
if(getScrollPosition()["y"]>180){
	scroll_progressif(180,10,100);
}
}











function ouvrir_liste_fichiers(menu){
	element(menu).className+=" ouvert";
	setTimeout('fermer_liste_fichiers("'+menu+'")',200);
}
function fermer_liste_fichiers(menu){
	element(menu).className=element(menu).className.replace(" ouvert","");
}

/*function lancer_fichier_audio(zone,url){
	if(typeof(zone)=="string")
		zone = element(zone);
	if(element(zone.id+"_fichier")!=null)
		element(zone.id+"_fichier").parentNode.removeChild(element(zone.id+"_fichier"));
	var params = "url="+url;
		params += "&color=23AADD&auto_play=false&buying=true&liking=true&download=true&sharing=true&show_artwork=false&show_comments=true&show_playcount=true&show_user=false&hide_related=true&visual=false&start_track=0&callback=true&show_reposts=false";
		
	var iframe = document.createElement("iframe");
		iframe.src = "https://w.soundcloud.com/player/?"+params;
		iframe.id = zone.id+"_fichier";
	zone.appendChild(iframe);
}*/
function fermer_fichier(){
	if(element("zone_fichiers_persistant")!=null) element("zone_fichiers_persistant").parentNode.removeChild(element("zone_fichiers_persistant"));
}
function lancer_fichier_audio(zone,url){
	var colonne_droite = element("colonne_droite").firstChild.firstChild;
	//TODO
	//1. On regarde si une zone fichier persistant existe dans la colonne droite, si oui on la supprime
	if(element("zone_fichiers_persistant")!=null) element("zone_fichiers_persistant").parentNode.removeChild(element("zone_fichiers_persistant"));
	if(element("zone_fichiers")!=null){
		var zone_fichiers = element("zone_fichiers").cloneNode(true);
		zone_fichiers.removeChild(zone_fichiers.lastChild);
		zone_fichiers.className += " zone_fichiers";
		zone_fichiers.id = "";
		var zone_fichiers_persistant = document.createElement("div");
			zone_fichiers_persistant.appendChild(zone_fichiers);
		
		var params = "url="+url;
			params += "&color=23AADD&auto_play=true&buying=true&liking=true&download=true&sharing=true&show_artwork=false&show_comments=true&show_playcount=true&show_user=false&hide_related=true&visual=false&start_track=0&callback=true&show_reposts=false";
		
		var iframe = document.createElement("iframe");
			iframe.src = "https://w.soundcloud.com/player/?"+params;
			iframe.id = "fichier_audio_persistant";
		zone_fichiers.appendChild(iframe);
	}
	else
		var zone_fichiers_persistant = document.createElement("div");
	zone_fichiers_persistant.id = "zone_fichiers_persistant";
	//2. On créait la nouvelle zone
	
	/*if(typeof(zone)=="string")
		zone = element(zone);
	if(element(zone.id+"_fichier")!=null)
		element(zone.id+"_fichier").parentNode.removeChild(element(zone.id+"_fichier"));*/
	
	var img_fermer = document.createElement("img");
		img_fermer.className = "fermer";
		img_fermer.src = 'img/img_colorize.php?uri=ico_delete.png&c=133,144,151';
		img_fermer.onclick = function(){fermer_fichier();};
	zone_fichiers_persistant.appendChild(img_fermer);
	colonne_droite.insertBefore(zone_fichiers_persistant,colonne_droite.firstChild);
	//img_load(zone_fichiers_persistant.firstChild);
}

function change_distance(input,ne_pas_recharger){
	var div = element("libelle_distance");
	var nouvelle_distance = "";
	if(input.value==input.min){ //Seulement la ville
		div.firstChild.style.display = "inline";
		div.lastChild.previousSibling.style.display = "none";
		div.lastChild.style.display = "none";
	}
	else{
		if(input.value==input.max){ //Tous
			div.firstChild.style.display = "none";
			div.lastChild.previousSibling.style.display = "none";
			div.lastChild.style.display = "inline";
			nouvelle_distance = "tous";
		}
		else{ //Distance en km
			div.firstChild.style.display = "inline";
			div.lastChild.previousSibling.style.display = "inline";
			div.lastChild.style.display = "none";
				div.lastChild.previousSibling.firstChild.data = " + "+input.value+"km";
			nouvelle_distance = input.value+"km";
		}
	}
	//div.style.left = Math.floor(input.value*100/input.max)+"%";
	div.style.left = (largeur(div.parentNode)-largeur(input))/2-largeur(div)/2+Math.floor(input.value/input.max*largeur(input))+"px";
	
	if(typeof(ne_pas_recharger)=="undefined"||!ne_pas_recharger){
		var parametres = getParametresURL();
			parametres["distance"] = nouvelle_distance;
		setUrl(parametres,true);
	}
}


//TODO Suppression de la fonction ci-dessous devenue obsolète
function change_tri(el,ne_pas_recharger){
	if(el.className!="actif"){
		element("div_tri").firstChild.className = "";
		element("div_tri").firstChild.nextSibling.className = "";
		element("div_tri").lastChild.className = "";
		
		el.className = "actif";
		nouveau_tri = el.id.replace("tri_","");
		
		if(typeof(ne_pas_recharger)=="undefined"||!ne_pas_recharger){
			var parametres = getParametresURL();
				parametres["tri"] = nouveau_tri;
			setUrl(parametres);
		}
		
	}
}

function change_date(input){
	var reg_date = /[0-9]{2}\/[0-9]{2}\/[0-9]{4}/gi;
	if(reg_date.test(input.value)){
		var parametres = getParametresURL();
			parametres["date"] = input.value.replace(/\//gi, '-');
		setUrl(parametres,true);
	}
}

function getParametresURL(url){
	//var regex_url = /(?:http:\/\/www\.ensembleici\.fr\/00_dev_sam\/)?([a-z-]+)\.([0-9]+)(?:\.(editorial|agenda|forum|petite-annonce|structure)(?:\.([a-z0-9-]+)\.([0-9]+))?(?:\.([0-9]+km|tous))?(?:\.du-([0-9]{2}-[0-9]{2}-[0-9]{4}))?(?:\.(distance|reputation|date))?(?:\.page([0-9]+))?)?\.html$/gi;
	var regex_url = /(?:http:\/\/www\.ensembleici\.fr\/00_dev_sam\/)?([a-z-]+)\.([0-9]+)(?:\.(editorial|agenda|forum|petite-annonce|structure)(?:\.(?:tag([0-9]+(?:-[0-9]+)*)))?(?:\.([a-z0-9-]+)\.([0-9]+))?(?:\.([0-9]+km|tous))?(?:\.du-([0-9]{2}-[0-9]{2}-[0-9]{4}))?(?:\.(distance|reputation|date))?(?:\.page([0-9]+))?)?\.html$/gi;
	if(typeof(url)=="undefined")
		var url = document.URL;
	var param_url = regex_url.exec(url);
	var retour = new Array();
		if(typeof(param_url)=="object"&&param_url!=null){
			retour["libelle_ville"] = (typeof(param_url[1])!="undefined")?param_url[1]:"";
			retour["numero_ville"] = (typeof(param_url[2])!="undefined")?param_url[2]:"";
			retour["type"] = (typeof(param_url[3])!="undefined")?param_url[3]:"";
			retour["tags"] = (typeof(param_url[4])!="undefined")?param_url[4]:"";
		
			retour["titre_fiche"] = (typeof(param_url[5])!="undefined")?param_url[5]:"";
			retour["no_fiche"] = (typeof(param_url[6])!="undefined")?param_url[6]:"";
	
			retour["distance"] = (typeof(param_url[7])!="undefined")?param_url[7]:"";
			retour["date"] = (typeof(param_url[8])!="undefined")?param_url[8]:"";
			retour["tri"] = (typeof(param_url[9])!="undefined")?param_url[9]:"";
			retour["num_page"] = (typeof(param_url[10])!="undefined")?param_url[10]:"";
		}
		else{
			var regex_url = /(?:http:\/\/www\.ensembleici\.fr\/00_dev_sam\/)?([a-z-]+)(?:\.([a-z-]+))?(?:\.([0-9]+))?(?:\.(generalites|thematique|details|illustration|validation))?\.html$/gi;
			var param_url = regex_url.exec(url);
			if(typeof(param_url)=="object"&&param_url!=null){
				console.log(param_url);
				retour["type"] = (typeof(param_url[1])!="undefined")?param_url[1]:"";
				retour["sous_page"] = (typeof(param_url[2])!="undefined")?param_url[2]:"";
				retour["no"] = (typeof(param_url[3])!="undefined")?param_url[3]:"";
				retour["etape"] = (typeof(param_url[4])!="undefined")?param_url[4]:"";
			}
			else{
				var regex_url = /(?:http:\/\/www\.ensembleici\.fr\/00_dev_sam\/)?recherche\.php\?q=(.*)$/gi;
				var param_url = regex_url.exec(url);
				if(typeof(param_url)=="object"&&param_url!=null){
					console.log(param_url);
					retour["type"] = "recherche";
					retour["q"] = (typeof(param_url[1])!="undefined")?param_url[1]:"";
				}
			}
		}
	return retour;
}
function setUrl(parametres,raz_pages){
	if(typeof(raz_pages)=="undefined")
		raz_pages = false;
	var url = "http://www.ensembleici.fr/00_dev_sam/";
		url += parametres["libelle_ville"];
		url += "."+parametres["numero_ville"];
		if(parametres["type"]!="")
			url += "."+parametres["type"];
		if(parametres["no_fiche"]!=""){ //C'est une fiche
			url += "."+parametres["titre_fiche"];
			url += "."+parametres["no_fiche"];
		}
		else{ //Sinon
			//TODO TAGS
			if(parametres["distance"]!="")
				url += "."+parametres["distance"];
			if(parametres["date"]!="")
				url += ".du-"+parametres["date"];
			if(parametres["tri"]!=""&&parametres["tri"]!="date")
				url += "."+parametres["tri"];
			if(parametres["num_page"]!=""&&parametres["num_page"]>1&&!raz_pages)
				url += ".page"+parametres["num_page"];
		}
		url += ".html";
	history.pushState({ path: this.path }, '', url);
	charger_page();
}

TIMEOUT_BARRE_CHARGEMENT = false;
function barre_chargement(etat){console.log("laaaaaaaaaaaaaaaaaaa "+etat);
	if(TIMEOUT_BARRE_CHARGEMENT!=false){
		clearTimeout(TIMEOUT_BARRE_CHARGEMENT);
		TIMEOUT_BARRE_CHARGEMENT = false;
	}
	
	if(etat==0){ //On créait la barre
		if(element("barre_chargement").firstChild!=null)
			element("barre_chargement").removeChild(element("barre_chargement").firstChild);
		element("barre_chargement").className = "chargement";
		element("barre_chargement").appendChild(document.createElement("div"));
	}
	else if(etat==1){
		element("barre_chargement").className = "chargement etat1";
	}
	else if(etat==2){
		element("barre_chargement").className = "chargement etat2";
	}
	else if(etat==3){
		element("barre_chargement").className = "chargement etat3";
	}
	else if(etat==4){
		element("barre_chargement").className = "chargement etat4";
		TIMEOUT_BARRE_CHARGEMENT = setTimeout('barre_chargement(5);',300);
	}
	else if(etat==5){
		if(element("barre_chargement").firstChild!=null)
			element("barre_chargement").removeChild(element("barre_chargement").firstChild);
	}/*
	if(typeof(ouvrir)=="undefined")
		ouvrir = true;
	if(ouvrir){ //On ouvre le filtre
		element("barre_chargement").className = "chargement";
		setTimeout('barre_chargement(false);',600);
	}
	else{ //On ferme le filtre
		element("barre_chargement").removeChild(element("barre_chargement").firstChild);
		element("barre_chargement").className="";
		element("barre_chargement").appendChild(document.createElement("div"));
	}*/
}

function plier_deplier(div){
	if(div.className.indexOf("deplier")>-1){
		div.className = div.className.replace("deplier","plier");
	}
	else{
		div.className = div.className.replace("plier","deplier");
	}
}



function set_vie(select){
	var vie = select.value.split("_")[0];
	element("boite_tag").className = vie;
}

var XHR_RECHERCHE_TAG = false;
var INPUT_RECHERCHE_TAG_BLUR = false;
function input_recherche_tag_blur(input){
	input_blur(input);
	/*if(INPUT_RECHERCHE_TAG_BLUR!=false)
		clearTimeout(INPUT_RECHERCHE_TAG_BLUR);
	INPUT_RECHERCHE_TAG_BLUR = setTimeout('element("zone_recherche").className = "vide"',300);*/
}
function input_recherche_tag_focus(input){
	input_focus(input);
	/*if(INPUT_RECHERCHE_BLUR!=false){
		clearTimeout(INPUT_RECHERCHE_BLUR);
		INPUT_RECHERCHE_BLUR=false;
	}
	element("zone_recherche").className = "";*/
}
function rechercher_tag(input){
	var texte = input.value;
	//if(texte.length>=2&&texte!=input.title){
		/*if(XHR_RECHERCHE_MESSAGE!=false){
			clearTimeout(XHR_RECHERCHE_MESSAGE);
			XHR_RECHERCHE_MESSAGE = false;
		}*/
		//On recherche le texte.
		if(XHR_RECHERCHE_TAG!=false){
			XHR_RECHERCHE_TAG.abort();
		}
		XHR_RECHERCHE_TAG = getXhr();
		XHR_RECHERCHE_TAG.onreadystatechange = function(){
			if(XHR_RECHERCHE_TAG.readyState == 4){
				if(XHR_RECHERCHE_TAG.status == 200){
					var reponse = eval("("+XHR_RECHERCHE_TAG.responseText+")");
					XHR_RECHERCHE_TAG = false;
					creer_resultat_recherche_tag(reponse);
				}
			}
		};
		XHR_RECHERCHE_TAG.open("POST", "03_ajax/recherche_tag.php", true);
		XHR_RECHERCHE_TAG.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		XHR_RECHERCHE_TAG.send("m="+encodeURIComponent(texte)+"&exception="+get_tags_courants()); //"&vie="+((element("boite_tag").className=="vie-toute")?"":element("select_vie").value.split("_")[1])+
	/*}
	else{
		var zone = element("zone_recherche");
		vide(zone);
	}*/
}
function creer_resultat_recherche_tag(tags){
	console.log(tags);
	//1. On vide la zone
	
	//2. On remplie avec les nouveaux tags
	var zone = element("liste_tag");
	vide(zone);
	for(var t=0;t<tags.length;t++){
		//On récupère les vies correspondantes au tag courant
			//$les_vies_tag = get_vies($les_tags[$t]["no"]);
			//$classe_vie = "";
			//for($v=0;$v<count($les_vies_tag);$v++){
				//$classe_vie .= " ".url_rewrite($les_vies_tag[$v]["libelle"]);
			//}
		//On créait la liste des tags dispos
		//$liste_tous_tags .= ((!empty($liste_tous_tags))?",":"").$tous_tags[$t]["no"];
		//on créait le html de cette liste
		var div_tag = document.createElement("div");
			div_tag.className = "un_tag "+tags[t]["class"];
			div_tag.id = "tag_"+tags[t]["no"];
			div_tag.onclick = function(){tag_click(this);};
			div_tag.appendChild(document.createTextNode(tags[t]["titre"]));
			console.log(tags[t]);
		zone.appendChild(div_tag);
	}
}
function tag_click(tag){
	//1. On place le tag dans la liste cible
	if(tag.parentNode.id=="liste_tag") //1.1 On a sélectionné un tag dispo
		var zone = "liste_tag_select";
	else //1.2 On a retiré un tag selectionné
		var zone = "liste_tag";
	//2. On calcule sa position dans la liste (ordre alphabétique)
	var les_tags = element(zone).getElementsByTagName("div");
	var l = 0;
	while(l<les_tags.length&&les_tags[l].firstChild.data<=tag.firstChild.data){
		l++;
	}
	if(l==les_tags.length)
		element(zone).appendChild(tag);
	else
		element(zone).insertBefore(tag,les_tags[l]);
	/*
	//2. On récupère la liste des tags (sélectionnés)
	var les_tags = element("tags_select").getElementsByTagName("input");
	var liste_tags = "";
	for(var t=0;t<les_tags.length;t++){
		liste_tags += ((liste_tags!="")?",":"")+les_tags[t].id.split("_")[1];
	}
	//3. On met à jour la liste des tags dans laquelle on a ajouté le tag (pour l'ordre alphabétique) 
	*/
}
function get_tags_courants(){
	var les_tags = element("liste_tag_select").getElementsByTagName("div");
	var liste_tags = "";
	for(var t=0;t<les_tags.length;t++){
		liste_tags += ((liste_tags!="")?",":"")+les_tags[t].id.split("_")[1];
	}
	return liste_tags;
}




var XHR_RECHERCHE_ITEM = false;
var INPUT_RECHERCHE_BLUR = false;
function input_recherche_blur(input){
	input_blur(input);
	/*if(INPUT_RECHERCHE_BLUR!=false)
		clearTimeout(INPUT_RECHERCHE_BLUR);
	INPUT_RECHERCHE_BLUR = setTimeout('element("zone_recherche").className = "vide"',300);*/
}
function input_recherche_focus(input){
	input_focus(input);
	/*if(INPUT_RECHERCHE_BLUR!=false){
		clearTimeout(INPUT_RECHERCHE_BLUR);
		INPUT_RECHERCHE_BLUR=false;
	}
	element("zone_recherche").className = "";*/
}

function rechercher(form){
	if(typeof(history.pushState)!="undefined"){ //Système compatible
		PAGE_COURANTE = "recherche";
		console.log(form.action);
		history.pushState({ path: this.path }, '', form.action+"?q="+element("q").value);
		charger_page();
		return false;
	}
	else{ //Lien normal
		return true;
	}
}
function rechercher_temps_reel(input,type){
	var texte = input.value;
	if(texte.length>=2&&texte!=input.title){
		/*if(XHR_RECHERCHE_MESSAGE!=false){
			clearTimeout(XHR_RECHERCHE_MESSAGE);
			XHR_RECHERCHE_MESSAGE = false;
		}*/
		//On recherche le texte.
		if(XHR_RECHERCHE_ITEM!=false){
			XHR_RECHERCHE_ITEM.abort();
		}
		XHR_RECHERCHE_ITEM = getXhr();
		XHR_RECHERCHE_ITEM.onreadystatechange = function(){
			if(XHR_RECHERCHE_ITEM.readyState == 4){
				if(XHR_RECHERCHE_ITEM.status == 200){
					var reponse = eval("("+XHR_RECHERCHE_ITEM.responseText+")");
					XHR_RECHERCHE_ITEM = false;
					creer_resultat_recherche_temps_reel(reponse);
				}
			}
		};
		XHR_RECHERCHE_ITEM.open("POST", "03_ajax/recherche_temps_reel.php", true);
		XHR_RECHERCHE_ITEM.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		XHR_RECHERCHE_ITEM.send("q="+encodeURIComponent(texte)+"&p="+type);
	}
	else{
		var zone = element("zone_recherche");
		vide(zone);
	}
}
function creer_resultat_recherche_temps_reel(resultat){
	resultat = resultat["liste"];
	var zone = element("zone_recherche");
	vide(zone);
	for(var i=0;i<resultat.length;i++){
		var a = document.createElement("a");
			a.className = resultat[i]["type"];
			a.innerHTML = resultat[i]["titre"];
			if(resultat[i]["type"]=="ville")
				var param = "no_ville="+resultat[i]["no"];
			else if(resultat[i]["type"]=="code_postal")
				var param = "cp="+resultat[i]["libelle"];
			else if(resultat[i]["type"]=="utilisateur")
				var param = "user="+resultat[i]["no"];
			else if(resultat[i]["type"]=="evenement")
				var param = "no="+resultat[i]["no"];
			else
				var param = "";
			//a.href = "?"+param;
			//a.onclick = function(e){choix_page(this,e);};
		zone.appendChild(a);
	}
}
function accepter_cookies(){
	//Évidemment en acceptant un cookie l'utilisateur en bouf un... Pourtant on n'en utilisait qu'un seul!
	var xhr = getXhr();
		xhr.open("POST", "03_ajax/accepter_cookie.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send(null);
	element("information_cookies").parentNode.removeChild(element("information_cookies"));
}
/*
function rechercher(input){
	var texte = input.value;
	if(texte.length>=2&&texte!=input.title){
		//On recherche le texte.
		if(XHR_RECHERCHE_ITEM!=false){
			XHR_RECHERCHE_ITEM.abort();
		}
		XHR_RECHERCHE_ITEM = getXhr();
		XHR_RECHERCHE_ITEM.onreadystatechange = function(){
			if(XHR_RECHERCHE_ITEM.readyState == 4){
				if(XHR_RECHERCHE_ITEM.status == 200){
					var reponse = eval("("+XHR_RECHERCHE_ITEM.responseText+")");
					XHR_RECHERCHE_ITEM = false;
					creer_resultat_recherche(reponse);
				}
			}
		};
		XHR_RECHERCHE_ITEM.open("POST", "ajax/recherche.php", true);
		XHR_RECHERCHE_ITEM.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		XHR_RECHERCHE_ITEM.send("m="+encodeURIComponent(texte)+"&type="+OPTIONS["page"]);
	}
	else{
		var zone = element("zone_recherche");
		vide(zone);
	}
}
function creer_resultat_recherche(resultat){
	var zone = element("zone_recherche");
	vide(zone);
	for(var i=0;i<resultat.length;i++){
		var a = document.createElement("a");
			a.className = resultat[i]["type"];
			a.appendChild(document.createTextNode(resultat[i]["libelle"]));
			if(resultat[i]["type"]=="ville")
				var param = "no_ville="+resultat[i]["no"];
			else if(resultat[i]["type"]=="code_postal")
				var param = "cp="+resultat[i]["libelle"];
			else if(resultat[i]["type"]=="utilisateur")
				var param = "user="+resultat[i]["no"];
			else if(resultat[i]["type"]=="evenement")
				var param = "no="+resultat[i]["no"];
			else
				var param = "";
			a.href = "?"+param;
			a.onclick = function(e){choix_page(this,e);};
		zone.appendChild(a);
	}
}*/

function affiche_div_commentaire(span){
	var zone = span.parentNode;
	var insertBefore = false;
	var no_message = span.parentNode.id.split("_")[1];
	if(zone.getElementsByClassName("un_commentaire").length>0){
		insertBefore = zone.getElementsByClassName("un_commentaire")[0];
	}
	var textarea = document.createElement("textarea");
		textarea.id = "reponseCommentaire_"+no_message;
		textarea.style.width = 100+"%";
	var btn_annuler = document.createElement("input");
		btn_annuler.type = "button";
		btn_annuler.value = "Annuler";
		btn_annuler.className = "ico couleur fleche_gauche";
		btn_annuler.style.cssFloat = "left";
		ajoute_evenement(btn_annuler,"click",'fermer_repondre('+no_message+')');
	var btn_valider = document.createElement("input");
		btn_valider.type = "button";
		btn_valider.value = "Commenter";
		btn_valider.className = "ico couleur editer";
		btn_valider.style.cssFloat = "right";
		ajoute_evenement(btn_valider,"click",'repondre('+no_message+',true)');
	var div_commentaire = document.createElement("div");
	div_commentaire.appendChild(textarea);
	div_commentaire.appendChild(btn_annuler);
	div_commentaire.appendChild(btn_valider);
	div_commentaire.style.paddingBottom = 30+"px";
	if(insertBefore!=false)
		span.parentNode.insertBefore(div_commentaire,insertBefore);
	else
		span.parentNode.appendChild(div_commentaire);
	span.style.display = "none";
	textareaToCK('reponseCommentaire_'+no_message);
}

function fermer_repondre(no){
	if(typeof(no)=="undefined"){
		CKEDITOR.instances.reponse_forum.setData("");
		CKEDITOR.instances.reponse_forum.destroy();
		//input_blur(document.getElementById("reponse_forum"));
		element("input_repondre").style.display = "inline-block";
		element("reponse_forum").style.display = "none";
		document.getElementById("btn_reponse").style.height = 1+"px";
		set_opacity(document.getElementById("btn_reponse"), 0);
		document.getElementById("btn_annuler").style.height = 1+"px";
		set_opacity(document.getElementById("btn_annuler"), 0);
		element("zone_reponse").style.paddingBottom = 10+"px";
		element("btn_activer_notifications").style.height = 1+"px";
		set_opacity(document.getElementById("btn_activer_notifications"), 0);
		REPONSE_EN_COURS=false;
	}
	else{
		CKEDITOR.instances["reponseCommentaire_"+no].destroy();
		element("reponseCommentaire_"+no).parentNode.parentNode.getElementsByTagName("span")[0].style.display = "inline";
		element("reponseCommentaire_"+no).parentNode.parentNode.removeChild(element("reponseCommentaire_"+no).parentNode);
	}
}

function textareaToCK(id){
	var reg_height = /height\[([0-9]+(?:px|%))\]/gi;
		var height = reg_height.exec(element(id).className);
	var param_ck = {language:'fr',uiColor: '#EDEDED'};
	if(height!=null&&height.length==2)
		param_ck["height"] = height[1];console.log(param_ck);
	CKEDITOR.replace(id,param_ck);
}

function afficher_image(img){
	var contenu = document.createElement("div");
	var img_taille_reelle = document.createElement("img");
		img_taille_reelle.src = img.src;
		img_taille_reelle.style.maxWidth = 100+"%";
	contenu.appendChild(img_taille_reelle);
	colorbox(contenu);
}

function espace_personnel_etape_suivante(form,params){
	if(typeof(history.pushState)!="undefined"){ //Système compatible
		//PAGE_COURANTE = "recherche";
		console.log(form.action);
		if(typeof(params)=="undefined")
			params = "";
		enregistrer(params);
		history.pushState({ path: this.path }, '', form.action);
		charger_page();
		return false;
	}
	else{ //Lien normal
		return true;
	}
}

function enregistrer(params){
	filtre();
	//On récupère les inputs et les selects
	var bloc = element("contenu_page");
	var inputs = bloc.getElementsByTagName("input");
	var selects = bloc.getElementsByTagName("select");
	var textareas = bloc.getElementsByTagName("textarea");
	if(typeof(params)=="undefined")
		var params = "";
	var pseudo_modifie = false;
	
	for(var i=0;i<inputs.length;i++){
		if(inputs[i].id.substring(0,3)=="BDD"){
			if(inputs[i].type!="checkbox"){
				params += ((params!="")?"&":"")+inputs[i].name.replace("BDD","")+"="+((inputs[i].value!=inputs[i].title)?encodeURIComponent(inputs[i].value):'');
				if(inputs[i].name.replace("BDD","")=="pseudo"&&inputs[i].value!=""&&inputs[i].value!=inputs[i].title)
					pseudo_modifie = inputs[i].value;
			}
			else
				params += ((params!="")?"&":"")+inputs[i].name.replace("BDD","")+"="+((inputs[i].checked)?1:0);
		}
	}
	for(var i=0;i<selects.length;i++){
		if(selects[i].id.substring(0,3)=="BDD")
			params += ((params!="")?"&":"")+selects[i].name.replace("BDD","")+"="+((selects[i].value!=selects[i].title)?encodeURIComponent(selects[i].value):'');
	}
	for(var i=0;i<textareas.length;i++){
		if(!dans_tab("editeur",textareas[i].className.split(" "))&&textareas[i].id.substring(0,3)=="BDD")
			params += ((params!="")?"&":"")+textareas[i].name.replace("BDD","")+"="+((textareas[i].value!=textareas[i].title)?encodeURIComponent(textareas[i].value):'');
	}
	//On récupère les textes longs
	for(var instanceName in CKEDITOR.instances){
	   params += "&"+CKEDITOR.instances[instanceName].name.replace("BDD","")+"="+encodeURIComponent(CKEDITOR.instances[instanceName].getData());
	}
	//On récupère les tags
	if(element("tags_select")!=null){
		var les_tags_select = element("tags_select").getElementsByTagName("input");
		var liste_tags = "";
		for(var i=0;i<les_tags_select.length;i++){
			liste_tags += ((liste_tags!="")?",":"")+les_tags_select[i].id.split("_")[1];
		}
		params += "&tags="+liste_tags;
	}
	
	xhr = getXhr();
	xhr.open("POST", "ajax/creationModification.php", false);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xhr.send(params);
	var reponse = eval("("+xhr.responseText+")");
	if(analyser_ajax(reponse)){
		message(reponse[1]);
		if(pseudo_modifie!=false){
			vide(element("span_pseudo"));
			element("span_pseudo").appendChild(document.createTextNode(pseudo_modifie));
		}
	}
	return reponse;
}
