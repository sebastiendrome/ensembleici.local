<?php

//variables------------------------------------------------
$id_recu = $_GET["id_recu"];
$type = $_GET["type"];
$nb_like = $_GET["nb_like"];
$urlpage = $_GET["urlpage"];
$ip_client = $_SERVER['REMOTE_ADDR'];
$erreurbdd = "Erreur de connexion à la base de données";
if (isset($_GET["page"]))
	$page = $_GET["page"];

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

require_once ('01_include/_connect.php');

//---------------Suppression des visites trop anciennes
$sql_delete="DELETE FROM `aime` WHERE TO_DAYS(NOW()) - TO_DAYS(date_like) >= 2";
$delete = $connexion->prepare($sql_delete);
$delete->execute(array(':id_delete'=>$id_delete)) or die ("Erreur ".__LINE__." : ".$sql_delete);

if(($ip_client) && ($id_recu) && ($type))
{
	//if($ip_client!="90.14.4.236"){
	if($ip_client!="77.247.181.164"&&$ip_client!="77.247.181.164"&&$ip_client!="5.133.179.171"){
		//---------------on regarde si le visiteur à déja aimé cet evenement aujourd'hui 
		$sql_testaime="SELECT id_like FROM `aime` WHERE ip_like=:ip_like AND no_occurence=:no_occurence AND type_like=:type_like";
		$res_testaime = $connexion->prepare($sql_testaime);
		$res_testaime->execute(array(':ip_like'=>$ip_client,':type_like'=>$type,':no_occurence'=>$id_recu));
		$t_testaime = $res_testaime->rowCount();
	
		//si il n'est pas autorisé a faire un like
		if ($t_testaime <= 0)
		{
			//Si il est autorisé à faire un like, on l'enregistre dans la BDD 
			$sql_user = "INSERT INTO aime(ip_like, date_like, type_like, no_occurence) VALUES(:ip_like, NOW(), :type_like, :no_occurence);";
			$ajout_user = $connexion->prepare($sql_user);
			$ajout_user->execute(array(':ip_like'=>$ip_client,':type_like'=>$type,':no_occurence'=>$id_recu)) or die ("Erreur ".__LINE__." : ".$sql_user);
		
			//on incrémente le nombre de j'aime de l'évenement
			$sql_ajout_evt = "UPDATE ".$type." SET nb_aime=nb_aime+1 WHERE no=:no_occurence";
			$ajout_evt = $connexion->prepare($sql_ajout_evt);
			$ajout_evt->execute(array(':no_occurence'=>$id_recu)) or die ("Erreur ".__LINE__." : ".$sql_ajout_evt);
		}
	}
	?>
	<div id="colorbox_like">
		<img width="500" alt="Bienvenue sur Ensemble ici !" src="img/bandeau-colorbox.png" />
		<p>Merci d'avoir mis <?php echo $type_cet; ?> dans vos coups de coeur... <br/>
		Désirez vous partager <? echo $type_cet; ?> sur facebook ?</p>
		<br clear="all" />
		<iframe class="iframe_fb" src="//www.facebook.com/plugins/like.php?href=<?php echo $urlpage; ?>&amp;send=false&amp;layout=standard&amp;width=300&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=35" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
		<br clear="all" />
		<div class="ferme-colorbox boutonbleu ico-fleche" title="Fermer cette fen&ecirc;tre">Fermer</div>
	</div>
	<?php
}
?>


