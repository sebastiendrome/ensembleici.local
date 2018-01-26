<?php
if(isset($_POST["no"])&&$_POST["no"]!=null&&$_POST["no"]!=0){
	//Dans un second temps, on récupère le texte correspondant à no_lettre
	$requete = "SELECT lettreinfo_envoi.nb_liste AS nb_l, lettreinfo_envoi.nb_envoi AS nb_e FROM lettreinfo_envoi JOIN lettreinfo ON lettreinfo.no_envoi=lettreinfo_envoi.no WHERE lettreinfo.no=:no";
	$res = $connexion->prepare($requete);
	$res->execute(array(":no"=>$_POST["no"]));
	$tab = $res->fetchAll();
	$nb_liste = $tab[0]["nb_l"];
	$nb_envoi = $tab[0]["nb_e"];
	//Enfin, on remplie la liste avec : la liste complète, ou l'adresse en paramètre pour le test
	if(isset($_POST["a"])&&$_POST["a"]!=null&&$_POST["a"]!=""){ //On remplie la file d'attente pour le test
		//On créait l'en-tête
		$from = 'newsletter@africultures.info';
		$destinataire = urldecode($_POST["a"]);
		if(isset($_POST["f"])&&$_POST["f"]==1){
			$message = "envoi terminé : ".$nb_envoi." mails envoyés.";
			$obj = "fin des envois";
		}
		else{
			$message = "envoi ".$nb_envoi." / ".$nb_liste." mails envoyés.";
			$obj = "suivi des envois";
		}

		$headers = 'MIME-Version: 1.0' . " \n";
		$headers .= 'Content-type: text; charset=iso-8859-1' . " \n";
		$headers .= "X-Mailer: PHP/" . phpversion() . "\n" ;
		$headers .= "X-Sender: <www.africultures.info>\n";
		$headers .= "X-auth-smtp-user: newsletter@africultures.info \n";
		$headers .= "X-Priority: 3 \n";
		$headers .= "X-Unsubscribe-Web:<http://www.africultures.com/php/?nav=self&sr=desinscription-lettre>  \n";
		$headers .= "X-Unsubscribe-Email:<mailto:unsubscribe@africultures.com>  \n";
		$headers .= 'From: ' . $from . "\r\n";
		
		mail($destinataire, $obj, $message, $headers);
		
		$reponse = array(false, utf8_encode("message envoyé ! "));
	}
	else{
		$reponse = array(false, utf8_encode("une erreur est survenue, veuiillez réessayer ! "));
	}
}
else{
	$reponse = array(false, utf8_encode("une erreur est survenue, veuiillez réessayer ! "));
}
echo json_encode($reponse);
?>