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
	charge_image(ROOT_SITE+"img/bandeau-colorbox.png");
	init_page();
	//setTimeout("element('header').style.width=100+'%';alert('yo');",5000);
}
	function charge_image(url){
		var img = document.createElement("img");
			img.src = url;
	}

function connexion(fonction_sortie){
	fonction_sortie = decodeURIComponent(fonction_sortie);
	filtre();
	if(element("input_email")!=null){ //Il y a une colorbox
		var param = "email="+encodeURIComponent_vrai(element("input_email").value)+"&mdp="+encodeURIComponent_vrai(element("input_mdp").value);
	}
	else{ //On est sur l'espace personnel
		var param = "email="+encodeURIComponent_vrai(element("input_email_espacePerso").value)+"&mdp="+encodeURIComponent_vrai(element("input_mdp_espacePerso").value);
	}
	var xhr = getXhr();
		xhr.open("POST", "03_ajax/connexion.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send(param);
	var connexion = eval("("+xhr.responseText+")");
	if(connexion[0]){
		if(element("colorbox")!=null)
			supprime_message("colorbox",true);
		if(typeof(fonction_sortie)!="undefined")
			eval(fonction_sortie);
		else
			charger_page();
		filtre(false);
                $(location).attr('href', $(location).attr('href'));
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
        $(location).attr('href', $(location).attr('href'));
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
		
		
		if(el.href.indexOf("#")>-1&&element(el.href.split("#")[1])!=null){
			console.log(offsetAbs(element(el.href.split("#")[1]))["top"]);
			scroll_progressif(offsetAbs(element(el.href.split("#")[1]))["top"]);
		}
		return false;
	}
	else{ //Lien normal
		return true;
	}
}


var XHR_CHARGEMENT_PAGE = false;
	function charger_page(param_sup){
		if(typeof(param_sup)=="undefined")var param_sup="";
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
		
		if(get_link_cssBig()!=false&&(typeof(parametres["no"])!="undefined"&&parametres["no"]!=null&&parametres["no"]!=""))
			remonter_progressif();
		
		
		
		var params = "";
		for(var cle in parametres){
			params += ((params!="")?"&":"")+cle+"="+encodeURIComponent_vrai(parametres[cle]);
		}
		params += ((params!="")?"&":"")+param_sup;
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
					element("menu").className = parametres["p"];
					element("contenu").className = parametres["p"];
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
                
                var hauteur=$('#barressmenu').offset().top - 60;
                $('html,body').animate({scrollTop:hauteur},2000);
                
    var date_min = new Date();
    if ($( "#BDDdate_debut" ).hasClass('vide')) {
        $( "#BDDdate_debut" ).removeClass('vide');
    }
    if ($( "#BDDdate_fin" ).hasClass('vide')) {
        $( "#BDDdate_fin" ).removeClass('vide');
    }
    $( "#BDDdate_debut" ).datepicker({ minDate: date_min, dateFormat: "dd/mm/yy"});
    $( "#BDDdate_fin" ).datepicker({ minDate: date_min, dateFormat: "dd/mm/yy"});
		
		//console.log();
		_gaq.push(['_trackPageview', ("/"+document.URL.replace(ROOT_PROD,""))]);
		//google_analytics(document,'script');
	}
	
	function afficher_page(zone,lignes){
		
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
		filtre(false);
	}
	
	function init_page(){
		console.log("youhhouoououou");
		if(element("liste_audio")!=null&&element("liste_audio").firstChild!=null){
			//console.log(element("liste_audio").firstChild.onclick);
			//element("liste_audio").firstChild.click();
			lancer_fichier_audio('zone_fichiers',element("liste_audio").firstChild.lastChild.previousSibling.value,element("liste_audio").firstChild.lastChild.value,true); //'https://soundcloud.com/th-o-radio-m/la-fauchere-et-terre-de-liens'
		}
	}
	
function fenetre_ville(empecher_fermeture,fonction_sortie){
	if(typeof(empecher_fermeture)=="undefined")
		empecher_fermeture = false;
	filtre(true,empecher_fermeture);
	//On récupère le contenu pour ville
	var xhr = getXhr();
		xhr.open("POST", "01_include/struct_colorBox.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("colorbox=ville"+((empecher_fermeture)?'&ne_pas_fermer=1':'')+"&fonction_sortie="+encodeURIComponent_vrai(fonction_sortie));
	colorbox(xhr.responseText,empecher_fermeture);
}
function fenetre_connexion(empecher_fermeture,fonction_sortie){
	if(typeof(empecher_fermeture)=="undefined")
		empecher_fermeture = false;
	if(typeof(fonction_sortie)=="undefined")
		fonction_sortie = "";
	filtre(true,empecher_fermeture);
	//On récupère le contenu pour la connexion
	var xhr = getXhr();
		xhr.open("POST", "01_include/struct_colorBox.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("colorbox=connexion"+((empecher_fermeture)?'&ne_pas_fermer=1':'')+'&fonction_sortie='+encodeURIComponent_vrai(fonction_sortie));
	colorbox(xhr.responseText,empecher_fermeture);
}
function fenetre_nouveau_compte(empecher_fermeture,fonction_sortie){
	if(typeof(empecher_fermeture)=="undefined")
		empecher_fermeture = false;
	filtre(true,empecher_fermeture);
	//On récupère le contenu pour la connexion
	var xhr = getXhr();
		xhr.open("POST", "01_include/struct_colorBox.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("colorbox=nouveau_compte"+((empecher_fermeture)?'&ne_pas_fermer=1':'')+"&fonction_sortie="+encodeURIComponent_vrai(fonction_sortie));
	colorbox(xhr.responseText,empecher_fermeture);
}
function fenetre_newsletter(){
	filtre(true);
	//On récupère le contenu pour la connexion
	var xhr = getXhr();
		xhr.open("POST", "01_include/struct_colorBox.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("colorbox=newsletter");
	colorbox(xhr.responseText);
}
function fenetre_contact(no_contact){
	//On récupère le contenu pour la connexion
	var xhr = getXhr();
		xhr.open("POST", "01_include/struct_colorBox.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("colorbox=contact&sans_image_header=1&no_contact="+no_contact);
	colorboxLight(xhr.responseText);
}
function fenetre_contactbis(email){
	//On récupère le contenu pour la connexion
	var xhr = getXhr();
		xhr.open("POST", "01_include/struct_colorBox.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("colorbox=email&sans_image_header=1&email="+email);
	colorboxLight(xhr.responseText);
}
function fenetre_pseudo(empecher_fermeture,fonction_sortie){
	if(typeof(empecher_fermeture)=="undefined")
		empecher_fermeture = false;
	if(typeof(fonction_sortie)=="undefined")
		fonction_sortie = "";
	filtre(true,empecher_fermeture);
	//On récupère le contenu pour la connexion
	var xhr = getXhr();
		xhr.open("POST", "01_include/struct_colorBox.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("colorbox=pseudo"+((empecher_fermeture)?'&ne_pas_fermer=1':'')+'&fonction_sortie='+encodeURIComponent_vrai(fonction_sortie));
	colorbox(xhr.responseText,empecher_fermeture);
}
function fenetre_creationModificationContact(no_contact){
	//On récupère le contenu pour la connexion
	var xhr = getXhr();
		xhr.open("POST", "01_include/struct_colorBox.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("colorbox=creationModificationContact&no_contact="+no_contact+"&sans_image_header=1");
	
	colorboxLight(xhr.responseText);
	
	var selects = element("colorboxLight").getElementsByTagName("select");
	for(var i=0;i<selects.length;i++){
		changer_type_contact(selects[i],true);
	}
}
function creationModificationContact(){
	//1. On récupère tous les champs : 
	var nom = (element("input_contact_nom").value!=element("input_contact_nom").title)?encodeURIComponent_vrai(element("input_contact_nom").value):"";
	var no_contact = element("no_contact").value;
	if(element("est_moi")!=null)
		var est_moi = ((element("est_moi").checked)?1:'');
	else
		var est_moi="";
	
	var les_inputs = element("input_contact_nom").nextSibling.getElementsByTagName("input");
	
	var les_selects = element("input_contact_nom").nextSibling.getElementsByTagName("select");
	
	var params = "input_contact_nom="+nom+"&est_moi="+est_moi+"&no_contact="+no_contact;
	
	for(var i=0;i<les_inputs.length;i++){
		if(les_inputs[i].type!="checkbox")
			params += "&"+les_inputs[i].name+"="+((les_inputs[i].value!=les_inputs[i].title)?encodeURIComponent_vrai(les_inputs[i].value):'');
		else
			params += "&"+les_inputs[i].name+"="+(les_inputs[i].checked?1:'');
	}
	
	for(var i=0;i<les_selects.length;i++){
		params += "&"+les_selects[i].name+"="+encodeURIComponent_vrai(les_selects[i].value);
	}
	
	//2. On envoie la requête
	var xhr = getXhr();
		xhr.open("POST", "03_ajax/creationModification_contact.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send(params);
	var contact = eval("("+xhr.responseText+")");
	if(contact[0]){
		supprime_message("colorboxLight",true);
		//message(contact[1],{"devant":true,"duree":3});
		
		//On récupère le "bouton ajouter"
		if(element("contact_"+contact[1]["no"])==null){
			var btn_ajout = element("contact").firstChild.lastChild;
			if(btn_ajout.previousSibling!=null&&typeof(btn_ajout.previousSibling.className)!="undefined"&&btn_ajout.previousSibling.className.indexOf("un_contact")>-1)
				var dernier_num = btn_ajout.previousSibling.firstChild.id.split("_")[btn_ajout.previousSibling.firstChild.id.split("_").length-1];
			else
				var dernier_num = 0;
			var div_contact = document.createElement("div");
				div_contact.className = "un_contact";
				div_contact.id = "contact_"+contact[1]["no"];
				var input_hidden = document.createElement("input");
					input_hidden.type = "hidden";
					input_hidden.value = contact[1]["no"];
					input_hidden.id = "BDDcontact_no_"+dernier_num;
					input_hidden.name = "BDDcontact_no_"+dernier_num;
				var div_click = document.createElement("div");
					div_click.appendChild(document.createTextNode(contact[1]["nom"]));
				ajoute_evenement(div_click,"click",'fenetre_creationModificationContact('+contact[1]["no"]+')');
				//if(element("BDDtype").value=="evenement"||element("BDDtype").value=="structure"){
					var select_role = document.createElement("select");
						select_role.id = "BDDcontact_no_role_"+dernier_num;
						select_role.name = "BDDcontact_no_role_"+dernier_num;
					var xhr = getXhr();
						xhr.open("POST", "03_ajax/get_select_role.php", false);
						xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
						xhr.send(null);
					var les_roles = eval("("+xhr.responseText+")");
					var option = document.createElement("option");
						option.value = 0;
						option.appendChild(document.createTextNode("Rôle du contact"));
					select_role.appendChild(option);
					for(var j=0;j<les_roles.length;j++){
						var option = document.createElement("option");
							option.value = les_roles[j]["no"];
							option.appendChild(document.createTextNode(les_roles[j]["libelle"]));
						select_role.appendChild(option);
					}
					if(element("BDDtype").value!="evenement"&&element("BDDtype").value!="structure")
						select_role.style.display = "none";
				/*}
				else{
					var select_role = document.createElement("input");
						select_role.type = "hidden";
						select_role.value = 0;
						select_role.id = "BDDcontact_no_role_"+dernier_num;
						select_role.name = "BDDcontact_no_role_"+dernier_num;
				}*/
				
				var span_fermer = document.createElement("span");
					span_fermer.className = "fermer infobulle";
					span_fermer.onclick = function(){this.parentNode.parentNode.removeChild(this.parentNode);maj_input_no_contact();};
					span_fermer.title = "Retirer ce contact";
		
			div_contact.appendChild(input_hidden);
			div_contact.appendChild(div_click);
			div_contact.appendChild(select_role);
			div_contact.appendChild(span_fermer);
		
			btn_ajout.parentNode.insertBefore(div_contact,btn_ajout);
			
			//On met à jour les id et name pour l'enregistrement
			maj_input_no_contact();
		}
		else{
			element("contact_"+contact[1]["no"]).firstChild.nextSibling.firstChild.data = contact[1]["nom"];
		}
	}
	else{
		message(contact[1],{"devant":true,"duree":3});
	}
	return false;
}
	function maj_input_no_contact(){
		var les_inputs = element("contact").getElementsByTagName("input");
		for(var i=0;i<les_inputs.length;i++){
			les_inputs[i].id = "BDDcontact_no_"+i;
			les_inputs[i].name = "BDDcontact_no_"+i;
		}
		var les_selects = element("contact").getElementsByTagName("select");
		for(var i=0;i<les_selects.length;i++){
			les_selects[i].id = "BDDcontact_no_role_"+i;
			les_selects[i].name = "BDDcontact_no_role_"+i;
		}
	}
	
	function verifier_contact_vide_blur(input){
		var les_inputs = copier_tab(input.parentNode.parentNode.parentNode.getElementsByTagName("input"));
		//Ce n'est pas le dernier input, ni les deux premiers, et il est vide
		if(input!=les_inputs[les_inputs.length-2]&&input!=les_inputs[0]&&input!=les_inputs[2]&&input.value==""){
			input.parentNode.parentNode.parentNode.removeChild(input.parentNode.parentNode);
		}
		else
			input_blur(input);
	}
	function verifier_contact_vide(table){
		var les_inputs = copier_tab(table.getElementsByTagName("input"));
		for(var i=0;i<les_inputs.length;i++){
			if(les_inputs[i].type=="text"){ //C'est une valeur (pas la case "public")
				if(les_inputs[i].value==""||les_inputs[i].value==les_inputs[i].title){//1. L'input est vide
					//On regarde alors si l'input suivant existe, sinon on ne fait rien.
					if(typeof(les_inputs[i+2])!="undefined"){ //i+2 car on saute l'input checkbox "public/privé"
						//S'il est vide on supprime sa ligne.
						console.log(les_inputs.length);
						if((les_inputs[i+2].value==""||les_inputs[i+2].value==les_inputs[i+2].title)&&i>1)//&&les_inputs.length>4
							les_inputs[i+2].parentNode.parentNode.parentNode.removeChild(les_inputs[i+2].parentNode.parentNode);
						//Sinon on supprime la ligne de l'input courant
						/*else{
							les_inputs[i].parentNode.parentNode.parentNode.removeChild(les_inputs[i].parentNode.parentNode);
							les_inputs[i+2].focus();
						}*/
					}
				}
				else{//2. L'input n'est pas vide
					//On regarde alors si l'input suivant existe, sinon on le créait
					if(typeof(les_inputs[i+2])=="undefined"){
						var ligne = table.getElementsByTagName("tr")[table.getElementsByTagName("tr").length-1].cloneNode(true);
							ligne.className = "";
						//var id = table.getElementsByTagName("tr")[table.getElementsByTagName("tr").length].id;
							var inputs_ligne = ligne.getElementsByTagName("input");
							var num = inputs_ligne[0].id.split("_")[inputs_ligne[0].id.split("_").length-1];
								num++;
								inputs_ligne[0].value = "";
								inputs_ligne[0].className = "";
								inputs_ligne[0].id = "input_contact_"+num;
								inputs_ligne[0].name = "input_contact_"+num;
								inputs_ligne[1].checked = false;
								inputs_ligne[1].id = "afficher_contact_"+num;
								inputs_ligne[1].name = "afficher_contact_"+num;
								inputs_ligne[1].nextSibling.htmlFor = "afficher_contact_"+num;
							var inputs_select = ligne.getElementsByTagName("select");
								inputs_select[0].value = 0;
								inputs_select[0].id = "select_contact_"+num;
								inputs_select[0].name = "select_contact_"+num;
						table.appendChild(ligne);
						
						parcours_recursif(ligne);
						inputs_ligne[0].onblur = function(){verifier_contact_vide_blur(this);};
						changer_type_contact(inputs_select[0]);
					}
				}
			}
		}
	}
	function changer_type_contact(select,ne_pas_modifier){
		if(typeof(ne_pas_modifier)=="undefined")
			ne_pas_modifier = false;
		var ligne = select.parentNode.parentNode;
			ligne.className = url_rewrite(select.options[select.selectedIndex].text);
		var inputs_ligne = ligne.getElementsByTagName("input");
		var label = ligne.getElementsByTagName("label")[0];
		var input = inputs_ligne[0];
		var checkbox = inputs_ligne[1];
		
		input.className = "";
		//if(!ne_pas_modifier)
		if(input.value==input.title)
			input.value = "";
		
		if(ligne.className=="email"){
			input.title = "Adresse email";
			
			label.firstChild.data = " Privée";
			checkbox.style.display="none";
			if(!ne_pas_modifier)
				checkbox.checked = false;
		}
		else if(ligne.className=="telephone"){
			input.title = "Numéro de téléphone";
			
			label.firstChild.data = " Public";
			checkbox.style.display="inline-block";
			if(!ne_pas_modifier)
				checkbox.checked = false;
		}
		else if(ligne.className=="facebook"){
			input.title = "URL de votre page facebook";
			
			label.firstChild.data = " Public";
			checkbox.style.display="inline-block";
			if(!ne_pas_modifier)
				checkbox.checked = true;
		}
		else if(ligne.className=="site-internet"){
			input.title = "URL de votre site";
			
			label.firstChild.data = " Public";
			checkbox.style.display="inline-block";
			if(!ne_pas_modifier)
				checkbox.checked = true;
		}
		else if(ligne.className=="soundcloud"){
			input.title = "URL de votre page soundcloud";
			
			label.firstChild.data = " Public";
			checkbox.style.display="inline-block";
			if(!ne_pas_modifier)
				checkbox.checked = true;
		}
		else if(ligne.className=="youtube"){
			input.title = "URL de votre chaine youtube";
			
			label.firstChild.data = " Public";
			checkbox.style.display="inline-block";
			if(!ne_pas_modifier)
				checkbox.checked = true;
		}
		else if(ligne.className=="twitter"){
			input.title = "URL de votre page twitter";
			
			label.firstChild.data = " Public";
			checkbox.style.display="inline-block";
			if(!ne_pas_modifier)
				checkbox.checked = true;
		}
		else{
			input.title = "Sélectionnez un type";
			
			label.firstChild.data = " ";
			checkbox.style.display="none";
		}
		
		input_blur(input);
	}
	
		function url_rewrite(chaine){
			chaine = chaine.replace(/&([A-Za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);/gi, '$1');
			chaine = chaine.replace(/&([A-Za-z]{2})(?:lig);/gi, '$1');
			chaine = chaine.replace(/ /gi, '-');
			chaine = chaine.replace(/[éèêë]/gi, 'e');
			chaine = chaine.replace(/[àâä]/gi, 'a');
			chaine = chaine.replace(/[ùûü]/gi, 'u');
			chaine = chaine.replace(/[öôò]/gi, 'o');
			chaine = chaine.replace(/[ïîì]/gi, 'i');
			chaine = chaine.replace(/ç/gi, 'c');
			chaine = chaine.replace(/&[^;]+;/gi, '');
			chaine = chaine.replace(/[^A-Za-z0-9-]+/gi, '');
			chaine = chaine.replace(/-{2,}/gi, '');
			return chaine.toLowerCase();
		}
		
	function ajouter_ligne_contact(table){
		var ligne = table.getElementsByTagName("tr")[0].cloneNode(true);
			//TODO On donne la bonne classe à la ligne
			//TODO la fonction qui change le contenu d'une ligne en fonction de sa classe
		table.appendChild(ligne);
	}


function inscription_newsletter(){
	var no_ville = element("BDDno_ville_cc").value;
	var xhr = getXhr();
		xhr.open("POST", "03_ajax/newsletter.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("input_email="+encodeURIComponent_vrai(element("input_email").value)+"&no_ville="+no_ville+"&input_captcha="+encodeURIComponent_vrai(element("input_captcha").value));
	var newsletter = eval("("+xhr.responseText+")");
	if(newsletter[0]){
		supprime_message("colorbox",true);
		message(newsletter[1],{"devant":true,"duree":3});
	}
	else{
		recharger_captcha(element("input_captcha").nextSibling.nextSibling)
		message(newsletter[1],{"devant":true,"duree":3});
	}
	return false;
}

function inscription(){
	var parametre = new Array();
		parametre["input_no_ville"] = element("input_no_ville").value;
		parametre["input_email"] = (element("input_email").value!=element("input_email").title)?element("input_email").value:'';
		parametre["input_email_verification"] = (element("input_email_verification").value!=element("input_email_verification").title)?element("input_email_verification").value:'';
		parametre["input_mdp"] = (element("input_mdp").value!=element("input_mdp").title)?element("input_mdp").value:'';
		parametre["input_captcha"] = (element("input_captcha").value!=element("input_captcha").title)?element("input_captcha").value:'';
		parametre["input_pseudo"] =(element("input_pseudo").value!=element("input_pseudo").title)? element("input_pseudo").value:'';
	var params = "";
	for(var cle in parametre){
		params += ((params!="")?"&":"")+cle+"="+encodeURIComponent_vrai(parametre[cle]);
	}
	
	var xhr = getXhr();
		xhr.open("POST", "03_ajax/inscription.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send(params);
	var newsletter = eval("("+xhr.responseText+")");
	if(newsletter[0]){
		supprime_message("colorbox",true);
		message(newsletter[1],{"devant":true});
	}
	else{
		recharger_captcha(element("input_captcha").nextSibling.nextSibling)
		message(newsletter[1],{"devant":true,"duree":3});
	}
	return false;
}

function afficher_formulaire_courriel(fiche_contact){
	if(fiche_contact.className.indexOf("formulaire_ouvert")>-1){
		fiche_contact.className = fiche_contact.className.replace(" formulaire_ouvert","");
	}
	else{
		fiche_contact.className += " formulaire_ouvert";
	}
}

function envoyer_courriel(){
	var parametre = new Array();
		parametre["input_email_expediteur"] = (element("input_email_expediteur").value!=element("input_email_expediteur").title)?element("input_email_expediteur").value:'';
		parametre["input_contact_libelle"] = (element("input_contact_libelle").value!=element("input_contact_libelle").title)?element("input_contact_libelle").value:'';
		parametre["textarea_contenu_mail"] = (element("textarea_contenu_mail").value!=element("textarea_contenu_mail").title)?element("textarea_contenu_mail").value:'';
		parametre["input_no_contact"] = element("input_no_contact").value;
		parametre["input_captcha"] = (element("input_captcha").value!=element("input_captcha").title)?element("input_captcha").value:'';
	var params = "";
	for(var cle in parametre){
		params += ((params!="")?"&":"")+cle+"="+encodeURIComponent_vrai(parametre[cle]);
	}
	
	var xhr = getXhr();
		xhr.open("POST", "03_ajax/envoyer_courriel.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send(params);
	var newsletter = eval("("+xhr.responseText+")");
	if(newsletter[0]){
		supprime_message("colorboxLight",true);
		message(newsletter[1],{"devant":true,"duree":3});
	}
	else{
		recharger_captcha(element("input_captcha").nextSibling.nextSibling)
		message(newsletter[1],{"devant":true,"duree":3});
	}
	return false;
}

function recharger_captcha(img){
	var d = new Date();
	img.src = "01_include/img_captcha.php?a="+d.getTime();
	img.previousSibling.previousSibling.value = "";
	input_blur(img.previousSibling.previousSibling);
}

function poster_message_fiche(no_msg){
	if(typeof(no_msg)=="undefined") var no_msg=0; //C'est un message, et non la réaction à un message
	//1. On vérifie que l'utilisateur est connecté
	if(est_connecte()){
		//2. On vérifie que l'utilisateur possède un pseudo
		if(get_pseudo()!=""){
			//3 On récupère les informations sur le message
			if(no_msg>0)
				var contenu = CKEDITOR.instances["nouveau_commentaire_"+no_msg].getData(); //On récupère le contenu du message (textarea nouveau_commentaire_[no_msg])
			else
				var contenu = CKEDITOR.instances.nouveau_message.getData(); //On récupère le contenu (textarea nouveau_message)
			//4. On récupère les paramètres de la fiches (type,no)
			var param = getParametresURL();
			var parametres = new Array();
				parametres["no"] = param["no"];
				parametres["p"] = param["p"];
				parametres["contenu"] = contenu;
				parametres["no_msg"] = no_msg;
			var params = "";
			for(var cle in parametres){
				params += ((params!="")?"&":"")+cle+"="+encodeURIComponent_vrai(parametres[cle]);
			}
			//5. On poste le message (BDD)
			var xhr = getXhr();
				xhr.open("POST", "03_ajax/poster_message.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send(params);
			var resultat_message = eval("("+xhr.responseText+")");
			if(resultat_message[0]){
				if(resultat_message[1]["inscription_sujet_complet"]){
					console.log(1)
					if(element("colonne_gauche").getElementsByTagName("h4")[0]!=null){
						console.log(2)
						var h4 = element("colonne_gauche").getElementsByTagName("h4")[0];
						console.log(h4.getElementsByTagName("div")[2]);
						if(h4.getElementsByTagName("div")[h4.getElementsByTagName("div").length-1]!=null){
							var div_notif = h4.getElementsByTagName("div")[h4.getElementsByTagName("div").length-1];
							if(div_notif.className!=null&&div_notif.className.indexOf("ajout_notification")>-1&&div_notif.className.indexOf("actif")==-1)
								div_notif.className += " actif";
						}
					}
				}
				poster_message_fiche_graphique(no_msg,resultat_message[1]["no_message"],contenu,resultat_message[1]["date"],resultat_message[1]["utilisateur"],resultat_message[1]["no_contact"]);
				//6. On poste (graphique)
				/*if(no_msg==0)
					poster_nouveau_message(contenu,resultat_message[1]["no_message"],resultat_message[1]["no_utilisateur"],resultat_message[1]["pseudo"],resultat_message[1]["date"]);
				else
					poster_repondreCommentaire(contenu,resultat_message[1]["no_message"],no_msg,resultat_message[1]["no_utilisateur"],resultat_message[1]["pseudo"],resultat_message[1]["date"]);
				if(resultat_message[1]["inscription_fil"]){
					//On active la cloche
					
				}*/
			}
			else{
				message(resultat_message[1],{"devant":true,"duree":3});
			}
		}
		else{ //On appelle la fenêtre de création de pseudo qui rappellera cette fonction
			fenetre_pseudo(false,encodeURIComponent_vrai("poster_message_fiche("+no_msg+")"));
		}
	}
	else{ //On appelle la fenêtre de connexion qui rappellera cette fonction
		fenetre_connexion(false,encodeURIComponent_vrai("poster_message_fiche("+no_msg+")"));
	}
}

function poster_message_fiche_graphique(no_parent,no,contenu,date,utilisateur,no_contact){
	fermer_div_message(no_parent);
	var div_contenu = document.createElement("div");
		div_contenu.innerHTML = contenu;
	var div_principale = document.createElement("div");
	var div = document.createElement("div");
		div_principale.appendChild(div);
	if(no_parent>0){ // C'est un commentaire
		div_principale.className = "un_commentaire";
		div_principale.id = "commentaire-"+no;
		//On termine le bloc de contenu
		div_contenu.className = "un_commentaire_contenu editable infobulle[Modifier ce commentaire|haut]";
		div_contenu.onclick = function(){affiche_div_modification_message_commentaire(this);};
		div.appendChild(div_contenu);
		//On créait le footer du message
		var div_footer = document.createElement("div");
			div_footer.className = "un_commentaire_footer";
			var span_utilisateur = document.createElement("span");
				span_utilisateur.className = "un_commentaire_utilisateur";
				span_utilisateur.appendChild(document.createTextNode(utilisateur));
				if(no_contact>0)
					ajoute_evenement(span_utilisateur,"click",'fenetre_contact('+no_contact+')');
			div_footer.appendChild(span_utilisateur);
			var span_date = document.createElement("span");
				span_date.className = "un_commentaire_date";
				span_date.appendChild(document.createTextNode(date));
			div_footer.appendChild(span_date);
		div.appendChild(div_footer);
		//On créait la croix de suppression
		var div_supprimer = document.createElement("div");
			div_supprimer.className = "supprimer infobulle[Supprimer|bas]";
			div_supprimer.onclick = function(e){supprimer_message_commentaire(this.parentNode.parentNode,e)};
		div.appendChild(div_supprimer);
		//On insère le commentaire
		var zone = element("message-"+no_parent).firstChild.firstChild.nextSibling.nextSibling;
		zone.insertBefore(div_principale,zone.lastChild);
	}
	else{ // C'est un message
		div_principale.className = "un_message";
		div_principale.id = "message-"+no;
		//On créait le header du message
		var div_header = document.createElement("div");
			div_header.className = "un_message_header";
			var div_date = document.createElement("div");
				div_date.className = "un_message_date";
				div_date.appendChild(document.createTextNode(date));
			div_header.appendChild(div_date);
			var div_utilisateur = document.createElement("div");
				div_utilisateur.className = "un_message_utilisateur";
				div_utilisateur.appendChild(document.createTextNode(utilisateur));
				if(no_contact>0)
					ajoute_evenement(div_utilisateur,"click",'fenetre_contact('+no_contact+')');
			div_header.appendChild(div_utilisateur);
		div.appendChild(div_header);
		//On termine le bloc de contenu
		div_contenu.className = "un_message_contenu editable infobulle[Modifier ce message|haut]";
		div_contenu.onclick = function(){affiche_div_modification_message_commentaire(this);};
		
		div.appendChild(div_contenu);
		//On créait le div commentaire;
		var div_commentaire = document.createElement("div");
			div_commentaire.className = "un_message_commentaires";
			var span_commentaire = document.createElement("span");
				span_commentaire.className = "lien_commentaire infobulle[Répondre à ce message|bas]";
				if(element("contenu").className.indexOf("forum")==-1)
					span_commentaire.appendChild(document.createTextNode("Répondre"));
				else
					span_commentaire.appendChild(document.createTextNode("Commenter"));
				ajoute_evenement(span_commentaire,'click','affiche_div_commentaire(this)');
			div_commentaire.appendChild(span_commentaire);
		div.appendChild(div_commentaire);
		//On créait la croix de suppression
		var div_supprimer = document.createElement("div");
			div_supprimer.className = "supprimer infobulle[Supprimer|bas]";
			div_supprimer.onclick = function(e){supprimer_message_commentaire(this.parentNode.parentNode,e)};
		div.appendChild(div_supprimer);
		//on insère le message
		var zone = element("zone_messages");
		if(zone.childNodes.length==0)
			zone.appendChild(div_principale);
		else
			zone.insertBefore(div_principale,zone.firstChild);
	}
	parcours_recursif(div_principale);
}
	function supprimer_message_commentaire(div_message,event,confirmer){
		if(typeof(confirmer)=="undefined"||!confirmer){
			console.log(div_message);
			console.log(div_message.id);
			var param_message = {"ne_pas_fermer":true,"class":"colorboxLight","btn":new Array({'click':'fermer','class':'ico fleche_gauche','value':'Annuler'},{'class':'ico supprimer','click':'supprimer_message_commentaire("'+div_message.id+'",false,true);fermer','value':'Supprimer'})}
			if(typeof(event.clientX)!="undefined"&&event.clientX!=null){
				param_message["pos"] = {"x":event.clientX,"y":event.clientY+getScrollPosition()["y"]};
			}
			
			message("<h3>Êtes-vous sûr de vouloir supprimer ce message?</h3><p>Attention, l'action est irréversible.</p>",param_message);
		}
		else{
			if(est_connecte()){
				//On appele l'ajax qui supprime le message dans la base de données
				console.log(div_message);
				var no_msg = element(div_message).id.split("-")[1];
				var xhr = getXhr();
					xhr.open("POST", "03_ajax/delete_message.php", false);
					xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xhr.send("no="+no_msg);
				var resultat = eval("("+xhr.responseText+")");
				if(resultat[0]){
					//On supprime le message graphiquement
					element(div_message).parentNode.removeChild(element(div_message));
				}
				message(resultat[1]);
			}
			else{ //On appelle la fenêtre de connexion qui rappellera cette fonction
				fenetre_connexion(false,encodeURIComponent_vrai('supprimer_message_commentaire("'+div_message+'",false,true)'));
			}
		}
	}
	
	function affiche_div_modification_message_commentaire(div){
		var no_msg = div.parentNode.parentNode.id.split("-")[1];
		var textarea = document.createElement("textarea");
			textarea.value = div.innerHTML;
			textarea.id = "modification_message-"+no_msg;
		var div_modification = document.createElement("div");
			div_modification.appendChild(textarea);
		var input_annuler = document.createElement("input");
			input_annuler.type = "button";
			input_annuler.value = "Annuler";
			input_annuler.className = "ico fleche_gauche couleur";
			ajoute_evenement(input_annuler,"click",'supprimer_div_modification_message_commentaire('+no_msg+')');
		var input_enregistrer = document.createElement("input");
			input_enregistrer.type = "button";
			input_enregistrer.value = "Valider";
			input_enregistrer.className = "ico fleche couleur";
			input_enregistrer.style.cssFloat = "right";
			ajoute_evenement(input_enregistrer,"click",'enregistrer_modification_message_commentaire('+no_msg+')');
		div_modification.appendChild(input_annuler);
		div_modification.appendChild(input_enregistrer);
		div.parentNode.insertBefore(div_modification,div);
		div.style.display = "none";
		textareaToCK("modification_message-"+no_msg);
	}
	
	function supprimer_div_modification_message_commentaire(no_msg){
		//On réaffiche la div normal avec le bon contenu
		element("modification_message-"+no_msg).parentNode.nextSibling.style.display = "block";
		//On supprime la div_modification
		element("modification_message-"+no_msg).parentNode.parentNode.removeChild(element("modification_message-"+no_msg).parentNode);
	}
	function enregistrer_modification_message_commentaire(no_msg){
		if(est_connecte()){
			//On enregistre le contenu du textarea pour no_msg
			var contenu = CKEDITOR.instances["modification_message-"+no_msg].getData();
			var xhr = getXhr();
				xhr.open("POST", "03_ajax/update_message.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send("c="+encodeURIComponent(contenu)+"&no="+no_msg);
			var resultat = eval("("+xhr.responseText+")");
			if(resultat[0]){
				//On remplace le contenu de la div message ou commentaire
				element("modification_message-"+no_msg).parentNode.nextSibling.innerHTML = contenu;
				//On supprime le textarea
				supprimer_div_modification_message_commentaire(no_msg);
			}
			message(resultat[1]);
		}
		else{ //On appelle la fenêtre de connexion qui rappellera cette fonction
			fenetre_connexion(false,encodeURIComponent_vrai("enregistrer_modification_message_commentaire("+no_msg+")"));
		}
	}
/*
	function poster_nouveau_message(contenu,no,no_utilisateur,pseudo,date){
		var zone = element("zone_messages");
		var ancre = document.createElement("div");
			ancre.id = "message"+no;
			ancre.className = "ancre_message";
		var div_message = document.createElement("div");
			div_message.id = "message_"+no+"_"+no_utilisateur;
			div_message.className = "un_message";
			var div_information_message = document.createElement("div");
				div_information_message.className = "information_message";
				var span_pseudo = document.createElement("span");
					span_pseudo.className = "pseudo";
					span_pseudo.appendChild(document.createTextNode(pseudo));
				div_information_message.appendChild(span_pseudo);
				div_information_message.appendChild(document.createTextNode(" "+date));
			var div_contenu_message = document.createElement("div");
				div_contenu_message.className = "contenu_message";
				div_contenu_message.innerHTML = contenu;
			div_message.appendChild(div_information_message);
			div_message.appendChild(div_contenu_message);
		var div_repondre_message = document.createElement("div");
			div_repondre_message.id = "commentaire_"+no;
			div_repondre_message.className = "commentaires";
			var span_repondre = document.createElement("span");
				span_repondre.className = "lien_commentaire";
				span_repondre.appendChild(document.createTextNode("ajouter un commentaire"));
				span_repondre.onclick = function(){affiche_div_commentaire(this);};
			div_repondre_message.appendChild(span_repondre);
		if(zone.firstChild!=null)
			zone.insertBefore(div_repondre_message,zone.firstChild);
		else
			zone.appendChild(div_repondre_message);
		zone.insertBefore(div_message,zone.firstChild);
		zone.insertBefore(ancre,zone.firstChild);
		fermer_div_message();
	}
	function poster_repondreCommentaire(contenu,no,no_msg,no_utilisateur,pseudo,date){
		var zone = element("commentaire_"+no_msg);
		var div_message = document.createElement("div");
			div_message.id = "unCommentaire_"+no+"_"+no_utilisateur;
			div_message.className = "un_commentaire";
			var div_contenu_message = document.createElement("div");
				div_contenu_message.className = "contenu_unCommentaire";
				div_contenu_message.innerHTML = contenu;
			var div_signature_message = document.createElement("div");
				div_signature_message.className = "signature_commentaire";
				var span_pseudo = document.createElement("span");
					span_pseudo.className = "pseudo";
					span_pseudo.appendChild(document.createTextNode(pseudo));
				div_signature_message.appendChild(span_pseudo);
				div_signature_message.appendChild(document.createTextNode(" "+date));
			div_message.appendChild(div_contenu_message);
			div_message.appendChild(div_signature_message);
		var tab_com = zone.getElementsByClassName("");
		zone.insertBefore(div_message,zone.firstChild.nextSibling);
		fermer_div_message(no_msg);
	}*/

function est_connecte(){
	var xhr = getXhr();
		xhr.open("POST", "03_ajax/est_connecte.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send(null);
	return eval("("+xhr.responseText+")");
}
function get_pseudo(no){
	if(typeof(no)=="undefined")	param=null;
	else param = "no="+no;
	var xhr = getXhr();
		xhr.open("POST", "03_ajax/get_pseudo.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send(param);
	return xhr.responseText.replace("\r","").replace("\n",""); //Pourquoi est ce qu'un retour à la ligne se glisse ici ?? Bref un bout de scotch en attendant.
}
function modifier_pseudo(){
	var parametre = new Array();
		parametre["input_pseudo"] = (element("input_pseudo").value!=element("input_pseudo").title)?element("input_pseudo").value:'';
	var params = "";
	for(var cle in parametre){
		params += ((params!="")?"&":"")+cle+"="+encodeURIComponent_vrai(parametre[cle]);
	}
	var xhr = getXhr();
		xhr.open("POST", "03_ajax/modifier_pseudo.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send(params);
	var retour_pseudo = eval("("+xhr.responseText+")");
	if(retour_pseudo[0]){
		supprime_message("colorbox",true);
		message(retour_pseudo[1],{"devant":true,"duree":3});
	}
	else{
		message(retour_pseudo[1],{"devant":true,"duree":3});
	}
	return false;
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
		if(typeof(TIMEOUT_SUPPRESSION_MESSAGE["colorbox"])!="undefined"||TIMEOUT_SUPPRESSION_MESSAGE["colorbox"]!=false){
			clearTimeout(TIMEOUT_SUPPRESSION_MESSAGE["colorbox"]);
			TIMEOUT_SUPPRESSION_MESSAGE["colorbox"] = false;
			set_opacity(element("colorbox"),100);
			filtre();
		}
		//2.1. On créait alors la colorbox avec le contenu correspondant
		var div = element("colorbox");
		div.firstChild.innerHTML = contenu;
	}
	else
		var div = message(contenu,{"width":600,"id":"colorbox","ne_pas_fermer":empecher_fermeture,"filtre":true});
	parcours_recursif(div);
	return div;
}
function colorboxLight(contenu,param){
	if(typeof(param)=="undefined")
		param = {"id":"colorboxLight"};
	//1. On regarde si colorbox n'existe pas déjà
	if(element("colorboxLight")!=null){
		if(typeof(TIMEOUT_SUPPRESSION_MESSAGE["colorboxLight"])!="undefined"||TIMEOUT_SUPPRESSION_MESSAGE["colorboxLight"]!=false){
			clearTimeout(TIMEOUT_SUPPRESSION_MESSAGE["colorboxLight"]);
			TIMEOUT_SUPPRESSION_MESSAGE["colorboxLight"] = false;
			set_opacity(element("colorboxLight"),100);
		}
		//2.1. On créait alors la colorbox avec le contenu correspondant
		var div = element("colorboxLight");
		div.firstChild.innerHTML = contenu;
	}
	else
		var div = message(contenu,param);
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
function rechercher_ville2(input){
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
					creer_resultat_recherche_ville2(reponse);
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
			var b = document.createElement('b');
				b.appendChild(document.createTextNode(villes[i]["cp"]));
			ligne.appendChild(b);
			ligne.appendChild(document.createTextNode(" - "+villes[i]["libelle"]));
			ajoute_evenement(ligne,"click",'selectionner_ville('+villes[i]["no"]+',"'+villes[i]["url"]+'","'+villes[i]["libelle"]+'","'+(villes[i]["cp"]+" - "+villes[i]["libelle"])+'")');
		l.appendChild(ligne);
	}
}
function creer_resultat_recherche_ville2(villes){
	var l = element("recherche_ville_liste2").firstChild;
	vide(l);
	for(var i=0;i<villes.length;i++){
		var ligne = document.createElement("div");
			ligne.className = "recherche_ville_ligne";
			var b = document.createElement('b');
				b.appendChild(document.createTextNode(villes[i]["cp"]));
			ligne.appendChild(b);
			ligne.appendChild(document.createTextNode(" - "+villes[i]["libelle"]));
			ajoute_evenement(ligne,"click",'selectionner_ville2('+villes[i]["no"]+',"'+villes[i]["url"]+'","'+villes[i]["libelle"]+'","'+(villes[i]["cp"]+" - "+villes[i]["libelle"])+'")');
		l.appendChild(ligne);
	}
}

	function selectionner_ville(no,url,libelle,libelle_cp){
		/**
		Cette fonction est appellée dans 2 situations:
			1. L'utilisateur remplie la ville dans un champ de formulaire :
				Dans ces cas là un input type hidden input_no_ville est présent
			2. Sinon l'utilisateur souhaite changer de ville à l'aide de la colorbox
		**/
		//On prévoit tout les plantages JS
		if(element("input_no_ville")!=null&&typeof(element("input_no_ville").type)!="undefined"&&element("input_no_ville").type!=null&&element("input_no_ville").type=="hidden"){
			//L'utilisateur veut remplir le champ
			element("input_no_ville").value = no;
			element("recherche_ville").value = libelle_cp;
			vide(element("recherche_ville_liste").firstChild);
		}
		else if(element("BDDno_ville")!=null&&typeof(element("BDDno_ville").type)!="undefined"&&element("BDDno_ville").type!=null&&element("BDDno_ville").type=="hidden"){
			//L'utilisateur veut remplir le champ
			element("BDDno_ville").value = no;
                        if (element("ville") != null) {
                            element("ville").value = libelle_cp;
                        }
//			element("ville").value = libelle_cp;
                        if (element("recherche_ville") != null) {
                            element("recherche_ville").value = libelle_cp;
                        }
//                        element("update_infos_ville").value = libelle_cp;
			vide(element("recherche_ville_liste").firstChild);
		}
		else{
			//On modifie la ville en session et cookie
			var xhr = getXhr();
			xhr.open("POST", "03_ajax/modifier_ville.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("no="+no);
			var param_url = getParametresURL();
			//On met à jour la barre de menu (liens)
			element("menu_home").href = ROOT_SITE+url+"."+no+".html";
//			element("menu_editorial").href = ROOT_SITE+url+"."+no+".editorial.30km.html";
                        element("menu_editorial").href = ROOT_SITE+url+"."+no+".editorial.html";
			element("menu_agenda").href = ROOT_SITE+url+"."+no+".agenda.html";
//			element("menu_petiteannonce").href = ROOT_SITE+url+"."+no+".petite-annonce.30km.html";
                        element("menu_petiteannonce").href = ROOT_SITE+url+"."+no+".petite-annonce.html";
			element("menu_repertoire").href = ROOT_SITE+url+"."+no+".structure.html";
//			element("menu_forum").href = ROOT_SITE+url+"."+no+".forum.30km.html";
                        element("menu_forum").href = ROOT_SITE+url+"."+no+".forum.html";
		
			//On met à jour la barre de sous menu (libelle)
			element("h1ville").firstChild.data = libelle;
		
			//On recréait la nouvelle url (dans le navigateur)
			var new_url = ROOT_SITE;
				new_url += url+"."+no;
				if(typeof(param_url["p"])!="undefined"&&param_url["p"]!=""){
					new_url += "."+param_url["p"];
					if(typeof(param_url["no"])!="undefined"&&param_url["no"]!="")
						new_url += "."+param_url["titre"]+"."+param_url["no"];
				}
				new_url += ".html";
			history.pushState({ path: this.path }, '', new_url);
		
			//On recharge le contenu de la page sur laquelle l'utilisateur se trouve
			charger_page();
			supprime_message("colorbox",true);
			//filtre(false,true);
		}
	}
        
        function selectionner_ville2(no,url,libelle,libelle_cp){
		/**
		Cette fonction est appellée dans 2 situations:
			1. L'utilisateur remplie la ville dans un champ de formulaire :
				Dans ces cas là un input type hidden input_no_ville est présent
			2. Sinon l'utilisateur souhaite changer de ville à l'aide de la colorbox
		**/
		//On prévoit tout les plantages JS
		if(element("input_no_ville")!=null&&typeof(element("input_no_ville").type)!="undefined"&&element("input_no_ville").type!=null&&element("input_no_ville").type=="hidden"){
			//L'utilisateur veut remplir le champ
			element("input_no_ville").value = no;
			element("recherche_ville").value = libelle_cp;
			vide(element("recherche_ville_liste2").firstChild);
		}
		else if(element("BDDno_ville_update")!=null&&typeof(element("BDDno_ville_update").type)!="undefined"&&element("BDDno_ville_update").type!=null&&element("BDDno_ville_update").type=="hidden"){
			//L'utilisateur veut remplir le champ
			element("BDDno_ville_update").value = no;
//			element("ville").value = libelle_cp;
                        element("update_infos_ville").value = libelle_cp;
			vide(element("recherche_ville_liste2").firstChild);
		}
		else{
			//On modifie la ville en session et cookie
			var xhr = getXhr();
			xhr.open("POST", "03_ajax/modifier_ville.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("no="+no);
			var param_url = getParametresURL();
			//On met à jour la barre de menu (liens)
			element("menu_home").href = ROOT_SITE+url+"."+no+".html";
//			element("menu_editorial").href = ROOT_SITE+url+"."+no+".editorial.30km.html";
                        element("menu_editorial").href = ROOT_SITE+url+"."+no+".editorial.html";
			element("menu_agenda").href = ROOT_SITE+url+"."+no+".agenda.html";
//			element("menu_petiteannonce").href = ROOT_SITE+url+"."+no+".petite-annonce.30km.html";
                        element("menu_petiteannonce").href = ROOT_SITE+url+"."+no+".petite-annonce.html";
			element("menu_repertoire").href = ROOT_SITE+url+"."+no+".structure.html";
//			element("menu_forum").href = ROOT_SITE+url+"."+no+".forum.30km.html";
                        element("menu_forum").href = ROOT_SITE+url+"."+no+".forum.html";
		
			//On met à jour la barre de sous menu (libelle)
			element("h1ville").firstChild.data = libelle;
		
			//On recréait la nouvelle url (dans le navigateur)
			var new_url = ROOT_SITE;
				new_url += url+"."+no;
				if(typeof(param_url["p"])!="undefined"&&param_url["p"]!=""){
					new_url += "."+param_url["p"];
					if(typeof(param_url["no"])!="undefined"&&param_url["no"]!="")
						new_url += "."+param_url["titre"]+"."+param_url["no"];
				}
				new_url += ".html";
			history.pushState({ path: this.path }, '', new_url);
		
			//On recharge le contenu de la page sur laquelle l'utilisateur se trouve
			charger_page();
			supprime_message("colorbox",true);
			//filtre(false,true);
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
		filtre(false);
		
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
//		input.className = input.className+" vide";
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


function input_recherche_ville2_focus(input){
	rechercher_ville2(input);
	input_focus(input);
	if(INPUT_RECHERCHE_VILLE_BLUR!=false){
		clearTimeout(INPUT_RECHERCHE_VILLE_BLUR);
		INPUT_RECHERCHE_VILLE_BLUR=false;
	}
	element("recherche_ville_liste2").className = "";
}


/*function diaporama_editorial(){

}*/

function diaporama_editorial_next(){
	var zone = element("home_editorial_bloc");
	if(zone.className=="editorial_1"){
		zone.className = "editorial_2";
	}
	else if(zone.className=="editorial_2"){
		zone.className = "editorial_3";
	}
}
function diaporama_editorial_previous(){
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
				el.contenu_infobulle = encodeURIComponent_vrai(texte);
				el.className = el.className.replace(/infobulle(\[(.+)\])?/gi,"");
				ajoute_evenement(el,"mouseover",'if(typeof(infobulle)=="function"){infobulle(this,"'+texte+'","'+encodeURIComponent_vrai(position)+'")}');
			}
		}
	}
	
        
	if(typeof(el.tagName)!="undefined"&&el.tagName.toLowerCase()=="input"){ //INPUTS
		if(el.id=="input_distance")
			change_distance(el,true);
			
		if(el.type=="text"){
			el.onfocus = function(){input_focus(this);};
			el.onblur = function(){input_blur(this);};
			if(typeof(el.id)!="unedifned"&&el.id!=null){
				if(el.id.indexOf("email")==-1)
					el.autocomplete="off";
			}
			else
				el.autocomplete="off";
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
                        else if(dans_tab("recherche_ville2",el.className.split(" "))){
				el.onkeyup = function(){rechercher_ville2(this);};
				el.onblur = function(){input_recherche_ville_blur(this)};
				el.onfocus = function(){input_recherche_ville2_focus(this);};
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
			/*el.style.width = (!isNaN(getStyle(el,"padding-left"))?(getStyle(el,"padding-left")*2-30):largeur(el)-30)+"px";
			var div_afficher = document.createElement("div");
				div_afficher.style.width = 30+"px";
				div_afficher.style.fontSize = 0.7+"em";
				var input_afficher = document.createElement("input");
					input_afficher.type = "checkbox";
				var label_afficher = document.createElement("label");
					label_afficher.appendChild(document.createTextNode("afficher"));
				div_afficher.appendChild(input_afficher);
				div_afficher.appendChild(label_afficher);
			if(el.nextSibling!=null)
				el.parentNode.insertBefore(div_afficher,el.nextSibling);
			else
				el.parentNode.appendChild(div_afficher);*/
			input_blur(el);
		}
		else if(el.type=="button"){
			if(dans_tab("recherche",el.className.split(" ")))
				el.onclick = function(){rechercher(this.previousSibling);};
			/* NE PAS SUPPRIMER, ÇA PEUT ÊTRE INTÉRESSANT D'UTILISER ÇA AVEC LES ID
			else if(el.id=="colorbox_connexion")
				el.onclick = function(){fenetre_connexion((this.className=="ne_pas_fermer"));};
			else if(el.id=="colorbox_ville")
				el.onclick = function(){fenetre_ville((this.className=="ne_pas_fermer"));};
			else if(el.id=="colorbox_nouveau_compte")
				el.onclick = function(){fenetre_nouveau_compte((this.className=="ne_pas_fermer"));};
			else if(el.id=="colorbox_ville")
				el.onclick = function(){fenetre_ville((this.className=="ne_pas_fermer"));};*/
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
					img_temoin.src = (url!="")?ROOT_SITE+url:"../img/img_colorize.php?uri=aucune_image.png&c=4e4e4e";
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
				param_ck["height"] = height[1];
			CKEDITOR.replace(el.id,param_ck);
		}
		else{
			el.onfocus = function(){input_focus(this);};
			el.onblur = function(){input_blur(this);};
			input_blur(el);
		}
	}
	//Liens
	if(typeof(el.tagName)!="undefined"&&el.tagName.toLowerCase()=="a"){
		if(el.href.indexOf(ROOT_SITE)==-1){
			//Ce n'est pas un lien sur ensembleici
			el.target = "_blank";
		}
		else{
			//C'est un lien interne
			//On s'assure qu'il s'agit d'un fichier html/htm ou php
			var test_href = el.href.split("#")[0];
				test_href = test_href.split("?")[0];
			if(test_href.indexOf(".html")==test_href.length-5||test_href.indexOf(".htm")==test_href.length-4||test_href.indexOf(".php")==test_href.length-4){
				if(el.onclick==null||el.onclick=="")
					el.onclick = function(e){choix_page(this,e);};
			}
			else{ //Sinon c'est un fichier pdf, image, etc.
				//TEST FICHIER PDF : el.href.split("?")[0].indexOf(".pdf")==el.href.split("?")[0].length-4
				el.target = "_blank";
			}
		}
	}
	
	/*
	if(&&el.href.indexOf(ROOT_SITE)>-1&&el.href.indexOf("#")==-1&&el.target!="_blank"&&el.href.split("?")[0].indexOf(".pdf")!=el.href.split("?")[0].length-4){
		el.onclick = function(e){choix_page(this,e);};
	}
	else if(typeof(el.tagName)!="undefined"&&el.tagName.toLowerCase()=="a"&&el.href.indexOf(ROOT_SITE)==-1&&el.target!="_blank"&&el.href.split("?")[0].indexOf(".pdf")==el.href.split("?")[0].length-4){
		el.target = "_blank";
	}*/
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
	XHR_FORMULAIRE.open("POST", "03_ajax/"+page, true);
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
				el.contenu_infobulle = encodeURIComponent_vrai(texte);
				el.className = el.className.replace(/infobulle(\[(.+)\])?/gi,"");
				ajoute_evenement(el,"mouseover",'if(typeof(infobulle)=="function"){infobulle(this,"'+texte+'","'+encodeURIComponent_vrai(position)+'")}');
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
	var link = get_link_cssBig();
	if(link!=false){ //Le fichier existe (MENU GRAND ECRAN)
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

var SCROLLING_PROGRESSIF = false;
function scroll_progressif(scroll_fin,pas,timer){
	if(SCROLLING_PROGRESSIF!=false){
		clearTimeout(SCROLLING_PROGRESSIF);
		SCROLLING_PROGRESSIF = false;
	}
	if(typeof(pas)=="undefined") pas = 100;
	if(typeof(timer)=="undefined") timer = 50;
	if(getScrollPosition()["y"]>scroll_fin+pas){
		set_documentScroll(getScrollPosition()["y"]-pas);
		SCROLLING_PROGRESSIF = setTimeout('scroll_progressif('+scroll_fin+')',timer);
	}
	else if(getScrollPosition()["y"]<scroll_fin-pas){
		set_documentScroll(getScrollPosition()["y"]+pas);
		SCROLLING_PROGRESSIF = setTimeout('scroll_progressif('+scroll_fin+')',timer);
	}
	else{
		set_documentScroll(scroll_fin);
	}
}

function remonter_progressif(){
	if(getScrollPosition()["y"]>180){
		scroll_progressif(180);
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
function lancer_fichier_audio(zone,lecteur,url,ne_pas_lancer){
	if(typeof(ne_pas_lancer)=="undefined")
		ne_pas_lancer = false;
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
		
		var iframe = document.createElement("iframe");
			if(lecteur=="soundcloud"){ //SOUNDCLOUD
				var params = "url="+url;
					params += "&color=23AADD&auto_play="+((ne_pas_lancer)?'false':'true')+"&buying=true&liking=true&download=true&sharing=true&show_artwork=false&show_comments=true&show_playcount=true&show_user=false&hide_related=true&visual=false&start_track=0&callback=true&show_reposts=false";
				iframe.src = "https://w.soundcloud.com/player/?"+params;
			}
			else{ //ARTE
				var reg_id = /http:\/\/audioblog\.arteradio\.com\/post\/([0-9]+)\/(.*)/gi;
				var tab_id = reg_id.exec(url); //http://audioblog.arteradio.com/post/3065691/et_si_on_discutait_du_projet__/
				var params = tab_id[1];
				iframe.src = "http://download.audioblogs.arteradio.com/static/player/embed.html?ids="+params;
			}
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
			parametres["dist"] = nouvelle_distance;
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
			parametres["du"] = input.value.replace(/\//gi, '-');
		setUrl(parametres,true);
	}
}

function getParametresURL(url){
	//var regex_url = /(?:http:\/\/www\.ensembleici\.fr\/00_dev_sam\/)?([a-z-]+)\.([0-9]+)(?:\.(editorial|agenda|forum|petite-annonce|structure)(?:\.([a-z0-9-]+)\.([0-9]+))?(?:\.([0-9]+km|tous))?(?:\.du-([0-9]{2}-[0-9]{2}-[0-9]{4}))?(?:\.(distance|reputation|date))?(?:\.page([0-9]+))?)?\.html$/gi;
	var regex_url = /^(?:http:\/\/www\.ensembleici\.fr\/)?([a-z-]+)\.([0-9]+)(?:\.(editorial|agenda|forum|petite-annonce|structure)(?:\.(?:tag([0-9]+(?:-[0-9]+)*)))?(?:\.([a-z0-9-]+)\.([0-9]+))?(?:\.([0-9]+km|tous))?(?:\.du-([0-9]{2}-[0-9]{2}-[0-9]{4}))?(?:\.(distance|reputation|date))?(?:\.page([0-9]+))?)?\.html$/gi;
	var formate_ancre = /#.*/gi;
	/***
	On simule ici le htaccess pour envoyer en ajax les bons paramètres post à charger_page.php
	***/
	//Accueil et listes : [NOM_VILLE].[NO_VILLE](.[TYPE_FICHE](.[TAGS])?(.[DISTANCE])?(.[DATE])?(.[TRI])?(.[PAGE])?)?.html
	var prefix_regex = ROOT_SITE.replace(/\./gi,'\\.').replace(/\//gi,'\\/');
	var regex_liste = new RegExp("^(?:"+prefix_regex+")?([a-z-]+)\.([0-9]+)(?:\.(editorial|agenda|forum|petite-annonce|structure)(?:\.(?:tag([0-9]+(?:-[0-9]+)*)))?(?:\.([0-9]+km|tous))?(?:\.du-([0-9]{2}-[0-9]{2}-[0-9]{4}))?(?:\.(distance|reputation|date))?(?:\.page([0-9]+))?)?\.html$","gi");
	//Fiches et prévisualisation de fiches : (previsualisation)?.[TYPE_FICHE].[NOM_VILLE].[TITRE].[NO_VILLE].[NO].html
	var regex_fiche = new RegExp("^(?:"+prefix_regex+")?(?:(previsualisation)\.)?(editorial|agenda|forum|petite-annonce|structure)\.([a-z-]+)\.([a-z0-9-]+)\.([0-9]+)\.([0-9]+)\.html$","gi");
	//Recherche
	var regex_recherche = new RegExp("(?:"+prefix_regex+")?recherche\.php\\?q=(.*)$","gi");
	
	//Autres pages
	var regex_autresPages = new RegExp("(?:"+prefix_regex+")?([a-z-]+)(?:\.([a-z-]+))?(?:\.([0-9]+))?(?:\.(generalites|thematique|details|illustration|validation))?\.html$","gi");
	//On récupère l'url à tester
	if(typeof(url)=="undefined")
		var url = document.URL;
	url = url.replace(formate_ancre,"");
		
	//on prépare la variable de retour
	var retour = new Array();
	//On test l'url
	var param_url = regex_liste.exec(url);
	if(typeof(param_url)=="object"&&param_url!=null){
		retour["nom_ville"] = (typeof(param_url[1])!="undefined")?param_url[1]:"";
		retour["id_ville"] = (typeof(param_url[2])!="undefined")?param_url[2]:"";
		retour["p"] = (typeof(param_url[3])!="undefined")?param_url[3]:"";
		retour["tags"] = (typeof(param_url[4])!="undefined")?param_url[4]:"";

		retour["dist"] = (typeof(param_url[5])!="undefined")?param_url[5]:"";
		retour["du"] = (typeof(param_url[6])!="undefined")?param_url[6]:"";
		retour["tri"] = (typeof(param_url[7])!="undefined")?param_url[7]:"";
		retour["np"] = (typeof(param_url[8])!="undefined")?param_url[8]:"";
	}
	else{
		var param_url = regex_fiche.exec(url);
		if(typeof(param_url)=="object"&&param_url!=null){
			retour["previsualisation"] = (typeof(param_url[1])!="undefined")?param_url[1]:"";
			retour["p"] = (typeof(param_url[2])!="undefined")?param_url[2]:"";
			retour["nom_ville"] = (typeof(param_url[3])!="undefined")?param_url[3]:"";
			retour["titre"] = (typeof(param_url[4])!="undefined")?param_url[4]:"";
			retour["id_ville"] = (typeof(param_url[5])!="undefined")?param_url[5]:"";
			retour["no"] = (typeof(param_url[6])!="undefined")?param_url[6]:"";
		}
		else{
			var param_url = regex_recherche.exec(url);
			if(typeof(param_url)=="object"&&param_url!=null){
				retour["p"] = "recherche";
				retour["q"] = (typeof(param_url[1])!="undefined")?param_url[1]:"";
			}
			else{
				var param_url = regex_autresPages.exec(url);
				if(typeof(param_url)=="object"&&param_url!=null){
					retour["p"] = (typeof(param_url[1])!="undefined")?param_url[1]:"";
					retour["sous_page"] = (typeof(param_url[2])!="undefined")?param_url[2]:"";
					retour["no"] = (typeof(param_url[3])!="undefined")?param_url[3]:"";
					retour["etape"] = (typeof(param_url[4])!="undefined")?param_url[4]:"";
				}
			}
		}
	}
	return retour;
}
function setUrl(parametres,raz_pages){
	if(typeof(raz_pages)=="undefined")
		raz_pages = false;
	var url = ROOT_SITE;
		if(typeof(parametres["no"])!="undefined"&&parametres["no"]!=""){ //C'est une fiche
			url += parametres["p"];
			url += "."+parametres["nom_ville"];
			url += "."+parametres["titre"];
			url += "."+parametres["id_ville"];
			url += "."+parametres["no"];
		}
		else{ //Sinon
			url += parametres["nom_ville"];
			url += "."+parametres["id_ville"];
			url += "."+parametres["p"];
			
			if(parametres["tags"]!="")
				url += ".tag"+parametres["tags"];
			if(parametres["dist"]!="")
				url += "."+parametres["dist"];
			if(parametres["du"]!="")
				url += ".du-"+parametres["du"];
			if(parametres["tri"]!=""&&parametres["tri"]!="date")
				url += "."+parametres["tri"];
			if(parametres["np"]!=""&&parametres["np"]>1&&!raz_pages)
				url += ".page"+parametres["np"];
		}
		url += ".html";
	history.pushState({ path: this.path }, '', url);
	charger_page();
}

TIMEOUT_BARRE_CHARGEMENT = false;
function barre_chargement(etat){
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
	if(element("boite_tag")!=null)
		element("boite_tag").className = vie;
	else if(element("boite_tag_publique")!=null)
		element("boite_tag_publique").className = vie;
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
		XHR_RECHERCHE_TAG.send("m="+encodeURIComponent_vrai(texte)+"&exception="+get_tags_courants()); //"&vie="+((element("boite_tag").className=="vie-toute")?"":element("select_vie").value.split("_")[1])+
	/*}
	else{
		var zone = element("zone_recherche");
		vide(zone);
	}*/
}
function creer_resultat_recherche_tag(tags){
	if(element("boite_tag_publique")!=null){
		var parametres = getParametresURL();
		var url = parametres["nom_ville"]+"."+parametres["id_ville"]+"."+parametres["p"]+"[**TAGS**]";
		if(parametres["dist"]!="")
			url += "."+parametres["dist"];
		if(parametres["du"]!="")
			url += "."+parametres["du"];
		if(parametres["tri"]!="")
			url += "."+parametres["du"];
		url += ".html";
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
			if(parametres["tags"]!="")
				var liste_tags = "."+parametres["tags"]+"-"+tags[t]["no"];
			else
				var liste_tags = ".tag"+tags[t]["no"];
			//on créait le html de cette liste
			var a_tag = document.createElement("a");
				a_tag.className = "un_tag "+tags[t]["class"];
				a_tag.id = "tag_"+tags[t]["no"];
				a_tag.onclick = function(){tag_click_public(this);};
				a_tag.appendChild(document.createTextNode(tags[t]["titre"]));
				a_tag.href = url.replace("[**TAGS**]",liste_tags);
			zone.appendChild(a_tag);
		}
	}
	else{ //Sinon on est dans l'étape thématique d'une création/modification
		var zone = element("tags_dispo");
		vide(zone);
		for(var t=0;t<tags.length;t++){
			//on créait le html de cette liste
			var div_tag = document.createElement("div");
				div_tag.className = "un_tag "+tags[t]["class"];
				var input_tag = document.createElement("input");
					input_tag.type = "checkbox";
					input_tag.id = "tag_"+tags[t]["no"];
					input_tag.onclick = function(){tag_click(this);};
				var label_tag = document.createElement("label");
					label_tag.htmlFor = "tag_"+tags[t]["no"];
					label_tag.appendChild(document.createTextNode(tags[t]["titre"]));
				div_tag.appendChild(input_tag);
				div_tag.appendChild(label_tag);
			zone.appendChild(div_tag);
		}
	}
}
function tag_click_public(tag){
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
	if(element("liste_tag_select")!=null){
		var les_tags = element("liste_tag_select").getElementsByTagName("a");
		var liste_tags = "";
		for(var t=0;t<les_tags.length;t++){
			liste_tags += ((liste_tags!="")?",":"")+les_tags[t].id.split("_")[1];
		}
	}
	else{
		var les_tags = element("tags_select").getElementsByTagName("input");
		var liste_tags = "";
		for(var t=0;t<les_tags.length;t++){
			liste_tags += ((liste_tags!="")?",":"")+les_tags[t].id.split("_")[1];
		}
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
		XHR_RECHERCHE_ITEM.send("q="+encodeURIComponent_vrai(texte)+"&p="+type);
	}
	else{
		var zone = element("zone_recherche");
		vide(zone);
	}
}
function creer_resultat_recherche_temps_reel(resultat){
	var zone = element("zone_recherche");
	vide(zone);
	var type = resultat["type"];
	zone.className = type;
	var resultat = resultat["resultat"]["liste"];
	for(var i=0;i<resultat.length;i++){
		var a = document.createElement("a");
			a.className = type;
			if(type=="evenement") type_url = "agenda";
			else type_url = type;
			a.href = "espace-personnel."+type_url+"."+resultat[i]["no"]+".generalites.html";
			var div_image = document.createElement("div");
				div_image.className = "image";
				var img = document.createElement("img");
					img.src = resultat[i]["image"];
					img.onload=function(){img_load(this)};
					img.onerror=function(){img_error(this)};
				div_image.appendChild(img);
			var h3 = document.createElement("h3");
				h3.innerHTML = resultat[i]["titre"];
			var p = document.createElement("p");
				p.appendChild(document.createTextNode(resultat[i]["descriptionsub"]));
			a.appendChild(div_image);
			a.appendChild(h3);
			a.appendChild(p);
			//a.href = "?"+param;
			//a.onclick = function(e){choix_page(this,e);};
		zone.appendChild(a);
	}
	parcours_recursif(zone);
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
		XHR_RECHERCHE_ITEM.send("m="+encodeURIComponent_vrai(texte)+"&type="+OPTIONS["page"]);
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

function affiche_div_message(btn){
	var zone = btn.parentNode;
	var insertBefore = false;
	var textarea = document.createElement("textarea");
		textarea.id = "nouveau_message";
		textarea.style.width = 100+"%";
	var btn_annuler = document.createElement("input");
		btn_annuler.type = "button";
		btn_annuler.value = "Annuler";
		btn_annuler.className = "ico couleur fleche_gauche";
		btn_annuler.style.cssFloat = "left";
		ajoute_evenement(btn_annuler,"click",'fermer_div_message()');
	var btn_valider = document.createElement("input");
		btn_valider.type = "button";
		btn_valider.value = "Valider";
		btn_valider.className = "ico couleur fleche";
		btn_valider.style.cssFloat = "right";
		ajoute_evenement(btn_valider,"click",'poster_message_fiche()');
	var div_commentaire = document.createElement("div");
	div_commentaire.appendChild(textarea);
	div_commentaire.appendChild(btn_annuler);
	div_commentaire.appendChild(btn_valider);
	div_commentaire.style.paddingBottom = 30+"px";
	btn.parentNode.insertBefore(div_commentaire,btn);
	btn.style.display = "none";
	textareaToCK('nouveau_message');
}

function affiche_div_commentaire(span){
	var zone = span.parentNode;
	var insertBefore = false;
	var no_message = span.parentNode.parentNode.parentNode.id.split("-")[1];
	if(zone.getElementsByClassName("un_commentaire").length>0){
		insertBefore = zone.getElementsByClassName("un_commentaire")[0];
	}
	var textarea = document.createElement("textarea");
		textarea.id = "nouveau_commentaire_"+no_message;
		textarea.style.width = 100+"%";
	var btn_annuler = document.createElement("input");
		btn_annuler.type = "button";
		btn_annuler.value = "Annuler";
		btn_annuler.className = "ico couleur fleche_gauche";
		btn_annuler.style.cssFloat = "left";
		ajoute_evenement(btn_annuler,"click",'fermer_div_message('+no_message+')');
	var btn_valider = document.createElement("input");
		btn_valider.type = "button";
		btn_valider.value = "Valider";
		btn_valider.className = "ico couleur fleche";
		btn_valider.style.cssFloat = "right";
		ajoute_evenement(btn_valider,"click",'poster_message_fiche('+no_message+')');
	var div_commentaire = document.createElement("div");
		div_commentaire.style.margin = "0.5em 1em 0.5em 2em";
		div_commentaire.style.paddingLeft = "10px";
	div_commentaire.appendChild(textarea);
	div_commentaire.appendChild(btn_annuler);
	div_commentaire.appendChild(btn_valider);
	div_commentaire.style.paddingBottom = 30+"px";
	/*if(insertBefore!=false)
		span.parentNode.insertBefore(div_commentaire,insertBefore);
	else*/
	span.parentNode.appendChild(div_commentaire);
	span.style.display = "none";
	textareaToCK('nouveau_commentaire_'+no_message);
}

function fermer_div_message(no){
	if(typeof(no)=="undefined"||no==0){
		//CKEDITOR.instances.afficheCommentaire.setData("");
		CKEDITOR.instances.nouveau_message.destroy();
		//input_blur(document.getElementById("reponse_forum"));
		element("nouveau_message").parentNode.nextSibling.style.display = "inline-block";
		element("nouveau_message").parentNode.parentNode.removeChild(element("nouveau_message").parentNode);
		/*document.getElementById("btn_reponse").style.height = 1+"px";
		set_opacity(document.getElementById("btn_reponse"), 0);
		document.getElementById("btn_annuler").style.height = 1+"px";
		set_opacity(document.getElementById("btn_annuler"), 0);
		element("zone_reponse").style.paddingBottom = 10+"px";
		element("btn_activer_notifications").style.height = 1+"px";
		set_opacity(document.getElementById("btn_activer_notifications"), 0);
		REPONSE_EN_COURS=false;*/
	}
	else{
		CKEDITOR.instances["nouveau_commentaire_"+no].destroy();
		//element("nouveau_commentaire_"+no).parentNode.parentNode.getElementsByTagName("span")[0].style.display = "inline";
		element("nouveau_commentaire_"+no).parentNode.previousSibling.style.display = "inline-block";
		element("nouveau_commentaire_"+no).parentNode.parentNode.removeChild(element("nouveau_commentaire_"+no).parentNode);
	}
}

function textareaToCK(id){
	var reg_height = /height\[([0-9]+(?:px|%))\]/gi;
		var height = reg_height.exec(element(id).className);
	var param_ck = {language:'fr',uiColor: '#EDEDED'};
	if(height!=null&&height.length==2)
		param_ck["height"] = height[1];
	CKEDITOR.replace(id,param_ck);
}

function afficher_image(img){
	var contenu = document.createElement("div");
	var div = document.createElement("div");
		div.style.maxWidth = 100+"%";
	var img_taille_reelle = document.createElement("img");
		img_taille_reelle.src = img.src;
		img_taille_reelle.style.width = 100+"%";
	div.appendChild(img_taille_reelle);
	contenu.appendChild(div);
	colorbox(contenu);
}

function espace_personnel_aller_a_etape(action){
	var form = element("formulaire_espace_personnel_etape");
	form.action = action;
	return espace_personnel_etape_suivante(form);
}

function espace_personnel_etape_suivante(form,params_supplementaire){
	if(typeof(history.pushState)!="undefined"){ //Système compatible
		console.log("111111111111111111111111111111111111111111111111111");
		//PAGE_COURANTE = "recherche";
		if(typeof(params_supplementaire)=="undefined")
			params_supplementaire = "";
		if(enregistrer(params_supplementaire)){ //On enregistre les informations de la page courante (et si tout va bien on charge la page suivante)
			console.log("222222222222222222222222222222222222222222222222222");
			history.pushState({ path: this.path }, '', form.action);
			charger_page();
			console.log("3333333333333333333333333333333333333333333333333333");
		}
		console.log("4444444444444444444444444444444444444444444444444444");
		return false;
		console.log("5555555555555555555555555555555555555555555555555");
	}
	else{ //Forumlaire non compatible AJAX (on bloque si l'utilisateur ne peut pas enregistrer)
		return peut_enregistrer();
	}
}
/*
function espace_personnel_etape_suivante(form,params_supplementaire){
	if(typeof(history.pushState)!="undefined"){ //Système compatible
		//PAGE_COURANTE = "recherche";
		if(typeof(params_supplementaire)=="undefined")
			params_supplementaire = "";
		if(enregistrer(params_supplementaire)){ //On enregistre les informations de la page courante (et si tout va bien on charge la page suivante)
			console.log("222222222222222222222222222222222222222222222222222");
			history.pushState({ path: this.path }, '', form.action);
			charger_page();
			console.log("3333333333333333333333333333333333333333333333333333");
		}
	}
	else{ //Lien normal
		if(peut_enregistrer())
			form.submit();
	}
}*/

function peut_enregistrer(){
	//TODO
	return true;
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
				params += ((params!="")?"&":"")+inputs[i].name.replace("BDD","")+"="+((inputs[i].value!=inputs[i].title)?encodeURIComponent_vrai(inputs[i].value):'');
				if(inputs[i].name.replace("BDD","")=="pseudo"&&inputs[i].value!=""&&inputs[i].value!=inputs[i].title)
					pseudo_modifie = inputs[i].value;
			}
			else
				params += ((params!="")?"&":"")+inputs[i].name.replace("BDD","")+"="+((inputs[i].checked)?1:0);
		}
	}
	for(var i=0;i<selects.length;i++){
		if(selects[i].id.substring(0,3)=="BDD")
			params += ((params!="")?"&":"")+selects[i].name.replace("BDD","")+"="+((selects[i].value!=selects[i].title)?encodeURIComponent_vrai(selects[i].value):'');
	}
        var presence = 0;
	for(var i=0;i<textareas.length;i++) {
            if(!dans_tab("editeur",textareas[i].className.split(" "))&&textareas[i].id.substring(0,3)=="BDD") {
                    params += ((params!="")?"&":"")+textareas[i].name.replace("BDD","")+"="+((textareas[i].value!=textareas[i].title)?encodeURIComponent_vrai(textareas[i].value):'');
            }
	}
	//On récupère les textes longs
	for(var instanceName in CKEDITOR.instances){
	   params += "&"+CKEDITOR.instances[instanceName].name.replace("BDD","")+"="+encodeURIComponent_vrai(CKEDITOR.instances[instanceName].getData());
           if (CKEDITOR.instances[instanceName].getData().indexOf('@') != -1) {
                presence = 1;
            }
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
	
        if (presence == 0) {
	xhr = getXhr();
	xhr.open("POST", "03_ajax/creationModification.php", false);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xhr.send(params);
	var reponse = eval("("+xhr.responseText+")");
	filtre(false);
	if(analyser_ajax(reponse)){
		//message(reponse[1]);
		if(pseudo_modifie!=false){
			vide(element("span_pseudo"));
			element("span_pseudo").appendChild(document.createTextNode(pseudo_modifie));
		}
		return true;
	}
	else
		return false;
        }
        else {
            $('#body_mod_infos').html("Vous devez saisir les adresse emails des contacts dans les champs prévus à cet effet aux étapes suivantes et non dans les descriptifs. Merci de corriger pour pouvoir passer à l'étape suivante.");
            $('#modal_infos').modal(); return false;
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
			//TODO On ouvre la fenetre de connexion avec le paramètre "reload" à false
		}
		else if(ajax[1]=="[DROIT]"){
			message("Vous n'avez pas les autorisations nescessaires ...");
			//TODO redirection vers l'accueil
		}
		else{
			message(ajax[1]);
		}
		return false;
	}
}
//Fonction pour les autoprez
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

var ROTATION_CHARGEMENT = new Array();
function rotation_chargement(id){
	if(typeof(id)=="undefined")
		var id = "filtre_chargement";
	if(element(id).firstChild.className!="rotate")
		element(id).firstChild.className = "rotate";
	else
		element(id).firstChild.className = "";
	ROTATION_CHARGEMENT[id] = setTimeout("rotation_chargement('"+id+"')",600);
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

function creer_nouvelle_fiche(form){
	if(typeof(history.pushState)!="undefined"){ //Système compatible
		if(typeof(e)!="undefined")
			e.preventDefault();
		//PAGE_COURANTE = el.id.split("_")[1];
		history.pushState({ path: this.path }, '', form.action);
		charger_page(element("input_titre_recherche").name+"="+element("input_titre_recherche").value);
		return false;
	}
	else{ //Lien normal
		return true;
	}
}

function img_error(img){
	img.src = "img/logo-ensembleici_fb.jpg";
	img.onerror = null;
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

function favori_coupdecoeur(btn,no,type){
	if(typeof(btn)=="string"){
		btn = element(btn);
		btn.id = null;	
	}
	if(btn.className.indexOf("coupdecoeur")>-1){ //COUP DE COEUR
		var xhr = getXhr();
			xhr.open("POST", "03_ajax/favori_coupdecoeur.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("no="+no+"&type="+type+"&action=coupdecoeur");
		var coupdecoeur = eval("("+xhr.responseText+")");
		if(analyser_ajax(coupdecoeur)){
			if(coupdecoeur[1]=="actif"){
				if(btn.className.indexOf("actif")==-1){
					btn.className = btn.className+" actif";
					if(btn.firstChild==null){
						btn.appendChild(document.createElement("div"));
						btn.firstChild.appendChild(document.createTextNode("1"));
					}
					else{
						var nb = parseInt(btn.firstChild.firstChild.data)+1;
						btn.firstChild.firstChild.data = nb;
					}
				}
			}
			else{
				if(btn.className.indexOf("actif")>0){
					btn.className = btn.className.replace(" actif","");
					var nb = parseInt(btn.firstChild.firstChild.data)-1;
					if(nb>0)
						btn.firstChild.firstChild.data = nb;
					else
						btn.removeChild(btn.firstChild);
				}
			}
		}
	}
	else if(btn.className.indexOf("favoris")>-1){ //FAVORI
		if(est_connecte()){
			var xhr = getXhr();
				xhr.open("POST", "03_ajax/favori_coupdecoeur.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send("no="+no+"&type="+type+"&action=favori");
			var favori = eval("("+xhr.responseText+")");
			if(analyser_ajax(favori)){
				if(favori[1]=="actif"){
					if(btn.className.indexOf("actif")==-1)
						btn.className = btn.className+" actif";
				}
				else{
					if(btn.className.indexOf("actif")>0)
						btn.className = btn.className.replace(" actif","");
				}
			}
		}
		else{ //On appelle la fenêtre de connexion qui rappellera cette fonction
			btn.id = "bouton_favori_connexion";
			fenetre_connexion(false,encodeURIComponent_vrai("favori_coupdecoeur('"+btn.id+"',"+no+",'"+type+"')"));
		}
	}
	else{ //NOTIFICATIONS
		if(est_connecte()){
			var xhr = getXhr();
				xhr.open("POST", "03_ajax/favori_coupdecoeur.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send("no="+no+"&type="+type+"&action=notification");
			var notification = eval("("+xhr.responseText+")");
			if(analyser_ajax(notification)){
				if(notification[1]=="actif"){
					if(btn.className.indexOf("actif")==-1)
						btn.className = btn.className+" actif";
				}
				else{
					if(btn.className.indexOf("actif")>0)
						btn.className = btn.className.replace(" actif","");
				}
			}
		}
		else{ //On appelle la fenêtre de connexion qui rappellera cette fonction
			btn.id = "bouton_notification_connexion";
			fenetre_connexion(false,encodeURIComponent_vrai("favori_coupdecoeur('"+btn.id+"',"+no+",'"+type+"')"));
		}
	}
}

function encodeURIComponent_vrai(s){ //BANDE DE FILS DE PUTE REMPLACEZ TOUT SAUF LES SIMPLE QUOTE!
	return encodeURIComponent(s).replace(/'/g, "%27");
}

