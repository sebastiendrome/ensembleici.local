<?php
$requete_statut_liste = "SELECT * FROM statut ORDER BY libelle";
$tab_item = execute_requete($requete_statut_liste);

//BOUTON DE SUPPRESSIONS ET CRÉATIONS
$menu = '<div class="nouveau_suppression">';
        $menu .= '<div class="nouveau"><a href="?page=statut-structure&no=-1"><input type="button" value="Nouveau statut" class="nouveau" /></a></div>';
$menu .= '</div>';
if(!empty($NO)){
    $menu .= '<div style="text-align:center; margin-top:20px;">';
        $menu .= '<a href="'.$root_site_prod.'gestion/?page=statut-structure&no=" class="btn btn-danger">Retour liste</a>';
    $menu .= '</div>';
}

if(!empty($NO)){
    if($NO != -1){
        $requete_bloc_edit = "SELECT * FROM statut WHERE no = :no";
        $tab_item = execute_requete($requete_bloc_edit,array(":no"=>$NO));
        $monbloc = $tab_item[0];
        

        $contenu .= '<div class="bloc">';
        $contenu .= '<div><form class="form-horizontal" role="form">';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-1 control-label">Libellé</label>';
            $contenu .= '<div class="col-sm-5">';
                $contenu .= '<input type="text" class="form-control" id="libelle_statut" placeholder="Libellé" value="'.$monbloc['libelle'].'">';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group" style="text-align:center;">';
            $contenu .= '<div class="col-sm-8">';
                $contenu .= '<button type="submit" id="btn_update_statut" class="btn btn-primary">Enregistrer les modifcations</button>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<span id="statut_no" style="display:none;">'.$NO.'</span>';
        $contenu .=  '</form></div>';
        $contenu .= '</div>';
    }
    else {
        $contenu .= '<div class="bloc">';
        $contenu .= '<div><form class="form-horizontal" role="form">';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-1 control-label">Libellé</label>';
            $contenu .= '<div class="col-sm-5">';
                $contenu .= '<input type="text" class="form-control" id="libelle_statut" placeholder="Libellé" value="">';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group" style="text-align:center;">';
            $contenu .= '<div class="col-sm-8">';
                $contenu .= '<button type="submit" id="btn_add_statut" class="btn btn-primary">Ajouter le statut</button>';
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
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Libellé</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Nombre de structures</td>';
                $contenu .= '<td class="action"></td>';
            $contenu .= '</tr>';
        $i = 0;
        foreach ($tab_item as $k => $v) {
            $req_count_str = "SELECT COUNT(no) AS nb_str FROM structure WHERE no_statut = :no";
            $count_str = execute_requete($req_count_str,array(":no" => $v['no']));
	    $nb_str = $count_str[0]["nb_str"];
            
            $contenu .= '<tr class="'.(($i%2!=0)?"impaire":"paire").'" >';
                $contenu .= "<td>".$v['libelle']."</a></td>";
                $contenu .= "<td>".(($nb_str > 0) ? $nb_str : '')."</td>";
                $contenu .= '<td class="action_utilisateur">';
                    $contenu .= '<a href="?page='.$PAGE.'&no='.$v['no'].'"><input type="button" class="editer" value="" /></a>';
                    if ($nb_str == 0) {
                        $contenu .= '<input type="button" name="btn_delete_statut" data-ref="'.$v['no'].'" class="etiquette_suppression2" value="" />';
                    }
                    else {
                        $contenu .= '<input type="button" name="btn_delete_statut" data-ref="-1" class="etiquette_suppression2" value="" />';
                    }
                $contenu .= '</td>';
            $contenu .= "</tr>";
            $i++;
        }

        $contenu .= '</table>';
        
$contenu .= '</div></div>';
}

$page = array("menu"=>$menu,"contenu"=>$contenu);
?>
