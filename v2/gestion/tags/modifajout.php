<?php
/*****************************************************
Modification d'un tag
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Affichage du message s'il existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);

// Détermination de l'id
if (isset($_GET['id'])) $id_tag = intval($_GET['id']);
$mode_ajout = intval($_GET['ajout']);

if($id_tag||$mode_ajout) {

    if ($mode_ajout)
    {
	$titrepage = "Ajout d'$cc_une";

	// On génère un id
	$id = time();
	
	// L'id est-il déjà dans la base ?
	do {
	    $sql_existe = "SELECT * FROM `tag` WHERE no=:no_tag";
	    $res_existe = $connexion->prepare($sql_existe);
	    $res_existe->execute(array(':no_tag'=>$id)) or die ("Erreur 30 : ".$sql_existe);
	    $nb_tag_existe = $res_existe->rowCount();
	  if($nb_tag_existe)
	    $id++;
	  else
	    $id_tag = $id;
	} while (!$id_tag);
    }
    else
    {
	$titrepage = "Modification d'$cc_une";

	// Détails du tag
	$sql="SELECT *
	      FROM `tag` T
	      WHERE T.no=:no_tag";
	$res = $connexion->prepare($sql);
	$res->execute(array(':no_tag'=>$id_tag)) or die ("Erreur 47 : ".$sql);
	$row=$res->fetchAll();

	// Préparation des variables pour affichage
	$titre = htmlspecialchars($row[0]['titre']);
       
    }

    // Paramètres pour colorbox
    $_SESSION['id_tag_passer'] = $id_tag;

     
  // Lignes à ajouter au header
$ajout_header = <<<AJHE
<script type="text/javascript" src="../../js/fonction_auto_presentation.js"></script>
<script>
	$(function() {
   
	    $('#ajax-supp').hide();
	    
	    var no_tag = $id_tag;

	    $(".deletevie").live('click', function() {
		$('#ajax-supp').fadeIn();
		var commentContainer = $(this).parents('li:first');
		var id = $(this).attr("id");
		var string = "no_vie=" + id + "&" + "no_tag=" + no_tag;
		$.ajax({
		   type: "POST",
		   url: "suppvie.php",
		   data: string,
		   cache: false,
		   success: function(){
			commentContainer.fadeOut("slow", function(){\$(this).remove();} );
			$('#ajax-supp').fadeOut();
		  }
		});
		return false;
	    });
	    
	    $(".deletesstag").live('click', function() {
		$('#ajax-supp').fadeIn();
		var commentContainer = $(this).parents('li:first');
		var id = $(this).attr("id");
		var string = "no_sstag=" + id + "&" + "no_tag=" + no_tag;
		$.ajax({
		   type: "POST",
		   url: "suppsstag.php",
		   data: string,
		   cache: false,
		   success: function(){
			commentContainer.fadeOut("slow", function(){\$(this).remove();} );
			$('#ajax-supp').fadeOut();
		  }
		});
		return false;
	    });
	    
	    $(".deleteevt").live('click', function() {
		$('#ajax-supp').fadeIn();
		var commentContainer = $(this).parents('li:first');
		var id = $(this).attr("id");
		var string = "no_evt=" + id + "&" + "no_tag=" + no_tag;
		$.ajax({
		   type: "POST",
		   url: "suppevt.php",
		   data: string,
		   cache: false,
		   success: function(){
			commentContainer.fadeOut("slow", function(){\$(this).remove();} );
			$('#ajax-supp').fadeOut();
		  }
		});
		return false;
	    });

	    // ajout de vie
	    $("#ajoutvie").live('click', function() {
		    $.fn.colorbox({
		      href:"ajoutvie.php",
		      width:"550px",
		      onClosed:function(){
			      $("#les_vies").load("affvies.php", {id:$id_tag});
			      $(".message").load("../inc-message.php");
		      },
		      onComplete : function() {
				    $(this).colorbox.resize();
		      }
		    });
		    return false; 
	    });

	    // ajout de ajoutsstag
	    $("#ajoutsstag").live('click', function() {
		    $.fn.colorbox({
		      href:"ajoutsstag.php",
		      width:"550px",
		      onClosed:function(){
			      $("#les_sstags").load("affsstags.php", {id:$id_tag});
			      $(".message").load("../inc-message.php");
		      },
		      onComplete : function() {
				    $(this).colorbox.resize();
		      }
		    });
		    return false; 
	    });
	    
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
	<a href='supp.php?non_ajax=1&no=<?php if ($id_tag) echo $id_tag?>' class="boutonbleu ico-supprimer delete-evtstruct">Supprimer <?php echo $cc_cettemin; ?></a>
  </div>
<?php } ?>

  <ul>
<fieldset>
    <li><label for="id_tag">Numéro : </label>
	  <input type="text" name="id_tag" size="7" class="input verouille" value="<?php echo $id_tag; ?>" readonly /></li>
      <li><label for="titre">Nom du tag : <sup>*</sup></label>
    	<input type="text" name="titre" size="70" class="input" value="<?php echo $titre; ?>" /></li>
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

<form id="adform1" action="" method="post" class="formA">
<fieldset>
  <legend>Vies associées</legend>
  <li><label>Vies :</label>
  <div class="chps_non_input avecliste">
  <div id="les_vies">
	<?php
	    // Affiche les vies associées
	    // Paramètre : $id_tag
	    require("affvies.php");
	?>
    </div>
     <a href="" id="ajoutvie" title="Associer à une nouvelle vie" class="boutonbleu ico-ajout">Associer une nouvelle vie</a>
    </div>
</li>
</fieldset>

<fieldset>
  <legend>Sous-tags associés</legend>
  <li><label>Sous-tags :</label>
  <div class="chps_non_input avecliste">
  <div id="les_sstags">
	<?php
	    // Affiche les sstags associés
	    require("affsstags.php");
	?>
    </div>
     <a href="" id="ajoutsstag" title="Associer à un nouveau sous tag" class="boutonbleu ico-ajout">Associer un nouveau sous-tag</a>
    </div>
</li>
</fieldset>

<fieldset>
  <legend>Evènements associés</legend>
  <li><label>Evènements :</label>
  <div class="chps_non_input avecliste">
  <div id="les_evts">
	<?php
	    // Affiche les evts associés
	    require("affevts.php");
	?>
    </div>
</div>
</li>
</fieldset>

</form><br/><br/>

<?php
include "../inc-footer.php";
} else {
  $_SESSION['message'] .= "Erreur : veuillez sélectionner ".$cc_une." à modifier.";  
  header("location:".$URLadmin."admin.php");
  exit();
}
 
?>
