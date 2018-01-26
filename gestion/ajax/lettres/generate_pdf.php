<?php
session_start();
//1. Initialisation de la session
include "../../../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../../../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../../../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../../../01_include/_init_var.php";

$file = '../../../js/html2pdf/html2pdf.class.php';
require($file);

$tabmois = array('01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04' => 'Avril', '05' => 'Mai', '06' => 'Juin', '07' => 'Juillet', 
    '08' => 'Août', '09' => 'Septembre', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'); 
$tabjour = array('1' => 'Lundi', '2' => 'Mardi', '3' => 'Mercredi', '4' => 'Jeudi', '5' => 'Vendredi', '6' => 'Samedi', '7' => 'Dimanche');

$return_code = '0';
$tab = array();
$no = $_POST['no'];

$req1 = "SELECT L.no, L.date_debut, L.territoires_id, T.nom as territoire FROM lettreinfo L, territoires T WHERE L.territoires_id = T.id AND L.no = ".$no;
$res1 = $connexion->prepare($req1);
$res1->execute();
$territoire_res = $res1->fetch(PDO::FETCH_ASSOC);
$territoire = $territoire_res['territoires_id'];
$nom_territoire = $territoire_res['territoire'];
$date_debut = substr($territoire_res['date_debut'], 8, 2).'/'.substr($territoire_res['date_debut'], 5, 2).'/'.substr($territoire_res['date_debut'], 0, 4);

$date = new DateTime($territoire_res['date_debut']);
$date->add(new DateInterval('P10D'));
//$date_fin = $date->format('d/m/Y');

if ($_POST['agenda'] == 1) {
    // génération du fichier pdf agenda
    if ($_POST['datefin'] != '') {
        $date_fin2 = substr($_POST['datefin'], 6, 4).'-'.substr($_POST['datefin'], 3, 2).'-'.substr($_POST['datefin'], 0, 2);
    }
    else {
        $date_fin2 = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") + 10, date("Y")));
    }
        
    $requete =  "SELECT E.no as no, E.heure_debut as heure_debut, E.heure_fin as heure_fin, G.no as nogenre, E.titre as titre, G.libelle as libelle,
            E.no_ville as no_ville, E.adresse as direccion, V.nom_ville_maj as nom_ville, CONVERT(E.description USING utf8) as description, E.nomadresse, 
            E.telephone, E.email, E.telephone2, E.site, B.nom as contact, B.no as idcontact, E.date_debut, E.date_fin  
            FROM villes V, genre G, communautecommune_ville T, communautecommune C, evenement E 
            LEFT JOIN  evenement_contact O ON O.no_evenement = E.no LEFT JOIN contact B ON  O.no_contact = B.no
            WHERE E.date_debut - INTERVAL 1 DAY  < :datefin AND E.date_fin + INTERVAL 1 DAY  > :datedebut AND E.etat = 1 AND E.validation = 1 
            AND E.no_ville = V.id AND E.no_genre = G.no AND V.id = T.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t ORDER BY E.date_debut ASC, V.nom_ville ASC";
    $results_evenements = $connexion->prepare($requete);
    $results_evenements->execute(array(':datefin' => $date_fin2, ':datedebut' => $territoire_res['date_debut'], ":t" => $territoire));
    $tab_evenements = $results_evenements->fetchAll(PDO::FETCH_ASSOC); 
    ob_start();
    ?>
    <link rel="stylesheet" type="text/css" href="../../../css/pdf.css" />
    <page backtop="40mm" backbottom="20mm" backleft='20px' backright='20px;'>
        <page_header>
            <table style="margin-top: 20px; margin-left: 20px;">
                <tr>
                    <td style="vertical-align: top; width: 447px;">
                        <table>
                            <tr>
                                <td style="vertical-align: top; width: 447px;">
                                    <img src="../../../img/logomini.png" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="text-align: right; font-weight: bold;font-size: 24px;">
                                        <span><?= $nom_territoire ?></span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="vertical-align: top; width: 250px; padding-left: 10px; color: #375d8e; text-align: center;">
                        <div style="margin-left: 10px;"><b>Retrouvez le détail de l'agenda et les petites annonces sur le site www.ensembleici.fr</b></div>
                        <div style="margin-left: 10px;">Il vous est également aisé d'ajouter des informations via les formulaires du site !</div>
                    </td>
                </tr>
            </table>
        </page_header>
        <page_footer>
            <div style="text-align: center; margin-bottom: 10px; color: #375d8e; font-size: 11px;">
                Le site Ensemble ici est un projet associatif, évolutif et collaboratif.&nbsp;
                page [[page_cu]]/[[page_nb]]
            </div>
        </page_footer>

            <div class="letter_titre">
                L'agenda du <?= $date_debut ?> au <?= substr($date_fin2, 8, 2).'/'.substr($date_fin2, 5, 2).'/'.substr($date_fin2, 0, 4) ?>
            </div>
            <?php 
                $evt_date_debut_prec = '';
                $tab_evt_long = array();
                $nb_lignes = 0;
                $tab_evt = array();
                foreach ($tab_evenements as $k => $v) {
                    $valable = 1;
                    if (in_array($v['no'], $tab_evt)) {
                        $valable = 0;
                    }
                    if ($valable == 1) {
                        if ($nb_lignes > 25) { ?>
                        </page>
                        <page pageset="old">
                        <?php
                            $nb_lignes = 0;
                        }

                        if ($v['date_debut'] < $territoire_res['date_debut']) {
    //                        $evt_date_debut = $territoire_res['date_debut'];
                            $tab_evt_long[$k] = $v;
                        }
                        else {
                            $chaine = substr(strip_tags(html_entity_decode(trim($v['description']), ENT_QUOTES, 'UTF-8')), 0, 800);
                            $chaine = str_replace(CHR(10)," ",$chaine);
                            $chaine = str_replace(CHR(9),"",$chaine);
                            $chaine = str_replace(CHR(11),"",$chaine);
                            $chaine = str_replace(CHR(12),"",$chaine);
                            $chaine = str_replace(CHR(13)," ",$chaine);
                            $chaine = str_replace('
                                ','', nl2br($chaine));
                            $chaine = str_replace("&#39;","'",$chaine);
                            $chaine = str_replace("&quot;",'"',$chaine);
                            if (strlen($v['description']) > 800) {
                                $chaine .= ' ... (lire la suite sur notre site)';
                            }
                            $evt_date_debut = $v['date_debut'];

                            if ($evt_date_debut != $evt_date_debut_prec) {
                                // on écrit la date ?>
                                <div class='letter_bloc_agenda_date'>
                                    <?php $tabdate = explode('-', date('N-j-m', mktime(0, 0, 0, substr($evt_date_debut, 5, 2), substr($evt_date_debut, 8, 2), substr($evt_date_debut, 0, 4)))); 
                                    echo $tabjour[$tabdate[0]].' '.$tabdate[1].' '.$tabmois[$tabdate[2]]; 
                                    $nb_lignes++;
                                    ?>
                                </div>
                                <?php $evt_date_debut_prec = $evt_date_debut;
                            }
                            // on écrit le titre de l'événement
                            $nb_lignes++;
                            ?>
                            <table style="margin-top: 25px">
                                <tr>
                                    <td style='width:120px;'><div class='letter_bloc_agenda_libelle'><?= $v['libelle'] ?></div></td>
                                    <td style='width:310px;'><div style='font-weight:bold; padding-top: 5px;'><?= $v['titre'] ?></div></td>
                                    <td style='width:100px;'><div style='font-weight:bold; margin-left: 10px; padding-top: 5px;'>
                                        <?= ($v['heure_debut'] != '') ? (($v['heure_fin'] != '') ? substr($v['heure_debut'], 0, 5).' - '. substr($v['heure_fin'], 0, 5) : substr($v['heure_debut'], 0, 5)) : '' ?>
                                    </div></td>
                                    <td style='width:190px;'><div class='letter_bloc_agenda_ville'><?= $v['nom_ville'] ?></div></td>
                                </tr>
                            </table>
                            <?php 
                            if ($v['date_debut'] != $v['date_fin']) {
                                // ajout d'une ligne pour date de fin
                                $chaine_fin_evt = "Date de fin de l'événement : ".substr($v['date_fin'], 8,2).'/'.substr($v['date_fin'], 5,2).'/'.substr($v['date_fin'], 0,4); ?>
                                <table style='margin-left: 125px;'>
                                    <tr><td>
                                        <?= $chaine_fin_evt ?>
                                    </td></tr>
                                </table>
                            <?php 

                            $nb_lignes++;
                            } ?>
                            <table style='margin-top:10px; padding-left: 10px; width: 720px; margin-bottom: 10px;'>
                                <tr><td>
                                <div style='width: 730px;'><?= $chaine ?></div>
                                </td></tr>
                            </table>
                            <?php
                            $estimation_lignes = intval(strlen($chaine) / 110) + 1;
                            $nb_lignes += $estimation_lignes;

                            if (($v['nomadresse'] != '') || ($v['telephone']) || ($v['email']) || ($v['site'])) {
                                $nb_lignes++;
                                $chainelieu = ''; $chainetel = ''; $chainemail = ''; $chainesite = '';
                                $first_item = 1;

                                if ($v['nomadresse'] != '') {
                                    $chainelieu = "<span>Lieu : ".$v['nomadresse'].'</span>';
                                    $first_item = 0;
                                }
                                if ($v['telephone'] != '') {
                                    if ($first_item == 1) {
                                        $chainetel = "<span>Téléphone : ".$v['telephone'].'</span>';
                                    }
                                    else {
                                        $chainetel = "<span style='margin-left:20px;'>Téléphone : ".$v['telephone'].'</span>';
                                    }
                                    $first_item = 0;
                                }
                                if ($v['email'] != '') {
                                    if ($first_item == 1) {
                                        $chainemail = "<span>Email : ".$v['email'].'</span>';
                                    }
                                    else {
                                        $chainemail = "<span style='margin-left:20px;'>Email : ".$v['email'].'</span>';
                                    }
                                    $first_item = 0;
                                }
                                if ($v['site'] != '') {
                                    if ($first_item == 1) {
                                        $chainesite = "<span>Site Internet : ".str_replace("http://", "", $v['site']).'</span>';
                                    }
                                    else {
                                        $chainesite = "<span style='margin-left:20px;'>Site Internet : ".str_replace("http://", "", $v['site']).'</span>';
                                    }
                                    $chainesite = str_replace("https://", "", $chainesite);
                                }
                                ?>
                                <table style='width: 730px; margin-left: 10px;'>
                                    <tr><td>
                                    <?= $chainelieu.$chainetel.$chainemail.$chainesite ?>
                                    </td></tr>
                                </table>
                                <?php 
                                if ($v['contact'] != '') {
                                    // on cherche les infos contacts
                                    $requete_contact =  "SELECT no_contactType, valeur FROM contact_contactType WHERE (no_contact = :no) AND no_contactType IN (1, 2) ORDER BY no_contactType";	
                                    $results_contacts = $connexion->prepare($requete_contact);
                                    $results_contacts->execute(array(':no'=> $v['idcontact']));
                                    $tab_contacts = $results_contacts->fetchAll(PDO::FETCH_ASSOC); 

                                    // affichage des infos contact ?>
                                    <table style="margin-left:10px;">
                                        <tr><td>
                                        Contact : <?= $v['contact'] ?>
                                        <?php foreach ($tab_contacts as $k1 => $v1) { ?>
                                        <span style='margin-left: 20px;'><?= $v1['valeur'] ?></span>
                                        <?php } ?>
                                        </td></tr>
                                    </table>
                                <?php
                                $nb_lignes++;
                                }
                                ?>
                                <?php
                            }
                        }
                        array_push($tab_evt, $v['no']);
                    }
                }
                $nb_lignes = 0;
                if (sizeof($tab_evt_long) != 0) { ?> 
                    </page>
                    <page pageset="old">
                            <div class="letter_titre">
                                Les événements de longue durée
                            </div>
                    <?php 
                    $matab_long = array();
                    foreach ($tab_evt_long as $k => $v) {
                        $valable = 1;
                        if (in_array($v['no'], $matab_long)) {
                            $valable = 0;
                        }
                        if ($valable == 1) {
                            if ($nb_lignes > 25) { ?>
                                </page>
                                <page pageset="old">
                            <?php
                                $nb_lignes = 0;
                            }
                            $chaine = substr(strip_tags(html_entity_decode(trim($v['description']), ENT_QUOTES, 'UTF-8')), 0, 800);
                            $chaine = str_replace(CHR(10)," ",$chaine);
                            $chaine = str_replace(CHR(9),"",$chaine);
                            $chaine = str_replace(CHR(11),"",$chaine);
                            $chaine = str_replace(CHR(12),"",$chaine);
                            $chaine = str_replace(CHR(13)," ",$chaine);
                            $chaine = str_replace('
                                ','', nl2br($chaine));
                            $chaine = str_replace("&#39;","'",$chaine);
                            $chaine = str_replace("&quot;",'"',$chaine);
                            if (strlen($v['description']) > 800) {
                                $chaine .= ' ... (lire la suite sur notre site)';
                            }
                            // on écrit le titre de l'événement 
                            $nb_lignes++;
                            ?>
                            <table style="margin-top: 25px">
                                <tr>
                                    <td style='width:120px;'><div class='letter_bloc_agenda_libelle'><?= $v['libelle'] ?></div></td>
                                    <td style='width:310px;'><div style='font-weight:bold; padding-top: 5px;'><?= $v['titre'] ?></div></td>
                                    <td style='width:100px;'><div style='font-weight:bold; margin-left: 10px; padding-top: 5px;'>
                                        <?= ($v['heure_debut'] != '') ? (($v['heure_fin'] != '') ? substr($v['heure_debut'], 0, 5).' - '. substr($v['heure_fin'], 0, 5) : substr($v['heure_debut'], 0, 5)) : '' ?>
                                    </div></td>
                                    <td style='width:190px;'><div class='letter_bloc_agenda_ville'><?= $v['nom_ville'] ?></div></td>
                                </tr>
                            </table>
                            <?php 
                                // ajout d'une ligne pour date de fin
                                $nb_lignes++;
                                $chaine_fin_evt = "Date de fin de l'événement : ".substr($v['date_fin'], 8,2).'/'.substr($v['date_fin'], 5,2).'/'.substr($v['date_fin'], 0,4); ?>
                                <table style='margin-left: 125px;'>
                                    <tr><td>
                                        <?= $chaine_fin_evt ?>
                                    </td></tr>
                                </table>
                            <table style='margin-top:10px; padding-left: 10px; width: 720px;'>
                                <tr><td>
                                <div style='width: 730px;'><?= $chaine ?></div>
                                </td></tr>
                            </table>

                            <?php
                            $estimation_lignes = intval(strlen($chaine) / 110) + 1;
                            $nb_lignes += $estimation_lignes;

                            if (($v['nomadresse'] != '') || ($v['telephone']) || ($v['email']) || ($v['site'])) {
                                $nb_lignes++;
                                $chainelieu = ''; $chainetel = ''; $chainemail = ''; $chainesite = '';

                                if ($v['nomadresse'] != '') {
                                    $chainelieu = "<span style='margin-left:20px;'>Lieu : ".$v['nomadresse'].'</span>';
                                }
                                if ($v['telephone'] != '') {
                                    $chainetel = "<span style='margin-left:20px;'>Téléphone : ".$v['telephone'].'</span>';
                                }
                                if ($v['email'] != '') {
                                    $chainemail = "<span style='margin-left:20px;'>Email : ".$v['email'].'</span>';
                                }
                                if ($v['site'] != '') {
                                    $chainesite = "<span style='margin-left:20px;'>Site Internet : ".str_replace("http://", "", $v['site']).'</span>';
                                    $chainesite = str_replace("https://", "", $chainesite);
                                }
                                ?>
                                <table style='margin-top:10px; width: 730px;'>
                                    <tr><td>
                                    <?= $chainelieu.$chainetel.$chainemail.$chainesite ?>
                                    </td></tr>
                                </table>
                                <?php 
                                if ($v['contact'] != '') {
                                    $nb_lignes++;
                                    // on cherche les infos contacts
                                    $requete_contact =  "SELECT no_contactType, valeur FROM contact_contactType WHERE (no_contact = :no) AND no_contactType IN (1, 2) ORDER BY no_contactType";	
                                    $results_contacts = $connexion->prepare($requete_contact);
                                    $results_contacts->execute(array(':no'=> $v['idcontact']));
                                    $tab_contacts = $results_contacts->fetchAll(PDO::FETCH_ASSOC); 

                                    // affichage des infos contact ?>
                                    <table style='margin-top:10px; margin-left: 20px;'>
                                        <tr><td>
                                        Contact : <?= $v['contact'] ?>
                                        <?php foreach ($tab_contacts as $k1 => $v1) { ?>
                                        <span style='margin-left: 20px;'><?= $v1['valeur'] ?></span>
                                        <?php } ?>
                                        </td></tr>
                                    </table>
                                <?php
                                }
                                ?>
                                <?php
                            }  
                            array_push($matab_long, $v['no']);
                        }
                    }
                }
            ?>
            
    </page>
    <?php
    $content = ob_get_clean();

    try{
        $pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
        $pdf->pdf->SetDisplayMode('fullpage');
        $pdf->writeHTML($content);

        $file = 'agenda_l'.$no.'_t'.$territoire.'.pdf';
        $nomfile = '../../../02_medias/14_lettreinfo_pdf_agenda/'.$file;
        $pdf->Output($nomfile,'F');
        
        $tab['agenda'] = $file;

    }catch(HTML2PDF_exception $e){
            die($e);
    }
}

if ($_POST['annonces'] == 1) {
    $date_limite = date('Y-m-d', mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));
    $requete =  "SELECT E.no as pid, E.titre as titre, E.monetaire, E.prix, E.no_ville as no_ville, V.nom_ville_maj as nom_ville, 
                CONVERT(E.description USING utf8) as description, E.site, Y.libelle as type_libelle, Y.no as type, E.date_creation, B.nom as contact, B.no as idcontact     
                FROM villes V, communautecommune_ville T, petiteannonce_type Y,communautecommune C, petiteannonce E 
                LEFT JOIN  petiteannonce_contact O ON O.no_petiteannonce = E.no LEFT JOIN contact B ON  O.no_contact = B.no 
                WHERE E.etat = 1 AND E.validation = 1 AND E.apparition_lettre < 2 AND E.no_ville = V.id AND E.date_fin > :datejour AND 
                V.id = T.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t AND E.date_creation > :datelim AND E.no_petiteannonce_type = Y.no ORDER BY Y.no ASC, V.nom_ville_maj ASC";
    $results_annonces = $connexion->prepare($requete);
    $results_annonces->execute(array(':datejour' => date('Y-m-d'), ":t" => $territoire, ":datelim" => $date_limite));
    $tab_annonces = $results_annonces->fetchAll(PDO::FETCH_ASSOC);
    
    ob_start();
    ?>
    <link rel="stylesheet" type="text/css" href="../../../css/pdf.css" />
    <page backtop="40mm" backbottom="10mm" backleft='20px' backright='20px;'>
        <page_header>
            <table style="margin-top: 20px; margin-left: 20px;">
                <tr>
                    <td style="vertical-align: top; width: 447px;">
                        <table>
                            <tr>
                                <td style="vertical-align: top; width: 447px;">
                                    <img src="../../../img/logomini.png" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="text-align: right; font-weight: bold;font-size: 24px;">
                                        <span><?= $nom_territoire ?></span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="vertical-align: top; width: 250px; padding-left: 10px; color: #375d8e; text-align: center;">
                        <div style="margin-left: 10px;"><b>Retrouvez le détail de l'agenda et les petites annonces sur le site www.ensembleici.fr</b></div>
                        <div style="margin-left: 10px;">Il vous est également aisé d'ajouter des informations via les formulaires du site !</div>
                    </td>
                </tr>
            </table>
        </page_header>
        <page_footer>
            <div style="text-align: center; margin-bottom: 10px; color: #375d8e; font-size: 11px;">
                page [[page_cu]]/[[page_nb]]
            </div>
        </page_footer>

            <div class="letter_titre">
                Les dernières annonces en date du <?= $date_debut ?>
            </div>
            <?php
                $type_libelle = ''; $nom_ville = ''; $i = 0;
                $matab_ann = array();
                foreach ($tab_annonces as $k => $v) {
                    $valable = 1;
                    if (in_array($v['pid'], $matab_ann)) {
                        $valable = 0;
                    }
                    if ($valable == 1) {
                        if ($v['type_libelle'] != $type_libelle) { 
                            switch ($v['type']) {
                                case '1' : $sousclasse = 'letter_bgcolor_annonce_offre'; break;
                                case '2' : $sousclasse = 'letter_bgcolor_annonce_demande'; break;
                                case '3' : $sousclasse = 'letter_bgcolor_annonce_initiative'; break;
                                default: $sousclasse = 'letter_bgcolor_annonce_offre'; break;
                            }
                            if ($type_libelle != '') { ?>
                                </page>
                                <page pageset="old">
                            <?php } ?>
                            <table><tr><td><div class='letter_sstitre_annonce <?= $sousclasse ?>'><?= $v['type_libelle'] ?></div></td></tr></table>

                            <?php
                            // on écrit l'en-tête
                            $type_libelle = $v['type_libelle']; 
                            $nom_ville = '';
                        }

                        // on écrit les annonces
                        $chaine = substr(strip_tags(html_entity_decode(trim($v['description']), ENT_QUOTES, 'UTF-8')), 0, 900);
                        $chaine = str_replace(CHR(10)," ",$chaine);
                        $chaine = str_replace(CHR(9),"",$chaine);
                        $chaine = str_replace(CHR(11),"",$chaine);
                        $chaine = str_replace(CHR(12),"",$chaine);
                        $chaine = str_replace(CHR(13)," ",$chaine);
                        $chaine = str_replace('
                            ','', nl2br($chaine));
                        $chaine = str_replace("&#39;","'",$chaine);
                        $chaine = str_replace("&quot;",'"',$chaine);
                        if (strlen($v['description']) > 900) {
                            $chaine .= ' ... (lire la suite sur notre site)';
                        }
                        if ($i%2 == 1) {
                            $class_color = 'background-color: #eeeeee;';
                        }
                        else {
                            $class_color = 'background-color: #ffffff;';
                        }
                        ?>
                        <table width="740" style="width: 740px;margin-top: 20px;<?= $class_color ?>">
                            <tr><td>
                            <table>
                                <tr>
                                    <td style='width:190px;'>
                                        <?php
                                        if ($nom_ville != $v['nom_ville']) {
                                            // on écrit le nom de la ville ?>
                                        <div class='letter_bloc_annonces_ville <?= $sousclasse ?>'><?= $v['nom_ville'] ?></div>
                                            <?php $nom_ville = $v['nom_ville'];
                                        }

                                        ?>
                                    </td>
                                    <td style='width:400px;'>
                                        <div style='font-weight: bold; margin-left: 10px;'><?= $v['titre'] ?></div>
                                    </td>
                                    <td style='width:140px;'>
                                        <div style='margin-left: 10px; font-weight: bold;'>Publié : <?= substr($v['date_creation'], 8, 2).'/'.substr($v['date_creation'], 5, 2).'/'.substr($v['date_creation'], 0, 4) ?></div>
                                    </td>
                                </tr>
                            </table>

                            <table class='letter_description_annonces'>
                                <tr><td>
                                    <div style="width:730px;"><?= $chaine ?></div>
                                </td></tr>
                            </table>
                            <?php 
                            if ($v['contact'] != '') {
                                // on cherche les infos contacts
                                $requete_contact =  "SELECT no_contactType, valeur FROM contact_contactType WHERE (no_contact = :no) AND no_contactType IN (1, 2) ORDER BY no_contactType";	
                                $results_contacts = $connexion->prepare($requete_contact);
                                $results_contacts->execute(array(':no'=> $v['idcontact']));
                                $tab_contacts = $results_contacts->fetchAll(PDO::FETCH_ASSOC); 

                                // affichage des infos contact ?>
                                <table style='margin-top:10px; padding-left: 5px;'>
                                    <tr><td>
                                        Contact : <?= $v['contact'] ?>
                                        <?php foreach ($tab_contacts as $k1 => $v1) { ?>
                                        <span style='margin-left: 20px;'><?= $v1['valeur'] ?></span>
                                        <?php } ?>
                                    </td></tr>
                                </table>
                            <?php
                            }
                            ?>

                            </td></tr>
                        </table> <!-- fin bloc annonces -->
                        <?php
                        $i++;
                        array_push($matab_ann, $v['pid']);
                    }
                }
            ?>
    </page>
    <?php
    $content = ob_get_clean();

    try{
        $pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
        $pdf->pdf->SetDisplayMode('fullpage');
        $pdf->writeHTML($content);

        $file = 'annonces_l'.$no.'_t'.$territoire.'.pdf';
        $nomfile = '../../../02_medias/15_lettreinfo_pdf_annonces/'.$file;
        $pdf->Output($nomfile,'F');
        
        $tab['annonces'] = $file;

    }catch(HTML2PDF_exception $e){
            die($e);
    }
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
