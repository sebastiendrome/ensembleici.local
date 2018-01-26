<?php
session_name("EspacePerso");
session_start();
require_once('config.php');
$no_lettre = $_POST["id"];
$position = $_GET["pos"];
//On récupère la liste deqs événements pour no_lettre

$requete_publicite = "SELECT publicites.no,publicites.url_image,publicites.titre,publicites.site FROM publicites WHERE publicites.etat=1 AND publicites.site<>'' AND publicites.site IS NOT NULL AND publicites.url_image<>'' AND publicites.url_image IS NOT NULL";
$res_publicite = $connexion->prepare($requete_publicite);
$res_publicite->execute(array(":no"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_publicite);
$liste_publicite = $res_publicite->fetchAll();
?>
<div>
<?php
for($i=0;$i<count($liste_publicite);$i++){
	?>
	<div style="padding-top:20px;padding-bottom:20px;text-align:center;" onmouseover="this.style.backgroundColor='#F0EDEA';" onmouseout="this.style.backgroundColor='transparent';" onclick="ajouter_publicite(<?php echo $liste_publicite[$i]["no"]; ?>)">
		<div style="font-weight:bold;"><?php echo $liste_publicite[$i]["titre"]." :"; ?></div>
		<img src="<?php echo $root_site.$liste_publicite[$i]["url_image"]; ?>" />
		<div><a onclick="lien_click()" href="<?php echo $liste_publicite[$i]["site"]; ?>" target="_blank"><?php echo $liste_publicite[$i]["site"]; ?></a></div>
	</div>
	<?php
}
?>
</div>
<a id="ajoutpublicite" class="boutonbleu ico-ajout" title="Ajouter une pub" href="">Ajouter une publicit&eacute;</a>
<!-- bouton permettant d'atteindre la création de nouvelles publicités -->
<script type="text/javascript">
var CLICK_SUR_LIEN = false;
function lien_click(){
	CLICK_SUR_LIEN = true;
}

function ajouter_publicite(no){
	if(!CLICK_SUR_LIEN){
		//On récupère le numéro de la lettren puis le numéro de la position, et le numéro de la publicité en parametre
		no_lettre = <?php echo $_GET["no_lettre"]; ?>;
		pos = <?php echo $_GET["pos"]; ?>;
		<?php
		if(isset($_GET["existe"])&&$_GET["existe"]==1){
		?>
		ajout = 0;
		<?php
		}
		else{
		?>
		ajout = 1;
		<?php
		}
		?>
		//On envoi tout ça à la BDD
		var xhr = getXhr();
			xhr.open("POST", "ajax/ajout_pub.php", false);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xhr.send("no_lettre="+no_lettre+"&pos="+pos+"&no_pub="+no+"&ajout="+ajout);
		var reponse = eval("("+xhr.responseText+")");
		if(reponse){
			$.fn.colorbox.close();
		}
		else{
			alert("une erreur est survenue, veuillez réessayer ...");
		}
	}
	else{
		CLICK_SUR_LIEN = false;
	}
}

$("#ajoutpublicite").live('click', function(){
	$.fn.colorbox({
	  href:"form_publicite.php",
	  width:"800px",
	  onClosed: function(){
		  // $("#zone_publicite").load("affpublicite.php", {id:<?php echo $no_lettre; ?>});
		  // $(".message").load("../inc-message.php");
		  // reouvre_old_colorbox();
	  },
	  onComplete : function() {
			$(this).colorbox.resize();
	  }
	});
	return false; 
});
/*
function reouvre_old_colorbox(){
	$.fn.colorbox({
	  href:"ajoutpublicite.php?no_lettre=<?php echo $no_lettre; ?>&pos=<?php echo $position; ?>",
	  width:"650px",
	  onClosed: function(){
		  $("#zone_publicite").load("affpublicite.php", {id:<?php echo $no_lettre; ?>});
		  $(".message").load("../inc-message.php");
	  },
	  onComplete : function() { 
			$(this).colorbox.resize();
	  }
	});
	return false; 
}*/

</script>