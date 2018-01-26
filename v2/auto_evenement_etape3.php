<?php
	session_name("EspacePerso");
	session_start();
	require ('01_include/connexion_verif.php');
	require('01_include/_connect.php');
	$type = "evenement";
	
	$mode_modification = intval($_REQUEST['mode_modification']);
	if (!$mode_modification && $_SESSION['mode_modification_evt'])
	  $mode_modification = intval($_SESSION['mode_modification_evt']);
	if ($mode_modification)
		$action_page = "Modifier";
	else
		$action_page = "Ajouter";
		

	if((isset($_POST['no_orig'])&&$_POST['no_orig']>0))
	{
		//modification d'une fiche existante donc copie des donnees actuelles dans la table evenement_temp
		$_SESSION['no_evenement'] =$_POST['no_orig'];
	}

	$no_evenement=$_SESSION['no_evenement'];

	if (!$no_evenement)
	{
			// désactivé(e) ou inexistant(e)
			header("location:index.php");
			exit();
	}

	$sql_evenement="SELECT * FROM evenement WHERE no=:no";
	$res_evenement = $connexion->prepare($sql_evenement);
	$res_evenement->execute(array(':no'=>$no_evenement)) or die ("requete ligne 14 : ".$sql_evenement);
	$tab_evenement_modif=$res_evenement->fetchAll();
	//affichage des dates
	if($tab_evenement_modif[0]['date_debut']=="0000-00-00")
	{
		// $date_debut_event="JJ/MM/AAAA";
		$date_debut_event="";
	}
	else
	{
		$tab_date_debut_event=explode("-", $tab_evenement_modif[0]['date_debut']);
		$date_debut_event=$tab_date_debut_event[2]."/".$tab_date_debut_event[1]."/".$tab_date_debut_event[0];
	}
	if($tab_evenement_modif[0]['date_fin']=="0000-00-00")
	{
		//$date_fin_event="JJ/MM/AAAA";
		$date_fin_event="";
	}
	else
	{
		$tab_date_fin_event=explode("-", $tab_evenement_modif[0]['date_fin']);
		$date_fin_event=$tab_date_fin_event[2]."/".$tab_date_fin_event[1]."/".$tab_date_fin_event[0];
	}
	
	//affichage des heures
	$heure_debut_event = substr($tab_evenement_modif[0]['heure_debut'],0,-3);
	$heure_fin_event = substr($tab_evenement_modif[0]['heure_fin'],0,-3);

	// Formatage site internet
	if ($tab_evenement_modif[0]['site'])
		$site_web = htmlspecialchars($tab_evenement_modif[0]['site']);
	else
		$site_web = "http://";
	
	//recuperation des contacts
	$sql_evenement_contact="SELECT * FROM evenement_contact WHERE no_evenement=:no ORDER BY no_contact DESC";
	$res_evenement_contact = $connexion->prepare($sql_evenement_contact);
	$res_evenement_contact->execute(array(':no'=>$no_evenement)) or die ("requete ligne 19 : ".$sql_evenement_contact);
	$tab_evenement_contact=$res_evenement_contact->fetchAll();
	
	
	//recuperation du premier contact
	$sql_contact="SELECT * FROM contact WHERE no=:no";
	$res_contact = $connexion->prepare($sql_contact);
	$res_contact->execute(array(':no'=>$tab_evenement_contact[0]['no_contact'])) or die ("requete ligne 26 : ".$sql_contact);
	$tab_contact=$res_contact->fetchAll();
	
	//recuperation de l'intégralité des rôles
	$sql_role="SELECT * FROM role ORDER BY libelle";
	$res_role = $connexion->prepare($sql_role);
	$res_role->execute() or die ("requete ligne 32 : ".$sql_role);
	$tab_role=$res_role->fetchAll();
	
	//recuperation des infos de la ville
	$sql_villes2	="SELECT * FROM villes WHERE id=:id";
	$res_villes2 = $connexion->prepare($sql_villes2);
	$res_villes2->execute(array(":id"=>$tab_evenement_modif[0]['no_ville'])) or die ("requete ligne 32 : ".$sql_villes2);
	$tab_villes2=$res_villes2->fetchAll();
	
	// Si création d'évt et une seule structure enregistrée, on lie la structure
	if (!$mode_modification)
	{
		// ce user a une structure ?
		$sql_struct ="SELECT no FROM structure WHERE no_utilisateur_creation=:user";
		$res_struct = $connexion->prepare($sql_struct);
		$res_struct->execute(array(":user"=>$_SESSION['UserConnecte_id'])) or die ("requete ligne 32 : ".$sql_struct);
		$nb_struct = $res_struct->rowCount();
		$tab_struct = $res_struct->fetchAll();
		
		if ($nb_struct==1)
		{
			$no_A = intval($no_evenement);
			$type_A = "evenement";
			$no_B = intval($tab_struct[0]["no"]);			
			$type_B = "structure";

			if ($no_A && $no_B && $type_A && $type_B){
				$sql_ajt = "INSERT INTO `liaisons`
					(`no` , `type_A` , `no_A` , `type_B` , `no_B` , `date_creation`)
						VALUES
					(NULL , :type_A, :no_A, :type_B, :no_B, NOW())";
				$insert_ajt = $connexion->prepare($sql_ajt);
				$insert_ajt->execute(array(':type_A'=>$type_A, ':no_A'=>$no_A, ':type_B'=>$type_B, ':no_B'=>$no_B)) or die ("Erreur ".__LINE__." : ".$sql_ajt);
			}				
		}
	}

	// include header
	
		$titre_page = $action_page." un évènement - Etape 3";
		$meta_description = $action_page." un évènement sur Ensemble ici : Tous acteurs de la vie locale";
		
	$titre_page_bleu = " ";
	/* $chem_css_ui = $root_site."css/jquery-ui-1.8.21.custom.css";
	<link rel="stylesheet" href="$chem_css_ui" type="text/css" />
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script> */
	$ajout_header = <<<AJHE
		<script type="text/javascript" src="js/fonction_auto_presentation.js"></script>
		<script>
			$(function() {
				/* jQuery UI date picker français */
				$.datepicker.regional['fr'] = {
					closeText: 'Fermer',
					prevText: '&#x3c;Préc',
					nextText: 'Suiv&#x3e;',
					currentText: 'Courant',
					monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
					'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
					monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun',
					'Jul','Aoû','Sep','Oct','Nov','Déc'],
					dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
					dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
					dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
					weekHeader: 'Sm',
					dateFormat: 'dd/mm/yy',
					firstDay: 1,
					isRTL: false,
					showMonthAfterYear: false,
					yearSuffix: ''};
				$.datepicker.setDefaults($.datepicker.regional['fr']);
			
				$( "#date_debut" ).datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					onSelect: function( selectedDate ) {
						$( "#date_fin" ).datepicker( "option", "minDate", selectedDate );
					}
				});
				$( "#date_fin" ).datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					onSelect: function( selectedDate ) {
						$( "#date_debut" ).datepicker( "option", "maxDate", selectedDate );
					}
				});		
				
			$("#heure_debut").timepicker({
				currentText: 'actuel',
				closeText: 'valider',
				timeOnlyTitle: 'Saisir l\'heure',
				timeText: 'Heure saisie',
				hourText: 'Heures',             
				minuteText: 'Minutes',
				stepMinute: 5
			  });

			$("#heure_fin").timepicker({
				currentText: 'actuel',
				closeText: 'valider',
				timeOnlyTitle: 'Saisir l\'heure',
				timeText: 'Heure saisie',
				hourText: 'Heures',             
				minuteText: 'Minutes',
				stepMinute: 5
			  });
			});
		</script>
AJHE;
	include ('01_include/structure_header.php');
?>

      <div id="colonne2">                  
        <div id="formevent" class="blocB">
			<h1><?php echo $titre_page; ?></h1>

			<?php require('01_include/structure_etapes_form.php'); ?>
					<form name="EIForm" id="EIForm" action="auto_evenement_etape4.php" method="post" class="formA" accept-charset="UTF-8">
<?php // Supprimé : onSubmit="return verif_evenement_etape3()" ?>
					<h1 class="titreA">DATE</h1>
					<fieldset><br/>
						<label>Date début <sup>*</sup> :</label>
						<input type="text" name="date_debut" id="date_debut" class="validate[required]" value="<?php echo $date_debut_event?>">
						<br/>
						<label>Date fin <sup>*</sup> :</label>
						<input type="text" name="date_fin" id="date_fin" class="validate[required]" value="<?php echo $date_fin_event?>">
					</fieldset>
					
					<h1 class="titreA">HEURE</h1>
					<fieldset><br/>
						<label>Heure début :</label>
						<input type="text" name="heure_debut" id="heure_debut" value="<?php echo $heure_debut_event?>">
						<br/>
						<label>Heure fin :</label>
						<input type="text" name="heure_fin" id="heure_fin" value="<?php echo $heure_fin_event?>">
					</fieldset>
					
					<h1 class="titreA">LIEU</h1>
					<fieldset>
                    <sup>Choisir la ville dans la liste proposée. (Départements autorisés : 26, 07, 84, 05)</sup><br/>
						<label>Nom du lieu :</label>
						<input size="35" type="text" name="nomadresse" value="<?php echo htmlspecialchars($tab_evenement_modif[0]['nomadresse'])?>">
						<br/>
						<label>Adresse :</label>
						<input type="text" name="adresse" value="<?php echo htmlspecialchars($tab_evenement_modif[0]['adresse'])?>" size="35">
						<br/>

						<label>Code postal <sup>*</sup> :</label>
						<input type="text" name="cp" id="cp" size="6" value="<?php echo $tab_villes2[0]['code_postal']?>" class="validate[required,minSize[5],maxSize[5],custom[integer]] cpville" />
						<br/>
						<label>Ville <sup>*</sup>
						:</label> <input type="text"
						class="validate[required] cpville"
						name="ville" id="ville" value="<?php echo
						$tab_villes2[0]['nom_ville_maj']?>" size="35" /> <img src="<?php echo $root_site; ?>img/tick-vert.png" alt="Ville validée" id="ville-valide" />
						<input type="hidden" name="rech_idville" id="id_ville" value="<?php echo $tab_villes2[0]['id']?>"/>

						<br/>
						<label>T&eacute;l&eacute;phone principal :</label>
						<input type="text" name="telephone_evenement" value="<?php echo htmlspecialchars($tab_evenement_modif[0]['telephone'])?>">
						<br/>
						<label>Mobile :</label>
						<input type="text" name="telephone2_evenement" value="<?php echo htmlspecialchars($tab_evenement_modif[0]['telephone2'])?>">
						<br/>
						<label>Email :</label>
						<input type="text" name="email_evenement" value="<?php echo htmlspecialchars($tab_evenement_modif[0]['email'])?>" size="35">
					</fieldset>
					<h1 class="titreA">CONTACT</h1>
					<fieldset>
						<sup>Seul le nom sera affiché sur le site. Les autres informations resteront confidentielles.</sup><br/>
						<label>Personne &agrave; contacter :</label>
						<input type="hidden" name="no_contact" value="<?php echo $tab_evenement_contact[0]['no_contact']; ?>"/>
						<input type="text" name="nom_contact" value="<?php echo htmlspecialchars($tab_contact[0]['nom']) ?>">
						<br/>
						<label>R&ocirc;le du contact :</label>
						<select name="role">
							<option value="0" selected></option>
							<?php
								for($indice_role=0; $indice_role<count($tab_role); $indice_role++)
								{
									if($tab_role[$indice_role]['no']==$tab_evenement_contact[0]['no_role'])
									{
										echo "<option value=\"".$tab_role[$indice_role]['no']."\" selected>".$tab_role[$indice_role]['libelle']."</option>";
									}
									else
									{
										echo "<option value=\"".$tab_role[$indice_role]['no']."\">".$tab_role[$indice_role]['libelle']."</option>";
									}
								}
							?>	
						</select>
						<br/>
						<label>T&eacute;l&eacute;phone contact :</label>
						<input type="text" name="tel" value="<?php echo htmlspecialchars($tab_contact[0]['telephone']) ?>">
						<br/>
						<label>Email contact :</label>
						<input type="text" name="email" value="<?php echo htmlspecialchars($tab_contact[0]['email'])?>" class="validate[custom[email]]" size="35">
					</fieldset>
					<h1 class="titreA">SITE INTERNET</h1>
					<fieldset>
						<label>Site internet :</label>
						<input type="text" name="site_internet" value="<?php echo $site_web; ?>" size="35">
					</fieldset>
					<h1 class="titreA">LIAISONS</h1>
						      <div id="les_liaisons">
						      <?php
							  // Affiche les liaisons
							  // Paramètre : $id_source et $type_source
							  // Optionnel : $depuis_formulaire
							  $type_source = $type;
							  $id_source = $no_evenement;
							  $depuis_formulaire = true;
							  							  
							  require("ajax_liaisons_affiche.php");
						      ?>
						      </div>
					<div class="actions boutons_centre">
						<div id="ajoutliaison_titre" class="boutonbleu ico-ajout">Ajouter une liaison</div>
					</div>
					<div id="ajoutliaison_form">
						<fieldset>
						<div id="msg"></div>
						<div name="formajoutliaison" id="formajoutliaison" class="formA">
							<input type="hidden" value="<?php echo $type_source_ok ?>" name="type_A" id="type_A">
							<input type="hidden" value="<?php echo $id_source ?>" name="no_A" id="no_A">
							
								<ul>		
									<li><label for="type_B">Type : </label>
									<select name="type_B" id="type_B">
										<option value="evenement">Evènement</option>
										<option selected value="structure">Structure</option>
										<option value="petiteannonce">Petite annonce</option>
									</select></li>
									<li><input type="hidden" id="no_B" name="no_B" />
									<label for="nom_liaison_B">Recherche : </label>
									<input type="text" id="nom_liaison_B" name="nom_liaison_B" size="35" /> <img src="<?php echo $root_site; ?>img/tick-vert.png" alt="Liaison validée" id="liee-valide" /></li>
								</ul>
								
								<div class="actions boutons_centre"><div id="ajoutliaison" class="boutonbleu ico-ajout">Ajouter</div></div>
						</div>
						</fieldset>
					</div>

						<input type=hidden name="provenance" value="etape3">
						<input type="hidden" name="ajout_contact" value="ok">
						<?php
						if ($mode_modification)
							echo "\n<input type='hidden' name='mode_modification' value='1' />\n";
						?>
						<div class="actions"><input type="submit" class="boutonbleu ico-fleche" value="Suite (étape 4)"></div>
					</form>
					</fieldset>
				
				
		</div>
		<div class="clear"></div>
      </div>
		<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
		<script type="text/javascript">
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

				// ajouter une liaison
				$('#ajoutliaison_titre').click(function() {
				  $('#ajoutliaison_form').slideToggle('slow', function() {
				  });
				});

				// Validation form
				$("#EIForm").validationEngine("attach",{promptPosition : "topRight", scroll: false});
				Tester_idville();

				$('#EIForm').submit(function() {
					return Tester_idville();
				});

				// Validation de la ville
				/* A chaque caractère on lance change
				$('.cpville').keyup(function(){
				  $(this).change();
				}); */
				$('.cpville').change(function() {
					$('#ville-valide').hide();
				});
			});
		
			// Autocomplete ville
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
				select: function(event, ui) {
					Tester_idville();
				}
			});

			$(function() {
				Tester_idliaison();
				
				$('#ajoutliaison').click(function() {
					var formData = $('#formajoutliaison *').serialize();
					$.ajax({
						type : 'POST',
						url : 'ajax_liaisons_ajout.php',
						dataType : 'json',
						data: formData,
						success : function(dataret){
							if (!dataret)
								var dataret = "Ajout impossible : Merci de choisir un élement de la liste proposée.";
							$('#msg').html(dataret).slideDown().delay(3000).slideUp();
							$("#les_liaisons").load("ajax_liaisons_affiche.php", {id:<?php echo $id_source; ?>, type:'<?php echo $type_source; ?>',depuis_formulaire:1});
							// Vide les input
							$("#no_B").val("");
							$("#nom_liaison_B").val("");
						    $("#liee-valide").hide();
						}
					});
				});
				
				$(".supprimliaison").live('click', function() {
				    var answer = confirm('Etes-vous sur de vouloir supprimer cette liaison ?');
				    if (answer == true)
				    {
				      var commentContainer = $(this).parents('tr:first');
				      var id = $(this).attr("id");
				      // $type_source
				      // $id_source
				      var string = "no_liaison=" + id + "&type_source=<?php echo $type_source; ?>&id_source=<?php echo $id_source ?>";
				      $.ajax({
					 type: "POST",
					 url: "ajax_liaisons_delete.php",
					 data: string,
					 cache: false,
					 success : function(dataret){
						if (dataret) {
							commentContainer.fadeOut("slow", function(){$(this).remove();} );
							$('#msg').html(dataret).slideDown().delay(3000).slideUp();
						}
					 }
				      });
				    }
				    return false;
				});

				
				$( "#nom_liaison_B" ).autocomplete({
					source: function( request, response ) {
					    $.ajax({
						url: "ajax_liaisons_recherche.php",
						dataType: "json",
						data: {
						    no_A: $("#no_A").val(),
						    type_A: $("#type_A").val(),
						    type_B: $("#type_B").val(),
						    term: request.term
						},
						success: function( dataret ) {
						    response(dataret);
						}
					    });
					},
					minLength: 3,
					delay: 0,
					autoFocus: true,
					select: function( event, ui ) {
						$('#nom_liaison_B').val(ui.item.label);
						$('#no_B').val(ui.item.value);
						Tester_idliaison();
						return false;
					}
				});
			
				$("#nom_liaison_B").on("input", null, null, function(event){
				    $('#no_B').val("");
				    $("#liee-valide").hide();
				});

			});
			
			function Tester_idliaison()
			{
				var valiv = $("#no_B").val();
				if($.trim(valiv)=="")
				{
					$("#liee-valide").hide();
					$("#nom_liaison_B").val('');
					return false;
				}
				else
				{
					$('#liee-valide').show("slow");
					return true;
				}
			}
		</script>
<?php

$affiche_articles = true;
$affiche_publicites = true;
include ('01_include/structure_colonne3.php');

include ('01_include/structure_footer.php');
?>