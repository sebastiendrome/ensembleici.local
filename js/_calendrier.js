/*function onload(){
	var inputs=document.getElementsByTagName("input");
	for(var i=0;i<inputs.length;i++){
		if(inputs[i].className.indexOf("calendrier")>-1){
			_calendrier_init(inputs[i]);
		}
	}
}*/
function datesql(d){
	var reg_date = /[0-9]{2}\/[0-9]{2}\/[0-9]{4}/gi;
	if(reg_date.test(d)){
		var _d=d.split("/");
		return _d[2]+"-"+_d[1]+"-"+_d[0];
	}
	else{
		return false;
	}
}
function datefr(d){
	var reg_date = /[0-9]{4}-[0-9]{2}-[0-9]{2}/gi;
	if(reg_date.test(d)){
		var _d=d.split("-");
		return _d[2]+"/"+_d[1]+"/"+_d[0];
	}
	else{
		return false;
	}
}
function date_to_string(dateJs){
	var m = dateJs.getMonth()+1;
	var d = dateJs.getDate();
	if((""+m).length==1)
		m = "0"+m;
	if((""+d).length==1)
		d = "0"+d;
	return dateJs.getFullYear()+"-"+m+"-"+d;
}
function _calendrier_ouvrir(input){
	//on vérifie le format actuel (sécurité)
	_calendrier_verif_input(input);
	if(input.id==null||input.id=="")
		input.id = "_calendrier_input";
	var date = datesql(input.value);
	var _date = date.split("-");
	var mois = _date[1];
	var annee = _date[0];
		var annee_courante = new Date().getFullYear();
	var jour = _date[2];
	//On récupère le calendrier
	if(element("calendrier")==null){
		//On créait le calendrier s'il n'existe pas
		var calendrier = document.createElement("div");
			calendrier.id = "calendrier";
			if(input.className.indexOf("non_anterieur")>-1)
				calendrier.className = "non_anterieur";
			if(input.className.indexOf("agenda")>-1)
				calendrier.className += " agenda";
			else if(input.className.indexOf("editorial")>-1)
				calendrier.className += " editorial";
			else if(input.className.indexOf("structure")>-1)
				calendrier.className += " structure";
			else if(input.className.indexOf("petite-annonce")>-1)
				calendrier.className += " petite-annonce";
			else if(input.className.indexOf("forum")>-1)
				calendrier.className += " forum";
		document.body.appendChild(calendrier);
		var div_libelle_jour = document.createElement("div");
		var zone_jours = document.createElement("div");
		calendrier.appendChild(div_libelle_jour);
		calendrier.appendChild(zone_jours);
		//On créait la ligne des jours de la semaine, et la ligne permettant de changer de mois et d'anéne
		var sem = new Array("lun","mar","mer","jeu","ven","sam","dim");
		
		for(var s=0;s<sem.length;s++){
			var div_sem = document.createElement("div");
				div_sem.className = "jour semaine";
				div_sem.appendChild(document.createTextNode(sem[s]));
			div_libelle_jour.appendChild(div_sem);
		}
		
		/*var input_aujourdhui = document.createElement("input");
			input_aujourdhui.type="button";
			input_aujourdhui.value = "aujourd'hui";
		var input_valide = document.createElement("input");
			input_valide.type="button";
			input_valide.value = "Sélectionner";
		calendrier.appendChild(input_aujourdhui);
		calendrier.appendChild(input_valide);*/
		
		var div_mois = document.createElement("div");
			div_mois.className = "div_les_mois";
			div_mois.onclick = function(){ferme_mois();};
			var _div_mois = document.createElement("div");
				div_mois.appendChild(_div_mois);
			var tab_mois = new Array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","décembre");
			for(var m=0;m<tab_mois.length;m++){
				var mois_courant = (m+1);
				var div_un_mois = document.createElement("div");
					div_un_mois.className = "un_mois";
					div_un_mois.appendChild(document.createTextNode(tab_mois[m]));
					ajoute_evenement(div_un_mois,'click','console.log("oooooooooooooooo");_calendrier_modifier_mois_courant("'+input.id+'",'+mois_courant+');');
				_div_mois.appendChild(div_un_mois);
			}
		calendrier.appendChild(div_mois);
		
		var div_annee = document.createElement("div");
			div_annee.className = "div_les_annees";
			div_annee.onclick = function(){ferme_annee();};
			var _div_annee = document.createElement("div");
				div_annee.appendChild(_div_annee);
			var tab_annee = new Array();
			if(calendrier.className.indexOf("non_anterieur")>-1)
				a_retrait = 0;
			else
				a_retrait = -5;
			for(var a=0;a<12;a++){
				tab_annee[tab_annee.length] = parseInt(annee_courante)+a+a_retrait;
			}
			for(var a=0;a<tab_annee.length;a++){
				var div_une_annee = document.createElement("div");
					div_une_annee.className = "une_annee";
					div_une_annee.appendChild(document.createTextNode(tab_annee[a]));
					ajoute_evenement(div_une_annee,'click','_calendrier_modifier_annee_courante("'+input.id+'",'+tab_annee[a]+');');
				_div_annee.appendChild(div_une_annee);
			}
		calendrier.appendChild(div_annee);
	}
	else
		var calendrier = element("calendrier");
	//On récupère la position
	var pos = offsetAbs(input);
	calendrier.style.top = pos["top"]+largeur(input)/2-largeur(calendrier)/2+"px";
	calendrier.style.left = pos["left"]+largeur(input)/2-largeur(calendrier)/2+"px";
	
	_calendrier_creer_mois(date,input.id);
}
	function _calendrier_modifier_mois_courant(id_input,mois){
		//On récupère l'année en cours
		var annee = element("_calendrier_annee_courante").firstChild.data;
		if(mois<10)
			mois = "0"+mois;
		_calendrier_creer_mois(annee+'-'+mois+'-'+'-01',id_input);
	}
	function _calendrier_modifier_annee_courante(id_input,annee){
		var mois = element("_calendrier_mois_courant").firstChild.data;
			var tab_mois = new Array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","décembre");
		var mois = tab_index(mois,tab_mois)+1;
		if(mois>0){
			if(mois<10)
				mois = "0"+mois;
			_calendrier_creer_mois(annee+'-'+mois+'-'+'-01',id_input);
		}
	}


function _calendrier_creer_mois(date,id){
	element(id).value = datefr(date);
	var zone_jours = element("calendrier").firstChild.nextSibling;
	vide(zone_jours);
	if(element("calendrier").className.indexOf("non_anterieur")>-1)
		var non_anterieur = true;
	else
		var non_anterieur = false;
	var date_aujourdhui = date_to_string(new Date());
	var premier_du_mois = date.substring(0,8)+"01";
		var date_premier_du_mois = new Date(premier_du_mois);
		var date_premier_du_mois_string = date_to_string(date_premier_du_mois);
		var time_premier_du_mois = date_premier_du_mois.getTime();
	var jour_dernier_du_mois = 31;
	do{
		var dernier_du_mois = date.substring(0,8)+jour_dernier_du_mois;
		var date_dernier_du_mois = new Date(dernier_du_mois);
		jour_dernier_du_mois--; //On passe en général deux fois dans cette boucle un mois sur deux avec les exceptions 28/29 fevrier
		
	}while(isNaN(date_dernier_du_mois.getTime()));
		var date_dernier_du_mois_string = date_to_string(date_dernier_du_mois);
		var time_dernier_du_mois = date_dernier_du_mois.getTime();
		
	//1. On claclcul le premier lundi
	var jour_sem = date_premier_du_mois.getDay()-1;
		if(jour_sem==-1)jour_sem=6;
	var time_premier_lundi = time_premier_du_mois-(jour_sem*3600*24*1000);
	var date_premier_lundi = new Date(time_premier_lundi);
	//2. On calcul la date du dernier dimanche
	var jour_sem = date_dernier_du_mois.getDay()-1;
		if(jour_sem==-1)jour_sem=6;
	var time_dernier_dimanche = time_dernier_du_mois+((6-jour_sem)*3600*24*1000);
	var date_dernier_dimanche = new Date(time_dernier_dimanche);
	//3. On place maintenant les cases de date_premier_lundi à date_dernier_dimanche
	var time_courant = time_premier_lundi;
	while(time_courant<=time_dernier_dimanche){
		var date_courante = new Date(time_courant);
		var date_courante_string = date_to_string(date_courante);
		
		//Retour à la ligne le lundi (sauf le premier)
		if(time_courant>time_premier_lundi&&date_courante.getDay()==1)
			zone_jours.appendChild(document.createElement("br"));
		//On créait la case
		var div_jour = document.createElement("td");
			div_jour.appendChild(document.createTextNode(date_courante.getDate()));
			div_jour.className = "jour";
			//div_jour.id = date_courante_string;
		
		//On regarde si la case est cliquable ou pas
		var click = !(non_anterieur&&date_courante_string<date_aujourdhui);
		if(!click)
			div_jour.className += " disabled";
		//C'est aujourd'hui
		else if(date_aujourdhui==date_courante_string)
			div_jour.className += " aujourdhui";
		
		if(date_courante_string>=date_premier_du_mois_string&&date_courante_string<=date_dernier_du_mois_string){
			if(click)
				ajoute_evenement(div_jour,"click",'_calendrier_selectionner_jour(this,"'+date_courante_string+'","'+id+'")');
			if(date_courante_string==date)
				div_jour.className += " selectionne";
		}
		else{
			//div_jour.onclick = ajoute_evenement("click","creer_mois('"++"')");
			div_jour.className += " autre_mois";
			if(click)
				ajoute_evenement(div_jour,"click",'_calendrier_creer_mois("'+date_courante_string+'","'+id+'")');
		}
			
		zone_jours.appendChild(div_jour);
		time_courant += 3600*24*1000;
	}
	//On place maintenant le mois et l'année
	var tab_mois = new Array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","décembre");
	var _date = date.split("-");
		var annee = _date[0];
		var mois = _date[1];
	zone_jours.appendChild(document.createElement("br"));
	var div_mois = document.createElement("div");
		div_mois.className = "mois";
		div_mois.id = "_calendrier_mois_courant";
		div_mois.appendChild(document.createTextNode(tab_mois[(parseInt(mois)-1)]));
		div_mois.onclick = function(){ouvre_mois();}
	var div_annee = document.createElement("div");
		div_annee.className = "annee";	
		div_annee.id = "_calendrier_annee_courante";
		div_annee.appendChild(document.createTextNode(annee));
		div_annee.onclick = function(){ouvre_annee();}
	zone_jours.appendChild(div_mois);
	zone_jours.appendChild(div_annee);
}

function ouvre_mois(){
	var tab_mois = new Array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","décembre");
	
	element("calendrier").lastChild.previousSibling.className = "div_les_mois ouvert";
}
function ferme_mois(){
	element("calendrier").lastChild.previousSibling.className = "div_les_mois";
}

function ouvre_annee(){
	element("calendrier").lastChild.className = "div_les_annees ouvert";
}
function ferme_annee(){
	element("calendrier").lastChild.className = "div_les_annees";
}


function _calendrier_selectionner_jour(le_jour,date,id){
	var jours = element("calendrier").firstChild.nextSibling.childNodes;
	for(var i=0;i<jours.length;i++){
		if(jours[i].className.indexOf(" selectionne")>-1)
			jours[i].className = jours[i].className.replace(" selectionne","");
	}
	le_jour.className += " selectionne";
	element(id).value = datefr(date);
	//On ferme le calendrier
	if(element(id).className.indexOf("charger_page")>-1)
		change_date(element(id));
	_calendrier_quitter(id);
}
function _calendrier_quitter(id){
	if(element("calendrier")!=null)
		element("calendrier").parentNode.removeChild(element("calendrier"));
	if(id=="_calendrier_input"&&element(id)!=null)
		element(id).id="";
}


function _calendrier_init(input){
	//_calendrier_verif_input(input);
	input.onfocus = function(){input_focus(this);_calendrier_ouvrir(this)};
	//input.onblur = function(){input_blur(this);setTimeout('_calendrier_quitter("'+input.id+'")',400)};
}
function _calendrier_verif_input(input){
	//On vérifie le format de la date actuelle
	var date = datesql(input.value);
	var aujourdhui = date_to_string(new Date());
	if(!date){
		date = aujourdhui;
		input.value=datefr(date);
	}
	
	//Non antérieure
	if(input.className.indexOf("non_anterieur")>-1){
		if(date<aujourdhui){
			date = aujourdhui;
			input.value=datefr(date);
		}
	}
}
