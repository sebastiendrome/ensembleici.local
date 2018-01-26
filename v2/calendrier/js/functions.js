function getXhr(){if(window.XMLHttpRequest) xhr=new XMLHttpRequest();else if(window.ActiveXObject){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest, veuillez le mettre à  jour");xhr=false;}return xhr;}
function vide(e){if(e.firstChild){e.removeChild(e.firstChild);vide(e);}}

function Borrar(element,valor){
if(element.value==valor)
	{
		element.value="";
	}
}

function Escribir(element,valor){
	if(element.value=="")
	{
		element.value=valor;
	}
}

function activerDesactiverDisplay(active,inactive){
	//activa el primer elemento y desactiva el segundo, display:none
	document.getElementById(inactive).style.display="none";
	document.getElementById(active).style.display="inline";	
}

function changerdisplay(attribut,tableau){
	//establece el parametro en los elementos pasados en el array	
	//var array1 = eval("("+unescape(tableau)+")");
	    for (x=0;x<tableau.length;x++){
			document.getElementById(tableau[x]).style.display=attribut;	
		}
}