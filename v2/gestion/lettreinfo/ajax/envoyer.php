<?php
include "config_pear.php";
require_once('../../../01_include/_connect.php');
$nb_mail = 5;
if(isset($_POST["no"])&&$_POST["no"]!=""&&$_POST["no"]!=0&&isset($_POST["nb"])&&$_POST["nb"]!=""&&$_POST["nb"]!=0){
	$requete_info = "SELECT lettreinfo.no_envoi AS no_e, lettreinfo_envoi.nb_envoi AS nb_e FROM lettreinfo JOIN lettreinfo_envoi ON lettreinfo_envoi.no=lettreinfo.no_envoi WHERE lettreinfo.no=:no";
	$res_info = $connexion->prepare($requete_info);
	$res_info->execute(array(":no"=>$_POST["no"]));
	$tab_info = $res_info->fetchAll();
	$no_envoi = $tab_info[0]["no_e"];
	$nb_envoi = $tab_info[0]["nb_e"];
	if($nb_envoi==0){
		$requete_deb = "UPDATE lettreinfo_envoi SET date_debut=CURRENT_TIMESTAMP WHERE no=:no";
		$res_deb = $connexion->prepare($requete_deb);
		$res_deb->execute(array(":no"=>$no_envoi));
	}
	
	$file_denvoi_de_mail =& new Mail_Queue($db_options, $mail_options);
	$file_denvoi_de_mail->sendMailsInQueue($nb_mail);

	$a_envoyer = $file_denvoi_de_mail->getQueueCount();
	$nb_envoye = ($_POST["nb"]-$a_envoyer);
	$requete = "UPDATE lettreinfo_envoi SET nb_envoi=:nb WHERE no=:no";
	$res = $connexion->prepare($requete);
	$res->execute(array(":nb"=>$nb_envoye,":no"=>$no_envoi));
	if($a_envoyer>0) {
            sleep(1);
            $reponse = array(true,true); 
        }
	else {
		function seconde_to_string($s){
			if($s>60){
				$nb_s = $s%60;
				$m = floor($s/60);
				if($m>60){
					$nb_m = $m%60;
					$h = floor($m/60);
					if($h>24){
						$nb_h = $h%24;
						$j = floor($h/24);
						return $j."j ".$h."h ".$m."m ".$nb_s."s";
					}
					else{
						return $h."h ".$m."m ".$nb_s."s";
					}
				}
				else{
					return $m."m ".$nb_s."s";
				}
			}
			else{
				return $s."s";
			}
		}
		$requete_fin = "UPDATE lettreinfo_envoi SET date_fin=CURRENT_TIMESTAMP WHERE no=:no";
		$res_fin = $connexion->prepare($requete_fin);
		$res_fin->execute(array(":no"=>$no_envoi));
		//On r�cup�re le temps d'envoi
		$requete_time = "SELECT TIMESTAMPDIFF(SECOND,date_debut,date_fin) AS s FROM `lettreinfo_envoi` WHERE no=:no";
		$res_time = $connexion->prepare($requete_time);
		$res_time->execute(array(":no"=>$no_envoi));
		$tab_time = $res_time->fetchAll();
		$temps_envoi = $tab_time[0]["s"];
		$reponse = array(true,false,utf8_encode("La liste de mails a été envoyée en ".seconde_to_string($temps_envoi)));
	}
}
else{
	$reponse = array(false,utf8_encode("Une erreur est survenue, veuillez recharger la page !"));
}
echo json_encode($reponse);
?>