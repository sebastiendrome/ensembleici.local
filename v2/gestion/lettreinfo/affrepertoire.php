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
On regarde pour no_lettre = id si l'étape est valide.
	Si oui : on affiche le bout de lettre en prennant les structures de liste_structure_valide
	
	Si non : 
		On regarde si liste_structure est vide
			Si non : on affiche les structures de liste_structure
				On récupère les éventuelles structures apparues entre temps (date création > date_modification)
			Si oui : on récupère la liste des structures 
*/
$no_lettre = $_POST["id"];
$requete_generale = "SELECT * FROM lettreinfo_repertoire WHERE no_lettre=:no";
$res_requete_generale = $connexion->prepare($requete_generale);
$res_requete_generale->execute(array(":no"=>$no_lettre)) or die("erreur requête ligne 116 : ".$requete_generale);
$tab_requete_generale = $res_requete_generale->fetchAll();
$insertion = false;
$modification = false;
if(count($tab_requete_generale)>0){
	$etape_valide = (bool)$tab_requete_generale[0]["etape_valide"];
	if($etape_valide){
		//L'étape est déjà validée : on affiche le rendu final, ainsi qu'un bouton "modifier"
		$liste = (string)$tab_requete_generale[0]["liste_structure_valide"];
	}
	else{
		//L'étape n'est pas validée : on regarde la liste
		$liste = (string)$tab_requete_generale[0]["liste_structure_complete"];
		$liste_choisie = (string)$tab_requete_generale[0]["liste_structure"];
		if($liste!=null&&$liste!=""){
			//On charge aussi toutes les structures apparues depuis la dernière modification (donc qui ne sont pas dans la liste)
			$requete_liste_structures = "SELECT no FROM structure WHERE etat=1 AND structure.apparition_lettre=0 AND date_creation>:d";
			$res_liste_structures = $connexion->prepare($requete_liste_structures);
			$res_liste_structures->execute(array(":d"=>$tab_requete_generale[0]["date_modification"]));
			$tab_liste_structures = $res_liste_structures->fetchAll();
			$liste_2 = $liste;
			for($i=0;$i<count($tab_liste_structures);$i++){
				if($liste_2!="")
					$liste_2 .= ",";
				$liste_2 .= $tab_liste_structures[$i]["no"];
			}
			if($liste_2!=$liste){
				$liste = $liste2;
				$requete_update_liste_structures = "UPDATE lettreinfo_repertoire SET liste_structure_complete=:l WHERE no_lettre=:no";
				$res_update_liste_structures = $connexion->prepare($requete_update_liste_structures);
				$res_update_liste_structures->execute(array(":l"=>$liste,":no"=>$no_lettre));
			}
		}
		else{
			$modification = true;
		}
	}
}
else{
	$insertion = true;
}

if($insertion||$modification){
	//La liste temporaire est vide, on charge alors toutes les structures entrées depuis la dernière lettre d'information
		//Par défaut, on coche les 5 dernières.
		
	//On récupère la date de modification de cette étape pour la lettre précédente
	$requete_date_mod_derniere_lettre = "SELECT date_modification AS d FROM lettreinfo_repertoire WHERE no_lettre<>:no ORDER BY date_modification DESC LIMIT 1";
	$res_date_mod_derniere_lettre = $connexion->prepare($requete_date_mod_derniere_lettre);
	$res_date_mod_derniere_lettre->execute(array(":no"=>$no_lettre));
	$rep_date_mod_derniere_lettre = $res_date_mod_derniere_lettre->fetchAll();
	$date_mod_derniere_lettre = $rep_date_mod_derniere_lettre[0]["d"];
	
	//On récupère maintenant la liste des structures dont la date de création est supérieur à la date de la dernière lettre
	$requete_liste_structures = "SELECT no FROM structure WHERE etat=1 AND structure.apparition_lettre=0 AND date_creation>:d";
	$res_liste_structures = $connexion->prepare($requete_liste_structures);
	$res_liste_structures->execute(array(":d"=>$date_mod_derniere_lettre));
	$tab_liste_structures = $res_liste_structures->fetchAll();
	
	$liste = "";
	$liste_choisie = "";
	for($i=0;$i<count($tab_liste_structures);$i++){
		if($liste!="")
			$liste .= ",";
		$liste .= $tab_liste_structures[$i]["no"];
	}
	$liste_choisie = $liste;
	
	if($insertion){
		//On insère la liste
		$requete_insertion_structures = "INSERT INTO lettreinfo_repertoire(no_lettre,liste_structure_complete,liste_structure) VALUES(:no,:l,:l_c)";
		$res_insertion_structures = $connexion->prepare($requete_insertion_structures);
		$res_insertion_structures->execute(array(":no"=>$no_lettre,":l"=>$liste,":l_c"=>$liste_choisie));
	}
	else{
		$requete_modification_structures = "UPDATE lettreinfo_repertoire SET liste_structure_complete=:l_c, liste_structure=:l WHERE no_lettre=:no";
		$res_modification_structures = $connexion->prepare($requete_modification_structures);
		$res_modification_structures->execute(array(":no"=>$no_lettre,":l"=>$liste,":l_c"=>$liste_choisie));
	}
}

//On récupère maintenant toutes les informations sur les structures de la liste
if($liste!=""){
	$requete_structure = "SELECT * FROM structure WHERE no IN (".$liste.")";
	$res_requete_structure = $connexion->prepare($requete_structure);
	$res_requete_structure->execute();
	$tab_requete_structure = $res_requete_structure->fetchAll();
}
if(!$etape_valide){
?>
<div id="structure_message" style="width:100%;" class="message_sam_info">Vous pouvez valider l'&eacute;tape.</div>
<?php
}else{
?>
<div id="structure_message" style="width:100%;" class="message_sam_valide">L'&eacute;tape est valid&eacute;e.</div>
<?php
}
?>
<div id="structure_liste" style="width:100%;margin:auto;">
<?php
if(!$etape_valide){
	if($liste=="")
		echo "<div id=\"structure_aucune\">C'est malheureux, aucune structure n'a &eacute;t&eacute; saisie cette semaine</div>";
	else{
		// $limite = 10;
		$nb_check = 0;
		$limite = count($tab_requete_structure);
		for($i=0;$i<$limite;$i++){
			if(substr($tab_requete_structure[$i]["url_logo"],0,7)!="http://")
				$tab_requete_structure[$i]["url_logo"] = "http://www.ensembleici.fr/".$tab_requete_structure[$i]["url_logo"];
			?>
			<div onclick="verif_check(this.getElementsByTagName('input')[0]);" style="margin:10px;width:120px;height:100px;position:relative;overflow:hidden;border-radius:5px;display:inline-block;border:1px solid #E3D6C7;box-shadow:0px 0px 10px #<?php if(est_dans_liste($tab_requete_structure[$i]["no"],$liste_choisie)) echo "96DF5F"; else echo "aaa"; ?>">
				<img src="<?php echo $tab_requete_structure[$i]["url_logo"]; ?>" style="position:absolute;" style="visiblity:hidden;" onload="placePhotoDansCadre(this,this.parentNode)" />
				<div style="width:100%;height:20px;position:absolute;top:0px;left:0px;background-color:white;text-indent:10px;">
					<?php if(est_dans_liste($tab_requete_structure[$i]["no"],$liste_choisie)){ ?>
					<input onclick="verif_check(this)" type="checkbox" id="structure_check_<?php echo $tab_requete_structure[$i]["no"]; ?>" style="position:relative;top:3px;visibility:hidden;" checked="checked" />&nbsp;<label style="text-align:left;display:inline;padding:0px;margin:0px;width:auto;float:none;font-size:10px;" for="structure_check_<?php echo $tab_requete_structure[$i]["no"]; ?>">retirer</label>
					<?php }else{ 
						$nb_check++;
					?>
					<input onclick="verif_check(this)" type="checkbox" id="structure_check_<?php echo $tab_requete_structure[$i]["no"]; ?>" style="position:relative;top:3px;visibility:hidden;" />&nbsp;<label style="text-align:left;display:inline;padding:0px;margin:0px;width:auto;float:none;font-size:10px;" for="structure_check_<?php echo $tab_requete_structure[$i]["no"]; ?>">ajouter</label>
					<?php } ?>
					<img class="icone" width="12" height="12" title="Supprimer" alt="Supprimer" src="../../img/admin/icoad-supp.png" style="position:relative;top:3px;cursor:pointer;float:right;" onclick="supprimer(<?php echo $tab_requete_structure[$i]["no"]; ?>,'repertoire')">
				</div>
				<div style="width:100%;position:absolute;bottom:0px;left:0px;background-color:white;text-align:center;font-size:10px;opacity:0.9"><?php echo "<span style=\"font-weight:bold;\">".$tab_requete_structure[$i]["nom"]."</span><br/>".$tab_requete_structure[$i]["sous_titre"]; ?></div>
			</div>
			<?php
		}
	}
?>
</div>
<br/>
<div>
	<a id="ajoutrepertoire" class="boutonbleu ico-ajout" title="Promouvoir une structure" href="">Promouvoir une structure</a>
	<button onclick="valider_etape('repertoire');return false;" class="boutonbleu ico-fleche" id="valider_structure">Valider l'&eacute;tape</button>
</div>
<?php
}
else{
//Si l'étape est valide
?>
<br/>
<div style="width:650px;margin:auto;border: 1px solid #E3D6C7;padding:10px;background-color:white;">
<?php
include "lettre_en_cours/repertoire.php";
?>
</div>
<br/>
<button onclick="de_valider_etape('repertoire');return false;" class="boutonbleu ico-fleche" id="annuler_structure">Retour en mode cr&eacute;ation</button>
<?php
}
?>