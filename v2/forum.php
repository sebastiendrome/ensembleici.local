<?php
	// Affichage des pages villes
	session_name("EspacePerso");
	session_start();
	//if(!isset($_SESSION['date_pa']) || ($_SESSION['date_pa']=="")) $_SESSION['date_pa']=1;
	require ('01_include/_var_ensemble.php');
	require ('01_include/_connect.php');
	// Supprimer la ville enregistrée (supprimer le cookie)
	$changerville = intval($_GET["changerville"]);
	
	if ($changerville==1)setcookie("id_ville", "", time() - 365*24*3600,"/", null, false, true);
	// cookie expiré
	// Récupère la ville sélectionnée, depuis l'URL
	
	if (($_GET["id_ville"])&&(!empty($_GET["id_ville"])))
	{
		$id_ville = intval($_GET["id_ville"]);
		// Si lien du popup de choix de la ville
	
		if ((intval($_GET["choixville"]))&&($_GET["choixville"]==1))setcookie("id_ville", $id_ville, time() + 365*24*3600,"/", null, false, true);
	}

	// depuis le formulaire de recherche d'une ville (colorbox de l'index)
	elseif ((isset($_POST["rech_idville"]))&&(!empty($_POST["rech_idville"])))
	{
		$id_ville = intval($_POST["rech_idville"]);
	}

	// depuis le cookie
	elseif (($_COOKIE["id_ville"])&&(!empty($_COOKIE["id_ville"])))
	{
		$id_ville = intval($_COOKIE["id_ville"]);
	}

	// Sinon popup pour choisir la ville 
	if (((!$id_ville)||(empty($id_ville)))||($changerville))
	{
		$titre_ville = "Choisissez une ville...";

		$ajout_header = <<<AJHE
		<script type="text/javascript">
		  $(function() {
		    $.colorbox({
		      href:"ajax_choix_ville.php",
		      width:"550px",
		      onComplete : function() {
				$(this).colorbox.resize();
				$('#cboxOverlay').fadeOut(4000);
		      }
		    });
		  });
		</script>
AJHE;
		// On prend la ville par défaut (derrière la colorbox)
		$id_ville = $id_ville_defaut;
	}

	// Infos de la ville selectionnée
	if (!empty($id_ville))
	{
		$sql_ville="SELECT * FROM villes WHERE id = :idville";
		$res_ville = $connexion->prepare($sql_ville);
		$res_ville->execute(array(':idville'=>$id_ville));
		$tab_ville = $res_ville->fetch(PDO::FETCH_ASSOC);
		$titre_ville = $tab_ville["nom_ville_maj"];
		$cp_ville = $tab_ville["code_postal"];
		$titre_ville_url = $tab_ville["nom_ville_url"];
		$lat_ville = $tab_ville["latitude"];
		$lon_ville = $tab_ville["longitude"];
	}

	// Une vie sélectionnée ?
	if (($_GET["id_vie"])&&(!empty($_GET["id_vie"])))
	{
		$id_vie = intval($_GET["id_vie"]);
		// Récup nom pour titre
		$sql_v="SELECT * FROM vie WHERE no = :idvie";
		$res_v = $connexion->prepare($sql_v);
		$res_v->execute(array(':idvie'=>$id_vie));
		$tab_v = $res_v->fetch(PDO::FETCH_ASSOC);
		$titre_nomvie = $tab_v["libelle"];
		$nom_url_vie = $tab_v["libelle_url"];
	}
	else
	{
		$id_vie = 0;
	}

	// Un tag sélectionné ?
	if (($_GET["id_tag"])&&(!empty($_GET["id_tag"])))
	{
		$id_tag = intval($_GET["id_tag"]);
		$est_ss_tag = intval($_GET["ss_tag"]);

		// C'est un sous-tag ?
		if ($est_ss_tag)
		{
			// Récup nom pour titre
			$sql_t="SELECT I.libelle AS nom_vie, T.titre AS nom_tag
				FROM `sous_tag` T, `tag_sous_tag` A, vie_tag V, vie I
				WHERE T.no = :idtag
				AND T.no = A.no_sous_tag
				AND A.no_tag = V.no_tag
				AND V.no_vie = I.no";
			$res_t = $connexion->prepare($sql_t);
			$res_t->execute(array(':idtag'=>$id_tag));
			$tab_t = $res_t->fetch(PDO::FETCH_ASSOC);
			$titre_nomvie = $tab_t["nom_vie"];
			$titre_nomtag = $tab_t["nom_tag"];
		}
		else
		{
			// Récup nom pour titre
			$sql_t="SELECT I.libelle AS nom_vie, T.titre AS nom_tag
				FROM tag T, vie_tag V, vie I
				WHERE T.no = :idtag
				AND T.no = V.no_tag
				AND V.no_vie = I.no";
			$res_t = $connexion->prepare($sql_t);
			$res_t->execute(array(':idtag'=>$id_tag));
			$tab_t = $res_t->fetch(PDO::FETCH_ASSOC);
			$titre_nomvie = $tab_t["nom_vie"];
			$titre_nomtag = $tab_t["nom_tag"];			
		}
	}
	else
	{
		$id_tag = 0;
	}

	/*
	// Liens afficher tous les evenements / tout le répertoire (affichage spécifique)
	if ((intval($_GET["specif"]))&&($_GET["specif"]==1))
	{
		$aff_specifique = true;
		if ((intval($_GET["tous_evts"]))&&($_GET["tous_evts"]==1)) $tous_evts = true;
		if ((intval($_GET["tous_struct"]))&&($_GET["tous_struct"]==1)) $tous_struct = true;
		if ((intval($_GET["tous_pa"]))&&($_GET["tous_pa"]==1)) $tous_pa = true;
	}

	// Titre de la page
	
	if ($tous_evts)
		$titre_page = "Agenda de $titre_ville, $cp_ville (tous les évènements)";
	elseif ($tous_struct)
		$titre_page = "Répertoire de $titre_ville, $cp_ville (toutes les structures)";
	elseif ($tous_pa)
		$titre_page = "Petites annonces de $titre_ville, $cp_ville (toutes les petites annonces)";
	elseif ($id_vie)
		$titre_page = "$titre_nomvie, Agenda et répertoire de $titre_ville, $cp_ville";
	elseif ($id_tag)
		$titre_page = "$titre_nomtag ($titre_nomvie), Agenda et répertoire de $titre_ville, $cp_ville";
	else
	{
		// L'accueil de la ville
		$titre_page = "$titre_ville ($cp_ville) : Agenda et répertoire";
		$diaporama = true;
		$page_index = true;
		$affiche_articles = true;
	}*/
	
	$titre_page = "$titre_ville ($cp_ville) : Forums";
	$affiche_articles = true;
	$affiche_publicites = true;

	// include header de la page
	$titre_page_bleu = $titre_ville;
	$meta_description = $titre_page.". Agenda et répertoire de $titre_ville sur Ensemble ici : Tous acteurs de la vie locale";
	$page_ville = true;

	// Bouton modifier
	if ($page_index)
	{
	$ajout_header .= <<<AJHE
<link rel="stylesheet" href="css/homepage.css">
AJHE;
	}
	include ('01_include/structure_header.php');
	?>
	<link rel="stylesheet" type="text/css" href="css/forum.css" />
	<div id="colonne2">
		<section>
			<?php
				//$tous_forums = true;
				//include "01_include/affiche_forum.php";
				include "01_include/affiche_forum.php";
			?>
		</section>
	</div>
	<?php
	//Edito
		include "01_include/structure_colonne3.php";
	// Footer
		include ('01_include/structure_footer.php');
	?>
