<?php
$no_forum = $_GET["no"];
$requete_forum = "SELECT * FROM forum WHERE no=:no";
$res_forum = $connexion->prepare($requete_forum);
$res_forum->execute(array(":no"=>$no_forum));
$tab_forum = $res_forum->fetchAll();

$requete_message = "SELECT messageForum.*, utilisateur.pseudo AS utilisateur, utilisateur.no AS no_utilisateur FROM messageForum JOIN utilisateur ON messageForum.no_utilisateur_creation=utilisateur.no WHERE no_forum=:no AND no_message=:nom AND afficher=1 ORDER BY date_creation DESC";
$res_message = $connexion->prepare($requete_message);
?>
<h1 style="color:rgb(21,170,158);"><?php echo $tab_forum[0]["titre"]; ?></h1>
<div class="zone_question">
	<div class="contenu_question">
		<div class="illustr">
			<img src="<?php echo $root_site; ?>miniature.php?uri=<?php echo $tab_forum[0]['url_image']; ?>&amp;method=fit&amp;w=120" width="120" />
		</div>
		<?php echo $tab_forum[0]["contenu"]; ?>
	</div>
</div>
<div class="zone_reponse" id="zone_reponse">
<div class="repondre_question" style="text-align:right;">
	<input id="input_repondre" type="button" value="Exprimez-vous ..." class="boutonbleu ico-fleche" style="display:inline-block;" onclick="ouvrir_repondre(this,element('reponse_forum'));" />
	<textarea style="display:none;" id="reponse_forum" class="reponse_forum vide" title="R&eacute;pondre" onfocus="" onblur=""></textarea>
	<div id="btn_activer_notifications"><input type="checkbox" id="input_forum_notif" <?php echo ($cocher_abonnement)?'checked="checked"':''; ?>/><label for="input_forum_notif">&nbsp;Recevoir un mail lorsqu'un message est ajouté dans ce forum</label></div>
	<div id="btn_annuler"><input type="button" value="Annuler" class="boutonbleu ico-fleche" onclick="fermer_repondre()" /></div>
	<div id="btn_reponse"><input type="button" value="R&eacute;pondre" class="boutonbleu ico-fleche" onclick="repondre()" /></div>
</div>
</div>
<div class="zone_messages" id="zone_messages">
<?php
$res_message->execute(array(":no"=>$no_forum,":nom"=>0));
$tab_message = $res_message->fetchAll();
for($i=0;$i<count($tab_message);$i++){
	?>
	<div class="ancre_message" id="message<?php echo $tab_message[$i]["no"]; ?>"></div>
	<div class="un_message" id="message_<?php echo $tab_message[$i]["no"]; ?>_<?php echo $tab_message[$i]["no_utilisateur"]; ?>">
		<div class="information_message">
			<!--<a class="pseudo" href="profil.php?no=<?php echo $tab_message[$i]["no_utilisateur"]; ?>"><?php echo $tab_message[$i]["utilisateur"]; ?></a> le <?php echo datefr($tab_message[$i]["date_modification"],true,false); ?>-->
			<span class="pseudo"><?php echo $tab_message[$i]["utilisateur"]; ?></span> le <?php echo datefr($tab_message[$i]["date_creation"],true,false); ?>
			<?php
			if(est_connecte()){
				if($tab_message[$i]["no_utilisateur"]==$_SESSION["UserConnecte_id"]||est_admin()){ // || est_admin()
					?>
					<div class="bouton_admin_proprietaire">
						<img id="edit_message_<?php echo $tab_message[$i]["no"]; ?>" onclick="ouvre_edit_message(<?php echo $tab_message[$i]["no"]; ?>,'message',<?php echo $tab_message[$i]["no_utilisateur"]; ?>)" src="02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=FFD500" onmouseover="this.src='02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=e19f00';" onmouseout="this.src='02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=FFD500';" />
						<img onclick="supprimer_message(<?php echo $tab_message[$i]["no"]; ?>,'message',<?php echo $tab_message[$i]["no_utilisateur"]; ?>)" src="02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=FEBDBD" onmouseover="this.src='02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=241,63,65';" onmouseout="this.src='02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=FEBDBD';" />
					</div>
					<?php
				}
			}
			?>
		</div>
		<div class="contenu_message"><?php echo $tab_message[$i]["contenu"]; ?></div>
	</div>
	<div class="commentaires" id="commentaire_<?php echo $tab_message[$i]["no"]; ?>">
		<span class="lien_commentaire" onclick="affiche_div_commentaire(this);">ajouter un commentaire</span>
		<?php
			$res_message->execute(array(":no"=>$no_forum,":nom"=>$tab_message[$i]["no"]));
			$tab_sous_message = $res_message->fetchAll();
			for($j=0;$j<count($tab_sous_message);$j++){
			?>
				<div class="un_commentaire" id="unCommentaire_<?php echo $tab_sous_message[$j]["no"]; ?>_<?php echo $tab_sous_message[$j]["no_utilisateur"]; ?>">
					<div class="contenu_unCommentaire">
						<?php echo $tab_sous_message[$j]["contenu"]; ?>
					</div>
					<div class="signature_commentaire"><a class="pseudo" href="profil.php?no=<?php echo $tab_sous_message[$j]["no_utilisateur"]; ?>"><?php echo $tab_sous_message[$j]["utilisateur"]; ?></a> le <?php echo datefr($tab_message[$i]["date_creation"],true,false); ?></div>
					<?php
						if(est_connecte()){
							if($tab_sous_message[$j]["no_utilisateur"]==$_SESSION["UserConnecte_id"]||est_admin()){ // || est_admin()
								?>
							<div class="bouton_admin_proprietaire">
								<img id="edit_message_<?php echo $tab_sous_message[$j]["no"]; ?>" onclick="ouvre_edit_message(<?php echo $tab_sous_message[$j]["no"]; ?>,'unCommentaire',<?php echo $tab_sous_message[$j]["no_utilisateur"]; ?>)" src="02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=FFD500" onmouseover="this.src='02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=e19f00';" onmouseout="this.src='02_medias/01_interface/img_colorize.php?uri=ico_edit.png&c=FFD500';" />
								<img onclick="supprimer_message(<?php echo $tab_sous_message[$j]["no"]; ?>,'unCommentaire',<?php echo $tab_sous_message[$j]["no_utilisateur"]; ?>)" src="02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=FEBDBD" onmouseover="this.src='02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=241,63,65';" onmouseout="this.src='02_medias/01_interface/img_colorize.php?uri=ico_delete.png&c=FEBDBD';" />
							</div>
							<?php
							}
						}
					?>
				</div>
			<?php
			}
		?>
	</div>
	<?php
}
?>
</div>
