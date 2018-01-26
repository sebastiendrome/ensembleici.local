<?php
header('Content-Type: text/html; charset=UTF-8');
//require('/home/ensemble/www/00_dev_sam/01_include/_var_ensemble.php');
//include "/home/ensemble/www/00_dev_sam/01_include/_fonctions.php";
require('/home/ensemble/01_include/_var_ensemble.php');
include "/home/ensemble/01_include/_fonctions.php";

$requete_vie = "SELECT * FROM vie ORDER BY libelle";
$les_vies = execute_requete($requete_vie);

//TODO retirer les tags déjà existants pour kits
if(!empty($_POST["v"])){
	$requete_tag = "SELECT tag.no,tag.titre FROM tag JOIN vie_tag ON vie_tag.no_tag=tag.no WHERE vie_tag.no_vie=:v ORDER BY titre";
	$les_tags = execute_requete($requete_tag,array(":v"=>$_POST["v"]));
}
else{
	$requete_tag = "SELECT tag.no,tag.titre FROM tag JOIN vie_tag ON vie_tag.no_tag=tag.no GROUP BY tag.no ORDER BY titre";
	$les_tags = execute_requete($requete_tag);
}
	
if(!empty($_GET["t"])){
	$_GET["t"] = preg_replace("#[^0-9,]#i","",$_GET["t"]);
	$requete_sstag = "SELECT tag_sous_tag.no_tag,sous_tag.no,sous_tag.titre FROM sous_tag JOIN tag_sous_tag ON tag_sous_tag.no_sous_tag=sous_tag.no WHERE tag_sous_tag.no_tag IN (".$_GET["t"].") GROUP BY sous_tag.no ORDER BY sous_tag.titre";
	$les_sstags = execute_requete($requete_sstag);
}
else
	$les_sstags = array();



function ajoute_liste($l,$v){
	if($l!=""){
		if(!dans_liste($l,$v))
			return $l.",".$v;
		else
			return $l;
	}
	else
		return $v;
}

function dans_liste($l,$v){
	return (!empty($l))?dans_tableau(explode(",",$l),$v):false;
}

function supprime_liste($l,$v){
	if(strstr($l,",")){
		$_l = str_replace(",,",",",str_replace($v,"",$l));
		if(substr($_l,0,1)==",")
			$_l = substr($_l,1,strlen($_l));
		if(substr($_l,strlen($_l)-1,strlen($_l))==",")
			$_l = substr($_l,0,strlen($_l)-1);
		return $_l;
	}
	else{
		return ($l!=v)?$l:"";
	}
}

function dans_tableau($t,$v){
	$i=0;
	while($i<count($t)&&$t[$i]!=$v){$i++;}
	return ($i<count($t));
}



/*
$requete_vie = "SELECT * FROM vie";
$les_vies = execute_requete($requete_vie);
*/
echo '<html><head>';
?>
<link rel="stylesheet" href="../css/admin.css" type="text/css" />
<style type="text/css">
.zone_tags{
	max-width: 500px;
	background-color: #EDEDED;
	width:100%;/*style="width:250px;height:300px;border:1px solid grey;display: inline-block;vertical-align:top;max-height:80%;"*/
}
	.zone_tags>.options{
		
	}
	.zone_tags .libelle{
		background-color: rgb(54,62,67);
		color: #FFF;
		text-shadow: 0px 1px 0px rgba(0,0,0,0.1);
		text-align: center;
		padding: 0.5em;
	}
	.zone_tags>.tags{
		
	}
	.zone_tags>.tags>div, .zone_tags>.options>div{
		max-width:250px;
		min-width: 200px;
		width: 100%;
		display: inline-block;
		box-shadow: 0px 0px 1px 0px rgb(54,62,67);
		position: relative;
	}
	.liste_tags{
		height: 200px;
		overflow:auto;
	}
	.zone_tags select{
		margin: 0em;
		padding: 1em 0em;
		width: 100%;
	}
	.un_tag{
		display:inline-block;
		margin:0.2em;
		padding:0.2em;
		border-radius: 3px;
		background-color: white;
		box-shadow: 0px 0px 4px -2px rgba(54, 62, 67, 0.2);
	}
	
	
	
	.voyant{
		display:inline-block;
		width: 1em;
		height: 1em;
		background-color: rgb(143,143,143);
		margin:0.5em;
		border-radius: 50%;
		box-shadow: 1px 1px 5px -2px rgb(0,0,0) inset;
	}
	.voyant.valide{
		background-color: #50BF28;
	}
	.voyant.modification{
		background-color:#F2C824;
	}
	.voyant.importation{
		background-color:#2498F2;
	}
	
</style>

<script type="text/javascript" src="../js/_f.js"></script>
<script type="text/javascript" src="../js/_msg.js"></script>
<script type="text/javascript" src="../js/_responsive.js"></script>
<script type="text/javascript" src="../js/admin.js"></script>

<script type="text/javascript">
function tag_click(tag){
	if(tag.checked){
		element("tags_select").appendChild(tag.parentNode);
	}
	else{
		element("tags_dispo").appendChild(tag.parentNode);
	}
}
</script>
<div class="voyant"></div>
<br/>
<div class="voyant valide"></div>
<br/>
<div class="voyant modification"></div>
<br/>
<div class="voyant importation"></div>
<?php
echo '<div class="zone_tags">';
	echo '<div class="options">';
		echo '<div>';
			echo '<input type="text" class="recherche" />';
			echo '<input type="button" class="recherche" />';
		echo '</div>';
		echo '<div>';
			echo '<select name="v" onchange="this.parentNode.submit();">';
				echo '<option value="0">Toutes les vies</option>';
			for($v=0;$v<count($les_vies);$v++){
				echo '<option value="'.$les_vies[$v]["no"].'"'.(($_POST["v"]!=$les_vies[$v]["no"])?'':' selected="selected"').'>'.$les_vies[$v]["libelle"].'</option>';
			}
			echo '</select>';
		echo '</div>';
	echo '</div>';
	echo '<div class="tags">';
		echo '<div>';
			echo '<div class="libelle">';
			echo 'Tags disponibles';
			echo '</div>';
			echo '<div class="liste_tags" id="tags_dispo">';
				for($t=0;$t<count($les_tags);$t++){
					if(!dans_liste($_GET["t"],$les_tags[$t]["no"])){
						$url_tag = ajoute_liste($_GET["t"],$les_tags[$t]["no"]);
						$dans_liste = false;
					}
					else{
						$url_tag = supprime_liste($_GET["t"],$les_tags[$t]["no"]);
						$dans_liste = true;
					}
					echo '<div class="un_tag">';
						echo '<input type="checkbox"'.((!$dans_liste)?'':' checked="checked"').' id="tag_'.$les_tags[$t]["no"].'" onclick="tag_click(this);" />';
						echo '<label for="tag_'.$les_tags[$t]["no"].'">'.$les_tags[$t]["titre"].'</label>';
					echo '</div>';
				}
			echo '</div>';
			
		echo '</div>';
		echo '<div>';
			echo '<div class="libelle">';
			echo 'Tags sélectionnés';
			echo '</div>';
			echo '<div class="liste_tags" id="tags_select">';
			
			echo '</div>';
			
		echo '</div>';
	echo '</div>';
echo '</div>';
?>
