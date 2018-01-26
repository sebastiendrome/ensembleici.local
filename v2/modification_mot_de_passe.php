<?php
	session_name("EspacePerso");
	session_start();
	require ('01_include/_var_ensemble.php');
	require ('01_include/_connect.php');
	
	// include header
	$titre_page = "Votre mot de passe";
	$meta_description = "Modification de votre mot de passe d'accès à votre espace personnel. Ensemble ici : Tous acteurs de la vie locale. Accès à votre espace personnel.";

	$ajout_header = <<<AJHE
AJHE;
	include ('01_include/structure_header.php');

?>
      <div id="colonne2" class="page_inscription">                  

	<h2>Modification de votre mot de passe d'accès à votre espace personnel.</h2>

		<?php

			if ((!empty($_POST["nouveaumdp"]))&&(!empty($_POST["login"]))&&(!empty($_POST["code_reinit"])))
			{
				$loginbdd = trim($_POST["login"]);
				if (valid_email($loginbdd)){
					// Variables pour la requête
					$mdpbdd = md5($_POST['login'].$_POST['nouveaumdp'].$cle_cryptage);
					$code_reinit = htmlentities(trim($_POST["code_reinit"]));		
					$statutbdd = 1; //actif
					// mise à jour du compte
					$StrQueryUpd = "UPDATE `$table_user`
						    SET mot_de_passe=:mdpbdd
						    WHERE code_reinit_mot_de_passe=:code_reinit
						    AND email=:loginbdd
						    AND etat=:statut";
					$QueryUpd = $connexion->prepare($StrQueryUpd);
					$QueryUpd->bindParam(":mdpbdd", $mdpbdd, PDO::PARAM_STR);
					$QueryUpd->bindParam(":loginbdd", $loginbdd, PDO::PARAM_STR);
					$QueryUpd->bindParam(":code_reinit", $code_reinit, PDO::PARAM_STR);
					$QueryUpd->bindParam(":statut", $statutbdd, PDO::PARAM_INT);
					$count_exec = $QueryUpd->execute();
					// $QueryUpd->debugDumpParams();
					if($count_exec){
						echo '<br/><p id="erreur">Votre mot de passe a été modifié avec succès. </p>';
						echo '<p><a href="inscription.php?etape=etape1" title="Connexion à votre espace personnel">Cliquez ici pour vous connecter à votre espace personnel.</a></p>';
					}else{
					
					}
				}
			}
			else
			{
			
				if (($_GET["login"])&&(!empty($_GET["login"]))&&($_GET["key"])&&(!empty($_GET["key"])))
				{
					$loginbdd = trim($_GET["login"]);
					if (valid_email($loginbdd)){
						// on fait la requête pour savoir si le couple key/login existe
						$code_reinit = htmlentities(trim($_GET["key"]));		
						$statutbdd = 1; //actif
						$strQuery = "SELECT * FROM `$table_user` WHERE code_reinit_mot_de_passe=:code_reinit AND email=:loginbdd AND etat=:statut LIMIT 0, 1";
						
						$query = $connexion->prepare($strQuery);
						$query->bindParam(":code_reinit", $code_reinit, PDO::PARAM_STR);
						$query->bindParam(":loginbdd", $loginbdd, PDO::PARAM_STR);
						$query->bindParam(":statut", $statutbdd, PDO::PARAM_INT);
						$query->execute();
						$count_cores = $query->rowCount();
						$query->closeCursor();
						$query = NULL;
						if($count_cores === 1){
							$afficher_form = true;
						}		
					}
				}
				if ($afficher_form) {
				?>
				<form id="EDmodifmdp" name="EDmodifmdp" action="" method="post" accept-charset="UTF-8" class="formA">
					<fieldset>
					<ul>
						<li>
							<label for="login">Votre Email (identifiant) : </label>
							<input type="text" name="login" id="login" readonly="readonly" value="<?php echo $loginbdd; ?>" class="validate[required] verouille" />						
						</li>
						<li>
							<label for="nouveaumdp">Nouveau mot de passe :</label>
							<input type="password" name="nouveaumdp" id="nouveaumdp" value="" class="validate[required]" />
						</li>
							<input type="hidden" name="code_reinit" id="code_reinit" value="<?php echo $code_reinit; ?>" />
	
						<div class="boutons">
							<input type="submit" class="boutonbleu ico-login" value="Modifier le mot de passe">
						</div>
					</fieldset>
				</form>
				<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
				<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
				<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
				<script type="text/javascript">
					$(document).ready(function() {
					    // Validation form
					    $("#EDmodifmdp").validationEngine("attach",{promptPosition : "topRight", scroll: false});
					});
				</script>
				
				
				<?php
				} else{
					?>
					<br/><div id="erreur">Ce compte n'existe pas ou l'adresse est erronée. </div>
					
					<?php
				}
			}
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