<?php
/*****************************************************
Gestion des contenus des blocs
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Affichage du message si existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);

// Détermination de l'id
$id_bloc = intval($_GET['id']);
if($id_bloc)
{

// Détails du bloc
$sql="SELECT *
      FROM `contenu_blocs`
      WHERE no=:no_bloc";
$res = $connexion->prepare($sql);
$res->execute(array(':no_bloc'=>$id_bloc)) or die ("Erreur 23 : ".$sql);
$row=$res->fetchAll();
$nom_bloc = $row[0]['nom_bloc'];
$titre = $row[0]['titre'];
$contenu = $row[0]['contenu'];
$titrepage = "Modification ".$nom_bloc;

// Lignes à ajouter au header
$ajout_header = <<<AJHE
<script type="text/javascript" src="../../js/ckeditor/ckeditor.js"></script><script type="text/javascript">
  window.onload = function()
  {	
	  CKEDITOR.replace('contenu',{toolbar:'AutoA',uiColor:'#F0EDEA',language:'fr',width:'520',height:'200',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
  };</script>
AJHE;

include "../inc-header.php";
?>


<form id="adform1" action="domodif.php" method="post" class="formA">
<input type="hidden" name="nom_bloc" value="<?php echo $nom_bloc; ?>" />
<input type="hidden" name="id_bloc" value="<?php echo $id_bloc; ?>" />
  <ul>
<fieldset>
  <li><label for="titre">Titre : <sup>*</sup></label>
    	<input type="text" name="titre" size="70" class="input" value="<?php echo $titre; ?>" /></li>
  <li><label for="contenu">Contenu : </label>
    	<textarea name="contenu" class="CKEDITOR" ><?php echo $contenu; ?></textarea></li>
</fieldset>
  </ul>

  <div class="actions"><button type="submit" class="boutonbleu ico-fleche">Modifier</button></div>
</form>


<?php
  include "../inc-footer.php";
  
	}	else {
	$_SESSION['message'] .= "Erreur : veuillez sélectionner ".$cc_une." à modifier.";  
  header("location:admin.php");
	exit();
	}
 
?>
