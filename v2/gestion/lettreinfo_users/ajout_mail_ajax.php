<?php
session_name("EspacePerso");
session_start();
require_once "config.php";
$reponse = array();
if($_POST["v"]!=""){
	function emailCheck($email){
		// Auteur : bobocop (arobase) bobocop (point) cz
		// Traduction des commentaires par mathieu
		
		// Le code suivant est la version du 2 mai 2005 qui respecte les RFC 2822 et 1035
		// http://www.faqs.org/rfcs/rfc2822.html
		// http://www.faqs.org/rfcs/rfc1035.html	
		$atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // caractères autorisés avant l'arobase
		$domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // caractères autorisés après l'arobase (nom de domaine)	                               
		$regex = '/^' . $atom . '+' .   // Une ou plusieurs fois les caractères autorisés avant l'arobase
		'(\.' . $atom . '+)*' .         // Suivis par zéro point ou plus
										// séparés par des caractères autorisés avant l'arobase
		'@' .                           // Suivis d'un arobase
		'(' . $domain . '{1,63}\.)+' .  // Suivis par 1 à 63 caractères autorisés pour le nom de domaine
										// séparés par des points
		$domain . '{2,63}$/i';          // Suivi de 2 à 63 caractères autorisés pour le nom de domaine
		
		// test de l'adresse e-mail
		if (preg_match($regex, $email)) {
			return true;
		} else {
			return false;
		}
	}

	function extractEmailsFromString($sChaine){
		if(false !== preg_match_all('`\w(?:[-_.]?\w)*@\w(?:[-_.]?\w)*\.(?:[a-z]{2,4})`', $sChaine, $aEmails)) {
			if(is_array($aEmails[0]) && sizeof($aEmails[0])>0) {
				return array_unique($aEmails[0]);
			}
		}	
		return null;
	}
	if($_POST["m"]!=""){
		$reponse[0] = true;
		$les_mails = extractEmailsFromString(urldecode($_POST["m"]));
		$nb_modif = 0;
		$nb_ajout = 0;
	}
	else if($_POST["t"]!=""){
		$nb_modif = $_POST["nbm"];
		$nb_ajout = $_POST["nba"];
		$reponse[0] = true;
		$les_mails = explode(";",urldecode($_POST["t"]));
	}
	else{
		//Une erreur est alors survenue
		$reponse[0] = false;
	}
	
	if($reponse[0]){ //Si $reponse[0] alors les paramètres sont bons, on continue
		$NB_MAX = 50;
		
		if(count($les_mails)>$NB_MAX){
			$max = $NB_MAX;
		}
		else{
			$max = count($les_mails);
		}
		
		for($i=0;$i<$max;$i++){
			if(emailCheck($les_mails[$i])){
				//On vérifie chaque entrée, et l'insère si elle n'existe pas.
				$requete_existe = "SELECT email,no_ville FROM newsletter WHERE email=:e";
				$res_existe = $connexion->prepare($requete_existe) or die("pb prepare");
				$res_existe->execute(array(":e"=>$les_mails[$i])) or die("pb execute 1");
				$existe = $res_existe->fetchAll();
				if(count($existe)==0){ //On insère
					//On calcul le code désinscription
					$code_desinscription = md5($les_mails[$i].$cle_cryptage);
					$requete_insertion = "INSERT INTO newsletter(email,no_ville,etat,code_desinscription_nl) VALUES(:e,:v,1,:c)";
					$res_insertion = $connexion->prepare($requete_insertion) or die("pb prepare");
					$res_insertion->execute(array(":e"=>$les_mails[$i],":v"=>$_POST["v"],":c"=>$code_desinscription)) or die("pb execute 2 : ".$les_mails[$i]);
					$nb_ajout++;
				}
				else{ //Si elle existe et que la ville est différent, on met à jour
					if($existe[0]["no_ville"]!=$_POST["v"]){
						$requete_update = "UPDATE newsletter SET no_ville=:v WHERE email=:e";
						$res_update = $connexion->prepare($requete_update) or die("pb prepare");
						$res_update->execute(array(":e"=>$les_mails[$i],":v"=>$_POST["v"])) or die("pb execute 3");
						$nb_modif++;
					}
				}
			}
		}
		//S'il reste des adresses, on les retourne
		if($max!=count($les_mails)){ //Là il reste des adresses
			$reponse[1] = true;
			$les_mails_non_inseres = array();
			for($j=$max;$j<count($les_mails);$j++){
				$les_mails_non_inseres[] = $les_mails[$j];
			}
			$reponse[2] = array("adresses"=>$les_mails_non_inseres,"ville"=>$_POST["v"],"nbm"=>$nb_modif,"nba"=>$nb_ajout);
		}
		else{ //Là il n'y a plus d'adresse
			$reponse[1] = false;
			$reponse[2] = array("nbm"=>$nb_modif,"nba"=>$nb_ajout);
		}
	}
}
else{
	$reponse[0] = false;
}
echo json_encode($reponse);
?>