<?php
	session_name("EspacePerso");
	session_start();
	require_once ('01_include/_var_ensemble.php');
	require_once ('01_include/_connect.php');

	// Inscrire un ami ou s'inscrire soi même à la newsletter
	$envoyer_ami = intval($_GET["envoyer_ami"]);
	// include header
	if ($envoyer_ami) 
		$titre_page = "Envoyer la lettre d'information à un ami";
	else
		$titre_page = "Inscription à la lettre d'information";

	$meta_description = $titre_page.". Ensemble ici : Tous acteurs de la vie locale";

	include ('01_include/structure_header.php');

	$txt_erreur="";

	// Traitement de l'inscription
	if((intval($_POST['doinscrireami']))==1)
	{

		//verification du code de sécurité
		if ( (isset($_REQUEST['userCaptchaCode'])) && (!empty($_REQUEST['userCaptchaCode'])) )
		{
			$userCaptchaCode = $_REQUEST['userCaptchaCode'];
			/* Cryptage saisie en MD5 +  comparaison avec session */
			if( md5($userCaptchaCode) != $_SESSION['sysCaptchaCode'] )
			{
				$txt_erreur="Le code de sécurité saisi est erroné.";
			}
			else
			{

				$emailins = strtolower($_POST['mail']);
				$no_ville = intval($_POST['no_ville']);

				// l'adresse email existe dans la table utilisateurs ?
				$sql_utilisateurs="SELECT * FROM utilisateur WHERE email like :email";
				$res_utilisateurs = $connexion->prepare($sql_utilisateurs);
				$res_utilisateurs->execute(array(':email'=>$emailins)) or die ("Erreur ".__LINE__." : ".$sql_utilisateurs);
				$tab_utilisateur=$res_utilisateurs->fetchAll();
				if (count($tab_utilisateur))
					$existe_util_membre = true;

				// l'adresse email existe dans la table newsletter ? 
				// On récupère l'état pour qualifier le message d'erreur
				$sql_util_news = "SELECT etat FROM newsletter WHERE email like :email";
				$res_util_news = $connexion->prepare($sql_util_news);
				$res_util_news -> execute(array(':email'=>$emailins)) or die ("Erreur ".__LINE__." : ".$sql_util_news);
				// $tab_util_news = $res_util_news->fetchAll();
				while($tab_util_news = $res_util_news->fetch(PDO::FETCH_ASSOC))
				{
					$existe_util_news = true;
					$existe_util_news_etat = $tab_util_news["etat"];
				}


				// email déjà inscrit ?
				if ( ($existe_util_membre) || ($existe_util_news) )
				{ 
					if ( ($existe_util_news) && (!$existe_util_news_etat) )
						$msg_deja = "Cette adresse email fait déjà parti des inscrits à la lettre d'information Ensemble ici mais est désabonnée.<br/>Pour réinscrire cette adresse, veuillez créer un espace personnel.";
					elseif ($existe_util_membre)
						$msg_deja = "Cette adresse email est associée à un espace personnel Ensemble ici.<br/>Pour gérer l'inscription à la newsletter, connectez-vous à votre espace personnel, puis cliquez sur l'onglet 'Mon compte'.";
					else
						$msg_deja = "Cette adresse email est déjà inscrite.";

					$txt_erreur = $msg_deja;
				}
				else
				{
						// On créé le code de desinscription à la lettre d'info
						$code_desinscription_nl = id_aleatoire();

						// inscription dans la table newsletter
						$sql_insert_news = "INSERT INTO `newsletter` 
											(email, no_ville, etat, code_desinscription_nl) 
											VALUES 
											(:email, :no_ville, :etat, :code_desinscription_nl)";
						$insNews = $connexion->prepare($sql_insert_news);
						$insNews->execute(array(':email'=>$emailins,':no_ville'=>$no_ville,':etat'=>1,':code_desinscription_nl'=>$code_desinscription_nl)) or die ("Erreur ".__LINE__." : ".$sql_insert_news);
						$no = $connexion->lastInsertId();
						$code_alea = $code_desinscription_nl; // on récupère le même code de desinscription à la lettre d'info
						$txt_reussi = "Inscription effectuée avec succès.";

/*					//envoi de l'email d'inscription
					$sujet = "ensembleici.fr - inscription à la lettre d'information";
					$mail_exp = $email_admin;
					$message = "Bonjour,<br>
								Merci d'avoir cr&eacute;&eacute; votre espace personnel.
								<br/><br/>
								Nous vous rappelons vos informations de connexion :<br>
								&nbsp;&nbsp;&nbsp;- votre login : <b>".$emailins."</b><br>
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
					$mail=strtolower($emailins);
					$destinataire = $dest;
					mail($mail,$sujet,$message,$headers);*/
					
				}
			}
		}
		else
		{
			//captcha vide normalement impossible....
			$txt_erreur="Le code de sécurité saisi est erroné.";
		}


	}
?>

      <div id="colonne2" class="page_inscription">

<?php
	// inscription réussie
	if ($txt_reussi)
		echo "<p id='erreur'>".$txt_reussi."</p>"; 
	else
	{

		// Message d'erreur
		if ($txt_erreur)
			echo "<p id='erreur'>".$txt_erreur."</p>"; 

?>
		<form id="EDinscami" name="EDinscami" action="<?php echo $PHP_SELF;?>" method="post" accept-charset="UTF-8" class="formA">
			<input type="hidden" name="code_desinscription_nl" id="code_desinscription_nl" value="<?php echo $code_desinscription_nl; ?>" /> 
			<a class="fermer" onClick="inscription_fermer()"></a>
			<div class="captcha">

				<fieldset>
				<div class="message"></div>
				<ul>
					<li>
						<label for="mail"><?php 
							if ($envoyer_ami) 
								echo "Adresse email de votre ami :";
							else
								echo "Votre email :";

							?></label>
						<input type="text" name="mail" id="mail" class="validate[required,custom[email]]" size="30" />
					</li>
					<li>
						<label for="no_ville">Pays de préférence :</label>
						<div style="float: left;">
							<input type="radio" name="no_ville" value="9558" checked> Val d'Eygues (Nyons)<br/>
							<input type="radio" name="no_ville" value="9608"> Pays de Rémuzat (Rémuzat)<br/>
							<input type="radio" name="no_ville" value="9542"> Hautes Baronnies (Montbrun-les-Bains)<br/>
							<input type="radio" name="no_ville" value="9424"> Pays de Buis-les-Baronnies	(Buis-les-Baronnies)<br/>
						</div>
						<div class="clear"></div>
					</li>
				</ul>
				<div name="captchaDiv" class="captchaDiv">
					<img src="antispam.php" alt="Captcha" id="captcha" name="captcha" class="captcha" /><a class="renew" onclick="document.images.captcha.src='antispam.php?id='+Math.round(Math.random(0)*1000)+1">
						<img src="02_medias/01_interface/refresh.png" alt="recharger l'image"/>
					</a>
				</div>
				<ul>
					<li>
						<label for="userCaptchaCode">Entrez le code de s&eacute;curit&eacute; :</label>
						<input type="text" name="userCaptchaCode" id="userCaptchaCode" class="validate[required]"/>
					</li>
				</ul>
				<div class="boutons">
				<input type="hidden" name="doinscrireami" value="1"/>
				<a href="#" title="Enregistrer" onClick="javascript:verif_inscription(); return false" class="boutonbleu ico-fleche">Inscrire</a>

				</div>
				</fieldset>
			</div>
		</form>
		<div class="clear"></div>
		<script type="text/javascript" src="js/fonction_auto_presentation.js"></script>
		<script type="text/javascript">
		function verif_inscription()
		{
			if(!test_champ_vide(document.EDinscami.userCaptchaCode.value))
			{
				alert("Veuillez saisir le code de sécurité !!");
				document.EDinscami.userCaptchaCode.focus();
				//return false;
			}
			else
			{
				if(!test_email_valide(document.EDinscami.mail.value))
				{
					alert("Veuillez saisir un email valide !!");
					document.EDinscami.mail.focus();
					//return false;
				}
				else
				{
					document.EDinscami.submit();
					//alert("formulaire OK");
				}
			}
		}
		</script>
		<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
		<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript">
			$(function() {
			    // Validation form
			    $("#EDinscami").validationEngine("attach",{promptPosition : "topRight", scroll: false});
			});
		</script>
<?php
	}

	if (!$envoyer_ami) 
	{
?>
	<h1>Votre espace personnel</h1>
	<p>
		Vous pouvez également <a href="identification.html" title="Création espace personnel">créer un espace personnel </a>qui vous permettra :
		<ul>
			<li>de faciliter votre navigation sur le site en mémorisant votre situation géographique</li>
			<li>d'ajouter, de modifier vos informations (évènements, strutures, petites annonces...etc...)</li>
		</ul>
	</p>
	<h1>Archives</h1>
	<p>
		Vous pouvez <a href="lettreinfo_archives.php" title="Archives des lettres d'info">consulter les précédentes lettres d'informations</a>, déjà envoyées.
	</p>
<?php } ?>
   </div>
<?php	
	// Colonne 3
	$affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');

	// Footer
	include ('01_include/structure_footer.php');
?>