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
$no_lettre = $_GET["id"];
$requete_liste_complete = "SELECT liste_petiteannonce_complete AS l FROM lettreinfo_petiteannonce WHERE no_lettre=:no";
$res_liste_complete = $connexion->prepare($requete_liste_complete);
$res_liste_complete->execute(array(":no"=>$no_lettre));
$tab_liste_complete = $res_liste_complete->fetchAll();
$liste_complete = $tab_liste_complete[0]["l"];
if($liste_complete!="")
	$requete_liste = "SELECT * FROM petiteannonce WHERE apparition_lettre>0 AND date_fin>NOW() AND no NOT IN (".$liste_complete.")";
else
	$requete_liste = "SELECT * FROM petiteannonce WHERE apparition_lettre>0 AND date_fin>NOW()";
$res_requete_liste = $connexion->prepare($requete_liste);
$res_requete_liste->execute() or die("erreur requête ligne 116 : ".$requete_liste);
$tab_requete_liste = $res_requete_liste->fetchAll();
?>

<script type="text/javascript">
function select(no,d){
	//On ajoute no à la liste des répertoires de la semaine
	var xhr = getXhr();
		xhr.open("POST", "ajax/modif_liste_complete.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("e=petiteannonce&no="+no+"&act=add&no_l=<?php echo $no_lettre; ?>");
	var reponse = eval("("+xhr.responseText+")");
	if(reponse)
		d.$.colorbox.close();
	else
		alert("une erreur est survenue");
}
</script>
<div id="petiteannonce_liste_boite" style="width:100%;margin:auto;">
<?php
if(count($tab_requete_liste)==0)
	echo "<div id=\"petiteannonce_boite_aucune\">Aucune petite annonce dans la bo&icirc;te actuellement</div>";
else{
	// $limite = 10;
	for($i=0;$i<count($tab_requete_liste);$i++){
		if(substr($tab_requete_liste[$i]["url_image"],0,7)!="http://")
			$tab_requete_liste[$i]["url_image"] = "http://www.ensembleici.fr/".$tab_requete_liste[$i]["url_image"];
		?>
		<div style="margin:10px;width:130px;height:100px;position:relative;overflow:hidden;border-radius:5px;display:inline-block;background-color:#F0EEEB;cursor:pointer;border:1px solid #E3D6C7;" onclick="select(<?php echo $tab_requete_liste[$i]["no"]; ?>,parent);">
			<?php
			if($tab_requete_liste[$i]["url_image"]!="http://www.ensembleici.fr/"){
				list($largeur,$hauteur) = getimagesize($tab_requete_liste[$i]["url_image"]);
				$new_largeur = 180;
				$new_hauteur = $new_largeur*$hauteur/$largeur;
				if($new_hauteur>180){
					$new_hauteur = 180;
					$new_largeur = $new_hauteur*$largeur/$hauteur;
				}
				if($new_largeur<180)
					$margin_x = (180-$new_largeur)/2;
				else
					$margin_x = 0;
			?>
			<img src="<?php echo $tab_requete_liste[$i]["url_image"]; ?>" style="position:absolute;top:0px;left:<?php echo $margin_x."px;width:".$new_largeur."px;height:".$new_hauteur."px;"; ?>" />
			<?php
			}
			?>
			<div style="width:100%;position:absolute;bottom:0px;left:0px;background-color:white;text-align:center;font-size:10px;"><?php echo "<span style=\"font-weight:bold;\">".$tab_requete_liste[$i]["titre"]."</span>"; ?></div>
			<div style="display:inline-block;width:auto;min-width:26px;text-align:center;height:26px;border-radius:15px 15px 15px 15px;background-color:#D90000;color:white;font-weight:bold;font-size:21px;position:absolute;right:10px;top:10px;border:4px solid #E7E8E8;box-shadow: 0px 0px 12px #aaa;"><?php echo $tab_requete_liste[$i]["apparition_lettre"]; ?></div>
		</div>
		<?php
	}
}
?>
</div>