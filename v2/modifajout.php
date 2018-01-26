<?php
/*****************************************************
Modification d'un évènement
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Affichage du message s'il existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);

// Détermination de l'id
if (isset($_GET['id'])) $id_event = intval($_GET['id']);
$mode_ajout = intval($_GET['ajout']);

if($id_event||$mode_ajout) {

    // Genres
    $sql_genre="SELECT * FROM genre ORDER BY libelle";
    $res_genre = $connexion->prepare($sql_genre);
    $res_genre->execute() or die ("Erreur 41 : ".$sql_genre);
    $tab_genre=$res_genre->fetchAll();
    
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
	    $sql_existe = "SELECT * FROM `evenement` WHERE no=:no_event";
	    $res_existe = $connexion->prepare($sql_existe);
	    $res_existe->execute(array(':no_event'=>$id)) or die ("Erreur 31 : ".$sql_existe);
	    $nb_evts_existe = $res_existe->rowCount();
	  if($nb_evts_existe)
	    $id++;
	  else
	    $id_event = $id;
	} while (!$id_event);

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

	$validation = 1; // Validé
	$etat = 1; // Activé

    }
    else
    {
	// Modification d'un évènement
	$titrepage = "Modification d'$cc_une";

	// Détails de l'évenement
	$sql="SELECT *, G.libelle AS libelle_genre
	      FROM `evenement` E
	      LEFT JOIN `genre` G ON E.`no_genre` = G.`no`
	      WHERE E.no=:no_event";
	$res = $connexion->prepare($sql);
	$res->execute(array(':no_event'=>$id_event)) or die ("Erreur 23 : ".$sql);
	$row=$res->fetchAll();
       
	// Tags
	$sql_tag="SELECT no, titre
		    FROM `evenement_tag` E, `tag` T
		    WHERE E.no_tag = T.no
		    AND no_evenement=:no";
	$res_tag = $connexion->prepare($sql_tag);
	$res_tag->execute(array(':no'=>$id_event)) or die ("Erreur 30 : ".$sql_tag);
	$tab_tag=$res_tag->fetchAll();
    
	// Contacts
	$sql_evenement_contact="SELECT *
				FROM evenement_contact
				WHERE no_evenement=:no
				ORDER BY no_contact DESC";
	$res_evenement_contact = $connexion->prepare($sql_evenement_contact);
	$res_evenement_contact->execute(array(':no'=>$id_event)) or die ("Erreur 64 : ".$sql_evenement_contact);
	$tab_evenement_contact=$res_evenement_contact->fetchAll();
	
	// Premier contact
	$sql_contact = "SELECT * FROM contact WHERE no=:no";
	$res_contact = $connexion->prepare($sql_contact);
	$res_contact->execute(array(':no'=>$tab_evenement_contact[0]['no_contact'])) or die ("Erreur 53 : ".$sql_contact);
	$tab_contact=$res_contact->fetchAll();
	
	// Infos de la ville
	$sql_villes = "SELECT * FROM villes WHERE id=:id";
	$res_villes = $connexion->prepare($sql_villes);
	$res_villes->execute(array(":id"=>$row[0]['no_ville'])) or die ("Erreur 65 : ".$sql_villes);
	$tab_villes=$res_villes->fetchAll();
    
	// Créateur de l'evt
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
	$no_utilisateur_creation = $row[0]['no_utilisateur_creation'];

	// Préparation des variables pour affichage
	$titre = htmlspecialchars($row[0]['titre']);
	$genre = $row[0]['no_genre'];
	$libelle_genre = $row[0]['libelle_genre'];
	$description = utf8_encode($row[0]['description']);
	$description_complementaire = utf8_encode($row[0]['description_complementaire']);
	$etat = $row[0]['etat'];
	$validation = $row[0]['validation'];
	$nb_aime = $row[0]['nb_aime'];
	$nomadresse = htmlspecialchars($row[0]['nomadresse']);
	$adresse = htmlspecialchars($row[0]['adresse']);
	$telephone = $row[0]['telephone'];
	$telephone2 = $row[0]['telephone2'];
	$email = $row[0]['email'];
	$site = $row[0]['site'];
	$illustration = $row[0]['url_image'];
	$copyright = htmlspecialchars($row[0]['copyright']);
	$sous_titre = htmlspecialchars($row[0]["sous_titre"]);
	$source_nom = $row[0]["source_nom"];
	if($row[0]['date_debut']=="0000-00-00")
	  $date_debut_event="";
	else
	  $date_debut_event = datefr($row[0]['date_debut']);
	if($row[0]['date_fin']=="0000-00-00")
	  $date_fin_event="";
	else
	  $date_fin_event = datefr($row[0]['date_fin']);
	  
	if(($row[0]['heure_debut']== NULL) or ($row[0]['heure_debut']== "") )
	  $heure_debut_event="";
	else
	  $heure_debut_event = $row[0]['heure_debut'];  
	  
	if(($row[0]['heure_fin']== NULL) or ($row[0]['heure_fin']== "") )
	  $heure_fin_event="";
	else
	  $heure_fin_event = $row[0]['heure_fin'];   
	  
	  
	if($row[0]['date_creation']=="0000-00-00")
	  $date_creation="";
	else
	  $date_creation = datefr($row[0]['date_creation']);
	 	

	// Structures liées
	$no_structure = $row[0]['no_structure'];
	if($no_structure)
	{
	    $sql_str="SELECT nom
			FROM `structure`
			WHERE  no=:id";
	    $res_strlie = $connexion->prepare($sql_str);
	    $res_strlie->execute(array(":id"=>$no_structure)) or die ("Erreur ".__LINE__." : ".$sql_str);
	    $tab_strlie = $res_strlie->fetchAll();
	    $structure_liee_nom = $tab_strlie[0]['nom']." ($no_structure)";
	}
	  
	// Contact
	$contact_no = $tab_contact[0]['no'];
	$contact_nom = htmlspecialchars($tab_contact[0]['nom']);
	$contact_telephone = $tab_contact[0]['telephone'];
	$contact_email = $tab_contact[0]['email'];
	$contact_role = $tab_evenement_contact[0]['no_role'];
    
	// Préparation du lien pour voir
	if (($etat)&&($tab_villes[0]['id']))
	{
	    $titre_pour_lien = coupe_chaine($titre,130,false);
	    if ($libelle_genre)
	      $titre_pour_lien = $libelle_genre."-".$titre_pour_lien;
	    // Lien vers le détails de l'évenement. 
	    $lien_voir = strtolower($root_site."evenement.".url_rewrite($tab_villes[0]['nom_ville_maj']).".".url_rewrite($titre_pour_lien).".".$tab_villes[0]["id"].".".$id_event.".html");
	}


    }

    // Paramètres pour colorbox Ajout d'un tag
    $_SESSION['id_event_passer'] = $id_event;

     
  // Lignes à ajouter au header
$ajout_header = <<<AJHE
<script type="text/javascript" src="../../js/fonction_auto_presentation.js"></script>
<script type="text/javascript" src="../../js/ckeditor/ckeditor.js"></script><script type="text/javascript">
  window.onload = function()
  {	
	  CKEDITOR.replace('description',{toolbar:'AutoA',uiColor:'#F0EDEA',language:'fr',width:'520',height:'200',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
	  CKEDITOR.replace('description_complementaire',{toolbar:'AutoA',uiColor:'#F0EDEA',language:'fr',width:'520',height:'200',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
  };</script>

<script>
	$(function() {
	  /* jQuery UI date picker français */
	  /* Written by Keith Wood (kbwood{at}iinet.com.au) and Stéphane Nahmani (sholby@sholby.net). */
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
  
	  $( '#date_debut' ).datepicker({
		  defaultDate: "+1w",
		  changeMonth: true,
		  onSelect: function( selectedDate ) {
			  $( "#date_fin" ).datepicker( "option", "minDate", selectedDate );
		  }
	  });
	  $( '#date_fin' ).datepicker({
		  defaultDate: "+1w",
		  changeMonth: true,
		  onSelect: function( selectedDate ) {
			  $( "#date_debut" ).datepicker( "option", "maxDate", selectedDate );
		  }
	  });
	  
	  $("#heure_debut").timepicker({
            timeText: 'Heure saisie',
            hourText: 'Heures',             
            minuteText: 'Minutes',
            stepMinute: 5
          });
	  
	  $("#heure_fin").timepicker({
            timeText: 'Heure saisie',
            hourText: 'Heures',             
            minuteText: 'Minutes',
            stepMinute: 5
          });

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
		var no_evenement = $id_event;
		var string = "no_tag=" + id + "&" + "no_evenement=" + no_evenement;
		$.ajax({
		   type: "POST",
		   url: "supptag.php",
		   data: string,
		   cache: false,
		   success: function(retour){
			$('#ajax-supp').fadeOut();
			if (retour=="ok") {
			    commentContainer.fadeOut("slow", function(){\$(this).remove();} );
			}
			$(".message").load("../inc-message.php");
			$('.message').slideDown("slow");
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
			    $("#les_liaisons").load("aff_liaisons.php", {id:$id_event});
			}
			$(".message").load("../inc-message.php");
		   }
		});
		return false;
	    });

	    // colorbox sur l'ajout de tag
	    $("#ajouttag").live('click', function() {
		    $.fn.colorbox({
		      href:"ajouttag.php",
		      width:"550px",
		      onClosed:function(){
			      $("#les_tags").load("afftag.php", {id:$id_event});
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
		      data: "type_A=$type_source&no_A=$id_event",
		      width:"750px",
		      onClosed:function(){
			      $("#les_liaisons").load("aff_liaisons.php", {id:$id_event});
			      $(".message").load("../inc-message.php");
		      },
		      onComplete : function() {
				    $(this).colorbox.resize();
		      }
		    });
		    return false; 
	    });
	    
	    $(".delete_version").live('click', function() {
		var answer = confirm('Etes-vous sur de vouloir supprimer cette version ?');
		if (answer == true)
		{
		  $('#ajax-supp').fadeIn();
		  var commentContainer = $(this).parents('tr:first');
		  var id = $(this).attr("id");
		  var no_evenement = $id_event;
		  var string = "no_item=" + id + "&" + "no_evenement=" + no_evenement;
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
		  var no_evenement = $id_event;
		  var string = "no_item=" + id + "&no_evenement=" + no_evenement + "&type_es=$type_source";
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
		    var no_evenement = $id_event;
		    var string = "no_item=" + id + "&" + "no_evenement=" + no_evenement;
		    $.fn.colorbox({
		      href:"aff_une_version.php",
		      width:"750px",
		      height:"650px",
		      data:string,
		      onClosed:function(){
			      $("#les_versions").load("aff_versions.php", {id:$id_event});
			      $(".message").load("../inc-message.php");
		      }
		    });
		    return false; 
	    });

	    // Confirmation suppression 
	    $('.delete-evtstruct').click(function(){
	      var answer = confirm('Etes-vous sur de vouloir supprimer $cc_cettemin ?');
	      return answer;
	    });
});

</script>

AJHE;


include "../inc-header.php";
?>

<div id="ajax-supp">
	<img src="../../img/image-loader.gif" alt="Suppression du tag en cours" /><br/>
	Traitement en cours...
</div>

<form id="adform1" action="domodifajout.php" method="post" class="formA" enctype="multipart/form-data">
  <div class="actions">
    <?php if (!$mode_ajout) { ?>
	<a href='supp.php?non_ajax=1&no_evenement=<?php if ($id_event) echo $id_event?>' class="boutonbleu ico-supprimer delete-evtstruct">Supprimer <?php echo $cc_cettemin; ?></a>
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
  <li><label for="nom">Titre : <sup>*</sup></label>
    	<input type="text" name="titre" size="70" class="input" value="<?php echo $titre; ?>" /></li>
  <li><label for="sous_titre">Sous-titre : </label>
    	<input type="text" name="sous_titre" size="70" class="input" value="<?php echo $sous_titre; ?>" /></li>
	
  <li><label for="id_event">Numéro : </label>
    	<input type="text" name="id_event" size="7" class="input verouille" value="<?php echo $id_event; ?>" readonly /></li>
  <li><label for="date_creation">Date de création : </label>
    	<input type="text" name="date_creation" size="7" class="input verouille" value="<?php echo $date_creation; ?>" readonly />
	<?php
	
	if ($email_utilisateur_creation)
	{
	  echo "<a href=\"../users/modifajout.php?id=".$no_utilisateur_creation."\" title=\"Voir l'utilisateur\" target=\"_blank\">";
	  echo "par $email_utilisateur_creation ($nom_ville_utilisateur_creation, $code_postal_utilisateur_creation)";
	  echo " <img src=\"".$root_site."img/admin/icoad-rech.png\"></a>";
	}
	else
	{
	  if (!empty($source_nom))
	    echo " (Importé de ".$source_nom.")";
	  else
	    echo " (Importation ou utilisateur supprimé)";
	}
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
  		<option value="1" <?php echo ($validation==1?"selected":"") ?>>Validé<?php echo ($validation!=1?" (supprime les versions)":"") ?></option>
  		<option value="2" <?php echo ($validation==2?"selected":"") ?>>Modifié, non validé</option>
  	</select></li>
	<input type="hidden" name="validation_ancien" value="<?php echo $validation ?>">
  <li><label for="no_genre">Genre : <sup>*</sup></label>
    <select name="no_genre">
	    <?php
		    for($indice_statut=0;$indice_statut<count($tab_genre); $indice_statut++)
		    {
			    if ($tab_genre[$indice_statut]['type_genre']=="E")
				$type = "Evènement";
			    elseif ($tab_genre[$indice_statut]['type_genre']=="A")
				$type = "Annonce";

			    if($tab_genre[$indice_statut]['no']==$genre)
			    {
				    echo "<option value=\"".$tab_genre[$indice_statut]['no']."\" selected>".$tab_genre[$indice_statut]['libelle']." (".$type.")</option>";
			    }
			    else
			    {
				    echo "<option value=\"".$tab_genre[$indice_statut]['no']."\" >".$tab_genre[$indice_statut]['libelle']." (".$type.")</option>";
			    }
		    
		    }						
	    ?>
    </select></li>
    <li><label for="nb_aime">Nombre de like : </label>
    <input type="text" name="nb_aime" value="<?php echo $nb_aime?>" size="3"></li>

    <li><label for="date_debut">Date de début <sup>*</sup> :</label>
    <input type="text" size="7"  name="date_debut" id="date_debut" value="<?php echo $date_debut_event?>"></li>
    <li><label for="date_fin">Date de fin <sup>*</sup> :</label>
    <input type="text" size="7"  name="date_fin" id="date_fin" value="<?php echo $date_fin_event?>"></li>
	
	<li><label for="heure_debut">Heure de début :</label>
    <input type="text" size="7"  name="heure_debut" id="heure_debut" value="<?php echo substr($heure_debut_event,0,-3);?>" ></li>
	
	<li><label for="heure_fin">Heure de fin :</label>
    <input type="text" size="7"  name="heure_fin" id="heure_fin" value="<?php echo substr($heure_fin_event,0,-3)?>"></li>
	
    <li><label for="site">Site internet <sup>avec http://</sup> : </label>
    <input type="text" name="site" value="<?php echo $site?>" size="35">
    <?php
    if ($site)
    {
      echo "<a href=\"".$site."\" target=\"_blank\" class=\"Voir le site\">";
      echo "<img src=\"".$root_site."img/admin/icoad-rech.png\">";
      echo "</a>";
    }
    ?>
    
    </li>
</fieldset>

<fieldset>
  <legend>Tags</legend>
  <li><label>Tags : <sup>(Vie)</sup></label>
  <div class="chps_non_input avecliste">
  <div id="les_tags">
	<?php
	    // Affiche les tags
	    // Paramètre : $id_event
	    require("afftag.php");
	?>
    </div>
     <a href="" id="ajouttag" title="Ajouter des tags" class="boutonbleu ico-ajout">Ajouter des tags</a>
    </div>
</li>
</fieldset>
<fieldset>
<legend>Lieu</legend>
    <li><label>Nom du lieu :</label>
    <input size="35" type="text" name="nomadresse" value="<?php echo $nomadresse; ?>"></li>
    <li><label>Adresse :</label>
    <input type="text" name="adresse" value="<?php echo $adresse?>" size="35"></li>

    <li><label>Code postal <sup>*</sup> :</label>
    <input type="text" name="cp" id="cp" size="6" value="<?php echo $tab_villes[0]['code_postal']?>" class="validate[required,minSize[5],maxSize[5],custom[integer]] cpville" /></li>

    <li><label>Ville <sup>*</sup>
    :</label> <input type="text"
    class="validate[required] cpville"
    name="ville" id="ville" value="<?php echo
    $tab_villes[0]['nom_ville_maj']?>" size="35" /> <img src="<?php echo $root_site; ?>img/tick-vert.png" alt="Ville validée" id="ville-valide" /></li>
    
    <input type="hidden" name="id_ville" id="id_ville" value="<?php echo $tab_villes[0]['id']?>"/>

    <li><label>T&eacute;l&eacute;phone principal :</label>
    <input type="text" name="telephone" value="<?php echo $telephone?>"></li>
    <li><label>Mobile :</label>
    <input type="text" name="telephone2" value="<?php echo $telephone2?>"></li>
    <li><label>Email :</label>
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
<li><label for="description_complementaire">Description complémentaire : </label>
    	<textarea name="description_complementaire" class="CKEDITOR" ><?php echo $description_complementaire; ?></textarea></li>
</fieldset>

<fieldset>
  <legend>Illustration</legend>
<li><label for="description">Illustration : </label>
  <div class="chps_non_input">
<?php
  // Affiche l'image
  if ($illustration)
  {
    echo "<input type=\"hidden\" name=\"url_logo\" value=\"".$illustration."\"/>\n";
    echo "<div><div class=\"illustr\">";

    if (strpos($illustration, "http://www.culture-provence-baronnies.fr") !== false)
    {
		// image distante
		echo "<a href=\"".$illustration."\" class=\"agrandir\">";
		echo "<img src=\"".$illustration."\" width=\"150\" />";	
    }
    else
    {
		// Image locale
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
    }
    echo "</a>";
    echo "</div>";
    echo "<a href='#' id=\"".$id_event."\" class=\"delete\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer l'image\" title=\"Supprimer l'image\" height=\"16\" width=\"16\" /></a></div>\n";
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
  <legend>Liaisons</legend>
  
    <div id="les_liaisons">
	<?php
	    // Affiche les liaisons
	    // Paramètre : $id_source et $type_source (config.php)
	    $id_source = $id_event;
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
	      // Paramètre : $id_event
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
  <div class="actions"><button type="submit" class="boutonbleu ico-fleche"><?php
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

	  function Tester_idstruct()
	  {
		  var valiv = $("#no_structure").val();
		  if($.trim(valiv)=="")
		  {
			  $("#struct-liee-valide").hide();
			  $("#nom_structure_liee").val('');
			  return false;
		  }
		  else
		  {
			  $('#struct-liee-valide').show("slow");
			  return true;
		  }
	  }
  
	$(function() {
	      // Tester idville
	      Tester_idville();

	      // une structure liée ?
	      Tester_idstruct();

	      $('#adform1').submit(function() {
		      return Tester_idville();
	      });

	      // Validation de la ville
	      $('.cpville').change(function() {
		      $("#id_ville").val("");
	      });

	    // Autocomplete nom de la ville
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
   
	    $( "#nom_structure_liee" ).autocomplete({
		source: "recherche_structure.php",
		minLength: 3,
		delay: 0,
		autoFocus: true,
		select: function( event, ui ) {
		    if (ui.item)
		    {
			$('#no_structure').val(ui.item.no_structure);
		    }
		    Tester_idstruct();
		}
	    });

	    $("#nom_structure_liee").on("input", null, null, function(event){
		$('#no_structure').val("");
		$("#struct-liee-valide").hide();		
	    });
	
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
