<?php
/*****************************************************
Modification d'une structure
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Affichage du message s'il existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);

// Détermination de l'id
if (isset($_GET['id'])) $id_structure = intval($_GET['id']);
$mode_ajout = intval($_GET['ajout']);

if($id_structure||$mode_ajout) {

    // Statut
    $sql_statut="SELECT * FROM statut ORDER BY libelle";
    $res_statut = $connexion->prepare($sql_statut);
    $res_statut->execute() or die ("Erreur 41 : ".$sql_statut);
    $tab_statut=$res_statut->fetchAll();

    // Rôles
    $sql_role = "SELECT * FROM role ORDER BY libelle";
    $res_role = $connexion->prepare($sql_role);
    $res_role->execute() or die ("Erreur 59 : ".$sql_role);
    $tab_role=$res_role->fetchAll();
	
    if ($mode_ajout)
    {
	$titrepage = "Ajout d'$cc_une";

	// On génère un id
	$id = time();
	
	// L'id est-il déjà dans la base ?
	do {
	    $sql_existe = "SELECT * FROM `structure` WHERE no=:no_structure";
	    $res_existe = $connexion->prepare($sql_existe);
	    $res_existe->execute(array(':no_structure'=>$id)) or die ("Erreur 42 : ".$sql_existe);
	    $nb_struct_existe = $res_existe->rowCount();
	  if($nb_struct_existe)
	    $id++;
	  else
	    $id_structure = $id;
	} while (!$id_structure);

	$date_creation = date("d/m/Y");
	$email_utilisateur_creation = addslashes($_SESSION['UserConnecte_email']);
	$no_utilisateur_creation = $_SESSION['UserConnecte_id'];
	
	// La ville de l'utilisateur
	$sql_user="SELECT * FROM `utilisateur` U, `villes` V
		    WHERE U.no_ville = V.id
		    AND U.no = :iduser";
	$res_user = $connexion->prepare($sql_user);
	$res_user->execute(array(':iduser'=>$no_utilisateur_creation));
	$tab_user = $res_user->fetch(PDO::FETCH_ASSOC);
	$nom_ville_utilisateur_creation = $tab_user["nom_ville_url"];
	$code_postal_utilisateur_creation = $tab_user["code_postal"];

	$etat = 1; // Activé
	$validation = 1; // Validé

    }
    else
    {
	// Modification d'une structure
	$titrepage = "Modification d'$cc_une";

	// Détails de la structure
	$sql="SELECT *, B.libelle AS libelle_statut
	      FROM `structure` A, `statut` B
	      WHERE A.no=:no_structure
	      AND A.`no_statut` = B.`no`";
	$res = $connexion->prepare($sql);
	$res->execute(array(':no_structure'=>$id_structure)) or die ("Erreur 23 : ".$sql);
	$row=$res->fetchAll();
	
	// Contacts
	$sql_structure_contact="SELECT *
				FROM structure_contact
				WHERE no_structure=:no
				ORDER BY no_contact DESC";
	$res_structure_contact = $connexion->prepare($sql_structure_contact);
	$res_structure_contact->execute(array(':no'=>$id_structure)) or die ("Erreur 64 : ".$sql_structure_contact);
	$tab_structure_contact=$res_structure_contact->fetchAll();
	
	// Premier contact
	$sql_contact = "SELECT * FROM contact WHERE no=:no";
	$res_contact = $connexion->prepare($sql_contact);
	$res_contact->execute(array(':no'=>$tab_structure_contact[0]['no_contact'])) or die ("Erreur 53 : ".$sql_contact);
	$tab_contact=$res_contact->fetchAll();
	
	// Infos de la ville
	$sql_villes = "SELECT * FROM villes WHERE id=:id";
	$res_villes = $connexion->prepare($sql_villes);
	$res_villes->execute(array(":id"=>$row[0]['no_ville'])) or die ("Erreur 65 : ".$sql_villes);
	$tab_villes=$res_villes->fetchAll();
    
	// Créateur de la structure
	$sql_createur = "SELECT email, nom_ville_maj, code_postal
			FROM utilisateur U, villes V
			WHERE U.no_ville = V.id
			AND no=:id";
	$res_createur = $connexion->prepare($sql_createur);
	$res_createur->execute(array(":id"=>$row[0]['no_utilisateur_creation'])) or die ("Erreur 71 : ".$sql_createur);
	$tab_createur = $res_createur->fetchAll();
	$email_utilisateur_creation = $tab_createur[0]['email'];
	$code_postal_utilisateur_creation = $tab_createur[0]['code_postal'];
	$nom_ville_utilisateur_creation = $tab_createur[0]['nom_ville_maj'];
    
	// Préparation des variables pour affichage
	$nom = htmlspecialchars($row[0]['nom']);
	$sous_titre = htmlspecialchars($row[0]["sous_titre"]);
	$statut = $row[0]['no_statut'];
	$nb_aime = $row[0]['nb_aime'];
	$libelle_statut = $row[0]['libelle_statut'];
	$description = utf8_encode($row[0]['description']);
	$etat = $row[0]['etat'];
	$validation = $row[0]['validation'];
	$nomadresse = htmlspecialchars($row[0]['nomadresse']);
	$adresse = htmlspecialchars($row[0]['adresse']);
	$adresse_complementaire = htmlspecialchars($row[0]['adresse_complementaire']);
	$telephone = $row[0]['telephone'];
	$telephone2 = $row[0]['telephone2'];
	$fax = $row[0]['fax'];
	$email = $row[0]['email'];
	$site_internet = $row[0]['site_internet'];
	$facebook = $row[0]['facebook'];
	$illustration = $row[0]['url_logo'];
	$copyright = htmlspecialchars($row[0]['copyright']);
	if($row[0]['date_creation']=="0000-00-00")
	  $date_creation="";
	else
	  $date_creation = datefr($row[0]['date_creation']);
	  
	// Contact
	$contact_no = $tab_contact[0]['no'];
	$contact_nom = htmlspecialchars($tab_contact[0]['nom']);
	$contact_telephone = $tab_contact[0]['telephone'];
	$contact_email = $tab_contact[0]['email'];
	$contact_role = $tab_structure_contact[0]['no_role'];
    
	// Préparation du lien pour voir
	if (($etat)&&($tab_villes[0]['id']))
	{
	    $titre_pour_lien = coupe_chaine($nom,130,false);
	    if ($libelle_statut)
	      $titre_pour_lien = $libelle_statut."-".$titre_pour_lien;
	    // Lien vers le détails de l'évenement. 
	    $lien_voir = strtolower($root_site."structure.".url_rewrite($tab_villes[0]['nom_ville_maj']).".".url_rewrite($titre_pour_lien).".".$tab_villes[0]['id'].".".$id_structure.".html");
	}
    }
    
    // Paramètres pour colorbox Ajout d'un ss-tag
    $_SESSION['id_structure_passer'] = $id_structure;

   
  // Lignes à ajouter au header
$ajout_header = <<<AJHE
<script type="text/javascript" src="../../js/ckeditor/ckeditor.js"></script><script type="text/javascript">
  window.onload = function()
  {
	  CKEDITOR.replace('description',{toolbar:'AutoA',uiColor:'#F0EDEA',language:'fr',width:'520',height:'200',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
  };</script>

<script>
	$(function() {

	    // Suppression d'une illustration
	    $(".delete").click(function() {
		if(confirm("Etes-vous sur de vouloir supprimer cette illustration ?" )) {
		    var id = $(this).attr("id");
		    delete_img(id);
		}
	    });
	    function delete_img(id)
	    {
		    var commentContainer = $("#"+id).parents('div:first');
		    var string = "id=" + id;
		    
		    $.ajax({
		       type: "POST",
		       url: "suppimg.php",
		       data: string,
		       cache: false,
		       success: function(){
			    commentContainer.fadeOut("slow", function(){\$(this).remove();} );
			    $('.message').html("Illustration supprimée avec succès.");
			    $('.message').slideDown("slow");                                                       
		      }
		    });
		    
		    return false;
	    }
	    
	    $('#ajax-supp').hide();
	    
		$(".deletetag").live('click', function() {
		    $('#ajax-supp').fadeIn();
		    var commentContainer = $(this).parents('li:first');
		    var id = $(this).attr("id");
		    var no_structure = $id_structure;
		    var string = "no_sous_tag=" + id + "&" + "no_structure=" + no_structure;
			    
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

	    	    
	    $(".deleteliaison").live('click', function() {
		$('#ajax-supp').fadeIn();
		var commentContainer = $(this).parents('li:first');
		var id = $(this).attr("id");
		var string = "no_liaison=" + id;
		$.ajax({
		   type: "POST",
		   url: "supp_liaison.php",
		   data: string,
		   cache: false,
		   success: function(retour){
			$('#ajax-supp').fadeOut();
			if (retour=="ok") {
			    // Recharge les liaisons
			    $("#les_liaisons").load("aff_liaisons.php", {id:$id_structure});
			}
			$(".message").load("../inc-message.php");
		   }
		});
		return false;
	    });
	    
	    // colorbox sur .iframe
	    $("#ajoutsstag").live('click', function() {
		    $.fn.colorbox({
		      href:"ajoutsstag.php",
		      width:"550px",
		      onClosed:function(){
			      $("#les_sstags").load("affsstag.php", {id:$id_structure});
			      $(".message").load("../inc-message.php");
		      },
		      onComplete : function() { 
				    $(this).colorbox.resize();
		      }
		    });
		    return false; 
	    });  

	    // colorbox sur l'ajout de liaison
	    $("#ajoutliaison").live('click', function() {
		    $.fn.colorbox({
		      href:"ajoutliaison.php",
		      data: "type_A=$type_source&no_A=$id_structure",
		      width:"750px",
		      onClosed:function(){
			      $("#les_liaisons").load("aff_liaisons.php", {id:$id_structure});
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
	      var answer = confirm('Etes-vous sur de vouloir supprimer $cc_cettemin ?');
	      return answer;
	    });

	    $(".delete_version").live('click', function() {
		var answer = confirm('Etes-vous sur de vouloir supprimer cette version ?');
		if (answer == true)
		{
		  $('#ajax-supp').fadeIn();
		  var commentContainer = $(this).parents('tr:first');
		  var id = $(this).attr("id");
		  var no_structure = $id_structure;
		  var string = "no_item=" + id + "&" + "no_structure=" + no_structure;
		  $.ajax({
		     type: "POST",
		     url: "supp_version.php",
		     data: string,
		     cache: false,
		     success: function(){
			  commentContainer.fadeOut("slow", function(){\$(this).remove();} );
			  $('#ajax-supp').fadeOut();
			  $(".message").load("../inc-message.php");
		    }
		  });
		}
		return false;
	    });
	    
	    $(".restaurer_version").live('click', function() {
		var answer = confirm("Etes-vous sur de vouloir restaurer cette version ? Attention, la version actuelle sera écrasée");
		if (answer == true)
		{
		  $('#ajax-supp').fadeIn();
		  var commentContainer = $(this).parents('tr:first');
		  var id = $(this).attr("id");
		  var no_structure = $id_structure;
		  var string = "no_item=" + id + "&no_structure=" + no_structure + "&type_es=$type_source";
		  $.ajax({
		     type: "POST",
		     url: "restaurer_version.php",
		     data: string,
		     cache: false,
		     success: function(retour){
			if (retour=="ok") {
			    window.location.reload();
			} else {
			    alert(retour);
			}
		    }
		  });
		}
		return false;
	    });
	
	    // colorbox sur l'affichage d'une version
	    $(".voir_version").live('click', function() {
		    var id = $(this).attr("id");
		    var no_structure = $id_structure;
		    var string = "no_item=" + id + "&" + "no_structure=" + no_structure;
		    $.fn.colorbox({
		      href:"aff_une_version.php",
		      width:"750px",
		      height:"650px",
		      data:string,
		      onClosed:function(){
			      $("#les_versions").load("aff_versions.php", {id:$id_structure});
			      $(".message").load("../inc-message.php");
		      }
		    });
		    return false; 
	    });

	});
</script>

AJHE;

include "../inc-header.php";
?>

<p class="mess"></p>
	
<div id="ajax-supp">
	<img src="../../img/image-loader.gif" alt="Suppression en cours" /><br/>
	Suppression du ss-tag en cours...
</div>

<form id="adform1" action="domodifajout.php" method="post" class="formA" enctype="multipart/form-data">
<div class="actions">
<?php if (!$mode_ajout) { ?>
	<a href='supp.php?non_ajax=1&no_structure=<?php if ($id_structure) echo $id_structure?>' class="boutonbleu ico-supprimer delete-evtstruct">Supprimer <?php echo $cc_cettemin; ?></a>
	<?php if ($lien_voir) { ?>
	<a href="<?php echo $lien_voir; ?>" class="boutonrouge ico-loupe-rge" title="Voir <?php echo $cc_cettemin; ?>" target="_blank">Voir sur le site</a>
	<?php }
	} ?>
	<button type="submit" class="boutonbleu ico-modifier"><?php
	    if($mode_ajout)
		echo 'Ajouter';
	    else
		echo 'Modifier';
	?></button>
</div>

  <ul>
<fieldset>
  <legend>Généralités</legend>
  <li><label for="nom">Nom : <sup>*</sup></label>
    	<input type="text" name="nom" size="70" class="input" value="<?php echo $nom; ?>" /></li>
  <li><label for="id_structure">Numéro : </label>
    	<input type="text" name="id_structure" size="7" class="input verouille" value="<?php echo $id_structure; ?>" readonly /></li>
  <li><label for="date_creation">Date de création : </label>
    	<input type="text" name="date_creation" size="7" class="input verouille" value="<?php echo $date_creation; ?>" readonly />
	<?php
	
	if ($email_utilisateur_creation)
	  echo "par $email_utilisateur_creation ($nom_ville_utilisateur_creation, $code_postal_utilisateur_creation)";
	else
	  echo " (Importation ou utilisateur supprimé)";
	?>
  </li>
	
<li><label for="etat">Etat : <sup>*</sup></label>
  	<select name="etat">
  		<option value="1" <?php echo ($etat==1?"selected":"") ?>>Activé</option>
  		<option value="0" <?php echo ($etat==0?"selected":"") ?>>Désactivé</option>
  	</select></li>
<li><label for="validation">Validation éditoriale : <sup>*</sup></label>
  	<select name="validation">
  		<option value="0" <?php echo ($validation==0?"selected":"") ?>>Non validé</option>
  		<option value="1" <?php echo ($validation==1?"selected":"") ?>>Validé</option>
  		<option value="2" <?php echo ($validation==2?"selected":"") ?>>Modifié, non validé</option>
  	</select></li>
    <li><label for="nb_aime">Nombre de like : </label>
    <input type="text" name="nb_aime" value="<?php echo $nb_aime?>" size="3"></li>
    <input type="hidden" name="validation_ancien" value="<?php echo $validation ?>">
  <li><label for="sous_titre">Sous-titre : </label>
    	<input type="text" name="sous_titre" size="70" class="input" value="<?php echo $sous_titre; ?>" /></li>
  <li><label for="no_statut">Statut : <sup>*</sup></label>
    <select name="no_statut">
	    <?php
		    for($indice_statut=0;$indice_statut<count($tab_statut); $indice_statut++)
		    {
			    if($tab_statut[$indice_statut]['no']==$statut)
			    {
				    echo "<option value=\"".$tab_statut[$indice_statut]['no']."\" selected>".$tab_statut[$indice_statut]['libelle']."</option>";
			    }
			    else
			    {
				    echo "<option value=\"".$tab_statut[$indice_statut]['no']."\" >".$tab_statut[$indice_statut]['libelle']."</option>";
			    }
		    
		    }						
	    ?>
    </select></li>

    <li><label for="site_internet">Site internet <sup>avec http://</sup> : </label>
    <input type="text" name="site_internet" value="<?php echo $site_internet?>" size="35">
    <?php
    // Aperçu du lien
    if ($site_internet)
    {
      echo "<a href=\"".$site_internet."\" target=\"_blank\" class=\"Voir le site internet\">";
      echo "<img src=\"".$root_site."img/admin/icoad-rech.png\">";
      echo "</a>";
    }
    ?>
    </li>
    <li><label for="facebook">Lien facebook <sup>avec http://</sup> : </label>
    <input type="text" name="facebook" value="<?php echo $facebook?>" size="35">
    <?php
    // Aperçu du lien
    if ($facebook)
    {
      echo "<a href=\"".$facebook."\" target=\"_blank\" class=\"Voir le facebook\">";
      echo "<img src=\"".$root_site."img/admin/icoad-rech.png\">";
      echo "</a>";
    }
    ?>
    </li>

</fieldset>

<fieldset>
  <legend>Sous-tags</legend>
  <li><label>Sous-tags : <sup>(Tag associés)</sup></label>
  <div class="chps_non_input avecliste">
  <div id="les_sstags">
<?php
    // Affiche les sous-tags
    // Paramètre : $id_structure
    require("affsstag.php");
?>
    </div>
     <a href="" id="ajoutsstag" title="Ajouter des sous-tags" class="boutonbleu ico-ajout">Ajouter des sous-tags</a>
    </div>
</li>
</fieldset>
<fieldset>
<legend>Lieu</legend>
    <li><label for="nomadresse">Nom du lieu :</label>
    <input size="35" type="text" name="nomadresse" value="<?php echo $nomadresse; ?>"></li>
    <li><label for="adresse">Adresse :</label>
    <input type="text" name="adresse" value="<?php echo $adresse?>" size="35"></li>
    <li><label for="adresse_complementaire">Adresse complémentaire :</label>
    <input type="text" name="adresse_complementaire" value="<?php echo $adresse_complementaire?>" size="35"></li>

    <li><label>Code postal <sup>*</sup> :</label>
    <input type="text" name="cp" id="cp" size="6" value="<?php echo $tab_villes[0]['code_postal']?>" class="cpville" /></li>

    <li><label>Ville <sup>*</sup>
    :</label> <input type="text"
    class="validate[required] cpville"
    name="ville" id="ville" value="<?php echo
    $tab_villes[0]['nom_ville_maj']?>" size="35" /> <img src="<?php echo $root_site; ?>img/tick-vert.png" alt="Ville validée" id="ville-valide" /></li>
    
    <input type="hidden" name="id_ville" id="id_ville" value="<?php echo $tab_villes[0]['id']?>"/>

    <li><label for="telephone">T&eacute;l&eacute;phone principal :</label>
    <input type="text" name="telephone" value="<?php echo $telephone?>"></li>
    <li><label for="telephone2">Mobile :</label>
    <input type="text" name="telephone2" value="<?php echo $telephone2?>"></li>
    <li><label for="fax">Fax :</label>
    <input type="text" name="fax" value="<?php echo $fax?>"></li>
    <li><label for="email">Email :</label>
    <input type="text" name="email" value="<?php echo $email?>" size="35"></li>
</fieldset>
<fieldset>
  <legend>Contact</legend>
    <input type="hidden" name="contact_no" value="<?php echo $contact_no?>">
    <li><label for="contact_nom">Personne &agrave; contacter :</label>
      <input type="text" name="contact_nom" value="<?php echo $contact_nom?>">
    </li>
    <li><label for="contact_role">R&ocirc;le du contact :</label>
      <select name="contact_role">
	    <option value="0" selected></option>
	    <?php
		    for($indice_role=0; $indice_role<count($tab_role); $indice_role++)
		    {
			    if($tab_role[$indice_role]['no']==$contact_role)
			    {
				    echo "<option value=\"".$tab_role[$indice_role]['no']."\" selected>".$tab_role[$indice_role]['libelle']."</option>";
			    }
			    else
			    {
				    echo "<option value=\"".$tab_role[$indice_role]['no']."\">".$tab_role[$indice_role]['libelle']."</option>";
			    }
		    }
	    ?>	
    </select></li>
    <li><label>T&eacute;l&eacute;phone contact :</label>
    <input type="text" name="contact_telephone" value="<?php echo $contact_telephone?>"></li>
    <li><label>Email contact :</label>
    <input type="text" name="contact_email" value="<?php echo $contact_email?>" size="35"></li>
</fieldset>
<fieldset>
  <legend>Descriptions</legend>
<li><label for="description">Description : </label>
    	<textarea name="description" class="CKEDITOR" ><?php echo $description; ?></textarea></li>
</fieldset>

<fieldset>
  <legend>Illustration</legend>
<li><label for="description">Illustration : </label>
  <div class="chps_non_input">
<?php
  // Affiche l'image
  if ($illustration)
  {
    echo "<input type=\"hidden\" name=\"url_logo\" value=\"".$illustration."\"/>";
    echo "<div><div class=\"illustr\">";

    if (fichier_existant($root_site.$illustration))
    {
      // Image existante dans ce dossier
      echo "<a href=\"".$root_site.$illustration."\" class=\"agrandir\">";
      echo "<img src=\"".$root_site."miniature.php?uri=".$illustration."&method=fit&w=150&h=150\" />";
    }
    else
    {
      // Image inexistante => on test dans un autre dossier
      if ($root_site == $root_site_dev)
        $autre_root_site = $root_site_prod;
      else
        $autre_root_site = $root_site_dev;
      echo "<a href=\"".$autre_root_site.$illustration."\" class=\"agrandir\">";
      echo "<img src=\"".$autre_root_site."miniature.php?uri=".$illustration."&method=fit&w=150&h=150\" />";
    }

    echo "</a>";  
    echo "</div>";
    echo "<a href='#' id=\"".$id_structure."\" class=\"delete\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer l'image\" title=\"Supprimer l'image\" height=\"16\" width=\"16\" /></a></div>\n";
    echo "<br/><input type=\"file\" name=\"illustration\" />";

  }
  else
  {
    echo "Aucune.";
    echo "<br/><input type=\"file\" name=\"illustration\" />";
  }
?></div>    	
</li>
    <li><label for="copyright">Copyright :</label>
    <input type="text" name="copyright" value="<?php echo $copyright?>" size="35"></li>
</fieldset>

<fieldset>
  <legend>Laisons</legend>
  
  <div id="les_liaisons">
	<?php
	    // Affiche les liaisons
	    // Paramètre : $id_source et $type_source (config.php)
	    $id_source = $id_structure;
	    require("aff_liaisons.php");
	?>
    </div>
    <div class="boutons_centre">
	<a href="" id="ajoutliaison" title="Ajouter une liaison" class="boutonbleu ico-ajout">Ajouter une liaison</a>
    </div>
</fieldset>

<fieldset>
  <legend>Versions</legend>
  <li><label>Versions : </label>
  <div class="chps_non_input avecliste">
    <div id="les_versions">
	  <?php
	      // Affiche les versions
	      // Paramètre : $id_structure
	      require("aff_versions.php");
	  ?>
      </div>
    </div>
</li>
</fieldset>
    </ul>
    <input type="hidden" name="no_utilisateur_creation" value="<?php echo $no_utilisateur_creation?>">
<?php
    if($mode_ajout)
	echo '<input type="hidden" name="mode_ajout" value="1">';
?>
  <div class="actions"><button type="submit" class="boutonbleu ico-modifier"><?php
    if($mode_ajout)
	echo 'Ajouter';
    else
	echo 'Modifier';
?></button></div>

</form>

  
  
  <script type="text/javascript">
	  function Tester_idville()
	  {
		  var valiv = $("#id_ville").val();
		  if($.trim(valiv)=="")
		  {
			  $('#ville-valide').hide();
			  $("#cp").val('');
			  $("#ville").val('');
			  return false;
		  }
		  else
		  {
			  $('#ville-valide').show("slow");
			  return true;
		  }
	  }
  
	  $(document).ready(function() {
		  // Tester idville
		  Tester_idville();
  
		  $('#adform1').submit(function() {
			  return Tester_idville();
		  });
  
		  // Validation de la ville
		  $('.cpville').change(function() {
			  $("#id_ville").val("");
		  });
	  });
  
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
			  Tester_idville();
		  }
      });
  </script>



<?php
include "../inc-footer.php";
} else {
  $_SESSION['message'] .= "Erreur : veuillez sélectionner ".$cc_une." à modifier.";  
  header("location:".$URLadmin.$rep."admin.php");
  exit();
}
 
?>
