<?php
switch ($PAGE) {
    case 'agenda' : $typeevt = 'événement'; $tab_liaison = 'evenement'; break;
    case 'evenement' : $typeevt = 'événement'; $tab_liaison = 'evenement'; break;
    case 'petiteannonce' : $typeevt = 'annonce'; $tab_liaison = 'petiteannonce'; break;
    case 'petite-annonce' : $typeevt = 'annonce'; $tab_liaison = 'petiteannonce'; break;
    case 'structure' : $typeevt = 'structure'; $tab_liaison = 'structure'; break;
    default: $typeevt = 'annonce'; $tab_liaison = 'petiteannonce'; break;
}
$contenu .= '<br/><br/><div class="bloc" id="liaisons">';
	$contenu .= '<div>';
		$contenu .= '<h1>Liaisons</h1>';
                $contenu .= '<span style="display:none;" id="liaison_no">'.$NO.'</span>';
                $contenu .= '<span style="display:none;" id="liaison_page">'.$tab_liaison.'</span>';
                $contenu .= '<div>Vous pouvez lier votre '.$typeevt.' à un événement, une petite annonce ou un structure préalablement créé.</div>';
                $contenu .= '<div style="margin-top:15px; margin-left:35px;">';
                    $contenu .= '<table><tr><td><label>Type de liaison</label></td><td>';
                    $contenu .= '<select id="select_liaison_fiche" style="width:250px;"><option value="-1" selected="selected"></option><option value="1">Evènement</option><option value="2">Petite annonce</option><option value="3">Structure</option></select>';
                    $contenu .= '</td></tr><tr><td style="padding-top: 10px;"><label>Nom de la fiche</label></td><td style="padding-top: 10px;">';
                    $contenu .= '<input type="text" id="nom_liaison_fiche" style="width:250px;" />';
//                    $contenu .= '</td></tr><tr><td colspan="2" style="text-align:center;padding-top: 10px;">';
//                    $contenu .= '<input type="button" id="btn_valid_liaison_fiche" class="btn btn-success" value="Lier les fiches" />';
                    $contenu .= '</td></tr></table>';
                $contenu .= '</div>';
                $contenu .= '<div style="display:none; text-align:center; margin-top:15px;" id="affiche_recherche_liaison">aaaaa</div>';
                
                if (isset($liaisons) && (sizeof($liaisons) > 0)) {
                    $contenu .= '<br/><div id="espace_fiches_liees"><h2>Fiches liées</h2><div id="div_fiches_liees">';
                    foreach ($liaisons as $k => $v) {
                        $contenu .= '<div>'.$v.'</div>';
                    }
                    $contenu .= '</div></div>';
                }
                else {
                    $contenu .= '<br/><div id="espace_fiches_liees" style="display:none;"><h2>Fiches liées</h2><div id="div_fiches_liees"></div></div>';
                }
                
//		$contenu .= '<div><input type="button" value="Ajouter" /></div>';
	$contenu .= '</div>';
$contenu .= '</div>';
$contenu .= '</br></br>';
?>
