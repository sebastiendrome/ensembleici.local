<?php
require_once('01_include/_var_ensemble.php');
//include('01_include/fonctions.php');
$login=strtolower(trim($_REQUEST['mail']));
$ville=$_REQUEST['ville'];
$cp=$_REQUEST['cp'];
$mot_de_passe=$_REQUEST['mdp'];
$txt_erreur="";
$code_desinscription_nl = strtolower(trim($_REQUEST['code_desinscription_nl']));
$loginbdd=strtolower(trim($_REQUEST['mail']));
$mdpbdd = md5($_REQUEST['mail'].trim($_REQUEST['mdp']).$cle_cryptage);
$statutbdd = 1; //actif
include('01_include/_connect.php');
//verification du code de sécurité
if (isset($_REQUEST['userCaptchaCode'])) 
{
	if (!empty($_REQUEST['userCaptchaCode']))
	{
		$userCaptchaCode = $_REQUEST['userCaptchaCode'];
		/* Cryptage saisie en MD5 +  comparaison avec session */
		if( md5($userCaptchaCode) != $_SESSION['sysCaptchaCode'] )
		{
			echo "<script>alert(\"Le code de sécurité saisi est erronné\")</script>";
			$txt_erreur="Le code de sécurité saisi est erronné.";
		}
		else
		{
			// l'adresse email existe dans la base ?
			$sql_utilisateurs="SELECT * FROM $table_user WHERE email like :email";
			$res_utilisateurs = $connexion->prepare($sql_utilisateurs);
			$res_utilisateurs->execute(array(':email'=>strtolower($login))) or die ("requete ligne 39 : ".$sql_utilisateurs);
			$tab_utilisateur=$res_utilisateurs->fetchAll();
			//email déjà existant
			if(count($tab_utilisateur)>0)
			{
				echo "<script>alert(\"Cette adresse email est déjà utilisée pour un compte.\")</script>";
				$txt_erreur="Cette adresse email est déjà utilisée pour un compte.<br/> Veuillez choisir une autre adresse email.";
			}
			else
			{
				//on teste la bonne existance de la ville
				$sql_ville="SELECT * FROM villes WHERE nom_ville_maj= :nom_ville_maj AND code_postal=:code_postal";
				$res_ville = $connexion->prepare($sql_ville);
				$res_ville->execute(array(':nom_ville_maj'=>$ville,':code_postal'=>$cp)) or die ("requete ligne 39 : ".$sql_ville);
				$tab_ville=$res_ville->fetchAll();
				//si problème de saisie
				if(count($tab_ville)==0)
				{
					echo "<script>alert(\"Un problème est survenu lors de l'enregistrement de votre commune.\")</script>";
					$txt_erreur="Un problème est survenu lors de l'enregistrement de votre commune.<br/> Veuillez recommencer.";
				}
				else
				{
					//recuperation de l'id de la ville
					$id_ville=$tab_ville[0]['id'];

					if ($code_desinscription_nl)
					{
						// suppression de la table newsletter si inscription depuis le lien de la lettre d'info
						$sql_del = "DELETE FROM `newsletter`
									WHERE code_desinscription_nl=:code 
										AND email=:email
									LIMIT 1";
						$delNews = $connexion->prepare($sql_del);
						$delNews->execute(array(':code'=>$code_desinscription_nl,':email'=>$login)) or die ("Erreur ".__LINE__." : ".$sql_del);
						$no = $connexion->lastInsertId();
						$code_alea = $code_desinscription_nl; // on récupère le même code de desinscription à la lettre d'info
					}
					else
					{
						// On créé le code de desinscription à la lettre d'info
						$code_alea = id_aleatoire();
					}

					// insertion dans la base envoi d'email admin + email inscrit
					$sql_utilisateurs = "INSERT INTO $table_user (email, no_ville, mot_de_passe, etat, verification_email, id_connexion, newsletter, code_desinscription_nl) VALUES (:email, :no_ville, :mot_de_passe, :etat, :verification_email, :id_connexion, :inscrit_newsletter, :code_desinscription_nl)";
					$insert = $connexion->prepare($sql_utilisateurs);
					$insert->execute(array(':email'=>$login, ':no_ville'=>$id_ville, ':mot_de_passe'=>$mdpbdd, ':etat'=>1, ':verification_email'=>0, ':id_connexion'=>id_aleatoire(), ':inscrit_newsletter'=>1, ':code_desinscription_nl'=>$code_alea)) or die ("Erreur ".__LINE__." : ".$sql_utilisateurs);
					$no = $connexion->lastInsertId();

					//envoi de l'email d'inscription
					$sujet = "ensembleici.fr - Creation d'un espace personnel";
					$mail_exp = $email_admin;
					$message = "Bonjour,<br>
								Merci d'avoir cr&eacute;&eacute; votre espace personnel.
								<br/><br/>
								Nous vous rappelons vos informations de connexion :<br>
								&nbsp;&nbsp;&nbsp;- votre login : <b>".$login."</b><br>
								&nbsp;&nbsp;&nbsp;- votre mot de passe : <b>".$mot_de_passe."</b><br><br>
								L’espace personnel vous permet de faciliter votre navigation sur le site « Ensemble ici », garantit un tri des informations en fonction de votre lieu d’habitation, sécurise vos annonces et facilite leur gestion.<br/><br/>
								Salutations, <br/><br/><br/>";
					$message = $emails_header.$message.$emails_footer;
					$boundary = "-----=" . md5( uniqid ( rand() ) );
					$headers = "From: $mail_exp \n"; 
					$headers .="Reply-To:".$mail_exp.""."\n"; 
					$headers .= "X-Mailer: PHP/".phpversion()."\n";
					$headers .= "MIME-Version: 1.0\n";
					$headers .= "Content-Type: text/html; charset='Iso-8859-1'; boundary=\"$boundary\"";
					$headers .='Content-Transfer-Encoding: quoted-printable';
					$mail=strtolower($login);
					$destinataire = $dest;
					mail($mail,$sujet,$message,$headers);
					
					redirige('01_include/connexion_espacePerso.php?login='.$loginbdd.'&mdp='.$mdpbdd.'&etape=creation');
				}
			}
		}
	}
	else
	{
		//captcha vide normalement impossible....
		echo "<script>alert(\"Le code de sécurité saisi est erronné\")</script>";
		$txt_erreur="Le code de sécurité saisi est erronné.";
	}
}
else
{
	//captcha vide normalement impossible....
	echo "<script>alert(\"Le code de sécurité saisi est erronné\")</script>";
	$txt_erreur="Le code de sécurité saisi est erronné.";
}
?>
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
						document.EDinscription.submit();
						//alert("formulaire OK");
					}
				}
			}
		}
	}
}
</script>

				<?php 
					if(strlen($txt_erreur)>1)
					{
						echo "<div id=\"erreur\">$txt_erreur";

						// Si clic sur lien dans la lettre d'info
						if ($code_desinscription_nl)
							echo " Merci de cliquer sur le bouton précédent de votre navigateur et de recommencer votre saisie.";
						echo "</div>";

					}
					if (!$code_desinscription_nl)
					{

				?>
				<form id="EDinscription" name="EDinscription" action="inscription.php?etape=validation_inscription" method="post" accept-charset="UTF-8" class="formA">
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
							<input type="text" name="userCaptchaCode" id="userCaptchaCode"  class="validate[required]"/>		
						</li>
						<li>
							<label for="mail">Votre Email (identifiant) :</label>
							<input type="text" name="mail" id="mail" value="<?php echo $login?>"  class="validate[required,custom[email]]"/>
						</li>
						<li>
							<label for="mail2">V&eacute;rification Email :</label>
							<input type="text" name="mail2" id="mail2" value="<?php echo $login?>"  class="validate[required,custom[email]]"/>
						</li>
						<li>
							<label for="mdp">Votre mot de passe :</label>
							<input type="password" name="mdp" id="mdp"  class="validate[required]"/>
						</li>
						<li>
							<label for="ville">Ville :</label>
							<input type="text" name="ville" id="ville" <?php echo $ville?> class="validate[required]"/>
						</li>
						<li>
							<label for="cp">Code postal :</label>
							<input type="text" name="cp" id="cp" size="6" <?php echo $cp?> class="validate[required]"/>
						</li>
					</ul>
					<div class="boutons">
					<a href="javascript:; return false" title="Enregistrer" onClick="javascript:verif_inscription()" class="boutonbleu ico-fleche">Enregistrer</a>
						
					</div>
					</fieldset>
				</div>
			</form>
			<div class="clear"></div>
			
			<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
		<script type="text/javascript">
			$(document).ready(function() {
			    // Validation form
			    $("#EDinscription").validationEngine("attach",{promptPosition : "topRight", scroll: false});
			});
		</script>
			
		<script>
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
<?php } ?>