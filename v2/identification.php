<?php
require_once ('01_include/_var_ensemble.php');
require('01_include/_js_active.php');

if(isset($msg_err)) echo "<div id=\"erreur\">$msg_err</div>";

?>
          <script type="text/javascript">
            $(document).ready(function(){
              $(".ajax").colorbox({
            		width:"550px",
            		onComplete : function() {
		              $(this).colorbox.resize();

		var cache = {};
		// Autocomplete
		$("#cp, #ville").autocomplete({
		  source: function (request, response)
		  {
		    //Si la réponse est dans le cache
		    if (('FR' + '-' + request.term) in cache)
		    {
		      response($.map(cache['FR' + '-' + request.term], function (item)
		      {
	
			return {
			  label: item.CP + ", " + item.VILLE,
			  value: function ()
			  {
			    if ($(this).attr('id') == 'cp')
			    {
			      $('#ville').val(item.VILLE);
			      return item.CP;
			    }
			    else
			    {
			      $('#cp').val(item.CP);
			      return item.VILLE;
			    }
			  }
			}
		      }));
		    }
		    //Sinon -> Requete Ajax
		    else
		    {
		      var objData = {};
		      if ($(this.element).attr('id') == 'cp')
		      {
			objData = { codePostal: request.term, pays: 'FR', maxRows: 10 };
		      }
		      else
		      {
			objData = { ville: request.term, pays: 'FR', maxRows: 10 };
		      }
		      $.ajax({
			url: "01_include/AutoCompletion.php",
			dataType: "json",
			data: objData,
			type: 'POST',
			success: function (data)
			{
			  //Ajout de reponse dans le cache
			  cache[('FR' + '-' + request.term)] = data;
			  response($.map(data, function (item)
			  {
	
			    return {
			      label: item.CP + ", " + item.VILLE,
			      value: function ()
			      {
				if ($(this).attr('id') == 'cp')
				{
				  $('#ville').val(item.VILLE);
				  return item.CP;
				}
				else
				{
				  $('#cp').val(item.CP);
				  return item.VILLE;
				}
			      }
			    }
			  }));
			}
		      });
		    }
		  },
		  minLength: 3,
		  delay: 600
		});

		            }
	            });
            });
          </script>

        <div id="intro_tt">

          <p>L’espace personnel permet de faciliter votre navigation sur le site « Ensemble ici » en mémorisant votre situation géographique.</p>

<p>Notre système d'ajout / modification de contenus nécessite la connexion à un espace personnel pour sécuriser et faciliter la gestion de vos informations.</p>
	</div>
          <div id="choix-login">
            
            <div class="bloc-blc">
                <p>Vous avez d&eacute;j&agrave; <br/>un espace personnel <br/>sur <span class="ei">"Ensemble Ici"</span></p>
		<div class="actions">
                  <a href="connexion_ajax.php?url=<?php echo $urlpage ?>" title="Connexion à votre espace personnel" class="boutonbleu ico-login infobulle-b ajax">Connexion</a><br/><br/>
                <a class="oubli ajax" href="ajax_oublie_mdp.php">Vous avez oubli&eacute; votre mot de passe ?</a>
                </div>
            </div>
            
            <div class="bloc-grs">
                <p>Vous n'avez pas encore<br/> d'espace personnel <br/>sur <span class="ei">"Ensemble Ici"</span> ?</p>
		<div class="actions">
                    <a href="inscription_ajax.php" title="Créez votre espace personnel" class="boutonbleu infobulle-b ico-fleche ajax">Cr&eacute;ez un compte</a>
		</div>
                <p class="note">(cr&eacute;ation en moins de 2 minutes)</p>
            </div>
            
            <div class="clear"></div>
          </div>