<?php
	session_name("EspacePerso");
	session_start();
	require_once ('01_include/_var_ensemble.php');
	require_once ('01_include/_connect.php');

	// On récupère l'adresse mail pour préremplir
	$code_desinscription_nl = $_GET["codoff"];

	$sql_user = "SELECT * FROM `newsletter`
	        WHERE code_desinscription_nl = :code_desinscription_nl
	        LIMIT 0,1";
	$res_user = $connexion->prepare($sql_user);
	$res_user->bindParam(':code_desinscription_nl', $code_desinscription_nl, PDO::PARAM_STR);
	$res_user->execute();

	while($tab_user = $res_user->fetch(PDO::FETCH_ASSOC))
	{
		$email_prerempli = $tab_user["email"];
		$no_ville_prerempli = $tab_user["no_ville"];
	}

	// si l'email n'est pas dans la table newsletter, on redirige vers la création de compte standard
	if (!$email_prerempli) 
	{
		$page = 'identification.html';
		header("Location: $root_site$page");
		exit;
	}

	// include header
	$titre_page = "Créez votre espace personnel";
	$meta_description = "Créez votre espace personnel sur Ensemble ici : Tous acteurs de la vie locale";

	include ('01_include/structure_header.php');
?>

      <div id="colonne2" class="page_inscription">

		<form id="EDinscription" name="EDinscription" action="inscription.php?etape=validation_inscription" method="post" accept-charset="UTF-8" class="formA">
			<input type="hidden" name="code_desinscription_nl" id="code_desinscription_nl" value="<?php echo $code_desinscription_nl; ?>" /> 
			<a class="fermer" onClick="inscription_fermer()"></a>
			<div class="captcha">
				<h2>Créez votre espace personnel</h2>
        	<div id="intro_tt">
				<p>L’espace personnel permet de faciliter votre navigation sur le site « Ensemble ici » en mémorisant votre situation géographique. Il vous permettra également d'ajouter vos propres informations sur Ensemble ici.</p>
			</div>

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
						<input type="hidden" name="ancienmail" id="ancienmail" value="<?php echo $email_prerempli; ?>" />
						<input type="text" name="mail" id="mail" value="<?php echo $email_prerempli; ?>" class="validate[required,custom[email]]" size="30" />
					</li>
					<li class="verifmail" style="display:none;">
						<label for="mail2">V&eacute;rification Email :</label>
						<input type="text" name="mail2" id="mail2" value="" size="30"/>
					</li>
					<li>
						<label for="mdp">Votre mot de passe :</label>
						<input type="password" name="mdp" id="mdp" class="validate[required]" />
					</li>
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
					if ( ($("#ancienmail").val() != $("#mail").val()) && (document.EDinscription.mail.value!=document.EDinscription.mail2.value) )
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
								document.EDinscription.submit();
								//alert("formulaire OK");
							}
						}
					}
				}
			}
		}
		var cache = {};
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
				delay: 300
			});
		</script>
		<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
		<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript">
			$(function() {
			    // Validation form
			    $("#EDinscription").validationEngine("attach",{promptPosition : "topRight", scroll: false});
			    $('#mail').change(function() {
					$(".verifmail").slideDown("slow");
				});

			});
		</script>

      </div>
<?php
	// Colonne 3
	$affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');

	// Footer
	include ('01_include/structure_footer.php');
?>