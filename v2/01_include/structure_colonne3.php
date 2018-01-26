      <div id="colonne3">
        <aside>
<?php
include_once ('_connect.php');

//Ajouté par sam pour les forums
if($messageForum){
	echo '<div id="div_notifications_forum" onclick="activer_notifications(!NOTIFICATIONS_ACTIVES);">';
		if(est_connecte()&&$est_abonne)
			echo '<img src="02_medias/01_interface/img_colorize.php?uri=ico_bell.png&c=15AA9E" />';
		else
			echo '<img src="02_medias/01_interface/ico_bellcroix.png" />';
		echo "Recevoir un mail lorsqu'un message est ajouté dans ce forum.";
	echo '</div>';
}



// Affichage des tags + description sur pages vie
if ($id_vie || $id_tag)
{
          // Affichage des descriptions des vies
          include ('affiche_description_vie.php');
        
          if (!$aff_specifique)
          // Affichage des tags
          include ('affiche_tags.php');
}


if ($affiche_articles)
{
          // Affichage des articles
          
          $idbloc = 1;
          $sql_bloc="SELECT titre, contenu
                      FROM `contenu_blocs`
                      WHERE etat = :etat
                      AND no = :idbloc";
          $res_bloc = $connexion->prepare($sql_bloc);
          $res_bloc->execute(array(':idbloc'=>$idbloc, ':etat'=>1));
          $tab_bloc = $res_bloc->fetch(PDO::FETCH_ASSOC);
          if ((!empty($tab_bloc["titre"]))||(!empty($tab_bloc["contenu"])))
          {
            echo "<div id=\"articles\" class=\"blocA\">";
            echo "<h1>Editorial</h1>";
            if (!empty($tab_bloc["titre"])) echo "<h2>".$tab_bloc["titre"]."</h2>";
              if (!empty($tab_bloc["contenu"])) echo "<div class=\"contenu\">".nl2br($tab_bloc["contenu"])."</div>";
            echo "</div>";
          }
}
if ($affiche_publicites){
        // Affichage des publicités sur pages vie
        if (!empty($id_vie))
        {
          $sql_pub="SELECT * FROM `publicites`
                    WHERE etat = :etat
                    AND id_vie = :id_vie
                    AND validite_du < CURDATE()
                    AND validite_au > CURDATE()
                    ORDER BY rand()
                    LIMIT 0,1";
          $res_pub = $connexion->prepare($sql_pub);
          $res_pub->execute(array(':etat'=>1,':id_vie'=>$id_vie));
          while($tab_pub = $res_pub->fetch(PDO::FETCH_ASSOC))
          {
            if ((!empty($tab_pub["titre"]))||(!empty($tab_pub["contenu"])))
            {
              echo "<div id=\"publicite1\" class=\"blocA\">";
              if (!empty($tab_pub["titre"])) echo "<h2>".$tab_pub["titre"]."</h2>";
              
              if (!empty($tab_pub["site"])) echo "<a href=\"".$tab_pub["site"]."\" target=\"_blank\" title=\"".$tab_pub["titre"]."\">";
              if (!empty($tab_pub["contenu"])) echo "<div class=\"contenu\">".nl2br($tab_pub["contenu"])."</div>";
              if (!empty($tab_pub["url_image"])) echo "<div class=\"contenuimg\"><img src=\"".$tab_pub["url_image"]."\" alt=\"".$tab_pub["titre"]."\"  width=\"266\" /></div>";              
              if (!empty($tab_pub["site"])) echo "</a>";
              
              echo "</div>";
            }
          }
        }
}

if ($page_index||$messageForum){

?>
          <div class="blocA">
				<?php
					require_once("fonctions_rss.php");
					echo "<div id=\"bloc_rss\">";
					echo "<h2>Actualité du CEDER</h2>";
					echo RSS_Display("http://www.ceder-provence.fr/spip.php?page=backend", 3);
					echo "</div>";
                ?>
          		<p class="signature"><a href="http://www.ensembleici.fr/structure.nyons.association-ceder.9568.246.html">Le Ceder</a></p>
          </div>

          <div class="blocA">
            <p>Le site « Ensemble ici » est un projet associatif, évolutif et collaboratif. Si vous rencontrez des problèmes techniques sur le site ou souhaitez nous soumettre vos idées pour faciliter l’utilisation des services, <a href="contact.html" title="Envoyer vos commentaires ou corrections"> cliquez ici pour nous envoyer vos commentaires ou corrections.</a></p>
            <p>Nous traiterons votre message dans les meilleurs délais.</p>
            <p class="signature">Le collectif « Ensemble ici » </p>
          </div>
<?php
}
// Covoiturage
/*
          <div class="blocA">
            <script type="text/javascript" src="http://preprod.ecolutis.com/cg26/trunk/ecoluSearch.js?title=Recherche+de+trajets+de+covoiturage" ></script>
          </div>
*/

?>
<?php

?>

        </aside>
<?php /*	<div id="ici-bientot">
	      <img src="img/fleche-ici-bientot.png" alt="Ici, bientôt..." width="134" height="86" />
	</div> */ ?>
        
      </div>

