var TIMEOUT_RESIZE = false;
var SESSION_DECONNECTEE = false;
function connexion(){
	filtre();
	var xhr = getXhr();
		xhr.open("POST", "ajax/connexion.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("email="+encodeURIComponent(element("input_email").value)+"&mdp="+encodeURIComponent(element("input_mdp").value));
	var connexion = eval("("+xhr.responseText+")");
	if(connexion[0]){
		var span_pseudo = document.createElement("span");
			span_pseudo.id = "span_pseudo";
			span_pseudo.appendChild(document.createTextNode(connexion[1]["pseudo"]));
		//element("bouton_menu_personnel").firstChild.data = connexion[1]["pseudo"]+" : "+connexion[1]["fonction"];
		vide(element("bouton_menu_personnel"));
		element("bouton_menu_personnel").appendChild(span_pseudo);
		element("bouton_menu_personnel").appendChild(document.createTextNode(" : "+connexion[1]["fonction"]));
		var zone_menu = element("principal");
		vide(zone_menu);
		for(var i=0;i<connexion[1]["menu"].length;i++){
			var a = document.createElement("a");
				a.href = "?page="+connexion[1]["menu"][i]["url_rewrite"];
				a.id = "page_"+connexion[1]["menu"][i]["url_rewrite"];
				a.className = "item_menu "+connexion[1]["menu"][i]["url_rewrite"];
				a.innerHTML = connexion[1]["menu"][i]["libelle"];
			zone_menu.appendChild(a);
		}
		element("section_body").className = "connecte";
		message(connexion[1]["message"]);
		//choix_page(element("page_<?php echo $PAGE; ?>"));
		if(!SESSION_DECONNECTEE)
			charger_page(get_params());
		filtre(false);
	}
	else{
		message(connexion[1]);
		filtre(false);
	}
	return false;
}/*
function filtre(afficher){
	if(afficher)
		element("filtre_chargement").className = "actif";
	else
		element("filtre_chargement").className = "";
}*/
function deconnexion(){
	var xhr = getXhr();
		xhr.open("POST", "ajax/deconnexion.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send(null);
	element("section_body").className = "";
}
function ouvrir_menu(menu){
	element(menu).style.maxHeight = ecran()["y"]-2+"px";
	element(menu).className="ouvert";
	setTimeout('raz_classNameMenu("'+menu+'")',200);
}
function raz_classNameMenu(menu){
	element(menu).className="";
}
function fermer_menu(menu){
	element(menu).className="ferme";
	setTimeout('raz_classNameMenu("'+menu+'")',200);
}

function onResize(){
	element("principal").style.maxHeight = ecran()["y"]-2+"px";
	element("personnel").style.maxHeight = ecran()["y"]-2+"px";
}
function mode_suppression(actif){
	if(actif){
		element("filtre_suppression").className = "actif";
		element("contenu_contenu").className = "mode_suppression";
	}
	else{
		element("filtre_suppression").className = "";
		element("contenu_contenu").className = "";
	}
}
function selectionner(ligne){
	if(!CLICK_SUR_BOUTON){
		if(dans_tab("selectionne",ligne.className.split(" ")))
			ligne.className = ligne.className.replace(" selectionne","");
		else
			ligne.className = ligne.className+" selectionne";
	}
	else{
		CLICK_SUR_BOUTON = false;
	}
}
var CLICK_SUR_BOUTON = false;
function clickSurBouton(){
	CLICK_SUR_BOUTON = true;
}

/****
DEUXIEME PARTIE
**/
                
	function onload(){
		//2. On prépare les infobulles, etc.
		parcours_recursif();
		rotation_chargement();
		var params = url_to_params();
		set_params(params);
	}
		var ROTATION_CHARGEMENT = new Array();
		function filtre(ouvrir,persistant){
			//ouvrir : (bool) Dit si on veut ouvrir (true ou undefined), ou fermer (false)
			//persistant : (bool) Signifie qu'il ne se ferme pas au click, et qu'on ne peut fermer les éventuelles fenetre ouvertes
			//aucun_chargement : (bool) Signifie que ce n'est pas un filtre de chargement
			if(typeof(ouvrir)=="undefined")
				ouvrir = true;
			if(typeof(persistant)=="undefined")
				persistant = false;
			if(ouvrir){ //On ouvre le filtre
				rotation_chargement("filtre_chargement");
				element("filtre_chargement").className = "actif";
				if(persistant)
					element("filtre_chargement").className += " persistant";
			}
			else{ //On ferme le filtre
				if(!dans_tab("persistant",element("filtre_chargement").className.split(" "))||persistant)
					element("filtre_chargement").className = "";
				if(ROTATION_CHARGEMENT["filtre_chargement"]!=false){
					clearTimeout(ROTATION_CHARGEMENT["filtre_chargement"]);
					ROTATION_CHARGEMENT["filtre_chargement"] = false;
				}
			}
		}
		function rotation_chargement(id){
			if(typeof(id)=="undefined")
				var id = "filtre_chargement";
			if(element(id).firstChild.className!="rotate")
				element(id).firstChild.className = "rotate";
			else
				element(id).firstChild.className = "";
			ROTATION_CHARGEMENT[id] = setTimeout("rotation_chargement('"+id+"')",600);
		}
		
		function filtre_colorbox(ouvrir,persistant){
			//ouvrir : (bool) Dit si on veut ouvrir (true ou undefined), ou fermer (false)
			//persistant : (bool) Signifie qu'il ne se ferme pas au click, et qu'on ne peut fermer les éventuelles fenetre ouvertes
			//aucun_chargement : (bool) Signifie que ce n'est pas un filtre de chargement
			if(typeof(ouvrir)=="undefined")
				ouvrir = true;
			if(typeof(persistant)=="undefined")
				persistant = false;
			if(ouvrir){ //On ouvre le filtre
				element("filtre_colorbox").className = "actif";
				if(persistant)
					element("filtre_colorbox").className += " persistant";
			}
			else{ //On ferme le filtre
				if(!dans_tab("persistant",element("filtre_colorbox").className.split(" "))||persistant)
					element("filtre_colorbox").className = "";
			}
		}
		
		/*
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
		}*/
		
		var TIMEOUT_AFFICHER_PAGE = false;
		/*function choix_page(el,e){
			filtre();
			if(typeof(history.pushState)!="undefined"){ //Système compatible ?
				if(typeof(e)!="undefined")
					e.preventDefault();
				PAGE_COURANTE = el.id.split("_")[1];
				history.pushState({ path: this.path }, '', el.href);
				charger_page();
				fermer_menu(el.parentNode.id);
				return false;
			}
			else{ //Lien normal
				return true;
			}
		}*/
		A_CLICK = false;
		window.onpopstate = function(){charger_url_courante();};
		/*function test_back_next(){
			//console.log(A_CLICK);
			//if(!A_CLICK){
				params = document.URL.split("?")[1];
				console.log(params);
				
			//}
			//else
			//	A_CLICK = !A_CLICK;
		}*/
			var PAGE_COURANTE = "";
			var VILLE = "";
			var CODE_POSTAL = "";
			var UTILISATEUR = "";
			var NO_ITEM = "";
			var OPTIONS = new Array();
				OPTIONS["page"] = "";
				OPTIONS["ville"] = "";
				OPTIONS["cp"] = "";
				OPTIONS["utilisateur"] = "";
				OPTIONS["no"] = "";
				OPTIONS["tri"] = "";
				OPTIONS["ordre"] = "";
			function get_params(){
				var params = "";
				if(OPTIONS["page"]!="")
					params += "page="+OPTIONS["page"];
				if(OPTIONS["ville"]!="")
					params += ((params!="")?"&":"")+"no_ville="+OPTIONS["ville"];
				if(OPTIONS["cp"]!="")
					params += ((params!="")?"&":"")+"cp="+OPTIONS["cp"];
				if(OPTIONS["utilisateur"]!="")
					params += ((params!="")?"&":"")+"user="+OPTIONS["utilisateur"];
				if(OPTIONS["no"]!="")
					params += ((params!="")?"&":"")+"no="+OPTIONS["no"];
				if(OPTIONS["tri"]!="")
					params += ((params!="")?"&":"")+"tri="+OPTIONS["tri"];
				if(OPTIONS["ordre"]!="")
					params += ((params!="")?"&":"")+"ordre="+OPTIONS["ordre"];
				return (params!="")?params:"page=accueil";
			}
			function url_to_params(url){
				if(typeof(url)=="undefined")
					url = document.URL;
				params = url.split("#")[0];
				params = params.split("?")[1];
				if(params==""||params==null||typeof(params)=="undefined") //Sécurité parce qu'ils veulent pas se mettre d'accord ces connards entre firefox et ie et chrome etc. soit la chaine est vide, soit elle n'existe pas bref ils me font péter un plomb ces cons
					params = "page=accueil";
				return params;
			}
			function charger_url_courante(){
				var params = url_to_params();
				var params_actuels = get_params();
				if(params!=params_actuels){
					filtre();
					set_params(params);
					charger_page(params);
				}
			}
				function set_params(params){
					params = params.split("&");
					for(var i=0;i<params.length;i++){
						var _p = params[i].split("=");
						console.log("test : "+_p[0]+" : "+_p[1]);
						if(_p[0]=="page"){
							if(typeof(_p[1])!="undefined")
								OPTIONS["page"] = _p[1];
						}
						else if(_p[0]=="cp"){
							if(typeof(_p[1])!="undefined"){
								OPTIONS["cp"] = _p[1];
								if(OPTIONS["cp"]!=""){
									OPTIONS["ville"]="";
									OPTIONS["utilisateur"]="";
								}
							}
						}
						else if(_p[0]=="no_ville"){
							if(typeof(_p[1])!="undefined"){
								OPTIONS["ville"] = _p[1];
								if(OPTIONS["ville"]!=""){
									OPTIONS["cp"]="";
									OPTIONS["utilisateur"]="";
								}
							}
						}
						else if(_p[0]=="user"){
							if(typeof(_p[1])!="undefined"){
								OPTIONS["utilisateur"] = _p[1];
								if(OPTIONS["utilisateur"]!=""){
									OPTIONS["cp"]="";
									OPTIONS["ville"]="";
								}
							}
						}
						else if(_p[0]=="no"){
							if(typeof(_p[1])!="undefined")
								OPTIONS["no"] = _p[1];
						}
						else if(_p[0]=="tri"){
							if(typeof(_p[1])!="undefined")
								OPTIONS["tri"] = _p[1];
						}
						else if(_p[0]=="ordre"){
							if(typeof(_p[1])!="undefined")
								OPTIONS["ordre"] = _p[1];
						}
					}
				}
		function choix_page(el,e){
			filtre();
			if(typeof(history.pushState)!="undefined"){ //Système compatible ?
				if(typeof(e)!="undefined")
					e.preventDefault();
				if(dans_tab("item_menu",el.className.split(" ")))
					fermer_menu(el.parentNode.id);
				if(document.location!=el.href){
					set_params(el.href.split("?")[1]);
					history.pushState({ path: this.path }, '', "?"+get_params());
					//charger_url_courante();
					charger_page();
				}
				else
					filtre(false);
				return false;
			}
			else{ //Lien normal
				return true;
			}
		}
			function charger_page(params){
				if(typeof(params)=="undefined")
					params=get_params();
				//var parametres = getParametresURL();
				var zone_affichage = element("contenu");
				var xhr = getXhr();
				xhr.open("POST", "ajax/charger_page.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send(params);
				var reponse = eval("("+xhr.responseText+")");
				if(analyser_ajax(reponse)){
					var page = reponse[1];
					/*
					for(var i=1;i<les_lignes.length;i++){ //On laisse la première ligne (colonne gauche, colonne droite)
						les_lignes[i].parentNode.removeChild(les_lignes[i]);
					}*/
					/*afficher_page(zone_affichage,eval("("+xhr.responseText+")"));*/
					//On supprime les instance de ck (inutil pour description qui est appellé sur toutes les pages)
					for(var instanceName in CKEDITOR.instances){
						CKEDITOR.instances[instanceName].destroy();
					}
					//On supprime les ck particuliers de chaque page (s'ils existent)
					/*
					if(CKEDITOR.instances["BDDchapo"])
						CKEDITOR.instances["BDDchapo"].destroy();
					if(CKEDITOR.instances["BDDdescription_complementaire"])
						CKEDITOR.instances["BDDdescription_complementaire"].destroy();
					if(CKEDITOR.instances["BDDnotes"])
						CKEDITOR.instances["BDDnotes"].destroy();
					if(CKEDITOR.instances["BDDcontenu"])
						CKEDITOR.instances["BDDcontenu"].destroy();*/
					vide(zone_affichage.lastChild);
				
				
					document.title = "Administration : "+page["titre"];
					element("bouton_menu_principal").firstChild.data = page["titre"];
					//if(!dans_tab(OPTIONS["page"],zone_affichage.className.split(" "))){ //Sinon inutil de recharger le menu
					if(true){
						vide(zone_affichage.firstChild);
						if(page["menu"]!=false){
							zone_affichage.className = OPTIONS["page"]+" avec_menu";
							element("contenu_menu").innerHTML = page["menu"];
						}
						else
							zone_affichage.className = OPTIONS["page"]+" sans_menu";
					}
					else{
						var contenu_filtre = (page["ville"]!=""&&page["ville"]!=null)?page["ville"]:((page["utilisateur"]!=""&&page["utilisateur"]!=null)?page["utilisateur"]:((page["cp"]!=""&&page["cp"]!=null)?page["cp"]:""));
						//((page["ville"]!=""&&page["ville"]!=null)?page["ville"]:((page["utilisateur"]!="")?page["utilisateur"]:((page["cp"]!=""&&page["cp"]!=null)?page["cp"]:"")));
						if(contenu_filtre!=""){
							if(element("filtre_cpVilleUtilisateur")==null){
								var zone_filtre = element("les_filtres");
								var div_filtre = document.createElement("div");
									div_filtre.className = "filtre_recherche actif";
									div_filtre.id = "filtre_cpVilleUtilisateur";
									var lib_filtre = document.createElement("div");
										lib_filtre.className = "libelle";
										var a_filtre = document.createElement("a");
											a_filtre.href = "?no_ville=&user=&cp=";
											var img_filtre = document.createElement("img");
												img_filtre.src = "../img/img_colorize.php?uri=non_actif.png&c=255,255,255";
											a_filtre.appendChild(img_filtre);
										lib_filtre.appendChild(a_filtre);
										lib_filtre.appendChild(document.createTextNode(contenu_filtre));
									div_filtre.appendChild(lib_filtre);
								zone_filtre.appendChild(div_filtre);
							}
							else{
								element("filtre_cpVilleUtilisateur").firstChild.lastChild.data = contenu_filtre;
							}
						}
						else{
							if(element("filtre_cpVilleUtilisateur")!=null)
								element("filtre_cpVilleUtilisateur").parentNode.removeChild(element("filtre_cpVilleUtilisateur"));
						}
					}
					element("contenu_contenu").innerHTML = page["contenu"];
					parcours_recursif(zone_affichage);
				}
				filtre(false);
			}
			
			function afficher_page(zone,lignes){
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
			
		function fenetre_ville(empecher_fermeture){
			if(typeof(empecher_fermeture)=="undefined")
				empecher_fermeture = false;
			filtre(true,empecher_fermeture);
			
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
			
			if(typeof(empecher_fermeture)=="undefined")
				empecher_fermeture = false;
			var params = {"max-width":516,"filtre":true,"width":"100%","id":"fenetre_pseudo"};
			colorbox(div,params,empecher_fermeture);
		}
		
		function fenetre_pseudo(empecher_fermeture){
			var div = document.createElement("div");
			
			var div_titre = document.createElement("div");
				div_titre.style.textAlign = "left";
				var h1 = document.createElement("h1");
					h1.appendChild(document.createTextNode("Informations personnelles"));
				div_titre.appendChild(h1);
			//var p = document.createElement("p");
			//	p.appendChild(document.createTextNode("Complétez votre profil petit à petit"));
			div.appendChild(div_titre);
			var header = document.createElement("img");
				header.src = "http://www.ensembleici.fr/img/bandeau-colorbox.png";
			div.appendChild(header);
			//div.appendChild(p);
			
			var formulaire = document.createElement("div");
				formulaire.className = "gris";
				formulaire.style.marginTop = 1+"em";
				var label_pseudo = document.createElement("label");
					label_pseudo.htmlFor = "input_pseudo";
					label_pseudo.appendChild(document.createTextNode("Nom d'utilisateur : "));
				var input_pseudo = document.createElement("input");
					input_pseudo.id = "input_pseudo";
					input_pseudo.type = "text";
					input_pseudo.value = getPseudo();
					input_pseudo.title = "nom d'utilisateur";
					input_pseudo.onfocus = function(){input_focus(this);};
					input_pseudo.onblur = function(){input_blur(this);};
					//input.onkeyup = function(){rechercher_pseudo(this);};
				formulaire.appendChild(label_pseudo);
				formulaire.appendChild(input_pseudo);
			div.appendChild(formulaire);
			var div_result = document.createElement("div");
				div_result.id = "recherche_pseudo";
				div_result.appendChild(document.createElement("div"));
			div.appendChild(div_result);
			
			if(typeof(empecher_fermeture)=="undefined")
				empecher_fermeture = false;
			var params = {"max-width":516,"width":"100%","id":"fenetre_pseudo","filtre":true,"btn":new Array({"value":"Annuler","click":"fermer"},{"value":"Enregistrer","click":"setPseudo(element('input_pseudo').value)"})};
			colorbox(div,params,empecher_fermeture);
			input_blur(input_pseudo);
		}
		
		function colorbox(div,params,empecher_fermeture){
			filtre_colorbox(true,empecher_fermeture);
			return message(div,params);
		}
		
		function getPseudo(){
			xhr = getXhr();
			xhr.open("POST", "ajax/getPseudo.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send(null);
			return xhr.responseText;
		}
		function setPseudo(p){
			xhr = getXhr();
			xhr.open("POST", "ajax/setPseudo.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("p="+encodeURIComponent(p));
			var reponse = eval("("+xhr.responseText+")");
			message(reponse[1]);
			if(reponse[0]){
				supprime_message("fenetre_pseudo",true);
				//On met à jour span_pseudo et l'éventuel span_pseudo2
				vide(element("span_pseudo"));
				element("span_pseudo").appendChild(document.createTextNode(p));
				if(element("span_pseudo2")!=null){
					vide(element("span_pseudo2"));
					element("span_pseudo2").appendChild(document.createTextNode(p));
				}
			}
		}
		
		function analyser_ajax(ajax){
			if(ajax[0]){
				return true;
			}
			else{
				if(ajax[1]=="[CONNEXION]"){
					SESSION_DECONNECTEE = true;
					message("Vous avez été déconnecté ...");
					element("section_body").className = "";
				}
				else if(ajax[1]=="[DROIT]"){
					message("Vous n'avez pas les autorisations nescessaires ...");
					//TODO redirection vers l'accueil
				}
				else
					message(ajax[1]);
				return false;
			}
		}
		
		var XHR_RECHERCHE_ITEM = false;
		var INPUT_RECHERCHE_BLUR = false;
		function input_recherche_blur(input){
			input_blur(input);
			if(INPUT_RECHERCHE_BLUR!=false)
				clearTimeout(INPUT_RECHERCHE_BLUR);
			INPUT_RECHERCHE_BLUR = setTimeout('element("zone_recherche").className = "vide"',300);
		}
		function input_recherche_focus(input){
			input_focus(input);
			if(INPUT_RECHERCHE_BLUR!=false){
				clearTimeout(INPUT_RECHERCHE_BLUR);
				INPUT_RECHERCHE_BLUR=false;
			}
			element("zone_recherche").className = "";
		}
		function rechercher(input){
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
			//menu_utilisateur_smartphone(true);
			//menu_site_smartphone(true);
			if(!dans_tab("persistant",element("filtre_colorbox").className.split(" "))){
				if(element("fenetre_ville")!=null)
					supprime_message("fenetre_ville",true);
				else if(element("fenetre_pseudo")!=null)
					supprime_message("fenetre_pseudo",true);
				//filtre_colorbox(false);
			}
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
					//while(el!=depart&&el.nextSibling==null){
						el = el.parentNode;
					}
					if(el!=depart) //on a trouvÃ© un nextSibling dans un parent
						parcours_recursif(depart,el.nextSibling);
					else //On a tout remontÃ© sans trouver de nouvel Ã©lÃ©ment.
						null;
				}
			}
		}
		
		var FORMULAIRE_FILE_DELETE_CLICK = false;
		var XHR_FORMULAIRE = false;
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
						el.onkeyup = function(){rechercher(this);};
						el.onblur = function(){input_recherche_blur(this)};
						el.onfocus = function(){input_recherche_focus(this);};
					}
					else if(dans_tab("prepare_element",el.className.split(" "))){
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
					input_blur(el);
				}
				else if(el.type=="button"){
					if(dans_tab("recherche",el.className.split(" ")))
						el.onclick = function(){rechercher(this.previousSibling);};
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
				console.log(el.href);
				el.onclick = function(e){choix_page(this,e);};
			}
		}
		
		function formulaire_file(input,format,temps_reel){
			var id = input.id.replace("fichier_courant_","");
			//element("visuel_"+id).firstChild.lastChild.style.display = "block";
			//set_opacity(element("visuel_"+id).firstChild.lastChild,100);
			if(!dans_tab("actif",element("filtre_"+id).className.split(" "))){
				element("filtre_"+id).className += " actif";
				rotation_chargement("filtre_"+id);
			}
			envoyer_formulaire(input.parentNode,"_form_upload_file.php",id,format);
			//var reponse = eval("("+envoyer_formulaire(input.parentNode,"_form_upload_file.php")+")");
		}
		
		function fin_upload_file(reponse,id,format){
			if(reponse["code_err"]==0){
				//On met Ã  jour la petite croix (grise foncÃ© avec mouseover/mouseout), si l'ancienne image Ã©tait vide
					if(element("visuel_"+id).lastChild.className=="fermer"&&element("visuel_"+id).firstChild.firstChild.src.indexOf("aucune_image")>-1){
						element("visuel_"+id).firstChild.firstChild.nextSibling.firstChild.data = element("visuel_"+id).firstChild.firstChild.nextSibling.firstChild.data.replace("Ajouter une ","Modifier l'").replace("Ajouter un ","Modifier le ");
						element("visuel_"+id).lastChild.src = "../img/img_colorize.php?uri=ico_delete.png&c=4E4E4E";
						element("visuel_"+id).lastChild.onmouseout = function(){this.src="../img/img_colorize.php?uri=ico_delete.png&c=4E4E4E";};
						if(format=="image")
							ajoute_evenement(element("visuel_"+id).lastChild,"mouseover",'this.src="../img/img_colorize.php?uri=ico_delete.png&c=FE0000";infobulle(this,"Supprimer l\'image","bas gauche")');
						else if(format=="pdf")
							ajoute_evenement(element("visuel_"+id).lastChild,"mouseover",'this.src="../img/img_colorize.php?uri=ico_delete.png&c=FE0000";infobulle(this,"Supprimer l\'image","bas gauche")');
						else
							ajoute_evenement(element("visuel_"+id).lastChild,"mouseover",'this.src="../img/img_colorize.php?uri=ico_delete.png&c=FE0000";infobulle(this,"Supprimer le fichier pdf","bas gauche")');
					}
				element("visuel_"+id).firstChild.firstChild.src = "http://www.ensembleici.fr/"+reponse["info"];
				element(id).value = reponse["info"];
			}
			else
				message(reponse["info"],4);
		}
		
		function envoyer_formulaire(form,page,id,format){
			if(XHR_FORMULAIRE!=false){
				XHR_FORMULAIRE.abort();
				XHR_FORMULAIRE = false;
			}
			if(typeof(form)=="string")
				form = element(form); //On a passÃ© l'id du formulaire au lieu du formulaire lui mÃªme
			var fd = new FormData(form);
			var inputs = form.getElementsByTagName("input");
			for(var i=0;i<inputs.length;i++){
				if(typeof(inputs[i].id)!="undefined"&&inputs[i].id!=""){
					if(inputs[i].type=="file")
						var valeur = (typeof(inputs[i].files[0])!="undefined")?inputs[i].files[0]:false;
					else
						var valeur = inputs[i].value;
					if(valeur!==false)
						fd.append(inputs[i].id,valeur);
					/***********************************************************
					  VERIFICATION DES DONNÃ‰ES EN FONCTION DE L'ATTRIBUT FORMAT
					  TODO
					  ***********************************************************/
				}
			}
			XHR_FORMULAIRE = getXhr();
			XHR_FORMULAIRE.onreadystatechange = function(){
				if(XHR_FORMULAIRE.readyState == 4){
					if(XHR_FORMULAIRE.status == 200){
						var reponse = eval("("+XHR_FORMULAIRE.responseText+")");
						XHR_FORMULAIRE = false;
						fin_upload_file(reponse,id,format);
					}
				}
			};
			XHR_FORMULAIRE.open("POST", "ajax/"+page, true);
			XHR_FORMULAIRE.send(fd);
			//return XHR_FORMULAIRE.responseText;
		}
		
		function formulaire_image_load(img){
			if(img.width>img.height){
				img.className = img.className.replace(" portrait","");
				img.className = img.className.replace(" carre","");
				img.className += " paysage";
			}
			else if(img.height>img.width){
				img.className = img.className.replace(" paysage","");
				img.className = img.className.replace(" carre","");
				img.className += " portrait";
			}
			else{
				img.className = img.className.replace(" portrait","");
				img.className = img.className.replace(" paysage","");
				img.className += " carre";
			}
	
			img.style.left = ((largeur(img.parentNode)-2)/2-largeur(img)/2)+"px"; //-2 Pour les bordures du parents...
			img.style.top = ((hauteur(img.parentNode)-2)/2-hauteur(img)/2)+"px";
	
			var id = img.parentNode.parentNode.id.replace("visuel_","");;
			//set_opacity(element(id).firstChild.lastChild,0);
			element("filtre_"+id).className = element("filtre_"+id).className.replace(" actif","");
			if(ROTATION_CHARGEMENT["filtre_"+id]!=false){
				clearTimeout(ROTATION_CHARGEMENT["filtre_"+id]);
				ROTATION_CHARGEMENT["filtre_"+id] = false;
			}
		}
		
		function enregistrer(params){
			filtre();
			//On récupère les inputs et les selects
			var bloc = element("contenu_contenu");
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
		
		function enregistrer_brouillon(){
			var reponse = enregistrer();
			filtre(false);
		}
		
		function ajouter_modifier(){
			var reponse = enregistrer("etat=1");
			if(reponse[0]){
				set_params("no=");
				history.pushState({ path: this.path }, '', "?"+get_params());
				charger_page();
			}
			else
				filtre(false);
		}
		
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
	
	
	
	function input_heures_blur(input){
		var h = input.value;
		var est_Nan = false;
		if(h!=""){
			var uniform = /[\.,_:;\/ H-]/g;
			console.log("etape 1: "+h);
			h = h.replace(uniform,"h");
			console.log("etape 2: "+h);
			var secur = /[^0-9h]/g;
			h = h.replace(secur,"");
			console.log("etape 3: "+h);
			if(h.indexOf("h")<0){ //Seulement des chiffres.
				/*if(i_s==s.length-1)
					h = (0,h.length-1);*/
				if(isNaN(parseInt(h))){
					est_Nan = true;
				}
				else{
					if(h.length>=3){
						if(parseInt(h.substr(0,1))<=2){ //Soir/journée genre 213 -> 21h30 , 16h30, etc. , ou 08xx...
							_h = h.substr(0,2);
							_m = (parseInt(h.substr(2,h.length))<10)?(parseInt(h.substr(2,h.length))+"0"):(h.substr(2,h.length));
						}
						else{ //matin -> 745 -> 07h45 etc.
							_h = "0"+h.substr(0,1);
							_m = h.substr(1,(h.length>4)?2:h.length);
						}
					}
					else if(h.length==1){
						_h = "0"+h;
						_m = "00";
					}
					else{ //Chiffre rond : 21, 14 ou 93 -> 09h30
						if(parseInt(h.substr(0,1))<=2){
							_h = h.substr(0,h.length);
							_m = "00";
						}
						else{
							_h = "0"+h.substr(0,1);
							_m = h.substr(1,1)+"0";
						}
					}
				}
			}
			else{ //il y a un séparateur.
				_h = h.split("h");
				if(_h[1]=="")
					_h[1] = 0;
				var _m = parseInt(_h[1]);
				var _h = parseInt(_h[0]);
				if(isNaN(_m)||isNaN(_h)){
					est_Nan = true;
				}
				else{
					if(_m<10){
						_m = "0"+_m;
					}
					if(_h<10){
						_h = "0"+_h;
					}
				}
			}
			if(!est_Nan){
				if(parseInt(_m)>=60){
					_m = "59";
				}
				if(parseInt(_h)>=24){
					_h = "00";
				}
				input.value = _h+"h"+_m;
			}
			else{
				input.value = "";
				message("Veuillez saisir une heure valide");
				input.focus();
			}
		}
		input_blur(input);
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
			XHR_RECHERCHE_VILLE.open("POST", "../03_ajax/recherche_ville_cp.php", true);
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
		if(element("recherche_ville_liste").className=="vide")
			element("recherche_ville_liste").className="";
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
            if (element("ville") != null) {
		element("ville").value = libelle;
                vide(element("recherche_ville_liste").firstChild);
            }
            if (element("user_ville") != null) {
		element("user_ville").value = libelle;
            }
		element("BDDno_ville").value = no;
		//vide(element("recherche_ville_liste").firstChild);
		//input_recherche_ville_blur(element("ville"));
	}
	
	var INPUT_RECHERCHE_VILLE_BLUR = false;
	function input_recherche_ville_blur(input){
		//if(element("BDDno_ville").value!=0) vide(element("recherche_ville_liste").firstChild);
		input_blur(input);
		if(INPUT_RECHERCHE_VILLE_BLUR!=false)
			clearTimeout(INPUT_RECHERCHE_VILLE_BLUR);
		INPUT_RECHERCHE_VILLE_BLUR = setTimeout('if(element("BDDno_ville").value!=0)element("recherche_ville_liste").className="vide"',200);
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
	
	
	/******
	TAGS
	****/
function tag_click(tag){
	var label = tag.nextSibling;
	//1. On place le tag dans la liste cible
	if(tag.checked) //1.1 On a sélectionné un tag dispo
		var zone = "tags_select";
	else //1.2 On a retiré un tag selectionné
		var zone = "tags_dispo";
	//2. On calcule sa position dans la liste (ordre alphabétique)
	var les_labels = element(zone).getElementsByTagName("label");
	var l = 0;
	while(l<les_labels.length&&les_labels[l].firstChild.data<=label.firstChild.data){
		l++;
	}
	if(l==les_labels.length)
		element(zone).appendChild(tag.parentNode);
	else
		element(zone).insertBefore(tag.parentNode,les_labels[l].parentNode);
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

function set_vie(select){
	var vie = select.value.split("_")[0];
	element("boite_tag").className = vie;
}

function get_tags_courants(){
	var les_tags = element("tags_select").getElementsByTagName("input");
	var liste_tags = "";
	for(var t=0;t<les_tags.length;t++){
		liste_tags += ((liste_tags!="")?",":"")+les_tags[t].id.split("_")[1];
	}
	return liste_tags;
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
		XHR_RECHERCHE_TAG.open("POST", "ajax/recherche_tag.php", true);
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
	var zone = element("tags_dispo");
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
			div_tag.className = "un_tag "+tags[t]["class"];;
			var input = document.createElement("input");
				input.type = "checkbox";
				input.id = "tag_"+tags[t]["no"];
				input.onclick = function(){tag_click(this);};
			var label = document.createElement("label");
				label.htmlFor = "tag_"+tags[t]["no"];
				label.appendChild(document.createTextNode(tags[t]["titre"]));
			div_tag.appendChild(input);
			div_tag.appendChild(label);
			console.log(tags[t]);
		zone.appendChild(div_tag);
	}
}


function activer_desactiver(no,type,btn){
	if(!dans_tab("expire",btn.className.split(" "))){
		if(btn.className.indexOf("actif")){ //Affichage liste
			if(dans_tab("actif",btn.className.split(" "))){
				var etat = 0; //Il est actuellement actif, on cherche à le désactiver
				btn.className = btn.className.replace(" actif","");
				btn.parentNode.parentNode.className += " non_actif";
			}
			else{
				var etat = 1;
				btn.className = btn.className+" actif";
				btn.parentNode.parentNode.className = btn.parentNode.parentNode.className.replace(" non_actif","");
			}
		}
		else{ //Affichage fiche
			if(btn.parentNode.className.indexOf("actif")>-1){
				btn.parentNode.className = btn.parentNode.className.replace(" actif","");
				btn.firstChild.data = "Activer";
				btn.previousSibling.firstChild.data = "Non actif";
			}
			else{
				btn.parentNode.className += " actif";
				btn.firstChild.data = "Désactiver";
				btn.previousSibling.firstChild.data = "Actif";
			}
		}
		//On appelle maintenant le fichier ajax qui va modifier cet état.
		var xhr = getXhr();
			xhr.open("POST", "ajax/activerDesactiver.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("no="+no+"&etat="+etat+"&type="+type);
		var reponse = eval("("+xhr.responseText+")");
		message(reponse[1]);
	}
}

function click_moiMeme(input){
	if(input.checked)
		document.getElementById("moiMeme_ou_autreContact").className = "moi_meme";
	else
		document.getElementById("moiMeme_ou_autreContact").className = "autre_contact";
}
function click_monetaire(input){
	if(input.checked)
		input.parentNode.className = "monetaire";
	else
		input.parentNode.className = "";
}
/**
shortcut.add("Ctrl+S",function() {
	alert("Sauvegarde");
},{
'type':'keydown',
'propagate':true,
'disable_in_input':false
});**/
