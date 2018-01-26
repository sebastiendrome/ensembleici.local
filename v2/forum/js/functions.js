function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre à  jour");xhr=false;}return xhr;}
function vide(e){if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}

function addslashes (str) {
  return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
}

function Borrar(valor)
{
if(document.getElementById("description").value==valor)
	{
		document.getElementById("description").value="";
	}
}

function Escribir(valor)
{
	if(document.getElementById("description").value=="")
	{
		document.getElementById("description").value=valor;
	}
}

function effacer(valeur,id)
{
if(document.getElementById(id).value==valeur)
	{
		document.getElementById(id).value="";
	}
}

function saisir(valeur,id)
{
	if(document.getElementById(id).value=="")
	{
		document.getElementById(id).value=valeur;
	}
}

function jsVerifCommentaireCkeditor(){	
	var titre = document.getElementById('titre_message').value;	
	var texte = CKEDITOR.instances.description.getData();
	var no_utilisateur = document.getElementById('no_utilisateur').value;
	var utilisateur_connecte = document.getElementById('utilisateur_connecte').value;
	var pseudo = document.getElementById('pseudo').value;
	
	var reponse = false;
	if(pseudo != "" && titre != "" && texte != "" && texte != null && no_utilisateur != "" && utilisateur_connecte != null){		
		reponse = true;
	}
	else if(utilisateur_connecte == "" || utilisateur_connecte == null ){	
		alert('Il faut se connecter');
		reponse = false;
	}
	else{
		alert('Il faut remplir tous les champs correctement');
		reponse = false;
	}
	return reponse;
}

function jsVerifCommentaire(no){
	if(typeof(no)=="undefined")
		no ="";
	var titre = document.getElementById('titre_message'+no).value;		
	var nom_modif='descriptionReponse'+no;
	var texte = CKEDITOR.instances[nom_modif].getData();
	var utilisateur_connecte = document.getElementById('utilisateur_connecte').value;
	var pseudo = document.getElementById('pseudo'+no).value;
	
	var reponse = false;
	if(pseudo != "" && titre != "" && texte != "" && texte != "Ecrire ici ..." && utilisateur_connecte != "" && utilisateur_connecte != null){		
		reponse = true;
	}
	else if(utilisateur_connecte == "" || utilisateur_connecte == null ){	
		alert('Il faut se connecter');
		reponse = false;
	}
	else{
		alert('Il faut remplir tous les champs correctement');
		reponse = false;
	}
	return reponse;
}

function jsVerifModif(no){
	if(typeof(no)=="undefined")
		no ="";
	//var texte = document.getElementById('modifdescript'+no).value;	
	var pseudo = document.getElementById('modifpseudo'+no).value;
	var titre = document.getElementById('modiftitre'+no).value;
	var nom_modif='modifdescript'+no;
	var texte = CKEDITOR.instances[nom_modif].getData();
	
	var reponse = false;
	if(pseudo != "" && texte != "" && titre != ""){		
		reponse = true;
	}
	else{
		alert('Il faut remplir tous les champs correctement');
		reponse = false;
	}
	return reponse;
}

function jsVerifConnex(){	
	var login = document.getElementById('login').value;	
	var mdp = document.getElementById('mdp').value;

	var reponse = false;
	if(login != "" && mdp != "" ){		
		reponse = true;
	}
	else{
		alert('Il faut remplir tous les champs correctement');
		reponse = false;
	}
	return reponse;
}

function jsAjouterCommentaire(no_fichier, cible){	

	var titre = document.getElementById('titre').value;	
	var texte = document.getElementById('texte').value;
	var nom = document.getElementById('nom').value;
	var email = document.getElementById('email').value;			
	if(titre != "" && texte != "" && texte != "Ecrire ici ..." && nom != "" && email != "" && checkEmail(email)){			
		params="no_fichier="+no_fichier+"&titre="+escape(titre)+"&texte="+escape(texte)+"&nom="+escape(nom)+"&email="+escape(email)+"";
		var xhr = getXhr();
			xhr.onreadystatechange = function(){
				if(xhr.readyState<4){							
				}
				else if(xhr.readyState == 4 && xhr.status == 200){			
					jsAfficherCommentaire(no_fichier,cible);
				}
			}
		xhr.open("POST", "ajouterCommentaire.php", true);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send(params); 	
	}
	else{
		alert("Il faut remplir tous les champs correctement");
	}
}

function jsAfficherCommentaire(no,cible){		

	var destin = document.getElementById(''+cible+'');		
	
	var xhr = getXhr();	
		xhr.onreadystatechange = function(){
			if(xhr.readyState<4){
			}
			else if(xhr.readyState == 4 && xhr.status == 200){	
				var valeur = eval("("+xhr.responseText+")");	

				$("#accordion").append(valeur);
				$("#accordion").accordion();	
			}
		}
		xhr.open("POST", "afficherCommentaires.php", true);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("no="+no);			
}

function jsAfficherFormulaire_reponse(id_bouton, id_div_commentaires, no_message, no_utilisateur, titre_message, no_sujet){
	
	// alert(id_bouton+" : "+id_div_commentaires+" : "+no_message+" : "+no_utilisateur+" : "+titre_message+" : "+no_sujet);
	
	// alert(document.getElementById(id_div_commentaires));
	var bouton = document.getElementById(id_bouton);	
	var destin = document.getElementById(id_div_commentaires);
	var id_description = 'descriptionReponse'+no_message;
	// alert (id_description);
	// alert(bouton.value);
	if (bouton.value == "Répondre"){

		var formulaire = "<form method=POST action=\"transition.php?envoi=ok&messagemessage="+no_message+"&reponse=ok\" onSubmit=\"return jsVerifCommentaire('"+no_message+"')\">";	
			formulaire += "<input name=\"titre_message"+no_message+"\" type=\"hidden\" id=\"titre_message"+no_message+"\"  value=\""+decodeURIComponent(titre_message)+"\"/>";
			formulaire += "&nbsp;&nbsp; Auteur :<input name=\"pseudo"+no_message+"\" type=\"text\" id=\"pseudo"+decodeURIComponent(no_message)+"\" value=\"\"/> ";
			formulaire += "<br><br><textarea name=\""+id_description+"\" id=\""+id_description+"\" class=\"ckeditor\" rows=\"6\" cols=\"80\" ></textarea><br>";
			formulaire += "<input type=\"hidden\" name=\"no_utilisateur"+no_message+"\" id=\"no_utilisateur"+no_message+"\" value=\""+no_utilisateur+"\"/>";
			formulaire += "<input type=\"hidden\" name=\"no_sujet\" id=\"no_sujet\" value=\""+no_sujet+"\"/>";
			formulaire += "<input type=\"hidden\" name=\"utilisateur_connecte\" id=\"utilisateur_connecte\" value=\"\".$_SESSION['UserConnecte_id'].\"\"/>"; 
			formulaire += "&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"AjouterCommentaire\" class=\"boutonbleu ico-ajout\" value=\"Ajouter\" >";
			formulaire += "</form>";	

			destin.innerHTML = formulaire;			
			bouton.value = "Annuler";
			if(CKEDITOR.instances['descriptionReponse'+no_message]){
				CKEDITOR.instances['descriptionReponse'+no_message].destroy();
			}
			CKEDITOR.replace('descriptionReponse'+no_message,{toolbar:'AutoA',uiColor:'#F0EDEA',language:'fr',width:'680',height:'100',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
			// CKEDITOR.replace(id_description,{toolbar:'AutoA',uiColor:'#F0EDEA',language:'fr',width:'520',height:'200',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
	}
	else if (bouton.value == "Annuler"){
		CKEDITOR.instances['descriptionReponse'+no_message].destroy();
		destin.innerHTML = "";			
		bouton.value = "Répondre";	
	}
}

function jsAfficherFormulaire_modifs(id_bouton, id_div, no_message, pseudo, descript, titre_message){
	
	var bouton = document.getElementById(id_bouton);	
	var destin = document.getElementById(id_div);
		
	if (bouton.value == "Modifier"){

		var formulaire = "<form method=POST action=\"transition.php?modif=ok&nomodif="+no_message+"\" onSubmit=\"return jsVerifModif('"+no_message+"')\" >";		
			formulaire += "&nbsp;&nbsp; Auteur :<input name=\"modifpseudo"+no_message+"\" type=\"text\" id=\"modifpseudo"+no_message+"\" value=\""+decodeURIComponent(pseudo)+"\"/> ";
			formulaire += "<br><br><textarea name=\"modifdescript"+no_message+"\" id=\"modifdescript"+no_message+"\" class=\"ckeditor\" rows=\"6\" cols=\"74\" >"+descript+"</textarea><br>";		 
			formulaire += "<input name=\"modiftitre"+no_message+"\" type=\"hidden\" id=\"modiftitre"+no_message+"\"  value=\""+decodeURIComponent(titre_message)+"\"/> ";			
			formulaire += "&nbsp;&nbsp;&nbsp;<input name=\"ModifCommentaire\" type=\"submit\" class=\"boutonbleu ico-fleche\" value=\"Modifier\" >";
			formulaire += "</form>";	
				// descript.replace(/<br\s*[\/]?>/gi, "\n")
			destin.innerHTML = formulaire;			
			bouton.value = "Annuler";	
			CKEDITOR.replace('modifdescript'+no_message,{toolbar:'AutoA',uiColor:'#F0EDEA',language:'fr',width:'680',height:'100',skin:'kama',enterMode : CKEDITOR.ENTER_BR});			
	}
	else if (bouton.value == "Annuler"){
		CKEDITOR.instances['modifdescript'+no_message].destroy();
		destin.innerHTML = "";			
		bouton.value = "Modifier";	
	}
}

function jsAfficherFormulaire_modifsPrincipal(id_bouton, id_div, no_message, pseudo, descript, titre_message){
	
	var bouton = document.getElementById(id_bouton);	
	var destin = document.getElementById(id_div);
		
	if (bouton.value == "Modifier"){

		var formulaire = "<form method=POST action=\"transition.php?modif=ok&nomodif="+no_message+"\" onSubmit=\"return jsVerifModif('"+no_message+"')\" >";		
			formulaire += "&nbsp;&nbsp; Auteur :<input name=\"modifpseudo"+no_message+"\" type=\"text\" id=\"modifpseudo"+no_message+"\" value=\""+decodeURIComponent(pseudo)+"\"/> ";
			formulaire += "&nbsp;&nbsp; Titre :<input name=\"modiftitre"+no_message+"\" type=\"text\" id=\"modiftitre"+no_message+"\" value=\""+decodeURIComponent(titre_message)+"\"/> ";
			formulaire += "<br><br><textarea name=\"modifdescript"+no_message+"\" id=\"modifdescript"+no_message+"\" class=\"ckeditor\" >"+descript+"</textarea><br>";		 
			formulaire += "&nbsp;&nbsp;&nbsp;<input name=\"ModifCommentaire\" type=\"submit\" class=\"boutonbleu ico-fleche\"  value=\"Modifier\" >";
			formulaire += "</form>";
			
			destin.innerHTML = formulaire;			
			bouton.value = "Annuler";	
			CKEDITOR.replace('modifdescript'+no_message,{toolbar:'AutoA',uiColor:'#F0EDEA',language:'fr',width:'680',height:'100',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
			
	}
	else if (bouton.value == "Annuler"){	
		CKEDITOR.instances['modifdescript'+no_message].destroy();
		destin.innerHTML = "";			
		bouton.value = "Modifier";			
	}	
}

function jsInscriptionDesinscriptionDesAlerts(no_utilisateur, no_sujet, no_msg, optionChecked){			
	
	var xhr = getXhr();
		xhr.open("POST", "php/update_utilisateur_message.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("no_utilisateur="+no_utilisateur+"&no_ville="+no_sujet+"&no_msg="+no_msg+"&checked="+((optionChecked)?1:0));
		if(xhr.responseText==""){
			if(optionChecked){
				alert('Vous venez de vous inscrire à la liste de diffusion');					
			}
			else{
				alert("Vous n'êtes plus inscrit à la liste de diffusion");					
			}
		}
		else
			alert("Désolé mais une erreur est survenue ...");
}