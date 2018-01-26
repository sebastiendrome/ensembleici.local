<?php
// Pour les favoris ?
if(isset($_GET['url']) && !($_GET['url']==""))
{
	$url =  $_GET['url'];
	$lien_url = "?url=$url";
}
else
{
	$lien_url = "";
}
if(isset($_GET['forum']) && ($_GET['forum']==1)){
	$forum = true;
}
else
	$forum = false;
		if(!$forum){
?>
		<form id="EDconnexion" name="EDconnexion" action="01_include/connexion_espacePerso.php<?php echo $lien_url ?>" method="post" accept-charset="UTF-8" class="formA">
			<h2>Connexion</h2>
			<fieldset>
			<ul>
				<li>
					<label for="login">Votre login <span>(Email)</span> : </label>
					<input type="text" name="login" id="login" value="" class="validate[required,custom[email]]" />
				</li>
				<li>
					<label for="mdp">Votre mot de passe :</label>
					<input type="password" name="mdp" id="mdp" value="" class="validate[required]" />
				</li>
				<div class="boutons">
					<input type="submit" class="boutonbleu ico-login" value="Connexion">
				</div>
			</fieldset>
		</form>
		<?php
		}
		else{
		?>
			<div class="formA">
				<h2>Connexion</h2>
				<fieldset>
				<ul>
					<li>
						<label for="login">Votre login <span>(Email)</span> : </label>
						<input type="text" name="login" id="login" value="" class="validate[required,custom[email]]" onkeyup="onkeyup_connexion(event)" />
					</li>
					<li>
						<label for="mdp">Votre mot de passe :</label>
						<input type="password" name="mdp" id="mdp" value="" class="validate[required]" onkeyup="onkeyup_connexion(event)" />
					</li>
					<div class="boutons">
						<input type="button" class="boutonbleu ico-login" value="Connexion" onclick="connexion_ajax()">
					</div>
				</fieldset>
			</div>
		<div style="text-align:center;">
			<p>Vous n'avez pas encore d'espace personnel sur "<span class="ei">Ensemble Ici</span>" ?</p>
			<input type="button" class="boutonbleu ico-login" value="Cr&eacute;ez un compte" id="btn_creer_compte">
			<p class="note">(cr&eacute;ation en moins de 2 minutes)</p>
		</div>
		<script type="text/javascript">
		$("#btn_creer_compte").colorbox({href:"inscription_ajax.php?forum=1&retour=<?php echo $_GET["retour"]; ?>"});
		
		function onkeyup_connexion(e){
			if(e.keyCode==13)
				connexion_ajax();
		}
		
		function connexion_ajax(){
			var login = element("login").value;
			var mdp = element("mdp").value;
			var xhr = getXhr();
				xhr.open("POST", "01_include/connexion_espacePerso.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send("login="+encodeURIComponent(login)+"&mdp="+encodeURIComponent(mdp)+"&ajax=1");
			var reponse = eval("("+xhr.responseText+")");
			if(reponse){
				//On vérifie alors que l'utilisateur connecté à un pseudo.
				var pseudo = recuperer_pseudo();
				if(!pseudo){
					//On ouvre alors la colorbox de création de pseudo
					$.colorbox({href:"choixpseudo_ajax.php?forum=1&retour=<?php echo $_GET['retour']; ?>"});
				}
				else{
					poster_message = true;
					$.colorbox.close();
				}
				//On rappelle la fonction repondre() (cette fois ci l'utilisateur est connecté)
				//repondre(<?php echo $_GET['no'].",".$_GET['com']; ?>);
				eval("<?php echo $_GET['retour']; ?>");
			}
			else
				alert("erreur d'identification");
		}
		</script>
		<?php
		}
		?>
		<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#login").focus();
			    // Validation form
			    $("#EDconnexion").validationEngine("attach",{promptPosition : "topRight", scroll: false});
			});
		</script>
