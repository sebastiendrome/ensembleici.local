<?php
	session_name("EspacePerso");
	session_start();
	// Utilisateur connecté ?
      	require ('01_include/connexion_verif.php');
	// include header
	$titre_page = "Espace personnel";
	$meta_description = "Ensemble ici : Tous acteurs de la vie locale. Accès à votre espace personnel.";
	$espace_perso = true;

	// Retour de la derniere étape des autopres => suppression des variables
	if ((intval($_GET['depuis_derniere_etape']))&&($_REQUEST['depuis_derniere_etape']==1))
	{
		unset($_SESSION['mode_modification_evt']);
		unset($_SESSION['mode_modification_str']);
		unset($_SESSION['mode_modification_pa']);
		unset($_SESSION['etape_arret_form_evt']);
		unset($_SESSION['etape_arret_form_str']);
		unset($_SESSION['etape_arret_form_pa']);
		unset($_SESSION['type_annonce']);
		unset($_SESSION['no_evenement']);
		unset($_SESSION['no_structure']);
		unset($_SESSION['no_pa']);
	}

	/* $chem_css_ui = $root_site."css/jquery-ui-1.8.21.custom.css";
	<link rel="stylesheet" href="$chem_css_ui" type="text/css" />
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script> */
	$ajout_header = <<<AJHE
	<script>
	  $(function() {
	      $( "#tabs" ).tabs({
		  beforeLoad: function( event, ui ) {
		      ui.jqXHR.error(function() {
			  ui.panel.html(
			      "Cette page est en cours de construction..." );
		      });
		  },
		  select: function(event,ui) {
			var url = $.data(ui.tab, 'load.tabs');
			if (url==='01_include/connexion_deconnexion.php'){
			    window.location.href=url;
			    return false;
			}
		  }
	      });
	  });
	</script>
AJHE;
	include ('01_include/structure_header.php');

	// Infos de la ville selectionnée
	if (!empty($id_ville))
	{
		require ('01_include/_connect.php');
		$strQuery = "SELECT no, droits, nom_ville_maj, code_postal, no_ville FROM `utilisateur` U, `villes` V
		WHERE U.no_ville=V.id
		AND id_connexion=:ucid
		AND `email`=:ucemail";
		$res_user = $connexion->prepare($strQuery);
		$res_user->bindParam(":ucid", $UserConnecte_id_fromSession, PDO::PARAM_STR);
		$res_user->bindParam(":ucemail", $UserConnecte_email, PDO::PARAM_STR);		
		$res_user->execute();
		// $tab_us = $res_user->fetchAll();
		$tab_us = $res_user->fetch(PDO::FETCH_ASSOC);
		$titre_ville = $tab_us["nom_ville_maj"];
		$code_postal = $tab_us["code_postal"];
		$no_user = $tab_us["no"];
		// Administrateur ou editeur ?
		if ($tab_us["droits"]=="A") $estAdmin = true;
		if ($tab_us["droits"]=="E") $estEditeur = true;
	}

?>
<div id="colonne2">                  
    <div id="tabs">
        <ul id="menuespacep">
            <li><a href="#tabs-1">Accueil</a></li>
            <li><a href="espace_favori.php">Mes archives</a></li>
            <li><a href="espace_mesannonces.php">Mes annonces</a></li>
            <li><a href="espace_moncompte.php">Mon compte</a></li>
            <li><a href="01_include/connexion_deconnexion.php?page=espace">Deconnexion</a></li>
        </ul>
        <div id="tabs-1">
            <?php
            if (isset($_SESSION['message']))
            {
                echo "<p id=\"message\">".$_SESSION['message']."</p>";
                unset($_SESSION['message']);
            }

			// Bloc de texte géré dans l'admin
			$idbloc = 4;
			$sql_bloc="SELECT titre, contenu
			          FROM `contenu_blocs`
			          WHERE etat = :etat
			          AND no = :idbloc";
			$res_bloc = $connexion->prepare($sql_bloc);
			$res_bloc->execute(array(':idbloc'=>$idbloc, ':etat'=>1));
			$tab_bloc = $res_bloc->fetch(PDO::FETCH_ASSOC);
			
			if ($estAdmin) 
			{
		    	echo '<p>Vous êtes administrateur : <a href="gestion/accueil_admin.php" class="boutonbleu ico-fleche">Accès à la gestion (vieu)</a></p>';
		    	echo '<p><a href="00_dev_sam/gestion" class="boutonbleu ico-fleche">Accès au nouvel espace de gestion</a></p>';
		    }
			elseif ($estEditeur) 
			{
		    	echo '<p>Vous êtes éditeur : <a href="00_dev_sam/gestion" class="boutonbleu ico-fleche">Accès au nouvel espace de gestion</a></p>';
		    }
			
			if ((!empty($tab_bloc["titre"]))||(!empty($tab_bloc["contenu"])))
			{
            	if (!empty($tab_bloc["titre"]))
            		echo "<h3>".$tab_bloc["titre"]."</h3>";
              	if (!empty($tab_bloc["contenu"]))
              		echo "<div class=\"contenu\">".nl2br($tab_bloc["contenu"])."</div>";
			}
			
			if ($estAdmin) 
			{
		    	echo '<p>Vous êtes administrateur : <a href="gestion/accueil_admin.php" class="boutonbleu ico-fleche">Accès à la gestion (vieu)</a></p>';
		    }
			elseif ($estEditeur) 
			{
		    	echo '<p><a href="gestion/accueil_editeur.php" class="boutonbleu ico-fleche">Accès à l\'ancien espace de gestion</a></p>';
		    }
		    ?>
        </div>
        
    </div>
    	

      </div>
    
<?php
	// Colonne 3
	$affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');
	
	// Footer
	include ('01_include/structure_footer.php');
?>
