<?php
/*****************************************************
Modification d'un sous tags
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Affichage du message s'il existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);

// Détermination de l'id
if (isset($_GET['id'])) $id_sstag = intval($_GET['id']);
$mode_ajout = intval($_GET['ajout']);

if($id_sstag||$mode_ajout) {

    if ($mode_ajout)
    {
	$titrepage = "Ajout d'$cc_une";

	// On génère un id
	$id = time();
	
	// L'id est-il déjà dans la base ?
	do {
	    $sql_existe = "SELECT * FROM `sous_tag` WHERE no=:no_tag";
	    $res_existe = $connexion->prepare($sql_existe);
	    $res_existe->execute(array(':no_tag'=>$id)) or die ("Erreur 30 : ".$sql_existe);
	    $nb_tag_existe = $res_existe->rowCount();
	  if($nb_tag_existe)
	    $id++;
	  else
	    $id_sstag = $id;
	} while (!$id_sstag);
    }
    else
    {
	$titrepage = "Modification d'$cc_une";

	// Détails du tag
	$sql="SELECT *
	      FROM `sous_tag` T
	      WHERE T.no=:no_tag";
	$res = $connexion->prepare($sql);
	$res->execute(array(':no_tag'=>$id_sstag)) or die ("Erreur 47 : ".$sql);
	$row=$res->fetchAll();

	// Préparation des variables pour affichage
	$titre = htmlspecialchars($row[0]['titre']);
       
    }

    // Paramètres pour colorbox
    $_SESSION['id_sstag_passer'] = $id_sstag;

     
  // Lignes à ajouter au header
$ajout_header = <<<AJHE
<script type="text/javascript" src="../../js/fonction_auto_presentation.js"></script>
<script>
	$(function() {
   
	    $('#ajax-supp').hide();
	    
	    var no_sstag = $id_sstag;

	    $(".deletetag").live('click', function() {
		$('#ajax-supp').fadeIn();
		var commentContainer = $(this).parents('li:first');
		var id = $(this).attr("id");
		var string = "no_tag=" + id + "&" + "no_sstag=" + no_sstag;
		$.ajax({
		   type: "POST",
		   url: "supptag.php",
		   data: string,
		   cache: false,
		   success: function(){
			commentContainer.fadeOut("slow", function(){\$(this).remove();} );
			$('#ajax-supp').fadeOut();
		  }
		});
		return false;
	    });
	    
	    $(".deletestruct").live('click', function() {
		$('#ajax-supp').fadeIn();
		var commentContainer = $(this).parents('li:first');
		var id = $(this).attr("id");
		var string = "no_struct=" + id + "&" + "no_sstag=" + no_sstag;
		$.ajax({
		   type: "POST",
		   url: "suppstruct.php",
		   data: string,
		   cache: false,
		   success: function(){
			commentContainer.fadeOut("slow", function(){\$(this).remove();} );
			$('#ajax-supp').fadeOut();
		  }
		});
		return false;
	    });

	    // ajout de tag
	    $("#ajouttag").live('click', function() {
		    $.fn.colorbox({
		      href:"ajouttag.php",
		      width:"550px",
		      onClosed:function(){
			      $("#les_tags").load("afftags.php", {id:$id_sstag});
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
	<a href='supp.php?non_ajax=1&no=<?php if ($id_sstag) echo $id_sstag?>' class="boutonbleu ico-supprimer delete-evtstruct">Supprimer <?php echo $cc_cettemin; ?></a>
  </div>
<?php } ?>

  <ul>
<fieldset>
    <li><label for="id_sstag">Numéro : </label>
	  <input type="text" name="id_sstag" size="7" class="input verouille" value="<?php echo $id_sstag; ?>" readonly /></li>
      <li><label for="titre">Nom du sous-tag : <sup>*</sup></label>
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
  <legend>Tags associés</legend>
  <li><label>Tags :</label>
  <div class="chps_non_input avecliste">
  <div id="les_tags">
	<?php
	    // Affiche les tags associés
	    require("afftags.php");
	?>
    </div>
     <a href="" id="ajouttag" title="Associer à un nouveau tag" class="boutonbleu ico-ajout">Associer un nouveau tag</a>
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
	    require("affstructs.php");
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
