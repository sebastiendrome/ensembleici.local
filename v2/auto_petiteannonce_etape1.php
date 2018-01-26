<?php
	session_name("EspacePerso");
	session_start();
	require ('01_include/connexion_verif.php');
	require('01_include/_connect.php');
	$type = "petiteannonce";
	$pa_titre="";
	$pa_description="";
	$pa_monetaire="";
	$pa_prix="";

	$mode_modification = intval($_REQUEST['mode_modification']);
	if (!$mode_modification && $_SESSION['mode_modification_pa'])
	  $mode_modification = intval($_SESSION['mode_modification_pa']);
	if ($mode_modification)
	{
		$_SESSION['mode_modification_pa'] = "1";
		$action_page = "Modifier";
	}
	else
		$action_page = "Ajouter";

	// retour sur une etape précédente
	if($_SESSION['no_pa']!=0)
	{
echo $_SESSION['no_pa'];
		$no_pa=$_SESSION['no_pa'];
		$sql_pa="SELECT * FROM petiteannonce WHERE no=:no AND no_utilisateur_creation=:no_utilisateur_creation";
		$res_pa = $connexion->prepare($sql_pa);
		$res_pa->execute(array(':no'=>$no_pa,':no_utilisateur_creation'=>$_SESSION['UserConnecte_id'])) or die ("Erreur ".__LINE__." : ".$sql_pa);
        $nb_rpa = $res_pa->rowCount();

		if ($nb_rpa)
		{
			$est_session = true;
			$tab_pa_modif=$res_pa->fetchAll();
			$pa_titre=$tab_pa_modif[0]['titre'];
			$pa_description=$tab_pa_modif[0]['description'];
			$pa_monetaire=$tab_pa_modif[0]['monetaire'];
			$pa_prix=$tab_pa_modif[0]['prix'];
		}
		else
		{
			unset($_SESSION['no_pa']);
		}
	}

	if (!$est_session)
	{

		//on demande la modification d'une fiche existante
		if((isset($_REQUEST['no_orig'])&&$_REQUEST['no_orig']>0))
		{
			$_SESSION['no_pa'] = $_REQUEST['no_orig'];
			//recuperation des informations de l'evenement dans la base de donnees
			$sql_pa="SELECT * FROM petiteannonce WHERE no=:no AND no_utilisateur_creation=:no_utilisateur_creation";
			$res_pa = $connexion->prepare($sql_pa);
			$res_pa->execute(array(':no'=>$_REQUEST['no_orig'],':no_utilisateur_creation'=>$_SESSION['UserConnecte_id'])) or die ("Erreur ".__LINE__." : ".$sql_pa);
			$tab_pa_modif=$res_pa->fetchAll();

			$pa_titre=$tab_pa_modif[0]['titre'];
			$pa_description=$tab_pa_modif[0]['description'];
			$pa_monetaire=$tab_pa_modif[0]['monetaire'];
			$pa_prix=$tab_pa_modif[0]['prix'];

			// mise à jour de l'evenement en cours de modification
			$maj_pa_etape1 = "UPDATE petiteannonce SET validation=2 WHERE no=:no AND no_utilisateur_creation=:no_utilisateur_creation";
			$maj_pa = $connexion->prepare($maj_pa_etape1);
			$maj_pa->execute(array(':no'=>$tab_pa_modif[0]['no'],':no_utilisateur_creation'=>$_SESSION['UserConnecte_id'])) or die ("Erreur ".__LINE__." : ".$maj_pa_etape1);
		}
		else
		{
			// creation d'une nouvelle entree dans la table evenement

			$titretemp='titre';
			$date_fin = date('Y-m-d',strtotime('+60 days'));
			$sql_pas = "INSERT INTO petiteannonce (date_fin,date_creation, no_utilisateur_creation, etat) VALUES (:date_fin,NOW(), :num_utilisateur, 0)";
			$insert = $connexion->prepare($sql_pas);
			$insert->execute(array(':date_fin'=>$date_fin,':num_utilisateur'=>$_SESSION['UserConnecte_id'])) or die ("Erreur ".__LINE__." : ".$sql_pas);
			$no = $connexion->lastInsertId();

			// recuperation des informations de l'evenement dans la BDD
			$sql_pa="SELECT * FROM petiteannonce WHERE no=:no AND no_utilisateur_creation=:no_utilisateur_creation";
			$res_pa = $connexion->prepare($sql_pa);
			$res_pa->execute(array(':no'=>$no,':no_utilisateur_creation'=>$_SESSION['UserConnecte_id'])) or die ("Erreur ".__LINE__." : ".$sql_pa);
			$tab_pa_modif=$res_pa->fetchAll();

			$pa_titre=$tab_pa_modif[0]['titre'];
			$pa_monetaire=$tab_pa_modif[0]['monetaire'];
			$pa_prix=$tab_pa_modif[0]['prix'];
			$pa_description=$tab_pa_modif[0]['description'];

			$_SESSION['no_pa'] =$no;

			// On supprime le mode modification précédemment enregistré
			unset($_SESSION['mode_modification_pa']);
			// Idem pour l'étape à laquelle on s'est arrêté
			unset($_SESSION['etape_arret_form_pa']);
		}
	}

	$type_accentue = "petite annonce";
	$un_accentue = "une petite annonce";

	// include header
	$titre_page = $action_page." ".$un_accentue." - Etape 1";
	$titre_page_bleu = " ";
	$meta_description = $action_page." ".$un_accentue." sur Ensemble ici : Tous acteurs de la vie locale";
	$ajout_header = <<<AJHE
		<script type='text/javascript' src="js/recherche_ajax.js"></script>
		<script type='text/javascript' src="js/fonction_auto_presentation.js"></script>
		<script type="text/javascript" src="js/ckeditor/ckeditor.js"></script><script type="text/javascript">
window.onload = function() {
CKEDITOR.replace('description',{toolbar:'Auto',uiColor:'#F0EDEA',language:'fr',width:'560',height:'200',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
};</script>
AJHE;
	include ('01_include/structure_header.php');
?>

<script type="text/javascript">
$(function() {
	// Aide
	$('#aide_icone').click(function() {
	  $('#aide_contenu').slideToggle('slow', function() {
	  });
	});
});

// Vérification du prix via validationEngine si monetaire
function verif_prix()
{
    if (($("#monetaire").val()==1) && ($('#prix').val() == 0))
	{
		return "Vous devez saisir un prix si votre annonce est monétaire !";
	}
}
</script>
      <div id="colonne2">
        <div id="formpa" class="blocB">
			<h1><?php echo $titre_page; ?></h1>

			<?php require('01_include/structure_etapes_form.php'); ?>

				<div id="aide_icone">
					<img src="img/icone-info.png" title="Cliquez pour afficher les explications" class="infobulle-b" />
				</div>

				<form name="EDretour" id="EDretour" action="<?php
					echo "ajouter_une_petiteannonce.html"; ?>" method="post" accept-charset="UTF-8">
					<input type="hidden" value="<?php echo $_SESSION['no_pa'] ?>" name="no_orig">
					<button type="submit" class="boutonbleu ico-flecheretour">Retour</button>
				</form>

				<div id="aide_contenu">
					<p>Afin de profiter au mieux de ce service, nous vous conseillons, dans un premier temps, de rassembler toutes les informations permettant de r&eacute;aliser votre fiche petite annonce.<br/><br/>
					Voici les &eacute;l&eacute;ments qui vous seront demand&eacute;s : (seuls les &eacute;l&eacute;ments suivi d'une <sup class="note">*</sup> sont obligatoires)<br/><br/>
					<ul>
						<li>De quoi s'agit-il ?<br/>
							<ul>
								<li>Titre de la petite annonce <sup class="note">*</sup></li> 
								<li>Un texte de présentation <sup class="note">*</sup></li>
								<li>Visuel (affiche, logo, photo équipe,…)</li>
								<li>Monétaire (vente / achat) ou non (don, service...) ? <sup class="note">*</sup></li>
								<li>Numéro de téléphone </li>
								<li>Email <sup class="note">*</sup></li>
							</ul>
						</li>
					</ul>
					</p>
				</div>

					<form name="EIForm" id="EIForm" action="auto_petiteannonce_etape2.php" method="post" class="formA" accept-charset="UTF-8">
					<fieldset>
					<label>Votre titre <sup>*</sup> :</label><input type="text" name="titre" value="<?php echo htmlspecialchars($pa_titre) ?>" class="validate[required]"/><br/><br/>

					<label for="monetaire">Annonce monétaire ? <sup>*</sup></label>
				  	<select name="monetaire" id="monetaire">
				  		<option value="1" <?php echo ($pa_monetaire==1?"selected":""); ?>>Oui</option>
				  		<option value="0" <?php echo ($pa_monetaire==0?"selected":""); ?>>Non</option>
				  	</select><br/><br/>

					<div id="prixcont">
						<label for="prix">Prix :</label><input id="prix" type="text" name="prix" size="6" value="<?php echo FormatPrix($pa_prix,$no_symbol=true) ?>" class="validate[funcCall[verif_prix]]" /> &euro;<br/><br/>
					</div>

					</fieldset>
					<label class="labellarge">Proposez-nous un texte descriptif - présentant votre petite annonce <sup>*</sup> :</label><br/><br/>
					<textarea id="description" name="description" class="validate[required,length[5,500]]">
					<?php
						echo $pa_description;
					?>
					</textarea>
					<input type=hidden name="no_pa_post" value="<?php echo $_SESSION['no_pa']?>">
					<input type=hidden name="provenance" value="etape1">
					<?php if ($mode_modification)
					echo "\n<input type='hidden' name='mode_modification' value='1' />\n";
					 ?>
					<br/><br/>
					<div class="actions"><button type="submit" class="boutonbleu ico-fleche">Suite (étape 2)</button></div>

					<br/><br/>
					</form>
				<div id="resultats"></div>

		</div>
		<div class="clear"></div>
      </div>
		<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
		<script type="text/javascript">
			$(document).ready(function() {
			    // Validation form
			    $("#EIForm").validationEngine(
			    	"attach",{
			    		promptPosition : "topRight", 
			    		scroll: false
			    	});

				// affichage du champ prix conditionnel
			    function affiche_prix()
			    {
				    if ($("#monetaire").val()==1)
				    	$('#prixcont').show(500);
				    else
				    	$('#prixcont').hide(500);
				    return false;
			    }

			    // Au chargement de la page
			    affiche_prix();
			    // Au changement sur le select monetaire
			    $("#monetaire").change(affiche_prix);

			});
		</script>
<?php

$affiche_articles = true;
$affiche_publicites = true;
include ('01_include/structure_colonne3.php');

include ('01_include/structure_footer.php');
?>