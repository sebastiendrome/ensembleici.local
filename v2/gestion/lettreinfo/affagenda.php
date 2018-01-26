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
	Si oui : on affiche le bout de lettre en prennant les �v�nements de liste_petiteannonce_valide
	
	Si non : 
		On regarde si liste_petiteannonce est vide
			Si non : on affiche les �v�nements de liste_evenement
				On récup�re les �ventuels �v�nements apparus entre temps (date cr�ation > date_modification)
			Si oui : on r�cup�re la liste des �v�nements 
*/
$no_lettre = $_POST["id"];
$requete_generale = "SELECT * FROM lettreinfo_agenda WHERE no_lettre=:no";
$res_requete_generale = $connexion->prepare($requete_generale);
$res_requete_generale->execute(array(":no"=>$no_lettre)) or die("erreur requ�te ligne 116 : ".$requete_generale);
$tab_requete_generale = $res_requete_generale->fetchAll();
$insertion = false;
//Du coup pour l'agenda, on r�cup�re la date de d�but de la lettre d'information.
	$req_date_lettre = "SELECT date_debut AS d, territoires_id as t FROM lettreinfo WHERE no=:no";
	$res_date_lettre = $connexion->prepare($req_date_lettre);
	$res_date_lettre->execute(array(":no"=>$no_lettre));
	$rep_date_lettre = $res_date_lettre->fetchAll();
	$date_debut_lettre = $rep_date_lettre[0]["d"];
        $territoire = $rep_date_lettre[0]["t"];

if(count($tab_requete_generale)>0){
	$etape_valide = (bool)$tab_requete_generale[0]["etape_valide"];
	if($etape_valide){
		//L'�tape est d�j� valid�e : on affiche le rendu final, ainsi qu'un bouton "modifier"
		$liste = (string)$tab_requete_generale[0]["liste_evenement_valide"];
	}
	else{
		$date_debut = time();
		$num_jour_courant = date("N");
		$nb_jour_dimanche = 7-$num_jour_courant;
		//On calcul le timestamp du dimanche qui arrive
		$date_fin = $date_debut + ($nb_jour_dimanche+7)*24*60*60;
		$date_debut_lettre = date("Y-m-d");
		$date_fin_lettre = date("Y-m-d",$date_fin);
		
		//L'�tape n'est pas valid�e : on regarde la liste
		$liste = (string)$tab_requete_generale[0]["liste_evenement_complete"];
		$liste_choisie = (string)$tab_requete_generale[0]["liste_evenement"];
		//On charge aussi tous les �v�nements apparues depuis la derni�re modification (donc qui ne sont pas dans la liste)
		//$requete_liste_agenda = "SELECT no FROM evenement WHERE evenement.etat=1 AND evenement.titre NOT LIKE '%hebdomadaire%' AND evenement.no_genre<>24 AND evenement.apparition_lettre<2 AND evenement.date_creation>:d AND date_debut<=:d_f AND date_fin>=:d_d";
                $requete_liste_agenda = "SELECT E.no FROM evenement E, communautecommune_ville T, communautecommune C WHERE E.etat=1 AND E.titre NOT LIKE '%hebdomadaire%' AND E.no_genre<>24 AND E.apparition_lettre<2 AND E.date_creation>:d AND E.date_debut<=:d_f AND E.date_fin>=:d_d AND T.no_ville = E.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t";
		$res_liste_agenda = $connexion->prepare($requete_liste_agenda);
		$res_liste_agenda->execute(array(":d"=>$tab_requete_generale[0]["date_modification"],":d_d"=>$date_debut_lettre,":d_f"=>$date_fin_lettre, ":t" => $territoire));
                $tab_liste_agenda = $res_liste_agenda->fetchAll();
		$old_liste = $liste;
		for($i=0;$i<count($tab_liste_agenda);$i++){
			if($liste!="")
				$liste .= ",";
			$liste .= $tab_liste_agenda[$i]["no"];
			if($liste_choisie!="")
				$liste_choisie .= ",";
			$liste_choisie .= $tab_liste_agenda[$i]["no"];
		}
		if($old_liste!=$liste){ //Si la liste est diff�rente de l'ancienne, alors il faut enregistrer les nouveaux �v�nements
			$requete_update_liste_agenda = "UPDATE lettreinfo_agenda SET liste_evenement_complete=:l,liste_evenement=:lc WHERE no_lettre=:no";
			$res_update_liste_agenda = $connexion->prepare($requete_update_liste_agenda);
			$res_update_liste_agenda->execute(array(":l"=>$liste,":lc"=>$liste_choisie,":no"=>$no_lettre));
		}
	}
}
else{
	$insertion = true;
}

if($insertion||$modification){

	$date_debut = time();
	$num_jour_courant = date("N");
	$nb_jour_dimanche = 7-$num_jour_courant;
	//On calcul le timestamp du dimanche qui arrive
	$date_fin = $date_debut + ($nb_jour_dimanche+7)*24*60*60;
	$date_debut_lettre = date("Y-m-d");
	$date_fin_lettre = date("Y-m-d",$date_fin);
	
	//La liste temporaire est vide, on charge alors tous les �v�nements entr�es depuis la derni�re lettre d'information
		//Par d�faut, on coche les 5 derni�res.
	
	//On r�cup�re la date de modification de cette �tape pour la lettre pr�c�dente
	/*
	$requete_date_mod_derniere_lettre = "SELECT date_modification AS d FROM lettreinfo_agenda WHERE no_lettre<>:no ORDER BY date_modification DESC LIMIT 1";
	$res_date_mod_derniere_lettre = $connexion->prepare($requete_date_mod_derniere_lettre);
	$res_date_mod_derniere_lettre->execute(array(":no"=>$no_lettre));
	$rep_date_mod_derniere_lettre = $res_date_mod_derniere_lettre->fetchAll();
	$date_mod_derniere_lettre = $rep_date_mod_derniere_lettre[0]["d"];*/
	
	//On r�cup�re maintenant la liste des petiteannonces dont la date de cr�ation est sup�rieur � la date de la derni�re lettre
	//$requete_liste_agenda = "SELECT evenement.no FROM evenement WHERE evenement.etat=1 AND evenement.titre NOT LIKE '%hebdomadaire%' AND evenement.no_genre<>24 AND evenement.apparition_lettre<2 AND date_debut<=:d_f AND date_fin>=:d_d ";
	$requete_liste_agenda = "SELECT E.no FROM evenement E, communautecommune_ville T, communautecommune C WHERE E.etat=1 AND E.titre NOT LIKE '%hebdomadaire%' AND E.no_genre<>24 AND E.apparition_lettre<2 AND E.date_debut<=:d_f AND E.date_fin>=:d_d AND T.no_ville = E.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t";
        $res_liste_agenda = $connexion->prepare($requete_liste_agenda);
	$res_liste_agenda->execute(array(":d_d"=>$date_debut_lettre,":d_f"=>$date_fin_lettre, ":t" => $territoire));
	$tab_liste_agenda = $res_liste_agenda->fetchAll();
	
	$liste = "";
	$liste_choisie = "";
	for($i=0;$i<count($tab_liste_agenda);$i++){
		if($liste!="")
			$liste .= ",";
		$liste .= $tab_liste_agenda[$i]["no"];
		if($liste_choisie!="")
			$liste_choisie .= ",";
		$liste_choisie .= $tab_liste_agenda[$i]["no"];
	}
	
	//On ins�re la liste
	$requete_insertion_agenda = "INSERT INTO lettreinfo_agenda(no_lettre,liste_evenement_complete,liste_evenement) VALUES(:no,:l,:l_c)";
	$res_insertion_agenda = $connexion->prepare($requete_insertion_agenda);
	$res_insertion_agenda->execute(array(":no"=>$no_lettre,":l"=>$liste,":l_c"=>$liste_choisie));
}

//On r�cup�re maintenant toutes les informations sur les petiteannonces de la liste
if($liste!=""){
	$requete_agenda = "SELECT evenement.*,genre.type_genre AS a_e, genre.libelle AS genre FROM evenement JOIN genre ON genre.no=evenement.no_genre WHERE evenement.no IN (".$liste.")";
	$res_requete_agenda = $connexion->prepare($requete_agenda);
	$res_requete_agenda->execute();
	$tab_requete_agenda = $res_requete_agenda->fetchAll();
}
if(!$etape_valide){
?>
<div id="agenda_message" style="width:100%;" class="message_sam_info">Vous pouvez valider l'&eacute;tape.</div>
<?php
}else{
?>
<div id="agenda_message" style="width:100%;" class="message_sam_valide">L'&eacute;tape est valid&eacute;e.</div>
<?php
}
?>
<div id="agenda_liste" style="width:100%;margin:auto;">
<?php
if(!$etape_valide){
	if($liste=="")
		echo "<div id=\"agenda_aucune\">C'est malheureux, aucun &eacute;v&eacute;nement est &agrave; pr&eacute;voir pour les 8 jours qui suivent ".datefr($date_debut_lettre)."</div>";
	else{
		// $limite = 10;
		$nb_check = 0;
		$limite = count($tab_requete_agenda);
		for($i=0;$i<$limite;$i++){
			if(substr($tab_requete_agenda[$i]["url_image"],0,7)!="http://")
				$tab_requete_agenda[$i]["url_image"] = "http://www.ensembleici.fr/".$tab_requete_agenda[$i]["url_image"];
			?>
			<div onclick="verif_check(this.getElementsByTagName('input')[0]);" style="margin:10px;width:120px;height:100px;position:relative;overflow:hidden;border-radius:5px;display:inline-block;border:1px solid #E3D6C7;box-shadow:0px 0px 10px #<?php if(est_dans_liste($tab_requete_agenda[$i]["no"],$liste_choisie)) echo "96DF5F"; else echo "aaa"; ?>">
				<img src="<?php echo $tab_requete_agenda[$i]["url_image"]; ?>" style="position:absolute;" style="visiblity:hidden;" onload="placePhotoDansCadre(this,this.parentNode)" />
				<div style="width:100%;height:20px;position:absolute;top:0px;left:0px;background-color:<?php if($tab_requete_agenda[$i]["a_e"]!="A") echo "white"; else echo "#FEE1E1"; ?>;text-indent:10px;">
					<?php
					if(est_dans_liste($tab_requete_agenda[$i]["no"],$liste_choisie)){
					?>
					<input type="checkbox" id="agenda_check_<?php echo $tab_requete_agenda[$i]["no"]; ?>" style="position:relative;top:3px;visibility:hidden;" checked="checked" />&nbsp;<label style="text-align:left;display:inline;padding:0px;margin:0px;width:auto;float:none;font-size:10px;" for="agenda_check_<?php echo $tab_requete_agenda[$i]["no"]; ?>">retirer</label>
					<?php
					}
					else{
					?>
					<input type="checkbox" id="agenda_check_<?php echo $tab_requete_agenda[$i]["no"]; ?>" style="position:relative;top:3px;visibility:hidden;" />&nbsp;<label style="text-align:left;display:inline;padding:0px;margin:0px;width:auto;float:none;font-size:10px;" for="agenda_check_<?php echo $tab_requete_agenda[$i]["no"]; ?>">ajouter</label>
					<?php
					}
					?>
					<img class="icone" width="12" height="12" title="Supprimer" alt="Supprimer" src="../../img/admin/icoad-supp.png" style="position:relative;top:3px;cursor:pointer;float:right;" onclick="supprimer(<?php echo $tab_requete_agenda[$i]["no"]; ?>,'agenda')">
				</div>
				<div style="width:100%;position:absolute;bottom:0px;left:0px;background-color:white;text-align:center;font-size:10px;opacity:0.9;"><?php echo "<span style=\"font-weight:bold;\">".$tab_requete_agenda[$i]["titre"]."</span><br/>"; if($tab_requete_agenda[$i]["date_debut"]!=$tab_requete_agenda[$i]["date_fin"]) echo "du ".datefr($tab_requete_agenda[$i]["date_debut"])." au ".datefr($tab_requete_agenda[$i]["date_fin"]); else echo "le ".datefr($tab_requete_agenda[$i]["date_debut"]); echo "<br/><span style=\"font-weight:bold;\">".$tab_requete_agenda[$i]["genre"]."</span>"; ?></div>
				<?php if($tab_requete_agenda[$i]["apparition_lettre"]>0){ ?>
				<div style="display:inline-block;width:auto;min-width:12px;text-align:center;height:12px;border-radius:10px 10px 10px 10px;background-color:#D90000;color:white;font-weight:bold;font-size:10px;position:absolute;right:10px;top:10px;border:3px solid #E7E8E8;box-shadow: 0px 0px 12px #aaa;"><?php echo $tab_requete_agenda[$i]["apparition_lettre"]; ?></div>
				<?php } ?>
			</div>
			<?php
		}
	}
?>
</div>
<br/>
<div>
	<a id="boiteagenda" class="boutonrouge ico-loupe-rge" title="Voir la bo&icirc;te" href="">Voir la bo&icirc;te</a>
	<a id="ajoutagenda" class="boutonbleu ico-ajout" title="Promouvoir un &eacute;v&eacute;nement" href="">Promouvoir un &eacute;v&eacute;nement</a>
	<button onclick="valider_etape('agenda');return false;" class="boutonbleu ico-fleche" id="valider_agendaSAM">Valider l'&eacute;tape</button>
</div>
<?php
}
else{
//Si l'�tape est valide
?>
<br/>
<div style="width:650px;margin:auto;border: 1px solid #E3D6C7;padding:10px;background-color:white;">
<?php
include "lettre_en_cours/agenda.php";
?>
</div>
<br/>
<button onclick="de_valider_etape('agenda');return false;" class="boutonbleu ico-fleche" id="annuler_agenda">Retour en mode cr&eacute;ation</button>
<?php
}
?>