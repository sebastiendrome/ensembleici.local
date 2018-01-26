function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre à  jour");xhr=false;}return xhr;}
function vide(e){if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}

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

function jsVerifCommentaire(no=''){	
//alert (no);
	var titre = document.getElementById('titre_message'+no).value;	
	var texte = document.getElementById('description'+no).value;
	var utilisateur_connecte = document.getElementById('utilisateur_connecte').value;
	var pseudo = document.getElementById('pseudo'+no).value;
	alert(utilisateur_connecte);
	
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

function jsAfficherFormulaire_reponse(id_bouton, id_div_commentaires, no_message, no_utilisateur, titre_message){
	
	var bouton = document.getElementById(id_bouton);	
	var destin = document.getElementById(id_div_commentaires);
	var id_description = 'description'+no_message;
	// alert (id_description);
	
	if (bouton.value == "Répondre"){

		var formulaire = "<form method=POST action=\"commentaires.php?envoi=ok&messagemessage="+no_message+"\" onSubmit=\"return jsVerifCommentaire('"+no_message+"')\">";	
			formulaire += "<input name=\"titre_message"+no_message+"\" type=\"hidden\" id=\"titre_message"+no_message+"\"  value=\""+titre_message+"\"/>";
			formulaire += "&nbsp;&nbsp; Auteur :<input name=\"pseudo"+no_message+"\" type=\"text\" id=\"pseudo"+no_message+"\" value=\"\"/> ";
			formulaire += "<br><br><textarea class=\"ckeditor\" name=\""+id_description+"\" id=\""+id_description+"\" rows=\"6\" cols=\"80\" onfocus=\"effacer('Ecrire ici ...','"+id_description+"')\" onblur=\"saisir('Ecrire ici ...','"+id_description+"')\">Ecrire ici ...</textarea><br>";
			formulaire += "<input type=\"hidden\" name=\"no_utilisateur"+no_message+"\" id=\"no_utilisateur"+no_message+"\" value=\""+no_utilisateur+"\"/>";
			formulaire += "<input type=\"hidden\" name=\"no_sujet\" id=\"no_sujet\" value=\"\".$no_sujet.\"\"/>";
			formulaire += "<input type=\"hidden\" name=\"utilisateur_connecte\" id=\"utilisateur_connecte\" value=\"\".$_SESSION['UserConnecte_id'].\"\"/>"; 
			formulaire += "&nbsp;&nbsp;&nbsp;<input name=\"AjouterCommentaire\" type=\"submit\" value=\"Ajouter\" >";
			formulaire += "</form>";	

			destin.innerHTML = formulaire;			
			bouton.value = "Annuler";
			
			// CKEDITOR.replace(id_description,{toolbar:'AutoA',uiColor:'#F0EDEA',language:'fr',width:'520',height:'200',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
	}
	else if (bouton.value == "Annuler"){
	
		destin.innerHTML = "";			
		bouton.value = "Répondre";	
	}
}

