function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre à  jour");xhr=false;}return xhr;}
function vide(e){if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}

function modif_etat(id){
	
	var cible = document.getElementById(id);
	var etat = cible.getAttribute("rel");
	alert(etat);
	var action = "act";	
	if (etat=="1") {action = "desact";}
	var xhr = getXhr();	
		xhr.onreadystatechange = function(){			
			if(xhr.readyState == 4 && xhr.status == 200){							
				if (cible != null){
					if (action=="act"){
						cible.firstChild.data = "Désactiver";
						cible.setAttribute("rel",1);
					}
					else{
						cible.firstChild.data = "Activer";	
						cible.setAttribute("rel",0);
					}		
				}	
			}
		};
		xhr.open("POST", "01_include/espace_active_petiteannonce.php", true);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("id="+id+"&action="+action);	
}		