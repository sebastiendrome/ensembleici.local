<?php
/*****************************************************
Modification d'un utilisateur
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Affichage du message s'il existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);

// Détermination de l'id
if (isset($_GET['id'])) $id_user = intval($_GET['id']);
$mode_ajout = intval($_GET['ajout']);

if($id_user||$mode_ajout) {

    if ($mode_ajout)
    {
	$titrepage = "Ajout d'$cc_une";

	// On génère un id
	$id = time();
	
	// L'id est-il déjà dans la base ?
	do {
	    $sql_existe = "SELECT * FROM `utilisateur` WHERE no=:no_user";
	    $res_existe = $connexion->prepare($sql_existe);
	    $res_existe->execute(array(':no_user'=>$id)) or die ("Erreur ".__LINE__." : ".$sql_existe);
	    $nb_user_existe = $res_existe->rowCount();
	  if($nb_user_existe)
	    $id++;
	  else
	    $id_user = $id;
	} while (!$id_user);

	$etat = 1; // Activé
    }
    else
    {
	$titrepage = "Modification d'$cc_une";

	// Détails de l'utilisateur
	$sql = "SELECT * FROM `utilisateur` U, `villes` V WHERE U.no_ville=V.id AND no=:no_user";
	$res = $connexion->prepare($sql);
	$res->execute(array(':no_user'=>$id_user)) or die ("Erreur ".__LINE__." : ".$sql);
	$row=$res->fetchAll();

	// Préparation des variables pour affichage
	$email = htmlspecialchars($row[0]['email']);
	$etat = intval($row[0]['etat']);
	$inscrit_newsletter = intval($row[0]['newsletter']);
	$verification_email = intval($row[0]['verification_email']);
	$droits = $row[0]['droits'];       
    }

    // Paramètres pour colorbox
    $_SESSION['id_user_passer'] = $id_user;

     
  // Lignes à ajouter au header
    $ajout_header = <<<AJHE
<script type="text/javascript" src="../../js/fonction_auto_presentation.js"></script>
<script>
	$(function() {
   
	    $('#ajax-supp').hide();
    
	    // Confirmation suppression 
	    $('.delete-evtstruct').click(function(){
	      var answer = confirm('Etes-vous sur de vouloir supprimer $cc_cettemin et toutes ses associations ?');
	      return answer;
	    });

	});
</script>
AJHE;

include "../inc-header.php";
?>

<div id="ajax-supp">
    <img src="../../img/image-loader.gif" alt="Suppression en cours" /><br/>
    Suppression en cours...
</div>

<form id="adform1" action="domodifajout.php" method="post" class="formA" enctype="multipart/form-data">
<?php if (!$mode_ajout) { ?>
  <div class="actions">
	<a href='supp.php?non_ajax=1&no=<?php if ($id_user) echo $id_user?>' class="boutonbleu ico-supprimer delete-evtstruct">Supprimer <?php echo $cc_cettemin; ?></a>
  </div>
<?php } ?>

  <ul>
<fieldset>
    <li><label for="id_user">Numéro : </label>
	  <input type="text" name="id_user" size="7" class="input verouille" value="<?php echo $id_user; ?>" readonly /></li>
    <li><label for="etat">Etat : <sup>*</sup></label>
	    <select name="etat">
		    <option value="1" <?php echo ($etat==1?"selected":"") ?>>Activé</option>
		    <option value="0" <?php echo ($etat==0?"selected":"") ?>>Désactivé</option>
	    </select></li>
    <li><label for="inscrit_newsletter">Lettre d'info : <sup>*</sup></label>
	    <select name="inscrit_newsletter">
		    <option value="1" <?php echo ($inscrit_newsletter==1?"selected":"") ?>>Inscrit</option>
		    <option value="0" <?php echo ($inscrit_newsletter==0?"selected":"") ?>>Non inscrit</option>
	    </select></li>

    <li><label for="verification_email">Email vérifié :</label>
	    <select name="verification_email">
		    <option value="1" <?php echo ($verification_email==1?"selected":"") ?>>Oui</option>
		    <option value="0" <?php echo ($verification_email==0?"selected":"") ?>>Non</option>
	    </select></li>
	<li><label for="email">Email : <sup>*</sup></label>
	  <input type="text" name="email" size="70" id="mail" class="input" value="<?php echo $email; ?>" /></li>
	  <input type="hidden" name="mail_1_verif" id="mail_1_verif" value="<?php echo $email ?>" id="mail_1_verif" />

	<li><label for="droits">Droits : <sup>*</sup></label>
		<select name="droits">
			<option value="" <?php echo (!$droits?"selected":"") ?>>Utilisateur</option>
			<option value="A" <?php echo ($droits=="A"?"selected":"") ?>>Administrateur</option>
			<option value="E" <?php echo ($droits=="E"?"selected":"") ?>>Editeur</option>
		</select></li>
	<li>
		<label>Ville <sup>*</sup> :</label>
		<input type="text" id="ville" name="ville" value="<?php echo $row[0]['nom_ville_maj']?>" class="cpville" size="35" />
		<img src="<?php echo $root_site; ?>img/tick-vert.png" alt="Ville validée" id="ville-valide" />
		<input type="hidden" name="rech_idville" id="id_ville" size="6" value="<?php echo $row[0]['no_ville']?>"/>
	</li>
	<li>
		<label>Code postal <sup>*</sup> :</label>
		<input type="text" name="cp" size="6" value="<?php echo $row[0]['code_postal']?>" class="cpville" id="cp"/>
	</li>
	<?php
	if(!$mode_ajout)
	    echo "<p>Si rien n'est saisi ci-dessous, le mot de passe restera inchangé.</p>";
	?>
	<li>
		<label for="mdp"><?php if(!$mode_ajout) echo "Nouveau "; ?>Mot de passe :</label>
		<input type="password" name="mdp" id="mdp" />
		<input type="hidden" name="mdp_verif" value="<?php echo $row[0]['mot_de_passe']?>" />
	</li>
	<li>
		<label for="mdp2">Confirmation mot de passe :</label>
		<input type="password" name="mdp2" id="mdp2" />
	</li>
</fieldset>
    </ul>

<?php
    if($mode_ajout)
	echo '<input type="hidden" name="mode_ajout" value="1">';
?>
  <div class="actions"><button type="submit" class="boutonbleu ico-fleche"><?php
    if($mode_ajout)
	echo 'Ajouter';
    else
	echo 'Modifier';
?></button></div>
</form>

<?
if(!$mode_ajout)
{ ?>
<form id="adform2" action="" method="post" class="formA">
<fieldset>
  <legend>Evènements associés</legend>
  <li><label>Evènements :</label>
  <div class="chps_non_input avecliste">
  <div id="les_evts">
	<?php
	    // Affiche les evenements associés
	    // Paramètre : $id_user
	    require("affevts.php");
	?>
    </div>
    </div>
</li>
</fieldset>

<fieldset>
  <legend>Structures associées</legend>
  <li><label>Structures :</label>
  <div class="chps_non_input avecliste">
  <div id="les_structs">
	<?php
	    // Affiche les structures associées
	    require("affstruct.php");
	?>
    </div>
    </div>
</li>
</fieldset>

</form><br/><br/>
<?php
}
?>
    <script type="text/javascript">
    function Tester_mdp()
    {
	    // On a modifié le mail ?
	    if((($("#mdp").val())!="")||($("#mdp2").val()!=""))
	    {
		if(($("#mdp").val())!=($("#mdp2").val()))
		{
		    alert("Vous devez confirmer le mot de passe.");
		    return false;
		}
		else
		{
		    return true;		    
		}
	    }
	    else
	    {
		return true;		    
	    }
    }

    function Tester_modif_mail()
    {
	    // On a modifié le mail ?
	    if(($("#mail").val())!=($("#mail_1_verif").val()))
	    {
		// Le mot de passe doit être changé
		if ((($("#mdp").val())=="")||($("#mdp2").val()=="")||(($("#mdp").val())!=($("#mdp2").val())))
		{
		    alert("Vous devez saisir un nouveau mot de passe, et le confirmer.");
		    return false;
		}
		else
		{
		    return true;		    
		}
	    }
	    else
	    {
		return true;		    
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
		    alert("Veuillez choisir une ville dans la liste.");
		    $('#ville').focus();
		    return false;
	    }
	    else
	    {
		    $('#ville-valide').show("slow");
		    $("#submitform").removeAttr("disabled");
		    return true;
	    }
    }

    $(function() {
	    // Tester idville
	    Tester_idville();

	    $('#adform1').submit(function() {
		// enlève espaces dans mail2
		$("#mail2").val($.trim($("#mail2").val()));
		var validform = false;
		validform = Tester_idville();
		validform = Tester_mdp();
		validform = Tester_modif_mail();
		return validform;
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
					url: "../../01_include/AutoCompletion.php",
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
<?php
include "../inc-footer.php";
} else {
  $_SESSION['message'] .= "Erreur : veuillez sélectionner ".$cc_une." à modifier.";  
  header("location:".$URLadmin."admin.php");
  exit();
}
 
?>
