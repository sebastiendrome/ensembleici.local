<?php
//RECHERCHE
//$menu = '<div class="recherche">';
//        $menu .= '<input type="text" value="Rechercher" title="Rechercher" class="recherche vide" /><input type="button" class="recherche" />';
//$menu .= '</div>';
////RESULTAT DE RECHERCHE
//$menu .= '<div id="zone_recherche" class="vide">';
//$menu .= '</div>';
//BOUTON DE SUPPRESSIONS ET CRÉATIONS
$menu = '<div class="nouveau_suppression">';
        $menu .= '<div class="nouveau"><a href="?page=abonnes&no=-1"><input type="button" value="Nouvel inscrit" class="nouveau" /></a></div>';
$menu .= '</div>';
if(!empty($NO)){
    $menu .= '<div style="text-align:center; margin-top:20px;">';
        $menu .= '<a href="'.$root_site_prod.'gestion/?page=abonnes&no=" class="btn btn-danger">Retour liste</a>';
    $menu .= '</div>';
}
else {
    $menu .= '<div>';
        $menu .= '<div class="bloc_filtre_titre">Filtres</div>';
        $menu .= '<div class="bloc_filtre_content">';
            $menu .= '<div>';
            $menu .= '<input type="text" class="form-control" id="abonnes_filtre_email" placeholder="Email" value="'.((isset($_GET['mail'])) ? $_GET['mail'] : "").'" />';
            $menu .= '</div>';
            $menu .= '<div><input type="text" class="form-control" id="abonnes_filtre_ville" placeholder="Ville" value="'.((isset($_GET['ville'])) ? $_GET['ville'] : "").'" /></div>';
            $menu .= '<div style="text-align:center;"><a class="btn btn-success" id="btn_filtre_abonne">Rechercher</a></div>';
        $menu .= '</div>';
        $menu .= '<div class="bloc_filtre_titre">Tri</div>';
        $menu .= '<div class="bloc_filtre_content">'; 
        $menu .= '<div><select id="sel_tri_abonne">'; 
            $menu .= '<option value="0"'.((isset($_GET['tri']) && ($_GET['tri'] == 0)) ? " selected=selected" : "").'>Inscription</option>';
            $menu .= '<option value="1"'.((isset($_GET['tri']) && ($_GET['tri'] == 1)) ? " selected=selected" : "").'>Email ASC</option>';
            $menu .= '<option value="2"'.((isset($_GET['tri']) && ($_GET['tri'] == 2)) ? " selected=selected" : "").'>Email DESC</option>';
            $menu .= '<option value="3"'.((isset($_GET['tri']) && ($_GET['tri'] == 3)) ? " selected=selected" : "").'>Ville ASC</option>';
            $menu .= '<option value="4"'.((isset($_GET['tri']) && ($_GET['tri'] == 4)) ? " selected=selected" : "").'>Ville DESC</option>';
        $menu .= '</select></div>';
        $menu .= '</div>';
    $menu .= '</div>';
}

if(!empty($NO)){
    if($NO != -1){
        $requete_utilisateur_edit = "SELECT U.email, U.date_inscription, U.etat, U.no, V.nom_ville_maj FROM newsletter U, villes V WHERE V.id = U.no_ville AND U.no = :no";
        $tab_item = execute_requete($requete_utilisateur_edit,array(":no"=>$NO));
        $monuser = $tab_item[0];
        $inscription = substr($monuser['date_inscription'], 8,2).'/'.substr($monuser['date_inscription'], 5, 2).'/'.substr($monuser['date_inscription'], 0, 4);
        switch ($monuser['droits']) {
            case 'A' : $droit = 2; break;
            case 'E' : $droit = 1; break;
            default: $droit = 0; break;
        }
        
        $contenu = '<div class="bloc entete">';
            $contenu .= '<div>';
                $contenu .= '<div class="infos">';
                    $contenu .= 'Numéro : '.$NO;
                    $contenu .= '<br/>';
                    $contenu .= 'Créé le '.$inscription;
                $contenu .= '</div>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="bloc">';
        $contenu .= '<div><form class="form-horizontal" role="form">';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-3 control-label">Adresse Email</label>';
            $contenu .= '<div class="col-sm-4">';
                $contenu .= '<input type="text" class="form-control" id="user_email" placeholder="Adresse email" value="'.$monuser['email'].'">';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-3 control-label">Ville</label>';
            $contenu .= '<div class="col-sm-4">';
//                $contenu .= '<input type="text" class="form-control" id="user_ville" placeholder="Ville" value="'.$monuser['nom_ville_maj'].'">';
                $contenu .= '<input type="text" class="recherche_ville" id="user_ville" placeholder="Code postal - Ville" value="'.$monuser['nom_ville_maj'].'"><input type="hidden" name="BDDno_ville" id="BDDno_ville" value="'.$monuser['no_ville'].'" /><div id="recherche_ville_liste"><div></div></div>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group" style="text-align:center;">';
            $contenu .= '<div class="col-sm-8">';
                $contenu .= '<button type="submit" id="btn_update_abonne" class="btn btn-primary" id="">Enregistrer les modifcations</button>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<span id="user_no" style="display:none;">'.$NO.'</span>';
        $contenu .=  '</form></div>';
        $contenu .= '</div>';
    }
    else {
        $contenu .= '<div class="bloc">';
        $contenu .= '<div><form class="form-horizontal" role="form">';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-3 control-label">Adresse Email</label>';
            $contenu .= '<div class="col-sm-4">';
                $contenu .= '<input type="text" class="form-control" id="user_email" placeholder="Adresse email">';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-3 control-label">Ville</label>';
            $contenu .= '<div class="col-sm-4">';
                $contenu .= '<input type="text" class="recherche_ville" id="user_ville" placeholder="Code postal - Ville"><input type="hidden" name="BDDno_ville" id="BDDno_ville" value="'.$no_ville.'" /><div id="recherche_ville_liste"><div></div></div>';
                
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group" style="text-align:center;">';
            $contenu .= '<div class="col-sm-8">';
                $contenu .= '<button type="submit" id="btn_add_abonne" class="btn btn-primary" id="">Enregistrer l\'abonné</button>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .=  '</form></div>';
        $contenu .= '</div>';
    }
}
else {
    if (isset($_GET['num'])) {
        $borneinf = ($_GET['num'] - 1) * 100;
        $numpage = $_GET['num'];
    }
    else {
        $borneinf = 0;
        $numpage = 1;
    }
    $limit = $borneinf.', 100';

    $requete_utilisateur_liste = "SELECT U.email, U.date_inscription, U.etat, U.no, V.nom_ville_maj FROM newsletter U, villes V, communautecommune_ville T, communautecommune C WHERE V.id = U.no_ville AND U.no_ville = T.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t ";
    if (isset($_GET['mail'])) {
        $requete_utilisateur_liste .= "AND U.email LIKE '%".$_GET['mail']."%' ";
    }
    if (isset($_GET['ville'])) {
        $requete_utilisateur_liste .= "AND V.nom_ville_maj LIKE '%".$_GET['ville']."%' ";
    }
    if (isset($_GET['tri'])) {
        switch ($_GET['tri']) {
            case 0 : $requete_utilisateur_liste .= "ORDER BY U.no DESC LIMIT ".$limit; break;
            case 1 : $requete_utilisateur_liste .= "ORDER BY U.email ASC LIMIT ".$limit; break;
            case 2 : $requete_utilisateur_liste .= "ORDER BY U.email DESC LIMIT ".$limit; break;
            case 3 : $requete_utilisateur_liste .= "ORDER BY V.nom_ville_maj ASC LIMIT ".$limit; break;
            case 4 : $requete_utilisateur_liste .= "ORDER BY V.nom_ville_maj DESC LIMIT ".$limit; break;
            default: $requete_utilisateur_liste .= "ORDER BY U.no DESC LIMIT ".$limit; break;
        }
    }
    else {
        $requete_utilisateur_liste .= "ORDER BY U.no DESC LIMIT ".$limit;
    }

    $tab_item = execute_requete($requete_utilisateur_liste,array(":t"=>$territoire));
    
    $requete_utilisateur_count = "SELECT COUNT(U.no) as nb FROM newsletter U, villes V, communautecommune_ville T, communautecommune C WHERE V.id = U.no_ville AND U.no_ville = T.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t ";
    $count_user = execute_requete($requete_utilisateur_count,array(":t"=>$territoire));
    $nb_user = $count_user[0]["nb"];
    
    $requete_utilisateur_count2 = "SELECT COUNT(U.no) as nb FROM newsletter U, villes V, communautecommune_ville T, communautecommune C WHERE V.id = U.no_ville AND U.no_ville = T.no_ville AND T.no_communautecommune = C.no AND U.etat = 1 AND C.territoires_id = :t ";
    $count_user2 = execute_requete($requete_utilisateur_count2,array(":t"=>$territoire));
    $nb_user2 = $count_user2[0]["nb"];

    $contenu = '<div class="bloc">'; 
    $contenu .= "<div style='text-align:center;'>Nombre total d'inscrits : ".$nb_user." (".$nb_user2." recevant la newsletter)</div>";
    $contenu .= '<div>';
        $contenu .= '<table>';
            $contenu .= '<tr class="titre">';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Email</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Ville</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Inscription</td>';
                $contenu .= '<td class="action"></td>';
//                        $contenu .= '<td>Agenda</td>';
            $contenu .= '</tr>';
        $i = 0;
        foreach ($tab_item as $k => $v) {
            $inscription = substr($v['date_inscription'], 8,2).'/'.substr($v['date_inscription'], 5, 2).'/'.substr($v['date_inscription'], 0, 4);
            $contenu .= '<tr class="'.(($i%2!=0)?"impaire":"paire").'" >';
                $contenu .= "<td><a href='mailto:".$v['email']."'>".$v['email']."</a></td>";
                $contenu .= "<td>".$v['nom_ville_maj']."</td>";
                $contenu .= "<td style='text-align:center;'>".$inscription."</td>";
                $contenu .= '<td class="action_utilisateur">';
                    $contenu .= '<input id="'.$v['no'].'" type="button" name="btn_active_abonne" data-etat="'.$v["etat"].'" data-ref="'.$v['no'].'" class="activer'.(($v["etat"]) ? " actif" : "").'" value="" />';
                    $contenu .= '<a href="?page='.$PAGE.'&no='.$v['no'].'"><input type="button" class="editer" value="" /></a>';
                    $contenu .= '<input type="button" name="btn_del_abonne" data-ref="'.$v['no'].'" class="etiquette_suppression2" value="" />';
                $contenu .= '</td>';
            $contenu .= "</tr>";
            $i++;
        }

        $contenu .= '</table>';
        
        // pagination 
        $contenu .= '<div style="text-align:center;">';
        $contenu .= '<nav><ul class="pagination">'; 
        $contenu .= '<li class="'.(($numpage == 1) ? "disabled" : "").'"><a href="'.$root_site_prod.'gestion/?page=abonnes&no=&num='.($numpage - 1).'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        $contenu .= '<li class="'.((count($tab_item) < 100) ? "disabled" : "").'"><a href="'.$root_site_prod.'gestion/?page=abonnes&no=&num='.($numpage + 1).'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        $contenu .= '</ul></nav>';
        $contenu .= '</div>';
        
$contenu .= '</div></div>';
}

$page = array("menu"=>$menu,"contenu"=>$contenu);
?>
