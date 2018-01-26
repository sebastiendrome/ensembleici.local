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
	Si oui : on affiche le bout de lettre en prennant les petiteannonces de liste_petiteannonce_valide
	
	Si non : 
		On regarde si liste_petiteannonce est vide
			Si non : on affiche les petiteannonces de liste_petiteannonce
				On r�cup�re les �ventuelles petiteannonces apparues entre temps (date cr�ation > date_modification)
			Si oui : on r�cup�re la liste des petiteannonces 
*/
$no_lettre = $_POST["id"];
$requete_generale = "SELECT * FROM lettreinfo_petiteannonce WHERE no_lettre=:no";
$res_requete_generale = $connexion->prepare($requete_generale);
$res_requete_generale->execute(array(":no"=>$no_lettre)) or die("erreur requête ligne 116 : ".$requete_generale);
$tab_requete_generale = $res_requete_generale->fetchAll();
$insertion = false;
$modification = false;
if(count($tab_requete_generale)>0){
	$etape_valide = (bool)$tab_requete_generale[0]["etape_valide"];
	if($etape_valide){
		//L'�tape est d�j� valid�e : on affiche le rendu final, ainsi qu'un bouton "modifier"
		$liste = (string)$tab_requete_generale[0]["liste_petiteannonce_valide"];
	}
	else{
		//L'�tape n'est pas valid�e : on regarde la liste
		$liste = (string)$tab_requete_generale[0]["liste_petiteannonce_complete"];
		$liste_choisie = (string)$tab_requete_generale[0]["liste_petiteannonce"];
		if($liste!=null&&$liste!=""){
			//On charge aussi toutes les petiteannonces apparues depuis la derni�re modification (donc qui ne sont pas dans la liste)
			$requete_liste_petiteannonces = "SELECT no FROM petiteannonce WHERE etat=1 AND evenement.apparition_lettre=0 date_creation>:d";
			$res_liste_petiteannonces = $connexion->prepare($requete_liste_petiteannonces);
			$res_liste_petiteannonces->execute(array(":d"=>$tab_requete_generale[0]["date_modification"]));
			$tab_liste_petiteannonces = $res_liste_petiteannonces->fetchAll();
			$liste_2 = $liste;
			for($i=0;$i<count($tab_liste_petiteannonces);$i++){
				if($liste_2!="")
					$liste_2 .= ",";
				$liste_2 .= $tab_liste_petiteannonces[$i]["no"];
			}
			if($liste_2!=$liste){
				$liste = $liste2;
				$requete_update_liste_petiteannonces = "UPDATE lettreinfo_petiteannonce SET liste_petiteannonce_complete=:l WHERE no_lettre=:no";
				$res_update_liste_petiteannonces = $connexion->prepare($requete_update_liste_petiteannonces);
				$res_update_liste_petiteannonces->execute(array(":l"=>$liste,":no"=>$no_lettre));
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

if($insertion){
	//La liste temporaire est vide, on charge alors toutes les petiteannonces entr�es depuis la derni�re lettre d'information
		//Par d�faut, on coche les 5 derni�res.
		
	//On r�cup�re la date de modification de cette �tape pour la lettre pr�c�dente
	$requete_date_mod_derniere_lettre = "SELECT date_modification AS d FROM lettreinfo_petiteannonce WHERE no_lettre<>:no ORDER BY date_modification DESC LIMIT 1";
	$res_date_mod_derniere_lettre = $connexion->prepare($requete_date_mod_derniere_lettre);
	$res_date_mod_derniere_lettre->execute(array(":no"=>$no_lettre));
	$rep_date_mod_derniere_lettre = $res_date_mod_derniere_lettre->fetchAll();
	$date_mod_derniere_lettre = $rep_date_mod_derniere_lettre[0]["d"];
	
	//On r�cup�re maintenant la liste des petiteannonces dont la date de cr�ation est sup�rieur � la date de la derni�re lettre
	$requete_liste_petiteannonces = "SELECT no FROM petiteannonce WHERE etat=1 AND evenement.apparition_lettre=0 AND date_creation>:d";
	$res_liste_petiteannonces = $connexion->prepare($requete_liste_petiteannonces);
	$res_liste_petiteannonces->execute(array(":d"=>$date_mod_derniere_lettre));
	$tab_liste_petiteannonces = $res_liste_petiteannonces->fetchAll();
	
	$liste = "";
	$liste_choisie = "";
	for($i=0;$i<count($tab_liste_petiteannonces);$i++){
		if($liste!="")
			$liste .= ",";
		$liste .= $tab_liste_petiteannonces[$i]["no"];
	}
	$liste_choisie = $liste;
	
	if($insertion){
		//On ins�re la liste
		$requete_insertion_petiteannonces = "INSERT INTO lettreinfo_petiteannonce(no_lettre,liste_petiteannonce_complete,liste_petiteannonce) VALUES(:no,:l,:l_c)";
		$res_insertion_petiteannonces = $connexion->prepare($requete_insertion_petiteannonces);
		$res_insertion_petiteannonces->execute(array(":no"=>$no_lettre,":l"=>$liste,":l_c"=>$liste_choisie));
	}
	else{
		$requete_modification_petiteannonces = "UPDATE lettreinfo_petiteannonce SET liste_petiteannonce_complete=:l_c, liste_petiteannonce=:l WHERE no_lettre=:no";
		$res_modification_petiteannonces = $connexion->prepare($requete_modification_petiteannonces);
		$res_modification_petiteannonces->execute(array(":no"=>$no_lettre,":l"=>$liste,":l_c"=>$liste_choisie));
	}
}

//On r�cup�re maintenant toutes les informations sur les petiteannonces de la liste
if($liste!=""){
	$requete_petiteannonce = "SELECT * FROM petiteannonce WHERE no IN (".$liste.")";
	$res_requete_petiteannonce = $connexion->prepare($requete_petiteannonce);
	$res_requete_petiteannonce->execute();
	$tab_requete_petiteannonce = $res_requete_petiteannonce->fetchAll();
}
if(!$etape_valide){
?>
<div id="petiteannonce_message" style="width:100%;" class="message_sam_info">Vous pouvez valider l'&eacute;tape.</div>
<?php
}else{
?>
<div id="petiteannonce_message" style="width:100%;" class="message_sam_valide">L'&eacute;tape est valid&eacute;e.</div>
<?php
}
?>
<div id="petiteannonce_liste" style="width:100%;margin:auto;">
<?php
if(!$etape_valide){
	if($liste=="")
		echo "<div id=\"petiteannonce_aucune\">C'est malheureux, aucune petite annonce n'a &eacute;t&eacute; saisie cette semaine</div>";
	else{
		// $limite = 10;
		$nb_check = 0;
		$limite = count($tab_requete_petiteannonce);
		for($i=0;$i<$limite;$i++){
			if(substr($tab_requete_petiteannonce[$i]["url_image"],0,7)!="http://")
				$tab_requete_petiteannonce[$i]["url_image"] = "http://www.ensembleici.fr/".$tab_requete_petiteannonce[$i]["url_image"];
			?>
			<div onclick="verif_check(this.getElementsByTagName('input')[0]);" style="margin:10px;width:120px;height:100px;position:relative;overflow:hidden;border-radius:5px;display:inline-block;border:1px solid #E3D6C7;box-shadow:0px 0px 10px #<?php if(est_dans_liste($tab_requete_petiteannonce[$i]["no"],$liste_choisie)) echo "96DF5F"; else echo "aaa"; ?>">
				<img src="<?php echo $tab_requete_petiteannonce[$i]["url_image"]; ?>" style="position:absolute;" style="visiblity:hidden;" onload="placePhotoDansCadre(this,this.parentNode)" />
				<div style="width:100%;height:20px;position:absolute;top:0px;left:0px;background-color:white;text-indent:10px;">
					<?php if(est_dans_liste($tab_requete_petiteannonce[$i]["no"],$liste_choisie)){ ?>
					<input type="checkbox" id="petiteannonce_check_<?php echo $tab_requete_petiteannonce[$i]["no"]; ?>" style="position:relative;top:3px;visibility:hidden;" checked="checked" />&nbsp;<label style="text-align:left;display:inline;padding:0px;margin:0px;width:auto;float:none;font-size:10px;" for="petiteannonce_check_<?php echo $tab_requete_petiteannonce[$i]["no"]; ?>">retirer</label>
					<?php }else{ 
						$nb_check++;
					?>
					<input type="checkbox" id="petiteannonce_check_<?php echo $tab_requete_petiteannonce[$i]["no"]; ?>" style="position:relative;top:3px;visibility:hidden;" />&nbsp;<label style="text-align:left;display:inline;padding:0px;margin:0px;width:auto;float:none;font-size:10px;" for="petiteannonce_check_<?php echo $tab_requete_petiteannonce[$i]["no"]; ?>">ajouter</label>
					<?php } ?>
					<img class="icone" width="12" height="12" title="Supprimer" alt="Supprimer" src="../../img/admin/icoad-supp.png" style="position:relative;top:3px;cursor:pointer;float:right;" onclick="supprimer(<?php echo $tab_requete_petiteannonce[$i]["no"]; ?>,'petiteannonce')">
				</div>
				<div style="width:100%;position:absolute;bottom:0px;left:0px;background-color:white;text-align:center;font-size:10px;opacity:0.9"><?php echo "<span style=\"font-weight:bold;\">".$tab_requete_petiteannonce[$i]["titre"]."</span><br/>"; if($tab_requete_petiteannonce[$i]["monetaire"]==1) echo "(transaction mon&eacute;taire)"; ?></div>
				<?php if($tab_requete_petiteannonce[$i]["apparition_lettre"]>0){ ?>
					<div style="display:inline-block;width:auto;min-width:26px;text-align:center;height:26px;border-radius:15px 15px 15px 15px;background-color:#D90000;color:white;font-weight:bold;font-size:21px;position:absolute;right:10px;top:10px;border:4px solid #E7E8E8;box-shadow: 0px 0px 12px #aaa;"><?php echo $tab_requete_petiteannonce[$i]["apparition_lettre"]; ?></div>
				<?php } ?>
			</div>
			<?php
		}
	}
?>
</div>
<br/>
<div>
	<a id="boitepetiteannonce" class="boutonrouge ico-loupe-rge" title="Voir la bo&icirc;te" href="">Voir la bo&icirc;te</a>
	<a id="ajoutpetiteannonce" class="boutonbleu ico-ajout" title="Promouvoir une petite annonce" href="">Promouvoir une petite annonce</a>
	<button onclick="valider_etape('petiteannonce');return false;" class="boutonbleu ico-fleche" id="valider_petiteannonce">Valider l'&eacute;tape</button>
</div>
<?php
}
else{
//Si l'�tape est valide
?>
<br/>
<div style="width:650px;margin:auto;border: 1px solid #E3D6C7;padding:10px;background-color:white;">
<?php
include "lettre_en_cours/petiteannonce.php";
?>
</div>
<br/>
<button onclick="de_valider_etape('petiteannonce');return false;" class="boutonbleu ico-fleche" id="annuler_petiteannonce">Retour en mode cr&eacute;ation</button>
<?php
}
?>