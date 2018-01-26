<?php
	$forum = isset($_GET["forum"])&&$_GET["forum"]==1;
?>	
		<form id="EDinscription" name="EDinscription" action="<?php if(!$forum) echo 'inscription.php?etape=validation_inscription'; ?>" method="post" accept-charset="UTF-8" class="formA">
			<a class="fermer" onClick="inscription_fermer()"></a>
			<div class="captcha">
				<h2>Créez votre espace personnel</h2>

				<fieldset>
				<div name="captchaDiv" class="captchaDiv">
					<img src="antispam.php" alt="Captcha" id="captcha" name="captcha" class="captcha" /><a class="renew" onclick="document.images.captcha.src='antispam.php?id='+Math.round(Math.random(0)*1000)+1">
						<img src="02_medias/01_interface/refresh.png" alt="recharger l'image"/>
					</a>
				</div>
				<div class="message"></div>
				<ul>
					<li>
						<label for="userCaptchaCode">Entrez le code de s&eacute;curit&eacute; :</label>
						<input type="text" name="userCaptchaCode" id="userCaptchaCode" class="validate[required]"/>
					</li>
					<li>
						<label for="mail">Votre Email (identifiant) :</label>
						<input type="text" name="mail" id="mail" value="" class="validate[required,custom[email]]"/>
					</li>
					<li>
						<label for="mail2">V&eacute;rification Email :</label>
						<input type="text" name="mail2" id="mail2" value="" class="validate[required,custom[email]]"/>
					</li>
					<li>
						<label for="mdp">Votre mot de passe :</label>
						<input type="password" name="mdp" id="mdp" class="validate[required]" />
					</li>
					<?php
					if($forum){
					?>
					<li>
						<label for="pseudo">Pseudo (pour ce forum) :</label>
						<input type="text" name="pseudo" id="pseudo" class="validate[required]" />
					</li>
					<?php
					}
					?>
					<li>
						<label for="ville">Ville :</label>
						<input type="text" name="ville" id="ville" />
					</li>
					<li>
						<label for="cp">Code postal :</label>
						<input type="text" name="cp" id="cp" size="6"/>
						<input type="hidden" name="rech_idville" id="rech_idville" size="6"/>
					</li>
				</ul>
				<div class="boutons">
				<a href="#" title="Enregistrer" onClick="javascript:verif_inscription(); return false" class="boutonbleu ico-fleche">Enregistrer</a>

				</div>
				</fieldset>
			</div>
		</form>
		<div class="clear"></div>
		<script type="text/javascript" src="js/fonction_auto_presentation.js"></script>
		<script type="text/javascript">
		function verif_inscription()
		{
			if(!test_champ_vide(document.EDinscription.userCaptchaCode.value))
			{
				alert("Veuillez saisir le code de sécurité !!");
				document.EDinscription.userCaptchaCode.focus();
				//return false;
			}
			else
			{
				if(!test_email_valide(document.EDinscription.mail.value))
				{
					alert("Veuillez saisir un email valide !!");
					document.EDinscription.mail.focus();
					//return false;
				}
				else
				{
					if(document.EDinscription.mail.value!=document.EDinscription.mail2.value)
					{
						alert("Votre email de vérification n'est pas valide !!");
						document.EDinscription.mail2.focus();
						//return false;
					}
					else
					{
						if(!test_champ_vide(document.EDinscription.mdp.value))
						{
							alert("Vous devez saisir votre mot de passe !!");
							document.EDinscription.mdp.focus();
							//return false;
						}
						else
						{
							if(!test_champ_vide(document.EDinscription.ville.value) && !test_champ_vide(document.EDinscription.cp.value))
							{
								alert("Vous devez saisir votre ville ou votre code postal !!");
								document.EDinscription.ville.focus();
								//return false;
							}
							else
							{
								<?php
								if(!$forum){
								?>
								document.EDinscription.submit();
								<?php
								}
								else{
								?>
									if(!test_champ_vide(document.EDinscription.pseudo.value))
									{
										alert("Vous devez saisir votre pseudo !!");
										document.EDinscription.pseudo.focus();
										//return false;
									}
									else
									{
										inscription();
									}
								<?php
								}
								?>
								//alert("formulaire OK");
							}
						}
					}
				}
			}
		}
		<?php
		if($forum){
		?>
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
		
		function inscription(){
			var param = "forum=1";
				param += "&userCaptchaCode="+encodeURIComponent(document.EDinscription.userCaptchaCode.value);
				param += "&mail="+encodeURIComponent(document.EDinscription.mail.value);
				param += "&mdp="+encodeURIComponent(document.EDinscription.mdp.value);
				param += "&ville="+encodeURIComponent(document.EDinscription.ville.value);
				param += "&cp="+encodeURIComponent(document.EDinscription.cp.value);
				param += "&pseudo="+encodeURIComponent(document.EDinscription.pseudo.value);
			var xhr = getXhr();
				xhr.open("POST", "03_ajax/inscription.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send(param);
			var reponse = eval("("+xhr.responseText+")");
			if(reponse["ok"]){
				$.colorbox.close();
				eval("<?php echo $_GET['retour']; ?>");
			}
			else{
				alert(reponse["alert"]);
				if(reponse["code_erreur"]==1){
					document.images.captcha.src='antispam.php?id='+Math.round(Math.random(0)*1000)+1;
					document.EDinscription.userCaptchaCode.value = "";
					document.EDinscription.userCaptchaCode.focus();
				}
				else if(reponse["code_erreur"]==2){
					document.EDinscription.mail.value = "";
					document.EDinscription.mail2.value = "";
					document.EDinscription.mail.focus();
				}
				else if(reponse["code_erreur"]==3){
					document.EDinscription.cp.value = "";
					document.EDinscription.ville.value = "";
					document.EDinscription.ville.focus();
				}
				else if(reponse["code_erreur"]==4){
					document.EDinscription.pseudo.value = "";
					document.EDinscription.pseudo.focus();
				}
			}
		}
		
		<?php
		}
		?>
		</script>
		<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
		<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript">
			$(function() {
			    // Validation form
			    $("#EDinscription").validationEngine("attach",{promptPosition : "topRight", scroll: false});

			});
		</script>




