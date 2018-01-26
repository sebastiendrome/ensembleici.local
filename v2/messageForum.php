<?php
	// Affichage des pages villes
	session_name("EspacePerso");
	session_start();
	//if(!isset($_SESSION['date_pa']) || ($_SESSION['date_pa']=="")) $_SESSION['date_pa']=1;
	require ('01_include/_var_ensemble.php');
	require ('01_include/_connect.php');
	// Supprimer la ville enregistrée (supprimer le cookie)
	$changerville = intval($_GET["changerville"]);
	
	if ($changerville==1)setcookie("id_ville", "", time() - 365*24*3600,"/", null, false, true);
	// cookie expiré
	// Récupère la ville sélectionnée, depuis l'URL
	
	if (($_GET["id_ville"])&&(!empty($_GET["id_ville"])))
	{
		$id_ville = intval($_GET["id_ville"]);
		// Si lien du popup de choix de la ville
	
		if ((intval($_GET["choixville"]))&&($_GET["choixville"]==1))setcookie("id_ville", $id_ville, time() + 365*24*3600,"/", null, false, true);
	}

	// depuis le formulaire de recherche d'une ville (colorbox de l'index)
	elseif ((isset($_POST["rech_idville"]))&&(!empty($_POST["rech_idville"])))
	{
		$id_ville = intval($_POST["rech_idville"]);
	}

	// depuis le cookie
	elseif (($_COOKIE["id_ville"])&&(!empty($_COOKIE["id_ville"])))
	{
		$id_ville = intval($_COOKIE["id_ville"]);
	}

	// Sinon popup pour choisir la ville 
	if (((!$id_ville)||(empty($id_ville)))||($changerville))
	{
		$titre_ville = "Choisissez une ville...";

		$ajout_header = <<<AJHE
		<script type="text/javascript">
		  $(function() {
		    $.colorbox({
		      href:"ajax_choix_ville.php",
		      width:"550px",
		      onComplete : function() {
				$(this).colorbox.resize();
				$('#cboxOverlay').fadeOut(4000);
		      }
		    });
		  });
		</script>
AJHE;
		// On prend la ville par défaut (derrière la colorbox)
		$id_ville = $id_ville_defaut;
	}

	// Infos de la ville selectionnée
	if (!empty($id_ville))
	{
		$sql_ville="SELECT * FROM villes WHERE id = :idville";
		$res_ville = $connexion->prepare($sql_ville);
		$res_ville->execute(array(':idville'=>$id_ville));
		$tab_ville = $res_ville->fetch(PDO::FETCH_ASSOC);
		$titre_ville = $tab_ville["nom_ville_maj"];
		$cp_ville = $tab_ville["code_postal"];
		$titre_ville_url = $tab_ville["nom_ville_url"];
		$lat_ville = $tab_ville["latitude"];
		$lon_ville = $tab_ville["longitude"];
	}

	// Une vie sélectionnée ?
	if (($_GET["id_vie"])&&(!empty($_GET["id_vie"])))
	{
		$id_vie = intval($_GET["id_vie"]);
		// Récup nom pour titre
		$sql_v="SELECT * FROM vie WHERE no = :idvie";
		$res_v = $connexion->prepare($sql_v);
		$res_v->execute(array(':idvie'=>$id_vie));
		$tab_v = $res_v->fetch(PDO::FETCH_ASSOC);
		$titre_nomvie = $tab_v["libelle"];
		$nom_url_vie = $tab_v["libelle_url"];
	}
	else
	{
		$id_vie = 0;
	}

	// Un tag sélectionné ?
	if (($_GET["id_tag"])&&(!empty($_GET["id_tag"])))
	{
		$id_tag = intval($_GET["id_tag"]);
		$est_ss_tag = intval($_GET["ss_tag"]);

		// C'est un sous-tag ?
		if ($est_ss_tag)
		{
			// Récup nom pour titre
			$sql_t="SELECT I.libelle AS nom_vie, T.titre AS nom_tag
				FROM `sous_tag` T, `tag_sous_tag` A, vie_tag V, vie I
				WHERE T.no = :idtag
				AND T.no = A.no_sous_tag
				AND A.no_tag = V.no_tag
				AND V.no_vie = I.no";
			$res_t = $connexion->prepare($sql_t);
			$res_t->execute(array(':idtag'=>$id_tag));
			$tab_t = $res_t->fetch(PDO::FETCH_ASSOC);
			$titre_nomvie = $tab_t["nom_vie"];
			$titre_nomtag = $tab_t["nom_tag"];
		}
		else
		{
			// Récup nom pour titre
			$sql_t="SELECT I.libelle AS nom_vie, T.titre AS nom_tag
				FROM tag T, vie_tag V, vie I
				WHERE T.no = :idtag
				AND T.no = V.no_tag
				AND V.no_vie = I.no";
			$res_t = $connexion->prepare($sql_t);
			$res_t->execute(array(':idtag'=>$id_tag));
			$tab_t = $res_t->fetch(PDO::FETCH_ASSOC);
			$titre_nomvie = $tab_t["nom_vie"];
			$titre_nomtag = $tab_t["nom_tag"];			
		}
	}
	else
	{
		$id_tag = 0;
	}

	// Liens afficher tous les evenements / tout le répertoire (affichage spécifique)
	if ((intval($_GET["specif"]))&&($_GET["specif"]==1))
	{
		$aff_specifique = true;
		if ((intval($_GET["tous_evts"]))&&($_GET["tous_evts"]==1)) $tous_evts = true;
		if ((intval($_GET["tous_struct"]))&&($_GET["tous_struct"]==1)) $tous_struct = true;
		if ((intval($_GET["tous_pa"]))&&($_GET["tous_pa"]==1)) $tous_pa = true;
	}

	// Titre de la page
	/*
	if ($tous_evts)
		$titre_page = "Agenda de $titre_ville, $cp_ville (tous les évènements)";
	elseif ($tous_struct)
		$titre_page = "Répertoire de $titre_ville, $cp_ville (toutes les structures)";
	elseif ($tous_pa)
		$titre_page = "Petites annonces de $titre_ville, $cp_ville (toutes les petites annonces)";
	elseif ($id_vie)
		$titre_page = "$titre_nomvie, Agenda et répertoire de $titre_ville, $cp_ville";
	elseif ($id_tag)
		$titre_page = "$titre_nomtag ($titre_nomvie), Agenda et répertoire de $titre_ville, $cp_ville";
	else
	{
		// L'accueil de la ville
		$titre_page = "$titre_ville ($cp_ville) : Agenda et répertoire";
		$diaporama = true;
		$page_index = true;
		$affiche_articles = true;
	}*/
	
	$titre_page = "$titre_ville ($cp_ville) : ".$titre_forum;
	$messageForum = true;
	$affiche_articles = true;
	$affiche_publicites = true;
	
	/*******
		On récupère les informations propres au forum et à l'utilisateur.
		****/
	$est_abonne = false;
	$cocher_abonnement = true;
	if(est_connecte()){ //L'utilisateur 
		$requete_abonne = "SELECT inscrit FROM forum_inscription WHERE no_utilisateur=:no AND no_forum=:nof AND no_message=0";
		$res_abonne = $connexion->prepare($requete_abonne);
		$res_abonne->execute(array(":no"=>$_SESSION["UserConnecte_id"],":nof"=>$_GET["no"]));
		$tab_abonne = $res_abonne->fetchAll();
		if(count($tab_abonne)>0){
			if($tab_abonne[0]["inscrit"]==1)
				$est_abonne = true;
			else
				$cocher_abonnement = false;
		}
			
	}
	
	
	
	

	// include header de la page
	$titre_page_bleu = $titre_ville;
	$meta_description = $titre_page.". Agenda et répertoire de $titre_ville sur Ensemble ici : Tous acteurs de la vie locale";
	$page_ville = true;
	
	

	// Bouton modifier
	if ($page_index)
	{
	$ajout_header .= <<<AJHE
<link rel="stylesheet" href="css/homepage.css">
AJHE;
	}
	include ('01_include/structure_header.php');
	?>
	<link rel="stylesheet" type="text/css" href="css/messageForum.css" />
	<script type="text/javascript" src="js/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	var REPONSE_EN_COURS = false;
	var NO_FORUM = <?php echo $_GET["no"]; ?>;
	var NOTIFICATIONS_ACTIVES = <?php echo ($est_abonne)?'true':'false'; ?>;
	var NOTIFICATIONS_INSCRIPTION_AUTO = <?php echo ($cocher_abonnement)?'true':'false'; ?>;
	var TEXTE_HTML_MODIFICATION = new Array();
	
	function input_focus(input){
		if(input.value==input.title){
			input.value = "";
			input.className = input.className.replace(" vide","");
		}
		else{
			input.select();
		}
	}
	function input_blur(input){
		if(input.value==""){
			input.value = input.title;
			input.className = input.className+" vide";
		}
	}
	function textareaToCK(id){
		CKEDITOR.replace(id,{toolbar:'Auto',uiColor:'#F0EDEA',language:'fr',skin:'kama',enterMode : CKEDITOR.ENTER_BR, on : {instanceReady : function(){this.focus();}}});
		//document.getElementById(id).focus();
	}
	function affiche_btn_reponse(){
		document.getElementById("btn_reponse").style.height = 30+"px";
		set_opacity(document.getElementById("btn_reponse"), 100);
		
		document.getElementById("btn_annuler").style.height = 30+"px";
		set_opacity(document.getElementById("btn_annuler"), 100);
		element("zone_reponse").style.paddingBottom = 40+"px";
		element("btn_activer_notifications").style.height = 20+"px";
		set_opacity(document.getElementById("btn_activer_notifications"), 100);
		/*alert(document.body);
		if(document.body)
			
		else*/;
	}
	
	//document.body.onscroll = function(){scrolling();};
	
	function scrolling_page(){
		if(!REPONSE_EN_COURS){
			var t_css = (!isNaN(parseInt(element("zone_reponse").style.top)))?parseInt(element("zone_reponse").style.top):0;
			var t_defaut = haut("zone_reponse")+haut("contenu")-t_css;
			var scroll = (document.documentElement)?document.documentElement.scrollTop:document.body.scrollTop;
			if(scroll>t_defaut){
				element("zone_reponse").style.top = (scroll-t_defaut)+"px";
			}
			else
				element("zone_reponse").style.top = 0+"px";
		}
	}
	
	function ouvrir_repondre(btn,input){
		btn.style.display="none";
		input.style.display="block";
		REPONSE_EN_COURS=true;
		//input_focus(input);
		//if(typeof(CKEDITOR.instances["reponse_forum"])=="undefined")
		textareaToCK('reponse_forum');
		affiche_btn_reponse();
		(document.documentElement)?document.documentElement.scrollTop=325:document.body.scrollTop=325;
		element("zone_reponse").style.top = 0+"px";
		if(NOTIFICATIONS_ACTIVES){
			element("input_forum_notif").checked = true;
		}
		else{
			if(NOTIFICATIONS_INSCRIPTION_AUTO){
				element("input_forum_notif").checked = true;
			}
			else{
				element("input_forum_notif").checked = false;
			}
		}
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
	
	function repondre(no,com){
		if(typeof(no)=="undefined")
			no = 0;
		if(typeof(com)=="undefined")
			com = false;
		/*alert(no);
		alert(com);*/
		//On vérifie que les conditions sont bien remplies pour écrire.
		var poster_message = false;
		if(!est_connecte()){
			//On ouvre alors la colorbox de connexion.
			//$.colorbox({href:"connexion_ajax.php?forum=1&no="+no+"&com="+(com?"true":"false")});
			$.colorbox({href:"connexion_ajax.php?forum=1&retour=placer_boutons();repondre("+no+","+((com)?"true":"false")+")"});
		}
		else{
			//2. On s'assure que l'utilisateur a un pseudo.
			var pseudo = recuperer_pseudo();
			if(!pseudo){ //On ouvre alors la colorbox de création de pseudo
				$.colorbox({href:"choixpseudo_ajax.php?forum=1&retour=repondre("+no+","+((com)?"true":"false")+")"});
			}
			else{
				poster_message = true;
			}
		}
			
		if(poster_message){
			//L'utilisateur est connecté, et il a un pseudo.
			if(com){ //L'utilisateur commente un message
				var contenu = CKEDITOR.instances["reponseCommentaire_"+no].getData();
			}
			else{ //l'utilisateur répond au sujet
				var contenu = CKEDITOR.instances.reponse_forum.getData();
			}
			//On envoi à la bdd le message/commentaire.
			var param = "no_forum="+NO_FORUM+"&no_message="+no+"&contenu="+encodeURIComponent(contenu);
			var xhr = getXhr();
				xhr.open("POST", "03_ajax/post_message_forum.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send(param);
			var reponse = eval("("+xhr.responseText+")");
			if(reponse!=false){
			
				var date = reponse["date_modification"];
				var no_message = reponse["no_message"];
				var numero_utilisateur = reponse["no_utilisateur"];
			
				if(com){
					CKEDITOR.instances["reponseCommentaire_"+no].setData("");
					fermer_repondre(no);
					var z = element("commentaire_"+no);
					var div_commentaire = document.createElement("div");
						div_commentaire.className = "un_commentaire";
						div_commentaire.id = "unCommentaire_"+no_message+"_"+numero_utilisateur;
						
						var div_boutons = document.createElement("div");
							div_boutons.className = "bouton_admin_proprietaire";
							var img_edit = document.createElement("img");
								img_edit.id = "edit_message_"+no_message;
								ajoute_evenement(img_edit,"click","ouvre_edit_message("+no_message+",'unCommentaire',"+numero_utilisateur+")");
								img_edit.src = "02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=FFD500";
								img_edit.onmouseover = function(){this.src='02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=e19f00';};
								img_edit.onmouseout = function(){this.src='02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=FFD500';};
							
							var img_delete = document.createElement("img");
								ajoute_evenement(img_delete,"click","supprimer_message("+no_message+",'unCommentaire',"+numero_utilisateur+")");
								img_delete.src = "02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=FEBDBD";
								img_delete.onmouseover = function(){this.src='02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=241,63,65';};
								img_delete.onmouseout = function(){this.src='02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=FEBDBD';};
							
							div_boutons.appendChild(img_edit);
							div_boutons.appendChild(img_delete);
							
					var div_contenu = document.createElement("div");
						div_contenu.className = "contenu_unCommentaire";
						div_commentaire.appendChild(div_contenu);
						div_contenu.innerHTML = contenu;
					var div_signature = document.createElement("div");
						div_signature.className = "signature_commentaire";
						var span_pseudo = document.createElement("span");
							span_pseudo.className = "pseudo";
							span_pseudo.appendChild(document.createTextNode(pseudo));
						div_signature.appendChild(span_pseudo);
						div_signature.appendChild(document.createTextNode(" le "+date));
					div_commentaire.appendChild(div_signature);
					div_commentaire.appendChild(div_boutons);
					if(z.getElementsByClassName("un_commentaire").length>0)
						z.insertBefore(div_commentaire,z.getElementsByClassName("un_commentaire")[0]);
					else
						z.appendChild(div_commentaire);
				}
				else{
					CKEDITOR.instances.reponse_forum.setData("");
					fermer_repondre();
					
					//Integration graphique
					var z = element("zone_messages");
					var nouveau_msg = document.createElement("div");
						nouveau_msg.className = "un_message";
						nouveau_msg.id = "message_"+no_message+"_"+numero_utilisateur;
				
					var infos = document.createElement("div");
						infos.className = "information_message";
						var span = document.createElement("span");
							span.className = "pseudo";
							span.appendChild(document.createTextNode(pseudo));
							//a.href = "profil.php?no="+numero_utilisateur;
						infos.appendChild(span);
						infos.appendChild(document.createTextNode(" le "+date));
						var div_boutons = document.createElement("div");
							div_boutons.className = "bouton_admin_proprietaire";
							var btn_edit = document.createElement("img");
								btn_edit.onmouseover = function(){this.src="02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=e19f00";};
								btn_edit.onmouseout = function(){this.src="02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=FFD500";};
								btn_edit.id = "edit_message_"+no_message;
								ajoute_evenement(btn_edit,"click","ouvre_edit_message("+no_message+",'message',"+numero_utilisateur+")");
								btn_edit.src = "02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=FFD500";
							var btn_delete = document.createElement("img");
								btn_delete.onmouseover = function(){this.src="02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=241,63,65";};
								btn_delete.onmouseout = function(){this.src="02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=FEBDBD";};
								btn_delete.src = "02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=FEBDBD";
								ajoute_evenement(btn_delete,"click","supprimer_message("+no_message+",'message',"+numero_utilisateur+")");
							div_boutons.appendChild(btn_edit);
							div_boutons.appendChild(btn_delete);
							infos.appendChild(div_boutons);
							
					var contenus = document.createElement("div");
						contenus.className = "contenu_message";
						contenus.innerHTML = contenu;
					nouveau_msg.appendChild(infos);
					nouveau_msg.appendChild(contenus);
					var commentaires = document.createElement("div");
						commentaires.className = "commentaires";
						commentaires.id = "commentaire_"+no_message;
						var span = document.createElement("span");
							span.className = "lien_commentaire";
							span.appendChild(document.createTextNode("ajouter un commentaire"));
							span.onclick = function(){affiche_div_commentaire(this);};
						commentaires.appendChild(span);
					/*set_opacity(nouveau_msg,0);
					set_opacity(commentaires,0);*/
					z.insertBefore(commentaires,z.firstChild);
					z.insertBefore(nouveau_msg,commentaires);
					
					(document.documentElement)?document.documentElement.scrollTop=325:document.body.scrollTop=325;
					if(element("input_forum_notif").checked)
						activer_notifications(true);
					else{
						if(NOTIFICATIONS_ACTIVES){
							activer_notifications(false);
						}
					}
						
				}
			}
		}
	}
	function est_connecte(){
		var xhr = getXhr();
			xhr.open("POST", "03_ajax/est_connecte.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send(null);
		return eval("("+xhr.responseText+")");
	}
	function recuperer_pseudo(){
		var xhr = getXhr();
			xhr.open("POST", "01_include/pseudo_utilisateur.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send(null);
		return (xhr.responseText!="")?xhr.responseText:false;
	}
	
	var CLIGNOTTEMENT_NOTIFICATION = false;
	var ROTATION_CLOCHE = false;
	function clignotter_notifications(passage){
		if(typeof(passage)=="undefined"){
			passage = 0;
			if(CLIGNOTTEMENT_NOTIFICATION!=false){
				clearTimeout(CLIGNOTTEMENT_NOTIFICATION);
				CLIGNOTTEMENT_NOTIFICATION = false;
			}
			if(ROTATION_CLOCHE!=false){
				clearTimeout(ROTATION_CLOCHE);
				ROTATION_CLOCHE = false;
			}
		}
		if(passage==0){
			element("div_notifications_forum").style.backgroundColor = "rgba(21,170,158,1)";
			CLIGNOTTEMENT_NOTIFICATION = setTimeout('clignotter_notifications(1)',700);
			rotation_cloche(11);
		}
		else if(passage==1){
			element("div_notifications_forum").style.backgroundColor = "rgba(21,170,158,0)";
			CLIGNOTTEMENT_NOTIFICATION = setTimeout('clignotter_notifications(2)',700);
		}
		else if(passage==2){
			element("div_notifications_forum").style.backgroundColor = "rgba(21,170,158,1)";
			CLIGNOTTEMENT_NOTIFICATION = setTimeout('clignotter_notifications(3)',700);
		}
		else if(passage==3){
			element("div_notifications_forum").style.backgroundColor = "rgba(21,170,158,0)";
			CLIGNOTTEMENT_NOTIFICATION = setTimeout('clignotter_notifications(4)',700);
		}
		else if(passage==4){
			element("div_notifications_forum").style.backgroundColor = "rgba(21,170,158,1)";
			CLIGNOTTEMENT_NOTIFICATION = setTimeout('clignotter_notifications(5)',700);
		}
		else if(passage==5){
			element("div_notifications_forum").style.backgroundColor = "rgba(21,170,158,0)";
			CLIGNOTTEMENT_NOTIFICATION = false;
		}
	}
	
	function rotation_cloche(num_max,num,sens){
		if(typeof(num)=="undefined")
			var num = 1;
		if(typeof(sens)=="undefined")
			var sens = "30deg";
		var img = element("div_notifications_forum").getElementsByTagName("img")[0];
		if(num<=num_max){
			apply_transform(img,"rotate("+sens+")");
			if(sens=="30deg")
				img.style.backgroundColor = "rgba(255,255,255,0.6)";
			else
				img.style.backgroundColor = "rgba(255,255,255,0.9)";
			num++;
			sens = (sens=="30deg")?"-30deg":"30deg";
			ROTATION_CLOCHE = setTimeout('rotation_cloche('+num_max+','+num+',"'+sens+'")',300);
		}
		else{
			apply_transform(img,"rotate(0deg)");
			img.style.backgroundColor = "rgba(255,255,255,0.9)";
			ROTATION_CLOCHE = false;
		}
	}
	
	function apply_transform(el,transform){
		el.style.webkitTransform = transform;
		el.style.MozTransform = transform;
		el.style.msTransform = transform;
		el.style.OTransform = transform;
		el.style.transform = transform;
	}
	
	function activer_notifications(activ){
		if(est_connecte()){
			var img = element("div_notifications_forum").getElementsByTagName("img")[0];
			if(!activ){
				notifications = 0;
				src = "02_medias/01_interface/ico_bellcroix.png";
				if(CLIGNOTTEMENT_NOTIFICATION!=false){
					clearTimeout(CLIGNOTTEMENT_NOTIFICATION);
					element("div_notifications_forum").style.backgroundColor = "rgba(21,170,158,0)";
					CLIGNOTTEMENT_NOTIFICATION = false;
					CLIGNOTTEMENT_NOTIFICATION = false;
				}
				if(ROTATION_CLOCHE!=false){
					clearTimeout(ROTATION_CLOCHE);
					apply_transform(img,"rotate(0deg)");
					img.style.backgroundColor = "rgba(255,255,255,0.9)";
					ROTATION_CLOCHE = false;
				}
			}
			else{
				clignotter_notifications();
				notifications = 1;
				src = "02_medias/01_interface/img_colorize.php?uri=ico_bell.png&c=15AA9E";
			}
			//On appelle la fonction xhr qui modifie l'inscription au forum.
			var param = "no_forum="+NO_FORUM+"&no_message=0&inscrit="+notifications;
			var xhr = getXhr();
				xhr.open("POST", "03_ajax/update_inscription_forum.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send(param);
			img.src = src;
			NOTIFICATIONS_ACTIVES = activ;
			if(NOTIFICATIONS_ACTIVES&&!element("input_forum_notif").checked)
				element("input_forum_notif").checked = true;
			else{
				if(!NOTIFICATIONS_ACTIVES&&element("input_forum_notif").checked)
					element("input_forum_notif").checked = false;
			}
		}
		else{
			$.colorbox({href:"connexion_ajax.php?forum=1&retour=placer_boutons();activer_notifications("+((activ)?"true":"false")+")"});
		}
	}
	
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
			btn_annuler.className = "boutonbleu ico-fleche";
			btn_annuler.style.cssFloat = "left";
			ajoute_evenement(btn_annuler,"click",'fermer_repondre('+no_message+')');
		var btn_valider = document.createElement("input");
			btn_valider.type = "button";
			btn_valider.value = "Commenter";
			btn_valider.className = "boutonbleu ico-fleche";
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
	function ouvre_edit_message(no,mes_ou_com,no_usr){
		ajoute_evenement(element("edit_message_"+no),"click",'annule_edit_message('+no+',"'+mes_ou_com+'",'+no_usr+')');
		var message = element(mes_ou_com+"_"+no+"_"+no_usr).getElementsByClassName("contenu_"+mes_ou_com)[0];
		var message_html = message.innerHTML;
			vide(message);
		TEXTE_HTML_MODIFICATION[no] = message_html;
		
		var textarea = document.createElement("textarea");
			textarea.id = "updateMessage_"+no;
			textarea.style.width = 100+"%";
			textarea.value = message_html
		var btn_annuler = document.createElement("input");
			btn_annuler.type = "button";
			btn_annuler.value = "Annuler";
			btn_annuler.className = "boutonbleu ico-fleche";
			btn_annuler.style.cssFloat = "left";
			ajoute_evenement(btn_annuler,"click",'annule_edit_message('+no+',"'+mes_ou_com+'",'+no_usr+')');
		var btn_valider = document.createElement("input");
			btn_valider.type = "button";
			btn_valider.value = "Modifier";
			btn_valider.className = "boutonbleu ico-fleche";
			btn_valider.style.cssFloat = "right";
			ajoute_evenement(btn_valider,"click",'confirm_edit_message('+no+',"'+mes_ou_com+'",'+no_usr+')');
		message.appendChild(textarea);
		message.appendChild(btn_annuler);
		message.appendChild(btn_valider);
		message.style.paddingBottom = 30+"px";
		textareaToCK('updateMessage_'+no);
	}
	
	function annule_edit_message(no,mes_ou_com,no_usr){
		var message = element(mes_ou_com+"_"+no+"_"+no_usr).getElementsByClassName("contenu_"+mes_ou_com)[0];
		CKEDITOR.instances["updateMessage_"+no].destroy();
		vide(message);
		message.innerHTML = TEXTE_HTML_MODIFICATION[no];
		message.style.paddingBottom = 0+"px";
		ajoute_evenement(element("edit_message_"+no),"click",'ouvre_edit_message('+no+',"'+mes_ou_com+'",'+no_usr+')');
	}
	
	function confirm_edit_message(no,mes_ou_com,no_usr){
		var contenu = CKEDITOR.instances["updateMessage_"+no].getData();
		var param = "no="+no+"&contenu="+encodeURIComponent(contenu);
		var xhr = getXhr();
			xhr.open("POST", "03_ajax/update_message_forum.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send(param);
		var reponse = eval("("+xhr.responseText+")");
		if(reponse[0]){
			TEXTE_HTML_MODIFICATION[no] = contenu;
			annule_edit_message(no,mes_ou_com,no_usr);
		}
		else
			alert(reponse[1]);
	}
	
	function supprimer_message(no,mes_ou_com,no_usr){
		if(mes_ou_com=="unCommentaire")mes_ou_com = "commentaire";
		if(confirm("Souhaitez vous réellement supprimer ce "+mes_ou_com+" et son contenu?")){
			var param = "no="+no;
			var xhr = getXhr();
				xhr.open("POST", "03_ajax/delete_message_forum.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send(param);
			var reponse = eval("("+xhr.responseText+")");
			if(reponse[0]){
				if(mes_ou_com=="message"){
					element("message_"+no+"_"+no_usr).parentNode.removeChild(element("message_"+no+"_"+no_usr));
					element("commentaire_"+no).parentNode.removeChild(element("commentaire_"+no));
				}
				else
					element("unCommentaire_"+no+"_"+no_usr).parentNode.removeChild(element("unCommentaire_"+no+"_"+no_usr));
			}
			else
				alert(reponse[1]);
		}
	}
	
	function placer_boutons(){
		//On récupère le no_utilisateur courant.
		var xhr = getXhr();
			xhr.open("POST", "03_ajax/select_info_user.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send(null);
		var reponse = eval("("+xhr.responseText+")");
		if(reponse!=false){
			var no_user_connect = reponse["id"];
			var est_admin = reponse["admin"];
			//On parcours les messages
			var msg = element("zone_messages").getElementsByClassName("un_message");
			for(var indice_msg=0;indice_msg<msg.length;indice_msg++){
				var div_info = msg[indice_msg].getElementsByClassName("information_message")[0];
				var no_usr = msg[indice_msg].id.split("_")[2];
				var no_message = msg[indice_msg].id.split("_")[1];
				if(est_admin||no_usr==no_user_connect){
					var div_boutons = document.createElement("div");
						div_boutons.className = "bouton_admin_proprietaire";
						var btn_edit = document.createElement("img");
							btn_edit.onmouseover = function(){this.src="02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=e19f00";};
							btn_edit.onmouseout = function(){this.src="02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=FFD500";};
							btn_edit.id = "edit_message_"+no_message;
							ajoute_evenement(btn_edit,"click","ouvre_edit_message("+no_message+",'message')");
							btn_edit.src = "02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=FFD500";
						var btn_delete = document.createElement("img");
							btn_delete.onmouseover = function(){this.src="02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=241,63,65";};
							btn_delete.onmouseout = function(){this.src="02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=FEBDBD";};
							btn_delete.src = "02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=FEBDBD";
							ajoute_evenement(btn_delete,"click","supprimer_message("+no_message+",'message')");
						div_boutons.appendChild(btn_edit);
						div_boutons.appendChild(btn_delete);
					div_info.appendChild(div_boutons);
				}
			}
			//On parcours les commentaires.
			var com = element("zone_messages").getElementsByClassName("commentaires");
			for(var indice_com=0;indice_com<com.length;indice_com++){
				var div_com = com[indice_com].getElementsByClassName("un_commentaire");
				for(var indice_unCom=0;indice_unCom<div_com.length;indice_unCom++){
					var no_usr = div_com[indice_unCom].id.split("_")[2];
					var no_message = div_com[indice_unCom].id.split("_")[1];
					if(est_admin||no_usr==no_user_connect){
						var div_boutons = document.createElement("div");
							div_boutons.className = "bouton_admin_proprietaire";
							var img_edit = document.createElement("img");
								img_edit.id = "edit_message_"+no_message;
								ajoute_evenement(img_edit,"click","ouvre_edit_message("+no_message+",'unCommentaire')");
								img_edit.src = "02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=FFD500";
								img_edit.onmouseover = function(){this.src='02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=e19f00';};
								img_edit.onmouseout = function(){this.src='02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=FFD500';};
			
							var img_delete = document.createElement("img");
								ajoute_evenement(img_delete,"click","supprimer_message("+no_message+",'unCommentaire')");
								img_delete.src = "02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=FEBDBD";
								img_delete.onmouseover = function(){this.src='02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=241,63,65';};
								img_delete.onmouseout = function(){this.src='02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=FEBDBD';};
			
							div_boutons.appendChild(img_edit);
							div_boutons.appendChild(img_delete);
						div_com[indice_unCom].appendChild(div_boutons);
					}
				}
			}
		}
	}
	
	//width:'560',height:'200'
	</script>
	<div id="colonne2">
		<section><div class="blocB">
			<?php
				//$tous_forums = true;
				//include "01_include/affiche_forum.php";
				include "01_include/affiche_messageForum.php";
			?>
		</div></section>
	</div>
	<?php
	//Edito
		include "01_include/structure_colonne3.php";
	// Footer
		include ('01_include/structure_footer.php');
?>
