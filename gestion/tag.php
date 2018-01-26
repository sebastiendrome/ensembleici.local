<?php
//if (isset($_GET['num'])) {
//    $borneinf = ($_GET['num'] - 1) * 100;
//    $numpage = $_GET['num'];
//}
//else {
//    $borneinf = 0;
//    $numpage = 1;
//}
//$limit = $borneinf.', 100';

$requete_vie = "SELECT no, libelle FROM vie ORDER BY no";
$tab_vie = execute_requete($requete_vie,array());

$requete_tag_liste = "SELECT DISTINCT(T.no), T.titre, V.libelle FROM tag T LEFT JOIN vie_tag A ON T.no = A.no_tag LEFT JOIN vie V ON A.no_vie = V.no ";
//if (isset($_GET['titre'])) {
//    $requete_tag_liste .= "AND T.titre LIKE '%".$_GET['titre']."%' ";
//}
if (isset($_GET['vie'])) {
    $requete_tag_liste .= "AND V.no = ".$_GET['vie']." ";
}
if (isset($_GET['tri'])) {
    switch ($_GET['tri']) {
        case 0 : $requete_tag_liste .= "GROUP BY T.no ORDER BY T.no DESC"; break;
        case 1 : $requete_tag_liste .= "GROUP BY T.no ORDER BY V.libelle ASC"; break;
        case 2 : $requete_tag_liste .= "GROUP BY T.no ORDER BY V.libelle DESC"; break;
        default: $requete_tag_liste .= "GROUP BY T.no ORDER BY T.titre ASC"; break;
    }
}
else {
    $requete_tag_liste .= "GROUP BY T.no ORDER BY T.titre ASC";
}

$tab_item = execute_requete($requete_tag_liste,array());


//BOUTON DE SUPPRESSIONS ET CRÉATIONS
$menu = '<div class="nouveau_suppression">';
        $menu .= '<div class="nouveau"><a href="?page=tag&no=-1"><input type="button" value="Nouveau tag" class="nouveau" /></a></div>';
$menu .= '</div>';
if(!empty($NO)){
    $menu .= '<div style="text-align:center; margin-top:20px;">';
        $menu .= '<a href="'.$root_site_prod.'gestion/?page=tag&no=" class="btn btn-danger">Retour liste</a>';
    $menu .= '</div>';
}
//else {
//    $menu .= '<div>';
//        $menu .= '<div class="bloc_filtre_titre">Filtres</div>';
//        $menu .= '<div class="bloc_filtre_content">';
//            $menu .= '<div style="text-align:center;"><select id="sel_filtre_vie">';
//                $menu .= '<option value=""></option>';
//                foreach ($tab_vie as $k => $v) {
//                    $menu .= '<option value="'.$v['no'].'"'.((isset($_GET['vie']) && ($_GET['vie'] == $v['no'])) ? " selected=selected" : "").'>'.$v['libelle'].'</option>';
//                }
//            $menu .= '</select></div>';
//            $menu .= '<div style="text-align:center;"><a class="btn btn-success" id="btn_filtre_tag">Rechercher</a></div>';
//        $menu .= '</div>';
//        $menu .= '<div class="bloc_filtre_titre">Tri</div>';
//        $menu .= '<div class="bloc_filtre_content">'; 
//        $menu .= '<div><select id="sel_tri_tag">'; 
//            $menu .= '<option value="0"'.((isset($_GET['tri']) && ($_GET['tri'] == 0)) ? " selected=selected" : "").'>Création</option>';
//            $menu .= '<option value="1"'.((isset($_GET['tri']) && ($_GET['tri'] == 1)) ? " selected=selected" : "").'>Libellé ASC</option>';
//            $menu .= '<option value="2"'.((isset($_GET['tri']) && ($_GET['tri'] == 2)) ? " selected=selected" : "").'>Libellé DESC</option>';
//        $menu .= '</select></div>';
//        $menu .= '</div>';
//    $menu .= '</div>';
//}

if(!empty($NO)){
    if($NO != -1){
        $requete_bloc_edit = "SELECT * FROM tag WHERE no = :no";
        $tab_item = execute_requete($requete_bloc_edit,array(":no"=>$NO));
        $monbloc = $tab_item[0];
        
        // liste des catégories associées 
        $requete_vie = "SELECT no_vie FROM vie_tag WHERE no_tag = :no";
        $tab_vietag = execute_requete($requete_vie,array(":no"=>$NO));
        $prems = 1; $liste = '(';
        foreach ($tab_vietag as $k => $v) {
            if ($prems == 1) {
                $prems = 0;
            }
            else {
                $liste .= ',';
            }
            $liste .= $v['no_vie'];
        }
        $liste .= ')';
        
        $requete_categories = "SELECT no, libelle FROM vie WHERE no NOT IN ".$liste." ORDER BY libelle ASC";
        $tab_categories = execute_requete($requete_categories);
        
        $requete_associees = "SELECT no, libelle FROM vie WHERE no IN ".$liste." ORDER BY libelle ASC";
        $tab_associees = execute_requete($requete_associees);
        
        $categories = ''; $premier = 1;
        foreach ($tab_associees as $k => $v) {
            if ($premier == 1) {
                $premier = 0;
            }
            else {
                $categories .= ', ';
            }
            $categories .= $v['libelle'];
        }
        

        $contenu .= '<div class="bloc">';
        $contenu .= '<div><form class="form-horizontal" role="form">';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-1 control-label">Libellé</label>';
            $contenu .= '<div class="col-sm-5">';
                $contenu .= '<input type="text" class="form-control" id="libelle_tag" placeholder="Libellé" value="'.$monbloc['titre'].'">';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-1 control-label">Catégories liées</label>';
            $contenu .= '<div class="col-sm-5">';
                $contenu .= '<label style="margin-top:15px;">'.$categories.'</label>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-1 control-label">Nouvelle Catégorie</label>';
            $contenu .= '<div class="col-sm-5">';
                $contenu .= '<select id="type_tag">';
                $contenu .= '<option value="-1"></option>';
                foreach ($tab_categories as $k => $v) {
                    $contenu .= '<option value="'.$v['no'].'">'.$v['libelle'].'</option>';
                }
                $contenu .= '</select>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group" style="text-align:center;">';
            $contenu .= '<div class="col-sm-8">';
                $contenu .= '<button type="submit" id="btn_update_tag" class="btn btn-primary">Enregistrer les modifications</button>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<span id="tag_no" style="display:none;">'.$NO.'</span>';
        $contenu .=  '</form></div>';
        $contenu .= '</div>';
    }
    else {
        $requete_categories = "SELECT no, libelle FROM vie ORDER BY libelle ASC";
        $tab_categories = execute_requete($requete_categories);
        
        $contenu .= '<div class="bloc">';
        $contenu .= '<div><form class="form-horizontal" role="form">';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-1 control-label">Libellé</label>';
            $contenu .= '<div class="col-sm-5">';
                $contenu .= '<input type="text" class="form-control" id="libelle_tag" placeholder="Libellé" value="">';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-1 control-label">Catégorie</label>';
            $contenu .= '<div class="col-sm-5">';
                $contenu .= '<select id="type_tag">';
                $contenu .= '<option value="-1"></option>';
                foreach ($tab_categories as $k => $v) {
                    $contenu .= '<option value="'.$v['no'].'">'.$v['libelle'].'</option>';
                }
                $contenu .= '</select>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group" style="text-align:center;">';
            $contenu .= '<div class="col-sm-8">';
                $contenu .= '<button type="submit" id="btn_add_tag" class="btn btn-primary" id="">Ajouter le Tag</button>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .=  '</form></div>';
        $contenu .= '</div>';
    }
}
else {
    $contenu = '<div class="bloc"><div>';
        $contenu .= '<table>';
            $contenu .= '<tr class="titre">';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Tags</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Catégorie</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Editorial</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Agenda</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Annonces</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Structure</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Forum</td>';
                $contenu .= '<td class="action"></td>';
//                        $contenu .= '<td>Agenda</td>';
            $contenu .= '</tr>';
        $i = 0;
        foreach ($tab_item as $k => $v) {
            $req_count_evt = "SELECT COUNT(*) AS nb_evts FROM evenement_tag WHERE no_tag = :no";
            $count_evt = execute_requete($req_count_evt,array(":no" => $v['no']));
	    $nb_evts = $count_evt[0]["nb_evts"];
            
            $req_count_ann = "SELECT COUNT(*) AS nb_ann FROM petiteannonce_tag WHERE no_tag = :no";
            $count_ann = execute_requete($req_count_ann,array(":no" => $v['no']));
	    $nb_ann = $count_ann[0]["nb_ann"];
            
            $req_count_str = "SELECT COUNT(*) AS nb_str FROM structure_tag WHERE no_tag = :no";
            $count_str = execute_requete($req_count_str,array(":no" => $v['no']));
	    $nb_str = $count_str[0]["nb_str"];
            
            $req_count_for = "SELECT COUNT(*) AS nb_for FROM forum_tag WHERE no_tag = :no";
            $count_for = execute_requete($req_count_for,array(":no" => $v['no']));
	    $nb_for = $count_for[0]["nb_for"];
            
            $req_count_edi = "SELECT COUNT(*) AS nb_edi FROM editorial_tag WHERE no_tag = :no";
            $count_edi = execute_requete($req_count_edi,array(":no" => $v['no']));
	    $nb_edi = $count_edi[0]["nb_edi"];
            
            $totalcrea = $nb_evts + $nb_ann + $nb_str + $nb_for;
            
            $requete_categ = "SELECT V.libelle FROM vie_tag A, vie V WHERE V.no = A.no_vie AND A.no_tag = :no";
            $tab_vies = execute_requete($requete_categ,array(":no" => $v['no']));
            $libelle = ''; $j = 1;
            foreach ($tab_vies as $k1 => $v1) {
                if ($j != 1) {
                    $libelle .= ', ';
                }
                $libelle .= $v1['libelle'];
                $j++;
            }
            
            $contenu .= '<tr class="'.(($i%2!=0)?"impaire":"paire").'" >';
                $contenu .= "<td>".$v['titre']."</td>";
                $contenu .= "<td>".$libelle."</td>";
                $contenu .= "<td style='text-align:center;'>".(($nb_edi > 0) ? $nb_edi : '')."</td>";
                $contenu .= "<td style='text-align:center;'>".(($nb_evts > 0) ? $nb_evts : '')."</td>";
                $contenu .= "<td style='text-align:center;'>".(($nb_ann > 0) ? $nb_ann : '')."</td>";
                $contenu .= "<td style='text-align:center;'>".(($nb_str > 0) ? $nb_str : '')."</td>";
                $contenu .= "<td style='text-align:center;'>".(($nb_for > 0) ? $nb_for : '')."</td>";
                $contenu .= '<td class="action_utilisateur">';
                    $contenu .= '<a href="?page='.$PAGE.'&no='.$v['no'].'"><input type="button" class="editer" value="" /></a>';
                    if ($totalcrea == 0) {
                        $contenu .= '<input type="button" name="btn_delete_tag" data-ref="'.$v['no'].'" class="etiquette_suppression2" value="" />';
                    }
                    else {
                        $contenu .= '<input type="button" name="btn_delete_tag" data-ref="-1" class="etiquette_suppression2" value="" />';
                    }
                $contenu .= '</td>';
            $contenu .= "</tr>";
            $i++;
        }

        $contenu .= '</table>';
        
        // pagination 
        $contenu .= '<div style="text-align:center;">';
        $contenu .= '<nav><ul class="pagination">'; 
        $contenu .= '<li class="'.(($numpage == 1) ? "disabled" : "").'"><a href="'.$root_site_prod.'gestion/?page=tag&no=&num='.($numpage - 1).'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        $contenu .= '<li class="'.((count($tab_item) < 100) ? "disabled" : "").'"><a href="'.$root_site_prod.'gestion/?page=tag&no=&num='.($numpage + 1).'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        $contenu .= '</ul></nav>';
        $contenu .= '</div>';
        
$contenu .= '</div></div>';
}

$page = array("menu"=>$menu,"contenu"=>$contenu);
?>
