<?php
header('Content-Type: text/html; charset=ISO-8859-15');
function surligne_recherche($phrase,$tab_mot_cle){
	$phrase = strip_tags($phrase);
	$tab_remplacement = array();
	$tab_a_remplacer = array();
	for($i=0;$i<count($tab_mot_cle);$i++){
		if($tab_mot_cle[$i]!=""){
			if(preg_match("#(".$tab_mot_cle[$i].")#i", $phrase, $result)){
				$phrase = str_replace($result[0],"|!".$i."!|",$phrase);
				$tab_remplacement[] = "<span style=\"background-color:#bbddff;\">".$result[0]."</span>";
				$tab_a_remplacer[] = "|!".$i."!|";			
			}
		}
	}
	return str_replace($tab_a_remplacer, $tab_remplacement, $phrase);
}
require_once('../../../01_include/_connect.php');
$is_no = (bool)$_POST["n"];
if($is_no){
	$mot=$_POST["m"];
	$cond = " WHERE evenement.no REGEXP '".$mot."'";
}
else{
	$les_mots_cles = array();
	$mot=urldecode($_POST["m"]);
	if($mot!=""){
		$mot = explode(" ",$mot);
		$cond = "";
		for($i=0;$i<count($mot);$i++){
			$mot_cle = $mot[$i];
			// Pour les appostrophe l' mot, l ' mot, l 'mot, l mot l� mot, l � mot, l �mot.
//				$mot_cle = preg_replace("#\s([a-z])(\�|\')\s([a-z][a-z]+)#i", ' $1[\'�]$2', $mot_cle);
//				$mot_cle = preg_replace("#\s([a-z])\s(\�|\')\s([a-z][a-z]+)#i", ' $1[\'�]$2', $mot_cle);
//				$mot_cle = preg_replace("#\s([a-z])\s(\�|\')([a-z][a-z]+)#i", ' $1[\'�]$2', $mot_cle);
//				$mot_cle = preg_replace("#\s([a-z])\s([a-z][a-z]+)#i", ' $1[\'�]$2', $mot_cle);
//				// echo '�';
//				
//				$mot_cle = addslashes($mot_cle);
//			// Pour les singuliers -> pluriels
//				$mot_cle = preg_replace('#([a-r]|[t-z])\s#i', '($1 |$1s )', $mot_cle);
//				//Pour les singulier -> pluriels (fin de ligne)
//					$mot_cle = preg_replace('#([a-r]|[t-z])$#i', '($1|$1s)', $mot_cle);
//			// Pour les pluriels -> singuliers
//				// $mot_cle = preg_replace('#([a-r]|[t-z])\s#i', '($1 |$1s )', $mot_cle);
//			// Pour les caract�res "-", "_" et espace
//				$mot_cle = preg_replace('#(\-|_|\s)#i', '(_|-| )', $mot_cle);
//			// Pour les accents (a, e, i, o, u)
//				$mot_cle = preg_replace('#[a����]#i', '([a����]|(&a[a-z]{3,6};))', $mot_cle); //(&a[a-z]{3,6};)
//				$mot_cle = preg_replace('#[e����]#i', '([e����]|(&e[a-z]{3,6};))', $mot_cle); //
//				$mot_cle = preg_replace('#[o����]#i', '([o����]|(&o[a-z]{3,6};))', $mot_cle); //(&o[a-z]{3,6};)
//				$mot_cle = preg_replace('#[i���]#i', '([i���]|(&i[a-z]{3,6};))', $mot_cle); //(&i[a-z]{3,6};)
//				$mot_cle = preg_replace('#[u���]#i', '([u���]|(&u[a-z]{3,6};))', $mot_cle); //(&u[a-z]{3,6};)
//				$mot_cle = preg_replace('#[c�]#i', '([c�]|(&ccedil;))', $mot_cle); //(&ccedil;)
				
				if($cond=="")
					$cond .= " WHERE ";
				else
					$cond .= " AND ";
				$cond .= "( evenement.titre REGEXP '^".$mot_cle."' OR evenement.titre REGEXP ' ".$mot_cle."' )";
				$les_mots_cles[] = $mot_cle;
		}
	}
}
//On r�cup�re la date de d�but de la lattre
$requete_d = "SELECT lettreinfo.date_debut AS d FROM lettreinfo WHERE no=:no";
$res_requete_d = $connexion->prepare($requete_d);
$res_requete_d->execute(array(":no"=>$_POST["no_l"])) or die ("requete ligne 11 : ".$requete_d);
$tab_requete_d = $res_requete_d->fetchAll();
$date_debut_lettre = $tab_requete_d[0]["d"];

$requete = "SELECT evenement.no, evenement.titre,evenement.date_debut,evenement.date_fin, villes.nom_ville AS ville, genre.type_genre AS a_e FROM evenement JOIN villes ON villes.id = evenement.no_ville JOIN genre ON evenement.no_genre=genre.no ".$cond." AND evenement.etat=1 AND date_fin>=:d LIMIT 8";
$res_requete = $connexion->prepare($requete);
$res_requete->execute(array(":d"=>$date_debut_lettre)) or die ("requete ligne 11 : ".$requete);
$tab_requete = $res_requete->fetchAll();
$reponse = array();
if(count($tab_requete)>0){
	for($i=0;$i<count($tab_requete);$i++){
		// On calcul le texte correspondant aux dates.
		$date_debut_tab = explode("-",$tab_requete[$i]["date_debut"]);
		$date_debut = $date_debut_tab[2]."/".$date_debut_tab[1]."/".$date_debut_tab[0];
		if($tab_requete[$i]["date_debut"]!=$tab_requete[$i]["date_fin"]){
			$date_fin_tab = explode("-",$tab_requete[$i]["date_fin"]);
			$date_fin = $date_fin_tab[2]."/".$date_fin_tab[1]."/".$date_fin_tab[0];

			$dates = "du ".$date_debut." au ".$date_fin;
		}
		else{
			$dates = "le ".$date_debut;
		}
		if($tab_requete[$i]["a_e"]=="E")
			$a_e = true;
		else
			$a_e = false;
		if($_POST["n"]==1)
			$reponse[] = array($tab_requete[$i]["no"],"<div style=\"display:inline;color:#2E2E2E;font-size:14px;\">".surligne_recherche($tab_requete[$i]["no"],array($mot))."</div> : <div style=\"display:inline;font-size:14px;font-weight:bold;color:#575757;\">".html_entity_decode($tab_requete[$i]["titre"])."</div>&nbsp;&nbsp;<div style=\"display:inline;color:#4E4F4F;font-size:12px;\">(".$tab_requete[$i]["ville"]." - ".$dates.")</div>",$a_e);
		else
			$reponse[] = array($tab_requete[$i]["no"],"<div style=\"display:inline;color:#2E2E2E;font-size:14px;\">".$tab_requete[$i]["no"]."</div> : <div style=\"display:inline;font-size:14px;font-weight:bold;color:#575757;\">".surligne_recherche(html_entity_decode($tab_requete[$i]["titre"]),$les_mots_cles,$mot)."</div>&nbsp;&nbsp;<div style=\"display:inline;color:#4E4F4F;font-size:12px;\">(".$tab_requete[$i]["ville"]." - ".$dates.")</div>",$a_e);
	}
}
else{
	$reponse[0] = false;
}
echo json_encode($reponse);
			
?>
