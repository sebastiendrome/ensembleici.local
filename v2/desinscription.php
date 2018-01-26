<?php
	session_name("EspacePerso");
	session_start();
	require_once ('01_include/_connect.php');
	require_once ('01_include/_var_ensemble.php');
	
	//Gestion des différentes désinscriptions.
	if(isset($_GET["f"])&&!empty($_GET["f"])){ //désinscription d'un fil de discussion
		$no_utilisateur = $_GET['u'];
		$no_forum =  $_GET['f'];
		$no_message =  $_GET['m'];
	
		// Suppresion   
		$sqlp = "UPDATE `forum_inscription` 
					SET inscrit = 0				
					WHERE no_forum = :f
					AND no_utilisateur = :u
					AND no_message = :m";	
		
			$sup= $connexion->prepare($sqlp);
			$sup->execute(array(':f'=>$no_forum,':u'=>$no_utilisateur,':m'=>$no_message)) or die ("Erreur ".__LINE__." : ".$sqlp);
		$nb_delete = $sup->rowCount();
		
		if($nb_delete>0){
			$requete_forum = "SELECT titre FROM forum WHERE no=:no";
			$res_forum = $connexion->prepare($requete_forum);
			$res_forum->execute(array(":no"=>$no_forum));
			$tab_forum = $res_forum->fetchAll();
			$titre_forum = $tab_forum[0]["titre"];
		
			if($no_message==0)
				$message_utilisateur = "Vous ne serez plus informé lorsq'un nouveau message sera posté dans le forum : <b>".$titre_forum."</b>";
			else
				$message_utilisateur = "Vous ne serez plus informé lorsque le message concerné sera commenté.";
		}
		else{
			$erreur_desinscription = true;
			
			$select = "SELECT * FROM forum_inscription WHERE no_forum=:f AND no_message=:m AND no_utilisateur=:u";
			$res = $connexion->prepare($select);
			$res->execute(array(':f'=>$no_forum,':u'=>$no_utilisateur,':m'=>$no_message));
			$tab = $res->fetchAll();
			if(count($tab)>0){
				$message_utilisateur = "Vous n'étiez déjà plus abonné aux alertes de ce ".(($no_message==0)?"forum":"message");
			}
			else{			
				$message_utilisateur = "Une erreur est survenue. Veuillez nous en excuser, et contacter ".$email_forum." si le problème persiste.";
			}
		}
			
		$titre_page = "Désinscription du forum";
		$meta_description = "Désinscription du fil de discussion du forum.";
		
	}
	else{ //désinscription à la lettre 'information
		// Récup des variables
		$cde = $_GET["codoff"];
		// typoff => Uniquement des lettres
		if (preg_match("/^[A-Za-z]+$/",$_GET["typoff"]))
		{
			$tbl = urldecode($_GET["typoff"]);	
			if ( !($tbl=="utilisateur" || $tbl=="newsletter") )
				$erreur_desinscription = true;
		}
		else
			$erreur_desinscription = true;


		if (!$erreur_desinscription) {

			if($tbl=="utilisateur")
				$ch = "newsletter";
			else
				$ch = "etat";

			// On récupère les informations sur l'utilisateur
			$requete = "SELECT email AS e FROM ".$tbl." WHERE code_desinscription_nl=:cde";
			$res_requete = $connexion->prepare($requete);
			$res_requete->execute(array(":cde"=>$cde));
			$tab_requete = $res_requete->fetchAll();
			if(count($tab_requete)==1) 
			{
				$email_utilisateur = $tab_requete[0]["e"];
				$requete_up = "UPDATE ".$tbl." SET ".$ch."=0 WHERE code_desinscription_nl=:cde";
				$res_requete_up = $connexion->prepare($requete_up);
				$res_requete_up->execute(array(":cde"=>$cde));
				$message_utilisateur = "L'adressse <strong>".$email_utilisateur."</strong> a été supprimée des destinataires de la lettre d'information Ensemble ici.";
			}
			else
			{
				$erreur_desinscription = true;
				$message_utilisateur = "Une erreur est survenue lors de la désinscription. Veuillez nous en excuser et envoyer un email à ".$email_admin." pour demander votre desinscription.";
			}
		}
		$titre_page = "Désinscription de la lettre d'information";
		$meta_description = "Ensemble ici : Tous acteurs de la vie locale";
	}
	
	$erreur_desinscription = false;

	
 	/* $chem_css_ui = $root_site."css/jquery-ui-1.8.21.custom.css";
	<link rel="stylesheet" href="$chem_css_ui" type="text/css" />
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script> */
	// include header
	include ('01_include/structure_header.php');
?>

      <div id="colonne2" class="page_inscription">
		<?php
		echo $message_utilisateur;
			echo "";
		?>
      </div>
<?php
	// Colonne 3
	$affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');

	// Footer
	include ('01_include/structure_footer.php');
?>
