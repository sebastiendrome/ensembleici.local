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
On r�cup�re a liste des partenaires
On r�cup�re la liste des partenaires coch�e actuellement.
	Si cette derni�re n'existe pas, elle prend la valeur de tous les partenaires.
*/
$no_lettre = $_POST["id"];
$territoire = $_POST["territoire"];
$requete_generale = "SELECT * FROM lettreinfo_partenaireinstitutionnel WHERE no_lettre=:no";
$res_requete_generale = $connexion->prepare($requete_generale);
$res_requete_generale->execute(array(":no"=>$no_lettre)) or die("erreur requête ligne 116 : ".$requete_generale);
$tab_requete_generale = $res_requete_generale->fetchAll();

$requete_partenaire = "SELECT * FROM partenaireinstitutionnel WHERE territoires_id = ".$territoire;
$res_requete_partenaire = $connexion->prepare($requete_partenaire);
$res_requete_partenaire->execute();
$tab_requete_partenaire = $res_requete_partenaire->fetchAll();

$insertion = false;
if(count($tab_requete_generale)>0){
	$liste = $tab_requete_generale[0]["liste"];
}
else{
	$liste = "";
	for($i=0;$i<count($tab_requete_partenaire);$i++){
		if($liste!="")
			$liste .= ",";
		$liste .= $tab_requete_partenaire[$i]["no"];
	}
	//On ins�re l'entr�e
	$requete_insertion = "INSERT INTO lettreinfo_partenaireinstitutionnel(no_lettre,liste) VALUES(:no,:l)";
	$res_insertion = $connexion->prepare($requete_insertion);
	$res_insertion->execute(array(":no"=>$no_lettre,":l"=>$liste));
}
?>
<div id="liste_partenaire_institutionnel" style="width:auto;margin:auto;">
	<?php
	for($i=0;$i<count($tab_requete_partenaire);$i++){
		//On place la liste des partenaires institutionnels
	?>
	<div style="float:left;height:100px;margin:10px;">
		<div><input id="p_<?php echo $tab_requete_partenaire[$i]["no"]; ?>" style="float:right;" type="checkbox" <?php if(est_dans_liste($tab_requete_partenaire[$i]["no"],$liste)) echo 'checked="checked" '; ?>onclick="input_check_partenaire();" /></div>
		<img src="<?php echo "http://www.ensembleici.fr/".$tab_requete_partenaire[$i]["image"]; ?>" />
	</div>
	<?php
	}
	?>
</div>
<script type="text/javascript">
function input_check_partenaire(){
	var c = document.getElementById("liste_partenaire_institutionnel").getElementsByTagName("input");
	var l = "";
	for(i=0;i<c.length;i++){
		if(c[i].checked){
			if(l!="")
				l+=",";
			l+=c[i].id.split("_")[1];
		}
	}
	var xhr = getXhr();
		xhr.open("POST", "ajax/modif_liste_partenaire.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("n=<?php echo $no_lettre; ?>&l="+escape(l));
	var reponse = eval("("+xhr.responseText+")");
	if(!reponse)
		alert("une erreur s'est produite");
}
</script>