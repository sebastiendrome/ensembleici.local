/************************************************************************************************************
	Utilisation:
		on appelle la fonction message avec comme paramètres:
			txt_html : le texte (les balises HTML sont acceptée.
			params : un tableau contenant tous les paramètres, tel que la taille, la position, ou la div parent
			
	Note:
		params peut être null
		dans ces cas là, on affiche le message avant de le faire disparaitre automatiquement au bout de "seconde_defaut" secondes
		params peut aussi être égale à un entier, dans ce cas là il représente le nombre de secondes avant d'effacer le message.
		
		params: il peuvent être dans n'importe quel sens:
			array("parent"->[chaine|element],"pos_x"->[réel],"pos_y"->[réel],"hauteur"->[réel],"largeur"->[réel],"seconde"->[int],"boutons"->[array],"overflow"->[string])
				boutons: array(array('btn1','action_btn1'),array('btn1','action_btn2'))
*************************************************************************************************************/

/*******************************************************
Fonctionnalité possibles a ajouter:
	choix du position de la div par rapport à la souris (angle bas gauche, angle haut gauche, angle bas droit, angle bas gauche)
	Vérification débordement (si la position left ou top aditionné à la hauteur ou la largeur dépasse la hauteur ou la largeur de l'écran, on décale)
	Ajout d'un filtre (ex: paramètre "filtre": true) qui permettrait de mettre un fond noir sur la page pour bloquer l'utilisateur en attendant le click
*******************************************************/

/***************
Exemple d'appel:
	message("ceci est un texte <span style='font-weight:bold'>HTML</span>");
		On affiche le message dans une div centrée sur la page de 400px de large, pour 2,5 secondes
	message("ceci est un texte <span style='font-weight:bold'>HTML</span>",4);
		On affiche le message dans une div centrée sur la page de 400px de large, pour 4 secondes
	message("ceci est un texte <span style='font-weight:bold'>HTML</span>",4,event);
		On affiche le message dans une div placée à la position de la souris de 400px de large, pour 4 secondes
	
	message("ceci est un texte <span style='font-weight:bold'>HTML</span>",{'parent':document.getElementById('div_parent'), 'seconde' : 5 },event);
	message("ceci est un texte <span style='font-weight:bold'>HTML</span>",{'parent':document.getElementById('div_parent'), 'bouton' : new Array(new Array('btn1','action_btn1()'),new Array('btn2','action_btn2()')) });
	message("ceci est un texte <span style='font-weight:bold'>HTML</span>",{'parent':document.getElementById('div_parent'), 'bouton' : new Array(new Array('btn1','action_btn1()'),new Array('btn2','action_btn2()')) },event);
	message("ceci est un texte <span style='font-weight:bold'>HTML</span>",{'parent':document.getElementById('div_parent'), 'bouton' : new Array(new Array('btn1','action_btn1()'),new Array('btn2','action_btn2()')), 'pos_x' : 10, 'pos_y' : 10 },event);

	note: l'action "supprime_msg" permet de fermer le message si elle est mise dans action_btn
*****/

var bordure_taille = 15;
var angle_image = "angle3.png";
var bord_image = "bord3.png";
var fond_image = "fond3.png";
var position_script_dossier_images = "message/";

var largeur_defaut = 200; //en pixels
var overflow_defaut = "auto";
var align_defaut = "center";
var pos_x_defaut = "centre"; //au centre de la page
var pos_y_defaut = "centre"; //au centre de la page
var temps_affichage_defaut = 2.5; //en secondes

var id_message = "message_"; //message_1, message_2, ...
var nb_message = 0;
var z_index = 1000; //z-index min
var couleur_texte = "#4C4C4C";

//Pour des boutons plus sympas:
var bord_bouton = "bord_btn.png";
var bord_bouton_over = "bord_btn_over.png";
var fond_bouton = "fond_btn.png";
var fond_bouton_over = "fond_btn_over.png";
var hauteur_bouton = 28;
var taille_police_bouton = 14;

var OPACIFICATION_MSG = new Array();
var TRANSPARENCE_MSG = new Array();

function message(txt_html,params,event){
	/*
	Cette fonction est généralement appellée
	*/
	//Seul txt_html est renseigné.
		//Pas de boutons: on affiche le message un certain temps.
	if(typeof(params)=="undefined"){
		//par défaut : 2.5 secondes
		//On créait un div témoins pour calculer cette taille
		var div = document.createElement("div");
			div.style.width = largeur_defaut+"px";
			div.style.visibility = "hidden";
			div.innerHTML = txt_html;
			document.body.appendChild(div);
		var hauteur = div.offsetHeight;
		document.body.removeChild(div);
		var id=id_message+nb_message;nb_message++;
		var pos_x = document.body.offsetWidth/2-largeur_defaut/2;
		var pos_y = document.body.offsetHeight/2-hauteur/2;
		message_temp(txt_html,id,document.body,pos_x,pos_y,hauteur,largeur_defaut,overflow_defaut,align_defaut,temps_affichage_defaut);
	}
	else if(typeof(params)=="number"){
		//l'utilisateur à choisit un nombre de secondes.
		
		var div = document.createElement("div");
			div.style.width = largeur_defaut+"px";
			div.style.visibility = "hidden";
			div.innerHTML = txt_html;
			document.body.appendChild(div);
		var hauteur = div.offsetHeight;
		
		if(typeof(event)=="undefined"){
			var pos_x = document.body.offsetWidth/2-largeur_defaut/2;
			var pos_y = document.body.offsetHeight/2-hauteur/2;
		}
		else{
			var pos_x = event.clientX+15;
			var pos_y = event.clientY+15+((document.documentElement && document.documentElement.scrollTop) || window.pageYOffset || self.pageYOffset || document.body.scrollTop);
			// alert((document.documentElement && document.documentElement.scrollTop) || window.pageYOffset || self.pageYOffset || document.body.scrollTop);
		}
		document.body.removeChild(div);
		var id=id_message+nb_message;nb_message++;
		message_temp(txt_html,id,document.body,pos_x,pos_y,hauteur,largeur_defaut,overflow_defaut,align_defaut,params);
	}
	//Sinon, il y a d'autres paramètres
	else{
		var parent = "";
		var pos_x = "";
		var pos_y = "";
		var hauteur = "";
		var largeur = "";
		var seconde = "";
		var boutons = false;
		var overflow = "";
		//On récupère le parent
		if(params["parent"]!=null){
			parent = params["parent"];
			if(typeof(parent)=="string"){
				parent = document.getElementById(parent);
			}
		}
		else{
			parent = document.body;
		}
		
		//On récupère les dimmensions
		if(params["largeur"]!=null)
			largeur = params["largeur"];
		else
			largeur = largeur_defaut;
		if(params["hauteur"]!=null)
			hauteur = params["hauteur"];
		else{ //Sinon la hauteur doit s'adapter à la taille du contenu
			//On créait un div témoins pour calculer cette taille
			var div = document.createElement("div");
				div.style.width = largeur+"px";
				div.style.visibility = "hidden";
				div.innerHTML = txt_html;
				document.body.appendChild(div);
			hauteur = div.offsetHeight;
			document.body.removeChild(div);
		}
		
		//Si il y a event
		if(typeof(event)!="undefined"){
			//On place alors le message là où se situe la souris
			pos_x = event.clientX;
			pos_y = event.clientY+((document.documentElement && document.documentElement.scrollTop) || window.pageYOffset || self.pageYOffset || document.body.scrollTop);
		}
		
		//On récupère les positions
		if(typeof(params["pos_x"])!="undefined"){
			if(pos_x!="") //On a déjà déterminé la position de la souris
				pos_x = pos_x+params["pos_x"];
			else
				pos_x = params["pos_x"];
		}
		else{
			if(pos_x==""){ //On a pas déterminé la position : on centre le message
				if(pos_x_defaut=="centre")
					pos_x = parent.offsetWidth/2-largeur/2;
			}
		}
		if(typeof(params["pos_y"])!="undefined"){
			if(pos_y!="") //On a déjà déterminé la position de la souris
				pos_y = pos_y+params["pos_y"];
			else
				pos_y = params["pos_y"];
		}
		else{
			if(pos_y==""){ //On a pas déterminé la position : on centre le message
				if(pos_y_defaut=="centre")
					pos_y = parent.offsetHeight/2-hauteur/2;
			}
		}

		//On récupère le nombre de secondes
		if(typeof(params["seconde"])!="undefined"){
			seconde = params["seconde"];
		}
		if(typeof(params["secondes"])!="undefined"){
			seconde = params["secondes"];
		}
		
		//On récupère les boutons
		if(typeof(params["boutons"])!="undefined"){
			boutons = params["boutons"];
		}
		if(typeof(params["bouton"])!="undefined"){
			boutons = params["bouton"];
		}
		
		//On récupère l'overflow
		if(typeof(params["overflow"])!="undefined"){
			overflow = params["overflow"];
		}
		else{
			overflow = overflow_defaut;
		}
		
		//On récupère l'alignement
		if(typeof(params["align"])!="undefined"){
			align = params["align"];
		}
		else{
			align = align_defaut;
		}
		var id=id_message+nb_message;nb_message++;
		// alert(txt_html+" :: "+parent+" :: "+pos_x+" :: "+pos_y+" :: "+hauteur+" :: "+largeur+" :: "+overflow+" :: "+boutons);
		if(seconde!="")
			message_temp(txt_html,id,parent,pos_x,pos_y,hauteur,largeur,overflow,align,seconde);
		else{
			creer_message(txt_html,id,parent,pos_x,pos_y,hauteur,largeur,overflow,align,boutons);			
		}
	}
}

function message_temp(txt_html,id,parent,pos_x,pos_y,hauteur,largeur,overflow,align,secondes){
	/*
	Cette fonction est appellée si l'utilisateur choisit le setTimeout aux boutons
	*/
	var ms = secondes*1000;
	creer_message(txt_html,id,parent,pos_x,pos_y,hauteur,largeur,overflow,align,false);
	setTimeout("supprime_msg('"+id+"')",ms);
}
	function supprime_msg(id){
		if(OPACIFICATION_MSG[id]!=false){
			clearTimeout(OPACIFICATION_MSG[id]);
			OPACIFICATION_MSG[id] = false;
		}
		desopacifie_msg(id,0);
	}

function creer_message(txt_html,id,parent,pos_x,pos_y,hauteur,largeur,overflow,align,boutons){
	OPACIFICATION_MSG[id] = false;
	if(TRANSPARENCE_MSG[id]!=false){
		clearTimeout(TRANSPARENCE_MSG[id]);
		TRANSPARENCE_MSG[id] = false;
	}
	var div_message = document.createElement("div");
		div_message.style.opacity = 0;
		div_message.style.zIndex = z_index;z_index++;
		div_message.style.overflow = "visible";
		div_message.setAttribute("id",id);
		div_message.style.width = largeur+"px";
		div_message.style.height = hauteur+"px";
		div_message.style.position = "absolute";
		div_message.style.top = pos_y+"px";
		div_message.style.left = pos_x+"px";
	var message = document.createElement("div");
		message.style.position = "absolute";
		message.style.textAlign = align;
		message.style.top = 0+"px";
		message.style.left = 0+"px";
		message.style.backgroundImage = "url("+position_script_dossier_images+"images/"+fond_image+")";
		message.style.color = couleur_texte;
		message.style.width = 100+"%";
		message.style.height = 100+"%";
		message.style.position = "absolute";
		message.style.overflow = overflow;
		message.innerHTML = txt_html;
	div_message.appendChild(message);
	parent.appendChild(div_message);
	
	//On s'occupe maintenant des boutons
	if(boutons!=false){
		div_message.style.top = div_message.offsetTop - 20 + "px";
		div_message.style.height = div_message.offsetHeight + 40 + "px";
		var nb_bouton = boutons.length;
		var table = document.createElement("table");
			table.style.width = 100+"%";
			message.appendChild(table);
		var tr = document.createElement("tr");
			table.appendChild(tr);
		for(i=0;i<boutons.length;i++){
			var td = document.createElement("td");
				td.style.textAlign = "center";
				tr.appendChild(td);
			var un_bouton = document.createElement("div");
				un_bouton.style.display = "inline-block";
				un_bouton.style.height = hauteur_bouton+"px";
				un_bouton.style.cursor = "pointer";
				un_bouton.style.fontSize = taille_police_bouton+"px";
				var g_un_bouton = document.createElement("div");
					g_un_bouton.style.width = 5+"px";
					g_un_bouton.style.height = hauteur_bouton+"px";
					g_un_bouton.style.display = "inline-block";
					g_un_bouton.style.cssFloat = "left";
					g_un_bouton.style.backgroundImage = "url("+position_script_dossier_images+"images/"+bord_bouton+")";
				var c_un_bouton = document.createElement("div");
					c_un_bouton.style.paddingTop = (hauteur_bouton-taille_police_bouton)/2;
					c_un_bouton.style.height = hauteur_bouton-((hauteur_bouton-taille_police_bouton)/2)+"px";
					c_un_bouton.style.display = "inline-block";
					c_un_bouton.style.cssFloat = "left";
					c_un_bouton.style.backgroundImage = "url("+position_script_dossier_images+"images/"+fond_bouton+")";
				var d_un_bouton = document.createElement("div");
					d_un_bouton.style.width = 5+"px";
					d_un_bouton.style.height = hauteur_bouton+"px";
					d_un_bouton.style.display = "inline-block";
					d_un_bouton.style.cssFloat = "left";
					d_un_bouton.style.backgroundImage = "url("+position_script_dossier_images+"images/php_rotation_image.php?img="+bord_bouton+"&deg=180)";
					c_un_bouton.appendChild(document.createTextNode(boutons[i][0]));
				un_bouton.appendChild(g_un_bouton);
				un_bouton.appendChild(c_un_bouton);
				un_bouton.appendChild(d_un_bouton);
				un_bouton.onmouseover = function(){bouton_over(this);};
				un_bouton.onmouseout = function(){bouton_out(this);};
			/*
			var un_bouton = document.createElement("input");
				un_bouton.setAttribute("type","button");
				un_bouton.setAttribute("value",boutons[i][0]);*/
				var les_fonctions = boutons[i][1].split(";");
				var le_onclick = "";
			
				for(j=0;j<les_fonctions.length;j++){
					if(les_fonctions[j]!="supprime_msg")
						le_onclick += les_fonctions[j]+";";
					else
						le_onclick += "supprime_msg('"+div_message.id+"');";
				}
				un_bouton.setAttribute("onclick",le_onclick);
				
			td.appendChild(un_bouton);
		}
	}
	else{ //Alors c'est un message temporaire
		div_message.setAttribute("onmouseover","msg_temp_over('"+div_message.id+"')");
		div_message.setAttribute("onmouseout","msg_temp_out('"+div_message.id+"')");
	}
	
	bordure_message(div_message);
	opacifie_msg(div_message.id,1);
}
	function msg_temp_over(id){
		set_opacity(id, 60);
	}
	function msg_temp_out(id){
		if(!TRANSPARENCE_MSG[id])
			set_opacity(id, 100);
	}

function bordure_message(div_message){
	var bordure_gauche = document.createElement("div");
		bordure_gauche.style.height = div_message.offsetHeight+"px";
		bordure_gauche.style.width = bordure_taille+"px";
		bordure_gauche.style.position = "absolute";
		bordure_gauche.style.top = 0+"px";
		bordure_gauche.style.left = -bordure_taille+"px";
		bordure_gauche.style.backgroundImage = "url('"+position_script_dossier_images+"images/"+bord_image+"')";
	div_message.appendChild(bordure_gauche);
	
	var bordure_droite = document.createElement("div");
		bordure_droite.style.height = div_message.offsetHeight+"px";
		bordure_droite.style.width = bordure_taille+"px";
		bordure_droite.style.position = "absolute";
		bordure_droite.style.top = 0+"px";
		bordure_droite.style.right = -bordure_taille+"px";
		bordure_droite.style.backgroundImage = "url("+position_script_dossier_images+"images/php_rotation_image.php?img="+bord_image+"&deg=180)";
	div_message.appendChild(bordure_droite);
	
	var bordure_bas = document.createElement("div");
		bordure_bas.style.height = bordure_taille+"px";
		bordure_bas.style.width = div_message.offsetWidth+"px";
		bordure_bas.style.position = "absolute";
		bordure_bas.style.bottom = -bordure_taille+"px";
		bordure_bas.style.right = 0+"px";
		bordure_bas.style.backgroundImage = "url("+position_script_dossier_images+"images/php_rotation_image.php?img="+bord_image+"&deg=90)";
	div_message.appendChild(bordure_bas);
	
	var bordure_haut = document.createElement("div");
		bordure_haut.style.height = bordure_taille+"px";
		bordure_haut.style.width = div_message.offsetWidth+"px";
		bordure_haut.style.position = "absolute";
		bordure_haut.style.top = -bordure_taille+"px";
		bordure_haut.style.right = 0+"px";
		bordure_haut.style.backgroundImage = "url("+position_script_dossier_images+"images/php_rotation_image.php?img="+bord_image+"&deg=270)";
	div_message.appendChild(bordure_haut);
	
	
	var angle_hg = document.createElement("div");
		angle_hg.style.height = bordure_taille+"px";
		angle_hg.style.width = bordure_taille+"px";
		angle_hg.style.position = "absolute";
		angle_hg.style.top = -bordure_taille+"px";
		angle_hg.style.left = -bordure_taille+"px";
		angle_hg.style.backgroundImage = "url('"+position_script_dossier_images+"images/"+angle_image+"')";
	div_message.appendChild(angle_hg);
	
	var angle_hd = document.createElement("div");
		angle_hd.style.height = bordure_taille+"px";
		angle_hd.style.width = bordure_taille+"px";
		angle_hd.style.position = "absolute";
		angle_hd.style.top = -bordure_taille+"px";
		angle_hd.style.right = -bordure_taille+"px";
		angle_hd.style.backgroundImage = "url('"+position_script_dossier_images+"images/php_rotation_image.php?img="+angle_image+"&deg=270')";
	div_message.appendChild(angle_hd);
	
	var angle_bd = document.createElement("div");
		angle_bd.style.height = bordure_taille+"px";
		angle_bd.style.width = bordure_taille+"px";
		angle_bd.style.position = "absolute";
		angle_bd.style.bottom = -bordure_taille+"px";
		angle_bd.style.right = -bordure_taille+"px";
		angle_bd.style.backgroundImage = "url('"+position_script_dossier_images+"images/php_rotation_image.php?img="+angle_image+"&deg=180')";
	div_message.appendChild(angle_bd);
	
	var angle_bg = document.createElement("div");
		angle_bg.style.height = bordure_taille+"px";
		angle_bg.style.width = bordure_taille+"px";
		angle_bg.style.position = "absolute";
		angle_bg.style.bottom = -bordure_taille+"px";
		angle_bg.style.left = -bordure_taille+"px";
		angle_bg.style.backgroundImage = "url('"+position_script_dossier_images+"images/php_rotation_image.php?img="+angle_image+"&deg=90')";
	div_message.appendChild(angle_bg);
	
}

function bouton_over(item){
	item.firstChild.style.backgroundImage = "url("+position_script_dossier_images+"images/"+bord_bouton_over+")";
	item.firstChild.nextSibling.style.backgroundImage = "url("+position_script_dossier_images+"images/"+fond_bouton_over+")";
	item.lastChild.style.backgroundImage = "url("+position_script_dossier_images+"images/php_rotation_image.php?img="+bord_bouton_over+"&deg=180)";
}

function bouton_out(item){
	item.firstChild.style.backgroundImage = "url("+position_script_dossier_images+"images/"+bord_bouton+")";
	item.firstChild.nextSibling.style.backgroundImage = "url("+position_script_dossier_images+"images/"+fond_bouton+")";
	item.lastChild.style.backgroundImage = "url("+position_script_dossier_images+"images/php_rotation_image.php?img="+bord_bouton+"&deg=180)";
}

function opacifie_msg(id,op_finale){
	var op_cour = parseFloat(document.getElementById(id).style.opacity);
	if(op_cour<op_finale){
		document.getElementById(id).style.opacity = op_cour+0.1;
		OPACIFICATION_MSG[id] = setTimeout("opacifie_msg('"+id+"',"+op_finale+")",50);
	}
	else{
		document.getElementById(id).style.opacity = op_finale;
		OPACIFICATION_MSG[id] = false;
	}
}
function desopacifie_msg(id,op_finale){
	var op_cour = parseInt(get_opacity(id));
	if(op_cour>op_finale){
		set_opacity(id, op_cour-10);
		TRANSPARENCE_MSG[id] = setTimeout("desopacifie_msg('"+id+"',"+op_finale+")",50);
	}
	else{
		set_opacity(id, op_finale);
		TRANSPARENCE_MSG[id] = false;
		if(op_finale==0){
			document.getElementById(id).parentNode.removeChild(document.getElementById(id));
		}
	}
}