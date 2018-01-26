function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre à  jour");xhr=false;}return xhr;}
function vide(e){if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}

function jsAjouterCommentaire(cible){	

	var titre = document.getElementById('titre_message').value;	
	var texte = document.getElementById('description').value;
	var no_utilisateur = document.getElementById('no_utilisateur').value;
	var no_sujet = document.getElementById('no_sujet').value;
		
	if(titre != "" && texte != "" && texte != "...Comentaires ici..."){			
		params="titre="+escape(titre)+"&texte="+escape(texte)+"&no_utilisateur="+escape(no_utilisateur)+"&no_sujet="+escape(no_sujet)+"";
		var xhr = getXhr();
			xhr.onreadystatechange = function(){
				if(xhr.readyState<4){							
				}
				else if(xhr.readyState == 4 && xhr.status == 200){			
					jsAfficherCommentaire(no_sujet,cible);
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

function jsAfficherCommentaire(no_sujet,cible){		

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

function jsVerifCommentaire(){	
	var titre = document.getElementById('titre_message').value;	
	var texte = document.getElementById('description').value;
	
	var reponse = false;
	if(titre != "" && texte != "" && texte != "...Comentaires ici..." ){		
		reponse = true;
	}
	else{
		alert('Il faut remplir tous les champs correctement');
		reponse = false;
	}
	return reponse;
}