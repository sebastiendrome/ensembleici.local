<?php
/*****************************************************
Modification d'un statut
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Affichage du message s'il existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);

// Détermination de l'id
if (isset($_GET['id'])) $id_statut = intval($_GET['id']);
$mode_ajout = intval($_GET['ajout']);

if($id_statut||$mode_ajout) {

    if ($mode_ajout)
    {
	$titrepage = "Ajout d'$cc_une";

	// On génère un id
	$id = time();

	// L'id est-il déjà dans la base ?
	do {
	    $sql_existe = "SELECT * FROM `statut` WHERE no=:no_statut";
	    $res_existe = $connexion->prepare($sql_existe);
	    $res_existe->execute(array(':no_statut'=>$id)) or die ("Erreur ".__LINE__." : ".$sql_existe);
	    $nb_user_existe = $res_existe->rowCount();
	  if($nb_user_existe)
	    $id++;
	  else
	    $id_statut = $id;
	} while (!$id_statut);

    }
    else
    {
	$titrepage = "Modification d'$cc_une";

	// Détails
	$sql = "SELECT * FROM `statut` S WHERE no=:no_statut";
	$res = $connexion->prepare($sql);
	$res->execute(array(':no_statut'=>$id_statut)) or die ("Erreur ".__LINE__." : ".$sql);
	$row=$res->fetchAll();

	// Préparation des variables pour affichage
	$libelle = $row[0]['libelle'];
    }

    // Paramètres pour colorbox
    $_SESSION['id_user_passer'] = $id_statut;

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
	<a href='supp.php?non_ajax=1&no=<?php if ($id_statut) echo $id_statut?>' class="boutonbleu ico-supprimer delete-evtstruct">Supprimer <?php echo $cc_cettemin; ?></a>
  </div>
<?php } ?>

  <ul>
<fieldset>
    <li><label for="id_statut">Numéro : </label>
	  <input type="text" name="id_statut" size="7" class="input verouille" value="<?php echo $id_statut; ?>" readonly /></li>
	<li><label for="libelle">Libellé : <sup>*</sup></label>
	  <input type="text" name="libelle" size="70" id="libelle" class="input" value="<?php echo $libelle; ?>" /></li>
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
  <legend>Structures associés</legend>
  <li><label>Structures :</label>
  <div class="chps_non_input avecliste">
  <div id="les_evts">
	<?php
	    // Affiche les structures associées
	    // Paramètre : $id_statut
	    require("affstructs.php");
	?>
    </div>
    </div>
</li>
</fieldset>

</form><br/><br/>
<?php
}

include "../inc-footer.php";
} else {
  $_SESSION['message'] .= "Erreur : veuillez sélectionner ".$cc_une." à modifier.";  
  header("location:".$URLadmin."admin.php");
  exit();
}

?>