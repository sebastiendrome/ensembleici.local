<?php
	session_name("EspacePerso");
	session_start();
	// Utilisateur connecté ?
	require ('01_include/connexion_verif.php');
	
	// Pour affichage spécifique des listes evt, struct, pa...etc...
	$espace_perso = true;
	
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
		// $tab_ville = $res_user->fetchAll();
		$tab_ville = $res_user->fetch(PDO::FETCH_ASSOC);
		$titre_ville = $tab_ville["nom_ville_maj"];
		$code_postal = $tab_ville["code_postal"];
		$no_user = $tab_ville["no"];
		if ($tab_ville["droits"]=="A")
			$estAdmin = true;
	}

if(!$estAdmin)
	{
	?>
	<h3>Mes évènements</h3>
		<div class="blocC">
			<div class="boutons ajouterse">
				<a href="ajouter_un_evenement.html" title="Ajouter un évènement" class="boutonbleu ico-ajout">Ajouter un évènement</a>		
			</div>
		<?php
		$no_utilisateur_creation = $no_user;
		$tous_evts = true;
		// Affichage des evenements : condition : $no_utilisateur_creation
		include ('01_include/affiche_agenda.php');
		?>
		</div>
		
	<h3>Mes structures</h3>
		<div class="blocC">
			<div class="boutons ajouterse">
				<a href="ajouter_une_structure.html" title="Ajouter une structure" class="boutonbleu ico-ajout">Ajouter une structure / activité</a>
			</div>
		<?php
		// Affichage des structures : condition : $no_utilisateur_creation
		include ('01_include/affiche_repertoire.php');
		?>	
		</div>
	
	<h3>Mes petites annonces</h3>
		<div class="blocC">
			<div class="boutons ajouterse">
				<a href="ajouter_une_petiteannonce.html" title="Ajouter une petite annonce" class="boutonbleu ico-ajout">Ajouter une petite annonce</a>
			</div>
		<?php
		// Affichage des pa : condition : $no_utilisateur_creation
		include ('01_include/affiche_petiteannonce.php');
		?>	
		</div>

	<?php
	}
	else
	{
	?>
			<div class="boutons ajouterse">
				<a href="ajouter_un_evenement.html" title="Ajouter un évenement" class="boutonbleu ico-ajout">Ajouter un évenement</a>	
				<a href="ajouter_une_structure.html" title="Ajouter une structure" class="boutonbleu ico-ajout">Ajouter une structure / activité</a>
				<a href="ajouter_une_petiteannonce.html" title="Ajouter une petite annonce" class="boutonbleu ico-ajout">Ajouter une petite annonce</a>
			</div>

	<?php
	}
	?>
