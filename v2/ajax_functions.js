// function jsChangerEtat(idboutton,no_pa){



function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre à  jour");xhr=false;}return xhr;}
function vide(e){if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}

function modif_etat(id){
	
	var etat = document.getElementById(id).getAttribute("rel");
	alert(etat);
	var action = "act";	
	if (etat==1) { action = "desact";}
	var xhr = getXhr();	
		xhr.onreadystatechange = function(){
			// if(xhr.readyState<4){
				// if(!eval("("+xhr.responseText+")"))
					// {alert("not working");}
			// }
			if(xhr.readyState == 4 && xhr.status == 200){
				var reponse = xhr.responseText;
				var cible = document.getElementById(id);
				if (cible != null){
					var action_after = "activer";
					if (etat==1){action_after="désactiver";}
					cible.firstChild.data = action_after;
					
					var etat_after=1;
					if(etat==1){etat_after=0;}
					
				//	document.getElementById(id).setAttribute(nombre, valor)
				//	cible.rel = etat_after; //?
												// style opacity
	//				cible.style.opacity 							
												//alert(id);
											//	alert(reponse);
											document.getElementById(id).setAttribute("rel",etat);
								
				}	
			}
		};
		// xhr.open("POST", "01_include/espace_active_petiteannonce.php", true);
		xhr.open("GET", "01_include/espace_active_petiteannonce.php?id="+id+"&action="+action, true);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		// xhr.send("id="+id+"&action="+action);	
		xhr.send(null);	
	return false;	
}		

		


/*
$('.activdesactiv').click(function() {

	var no_pa = $(this).attr('id');
	var etat = $(this).attr('rel');
	var action = "";

	if(etat==1){
		action = "desact";
	}
	else {action = "act";}
	
    param = "id="+no_pa+"&action="+action;

	$.ajax({
		url: "01_include/espace_active_petiteannonce.php",
		dataType: "json",
		data: param,
		type: 'GET',
		success: function (data)
		{
			// tester le retour de data
			// Change le texte du bouton
			if (etat==1){
				etat=0;
				$("#"+no_pa).html('activer');
				$("#"+no_pa).fadeTo( "fast", 0.33 );
			}
			else{
				etat=1;
				$("#"+no_pa).html('désactiver');
				$("#"+no_pa).fadeTo( "fast", 1 );
			}	
			
			$("#"+no_pa).attr('rel', etat);
			
			
			
		//	$("#"+no_pa).html(action+"iver").
	//		$(this).fadeTo( "fast", 0.33 ).fadeTo( "fast", 1 );
		//	$(this).parent(".une-pa").fadeTo( "slow", 0.33 ); // A modifier

		},
		error: function(){
		    alert("Erreur de traitement.");
		}
	});

	return false;
});
*/