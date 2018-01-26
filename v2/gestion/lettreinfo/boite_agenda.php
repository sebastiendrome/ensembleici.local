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
$requete_liste_complete = "SELECT lettreinfo_agenda.liste_evenement_complete AS l,lettreinfo.date_debut AS d FROM lettreinfo_agenda JOIN lettreinfo ON lettreinfo.no=lettreinfo_agenda.no_lettre WHERE lettreinfo_agenda.no_lettre=:no";
$res_liste_complete = $connexion->prepare($requete_liste_complete);
$res_liste_complete->execute(array(":no"=>$no_lettre));
$tab_liste_complete = $res_liste_complete->fetchAll();
$liste_complete = $tab_liste_complete[0]["l"];
$date_debut = $tab_liste_complete[0]["d"];
$date_debut = time();
$num_jour_courant = date("N");
$nb_jour_dimanche = 7-$num_jour_courant;
//On calcul le timestamp du dimanche qui arrive
$date_fin = $date_debut + ($nb_jour_dimanche+7)*24*60*60;
$date_debut_lettre = date("Y-m-d");
$date_fin_lettre = date("Y-m-d",$date_fin);
if($liste_complete!="")
	$requete_liste = "SELECT * FROM evenement WHERE titre NOT LIKE '%hebdomadaire%' AND date_debut<=:d_f AND date_fin>:d_d AND no NOT IN (".$liste_complete.")";
else
	$requete_liste = "SELECT * FROM evenement WHERE titre NOT LIKE '%hebdomadaire%' AND date_debut<=:d_f AND date_fin>:d_d";
$res_requete_liste = $connexion->prepare($requete_liste);
$res_requete_liste->execute(array(":d_d"=>$date_debut_lettre,":d_f"=>$date_fin_lettre)) or die("erreur requête ligne 116 : ".$requete_liste);
$tab_requete_liste = $res_requete_liste->fetchAll();
?>

<script type="text/javascript">
function select(d){
	var input = d.getElementsByTagName("input")[0];
	input.checked = !input.checked;
}
	function clique_check(c){
		c.checked = !c.checked;
	}

function ajouter_selection(d){
	var les_inputs = document.getElementById("agenda_liste_boite").getElementsByTagName("input");
	var i = 0;
	var les_erreurs = new Array();
	for(i=0;i<les_inputs.length;i++){
		if(les_inputs[i].type=="checkbox"){
			if(les_inputs[i].checked){
				//alert(les_inputs[i].id);
				var no = les_inputs[i].id.split("_")[1];
				var xhr = getXhr();
					xhr.open("POST", "ajax/modif_liste_complete.php", false);
					xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xhr.send("e=agenda&no="+no+"&act=add&no_l=<?php echo $no_lettre; ?>");
				var reponse = eval("("+xhr.responseText+")");
				if(!reponse)
					les_erreurs[les_erreurs.length] = no;
			}
		}
	}
	var message = "";
	for(i=0;i<les_erreurs.length;i++){
		if(message!="")
			message += " et ";
		message += document.getElementById("check_"+les_erreurs[i]).parentNode.getElementsByTagName("div")[0].firstChild.firstChild.data;
	}
	if(message!=""){
		message = "les événements : "+message+" n'ont pas pu être ajoutés";
		alert(message);
	}
	d.$.colorbox.close();
}
function annuler(d){
	d.$.colorbox.close();
}
</script>
<div id="agenda_liste_boite" style="width:100%;margin:auto;">
<?php
if(count($tab_requete_liste)==0)
	echo "<div id=\"agenda_boite_aucune\">Aucun &eacute;v&eacute;nement dans la bo&icirc;te actuellement</div>";
else{
	// $limite = 10;
	for($i=0;$i<count($tab_requete_liste);$i++){
		if(substr($tab_requete_liste[$i]["url_image"],0,7)!="http://")
			$tab_requete_liste[$i]["url_image"] = "http://www.ensembleici.fr/".$tab_requete_liste[$i]["url_image"];
		?>
		<div style="margin:10px;width:130px;height:100px;position:relative;overflow:hidden;border-radius:5px;display:inline-block;background-color:#F0EEEB;cursor:pointer;border:1px solid #E3D6C7;" onclick="select(this);">
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
			<input type="checkbox" id="check_<?php echo $tab_requete_liste[$i]["no"]; ?>" style="position:absolute;top:2px;left:2px;" onclick="clique_check(this)" />
		</div>
		<?php
	}
}
?>
	<button onclick="ajouter_selection(parent);return false;" class="boutonbleu ico-fleche">Ajouter la s&eacute;l&eacute;ction</button>
	<button onclick="annuler(parent);return false;" class="boutonbleu ico-fleche">Annuler</button>
</div>