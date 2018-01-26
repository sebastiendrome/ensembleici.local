<?php
session_name("EspacePerso");
session_start();
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

/*
On regarde pour no_lettre = id si l'�tape est valide.
	Si oui : on affiche le bout de lettre en prennant les structures de liste_structure_valide
	
	Si non : 
		On regarde si liste_structure est vide
			Si non : on affiche les structures de liste_structure
				On r�cup�re les �ventuelles structures apparues entre temps (date cr�ation > date_modification)
			Si oui : on r�cup�re la liste des structures 
*/
$no_lettre = $_POST["id"];
$territoire = $_POST["territoire"];
$requete_generale = "SELECT * FROM lettreinfo_edito WHERE no_lettre=:no";
$res_requete_generale = $connexion->prepare($requete_generale);
$res_requete_generale->execute(array(":no"=>$no_lettre)) or die("erreur requête ligne 116 : ".$requete_generale);
$tab_requete_generale = $res_requete_generale->fetchAll();
$insertion = false;
if(count($tab_requete_generale)>0){
	$etape_valide = (bool)$tab_requete_generale[0]["etape_valide"];
	$corps = (string)$tab_requete_generale[0]["corps"];
	$avant = (bool)$tab_requete_generale[0]["avant"];
	$mention_permanente = (bool)$tab_requete_generale[0]["mention_permanente"];
}
else{
	$insertion = true;
	$corps = "";
	$avant = false;
	$mention_permanente = true;
}

if($insertion){
	//On ins�re l'entr�e
	$requete_insertion_edito = "INSERT INTO lettreinfo_edito(no_lettre,corps,etape_valide) VALUES(:no,'',0)";
	$res_insertion_edito = $connexion->prepare($requete_insertion_edito);
	$res_insertion_edito->execute(array(":no"=>$no_lettre));
}

if(!$etape_valide){
?>
<div id="edito_message" style="width:100%;" class="message_sam_info">Vous pouvez valider l'&eacute;tape.</div>
<?php
}else{
?>
<div id="edito_message" style="width:100%;" class="message_sam_valide">L'&eacute;tape est valid&eacute;e.</div>
<?php
}
?>
<br/>
<div id="edito_liste" style="width:100%;margin:auto;">
<?php
if(!$etape_valide){
	?>
	<input type="checkbox" <?php if($mention_permanente) echo 'checked="checked"'; ?> id="check_edito" onclick="modifier_mention_permanente()" />
        <label for="check_edito" style="width:auto;float:none;display:inline;">&nbsp;inclure la mention permanente</label>&nbsp;
        <input onclick="modifier_mention_permanente()" name="avant_apres" type="radio" id="avant" <?php if($avant) echo 'checked="checked"'; ?> />
        <label for="avant" style="width:auto;float:none;display:inline;">&nbsp;avant</label>&nbsp;
        <input onclick="modifier_mention_permanente()" name="avant_apres" type="radio" id="apres" <?php if(!$avant) echo 'checked="checked"'; ?> />
        <label for="apres" style="width:auto;float:none;display:inline;">&nbsp;apr&egrave;s</label>
        <b>&nbsp;le texte.&nbsp;</b>
        <a id="modif_edito" class="boutonbleu ico-modifier" title="modifier la mention permanente">modifier la mention permanente</a>
	<textarea name="edito" style="width:100%" id="edito"><?php echo $corps; ?></textarea><br/>
	<a id="ajoutrepertoire_edito" class="boutonbleu ico-ajout" title="Ajouter une structure" href="">Ajouter une structure</a>
	<a id="ajoutagenda_edito" class="boutonbleu ico-ajout" title="Ajouter une structure" href="">Ajouter un &eacute;v&eacute;nement</a>
	<a id="ajoutpetiteannonce_edito" class="boutonbleu ico-ajout" title="Ajouter une structure" href="">Ajouter une petite annonce</a><br/>
	<?php
?>
</div>
<br/>
<div>
	<button onclick="valider_etape('edito');return false;" class="boutonbleu ico-fleche" id="valider_edito">Valider l'&eacute;tape</button>
</div>
<script type="text/javascript">
// alert(CKEDITOR.instances["edito"]);
// if(CKEDITOR.instances["edito"]!=null)
	// CKEDITOR.instances["edito"].destroy();
	// alert("stop");
if (CKEDITOR.instances['edito']) {
        CKEDITOR.remove(CKEDITOR.instances['edito']);
}
CKEDITOR.replace('edito',{toolbar:'NewsLetter',uiColor:'#F0EDEA',language:'fr',height:'300',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
function modifier_mention_permanente(){
	//On r�cup�re les valeurs par d�faut.
	if(document.getElementById("avant").checked)
		var avant = 1;
	else
		var avant = 0;
	if(document.getElementById("check_edito").checked)
		var mention = 1;
	else
		var mention = 0;
	//On les envoi au serveur
	var xhr = getXhr();
		xhr.open("POST", "ajax/valider_mention_permanente.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("a="+avant+"&m="+mention+"&n=<?php echo $no_lettre; ?>&p=<?php echo $territoire; ?>");
	var reponse = eval("("+xhr.responseText+")");
	if(!reponse){
		alert("une erreur est survenue, veuillez r�essayer.");
	}
}
</script>
<?php
}
else{
//Si l'�tape est valide
?>
<br/>
<div style="width:650px;margin:auto;border: 1px solid #E3D6C7;padding:10px;background-color:white;">
<?php
include "lettre_en_cours/edito.php";
?>
</div>
<br/>
<button onclick="de_valider_etape('edito');return false;" class="boutonbleu ico-fleche" id="annuler_edito">Retour en mode cr&eacute;ation</button>
<?php
}
?>