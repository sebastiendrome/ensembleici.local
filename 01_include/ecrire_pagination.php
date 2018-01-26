<?php
$contenu .= '<div class="pagination">';
	if($nb_page>1){
		$url_page = $NOM_VILLE_URL.'.'.$ID_VILLE.'.'.$PAGE_COURANTE;
			if(!empty($LISTE_TAGS))
				$url_page .= ".tag".$LISTE_TAGS;
			if($DISTANCE!=0)
				$url_page .= ".".(($DISTANCE>0)?$DISTANCE."km":"tous");
			if(!empty($DU))
				$url_page .= ".du-".$DU_URL;
			if(!empty($TRI))
				$url_page .= '.'.$TRI;
		$url_page .= '.page[**num_page**].html';
		
		if($NUMERO_PAGE<6){ //Début
			$debut = 1;
			$fin = min(6,$nb_page);
			if($fin!=$nb_page)
				$ecrire_derniere_page = true;
			else
				$ecrire_derniere_page = false;
			$ecrire_premiere_page = false;
		}
		else{
			if($NUMERO_PAGE<=$nb_page-5){ //Fin
				$debut = $NUMERO_PAGE-2;
				$fin = $NUMERO_PAGE+2;
				$ecrire_derniere_page = true;
				$ecrire_premiere_page = true;
			}
			else{ //Milieu
				$debut = $nb_page-5;
				$fin = $nb_page;
				$ecrire_derniere_page = false;
				$ecrire_premiere_page = true;
			}
		}
		$contenu .= '<a href="'.str_replace("[**num_page**]",($NUMERO_PAGE-1),$url_page).'" id="fleche_gauche_'.$pagination.'" class="'.(($NUMERO_PAGE!=1)?"actif":"").'">&lsaquo;</a>';
		if($ecrire_premiere_page)
			$contenu .= '<a href="'.str_replace(".page[**num_page**]","",$url_page).'">1</a> ... ';
		for($j=$debut;$j<=$fin;$j++){
			if($j!=1)
				$url_bouton_page = str_replace("[**num_page**]",$j,$url_page);
			else
				$url_bouton_page = str_replace(".page[**num_page**]","",$url_page);
			$contenu .= '<a'.(($j!=$NUMERO_PAGE)?'':' class="select"').' href="'.$url_bouton_page.'">'.$j.'</a>';
		}
		if($ecrire_derniere_page)
			$contenu .= ' ... <a href="'.str_replace("[**num_page**]",$nb_page,$url_page).'">'.$nb_page.'</a>';
		$contenu .= '<a href="'.str_replace("[**num_page**]",($NUMERO_PAGE+1),$url_page).'" id="fleche_droite_'.$pagination.'" class="'.(($NUMERO_PAGE!=$nb_page)?"actif":"").'">&rsaquo;</a>';//rsaquo
	}
$contenu .= '</div>';
?>
