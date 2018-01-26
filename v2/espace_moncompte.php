<?php
	session_name("EspacePerso");
	session_start();
	// Utilisateur connecté ?
      	require ('01_include/connexion_verif.php');

	// Infos de la ville selectionnée
	if ($UserConnecte_id_fromSession)	
	{
		require ('01_include/_connect.php');
		$strQuery = "SELECT no, droits, nom_ville_maj, code_postal, no_ville, newsletter FROM `utilisateur` U, `villes` V
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
?>
	<h3>Mon compte</h3>
	<div class="blocC">
		<p>Votre email : <strong><?php echo $_SESSION['UserConnecte_email']; ?></strong></p>
		<?php
		if (!empty($titre_ville)) echo "<p>Votre ville de préférence sur Ensemble ici : <strong>".$titre_ville." (".$code_postal.")</strong></p>";

		echo "<p><span class=\"ui-icon ui-icon-mail-closed\" style=\"display:inline-block;\"></span> ";
		if ($tab_ville["newsletter"])
			echo "Vous êtes inscrit à la lettre d'information Ensemble ici.</p>";
		else
			echo "Vous êtes désabonné de la lettre d'information Ensemble ici.</p>";

		?>
		<div class="actions">
			<a href="espace_personnel_modifier_infos.php" title="Editer vos informations personnelles" class="boutonbleu ico-modifier">Modifier</a>
		</div>
	</div>
    
    <?php 
	// if($estAdmin)
	    // echo "<p><a href=\"gestion/accueil_admin.php\"><strong>Vous êtes administrateur</strong> : Accès à l'administration du site</a></p>";
	?>