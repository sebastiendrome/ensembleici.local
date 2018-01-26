<?php
//On prépare certains paramètres en fonction de $_GET["p"]
if($PAGE_COURANTE=="editorial"){
	$TITRE_PAGE = "&Eacuteditorial";
	$SOUS_TITRE_PAGE = "Retrouvez sur cette page tout l'&eacuteditorial d'ensemble ici.";
	$FORMAT_IMAGE = "4/3";
}
else if($PAGE_COURANTE=="agenda"){
	$TITRE_PAGE = "Agenda";
	$SOUS_TITRE_PAGE = "Retrouvez sur cette page tout l'agenda d'ensemble ici.";
	$FORMAT_IMAGE = "carre";
}
//On récupère la liste des items
$tab_item = extraire_liste($PAGE_COURANTE,10);
$contenu = count($tab_item);
//On les affiches
$contenu = '<h1>'.$TITRE_PAGE.'</h1>';
$contenu .= '<h2>'.$SOUS_TITRE_PAGE.'</h2>';
$contenu .= '<div class="formulaire_filtre">';
	$contenu .= 'Afficher pour <span id="libelle_distance">Nyons seulement</span>&nbsp;<input type="range" range="10" value="0" min="0" max="60" /><br />';
	$contenu .= 'À partir du <input type="text" value="" /><br />';
	$contenu .= 'Trier par <select><option>Date</option><option>Distance</option><option>Réputation</option></select><br />';
$contenu .= '</div>';
for($i_item=0;$i_item<count($tab_item);$i_item++){
	$contenu .= '<div class="liste_ligne">';
		$contenu .= '<div class="genre_ville noSmartphone">';
			$contenu .= '<span class="genre">'.$tab_item[$i_item]["genre"].'</span>';
			$contenu .= '<span class="ville">'.$tab_item[$i_item]["ville"].'</span>';
		$contenu .= '</div>';
		$contenu .= '<h3>';
			$contenu .= '<a href="http://www.ensembleici.fr/00_dev_sam/'.$NOM_VILLE.'.'.$ID_VILLE.'.'.$PAGE_COURANTE.'.'.url_rewrite($tab_item[$i_item]["titre"]).'.'.$tab_item[$i_item]["no"].'.html">';
				$contenu .= $tab_item[$i_item]["titre"];
			$contenu .= '</a>';
		$contenu .= '</h3>';
		//Editorial
		if($PAGE_COURANTE=="editorial"){
			$contenu .= '<div class="source">';
				$contenu .= '<div>le '.$tab_item[$i_item]["date_creation"].' par '.$tab_item[$i_item]["pseudo"].'</div>';
			$contenu .= '</div>';
		}
		//Agenda
		else if($PAGE_COURANTE=="agenda"){
			if($tab_item[$i_item]["date_debut"]!=$tab_item[$i_item]["date_fin"])
				$date = "du <b>".datefr($tab_item[$i_item]["date_debut"])."</b> au <b>".datefr($tab_item[$i_item]["date_fin"])."</b>";
			else
				$date = "le <b>".datefr($tab_item[$i_item]["date_debut"])."</b>";
			$contenu .= '<div class="source">';
				$contenu .= '<div>'.$date.'</div>';
			$contenu .= '</div>';
		}
		$contenu .= '<div class="genre_ville smartphone">';
			$contenu .= '<span class="genre">'.$tab_item[$i_item]["genre"].'</span>';
			$contenu .= '<span class="separateur_genre_ville"> - </span>';
			$contenu .= '<span class="ville">'.$tab_item[$i_item]["ville"].'</span>';
		$contenu .= '</div>';
		$contenu .= '<a href="http://www.ensembleici.fr/00_dev_sam/'.$NOM_VILLE.'.'.$ID_VILLE.'.'.$PAGE_COURANTE.'.'.url_rewrite($tab_item[$i_item]["titre"]).'.'.$tab_item[$i_item]["no"].'.html">';
			$contenu .= '<div class="image '.$FORMAT_IMAGE.' invisible">';
				$contenu .= '<img src="'.$tab_item[$i_item]["image"].'" />';
				$contenu .= $tab_item[$i_item]["div_fichier"];
			$contenu .= '</div>';
		$contenu .= '</a>';
		$contenu .= '<div class="archiver noSmartphone infobulle" title="Archiver">&nbsp;</div><div class="coupdecoeur noSmartphone infobulle" title="Coup de coeur">&nbsp;</div>';
		if($PAGE_COURANTE=="agenda"){
			$contenu .= '<h4>'.$tab_item[$i_item]["sous_titresub"].'</h4>';
		}
		$contenu .= '<div class="description">'.$tab_item[$i_item]["descriptionsub"].'</div>';
		$contenu .= '<div class="coupdecoeur smartphone"><div>Coup de coeur</div></div><div class="archiver smartphone"><div>Archiver</div></div>';
		$contenu .= '<div style="clear:both;"></div>';
	$contenu .= '</div>';
}
$ligne1 = array(array("class"=>"grand_moyen","id"=>"colonne_gauche","contenu"=>$contenu),array("class"=>"moyen_petit","id"=>"colonne_droite","contenu"=>"vie et tag"));
$lignes = array(array("lignes"=>$ligne1));
?>
