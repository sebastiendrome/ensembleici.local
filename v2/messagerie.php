<?php
session_name("EspacePerso");
session_start();
include ('01_include/_connect.php');
include ('01_include/_var_ensemble.php');

$no = intval($_REQUEST['no']);
$type = strtolower($_REQUEST['type']);

if ((preg_match("#^(structure|evenement|annonce|petiteannonce)$#", $type))&&($no))
{
	// Annonce
	if ($type == "annonce") $type = "evenement";
	
	unset($_SESSION['messagerie_referer']);

	if(isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] !== "" )
	{
		// en provenance de notre domaine ?
		if (substr($_SERVER['HTTP_REFERER'], 0, strlen($root_site)) == $root_site)
			$_SESSION['messagerie_referer'] = $_SERVER['HTTP_REFERER'];
	}

	// récupération de l'email
	if ( ($type == "evenement") || ($type == "structure") )
	{
		// Structures et evements
		$sql_previsu="SELECT email FROM ".$type." WHERE no=:no AND etat = 1";
		$res_previsu = $connexion->prepare($sql_previsu);
		$res_previsu->execute(array(':no'=>$no)) or die ("Erreur ".__LINE__.".");
		$pv=$res_previsu->fetchAll();
		$destinataire = $pv[0]['email'];
	}
	elseif ($type == "petiteannonce")
	{
		// Petites annonces => 1er contact

		//recuperation des contacts
		$sql_liaison_contact="SELECT no_contact FROM ".$type."_contact WHERE no_".$type."=:no ORDER BY no_contact DESC";
		$res_liaison_contact = $connexion->prepare($sql_liaison_contact);
		$res_liaison_contact->execute(array(':no'=>$no)) or die ("Erreur ".__LINE__.".");
		$tab_liaison_contact=$res_liaison_contact->fetchAll();
		
		//recuperation du premier contact
		$sql_contact="SELECT email FROM contact WHERE no=:no";
		$res_contact = $connexion->prepare($sql_contact);
		$res_contact->execute(array(':no'=>$tab_liaison_contact[0]['no_contact'])) or die ("Erreur ".__LINE__.".");
		$tab_contact=$res_contact->fetchAll();
		$destinataire = $tab_contact[0]['email'];		
	}

	// utilisateur connecté ? on récupère son email
	$id_utilisateur = intval($_SESSION['UserConnecte_id']);
	if ($id_utilisateur)
	{
		$sql_utilisateurs="SELECT * FROM $table_user WHERE no = :no";
		$res_utilisateurs = $connexion->prepare($sql_utilisateurs);
		$res_utilisateurs->execute(array(':no'=>$id_utilisateur));
		$tab_utilisateur=$res_utilisateurs->fetchAll();
		if(count($tab_utilisateur))
			$expediteur = $tab_utilisateur[0]['email'];
	}

	if ($destinataire)
	{
	?>
	
	<div id="msgBox" class="message"></div>
	<div id="form">
		<div id="formulaire">
		<form method="post" id="EMessagerie" action="" class="formA">
		<div class="captcha">
			<h2>Envoyer un courriel <span id="loader"><img src="img/image-loader.gif" alt="Chargement en cours..." height="24" width="24"></span></h2>
	
			<fieldset>
			<div name="captchaDiv" class="captchaDiv">
				<img src="antispam.php" alt="Captcha" id="captcha" name="captcha" class="captcha" /><a class="renew" onclick="document.images.captcha.src='antispam.php?id='+Math.round(Math.random(0)*1000)+1">
					<img src="02_medias/01_interface/refresh.png" alt="recharger l'image"/>
				</a>
			</div>
			<ul>
				<li>
					<label for="userCaptchaCode">Recopiez ce code : <sup>*</sup></label>
					<input type="text" name="userCaptchaCode" id="userCaptchaCode" class="validate[required]" size="31"/>
				</li>
				<li>
					<label for="nom">Votre nom :</label>
					<input type="text" name="nom" id="nom" value="" size="31" />
				</li>
				<li>
					<label for="mail">Votre email : <sup>*</sup></label>
					<input type="text" name="mail" id="mail" value="<?php if ($expediteur) echo $expediteur; ?>" class="validate[required,custom[email]]" size="31"/>
				</li>
				<li>
					<label for="messageenv">Votre message : <sup>*</sup></label>
					<textarea id="messageenv" name="messageenv" class="validate[required]" rows="6" cols="32"></textarea>
				</li>
			</ul>

			<input type="hidden" value="<?php echo $no ?>" name="no">
			<input type="hidden" value="<?php echo $type ?>" name="type">

			<div class="boutons">
			<input type="submit" value="Envoyer" name="envoi" class="boutonbleu ico-fleche" />
			</div>
			Les champs marqués <sup>*</sup> sont requis.
			</fieldset>
		</div>
	</form>
	</div></div>
	<div class="clear"></div>
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
	<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">

		function ajaxValidationCallback(form, status)
		{
			$("#loader").show();
			if (status === true) {
				var formData = $('form#EMessagerie').serialize();
				$.ajax({
					type : 'POST',
					url : 'messagerie-envoi.php',
					dataType : 'json',
					data: formData,
					success : function(data){
						// Fermer la colorbox
						parent.jQuery.fn.colorbox.close();
					},
					error : function (xhr, ajaxOptions, thrownError){
						$("#msgBox").html("Erreur dans l'envoi de votre message : " + thrownError + "<br/>" + xhr.status);
						$("#loader").hide();
						$("#msgBox").show(400); 
					}
				});
				form.validationEngine('detach');
			} else {
				$("#msgBox").html(status + "Erreur dans l'envoi de votre message.<br/>");
				$("#loader").hide();
				$("#msgBox").show(400);
			}
		}

		$(function() {
			// Validation form
			$("#EMessagerie").validationEngine('attach', {
				promptPosition : "topLeft",
				scroll : false,
				onValidationComplete: ajaxValidationCallback
			});
		});
	</script>
	<?php
	} // Fin mail non envoyé
}
else
{
	// Erreur type ou no => Ferme la colorbox
	echo "<p>Impossible d'envoyer un email à l'auteur de cette fiche.</p>";
	// echo "<div clas='actions'><div class='ferme-colorbox'>Fermer</div></div>";
}
?>