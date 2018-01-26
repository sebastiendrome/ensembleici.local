<?php
if (isset($_GET['num'])) {
    $borneinf = ($_GET['num'] - 1) * 100;
    $numpage = $_GET['num'];
}
else {
    $borneinf = 0;
    $numpage = 1;
}
$limit = $borneinf.', 100';


if(!empty($NO)){
    $menu = '<div style="text-align:center; margin-top:20px;">';
        $menu .= '<a href="'.$root_site_prod.'gestion/?page=lettre-information&no=" class="btn btn-danger">Retour liste</a>';
    $menu .= '</div>';
}
else {
    $menu = '<div class="nouveau_suppression">';
        $menu .= '<div class="nouveau"><a href="?page=lettre-information&no=-1"><input type="button" value="Créer une lettre" class="nouveau" /></a></div>';
$menu .= '</div>';
}

if(!empty($NO)){
    if($NO != -1){
        // recherche des infos de la lettre
        $requete_lettre_infos = "SELECT L.no, L.date_debut, L.objet, L.date_creation, L.date_modification, L.pdf_agenda, L.pdf_annonces FROM lettreinfo L WHERE L.no = :no";
        $tab_lettre = execute_requete($requete_lettre_infos,array(":no"=>$NO));
        $malettre = $tab_lettre[0];

    }
//    else {
    if (($_GET['bloc'] != 'confirm') && ($_GET['bloc'] != 'fin')) {
        $menu .= "<div style='margin-left:15px; margin-top:20px; width:90%; font-size:1.2em; display:none;' id='li_menu_add'>";
        $menu .= "<div style='height:30px; border-bottom: 1px solid #aaaaaa'><a name='a_li' data-ref='generalites' style='cursor:pointer;'>Généralités</a></div>";
        $menu .= "<div style='height:30px; border-bottom: 1px solid #aaaaaa'><a name='a_li' data-ref='pdf' style='cursor:pointer;'>PDF</a></div>";
        $menu .= "<div style='height:30px; border-bottom: 1px solid #aaaaaa'><a name='a_li' data-ref='editorial' style='cursor:pointer;'>Editorial</a></div>";
        $menu .= "<div style='height:30px; border-bottom: 1px solid #aaaaaa'><a name='a_li' data-ref='agenda' style='cursor:pointer;'>Agenda</a></div>";
        $menu .= "<div style='height:30px; border-bottom: 1px solid #aaaaaa'><a name='a_li' data-ref='annonces' style='cursor:pointer;'>Annonces</a></div>";
        $menu .= "<div style='height:30px; border-bottom: 1px solid #aaaaaa'><a name='a_li' data-ref='structures' style='cursor:pointer;'>Structures</a></div>";
        $menu .= "<div style='height:30px; border-bottom: 1px solid #aaaaaa'><a name='a_li' data-ref='partenaires' style='cursor:pointer;'>Partenaires</a></div>";
        $menu .= "<div style='height:30px; border-bottom: 1px solid #aaaaaa'><a name='a_li' data-ref='publicites' style='cursor:pointer;'>Publicités</a></div>";
        $menu .= "<div style='height:30px; border-bottom: 1px solid #aaaaaa; display:none;' id='li_menu_envoi'><a name='a_li' data-ref='envoi' style='cursor:pointer;'>Envoi de la lettre</a></div>";
        $menu .= "</div>";
    }
    
    $nb_envois_lettre = '';
    if ($_GET['bloc'] == 'confirm') {
        // calcul du nombre d'envois
        $requete_nb_newsletter = "SELECT COUNT(N.no) as nb FROM newsletter N, communautecommune_ville V, communautecommune C WHERE N.etat = 1 
            AND V.no_ville = N.no_ville AND V.no_communautecommune = C.no AND C.territoires_id = :territoire";
        $nb_newsletter = execute_requete($requete_nb_newsletter,array(":territoire" => $territoire));
        
        $requete_nb_newsletter_bis = "SELECT COUNT(N.no) as nb FROM newsletterbis N, communautecommune_ville V, communautecommune C WHERE N.etat = 1 
            AND V.no_ville = N.no_ville AND V.no_communautecommune = C.no AND C.territoires_id = :territoire";
        $nb_newsletter_bis = execute_requete($requete_nb_newsletter_bis,array(":territoire" => $territoire));
        
        $requete_nb_utilisateur = "SELECT COUNT(U.no) as nb FROM utilisateur U, communautecommune_ville V, communautecommune C WHERE U.newsletter = 1 
            AND V.no_ville = U.no_ville AND V.no_communautecommune = C.no AND C.territoires_id = :territoire";
        $nb_utilisateur = execute_requete($requete_nb_utilisateur,array(":territoire" => $territoire));
        
        $nb_envois_lettre = $nb_newsletter[0]['nb'] + $nb_newsletter_bis[0]['nb'] + $nb_utilisateur[0]['nb'];
    }
        include 'ajout_lettre_informations.php';
//    }
}
else {
    // affichage de toutes les lettres d'infos
    $requete_lettres_liste = "SELECT lettreinfo.no,lettreinfo.date_debut,lettreinfo.objet,lettreinfo.repertoire,lettreinfo.date_modification,lettreinfo.date_creation,lettreinfo_envoi.no AS no_envoi,lettreinfo_envoi.date_fin, lettreinfo_envoi.nb_envoi, repertoire FROM `lettreinfo` LEFT JOIN lettreinfo_envoi ON lettreinfo_envoi.no=lettreinfo.no_envoi WHERE lettreinfo.territoires_id = :t ORDER BY date_creation DESC LIMIT ".$limit;
    $tab_item = execute_requete($requete_lettres_liste,array(":t"=>$territoire));
    $contenu = '<div class="bloc"><div>';
        $contenu .= '<table>';
            $contenu .= '<tr class="titre">';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Numéro</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Objet</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Période</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Création</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Envoi</td>';
                $contenu .= '<td style="text-align:center; font-weight:bolder;">Nombre diffusion</td>';
                $contenu .= '<td class="action"></td>';
//                        $contenu .= '<td>Agenda</td>';
            $contenu .= '</tr>';
        $i = 0;
        foreach ($tab_item as $k => $v) {
            $creation = substr($v['date_creation'], 8,2).'/'.substr($v['date_creation'], 5, 2).'/'.substr($v['date_creation'], 0, 4);
            $debut = substr($v['date_debut'], 8,2).'/'.substr($v['date_debut'], 5, 2).'/'.substr($v['date_debut'], 0, 4);
            $time_fin = strtotime($v["date_debut"])+10*86400;
            $fin = date('d/m/Y', $time_fin);
            if ($v['date_fin'] != '') {
                $envoi = substr($v['date_fin'], 8,2).'/'.substr($v['date_fin'], 5, 2).'/'.substr($v['date_fin'], 0, 4).' '.substr($v['date_fin'], 11 ,5);
            }
            else {
                $envoi = '';
            }
            $contenu .= '<tr class="'.(($i%2!=0)?"impaire":"paire").'" >';
                $contenu .= "<td>".$v['no']."</td>";
                $contenu .= "<td><a target='_blank' href='".$v['repertoire']."'>".$v['objet']."</a></td>";
                $contenu .= "<td>Du ".$debut." au ".$fin."</td>";
                $contenu .= "<td style='text-align:center;'>".$creation."</td>";
                $contenu .= "<td style='text-align:center;'>".$envoi."</td>";
                $contenu .= "<td style='text-align:center;'>".(($v['nb_envoi'] != '') ? $v['nb_envoi'] : '')."</td>";
                $contenu .= '<td class="action_utilisateur">';
//                    $contenu .= '<input id="'.$v['no'].'" type="button" name="btn_active_utilisateur" data-etat="'.$v["etat"].'" data-ref="'.$v['no'].'" class="activer'.(($v["etat"]) ? " actif" : "").'" value="" />';
                if (($v['nb_envoi'] == "") || ($v['nb_envoi'] == 0)) {
                    $contenu .= '<a href="?page='.$PAGE.'&no='.$v['no'].'"><input type="button" class="editer" value="" /></a>';
                }
                else {
                    $contenu .= '<input type="button" class="editer" value="" name="btn_noupdate_lettre" />';
                }
                    $contenu .= '<input type="button" name="btn_del_lettre" data-ref="'.$v['no'].'" class="etiquette_suppression2" value="" />';
                $contenu .= '</td>';
            $contenu .= "</tr>";
            $i++;
        }

        $contenu .= '</table>';
        
        // pagination 
        $contenu .= '<div style="text-align:center;">';
        $contenu .= '<nav><ul class="pagination">'; 
        $contenu .= '<li class="'.(($numpage == 1) ? "disabled" : "").'"><a href="'.$root_site_prod.'gestion/?page=lettre-information&no=&num='.($numpage - 1).'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        $contenu .= '<li class="'.((count($tab_item) < 100) ? "disabled" : "").'"><a href="'.$root_site_prod.'gestion/?page=lettre-information&no=&num='.($numpage + 1).'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        $contenu .= '</ul></nav>';
        $contenu .= '</div>';
        
$contenu .= '</div></div>';
}

$page = array("menu"=>$menu,"contenu"=>$contenu);
?>
