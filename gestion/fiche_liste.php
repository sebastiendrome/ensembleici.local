<?php
if($PAGE=="evenement"){
	$libelle_titre = "Nom de l'événement";
	$libelle_item = "événement";
	$libelle_nouveau = "nouvel";
	$determinant_le = "l'";
	$est_feminin = false;
}
else if($PAGE=="editorial"){
	$libelle_titre = "Titre de l'article";
	$libelle_item = "article";
	$libelle_nouveau = "nouvel";
	$determinant_le = "l'";
	$est_feminin = false;
}
else if($PAGE=="petite-annonce"){
	$libelle_titre = "Titre de l'annonce";
	$libelle_item = "annonce";
	$libelle_nouveau = "nouvelle";
	$determinant_le = "l'";
	$est_feminin = true;
}
else if($PAGE=="structure"){
	$libelle_titre = "Nom de la structure";
	$libelle_item = "structure";
	$libelle_nouveau = "nouvelle";
	$determinant_le = "la ";
	$est_feminin = true;
}
else if($PAGE=="forum"){
	$libelle_titre = "Sujet du forum";
	$libelle_item = "sujet";
	$libelle_nouveau = "nouveau";
	$determinant_le = "le ";
	$est_feminin = false;
}


if(!empty($NO)){
    if($NO!=-1){
            $tab = extraire_fiche($PAGE,$NO);

            if(!empty($tab)){
                    //Infos générales
                    $titre = $tab["titre"];
                    $sous_titre = $tab["sous_titre"];
                    $date_creation = $tab["date_creation"];
                    $date_modification = $tab["date_modification"];
                    $no_utilisateur = $tab["no_utilisateur"];
                    if(empty($no_utilisateur))
                            $nom_utilisateur = "importation depuis Drôme Provence Baronnie";
                    else
                            $nom_utilisateur = ((!empty($tab["pseudo"]))?$tab["pseudo"].' ('.$tab["email_utilisateur"].')':$tab["email_utilisateur"]);
                    $validation = $tab["validation"];
                    $actif = (bool)$tab["etat"];
                    $nb_aime = $tab["nb_aime"];
                    $site = $tab["site"];

                    //Lieu
                    $ville = $tab["ville"];
                    $cp = $tab["cp"];
                    $no_ville = $tab["no_ville"];

                    //Description
                    $description = $tab["description"];

                    //Illustration
                    $url_image = $tab["image"];
                            //Copyright
                            $copyright = $tab["copyright"];
                            //Légende
                            $legende = $tab["legende"];


                    if($PAGE=="evenement"||$PAGE=="structure"){
                            //Lieu
                            $nom_lieu = $tab["nom_adresse"];
                            $adresse = $tab["adresse"];
                            $telephone = $tab["telephone"];
                            $telephone2 = $tab["telephone2"];
                            $email = $tab["email"];
                    }

                    if($PAGE=="evenement"){
                            //infos générales
                            $libelle_genre = $tab["libelle_genre"];
                            $no_genre = $tab["no_genre"];
                            $date_deb = $tab["date_debut"];
                            $date_fin = $tab["date_fin"];
                            $heure_deb = $tab["heure_debut"];
                            $heure_fin = $tab["heure_fin"];
                            //Description
                            $description_complementaire = $tab["description_complementaire"];
                    }
                    else if($PAGE=="editorial"){
                            //Description
                            $chapo = $tab["chapo"];
                            $notes = $tab["notes"];
                            $afficher_signature = $tab["afficher_signature"];
                            $signature = $tab["signature"];
                    }
                    else if($PAGE=="structure"){
                            $facebook = $tab["facebook"];
                            $no_statut = $tab["no_statut"];
                    }
                    else if($PAGE=="petite-annonce"){
                            $prix = $tab["prix"];
                            $monetaire = $tab["monetaire"];
                            $no_petiteannonce_type = $tab["no_petiteannonce_type"];
                            $date_fin = $tab["date_fin"]; //Sinon aujourd'hui + 61jours (en moyenne deux mois)
                            $rayonmax = $tab["rayonmax"];
                    }
                    else if($PAGE=="forum"){
                            $afficher_signature = $tab["afficher_signature"];
                            $signature = $tab["signature"];
                            $no_forum_type = $tab["no_forum_type"];
                    }
                    
                    if($PAGE=="evenement" || $PAGE=="petite-annonce" || $PAGE=="structure") {
                        // récupération du contact
                        switch ($PAGE) {
                            case 'evenement' : $matable = 'evenement'; break;
                            case 'structure' : $matable = 'structure'; break;
                            case 'petiteannonce' : $matable = 'petiteannonce'; break;
                            case 'petite-annonce' : $matable = 'petiteannonce'; break;
                            default: break;
                        }

                        $requete_select_contact = "SELECT no_contact, C.nom FROM ".$matable."_contact T LEFT JOIN contact C ON C.no = T.no_contact WHERE no_".$matable." = :no";
                        $tab_item_contact = execute_requete($requete_select_contact,array(":no" => $NO));
                        if ($tab_item_contact[0]['no_contact'] != '') {
                            // le contact existe, on cherche ses coordonnées 
                            $no_contact = $tab_item_contact[0]['no_contact'];
                            $nom_contact = $tab_item_contact[0]['nom'];
                            
                            $requete_select_contact_type = "SELECT no_contactType, valeur FROM contact_contactType WHERE no_contact = :no";
                            $tab_item_type = execute_requete($requete_select_contact_type,array(":no" => $no_contact));
                            
                            foreach ($tab_item_type as $k => $v) {
                                if ($v['no_contactType'] == 1) {
                                    $tabtel = array('06', '07');
                                    if (in_array(substr($v['valeur'], 0, 2), $tabtel)) {
                                        $mobile_contact = $v['valeur'];
                                    }
                                    else {
                                        $telephone_contact = $v['valeur'];
                                    }
                                }
                                else {
                                    if ($v['no_contactType'] == 2) {
                                        $email_contact = $v['valeur'];
                                    }
                                }
                            }
                        }
                    }

                    //On appelle maintenant extraire_fiche
                    $contenu = '<div class="bloc entete">';
                            $contenu .= '<div>';
                                    $contenu .= '<div class="infos">';
                                            $contenu .= 'Numéro : '.$NO;
                                            $contenu .= '<br/>';
                                            $contenu .= 'Créé le '.$date_creation.' par '.$nom_utilisateur;
                                    $contenu .= '</div>';

                                    $contenu .= '<div class="etat_validation'.(($actif)?' actif':'').'">';
//                                            $contenu .= '&Eacute;tat: <span>'.(($actif)?'Actif ':'Non actif ').'</span><span class="lien" onclick="activer_desactiver('.$NO.',\''.$PAGE.'\',this)">'.(($actif)?'Désactiver ':'Activer ').'</span>';
                                            $contenu .= '&Eacute;tat: <span>'.(($actif)?'Actif ':'Non actif ').'</span>';
                                            $contenu .= '<br/>';
                                            if($validation==1) //VALIDE
                                                    $contenu .= '<span class="voyant valide">Validé</span>';
                                            else{
                                                    if($tab_item[$i]["source_nom"]!=""&&$tab_item[$i]["no_utilisateur_creation"]==0){ //IMPORTE
                                                            $contenu .= '<span class="voyant importation">importé, non validé</span>';
                                                    }
                                                    else{
                                                            if($validation==2){
                                                                    $contenu .= '<span class="voyant modification">Modifié, non validé</span>';
                                                            }
                                                            else{
                                                                    $contenu .= '<span class="voyant">Non validé</span>';
                                                            }
                                                    }
//                                                    if($_SESSION["droit"]["no"]==1)
//                                                            $contenu .= ' <span class="lien">Valider</span>';
                                                    $contenu .= '<div>'.(($validation==2&&$_SESSION["droit"]["no"]==1)?'<input type="button" value="afficher les versions" />':'').'</div>';
                                            }
                                            //$contenu .= (($validation==1)?'Validé':((($validation==2)?'modifié, non validé':'Non validé').(($_SESSION["droit"]["no"]==1)?' <span class="lien">Valider</span>':'')));
                                            //$contenu .= '<div>'.(($validation==2&&$_SESSION["droit"]["no"]==1)?'<input type="button" value="afficher les versions" />':'').'</div>';
                                            //$contenu .= '<span class="voyant'.(($tab_item[$i]["validation"]>0)?(($tab_item[$i]["validation"]>1)?' modification':' valide'):(($tab_item[$i]["source_nom"]!=""&&$tab_item[$i]["no_utilisateur_creation"]==0)?' importation':'')).'">Pour voir</span>';

                                    $contenu .= '</div>';
                                    //$contenu .= 'Dernière modifications apportées le '.$date_modification;
                            $contenu .= '</div>';
                    $contenu .= '</div>';
            }
            else{
                    $NO = -1;
                    $contenu = '<div class="bloc entete">';
                            $contenu .= '<div>';
                                    $contenu .= '<div class="attention">';
                                            $contenu .= 'La fiche demandé n\'existe pas<br />Vous pouvez <a href="?type=evenement&no=">Revenir à la liste des '.$libelle_item.'s</a> ou en enregistrer un nouveau en complétant le formulaire ci-dessous.';
                                    $contenu .= '</div>';
                            $contenu .= '</div>';
                    $contenu .= '</div>';
            }
    }
    else{
            $contenu = '<div class="bloc entete">';
                    $contenu .= '<div>';
                            $contenu .= '<div class="infos">';
                                    $contenu .= 'Remplissez le formulaire ci-dessous.<br />L\'étoile « * » signifie que le champ est obligatoire.';
                            $contenu .= '</div>';
                    $contenu .= '</div>';
            $contenu .= '</div>';
    }

    $menu .= '<a class="retour_liste" href="?page='.$PAGE.'&no=">';
            $menu .= '&larr; Retour à la liste';
    $menu .= '</a>';
//    $menu .= '<div class="option_modification">';
//            if(!$actif){
//                    $menu .= '<input type="button" value="Ajouter '.$determinant_le.$libelle_item.'" class="nouveau" onclick="ajouter_modifier()" />';
//                    $menu .= '<input type="button" value="Enregistrer le brouillon" class="enregistrer" onclick="enregistrer_brouillon()" />';
//            }
//            else
//                    $menu .= '<input type="button" value="Modifier" class="enregistrer" onclick="ajouter_modifier()" />';
//            $menu .= '<a href="http://www.ensembleici.fr/00_dev_sam/'.url_rewrite($ville).'.'.$no_ville.'.'.$PAGE.'.'.url_rewrite($titre).'.'.$NO.'.html" target="_blank"><input type="button" value="Voir" class="voirFiche" /></a>';
//            if($PAGE=="evenement")
//                    $menu .= '<input type="button" value="Créer une copie" class="copier" />';
//            if($_SESSION["droit"]["no"]==1)
//                    $menu .= '<input type="button" value="Supprimer" class="suppression" />';
//    $menu .= '</div>';

    /******
            On complète maintenant le menu en fonction du type
            **/

    $menu .= '<div class="option_modification">';
            $menu .= '<a class="item_sous_menu" name="item_sous_menu_admin" data-ref="generalites" style="cursor:pointer;">Généralités</a>';
            $menu .= '<a class="item_sous_menu" name="item_sous_menu_admin" data-ref="tags" style="cursor:pointer;">Tags</a>';
            $menu .= '<a class="item_sous_menu" name="item_sous_menu_admin" data-ref="lieu" style="cursor:pointer;">Lieu</a>';
            $menu .= '<a class="item_sous_menu" name="item_sous_menu_admin" data-ref="contact" style="cursor:pointer;">Contact</a>';
            $menu .= '<a class="item_sous_menu" name="item_sous_menu_admin" data-ref="description" style="cursor:pointer;">Déscription</a>';
            $menu .= '<a class="item_sous_menu" name="item_sous_menu_admin" data-ref="illustration" style="cursor:pointer;">Illustration</a>';
            $menu .= '<a class="item_sous_menu" name="item_sous_menu_admin" data-ref="liaisons" style="cursor:pointer;">Liaisons</a>';
    $menu .= '</div>';

    $contenu .= '<div id="contenu_fiche">';
    include "formulaires/_formulaire_generalites.php";
    include "formulaires/_formulaire_description.php";
    include "formulaires/_formulaire_tags.php";

    include "formulaires/_formulaire_lieu.php";
    if($PAGE=="evenement"||$PAGE=="petite-annonce"||$PAGE=="structure")
    include "formulaires/_formulaire_contact.php";
    else
    include "formulaires/_formulaire_auteur.php";

    

    include "formulaires/_formulaire_illustration.php";
    if($PAGE=="evenement"||$PAGE=="petite-annonce"||$PAGE=="structure") {
        include "formulaires/_formulaire_liaisons.php";
    }
    $contenu .= '</div>';
    $contenu .= '<div class="form-group" style="text-align:center;">';
        $contenu .= '<div class="col-sm-8">';
        if($NO != -1){
//            $contenu .= '<button type="submit" id="btn_update_fiche" data-page="'.$PAGE.'" data-ref="'.$NO.'" class="btn btn-primary" id="">Modifier et valider la fiche</button>';
            $contenu .= '<a id="btn_update_fiche" data-page="'.$PAGE.'" data-ref="'.$NO.'" class="btn btn-primary">Modifier et valider la fiche</a>';
            if ($validation != 1) {
//                $contenu .= '<button type="submit" id="btn_validate_fiche" data-page="'.$PAGE.'" data-ref="'.$NO.'" class="btn btn-success" id="">Valider sans modification</button>';
                $contenu .= '<a id="btn_validate_fiche" data-page="'.$PAGE.'" data-ref="'.$NO.'" class="btn btn-success">Valider sans modification</a>';
            }
        }
        else {
//            $contenu .= '<button type="submit" id="btn_add_fiche" data-page="'.$PAGE.'" class="btn btn-primary" id="">Enregistrer la fiche</button>';
            $contenu .= '<a id="btn_add_fiche" data-page="'.$PAGE.'" class="btn btn-primary">Enregistrer la fiche</a>';
        }
        $contenu .= '</div>';
    $contenu .= '</div></br></br>';
}
else{
    if (isset($_GET['num'])) {
        $numpage = $_GET['num'];
    }
    else {
        $numpage = 1;
    }
    
    $parametres = array("admin"=>true);
    if(!empty($UTILISATEUR))
            $parametres["utilisateur"] = $UTILISATEUR;
    else if(!empty($VILLE))
            $parametres["ville"] = $VILLE;
    else if(!empty($CP))
            $parametres["cp"] = $CP;

    if(empty($TRI)){
            $TRI = "date_creation";
            $ORDRE = "DESC";
    }
    $parametres["tri"] = $TRI;
    

    if(!empty($ORDRE))
            $parametres["ordre"] = $ORDRE;

    else if($PAGE=="structure"){
            $libelle_titre = "Nom de la structure";
    }
    $parametres["bo"] = 1;


    if (isset($_GET['ht'])) {
        $parametres["ht"] = 1;
        $tab_item = extraire_liste($PAGE,30,$numpage,$parametres);
    }
    else {
        $parametres["territoire"] = $_SESSION["utilisateur"]["territoire"];
        $tab_item = extraire_liste($PAGE,30,$numpage,$parametres);
    }

    $mareq = $tab_item['requete'];
    $nb_item_ville = $tab_item["count_ville"];
    $nb_item_proche = $tab_item["count_proche"];
    $nb_item_total = $tab_item["count_total"];
    $nb_page = $tab_item["count_page"];
    $lescond = $tab_item['cond'];
    $tab_item = $tab_item["liste"];
            


    //RECHERCHE
    $menu = '<div class="recherche">';
            $menu .= '<input type="text" value="Rechercher" title="Rechercher" class="recherche vide" /><input type="button" class="recherche" />';
    $menu .= '</div>';
    //RESULTAT DE RECHERCHE
    $menu .= '<div id="zone_recherche" class="vide">';
    $menu .= '</div>';
    //FILTRES
    $menu .= '<div id="les_filtres">';
        /*if($EXPIRE>0){
                $menu .= '<div class="filtre_recherche actif">';
                        $menu .= '<div class="libelle">';
                                $menu .= '<a href="?page='.$PAGE.'&no_ville='.$VILLE.'"><img src="../img/img_colorize.php?uri=non_actif.png&c=255,255,255" /></a>';
                                $menu .= '&Eacute;v&eacute;nements expir&eacute;s';
                        $menu .= '</div>';
                $menu .= '</div>';
        }*/
        if(!empty($VILLE)||!empty($NOM_UTILISATEUR)||!empty($CP)){
                $menu .= '<div class="filtre_recherche actif" id="filtre_cpVilleUtilisateur">';
                        $menu .= '<div class="libelle">';
                                $menu .= '<a href="?no_ville=&user=&cp="><img src="../img/img_colorize.php?uri=non_actif.png&c=255,255,255" /></a>';
                                $menu .= ((!empty($VILLE))?$LIBELLE_VILLE:((!empty($NOM_UTILISATEUR))?$NOM_UTILISATEUR:$CP));
                        $menu .= '</div>';
                $menu .= '</div>';
        }/*
        if($EXPIRE==0)
                $menu .= '<div class="lien_filtre"><a href="?page='.$PAGE.'&no_ville='.$VILLE.'&expire=1">Afficher aussi les &eacute;v&eacute;nements expir&eacute;s</a></div>';
    */
    $menu .= '</div>';
    //BOUTON DE SUPPRESSIONS ET CRÉATIONS
    $menu .= '<div class="nouveau_suppression">';
            $menu .= '<div class="nouveau"><a href="?page='.$PAGE.'&no=-1"><input type="button" value="'.$libelle_nouveau.' '.$libelle_item.'" class="nouveau" /></a></div>';
//            if($_SESSION["droit"]["no"]==1)
//                    $menu .= '<div class="suppression"><input type="button" value="Mode suppression" class="suppression" onclick="mode_suppression(true);" /></div>';
    $menu .= '</div>';
    $menu .= '<div id="legende">';
            $menu .= "<div>legende&nbsp;:</div>";
            $menu .= '<table>';
                    $menu .= '<tr><td><div class="voyant"></div></td><td>Non validé'.(($est_feminin)?'e':'').'</td></tr>';
//                    $menu .= '<tr><td><div class="voyant modification"></div></td><td>Modifié'.(($est_feminin)?'e':'').', non validé'.(($est_feminin)?'e':'').'</td></tr>';
                    $menu .= '<tr><td><div class="voyant valide"></div></td><td>Validé'.(($est_feminin)?'e':'').'</td></tr>';
                    if($PAGE=="evenement")
                            $menu .= '<tr><td><div class="voyant importation"></div></td><td>Importé, non validé</td></tr>';
//                    if($PAGE=="evenement"||$PAGE=="petite-annonce")
//                            $menu .= '<tr><td><img src="../img/img_colorize.php?uri=ico_expire.png&c=363E43" /></td><td>'.$libelle_item.' expriré'.((!$est_feminin)?'':'e').'</td></tr>';
                    $menu .= '<tr class="non_actif"><td colspan="2">'.$libelle_item.' non acti'.((!$est_feminin)?'f':'ve').' '.(($PAGE=="evenement"||$PAGE=="petite-annonce")?'(expiré'.((!$est_feminin)?'':'e').' ou désactivé'.((!$est_feminin)?'':'e').')':'(désactivé'.((!$est_feminin)?'':'e').')').'</td></tr>';
            $menu .= '</table>';
    $menu .= '</div>';
    $menu .= '<div id="filtre_suppression"><div>Séléctionnez les événements à supprimer, puis cliquez sur le bouton ci-dessous :<br /><input type="button" value="Supprimer" class="suppression" /><br />ou<br /><input type="button" value="annuler" onclick="mode_suppression(false);" /></div></div>';


    $contenu = '<div class="bloc">'; 
    if (!isset($_GET['ht'])) {
        $contenu .= '<div style="text-align:right;"><a style="cursor:pointer;" id="link_hors_territoire">Hors Territoire</a></div>';
    }
    else {
        $contenu .= '<div style="text-align:right;"><a href="?page='.$PAGE.'&no=">Mon territoire</a></div>';
    }
    $contenu .= '<div>';
        $contenu .= '<table>';
                $contenu .= '<tr class="titre">';
                        $contenu .= '<td></td>';
//                        $contenu .= '<td><a href="?tri=titre&ordre='.((empty($ORDRE)&&$TRI=="titre")?'DESC':'').'"'.(($TRI=="titre")?(' class="tri_courant'.((!empty($ORDRE))?' desc"':'"')):'').'>'.$libelle_titre.'</a></td>';
//                        $contenu .= '<td><a href="?tri=ville&ordre='.((empty($ORDRE)&&$TRI=="ville")?'DESC':'').'"'.(($TRI=="ville")?(' class="tri_courant'.((!empty($ORDRE))?' desc"':'"')):'').'>Ville</a></td>';
//                        if($PAGE=="evenement"||$PAGE=="petite-annonce")
//                                $contenu .= '<td><a href="?tri=date&ordre='.((empty($ORDRE)&&$TRI=="date")?'DESC':'').'"'.(($TRI=="date")?(' class="tri_courant'.((!empty($ORDRE))?' desc"':'"')):'').'>Date de fin</a></td>';
//                        else if($PAGE=="editorial"||$PAGE=="forum")
//                                $contenu .= '<td><a href="?tri=pseudo&ordre='.((empty($ORDRE)&&$TRI=="pseudo")?'DESC':'').'"'.(($TRI=="pseudo")?(' class="tri_courant'.((!empty($ORDRE))?' desc"':'"')):'').'>Auteur</a></td>';
//                        else if($PAGE=="structure")
//                                $contenu .= '<td><a href="?tri=statut&ordre='.((empty($ORDRE)&&$TRI=="statut")?'DESC':'').'"'.(($TRI=="statut")?(' class="tri_courant'.((!empty($ORDRE))?' desc"':'"')):'').'>Statut</a></td>';
//                        $contenu .= '<td><a href="?tri=date_creation&ordre='.((empty($ORDRE)&&$TRI=="date_creation")?'DESC':'').'"'.(($TRI=="date_creation")?(' class="tri_courant'.((!empty($ORDRE))?' desc"':'"')):'').'>Date de cr&eacute;ation</a></td>';
                        $contenu .= '<td>'.$libelle_titre.'</td>';
                        $contenu .= '<td>Ville</td>';
                        if($PAGE=="evenement"||$PAGE=="petite-annonce")
                                $contenu .= '<td>Date de fin</td>';
                        else if($PAGE=="editorial"||$PAGE=="forum")
                                $contenu .= '<td>Auteur</td>';
                        else if($PAGE=="structure")
                                $contenu .= '<td>Statut</td>';
                        $contenu .= '<td>Date de cr&eacute;ation</td>';
                        $contenu .= '<td class="action_utilisateur"></td>';
                $contenu .= '</tr>';
        foreach ($tab_item as $k => $v) {
                if(!(bool)$v["etat"]||$v["expire"])
                        $ligne_active = false;
                else
                        $ligne_active = true;

                        //On ne rend la ligne modifiable que pour les administrateur ou les propriétaire de la fiche (sauf pour evenement et structure)
                        $ligne_modifiable = ($_SESSION["droit"]["no"]==1||$PAGE=="evenement"||$PAGE=="structure"||(($PAGE=="petite-annonce"||$PAGE=="editorial"||$PAGE=="forum")&&$_SESSION["utilisateur"]["no"]==$v["no_utilisateur"]));

                $contenu .= '<tr class="'.(($i%2!=0)?"impaire":"paire").(($ligne_active)?"":" non_actif").'" onclick="selectionner(this);">';
                        $contenu .= '<td><div class="voyant'.(($v["validation"]>0)?(($v["validation"]>1)?' modification':' valide'):(($v["source_nom"]!=""&&$v["no_utilisateur_creation"]==0)?' importation':'')).'"></div></td>';
                        $contenu .= '<td>';
                            $contenu .= '<a href="'.$root_site.$PAGE.'.'.url_rewrite($v["ville"]).'.'.url_rewrite($v["titre"]).'.'.$v["no_ville"].'.'.$v["no"].'.html" target="_blank">';
                            $contenu .= $v["titre"];
                            $contenu .= '</a>';
                        $contenu .= '</td>';
                        $contenu .= '<td><a href="?page='.$PAGE.'&no_ville='.$v["no_ville"].'">'.$v["ville"].'</a></td>';
                        if($PAGE=="evenement"||$PAGE=="petite-annonce")
                                $contenu .= '<td>'.$v["date_fin"].'</td>';
                        else if($PAGE=="editorial"||$PAGE=="forum")
                                $contenu .= '<td>'.$v["pseudo"].'</td>';
                        else if($PAGE=="structure")
                                $contenu .= '<td>'.$v["statut"].'</td>';
                        $contenu .= '<td>'.$v["date_creation"].'</td>';
                        $contenu .= '<td class="action">';
                                if($ligne_modifiable){
                                        $contenu .= '<input id="'.$v['no'].'" type="button" name="btn_active_fiche" data-page="'.$PAGE.'" data-etat="'.$v["etat"].'" data-ref="'.$v['no'].'" class="activer'.(($v["etat"]) ? " actif" : "").'" value="" title="'.(($v["etat"]) ? "Dépublier" : "Publier").'" />';
                                        $contenu .= '<a href="?page='.$PAGE.'&no='.$v['no'].'"><input type="button" class="editer" value="" /></a>';
                                        $contenu .= '<input type="button" name="btn_del_fiche" data-ref="'.$v['no'].'"  data-page="'.$PAGE.'" class="etiquette_suppression2" value="" />';
                                }
                        $contenu .= '</td>';
                $contenu .= '</tr>';
        }

        $contenu .= '</table>';
        
        // pagination 
        $contenu .= '<div style="text-align:center;">';
        $contenu .= '<nav><ul class="pagination">'; 
        $contenu .= '<li class="'.(($numpage == 1) ? "disabled" : "").'"><a href="'.$root_site_prod.'gestion/?page='.$PAGE.'&no=&num='.($numpage - 1).'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        $contenu .= '<li class="'.((count($tab_item) < 30) ? "disabled" : "").'"><a href="'.$root_site_prod.'gestion/?page='.$PAGE.'&no=&num='.($numpage + 1).'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        $contenu .= '</ul></nav>';
        $contenu .= '</div>';
        
        
    $contenu .= '</div></div>';
}
$page = array("menu"=>$menu,"contenu"=>$contenu);
?>
