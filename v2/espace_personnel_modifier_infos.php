<?php
	session_name("EspacePerso");
	session_start();
	// Utilisateur connecté ?
  	require ('01_include/connexion_verif.php');
	require_once ('01_include/_connect.php');
	// Traitement des modifications
	if((intval($_POST['modif']))==1)
	{
		$newsletter = intval($_POST['newsletter']);
		$maj_espaceperso = "
			UPDATE `utilisateur` SET 
			newsletter=".$newsletter;

		// Mail modifié ? (avec mot de passe obligatoire pour encodage md5)
		if((($_POST['mail_1_verif'])!=($_POST['mail']))&&(valid_email($_POST['mail']))&&(valid_email($_POST['mail2'])))
		{
			// Mot de passe modifié ?
			if((trim($_POST['mdp'])!="")&&(trim($_POST['mdp2']!=""))&&(trim($_POST['mdp'])==trim($_POST['mdp2'])))
			{
				$maj_email = $_POST['mail'];
				if(($_POST['mail2'])==($maj_email))
				{
					// Test si l'adresse email existe dans la base
					$sql_utilisateurs="SELECT * FROM $table_user WHERE email like :email";
					$res_utilisateurs = $connexion->prepare($sql_utilisateurs);
					$res_utilisateurs->execute(array(':email'=>$maj_email)) or die ("Erreur.");
					$tab_utilisateur=$res_utilisateurs->fetchAll();
					if(count($tab_utilisateur)>0)
					{
						// email déjà existant
						$_SESSION['message'] .= "Votre email n'a pas été modifié car l'email saisi est déjà utilisé.<br/>";
					}
					else
					{
						$maj_espaceperso .= ", email='$maj_email'";
	
						$maj_mdp = md5($loginbdd.trim($_POST['mdp']).$cle_cryptage);
						// $maj_mdp = $_POST['mdp'];
						$maj_espaceperso .= ", mot_de_passe='$maj_mdp'";
	
						// On modifie les infos de la session ouverte
						$_SESSION['UserConnecte_email'] = $maj_email;
	
					}				
				}
				else
				{
					$_SESSION['message'] .= "Votre email n'a pas été modifié (confirmation différente).<br/>";
				}
			}
			else
			{
				$_SESSION['message'] .= "Vous devez saisir un nouveau mot de passe (et le confirmer) pour modifier votre email.<br/>";
			}
		}

		// Ville modifié ?
		if((intval($_POST['rech_idville_ancien']))!=(intval($_POST['rech_idville'])))
		{
			$id_ville = intval($_POST['rech_idville']);
			$maj_espaceperso .= ", no_ville=".$id_ville;

			// Met à jour le cookie
			setcookie("id_ville", $id_ville, time() + 365*24*3600,"/", null, false, true);
		}

		// Mdp modifié ?
		if((trim($_POST['mdp'])!="")&&(trim($_POST['mdp2']!=""))&&(trim($_POST['mdp'])==trim($_POST['mdp2'])))
		{
			$maj_espaceperso .= ", ";

			// Le login a aussi été modifié ?
			if (!empty($maj_email))
				$loginbdd = $maj_email;
			else	
				$loginbdd=strtolower(trim($_POST['mail_1_verif']));
				
			$maj_mdp = md5($loginbdd.trim($_POST['mdp']).$cle_cryptage);
			// $maj_mdp = $_POST['mdp'];
			$maj_espaceperso .= " mot_de_passe='$maj_mdp'";
		}
		
		$maj_espaceperso .= " WHERE no=".intval($_SESSION['UserConnecte_id']);

		$connexion->exec($maj_espaceperso);
		// echo $maj_espaceperso;
		// Redirection à l'accueil espace perso avec message
		$_SESSION['message'] .= "Votre compte a été modifié avec succès.";
		$page = "espace_personnel.php";
		header("Location: $root_site$page");
		exit;	
		
	}
	
	
	// Formulaire de modif

	// include header
	$titre_page = "Modifier mon compte";
	$meta_description = "Modifier mes infos personnelles sur Ensemble ici : Tous acteurs de la vie locale. Accès à votre espace personnel.";
	/* $chem_css_ui = $root_site."css/jquery-ui-1.8.21.custom.css";
<script type="text/javascript"   src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="$chem_css_ui" type="text/css" /> */
	$ajout_header = <<<AJHE
	<script>
	  $(function() {
	      $( "#tabs" ).tabs({
		collapsible:true,
		beforeLoad: function( event, ui ) {
		    ui.jqXHR.error(function() {
			ui.panel.html(
			    "Cette page est en cours de construction..." );
		    });
		},
		select: function(event,ui) {
		      var url = $.data(ui.tab, 'load.tabs');
		      if (url==='01_include/connexion_deconnexion.php'){
			  window.location.href=url;
			  return false;
		      }
		}
	      });

		$("#tabs ul li").removeClass("ui-state-active").removeClass("ui-tabs-selected");
		$(".moncpte").addClass("ui-state-active").addClass("ui-tabs-selected"); 

	});
	</script>
        
AJHE;
	include ('01_include/structure_header.php');
		
	// Infos de la ville selectionnée
	$UserConnecte_id_fromSession = addslashes($_SESSION['UserConnecte']);
	$UserConnecte_email = addslashes($_SESSION['UserConnecte_email']);

	if ((!empty($UserConnecte_id_fromSession))&&(!empty($UserConnecte_email)))
	{
		$strQuery = "SELECT nom_ville_maj, code_postal, email, no_ville, newsletter FROM `utilisateur` U, `villes` V
		WHERE U.no_ville=V.id
		AND id_connexion=:ucid
		AND `email`=:ucemail";
		$res_user = $connexion->prepare($strQuery);
		$res_user->bindParam(":ucid", $UserConnecte_id_fromSession, PDO::PARAM_STR);
		$res_user->bindParam(":ucemail", $UserConnecte_email, PDO::PARAM_STR);		
		$res_user->execute();
		$tab_user = $res_user->fetchAll();
		// $tab_user = $res_user->fetch(PDO::FETCH_ASSOC);

	}


?>
<div id="colonne2" class="page_inscription">                  
    <div id="tabs">
        <ul id="menuespacep">
            <li><a href="#tabs-1">Accueil</a></li>
            <li><a href="espace_favori.php">Mes favoris</a></li>
            <li><a href="espace_mesannonces.php">Mes annonces</a></li>
            <li><a class="moncpte" href="espace_moncompte.php">Mon compte</a></li>
            <li><a href="01_include/connexion_deconnexion.php?page=espace">Deconnexion</a></li>
        </ul>
        <div id="tabs-1">
            <?php
            if (isset($_SESSION['message']))
            {
                echo "<p id=\"message\">".$_SESSION['message']."</p>";
                unset($_SESSION['message']);
            }
		/* <div class="actions">
			<a href="01_include/connexion_deconnexion.php" class="boutonbleu ico-login ajax">Deconnexion</a>
		</div> */
        ?>
	<h3>Modifier mon compte</h3>
		

		<form id="EDinscription" name="EDinscription" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" accept-charset="UTF-8" class="formA">
				<fieldset>
				<div class="message"></div>
				<p>Pour modifier votre email, vous devez obligatoirement <br/>saisir un nouveau mot de passe en fin de formulaire.</p>
				<ul>
					<li>
						<label for="mail">Votre Email (identifiant) :</label>
						<input type="text" name="mail" value="<?php echo $tab_user[0]['email']?>" id="mail" class="validate[required,custom[email],ajax[checkLOGIN]]" size="35" />
						<input type="hidden" name="mail_1_verif" value="<?php echo $tab_user[0]['email']?>" id="mail_1_verif" />
					</li>
					<li>
						<label for="mail2">Confirmation email :</label>
						<input type="text" name="mail2" id="mail2" value="" class="validate[funcCall[checkEMAIL],custom[email]]" size="35"/>
					</li>
					<li>
						<label>Ville <sup>*</sup> :</label>
						<input type="text" id="ville" name="ville" value="<?php echo $tab_user[0]['nom_ville_maj']?>" class="validate[required] cpville" size="35" />
						<img src="<?php echo $root_site; ?>img/tick-vert.png" alt="Ville validée" id="ville-valide" />
						<input type="hidden" name="rech_idville" id="id_ville" size="6" value="<?php echo $tab_user[0]['no_ville']?>"/>
						<input type="hidden" name="rech_idville_ancien" value="<?php echo $tab_user[0]['no_ville']?>"/>
					</li>
					<li>
						<label>Code postal <sup>*</sup> :</label>
						<input type="text" name="cp" size="6" value="<?php echo $tab_user[0]['code_postal']?>" class="validate[required,minSize[5],maxSize[5],custom[integer]] cpville" id="cp"/><br/>
					</li>
					<li>
						<label for="newsletter">Lettre d'information :</label>
					    <select name="newsletter">
						    <option value="1" <?php echo ($tab_user[0]['newsletter']==1?"selected":"") ?>>Inscrit</option>
						    <option value="0" <?php echo ($tab_user[0]['newsletter']==0?"selected":"") ?>>Non inscrit</option>
					    </select>
					</li>
				<p>Si rien n'est saisi ci-dessous, votre mot de passe restera inchangé.</p>
					<li>
						<label for="mdp">Nouveau mot de passe :</label>
						<input type="password" name="mdp" id="mdp" class="validate[funcCall[checkMDP1]]" />
					</li>
					<li>
						<label for="mdp2">Confirmation mot de passe :</label>
						<input type="password" name="mdp2" id="mdp2" class="validate[funcCall[checkMDP2]]" />
					</li>
				</ul>
				</fieldset>

				<div class="boutons">
					<input type="hidden" name="modif" value="1"/>
					<input type="submit" value="Modifier" class="boutonbleu ico-fleche" id="submitform" />
				</div>
			
		</form>
		<div class="clear"></div>
		<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
		<script type="text/javascript">
		// Les confirmations sont identiques ? (Via validationEngine)
		function checkMDP1(field, rules, i, options){
			if (field.val() != $("#mdp2").val()) {
				// this allows the use of i18 for the error msgs
				return options.allrules.MDPdiff.alertText;
			}
		}
		function checkMDP2(field, rules, i, options){
			if (field.val() != $("#mdp").val()) {
				// this allows the use of i18 for the error msgs
				return options.allrules.MDPdiff.alertText;
			}
		}
		function checkEMAIL(field, rules, i, options){
			if (field.val() != $("#mail").val()) {
				// this allows the use of i18 for the error msgs
				return options.allrules.EMAILdiff.alertText;
			}
		}
		function Tester_confirmations()
		{
			// On a modifié le mail ?
			if(($("#mail").val())!=($("#mail_1_verif").val()))
			{
				alert($("#mail").val());
				if($.trim($("#mail2").val())=="")
				{
					alert("Veuillez confirmer votre email.");
					return false;
				}
			}
		}

		function Tester_idville()
		{
			var valiv = $("#id_ville").val();
			if($.trim(valiv)=="")
			{
				$('#ville-valide').hide();
				$("#submitform").attr("disabled", "disabled");
				$("#cp").val('');
				$("#ville").val('');
				return false;
			}
			else
			{
				$('#ville-valide').show("slow");
				$("#submitform").removeAttr("disabled");
				return true;
			}
		}

		$(document).ready(function() {
			// Validation form
			$("#EDinscription").validationEngine("attach",{promptPosition : "topRight", scroll: false});
			// Tester idville
			Tester_idville();

			$('#EDinscription').submit(function() {
				// enlève espaces dans mail2
				$("#mail2").val($.trim($("#mail2").val()));

				return Tester_idville();
				return Tester_confirmations();
			});

			// Validation de la ville
			$('.cpville').change(function() {
				$("#id_ville").val("");
			});

			// Changement email
			$('#mail').change(function() {
				$("#mail2").val(" ");
			});
			// Changement email2 : enlève espaces
			$('#mail2').change(function() {
				$("#mail2").val($.trim($("#mail2").val()));
			});
		});
	
		// Autocomplete
		var cache = {};

			// Autocomplete
			var cache = {};
			$("#cp, #ville, #id_ville").autocomplete({
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
									$('#id_ville').val(item.NO);
									return item.CP;
								    }
								    else
								    {
									$('#cp').val(item.CP);
									$('#id_ville').val(item.NO);
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
											$('#id_ville').val(item.NO);
											return item.CP;
										    }
										    else
										    {
											$('#cp').val(item.CP);
											$('#id_ville').val(item.NO);
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
				delay: 100,
				change: function(event, ui) {
					// $('#EDconnexion').validationEngine('hideAll');
					Tester_idville();
				}
			});			
		</script>
        </div>
        
    </div>
      </div>

<?php
	// Colonne 3
	$affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');
	
	// Footer
	include ('01_include/structure_footer.php');
?>