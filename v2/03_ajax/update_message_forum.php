<?php
// Affichage des pages villes
session_name("EspacePerso");
session_start();
//if(!isset($_SESSION['date_pa']) || ($_SESSION['date_pa']=="")) $_SESSION['date_pa']=1;
require ('../01_include/_var_ensemble.php');
require ('../01_include/_connect.php');
if(est_connecte()){
	$no_utilisateur = $_SESSION["UserConnecte_id"];
	//On regarde soit que l'utilisateur est admin.
	$continuer = est_admin();
	if(!$continuer){
		//Sinon on regarde que l'utilisateur modifie un message qui lui appartient.
		$requete_user = "SELECT no, no_message, no_forum FROM messageForum WHERE no_utilisateur_creation=:nou AND no=:no";
		$res_user = $connexion->prepare($requete_user);
		$res_user->execute(array(":no"=>$no,":nou"=>$no_utilisateur));
		$tab_user = $res_user->fetchAll();
		$continuer = (count($tab_user)>0);
		$commentaire = ($tab_user[0]["no_message"]!=0);
		$no_message = ($commentaire)?$tab_user[0]["no_message"]:$tab_user[0]["no"];
		$no_forum = $tab_user[0]["no_forum"];
	}
	
	if($continuer){
		$no = $_POST["no"];
		$contenu = urldecode($_POST["contenu"]);
		if($no>0){
			$requete_update = "UPDATE messageForum SET contenu=:c WHERE no=:no";	
			$res_update = $connexion->prepare($requete_update);
			$res_update->execute(array(":c"=>$contenu,":no"=>$no));
			
			//On récupère les informations que l'on a pas sur le message et sur l'utilisateur.
			$requete_pseudo = "SELECT pseudo FROM utilisateur WHERE no=:no";
			$res_pseudo = $connexion->prepare($requete_pseudo);
			$res_pseudo->execute(array(":no"=>$no_utilisateur));
			$tab_pseudo = $res_pseudo->fetchAll();
			$pseudo = $tab_pseudo[0]["pseudo"];

			$requete_forum = "SELECT titre FROM forum WHERE no=:no";
			$res_forum = $connexion->prepare($requete_forum);
			$res_forum->execute(array(":no"=>$no_forum));
			$tab_forum = $res_forum->fetchAll();
			$titre_sujet = $tab_forum[0]["titre"];
			
			$contenu_message = $contenu;
			
			$url_desinscription = "";
			$url_message = $root_site."forum.".url_rewrite($titre_sujet).".".$no_forum.".html#message".$no_message;
			$url_forum = $root_site."forum.".url_rewrite($titre_sujet).".".$no_forum.".html";
			$update = true;
			
			//On récupère les header et footer html
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_header.php");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			$HEADER_HTML = curl_exec($ch);
			curl_close($ch);
			//On récupère les header et footer html
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_footer.php");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			$FOOTER_HTML = curl_exec($ch);
			curl_close($ch);
			
			function formater_objet($o){
				$o = html_entity_decode($o);
				$o = strip_tags($o);

				// $o = preg_replace("#\s([a-z])(\’|\')\s([a-z][a-z]+)#i", ' $1 $2', $o);
				// $o = preg_replace("#\s([a-z])\s(\’|\')\s([a-z][a-z]+)#i", ' $1 $2', $o);
				// $o = preg_replace("#\s([a-z])\s(\’|\')([a-z][a-z]+)#i", ' $1 $2', $o);
				// $o = preg_replace("#\s([a-z])\s([a-z][a-z]+)#i", ' $1 $2', $o);
				$o = str_replace("'"," ",$o);
				$o = str_replace('"',"",$o);
	
			// Pour les accents (a, e, i, o, u)
				$o = preg_replace('#[ãàâä]#iu', 'a', $o); //(&a[a-z]{3,6};)
				$o = preg_replace('#[éèëê]#iu', 'e', $o); //
				$o = preg_replace('#[õòöô]#iu', 'o', $o); //(&o[a-z]{3,6};)
				$o = preg_replace('#[ìîî]#iu', 'i', $o); //(&i[a-z]{3,6};)
				$o = preg_replace('#[ùûü]#iu', 'u', $o); //(&u[a-z]{3,6};)
				$o = preg_replace('#ç#iu', 'c', $o); //(&ccedil;)
	
				$o = trim($o);
				return $o;
			}
			
			include "envoyer_alerte_admin.php";
			
			$reponse = array(true,"");
		}
		else
			$reponse = array(false,"Une erreur est survenue veuillez réessayer...");
		
	}
	else
		$reponse = array(false, "vous n'avez pas les droits nescessaires");
}
else{
	$reponse = array(false,"Vous n'êtes plus connecté");
}
echo json_encode($reponse);
?>
