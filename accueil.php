<?php
/************************************************
On récupère les informations dont on a besoin.
************************************************/
// new line from seb
/***
	1. Editorial
	**/
$territoire = 1;
if (isset($_SESSION["utilisateur"]["territoire"])) {
    $territoire = $_SESSION["utilisateur"]["territoire"];
}
$infos_edito = extraire_liste("editorial",3,1,array("distance"=>-1, 'territoire' => $territoire));
$liste_edito = $infos_edito["liste"];
$CONTENU_EDITORIAL = '<div id="home_editorial_bloc" class="editorial_1">'; //<div id="home_editorial_bloc_previous" onclick="diaporama_editorial_previous()"></div><div id="home_editorial_bloc_next" onclick="diaporama_editorial_next()"></div>
for($i=0;$i<count($liste_edito);$i++){
	//$CONTENU_EDITORIAL .= '<a href="'.$root_site.'.editorial.'.$NOM_VILLE_URL.'.'.url_rewrite($liste_edito[$i]["titre"]).'.'.$ID_VILLE.'.'.$liste_edito[$i]["no"].'.html">';
		$CONTENU_EDITORIAL .= '<div class="image 4/3 invisible">';
			$CONTENU_EDITORIAL .= '<img src="'.$liste_edito[$i]["image"].'" />';
			$CONTENU_EDITORIAL .= '<div class="home_editorial_bloc_resume">';
				$CONTENU_EDITORIAL .= '<h3 class="bleu"><a href="'.$root_site.'editorial.'.$NOM_VILLE_URL.'.'.url_rewrite($liste_edito[$i]["titre"]).'.'.$ID_VILLE.'.'.$liste_edito[$i]["no"].'.html">'.$liste_edito[$i]["titre"].'</a></h3>';
				if(!empty($liste_edito[$i]["sous_titre"]))
					$CONTENU_EDITORIAL .= '<h4><a style="text-decoration:none;color: rgb(68, 81, 88); font-weight:500px;" href="'.$root_site.'editorial.'.$NOM_VILLE_URL.'.'.url_rewrite($liste_edito[$i]["titre"]).'.'.$ID_VILLE.'.'.$liste_edito[$i]["no"].'.html">'.$liste_edito[$i]["sous_titre"].'</a></h4>';
				$CONTENU_EDITORIAL .= '<div><a style="text-decoration:none;color: rgb(68, 81, 88);" href="'.$root_site.'editorial.'.$NOM_VILLE_URL.'.'.url_rewrite($liste_edito[$i]["titre"]).'.'.$ID_VILLE.'.'.$liste_edito[$i]["no"].'.html">'.$liste_edito[$i]["descriptionsub"].'</a></div>';
			$CONTENU_EDITORIAL .= '</div>';
			$CONTENU_EDITORIAL .= '<div class="source">';
				$CONTENU_EDITORIAL .= '<div>le '.$liste_edito[$i]["date_creation"].' par '.$liste_edito[$i]["pseudo"].'</div>';
			$CONTENU_EDITORIAL .= '</div>';
			$CONTENU_EDITORIAL .= $liste_edito[$i]["div_fichier"];
		$CONTENU_EDITORIAL .= '</div>';
	//$CONTENU_EDITORIAL .= '</a>';
}
$CONTENU_EDITORIAL .= '</div>';
$CONTENU_EDITORIAL .= '<div id="home_editorial_boules">';
	$CONTENU_EDITORIAL .= '<span class="actif" name="slide_edito" data-ref="1" onclick="select_editorial(1);"></span>';
	$CONTENU_EDITORIAL .= '<span name="slide_edito" data-ref="2" onclick="select_editorial(2);"></span>';
	$CONTENU_EDITORIAL .= '<span name="slide_edito" data-ref="3" onclick="select_editorial(3);"></span>';
$CONTENU_EDITORIAL .= '</div>';
/***
	2. Editorial ensemble-ici, publicité, etc.
	**/
//On récupère le titre et le contenu du bloc "edito"
$requete = "SELECT titre, contenu FROM contenu_blocs WHERE ref=1 AND etat=1 AND territoires_id = ".$territoire;
$tab_editoEi = execute_requete($requete);
if(count($tab_editoEi)>0){
	$CONTENU_EDITORIAL_ENSEMBLEICI = '<h3>'.$tab_editoEi[0]["titre"].'</h3>'.$tab_editoEi[0]["contenu"];
}
else{
	$CONTENU_EDITORIAL_ENSEMBLEICI = "";
}
//$CONTENU_EDITORIAL_ENSEMBLEICI = '<h3>Ensembleici fait peau neuve&nbsp;!</h3><p>Ensembleici vous accueille aujourd\'hui sur sa nouvelle version.</p><p>Un nouveau menu vous permet de vous deplacer auisément entre les différentes parties du site.</p><p>Un éditorial est désormais en ligne, vivez l\'actualité autour de chez vous!</p><p>Le site est maintenant compatible tablettes et mobiles !</p>';
/***
	3. Agenda
	**/
	//L'agenda près de chez vous
$infos_agenda = extraire_liste("agenda",4,1,array("du"=>date("Y-m-d"),"courte_duree_seulement"=>true,"illustree_seulement"=>true,"ville_seulement"=>true,"elargir"=>true, "distance" => 30));
$liste_agenda = $infos_agenda["liste"];
$agenda = array();
for($i=0;$i<count($liste_agenda);$i++){
	//$html_bloc_agenda = '<a href="'.$root_site.url_rewrite($NOM_VILLE).'.'.$ID_VILLE.'.agenda.'.url_rewrite($liste_agenda[$i]["titre"]).'.'.$liste_agenda[$i]["no"].'.html">';
	$html_bloc_agenda = '<a href="'.$root_site.'agenda.'.url_rewrite($NOM_VILLE).'.'.url_rewrite($liste_agenda[$i]["titre"]).'.'.$ID_VILLE.'.'.$liste_agenda[$i]["no"].'.html">';
		$html_bloc_agenda .= '<div class="image 4/3 invisible" style="width:100%">';
			$html_bloc_agenda .= '<img src="'.$liste_agenda[$i]["image"].'" />';
			$html_bloc_agenda .= '<div class="home_agenda_bloc_date">';
				$html_bloc_agenda .= $liste_agenda[$i]["datehome"];
			$html_bloc_agenda .= '</div>';
			$html_bloc_agenda .= '<div class="home_agenda_bloc_description">';
				$html_bloc_agenda .= '<div>';
					$html_bloc_agenda .= $liste_agenda[$i]["date_du_au"]." : ".$liste_agenda[$i]["titresub"]." - ".$liste_agenda[$i]["genre"];
				$html_bloc_agenda .= '</div>';
			$html_bloc_agenda .= '</div>';
		$html_bloc_agenda .= '</div>';
	$html_bloc_agenda .= '</a>';
	$agenda[] = $html_bloc_agenda;
	// Ancienne ligne: $agenda[] = "<div class='image 4/3 invisible' style='width:100%'><img src='".$liste_agenda[$i]["image"]."' /><div class='home_agenda_bloc_date'>".$liste_agenda[$i]["datehome"]."</div><div class='home_agenda_bloc_description'><div>".$liste_agenda[$i]["date_du_au"]." : ".$liste_agenda[$i]["titresub"]." - ".$liste_agenda[$i]["genre"]."</div></div></div>";
}
$CONTENU_AGENDA = '<div id="zone_bloc_evenement_accueil" class="zone_petite">';
$CONTENU_AGENDA .= '<div class="bloc_evenement_accueil bloc_gauche"><div>'.$agenda[0].'</div></div>';
$CONTENU_AGENDA .= '<div class="bloc_evenement_accueil bloc_milieu"><div>'.$agenda[1].'</div></div>';
$CONTENU_AGENDA .= '<div class="bloc_evenement_accueil bloc_milieu"><div>'.$agenda[2].'</div></div>';
$CONTENU_AGENDA .= '<div class="bloc_evenement_accueil bloc_droite"><div>'.$agenda[3].'</div></div>';
$CONTENU_AGENDA .= '</div>';
	//On récupère le Top 3
$infos_top3 = extraire_liste("agenda",3,1,array("du"=>date("Y-m-d"),"courte_duree_seulement"=>true,"illustree_seulement"=>true,"tri"=>"reputation","distance"=>-1));
$liste_top3 = $infos_top3["liste"];
$top3 = array();
for($i=0;$i<count($liste_top3);$i++){
	$html_bloc_top3 = '<a href="'.$root_site.'agenda.'.url_rewrite($NOM_VILLE).'.'.url_rewrite($liste_top3[$i]["titre"]).'.'.$ID_VILLE.'.'.$liste_top3[$i]["no"].'.html">';
		$html_bloc_top3 .= '<div class="image 4/3 invisible">';
			$html_bloc_top3 .= '<img src="'.$liste_top3[$i]["image"].'" />';
			$html_bloc_top3 .= '<div class="home_agenda_bloc_date">'.$liste_top3[$i]["datehome"].'</div>';
			$html_bloc_top3 .= '<div class="home_agenda_bloc_description">';
				$html_bloc_top3 .= '<div>';
					$html_bloc_top3 .= $liste_top3[$i]["date_du_au"].' : '.$liste_top3[$i]["titresub"].' - '.$liste_top3[$i]["genre"];
				$html_bloc_top3 .= '</div>';
			$html_bloc_top3 .= '</div>';
		$html_bloc_top3 .= '</div>';
	$html_bloc_top3 .= '</a>';
	$top3[] = $html_bloc_top3;
	//$top3[] = '<img src="'.$liste_top3[$i]["image"].'" /><div class="home_agenda_bloc_date">'.$liste_top3[$i]["datehome"].'</div><div class="home_agenda_bloc_description"><div>'.$liste_top3[$i]["date_du_au"].' : '.$liste_top3[$i]["titresub"].' - '.$liste_top3[$i]["genre"].'</div></div>';
}
$CONTENU_AGENDA_TOP3 = '<div id="home_agenda_top3_bloc" class="zone_petite">';
//	$CONTENU_AGENDA_TOP3 .= '<div class="bloc_evenement_accueil bloc_gauche">';
//		$CONTENU_AGENDA_TOP3 .= '<div class="blocTop3">';
//			$CONTENU_AGENDA_TOP3 .= '<span>TOP 3</span><br />Des événements les plus aimés&nbsp;:';
//		$CONTENU_AGENDA_TOP3 .= '</div>';
//		$CONTENU_AGENDA_TOP3 .= '<div>';
//			$CONTENU_AGENDA_TOP3 .= '<div>';
//				$CONTENU_AGENDA_TOP3 .= $top3[0];
//			$CONTENU_AGENDA_TOP3 .= '</div>';
//		$CONTENU_AGENDA_TOP3 .= '</div>';
//	$CONTENU_AGENDA_TOP3 .= '</div>';
//	$CONTENU_AGENDA_TOP3 .= '<div class="bloc_evenement_accueil bloc_milieu">';
//		$CONTENU_AGENDA_TOP3 .= '<div>';
//			$CONTENU_AGENDA_TOP3 .= '<div>';
//				$CONTENU_AGENDA_TOP3 .= $top3[1];
//			$CONTENU_AGENDA_TOP3 .= '</div>';
//		$CONTENU_AGENDA_TOP3 .= '</div>';
//		$CONTENU_AGENDA_TOP3 .= '<div>';
//			$CONTENU_AGENDA_TOP3 .= '<div>';
//				$CONTENU_AGENDA_TOP3 .= $top3[2];
//			$CONTENU_AGENDA_TOP3 .= '</div>';
//		$CONTENU_AGENDA_TOP3 .= '</div>';
//	$CONTENU_AGENDA_TOP3 .= '</div>';
	$CONTENU_AGENDA_TOP3 .= '<div class="bloc_evenement_accueil" style="text-align:center; width:100%;">';
		$CONTENU_AGENDA_TOP3 .= '<div>';
                        if ($infos_agenda["count_ville"] < 2) {
                            $sujet_agenda = 'événement';
                        }
                        else {
                            $sujet_agenda = 'événements';
                        }
			$CONTENU_AGENDA_TOP3 .= $infos_agenda["count_ville"].' '.$sujet_agenda.' à '.ucwords($NOM_VILLE_URL).' et '.$infos_agenda["count_proche"].' dans un rayon de 30 kms';
			$CONTENU_AGENDA_TOP3 .= '<a style="margin-left:50px;" href="'.$NOM_VILLE_URL.'.'.$ID_VILLE.'.agenda.html"><input value="Tout l\'agenda" class="ico agenda" type="button" /></a>';
			$CONTENU_AGENDA_TOP3 .= '<a href="espace-personnel.mes-fiches.html#agenda"><input class="ico plus" type="button" value="Ajouter un événement" /></a>';
		$CONTENU_AGENDA_TOP3 .= '</div>';
	$CONTENU_AGENDA_TOP3 .= '</div>';
$CONTENU_AGENDA_TOP3 .= '</div>';
/***
	4. Petites annonces
	**/
$infos_petiteannonce = extraire_liste("petite-annonce",4,1,array("distance"=>30));
$liste_petiteannonce = $infos_petiteannonce["liste"];
$CONTENU_PETITEANNONCE = '<div>Les 4 Dernières petites annonces'; //id="home_petiteannonce"
for($i=0;$i<count($liste_petiteannonce);$i++){
	if($liste_petiteannonce[$i]["image"]!="")
		$liste_petiteannonce[$i]["image"] = '<div class="image carre invisible"><img src="'.$liste_petiteannonce[$i]["image"].'" /></div>';
	$CONTENU_PETITEANNONCE .= '<a href="'.$root_site.'petite-annonce.'.url_rewrite($NOM_VILLE).'.'.url_rewrite($liste_petiteannonce[$i]["titre"]).'.'.$ID_VILLE.'.'.$liste_petiteannonce[$i]["no"].'.html"><div class="ligne_item">'.$liste_petiteannonce[$i]["image"].'<h2>'.$liste_petiteannonce[$i]["titre"].'<span class="ville"> '.a_le_ville($liste_petiteannonce[$i]["ville"]).'</span></h2>'.((!$liste_petiteannonce[$i]["monetaire"])?(""):('&nbsp;<img class="ico-monetaire infobulle" title="'.$liste_petiteannonce[$i]["prix"].'" src="img/monetaire.png" />')).'</div></a>';
}
//        $CONTENU_PETITEANNONCE .= '<br />'.$infos_petiteannonce["count_ville"].' petites annonces dans la ville (+ '.$infos_petiteannonce["count_proche"].' proches)<br /><br />';
        if ($infos_petiteannonce["count_ville"] < 2) {
            $sujet_annonce = 'annonce';
        }
        else {
            $sujet_annonce = 'annonces';
        }
        $CONTENU_PETITEANNONCE .= '<br />'.$infos_petiteannonce["count_ville"].' '.$sujet_annonce.' à '.ucwords($NOM_VILLE_URL).' et '.$infos_petiteannonce["count_proche"].' dans un rayon de 30 kms<br /><br />';
	$CONTENU_PETITEANNONCE .= '<a href="'.$NOM_VILLE_URL.'.'.$ID_VILLE.'.petite-annonce.30km.html"><input value="Toutes les annonces" class="ico annonce" type="button" /></a>';
	$CONTENU_PETITEANNONCE .= '<a href="espace-personnel.mes-fiches.html#petite-annonce"><input class="ico plus" type="button" value="Ajouter une annonce" /></a>';
$CONTENU_PETITEANNONCE .= '</div>';
/***
	5. Répertoire
	**/
$infos_repertoire = extraire_liste("repertoire",4,1,array("distance"=>30));
$liste_repertoire = $infos_repertoire["liste"];
$CONTENU_REPERTOIRE = '<div>Les 4 Dernières structures';//id="home_repertoire"
for($i=0;$i<count($liste_repertoire);$i++){
	if($liste_repertoire[$i]["image"]!="")
		$liste_repertoire[$i]["image"] = '<div class="image carre invisible"><img src="'.$liste_repertoire[$i]["image"].'" /></div>';
	$CONTENU_REPERTOIRE .= '<a href="'.$root_site.'structure.'.$NOM_VILLE_URL.'.'.url_rewrite($liste_repertoire[$i]["titre"]).'.'.$ID_VILLE.'.'.$liste_repertoire[$i]["no"].'.html"><div class="ligne_item">'.$liste_repertoire[$i]["image"].'<h2>'.$liste_repertoire[$i]["titre"].'<span class="ville"> '.a_le_ville($liste_repertoire[$i]["ville"]).'</span></h2></div></a>';
}
//	$CONTENU_REPERTOIRE .= '<br />'.$infos_repertoire["count_ville"].' structures dans la ville (+ '.$infos_repertoire["count_proche"].' proches)<br /><br />';
        if ($infos_repertoire["count_ville"] < 2) {
            $sujet_structure = 'structure';
        }
        else {
            $sujet_structure = 'structures';
        }
        $CONTENU_REPERTOIRE .= '<br />'.$infos_repertoire["count_ville"].' '.$sujet_structure.' à '.ucwords($NOM_VILLE_URL).' et '.$infos_repertoire["count_proche"].' dans un rayon de 30 kms<br /><br />';
	$CONTENU_REPERTOIRE .= '<a href="'.$NOM_VILLE_URL.'.'.$ID_VILLE.'.structure.html"><input value="Tout le répertoire" class="ico repertoire" type="button" /></a>';
	$CONTENU_REPERTOIRE .= '<a href="espace-personnel.mes-fiches.html#repertoire"><input class="ico plus" type="button" value="Ajouter une structure" /></a>';
$CONTENU_REPERTOIRE .= '</div>';
/***
	6. Forum
	**/
$infos_forum = extraire_liste("forum",3,1,array("distance"=>30));
$liste_forum = $infos_forum["liste"];
$CONTENU_FORUM = '<div id="home_forum">Les 3 Derniers forums';
for($i=0;$i<count($liste_forum);$i++){
	if($liste_forum[$i]["image"]!="")
		$liste_forum[$i]["image"] = '<div class="image carre invisible"><img src="'.$liste_forum[$i]["image"].'" /></div>';
	$CONTENU_FORUM .= '<a href="'.$root_site.'forum.'.$NOM_VILLE_URL.'.'.url_rewrite($liste_forum[$i]["titre"]).'.'.$ID_VILLE.'.'.$liste_forum[$i]["no"].'.html"><div class="ligne_item">'.$liste_forum[$i]["image"].'<h2>'.$liste_forum[$i]["titre"].'<span class="ville"> par '.$liste_forum[$i]["pseudo"].' <i>(Dernière activité le '.$liste_forum[$i]["date_modification"].')</i></span></h2></div></a>';
}
//	$CONTENU_FORUM .= '<br />'.$infos_forum["count_total"].' sujets de forums, dont '.($infos_forum["count_ville"]+1).' dans votre ville.<br /><br />';
        if ($infos_forum["count_ville"] < 2) {
            $sujet_forum = 'sujet';
        }
        else {
            $sujet_forum = 'sujets';
        }
        $CONTENU_FORUM .= '<br />'.$infos_forum["count_ville"].' '.$sujet_forum.' à '.ucwords($NOM_VILLE_URL).' et '.$infos_forum["count_proche"].' dans un rayon de 30 kms.<br /><br />';
	$CONTENU_FORUM .= '<a href="'.$NOM_VILLE_URL.'.'.$ID_VILLE.'.forum.30km.html"><input value="Tous les forums" class="ico forum" type="button" /></a>';
	$CONTENU_FORUM .= '<a href="espace-personnel.mes-fiches.html#forum"><input class="ico plus" type="button" value="Ajouter un sujet" /></a>';
$CONTENU_FORUM .= '</div>';

$requete_pub = "SELECT no, titre, contenu, url_image, site FROM publicites WHERE etat=1 AND type = 2 AND substring(validite_du, 1, 10) <= CURDATE() AND substring(validite_au, 1, 10) >= CURDATE() AND territoires_id = ".$territoire." ORDER BY RAND() LIMIT 0,1";
$tab_pub = execute_requete($requete_pub);
if (sizeof($tab_pub) > 0) {
    $tabsize = getimagesize($tab_pub[0]['url_image']); $largeur = $tabsize[0]; $margin = (728 - $largeur)/2; 
    if (($largeur == '') || ($largeur == 0)) {
        $largeur = 728; $margin = 0;
    }
    $contenu_pub = "<div style='width:728px; height:90px; color:#000000; margin:0 auto;' title='".$tab_pub[0]['titre']."'>";
    $contenu_pub .= "<a style='margin-left:".$margin."px;' target='_blank' href='".$tab_pub[0]['site']."'><img src='".$tab_pub[0]['url_image']."' width='".$largeur."' alt='".$tab_pub[0]['titre']."' /></a>";
    $contenu_pub .= "</div>";
}

$ligne1 = array(array("class"=>"grand_moyen bleu no_padding","titre"=>true,"id"=>"home_editorial","contenu"=>$CONTENU_EDITORIAL),array("id"=>"home_editorial_ei","class"=>"moyen_petit logo_ei","contenu"=>$CONTENU_EDITORIAL_ENSEMBLEICI));
$ligne2 = array(array("class"=>"grand gris_fonce padding_60 rectangle_bas","titre"=>true,"id"=>"home_agenda","contenu"=>$CONTENU_AGENDA));
$ligne3 = array(array("class"=>"grand rouge rectangle_haut","id"=>"home_top3agenda","contenu"=>$CONTENU_AGENDA_TOP3));
if (sizeof($tab_pub) > 0) {
    $ligne3bis = array(array("class"=>"grand rectangle_haut","id"=>"home_pub","contenu"=>$contenu_pub));
}
$ligne4 = array(array("class"=>"moyen pomme padding_60","id"=>"home_petiteannonce","titre"=>true,"contenu"=>$CONTENU_PETITEANNONCE),array("class"=>"moyen orange padding_60","titre"=>true,"id"=>"home_repertoire","contenu"=>$CONTENU_REPERTOIRE));
$ligne5 = array(array("class"=>"grand vert padding_60","id"=>"home_forum","titre"=>true,"contenu"=>$CONTENU_FORUM));
$lignes = array(array("lignes"=>$ligne1),array("lignes"=>$ligne2),array("class"=>"no_padding","lignes"=>$ligne3),array("lignes"=>$ligne3bis),array("lignes"=>$ligne4),array("lignes"=>$ligne5));	
?>
