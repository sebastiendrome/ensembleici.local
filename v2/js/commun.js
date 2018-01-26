$(function(){
    // Infobulle
    $('.infobulle').poshytip({
	    showTimeout:0,
	    hideTimeout:0,
	    timeOnScreen:0,
	    className: 'infobulle-tip',
	    alignTo: 'target',
	    alignX: 'right',
	    alignY: 'top',
	    offsetX: -3,
	    offsetY: -44
    });
    // infobulle-bas
    $('.infobulle-b').poshytip({
	    showTimeout:0,
	    hideTimeout:0,
	    timeOnScreen:0,
	    className: 'infobulle-tip',
	    alignTo: 'target',
	    alignX: 'inner-left',
	    alignY: 'bottom',
	    offsetX: 10,
	    offsetY: 7
    });
    // infobulle-liste
    $('.infobulle-l').poshytip({
	    showTimeout:0,
	    hideTimeout:0,
	    timeOnScreen:0,
	    className: 'infobulle-tip',
	    alignTo: 'target',
	    alignX: 'inner-left',
	    alignY: 'bottom',
	    offsetX: 0,
	    offsetY: 7
    });
    // Recherche
    $("#form-recherche > #chaine").click(function () {
      if ($("#form-recherche > #chaine").val() == "Recherche") {
	$("#form-recherche > #chaine").val("");
      }
    });
    $("#form-recherche > #chaine").blur(function () {
      var lachaine = $("#form-recherche > #chaine").val();
      lachaine = lachaine.replace(/(^\s*)|(\s*$)/g,"");
      if (lachaine == "") {
	$("#form-recherche > #chaine").val("Recherche");
      }
    });
    $("#form-recherche").submit(function() {
      valid = true;
      var lachaine = $("#form-recherche > #chaine").val();
      lachaine = lachaine.replace(/(^\s*)|(\s*$)/g,"");
      if ((lachaine == "")||(lachaine == "Recherche")) {
	alert("Veuillez saisir un texte à rechercher.");
	valid = false;
      }
      return valid;
    });
       
});