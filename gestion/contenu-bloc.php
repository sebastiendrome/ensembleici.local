<?php
$requete_bloc_liste = "SELECT * FROM contenu_blocs WHERE territoires_id IN (0, :t) ORDER BY territoires_id ";
$tab_item = execute_requete($requete_bloc_liste,array(":t"=>$territoire));

//BOUTON DE SUPPRESSIONS ET CRÉATIONS
$menu = '<div class="nouveau_suppression">';
$menu .= '</div>';
if(!empty($NO)){
    $menu .= '<div style="text-align:center; margin-top:20px;">';
        $menu .= '<a href="'.$root_site_prod.'gestion/?page=contenu-bloc&no=" class="btn btn-danger">Retour liste</a>';
    $menu .= '</div>';
}

if(!empty($NO)){
    if($NO != -1){
        $requete_bloc_edit = "SELECT * FROM contenu_blocs WHERE no = :no";
        $tab_item = execute_requete($requete_bloc_edit,array(":no"=>$NO));
        $monbloc = $tab_item[0];
        

        $contenu .= '<div class="bloc">';
        $contenu .= '<div><form class="form-horizontal" role="form">';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-1 control-label">Titre</label>';
            $contenu .= '<div class="col-sm-5">';
                $contenu .= '<input type="text" class="form-control" id="titre_bloc" placeholder="Adresse email" value="'.$monbloc['titre'].'">';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-1 control-label">Contenu</label>';
            $contenu .= '<div class="col-sm-5">';
                $contenu .= '<textarea id="contenu_bloc">'.$monbloc['contenu'].'</textarea>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group" style="text-align:center;">';
            $contenu .= '<div class="col-sm-8">';
                $contenu .= '<button type="submit" id="btn_update_bloc" class="btn btn-primary">Enregistrer les modifcations</button>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<span id="bloc_no" style="display:none;">'.$NO.'</span>';
        $contenu .=  '</form></div>';
        $contenu .= '</div>';
    }
}
else {
    $contenu = '<div class="bloc"><div>';
        $contenu .= '<table>';
            $contenu .= '<tr class="titre">';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Nom</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Titre</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Type</td>';
                $contenu .= '<td class="action"></td>';
            $contenu .= '</tr>';
        $i = 0;
        foreach ($tab_item as $k => $v) {
            if ($v['territoires_id'] == 0) {
                $type = 'Commun';
            }
            else {
                $type =  'Personalisé';
            }
            $contenu .= '<tr class="'.(($i%2!=0)?"impaire":"paire").'" >';
                $contenu .= "<td>".$v['nom_bloc']."</a></td>";
                $contenu .= "<td>".$v['titre']."</td>";
                $contenu .= "<td>".$type."</td>";
                $contenu .= '<td class="action_utilisateur">';
                    $contenu .= '<a href="?page='.$PAGE.'&no='.$v['no'].'"><input type="button" class="editer" value="" /></a>';
                $contenu .= '</td>';
            $contenu .= "</tr>";
            $i++;
        }

        $contenu .= '</table>';
        
$contenu .= '</div></div>';
}

$page = array("menu"=>$menu,"contenu"=>$contenu);
?>
