
			<h2>Choix de votre pseudo</h2>
			<p>Il vous faut un pseudo. Il permettra aux autres membres de vous identifier sur le forum.</p>
			<div class="formA">
				<fieldset>
				<ul>
					<li>
						<label for="pseudo">Pseudo : </label>
						<input type="text" name="pseudo" id="pseudo" value="" class="validate[required]" onkeyup="est_libre_pseudo(event);" />
					</li>
					<li>
						<div id="est_libre">&nbsp;</div>
					</li>
				</ul>
					<div class="boutons">
						<input type="button" class="boutonbleu ico-login" value="Enregistrer" onclick="renseigner_pseudo()">
					</div>
				</fieldset>
			</div>
		<style type="text/css">
			#est_libre{
				text-align: center;
			}
			#est_libre.rouge{
				color: red;
			}
			#est_libre.vert{
				color: green;
			}
		</style>
		<script type="text/javascript">
		$("#btn_creer_compte").colorbox({href:"inscription_ajax.php?forum=1"});
		
		XHR_RECHERCHE_PSEUDO = false;
		
		function est_libre_pseudo(e){
			if(e.keyCode==13){
				renseigner_pseudo();
			}
			else{
				if(XHR_RECHERCHE_PSEUDO!=false){
					XHR_RECHERCHE_PSEUDO.abort();
				}
				XHR_RECHERCHE_PSEUDO = getXhr();
				XHR_RECHERCHE_PSEUDO.onreadystatechange = function(){
					if(XHR_RECHERCHE_PSEUDO.readyState == 4){
						if(XHR_RECHERCHE_PSEUDO.status == 200){
							var reponse = eval("("+XHR_RECHERCHE_PSEUDO.responseText+")");
							XHR_RECHERCHE_PSEUDO = false;
							if(reponse){ //Pseudo libre
								element("est_libre").className = "vert";
								element("est_libre").firstChild.data = "Ce pseudo est libre";
							}
							else{
								element("est_libre").className = "rouge";
								element("est_libre").firstChild.data = "Ce pseudo est d\351j\340 utilis\351 ...";
							}
						}
					}
				};
				//XHR_RECHERCHE.open("POST", "04_ajax/rechercher_personnes.php", true);
				XHR_RECHERCHE_PSEUDO.open("POST", "03_ajax/est_libre_pseudo.php", true);
				XHR_RECHERCHE_PSEUDO.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				XHR_RECHERCHE_PSEUDO.send("p="+encodeURIComponent(element("pseudo").value));
			}
		}
		
		function est_libre_pseudo_direct(p){
			var xhr = getXhr();
				xhr.open("POST", "03_ajax/est_libre_pseudo.php", false);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send("p="+encodeURIComponent(p));
			return eval("("+xhr.responseText+")");
		}
		
		function renseigner_pseudo(){
			//On appelle "update_pseuedo.php"
			var pseudo = element("pseudo").value;
			if(est_libre_pseudo_direct(pseudo)){
				var xhr = getXhr();
					xhr.open("POST", "03_ajax/update_pseudo.php", false);
					xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xhr.send("p="+encodeURIComponent(pseudo));
				$.colorbox.close();
				//On rappelle la fonction repondre() (cette fois ci l'utilisateur est connecté)
				//repondre(<?php echo $_GET['no'].",".$_GET['com']; ?>);
				eval("<?php echo $_GET['retour']; ?>");
			}
			else
				alert("Ce pseudo est d\351j\340 utilis\351 !");
		}
		</script>
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
