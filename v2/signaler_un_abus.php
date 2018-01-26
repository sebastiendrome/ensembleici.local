<?php
	$titre_page = "Signaler un abus";
	$meta_description = "Signaler un abus sur Ensemble Ici. Ensemble ici : Tous acteurs de la vie locale.";
	$ajout_header = <<<AJHE
    <script type="text/javascript">
		function envoiform(nom,email,objet,message)
		{
		  $("#msg").hide(); // cacher erreurs
		  $("#loader").show();
			var OAjax;
			if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
			else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP'); 
			OAjax.open('POST',"01_include/contact-envoi.php",true);
			OAjax.onreadystatechange = function()
			{
				if (OAjax.readyState == 4 && OAjax.status==200)
				{
					if (document.getElementById) 
					{	
						if (OAjax.responseText =='true') {
						  $("#formulaire").hide(400);
						  $("#msg").show(400);
						  $("#loader").hide();
						  $("#msg").html("Merci de votre message. Nous vous contacterons tr&egrave;s prochainement.");
						} else {
						  $("#msg").html(OAjax.responseText);
						  $("#loader").hide();
						  $("#msg").show(400); 
						}
					}
				}
			}
			OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
			OAjax.send('nom='+nom+'&email='+email+'&objet='+objet+'&message='+message+'&type=abus');
		}
    </script>
AJHE;
	include ('01_include/structure_header.php');

?>
      <div id="colonne2">

    <h1>Signaler un abus <span id="loader"><img src="img/image-loader.gif" alt="Chargement en cours..." height="24" width="24"></span></h1>
    <p>Vous avez noté une page faisant appel à nos services, mais qui ne respecte pas nos conditions d'utilisation ? Vous pouvez nous en informer.</p>

		<div id="msg">
		</div>
<div id="form">
		<div id="formulaire">
		  <form method="post" id="formpublic" onsubmit="envoiform(this.nom.value,this.email.value,this.objet.value,this.message.value);return false;" action="" class="formA">
			<ul>
			    <li><label for="nom">Nom :</label>
			    <input maxlength="100" size="38" name="nom" class="input"/></li>	
			    <li><label for="email">Email : </label>		
			    <input maxlength="100" size="38" name="email"  class="input"/></li>
			    <li><label for="objet">Objet : </label>		
			    <select class="input" name="objet">
				<option value="Contenu contraire aux conditions d utilisation">Contenu contraire aux conditions d'utilisation</option>
				<option value="Spam">Spam</option>
				<option value="Fraude">Fraude</option>
				<option value="Contenu illegal">Contenu illégal</option>
				<option value="Autre">Autre</option>
			    </select></li>
			</ul>
			<ul>
			     <li><label for="message">Message : <sup>*</sup> </label>
				<textarea name="message" cols="45" rows="7"></textarea></li>
			</ul>
		
		    <input type="hidden" name="sent" value="yes">
		    <div class="boutons">
		      <input type="submit" value="Envoyer" name="envoi" class="boutonbleu ico-fleche" />
		    </div>
		    Les champs marqués <sup>*</sup> sont requis.
		      </form>
		</div>
	      </div>
    


      </div>
    
<?php
	// Colonne 3
	// $affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');
	
	// Footer
	include ('01_include/structure_footer.php');
?>