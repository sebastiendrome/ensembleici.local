<?php
//1. Initialisation de la session
include "01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "01_include/_init_var.php";
//5. On charge maintenant le contenu de la page.
include "01_include/_init_page.php";
//echo contenu_colonne_droite($_POST["a"]);
$NO = $_POST["no"];
$requete_message = "SELECT messageForum.*, utilisateur.pseudo AS utilisateur, utilisateur.no AS no_utilisateur FROM messageForum JOIN utilisateur ON messageForum.no_utilisateur_creation=utilisateur.no WHERE no_forum=:no AND no_message=:nom AND afficher=1 ORDER BY date_creation DESC";
$res_message = $connexion->prepare($requete_message);
$contenu .= '<div id="exprimez_vous">';
	$contenu .= '<input type="button" value="Exprimez vous ..." class="ico couleur forum" onclick="affiche_div_message(this)" />';
$contenu .= '</div>';
$contenu .= '<div class="zone_messages" id="zone_messages">';
$res_message->execute(array(":no"=>$NO,":nom"=>0));
$tab_message = $res_message->fetchAll();
for($i=0;$i<count($tab_message);$i++){
	$contenu .= '<div class="ancre_message" id="message'.$tab_message[$i]["no"].'"></div>';
	$contenu .= '<div class="un_message" id="message_'.$tab_message[$i]["no"].'_'.$tab_message[$i]["no_utilisateur"].'">';
		$contenu .= '<div class="information_message">';
			$contenu .= '<span class="pseudo">'.$tab_message[$i]["utilisateur"].'</span> le '.datefr($tab_message[$i]["date_modification"],true,false);
			if(est_connecte()){
				if($tab_message[$i]["no_utilisateur"]==$_SESSION["UserConnecte_id"]||est_admin()){ // || est_admin()
					$contenu .= '<div class="bouton_admin_proprietaire">';
						$contenu .= '<img id="edit_message_'.$tab_message[$i]["no"].'" onclick="ouvre_edit_message('.$tab_message[$i]["no"].',\'message\','.$tab_message[$i]["no_utilisateur"].')" src="img/img_colorize.php?uri=ico_edit.png&c=FFD500" onmouseover="this.src=\'img/img_colorize.php?uri=ico_edit.png&c=e19f00\';" onmouseout="this.src=\'img/img_colorize.php?uri=ico_edit.png&c=FFD500\';" />';
						$contenu .= '<img onclick="supprimer_message('.$tab_message[$i]["no"].',\'message\','.$tab_message[$i]["no_utilisateur"].')" src="img/img_colorize.php?uri=ico_delete.png&c=FEBDBD" onmouseover="this.src=\'img/img_colorize.php?uri=ico_delete.png&c=241,63,65\';" onmouseout="this.src=\'img/img_colorize.php?uri=ico_delete.png&c=FEBDBD\';" />';
					$contenu .= '</div>';
				}
			}
		$contenu .= '</div>';
		$contenu .= '<div class="contenu_message">'.$tab_message[$i]["contenu"].'</div>';
	$contenu .= '</div>';
	$contenu .= '<div class="commentaires" id="commentaire_'.$tab_message[$i]["no"].'">';
		$contenu .= '<span class="lien_commentaire" onclick="affiche_div_commentaire(this);">ajouter un commentaire</span>';
		$res_message->execute(array(":no"=>$NO,":nom"=>$tab_message[$i]["no"]));
		$tab_sous_message = $res_message->fetchAll();
		for($j=0;$j<count($tab_sous_message);$j++){
			$contenu .= '<div class="un_commentaire" id="unCommentaire_'.$tab_sous_message[$j]["no"].'_'.$tab_sous_message[$j]["no_utilisateur"].'">';
				$contenu .= '<div class="contenu_unCommentaire">';
					$contenu .= $tab_sous_message[$j]["contenu"];
				$contenu .= '</div>';
				$contenu .= '<div class="signature_commentaire"><a class="pseudo" href="profil.php?no='.$tab_sous_message[$j]["no_utilisateur"].'">'.$tab_sous_message[$j]["utilisateur"].'</a> le '.datefr($tab_message[$i]["date_modification"],true,false).'</div>';
				if(est_connecte()){
					if($tab_sous_message[$j]["no_utilisateur"]==$_SESSION["UserConnecte_id"]||est_admin()){ // || est_admin()
					$contenu .= '<div class="bouton_admin_proprietaire">';
						$contenu .= '<img id="edit_message_'.$tab_sous_message[$j]["no"].'" onclick="ouvre_edit_message('.$tab_sous_message[$j]["no"].',\'unCommentaire\','.$tab_sous_message[$j]["no_utilisateur"].')" src="img/img_colorize.php?uri=ico_edit.png&c=FFD500" onmouseover="this.src=\'img/img_colorize.php?uri=ico_edit.png&c=e19f00\';" onmouseout="this.src=\'img/img_colorize.php?uri=ico_edit.png&c=FFD500\';" />';
						$contenu .= '<img onclick="supprimer_message('.$tab_sous_message[$j]["no"].',\'unCommentaire\','.$tab_sous_message[$j]["no_utilisateur"].')" src="img/img_colorize.php?uri=ico_delete.png&c=FEBDBD" onmouseover="this.src=\'img/img_colorize.php?uri=ico_delete.png&c=241,63,65\';" onmouseout="this.src=\'img/img_colorize.php?uri=ico_delete.png&c=FEBDBD\';" />';
					$contenu .= '</div>';
					}
				}
			$contenu .= '</div>';
		}
	$contenu .=	'</div>';
}
$contenu .= '</div>';

echo '<div style="width:555px">';
echo $contenu;
echo '</div>';
?>
