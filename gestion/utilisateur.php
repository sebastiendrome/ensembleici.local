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
        $menu .= '<div class="nouveau"><a href="?page=utilisateur&no=-1"><input type="button" value="Nouvel utilisateur" class="nouveau" /></a></div>';
$menu .= '</div>';
if(!empty($NO)){
    $menu .= '<div style="text-align:center; margin-top:20px;">';
        $menu .= '<a href="'.$root_site_prod.'gestion/?page=utilisateur&no=" class="btn btn-danger">Retour liste</a>';
    $menu .= '</div>';
}
else {
    $menu .= '<div>';
        $menu .= '<div class="bloc_filtre_titre">Filtres</div>';
        $menu .= '<div class="bloc_filtre_content">';
            $menu .= '<div>';
            $menu .= '<input type="text" class="form-control" id="filtre_email" placeholder="Email" value="'.((isset($_GET['mail'])) ? $_GET['mail'] : "").'" />';
            $menu .= '</div>';
            $menu .= '<div><input type="text" class="form-control" id="filtre_ville" placeholder="Ville" value="'.((isset($_GET['ville'])) ? $_GET['ville'] : "").'" /></div>';
            $menu .= '<div style="text-align:center;"><a class="btn btn-success" id="btn_filtre_user">Rechercher</a></div>';
        $menu .= '</div>';
        $menu .= '<div class="bloc_filtre_titre">Tri</div>';
        $menu .= '<div class="bloc_filtre_content">'; 
        $menu .= '<div><select id="sel_tri_user">'; 
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
        $requete_utilisateur_edit = "SELECT U.email, U.date_inscription, U.etat, U.newsletter, U.no, V.nom_ville_maj, U.no_ville, U.droits FROM utilisateur U, villes V WHERE V.id = U.no_ville AND U.no = :no";
        $tab_item = execute_requete($requete_utilisateur_edit,array(":no"=>$NO));
        $monuser = $tab_item[0];
        $news = $monuser['newsletter'];
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
            $contenu .= '<label class="col-sm-3 control-label">Lettre Info</label>';
            $contenu .= '<div class="col-sm-4">';
                $contenu .= '<select id="sel_user_lettre">';
                    $contenu .= '<option value="0" '.(($news == 0) ? "selected=selected" : "").'>Non Inscrit</option>';
                    $contenu .= '<option value="1" '.(($news == 1) ? "selected=selected" : "").'>Inscrit</option>';
                $contenu .= '</select>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-3 control-label">Droits</label>';
            $contenu .= '<div class="col-sm-4">';
                $contenu .= '<select id="sel_user_droits">';
                    $contenu .= '<option value="U" '.(($droit == 0) ? "selected=selected" : "").'>Utilisateur</option>';
                    $contenu .= '<option value="E" '.(($droit == 1) ? "selected=selected" : "").'>Editeur</option>';
                    $contenu .= '<option value="A" '.(($droit == 2) ? "selected=selected" : "").'>Administrateur</option>';
                $contenu .= '</select>';
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
                $contenu .= '<button type="submit" id="btn_update_user" class="btn btn-primary" id="">Enregistrer les modifcations</button>';
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
            $contenu .= '<label class="col-sm-3 control-label">Lettre Info</label>';
            $contenu .= '<div class="col-sm-4">';
                $contenu .= '<select id="sel_user_lettre">';
                    $contenu .= '<option value="0">Non Inscrit</option>';
                    $contenu .= '<option value="1">Inscrit</option>';
                $contenu .= '</select>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-3 control-label">Droits</label>';
            $contenu .= '<div class="col-sm-4">';
                $contenu .= '<select id="sel_user_droits">';
                    $contenu .= '<option value="U">Utilisateur</option>';
                    $contenu .= '<option value="E">Editeur</option>';
                    $contenu .= '<option value="A">Administrateur</option>';
                $contenu .= '</select>';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-3 control-label">Ville</label>';
            $contenu .= '<div class="col-sm-4">';
                $contenu .= '<input type="text" class="recherche_ville" id="user_ville" placeholder="Code postal - Ville"><input type="hidden" name="BDDno_ville" id="BDDno_ville" value="'.$no_ville.'" /><div id="recherche_ville_liste"><div></div></div>';
                
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-3 control-label">Mot de passe</label>';
            $contenu .= '<div class="col-sm-4">';
                $contenu .= '<input type="password" class="form-control" id="user_password" placeholder="">';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group">';
            $contenu .= '<label class="col-sm-3 control-label">Confirmation Mot de passe</label>';
            $contenu .= '<div class="col-sm-4">';
                $contenu .= '<input type="password" class="form-control" id="user_password2" placeholder="">';
            $contenu .= '</div>';
        $contenu .= '</div>';
        $contenu .= '<div class="form-group" style="text-align:center;">';
            $contenu .= '<div class="col-sm-8">';
                $contenu .= '<button type="submit" id="btn_add_user" class="btn btn-primary" id="">Enregistrer l\'utilisateur</button>';
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

    $requete_utilisateur_liste = "SELECT U.email, U.date_inscription, U.etat, U.newsletter, U.no, V.nom_ville_maj, U.droits FROM utilisateur U, villes V, communautecommune_ville T, communautecommune C WHERE V.id = U.no_ville AND U.no_ville = T.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t ";
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
    
    $requete_utilisateur_count = "SELECT COUNT(U.no) as nb FROM utilisateur U, villes V, communautecommune_ville T, communautecommune C WHERE V.id = U.no_ville AND U.no_ville = T.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t ";
    $count_user = execute_requete($requete_utilisateur_count,array(":t"=>$territoire));
    $nb_user = $count_user[0]["nb"];
    
    $requete_utilisateur_count2 = "SELECT COUNT(U.no) as nb FROM utilisateur U, villes V, communautecommune_ville T, communautecommune C WHERE V.id = U.no_ville AND U.no_ville = T.no_ville AND T.no_communautecommune = C.no AND U.newsletter = 1 AND C.territoires_id = :t ";
    $count_user2 = execute_requete($requete_utilisateur_count2,array(":t"=>$territoire));
    $nb_user2 = $count_user2[0]["nb"];

    $contenu = '<div class="bloc">'; 
    $contenu .= "<div style='text-align:center;'>Nombre total d'utilisateurs : ".$nb_user." (".$nb_user2." recevant la newsletter)</div>";
    $contenu .= '<div>';
        $contenu .= '<table>';
            $contenu .= '<tr class="titre">';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Email</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Ville</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Droit</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Lettre</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Inscription</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Agenda</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Annonces</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Structure</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Forum</td>';
                $contenu .= '<td class="action"></td>';
//                        $contenu .= '<td>Agenda</td>';
            $contenu .= '</tr>';
        $i = 0;
        foreach ($tab_item as $k => $v) {
            $req_count_evt = "SELECT COUNT(*) AS nb_evts FROM evenement E WHERE no_utilisateur_creation = :no";
            $count_evt = execute_requete($req_count_evt,array(":no" => $v['no']));
	    $nb_evts = $count_evt[0]["nb_evts"];
            
            $req_count_ann = "SELECT COUNT(*) AS nb_ann FROM petiteannonce E WHERE no_utilisateur_creation = :no";
            $count_ann = execute_requete($req_count_ann,array(":no" => $v['no']));
	    $nb_ann = $count_ann[0]["nb_ann"];
            
            $req_count_str = "SELECT COUNT(*) AS nb_str FROM structure E WHERE no_utilisateur_creation = :no";
            $count_str = execute_requete($req_count_str,array(":no" => $v['no']));
	    $nb_str = $count_str[0]["nb_str"];
            
            $req_count_for = "SELECT COUNT(*) AS nb_for FROM forum E WHERE no_utilisateur_creation = :no";
            $count_for = execute_requete($req_count_for,array(":no" => $v['no']));
	    $nb_for = $count_for[0]["nb_for"];
            
            $totalcrea = $nb_evts + $nb_ann + $nb_str + $nb_for;
            
            switch ($v['droits']) {
                case 'A' : $droit = 'Administrateur'; break;
                case 'E' : $droit = 'Editeur'; break;
                default: $droit = ''; break;
            }
            
            $inscription = substr($v['date_inscription'], 8,2).'/'.substr($v['date_inscription'], 5, 2).'/'.substr($v['date_inscription'], 0, 4);
            $contenu .= '<tr class="'.(($i%2!=0)?"impaire":"paire").'" >';
                $contenu .= "<td><a href='mailto:".$v['email']."'>".$v['email']."</a></td>";
                $contenu .= "<td>".$v['nom_ville_maj']."</td>";
                $contenu .= "<td>".$droit."</td>";
                $contenu .= "<td style='text-align:center;'>".(($v['newsletter'] == 1) ? 'Oui' : '')."</td>";
                $contenu .= "<td style='text-align:center;'>".$inscription."</td>";
                $contenu .= "<td style='text-align:center;'>".(($nb_evts > 0) ? $nb_evts : '')."</td>";
                $contenu .= "<td style='text-align:center;'>".(($nb_ann > 0) ? $nb_ann : '')."</td>";
                $contenu .= "<td style='text-align:center;'>".(($nb_str > 0) ? $nb_str : '')."</td>";
                $contenu .= "<td style='text-align:center;'>".(($nb_for > 0) ? $nb_for : '')."</td>";
                $contenu .= '<td class="action_utilisateur">';
                    $contenu .= '<input id="'.$v['no'].'" type="button" name="btn_active_utilisateur" data-etat="'.$v["etat"].'" data-ref="'.$v['no'].'" class="activer'.(($v["etat"]) ? " actif" : "").'" value="" />';
                    $contenu .= '<a href="?page='.$PAGE.'&no='.$v['no'].'"><input type="button" class="editer" value="" /></a>';
                    $contenu .= '<input type="button" name="btn_del_utilsateur" data-ref="'.$v['no'].'" data-possible="'.(($totalcrea > 0) ? "0" : "1").'" class="etiquette_suppression2" value="" />';
                $contenu .= '</td>';
            $contenu .= "</tr>";
            $i++;
        }

        $contenu .= '</table>';
        
        // pagination 
        $contenu .= '<div style="text-align:center;">';
        $contenu .= '<nav><ul class="pagination">'; 
        $contenu .= '<li class="'.(($numpage == 1) ? "disabled" : "").'"><a href="'.$root_site_prod.'gestion/?page=utilisateur&no=&num='.($numpage - 1).'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        $contenu .= '<li class="'.((count($tab_item) < 100) ? "disabled" : "").'"><a href="'.$root_site_prod.'gestion/?page=utilisateur&no=&num='.($numpage + 1).'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        $contenu .= '</ul></nav>';
        $contenu .= '</div>';
        
$contenu .= '</div></div>';
}

$page = array("menu"=>$menu,"contenu"=>$contenu);
?>
