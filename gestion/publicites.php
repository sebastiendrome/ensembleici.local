<?php


//BOUTON DE SUPPRESSIONS ET CRÉATIONS
$menu = '<div class="nouveau_suppression">';
        $menu .= '<div class="nouveau"><a href="?page=publicites&no=-1"><input type="button" value="Nouvelle publicité" class="nouveau" /></a></div>';
$menu .= '</div>';
if(!empty($NO)){
    $menu .= '<div style="text-align:center; margin-top:20px;">';
        $menu .= '<a href="'.$root_site_prod.'gestion/?page=publicites&no=" class="btn btn-danger">Retour liste</a>';
    $menu .= '</div>';
}

if(!empty($NO)){
    if($NO != -1){
        $requete_publicite = "SELECT * FROM publicites WHERE no = :no";
        $tab_item = execute_requete($requete_publicite, array(":no" => $NO));
        $mapub = $tab_item[0];
        
        $date_debut = substr($mapub['validite_du'], 8, 2).'/'.substr($mapub['validite_du'], 5, 2).'/'.substr($mapub['validite_du'], 0, 4);
        $date_fin = substr($mapub['validite_au'], 8, 2).'/'.substr($mapub['validite_au'], 5, 2).'/'.substr($mapub['validite_au'], 0, 4);

        $contenu .= '<div class="bloc">';
        $contenu .= '<span class="hide" id="publicite_no">'.$mapub['no'].'</span>';
        $contenu .= '<div><form class="form-horizontal" role="form">';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-2 control-label">* Titre</label>';
            $contenu .= '<div class="col-sm-6">';
                $contenu .= '<input type="text" class="form-control" id="publicite_titre" placeholder="Titre" value="'.$mapub['titre'].'">';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-2 control-label">* Dates de validité</label>';
            $contenu .= '<div class="col-sm-3">';
                $contenu .= '<input type="text" id="publicite_debut" placeholder="Début de validité" value="'.$date_debut.'" />';
            $contenu .= '</div>';
            $contenu .= '<div class="col-sm-3">';
                $contenu .= '<input type="text" id="publicite_fin" placeholder="Fin de validité" value="'.$date_fin.'" />';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<div class="col-sm-6 col-sm-offset-3">';
                $contenu .= '<img src="'.$root_site.$mapub['url_image'].'"';
                if ($mapub['type'] == 1) {
                    $contenu .= 'style="max-width:200px;" />';
                }
                else {
                    $contenu .= 'style="max-height:200px;" />';
                }
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-2 control-label">Site lié</label>';
            $contenu .= '<div class="col-sm-6">';
                $contenu .= '<input type="text" class="form-control" id="publicite_site" placeholder="url du site lié" value="'.$mapub['site'].'">
                            Veuilllez renseigner une url préfixée par http:// ou https:// pour une bonne prise en compte de votre saisie.';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group" style="text-align:center;">';
            $contenu .= '<div class="col-sm-8">';
                $contenu .= '<a id="btn_update_publicite" class="btn btn-primary">Modifier la publicité</a>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .=  '</form></div>';
        $contenu .= '</div>';
    }
    else {
        $contenu .= '<div class="bloc">';
        $contenu .= '<div><form class="form-horizontal" role="form">';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-2 control-label">* Titre</label>';
            $contenu .= '<div class="col-sm-6">';
                $contenu .= '<input type="text" class="form-control" id="publicite_titre" placeholder="Titre" value="">';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-2 control-label">* Dates de validité</label>';
            $contenu .= '<div class="col-sm-3">';
                $contenu .= '<input type="text" id="publicite_debut" placeholder="Début de validité" />';
            $contenu .= '</div>';
            $contenu .= '<div class="col-sm-3">';
                $contenu .= '<input type="text" id="publicite_fin" placeholder="Fin de validité" />';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-2 control-label">* Emplacement</label>';
            $contenu .= '<div class="col-sm-3">';
                $contenu .= '<select id="publicite_page">';
                    $contenu .= '<option value="0" selected="selected">Toutes les pages</option><option value="1">Editorial</option><option value="2">Agenda</option>
                        <option value="3">Annonces</option><option value="4">Structures</option><option value="5">Forum</option>';
                $contenu .= '</select>';
            $contenu .= '</div>';
            $contenu .= '<label class="col-sm-2 control-label">* Type</label>';
            $contenu .= '<div class="col-sm-3">';
                $contenu .= '<select id="publicite_vente">';
                    $contenu .= '<option value="0" selected="selected">Partenaires</option><option value="1">Espace publicitaire</option>';
                $contenu .= '</select>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-2 control-label">* Type de bannière</label>';
            $contenu .= '<div class="col-sm-3">';
                $contenu .= '<input type="radio" name="rad_type_pub" value="1" checked="checked" /> Format carré pour colonne de droite';
            $contenu .= '</div>';
            $contenu .= '<div class="col-sm-3">';
                $contenu .= '<input type="radio" name="rad_type_pub" value="3" /> Format vertical pour colonne de droite';
            $contenu .= '</div>';
            $contenu .= '<div class="col-sm-3">';
                $contenu .= '<input type="radio" name="rad_type_pub" value="2" /> Format rectangle pour page accueil';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-2 control-label">* Visuel</label>';
            $contenu .= '<div class="col-sm-2" id="plupload2">';
                $contenu .= '<div id="browse2"><a class="btn btn-success">Charger le visuel</a></div>';
                $contenu .= "<div id='progressgen2' style='color:#790000; font-weight: bolder;'></div>";
                $contenu .= "<div id='filelist2' class='hide'></div><br/>";
            $contenu .= '</div>';
            $contenu .= '<div class="col-sm-2" id="plupload2bis" style="display:none;">';
                $contenu .= '<div id="browse2bis"><a class="btn btn-success">Charger le visuel</a></div>';
                $contenu .= "<div id='progressgen2bis' style='color:#790000; font-weight: bolder;'></div>";
                $contenu .= "<div id='filelist2bis' class='hide'></div><br/>";
            $contenu .= '</div>';
            $contenu .= '<div class="col-sm-2" id="plupload2ter" style="display:none;">';
                $contenu .= '<div id="browse2ter"><a class="btn btn-success">Charger le visuel</a></div>';
                $contenu .= "<div id='progressgen2ter' style='color:#790000; font-weight: bolder;'></div>";
                $contenu .= "<div id='filelist2ter' class='hide'></div><br/>";
            $contenu .= '</div>';
            $contenu .= '<div class="col-sm-8">';
            $contenu .= "Pour assurer une cohérence au niveau de l'affichage, vous devez charger une image proportionnelle au format  
                        <span id='li_format_carre'>300x250 pour affichage sur la colonne de droite</span>
                        <span id='li_format_rectangle' style='display:none;'>728x90 pour affichage sur page accueil</span>
                        <span id='li_format_carre2' style='display:none;'>300x600 pour affichage sur la colonne de droite</span><br/>
                        Si ce n'était pas le cas, l'ajustement se fera sur la dimension maximale et une marge ser ajoutée à l'affichage.";
            $contenu .= '</div>';
            $contenu .= '<div class="col-sm-9 col-sm-offset-3" id="exist_image_name"></div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-2 control-label">Site lié</label>';
            $contenu .= '<div class="col-sm-6">';
                $contenu .= '<input type="text" class="form-control" id="publicite_site" placeholder="url du site lié" value="">
                            Veuilllez renseigner une url préfixée par http:// ou https:// pour une bonne prise en compte de votre saisie.';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group" style="text-align:center;">';
            $contenu .= '<div class="col-sm-8">';
                $contenu .= '<a id="btn_add_publicite" class="btn btn-primary">Ajouter la publicité</a>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .=  '</form></div>';
        $contenu .= '</div>';
    }
}
else {
    $requete_statut_liste = "SELECT P.* FROM publicites P  WHERE P.territoires_id = $territoire ORDER BY P.type, P.no DESC";
    $tab_item = execute_requete($requete_statut_liste);

    $contenu = '<div class="bloc"><div>';
        $contenu .= '<table>';
            $contenu .= '<tr class="titre">';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Titre</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Type</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Visuel</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Affichage</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Début validité</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Fin validité</td>';
//                $contenu .= '<td style="text-align:center; font-weight:bolder;">Compatibilté</td>';
                $contenu .= '<td class="action"></td>';
            $contenu .= '</tr>';
        $i = 0;
        foreach ($tab_item as $k => $v) {
            $date_debut = ''; 
            $ligne_active = 1;
            if ($v['type'] == 2) {
                $affichage = 'Accueil';
            }
            else {
                if ($v['vente'] == 0) {
                     $affichage = 'Toutes les pages';
                }
                else {
                    switch ($v['page']) {
                        case 0 : $affichage = 'Toutes les pages'; break;
                        case 1 : $affichage = 'Editorial'; break;
                        case 2 : $affichage = 'Agenda'; break;
                        case 3 : $affichage = 'Annonces'; break;
                        case 4 : $affichage = 'Structures'; break;
                        case 5 : $affichage = 'Forum'; break;
                        default: break;
                    }
                }
            }
            if ($v['vente'] == 0) {
                $partenariat = 'Partenaire';
            }
            else {
                $partenariat = 'Publicité';
            }
            if (($v['validite_du'] > date('Y-m-d')) || ($v['validite_au'] < date('Y-m-d'))) {
                $ligne_active = 0;
            }
            if (($v['validite_du'] != '') && (substr($v['validite_du'], 0, 10) != '0000-00-00')) {
                $date_debut = substr($v['validite_du'], 8, 2).'/'.substr($v['validite_du'], 5, 2).'/'.substr($v['validite_du'], 0, 4);
            }
            $date_fin = ''; 
            if (($v['validite_au'] != '') && (substr($v['validite_au'], 0, 10) != '0000-00-00')) {
                $date_fin = substr($v['validite_au'], 8, 2).'/'.substr($v['validite_au'], 5, 2).'/'.substr($v['validite_au'], 0, 4);
            }
            $contenu .= '<tr class="'.(($i%2!=0)?"impaire":"paire").(($ligne_active)?"":" non_actif").'" >';
                $contenu .= "<td>".$v['titre']."</td>";
                $contenu .= "<td>".$partenariat."</td>";
                $contenu .= "<td>"; 
                if ($v['site'] != '') {
                    $contenu .= "<a target='_blank' href='".$v['site']."' >";
                }
                $contenu .= "<img src='".$root_site_prod.$v['url_image']."' style='max-width:200px;max-height:50px;margin-top:10px;margin-bottom:10px;' />"; 
                if ($v['site'] != '') {
                    $contenu .= "</a>";
                }
                $contenu .= "</td>";
                $contenu .= "<td style='text-align:center;'>".$affichage."</td>";
                $contenu .= "<td style='text-align:center;'>".$date_debut."</td>";
                $contenu .= "<td style='text-align:center;'>".$date_fin."</td>";
//                $contenu .= "<td style='text-align:center;'>Site</td>";
                $contenu .= '<td class="action_utilisateur">';
                    $contenu .= '<a href="?page='.$PAGE.'&no='.$v['no'].'"><input type="button" class="editer" value="" /></a>';
                    $contenu .= '<input type="button" name="btn_delete_publicite" data-ref="'.$v['no'].'" class="etiquette_suppression2" value="" />';
                $contenu .= '</td>';
            $contenu .= "</tr>";
            $i++;
        }

        $contenu .= '</table>';
        
$contenu .= '</div></div>';
}

$page = array("menu"=>$menu,"contenu"=>$contenu);
?>
