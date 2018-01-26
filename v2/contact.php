<?php
	session_name("EspacePerso");
	session_start();
	// include header
	$titre_page = "Contactez l'équipe Ensemble ici";
	$meta_description = "Contactez l'équipe du site Ensemble Ici. Ensemble ici : Tous acteurs de la vie locale.";

	$ajout_header = <<<AJHE
    <script type="text/javascript">

		$(document).ready(function() {
		    $('#formpublic').on('submit', function() {
		 
	            $.ajax({
	                url: "01_include/contact-envoi.php",
	                type: $(this).attr('method'),
	                data: $(this).serialize(),
	                dataType: 'json',
	                success: function(json) {
						if (json.reponse =='true') {
						  $("#formulaire").hide(400);
						  $("#msg").show(400);
						  $("#loader").hide();
						  $("#msg").html("Merci de votre message. Nous vous contacterons tr&egrave;s prochainement.");
						} else {
						  $("#msg").html(json.reponse);
						  $("#loader").hide();
						  $("#msg").show(400); 
						}

	                }
	            });
		        return false;
		    });
		});

    </script>
AJHE;
	include ('01_include/structure_header.php');

?>
      <div id="colonne2">

<h1>Contactez-nous</h1>

<p>Notre équipe se tient disponible pour répondre à vos questions d’ordre technique sur le site « Ensemble ici », vos idées de services utiles, vos remarques sur la présentation du site,...<br/><br/>

Nous sommes, bien sûr, ouverts à toute proposition de partenariat ou de projet dans une perspective de mutualisation et de développement local.  </p>
<br/>
<h2>Coordonnées</h2>
<div class="bloc-grs">
    <br/><p><strong>Association DECOR</strong><br/> 
    Grande Rue<br/>
    26110 Les Pilles</p>
    <p>04 75 27 74 80</p>   
</div>
<div class="bloc-blc">
<br/><br/><br/>
L’<strong>association DECOR</strong> fait partie du collectif « Ensemble ici ». Elle porte administrativement et techniquement le projet.<br/>
</div>

<div class="clear"></div>
<br/>    
<h2>Formulaire de contact <span id="loader"><img src="img/image-loader.gif" alt="Chargement en cours..." height="24" width="24"></span></h2>

		<div id="msg">
		</div>
<div id="form">
		<div id="formulaire">
		  <form method="post" id="formpublic" action="" class="formA">
			<ul>
			    <li><label for="nom">Nom : <sup>*</sup></label>
			    <input maxlength="100" size="38" name="nom" id="nom" class="input"/></li>	
			    <li><label for="email">Email : <sup>*</sup> </label>		
			    <input maxlength="100" size="38" name="email" id="email" class="input"/></li>
			    <li><label for="telephone">Téléphone :</label>		
			    <input maxlength="14" name="telephone" id="telephone" class="input"/></li>	
			    <li><label for="adresse">Adresse :</label>		
			    <input maxlength="200" name="adresse" id="adresse" size="38" class="input"/></li>		
			</ul>
			<ul>
			     <li><label for="message">Message : <sup>*</sup> </label>
				<textarea name="message" id="messageta" cols="45" rows="7"></textarea></li>
			</ul>
		
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