<?php
	session_name("EspacePerso");
	session_start();
	require ('01_include/connexion_verif.php');
	require('01_include/_connect.php');
	$type = "petiteannonce";

	$mode_modification = intval($_REQUEST['mode_modification']);
	if (!$mode_modification && $_SESSION['mode_modification_pa'])
	  $mode_modification = intval($_SESSION['mode_modification_pa']);
	if ($mode_modification)
		$action_page = "Modifier";
	else
		$action_page = "Ajouter";

	if((isset($_POST['no_orig'])&&$_POST['no_orig']>0))
	{
		//modification d'une fiche existante
		$_SESSION['no_pa'] =$_POST['no_orig'];
	}

	$no_pa=$_SESSION['no_pa'];

	if (!$no_pa)
	{
			// désactivé(e) ou inexistant(e)
			header("location:index.php");
			exit();
	}

	$sql_pa="SELECT * FROM petiteannonce WHERE no=:no";
	$res_pa = $connexion->prepare($sql_pa);
	$res_pa->execute(array(':no'=>$no_pa)) or die ("Erreur ".__LINE__." : ".$sql_pa);
	$tab_pa_modif=$res_pa->fetchAll();
	// Formatage site internet
	if ($tab_pa_modif[0]['site'])
		$site_web = htmlspecialchars($tab_pa_modif[0]['site']);
	else
		$site_web = "http://";

	// Rayon par défaut (en mode ajout)
	if (!$mode_modification)
		$tab_pa_modif[0]['rayon'] = 50;

	//recuperation des contacts
	$sql_pa_contact="SELECT * FROM petiteannonce_contact WHERE no_petiteannonce=:no ORDER BY no_contact DESC";
	$res_pa_contact = $connexion->prepare($sql_pa_contact);
	$res_pa_contact->execute(array(':no'=>$no_pa)) or die ("Erreur ".__LINE__." : ".$sql_pa_contact);
	$tab_pa_contact=$res_pa_contact->fetchAll();

	//recuperation du premier contact
	$sql_contact="SELECT * FROM contact WHERE no=:no";
	$res_contact = $connexion->prepare($sql_contact);
	$res_contact->execute(array(':no'=>$tab_pa_contact[0]['no_contact'])) or die ("Erreur ".__LINE__." : ".$sql_contact);
	$tab_contact=$res_contact->fetchAll();

	//recuperation des infos de la ville
	$sql_villes2	="SELECT * FROM villes WHERE id=:id";
	$res_villes2 = $connexion->prepare($sql_villes2);
	$res_villes2->execute(array(":id"=>$tab_pa_modif[0]['no_ville'])) or die ("Erreur ".__LINE__." : ".$sql_villes2);
	$tab_villes2=$res_villes2->fetchAll();
	
	// include header
	$titre_page = $action_page." une petite annonce - Etape 3";
	$meta_description = $action_page." une petite annonce sur Ensemble ici : Tous acteurs de la vie locale";
	$titre_page_bleu = " ";
	$ajout_header = <<<AJHE
		<script type="text/javascript" src="js/fonction_auto_presentation.js"></script>
AJHE;
	include ('01_include/structure_header.php');
?>

      <div id="colonne2">
        <div id="formpa" class="blocB">
			<h1><?php echo $titre_page; ?></h1>
			<?php require('01_include/structure_etapes_form.php'); ?>
					<form name="EIForm" id="EIForm" action="auto_petiteannonce_etape4.php" method="post" class="formA" accept-charset="UTF-8">
					<fieldset>
						<label for="rayonmax">Rayon de diffusion : <sup>*</sup></label>
						  	<select name="rayonmax">
						  		<option value="10" <?php echo ($tab_pa_modif[0]['rayon']<=10?"selected":"") ?>>Moins de 10 kms</option>
						  		<option value="50" <?php echo (($tab_pa_modif[0]['rayon']>10)&&($tab_pa_modif[0]['rayon']<=50)?"selected":"") ?>>Moins de 50 kms</option>
						  		<option value="999" <?php echo ($tab_pa_modif[0]['rayon']>50?"selected":"") ?>>Plus de 50 kms</option>
						  	</select>
						<br/>
					</fieldset>
					<h1 class="titreA">CONTACT</h1>
					<fieldset>
						<input type="hidden" name="no_contact" value="<?php echo $tab_contact[0]['no_contact']; ?>"/>
						<br/>
						<label>Personne à contacter :</label>
						<input type="text" name="nom_contact" value="<?php echo htmlspecialchars($tab_contact[0]['nom'])?>">
						<br/>
						<label>T&eacute;l&eacute;phone :</label>
						<input type="text" name="telephone" value="<?php echo FormatTel(htmlspecialchars($tab_contact[0]['telephone'])) ?>" class="validate[custom[phone]]">
						Afficher ?
							<input name="afficher_tel" id="afficher_tel" value="1" type="checkbox" <?php if (intval($tab_pa_modif[0]['afficher_tel'])>0) echo " checked"; ?> />
						<br/>
						<label>Mobile :</label>
						<input type="text" name="mobile" value="<?php echo FormatTel(htmlspecialchars($tab_contact[0]['mobile'])) ?>" class="validate[custom[phone]]">
						Afficher ?
							<input name="afficher_mob" id="afficher_mob" value="1" type="checkbox" <?php if (intval($tab_pa_modif[0]['afficher_mob'])>0) echo " checked"; ?> />
						<br/>
						<sup>En renseignant votre adresse email, les personnes intéressées par votre annonce pourront vous contacter via le formulaire de contact intégré à Ensemble ici (votre adresse ne sera pas visible sur le site).</sup><br/>

						<label>Email :</label>
						<input type="text" name="email" value="<?php echo htmlspecialchars($tab_contact[0]['email'])?>" class="validate[custom[email]]" size="35">
					</fieldset>

					<fieldset>
						<label>Code postal <sup>*</sup> :</label>
						<input type="text" name="cp" id="cp" size="6" value="<?php echo $tab_villes2[0]['code_postal']?>" class="validate[required,minSize[5],maxSize[5],custom[integer]] cpville" />
						<br/>
						<label>Ville <sup>*</sup>
						:</label> <input type="text"
						class="validate[required] cpville"
						name="ville" id="ville" value="<?php echo
						$tab_villes2[0]['nom_ville_maj']?>" size="35" /> <img src="<?php echo $root_site; ?>img/tick-vert.png" alt="Ville validée" id="ville-valide" />
						<input type="hidden" name="rech_idville" id="id_ville" value="<?php echo $tab_villes2[0]['id']?>"/>
					</fieldset>
					<h1 class="titreA">SITE INTERNET</h1>
					<fieldset>
						<label>Lien (votre annonce sur un autre site ?) :</label>
						<input type="text" name="site_internet" value="<?php echo $site_web; ?>" size="35">
					</fieldset>
					<h1 class="titreA">LIAISONS</h1>
					<fieldset>
				      <div id="les_liaisons">
				      <?php
					  // Affiche les liaisons
					  // Paramètre : $id_source et $type_source
					  // Optionnel : $depuis_formulaire
					  $type_source = $type;
					  $id_source = $no_pa;
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
								<input type="text" id="nom_liaison_B" name="nom_liaison_B" size="32" /> <img src="<?php echo $root_site; ?>img/tick-vert.png" alt="Liaison validée" id="liee-valide" /></li>
							</ul>
							
							<div class="actions boutons_centre"><div id="ajoutliaison" class="boutonbleu ico-ajout">Ajouter</div></div>
						</div>
						</fieldset>
					</div>
					</fieldset>
					
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