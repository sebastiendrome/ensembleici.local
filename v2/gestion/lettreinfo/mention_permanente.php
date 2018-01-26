<?php
session_name("EspacePerso");
session_start();
header('Content-Type: text/html; charset=UTF-8');
include "config.php";
function est_dans_liste($no,$liste,$separateur=","){
	$l = explode($separateur,$liste);
	$i=0;
	while($i<count($l)&&$l[$i]!=$no){
		$i++;
	}
	if($i==count($l))
		return false;
	else
		return true;
}
$no_lettre = $_GET["id"];
$territoire = $_GET['territoire'];

$requete_generale = "SELECT * FROM lettreinfo_edito WHERE no_lettre=0 AND territoires_id=".$territoire;
$res_requete_generale = $connexion->prepare($requete_generale);
$res_requete_generale->execute() or die("erreur requ�te ligne 116 : ".$requete_generale);
$tab_requete_generale = $res_requete_generale->fetchAll();
$corps = (string)$tab_requete_generale[0]["corps"];
$mention_permanente = (bool)$tab_requete_generale[0]["mention_permanente"];
$avant = (bool)$tab_requete_generale[0]["avant"];
?>
<div id="zone_mention_permanente" style="width:100%;margin:auto;">
	<textarea name="mention_permanente" style="width:100%" id="mention_permanente"><?php echo $corps; ?></textarea><br/>
	<b>Charger par d&eacute;faut&nbsp;:&nbsp;</b><br/>
	<input type="checkbox" id="inclure_mention_d" <?php if($mention_permanente) echo 'checked="checked" '; ?>/><label for="inclure_mention_d">&nbsp;inclure la mention</label><br/>
	<input type="radio" id="avant_d" name="avant_apres_d" <?php if($avant) echo 'checked="checked" '; ?>/><label for="avant_d">&nbsp;avant</label><br/>
	<input type="radio" id="apres_d" name="avant_apres_d" <?php if(!$avant) echo 'checked="checked" '; ?>/><label for="apres_d">&nbsp;apr&egrave;s</label><span style="position:relative;top:-11px;">&nbsp;&nbsp;le texte saisi.</span>
</div>
<br/>
<div>
	<button onclick="valider_mention_permanente();return false;" class="boutonbleu ico-fleche" id="valider_mention_permanente">Enregistrer les modification</button>
</div>
<script type="text/javascript">
// if(CKEDITOR.instances["mention_permanente"])
	// CKEDITOR.instances["mention_permanente"].destroy();
/*if (CKEDITOR.instances['mention_permanente']){
	CKEDITOR.instances['mention_permanente'].destroy();
	//CKEDITOR.remove(CKEDITOR.instances['mention_permanente']);
}*/
//CKEDITOR.replace('mention_permanente',{toolbar:'NewsLetter',uiColor:'#F0EDEA',language:'fr',height:'300',skin:'kama',enterMode : CKEDITOR.ENTER_BR});

function valider_mention_permanente(){
	//On r�cup�re le texte.
	var t = encodeURIComponent(CKEDITOR.instances["mention_permanente"].getData());
	//On r�cup�re les valeurs par d�faut.
	if(document.getElementById("avant_d").checked)
		var avant = 1;
	else
		var avant = 0;
	if(document.getElementById("inclure_mention_d").checked)
		var mention = 1;
	else
		var mention = 0;
	//On les envoi au serveur
	var xhr = getXhr();
		xhr.open("POST", "ajax/valider_mention_permanente.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("t="+t+"&a="+avant+"&m="+mention+"&n=<?php echo $no_lettre; ?>&p=<?php echo $territoire; ?>");
	var reponse = eval("("+xhr.responseText+")");
	if(reponse){
		CKEDITOR.instances['mention_permanente'].destroy();
		window.parent.$.colorbox.close();
	}
	else{
		alert("une erreur est survenue, veuillez r�essayer.");
	}
}
</script>
