<?php
//1. On récupère le forum citoyen pour la ville en cours.
$requete_citoyen = "SELECT * FROM forum WHERE no_forum_type=1 AND no_ville=:noville";
if($_GET["dev"]==1)
	$WHERE_DEV = "";
else
	$WHERE_DEV = " forum.no>0 AND";
	
//2. On récupère la liste des forums "généraux".
$requete_forums = "SELECT forum.*, utilisateur.pseudo AS utilisateur, IFNULL(MAX(messageForum.date_modification),forum.date_modification) as d FROM forum JOIN utilisateur ON forum.no_utilisateur_creation=utilisateur.no LEFT JOIN messageForum ON forum.no = messageForum.no_forum WHERE".$WHERE_DEV." no_forum_type=2 GROUP BY forum.no ORDER BY d DESC, forum.date_modification DESC";
$res_forums = $connexion->prepare($requete_forums);
$res_forums->execute();
$tab_forums = $res_forums->fetchAll();

	$requete_derniere_modif = "SELECT date_modification AS d FROM messageForum WHERE no_forum=:no ORDER BY date_modification DESC LIMIT 1";
	$res_derniere_modif = $connexion->prepare($requete_derniere_modif);
?>
<div id="forumlocal" class="blocB">
	<h1>Forums</h1>
	<?php
	if(isset($id_ville)&&$id_ville>0){
		$requete_forumCitoyen = "SELECT forum.*, IFNULL(MAX(messageForum.date_modification),forum.date_modification) as d FROM forum LEFT JOIN messageForum ON forum.no = messageForum.no_forum WHERE no_forum_type=1 AND no_ville=:no GROUP BY forum.no ORDER BY d DESC, forum.date_modification DESC";
		$res_forumCitoyen = $connexion->prepare($requete_forumCitoyen);
		$res_forumCitoyen->execute(array(":no"=>$id_ville));
		$tab_forumCitoyen = $res_forumCitoyen->fetchAll();
	?>
	<article>
		<a href="<?php echo $root_site."forum.".url_rewrite($titre_ville).".".url_rewrite("forum citoyen").".".$id_ville.".".$tab_forumCitoyen[0]["no"].".html"; ?>" class="case_lien_citoyen">
			<div class="un-forum" style="padding:10px;padding-bottom:0px;position: relative;background-color:rgb(240,237,234);border-radius: 5px;border: 1px solid rgb(227, 214, 199);">
				<h2>Forum citoyen de <?php echo ucfirst($nom_ville); ?> !</h2>
				<br clear="right" />
				<!--<div class="illustr">
					<img src="<?php echo $root_site; ?>miniature.php?uri=<?php echo $tab_forumCitoyen[0]['url_image']; ?>&amp;method=fit&amp;w=80" width="80" />
				</div>-->
				<?php
				if($tab_forumCitoyen[0]["d"]!=$tab_forumCitoyen[0]["date_modification"]){
				?>
				<p><strong>Dernière réponse le <?php echo datefr($tab_forumCitoyen[0]["d"]); ?></strong></p>
				<?php
				}
				?>
				<p>Vie locale, informations à partager, actions collectives, idées...<br />
				Un espace neutre et indépendant pour échanger en liberté !<br /><br />
				<?php /*
				Cet espace de libre expression est ouvert à tous, dans le respect de quelques règles élémentaires:<br /><br />
				<ol><li>Tout propos puni par la loi, discriminatoire ou diffamatoire sera supprimé.</li><li>Les publicités commerciales sont interdites.</li><li>La courtoisie est de rigueur : toute critique doit se faire dans le respect de la personne.</li><li>Tout acte de modération sera justifié par un message privé envoyé à l’auteur.</li></ol>
				*/ ?>
				</p>
			</div>
		</a>
	</article>
	<?php
	}
	for($i=0;$i<count($tab_forums);$i++){		
		$tab_forums[$i]["contenu"] = strip_tags($tab_forums[$i]["contenu"]);
	?>
		<article>
			<a href="<?php echo $root_site."forum.".url_rewrite($titre_ville).".".url_rewrite($tab_forums[$i]["titre"]).".".$id_ville.".".$tab_forums[$i]["no"].".html"; ?>" class="case_lien">
				<div class="un-forum">
					<div class="genre_ville">
						Par <b><?php echo $tab_forums[$i]["utilisateur"]; ?></b> le <?php echo datefr($tab_forums[$i]["date_creation"]); ?>
					</div>
					<h2><?php echo $tab_forums[$i]["titre"]; ?></h2>
					<br clear="right" />
					<div class="illustr">
						<img src="<?php echo $root_site; ?>miniature.php?uri=<?php echo $tab_forums[$i]['url_image']; ?>&amp;method=fit&amp;w=80" width="80" />
					</div>
					<?php
					if($tab_forums[$i]["d"]!=$tab_forums[$i]["date_modification"]){
					?>
					<p><strong>Dernière réponse le <?php echo datefr($tab_forums[$i]["d"]); ?></strong></p>
					<?php
					}
					?>
					<p><?php echo (strlen($tab_forums[$i]["contenu"])>250)?(substr($tab_forums[$i]["contenu"],0,250)."[...]"):($tab_forums[$i]["contenu"]); ?></p>
				</div>
			</a>
		</article>
	<?php
	}
	?>
</div>
<?php

?>
