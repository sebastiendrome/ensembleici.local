function getElementsByClassName(el,c){
	if(typeof(document.getElementsByClassName)!="undefined")
		return el.getElementsByClassName(c);
	else{
		var selectAll = document.querySelectorAll("."+c);
		//On s'assure maintenant que chacun d'entre eux est dans el
		//alert(document);
		var retour = new Array();
		for(var i=0;i<selectAll.length;i++){
			if(dans_element(selectAll[i],el)){
				retour[retour.length] = selectAll[i];
			}
		}
		return retour;
	}
}
