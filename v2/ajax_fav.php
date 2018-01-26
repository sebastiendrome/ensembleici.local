<?php
session_name("EspacePerso");
session_start();
//variables------------------------------------------------
$action = $_GET["action"]; // id objet
$id_recu = $_GET["id_recu"]; // id objet
$type = $_GET["type"]; // type objet
$urlpage = $_GET["urlpage"]; // url page dédiée
$id_utilisateur = $_SESSION['UserConnecte_id']; // Id utilisateur
$erreurbdd = "Erreur de connexion à la base de données"; // message d'erreur
if (isset($_GET["page"])) 
	$page = $_GET["page"]; //si on est sur la page auto_previsu.php

// attention, ajouter aussi un case au javascript de index.php + ajout_like.php
switch ($type) {
	case "structure":
	$type_cet = "cette structure";
	break;
	case "evenement":
	$type_cet = "cet évènement";
	break;
	case "petiteannonce":
	$type_cet = "cette petite annonce";
	break;
}

$page_inscription = "etape1";

require_once ('01_include/_connect.php');

if(($id_utilisateur) && ($id_recu) && ($type)){ //si le client est identifié
	//---------------on regarde si le visiteur à déja archivé cet objet aujourd'hui 
	$sql_testfav="SELECT id_fav FROM `favori` WHERE no_utilisateur='".$id_utilisateur."' AND no_occurence='".$id_recu."' AND type_fav='".$type."'";
	$res_testfav = $connexion->prepare($sql_testfav);
	$res_testfav->execute();
	$t_testfav = $res_testfav->rowCount();
	if ($t_testfav <= 0 && $action!="supprime")
	{
		//Si il est autorisé l'archiver, on l'enregistre dans la BDD 
		$sql_userfav = "INSERT INTO favori(no_utilisateur, no_occurence, type_fav) VALUES(:no_utilisateur, :no_occurence, :type_fav);";
		$ajout_userfav = $connexion->prepare($sql_userfav);
		$ajout_userfav->execute(array(':no_utilisateur'=>$id_utilisateur,':no_occurence'=>$id_recu,':type_fav'=>$type)) or die ("Erreur ".__LINE__." : ".$sql_user);
		echo "<div id=\"colorbox_like\">";
		echo "<img width=\"500\" alt=\"Bienvenue sur Ensemble ici !\" src=\"img/bandeau-colorbox.png\"><p>Vous venez d'archiver $type_cet, et pouvez désormais le consulter à partir de votre  <a href=\"espace_personnel.html\" title=\"Votre espace personnel\">espace personnel</a>.</p><br clear=\"all\" /><div class=\"ferme-colorbox boutonbleu ico-fleche\" title=\"Fermer cette fen&ecirc;tre\">Fermer</div>";
		echo "</div>";
	}
	elseif($t_testfav > 0 && $action == "supprime")
	{
        echo "<div id=\"colorbox_like\">";
		echo "<img width=\"500\" alt=\"Bienvenue sur Ensemble ici !\" src=\"img/bandeau-colorbox.png\"><p>Voulez vos vraiment supprimer $type_cet de vos archives ?</p><br clear=\"all\" /><div class=\"oui-colorbox boutonbleu ico-fleche\" title=\"Fermer cette fen&ecirc;tre\" rel=\"$id_recu\" id=\"$type\" name=\"$id_utilisateur\">Oui</div><div class=\"ferme-colorbox non-colorbox boutonbleu ico-fleche\" title=\"Fermer cette fen&ecirc;tre\" rel=\"$id_recu\" id=\"$type\" name=\"$id_utilisateur\">Non</div>";
		echo "</div>";
	}
}elseif(!@$_SESSION['UserConnecte']){
	echo "<div id=\"colorbox_like\">";
	/*
	echo "<img width=\"500\" alt=\"Bienvenue sur Ensemble ici !\" src=\"img/bandeau-colorbox.png\"><p>Vous devez <a href=\"identification.html\" title=\"Connectez vous à votre espace personnel\">vous connecter</a> sur votre compte personnel pour archiver $type_cet.</p><br clear=\"all\" /><div class=\"ferme-colorbox boutonbleu ico-fleche\" title=\"Fermer cette fen&ecirc;tre\">Fermer</div>";
	*/

		include("gestion_page_inscription.php");
		echo "<img width=\"500\" alt=\"Bienvenue sur Ensemble ici !\" src=\"img/bandeau-colorbox.png\"><p>Vous devez <a href=\"identification.html\" title=\"Connectez vous à votre espace personnel\">vous connecter</a> sur votre compte personnel pour archiver $type_cet.</p>";

		include($mainPage);
		"<br clear=\"all\" /><div class=\"ferme-colorbox boutonbleu ico-fleche\" title=\"Fermer cette fen&ecirc;tre\">Fermer</div>";
	echo "</div>";
}
?>
		<script>
		$(".oui-colorbox").click(function() {
				var id_recu = $(this).attr('rel');
				var type = $(this).attr("id");
				var id_utilisateur = $(this).attr("name");

				switch (type) {
					case "structure":
					var type_sup = "Cette structure vient d'être<br />supprimée de vos archives";
					var type_fav = "Archiver cette structure";
					break;
					case "evenement":
					var type_sup = "Cet évènement vient d'être<br />supprimé de vos archives";
					var type_fav = "Archiver cet évènement";
					break;
					case "petiteannonce":
					var type_sup = "Cette petite annonce vient d'être<br />supprimée de vos archives";
					var type_fav = "Archiver cette petite annonce";
					break;
				}

				$.ajax({ 
					type: "POST", 
					url: "01_include/supprime_fav.php", 
					data: {id_recu: id_recu, type: type, id_utilisateur: id_utilisateur},
					success: function(retour){
					    if (retour=="ok") 
						{
							$("a[rel='"+id_recu+"'][class*='ico-fav'][id='"+type+"']").poshytip({
								content: type_sup,
								showOn: 'none',
								showTimeout:0,
								hideTimeout:4000,
								timeOnScreen:4000,
								className: 'infobulle-tip',
								alignTo: 'target',
								alignX: 'inner-left',
								alignY: 'bottom',
								offsetX: 1,
								offsetY: 7
							}).poshytip('show').removeClass("desactive").removeClass("supprime");
							$("a[rel='"+id_recu+"'][class*='ico-fav'][id='"+type+"']")[0].title = type_fav;
					    }
					}
				});
				$.colorbox.close();
			});

			$(".non-colorbox").click(function() {
				var id_recu = $(this).attr('rel');
				var type = $(this).attr("id");
				var id_utilisateur = $(this).attr("name");

				switch (type) {
					case "structure":
					var type_fav = "Supprimer cette structure de vos archives";
					break;
					case "evenement":
					var type_fav = "Supprimer cet évènement de vos archives";
					break;
					case "petiteannonce":
					var type_fav = "Supprimer cette petite annonce de vos archives";
					break;
				}

				$("a[rel='"+id_recu+"'][class*='ico-fav'][id='"+type+"']").addClass('desactive').addClass('supprime');
				$("a[rel='"+id_recu+"'][class*='ico-fav'][id='"+type+"']")[0].title = type_fav;
				$("a[rel='"+id_recu+"'][class*='ico-fav'][id='"+type+"']").poshytip({
								content: type_fav,
								showOn: 'none',
								className: 'infobulle-tip',
								alignTo: 'target',
								alignX: 'inner-left',
								alignY: 'bottom',
								offsetX: 1,
								offsetY: 7
							})
			});

		</script>