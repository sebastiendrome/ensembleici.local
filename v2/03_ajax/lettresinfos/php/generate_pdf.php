<?php
session_name("EspacePerso");
session_start();
require ('../../../01_include/_var_ensemble.php');
require ('../../../01_include/_connect.php');
require "tfpdf.php"; 
require ('funcionesfecha.php');

$dia = date('Y-m-d');
$req1 = "SELECT territoires_id FROM lettreinfo WHERE no = ".$_POST['id'];
$res1 = $connexion->prepare($req1);
$res1->execute();
$territoire = $res1->fetch(PDO::FETCH_ASSOC);

class PDF extends tFPDF{
    function Header()	{
        $this->AddFont('DejaVuB','B','DejaVuSansCondensed-Bold.ttf',true); 
        $this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);        
        $this->Image('../images/logomini.png',10,5,'','','','http://www.ensembleici.fr/');

        $this->SetFont('DejaVuB','B',10);	
        $this->SetTextColor(55,93,142);
        $this->SetXY(135,10);			
        $this->Multicell(0,6,'Retrouvez le détail de l\'agenda et les petites annonces sur le site www.ensembleici.fr !',0,'C');
        $this->SetXY(135,30);				
        $this->SetFont('DejaVu','',9);	
        $this->Multicell(0,6,'Il vous est également aisé d\'ajouter des informations via les formulaires du site !',0,'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('DejaVu','',10);	
    }

    function duree($fecha, $type, $territoire){
        $this->SetFont('DejaVuB','B',14);	
        $this->SetTextColor(0,0,0);
        if ($territoire == 1) {
            $this->SetXY(50,30);			
            $this->Multicell(80,6,'Baronnies',0,'C');
        }
        else {
            $this->SetXY(55,30);			
            $this->Multicell(80,6,'Vallée de la Drôme et Diois',0,'C');
        }
        
        $this->SetFont('DejaVuB','B',14);	
        $this->SetXY(40,45);
        $this->SetFillColor(85, 211, 255);
        $this->SetTextColor(255);
        if ($type == 'agenda') {
            $titre = "L'agenda ";
        } 
        else {
            $titre = "Les annonces ";
        }
        $semaine = $titre."du ".invertirFechas($fecha)." au ".invertirFechas(anadirDias($fecha,10));
        $this->Cell(120,10,$semaine,0,0,'C',true);
        $this->Ln(6);
    }    

    function FancyTable($diactual,$tailleville,$taillegenre, $territoire, $montab_global){
        require ('../../../01_include/_connect.php');
        // Ajouts des fonts
        $this->AddFont('DejaVuB','B','DejaVuSansCondensed-Bold.ttf',true);
        $this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
        $this->AddFont('ArimoB','B','Arimo-Bold.ttf',true);			
        $this->AddFont('Arimo','','Arimo-Regular.ttf',true);

        //Requete à la base de données
//        $requete =  "SELECT E.no as no, E.heure_debut as heure_debut, E.heure_fin as heure_fin, G.no as nogenre, E.titre as titre, G.libelle as libelle,
//                E.no_ville as no_ville, E.adresse as direccion, V.nom_ville_maj as nom_ville, CONVERT(E.description USING utf8) as description, E.nomadresse, 
//                E.telephone, E.email, E.telephone2, E.site, B.nom as contact, B.no as idcontact 
//                FROM villes V, genre G, communautecommune_ville T, communautecommune C, evenement E 
//                LEFT JOIN  evenement_contact O ON O.no_evenement = E.no LEFT JOIN contact B ON  O.no_contact = B.no
//                WHERE E.date_debut - INTERVAL 11 DAY  < :fechaact AND E.date_fin + INTERVAL 1 DAY  > :fechaact AND DATEDIFF(E.date_fin ,E.date_debut) < 8 
//                AND E.no_ville = V.id AND E.no_genre = G.no AND V.id = T.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t ORDER BY V.nom_ville ASC";
        $requete =  "SELECT E.no as no, E.heure_debut as heure_debut, E.heure_fin as heure_fin, G.no as nogenre, E.titre as titre, G.libelle as libelle,
                E.no_ville as no_ville, E.adresse as direccion, V.nom_ville_maj as nom_ville, CONVERT(E.description USING utf8) as description, E.nomadresse, 
                E.telephone, E.email, E.telephone2, E.site, B.nom as contact, B.no as idcontact, E.date_debut, E.date_fin  
                FROM villes V, genre G, communautecommune_ville T, communautecommune C, evenement E 
                LEFT JOIN  evenement_contact O ON O.no_evenement = E.no LEFT JOIN contact B ON  O.no_contact = B.no
                WHERE E.date_debut - INTERVAL 1 DAY  < :fechaact AND E.date_fin + INTERVAL 1 DAY  > :fechaact AND E.etat = 1 AND E.validation = 1 
                AND E.no_ville = V.id AND E.no_genre = G.no AND V.id = T.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t ORDER BY V.nom_ville ASC";	

        $results_evenements = $connexion->prepare($requete);
        $results_evenements->execute(array(':fechaact'=>$diactual, ":t" => $territoire)) or die ("requete ligne 66: ".$requete);
        $results_evenements->bindColumn(2, $data, PDO::PARAM_LOB);
        $tab_evenements = $results_evenements->fetchAll(PDO::FETCH_ASSOC); 

        // Compruebo si el dia tiene eventos
        if($tab_evenements){
                $this->Ln( );
                $this->SetTextColor(0);
                $this->SetDrawColor(247, 165, 70);
                $this->SetLineWidth(.3);
                $this->SetFont('DejaVuB','B',12);		           
                $this->SetFillColor(255, 215, 120);

            // obtengo el nombre del dia de la semana
            $date_jour_semaine = explode("-",$diactual);
            $dia_semana = nomJourSemaine($diactual)."   ".$date_jour_semaine[2]."   ".utf8_decode(nomDuMois($date_jour_semaine[1]));

            // compruebo si se va a quedar sin hijos
            $yjour = $this->GetY();
            if($yjour>240){$yjour=400;}		

            // muestro el dia en cuestión
            $this->SetY($yjour);
            $this->Cell(65,8,$dia_semana,1,0,'L',true);
            $this->Ln(12); 			
            $y = $this->GetY();		                  

            $ville = "";
            $indice_results = 0;
            $no_prec = $tab_evenements[$indice_results][no];
//            $montab_evt = array();
            while($tab_evenements[$indice_results]){
                $valable = 1;
                if (in_array($tab_evenements[$indice_results][no], $montab_global)) {
                    $valable = 0;
                }
                if ($valable == 1) {
                    if ($no_prec != $tab_evenements[$indice_results][no]) {
                        $y2 = $this->GetY();
                        if($y2<$y){
                                $y=$y2-6;
                        }
                        if ($y > 250) {
                            $this->AddPage();  
                            $y = $this->GetY();	
                        }

                        $this->SetXY(6,$y);
                        $x = 6;
                        //$x = $x + 12;						
                        $this->SetXY($x,$y);
                        $this->SetFillColor(255,255,204);
                        $this->SetFont('DejaVuB','B',10);						
                        $this->Multicell($taillegenre,6,$tab_evenements[$indice_results][libelle],0,'L',true);


                        $x = $x + $taillegenre + 2;
                        $this->SetXY($x,$y);
                        $this->SetFillColor(250,250,250);
                        $this->SetFont('DejaVuB','B',10);
                        $tailletitre2 = $this->GetStringWidth($tab_evenements[$indice_results][titre]);  
                        $tailletitre = 80;

                        //$tailletitre = $tailletitre -5;
                        //$this->Multicell($tailletitre,6,$tab_evenements[$indice_results][titre],0,'L',true);
                        $this->Multicell($tailletitre,6,$tab_evenements[$indice_results][titre],0,'L',true);
                        $ylibelle = $this->GetY();

                        $x = 120;
                        $this->SetXY($x,$y);

                        $horaire = "";
                        if ($tab_evenements[$indice_results][heure_debut] != ""){$horaire=substr($tab_evenements[$indice_results][heure_debut],0,-3);}
                        if ($tab_evenements[$indice_results][heure_fin] != ""){$horaire.=" - ".substr($tab_evenements[$indice_results][heure_fin],0,-3);}	
                        if($horaire == "") {$this->SetFillColor(255,255,255);}
                        else {$this->SetFillColor(250,250,250);}
                        $this->Multicell(0,6,$horaire,0,'L',true);

                        $this->SetXY(150,$y);
                        $this->SetFillColor(255,255,204);
                        $this->SetDrawColor(45, 171, 218);
                        $this->SetFont('DejaVuB','B',10);
                        $x = $tailleville;
                        if ($tab_evenements[$indice_results][nom_ville]!=$ville){
                                $this->Multicell($x,6,$tab_evenements[$indice_results][nom_ville],1,'L',true);
                        }

                        $ville = $tab_evenements[$indice_results][nom_ville];

                        if ($tab_evenements[$indice_results][date_debut] != $tab_evenements[$indice_results][date_fin]) {
                            // ajout d'une ligne pour date de fin

                            $this->SetFillColor(255,255,255);
                            $this->Ln(1); 
                            $x = $taillegenre + 8;
                            $this->SetXY($x,$ylibelle);
                            $this->SetFont('DejaVu','',9);
                            $chaine_fin_evt = "Date de fin de l'évènement : ".substr($tab_evenements[$indice_results][date_fin], 8,2).'/'.substr($tab_evenements[$indice_results][date_fin], 5,2).'/'.substr($tab_evenements[$indice_results][date_fin], 0,4); 
                            $this->Multicell(0,6, $chaine_fin_evt,0,'L',true);
                            $ylibelle = $this->GetY();
                        }
        //                $y2 = $this->GetY();
        //                if($y2<$y){
        //                        $y=$y2-6;
        //                }

                        $this->SetXY(10,$ylibelle);
                        $this->SetFillColor(255,255,255);
                        $this->Ln(1); 
                        $y = $this->GetY();
                        $this->SetXY(6,$y);
                        $this->SetFont('DejaVu','',9);
                        //$chaine = substr(html_entity_decode(strip_tags(str_replace("&#39;", "'", $tab_evenements[$indice_results][description]))), 0, 900);
                        $chaine = substr(strip_tags(html_entity_decode(trim($tab_evenements[$indice_results][description]), ENT_QUOTES, 'UTF-8')), 0, 900);
                        $chaine = str_replace(CHR(10)," ",$chaine);
                        $chaine = str_replace(CHR(9),"",$chaine);
                        $chaine = str_replace(CHR(11),"",$chaine);
                        $chaine = str_replace(CHR(12),"",$chaine);
                        $chaine = str_replace(CHR(13)," ",$chaine);
                        $chaine = str_replace('
                            ','', nl2br($chaine));
                        $chaine = str_replace("&#39;","'",$chaine);
                        $chaine = str_replace("&quot;",'"',$chaine);
                        if (strlen($tab_evenements[$indice_results][description]) > 900) {
                            $chaine .= ' ... (lire la suite sur notre site)';
                        }
                        $this->Multicell(0,6, $chaine,0,'L',true);

                        // ajout de la ligne contact
                        $this->Ln(1);   
                        $y = $this->GetY();
                        if ($y > 270) {
                            $this->AddPage();  
                            $y = $this->GetY();	
                        }
                        $this->SetXY(6,$y);

                        $x = 6;
                        if ($tab_evenements[$indice_results][nomadresse] != '') {
                            $chainelieu = "Lieu : ".$tab_evenements[$indice_results][nomadresse];
                            $longueur = $this->GetStringWidth($chainelieu) + 10;
                            $this->Multicell($longueur,6, $chainelieu,0,'L',true);
                            $x =  $x + $longueur;
                        }
                        $this->SetXY($x,$y);
                        if ($tab_evenements[$indice_results][telephone] != '') {
                            $chainetel = "Téléphone : ".$tab_evenements[$indice_results][telephone];
                            $longueur = $this->GetStringWidth($chainetel) +10;
                            if ($x + $longueur > 205) {
                                $this->Ln(5);
                                $x = 6;
                                $y = $this->GetY();
                                $this->SetXY(6,$y);
                            }
                            $this->Multicell($longueur,6, $chainetel,0,'L',true);
                            $x =  $x + $longueur;
                        }
                        $this->SetXY($x,$y);
                        if ($tab_evenements[$indice_results][email] != '') {
                            $chainemail = "Email : ".$tab_evenements[$indice_results][email];
                            $longueur = $this->GetStringWidth($chainemail) +10;
                            if ($x + $longueur > 205) {
                                $this->Ln(5);
                                $x = 6;
                                $y = $this->GetY();
                                $this->SetXY(6,$y);
                            }
                            $this->Multicell($longueur,6, $chainemail,0,'L',true);
                            $x =  $x + $longueur;
                        }
                        $this->SetXY($x,$y);
                        if ($tab_evenements[$indice_results][site] != '') {
                            $chainesite = "Site Internet : ".str_replace("http://", "", $tab_evenements[$indice_results][site]);
                            $chainesite = str_replace("https://", "", $chainesite);
                            $longueur = $this->GetStringWidth($chainesite) +10;
                            if ($x + $longueur > 205) {
                                $this->Ln(5);
                                $x = 6;
                                $y = $this->GetY();
                                $this->SetXY(6,$y);
                            }
                            $this->Multicell($longueur,6, $chainesite,0,'L',true);
                            $x =  $x + $longueur;
                        }

                        $this->Ln(1);
                        $y = $this->GetY();
                        $this->SetXY(6,$y);
                        if ($tab_evenements[$indice_results][contact] != '') {
                            $chainecontact = "Contact : ".$tab_evenements[$indice_results][contact];
                            $this->Multicell(0,6, $chainecontact,0,'L',true);
                            $x =  $this->GetX() + 50;
                            $this->SetXY($x,$y);

                            // recherche du contact
                            $requete_contact =  "SELECT no_contactType, valeur FROM contact_contactType WHERE (no_contact = :no) AND no_contactType IN (1, 2) ORDER BY no_contactType";	
                            $results_contacts = $connexion->prepare($requete_contact);
                            $results_contacts->execute(array(':no'=>$tab_evenements[$indice_results][idcontact])) or die ("requete ligne 66: ".$requete);
                            $tab_contacts = $results_contacts->fetchAll(PDO::FETCH_ASSOC); 

                            if ($tab_contacts) {
                                foreach ($tab_contacts as $k => $v) {
                                    $this->Multicell(80,6, $v[valeur],0,'L',true);
                                    $x =  $this->GetX() + 90;
                                    $this->SetXY($x,$y);
                                }
                            }
                        }

                        $this->Ln(1); 

                        $ylibelle = $this->GetY() + 10;
                        $this->SetY($ylibelle);
                        $this->Ln(1);   
                        $y = $this->GetY();
                    }
                    else {
//                        $this->Ln(1);
                        $y = $this->GetY() - 5;
                        $this->SetXY(20,$y);
                        if ($tab_evenements[$indice_results][contact] != '') {
                            $chainecontact = $tab_evenements[$indice_results][contact];
                            $this->Multicell(0,6, $chainecontact,0,'L',true);
                            $x =  $this->GetX() + 50;
                            $this->SetXY($x,$y);

                            // recherche du contact
                            $requete_contact =  "SELECT no_contactType, valeur FROM contact_contactType WHERE (no_contact = :no) AND no_contactType IN (1, 2) ORDER BY no_contactType";	
                            $results_contacts = $connexion->prepare($requete_contact);
                            $results_contacts->execute(array(':no'=>$tab_evenements[$indice_results][idcontact])) or die ("requete ligne 66: ".$requete);
                            $tab_contacts = $results_contacts->fetchAll(PDO::FETCH_ASSOC); 

                            if ($tab_contacts) {
                                foreach ($tab_contacts as $k => $v) {
                                    $this->Multicell(80,6, $v[valeur],0,'L',true);
                                    $x =  $this->GetX() + 90;
                                    $this->SetXY($x,$y);
                                }
                            }
                        }
                        $this->Ln(1); 

                        $ylibelle = $this->GetY() + 10;
                        $this->SetY($ylibelle);
                        $this->Ln(1);   
                        $y = $this->GetY();
                    }
                    $no_prec = $tab_evenements[$indice_results][no];
                }
                if ($tab_evenements[$indice_results][date_debut] != $tab_evenements[$indice_results][date_fin]) {
                    array_push($montab_global, $tab_evenements[$indice_results][no]);
                }
                $indice_results++;
            }  

            // Restauración de colores y fuentes
            $this->SetFillColor(224,235,255);
            $this->SetTextColor(0);
            $this->SetFont('DejaVu','',10);	
        }
        return $montab_global;
    }
    
    function FancyTableAnnonces($diactual,$tailleville,$taillegenre, $territoire){
        require ('../../../01_include/_connect.php');
        // Ajouts des fonts
        $this->AddFont('DejaVuB','B','DejaVuSansCondensed-Bold.ttf',true);
        $this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
        $this->AddFont('ArimoB','B','Arimo-Bold.ttf',true);			
        $this->AddFont('Arimo','','Arimo-Regular.ttf',true);
        
        $date_limite = date('Y-m-d', mktime(0, 0, 0, date("m")-1, date("d"),   date("Y")));

        //Requete à la base de données
        $requete =  "SELECT E.no as pid, E.titre as titre, E.monetaire, E.prix, E.no_ville as no_ville, V.nom_ville_maj as nom_ville, 
                CONVERT(E.description USING utf8) as description, E.site, Y.libelle as type_libelle, Y.no as type, E.date_creation, B.nom as contact, B.no as idcontact     
                FROM villes V, communautecommune_ville T, petiteannonce_type Y,communautecommune C, petiteannonce E 
                LEFT JOIN  petiteannonce_contact O ON O.no_petiteannonce = E.no LEFT JOIN contact B ON  O.no_contact = B.no 
                WHERE E.etat = 1 AND E.validation = 1 AND E.apparition_lettre < 2 AND E.no_ville = V.id AND E.date_fin > :fechaact AND 
                V.id = T.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = :t AND E.date_creation > :datelim AND E.no_petiteannonce_type = Y.no ORDER BY Y.no ASC";	

        $results_evenements = $connexion->prepare($requete);
        $results_evenements->execute(array(':fechaact'=>$diactual, ":t" => $territoire, ":datelim" => $date_limite)) or die ("requete ligne 66: ".$requete);
//        $results_evenements->bindColumn(2, $data, PDO::PARAM_LOB);
        $tab_evenements = $results_evenements->fetchAll(PDO::FETCH_ASSOC); 

        // Compruebo si el dia tiene eventos
        if($tab_evenements){
            $this->Ln( );
            $this->SetTextColor(0);
            $this->SetFont('DejaVuB','B',12);		           
            $this->SetFillColor(255, 215, 120);
			
            $y = $this->GetY();		                  
            $ville = ""; $type = ""; $prec = '';
            $indice_results = 0;											
            while($tab_evenements[$indice_results]){  
//                if ($tab_evenements[$indice_results][pid] != $prec) {
                if ($tab_evenements[$indice_results][pid] != $prec) {
                    switch ($tab_evenements[$indice_results][type]) {
                        case 0 : $this->SetFillColor(255,255,204); break;
                        case 1 : $this->SetFillColor(155,255,200); break;
                        case 2 : $this->SetFillColor(155,200,255); break;
                        default: $this->SetFillColor(255,255,204); break;
                    }

                    if ($type != $tab_evenements[$indice_results][type]) {
                        if ($type != '') {
                            $this->AddPage();
                        }
                        $y = $this->GetY() + 10;
                        $this->SetXY(10,$y);
                        $this->SetFont('DejaVuB','B',12);
                        $this->Multicell(190,10, strtoupper($tab_evenements[$indice_results][type_libelle]),1,'C',true);
                        $type = $tab_evenements[$indice_results][type];
                        $this->Ln(1); 
                        $y = $this->GetY() + 10;
                        $this->SetY($y);
                    } else {
                        if ($y > 260) {
                            $this->AddPage();  
                            $y = $this->GetY();	
                        }
                    }

                    $this->SetXY(10,$y);
    //                $this->SetDrawColor(45, 171, 218);
                    $this->SetFont('DejaVuB','B',10);
                    $x = $tailleville;
                    if ($tab_evenements[$indice_results][nom_ville]!=$ville){
                            $this->Multicell($x,6,$tab_evenements[$indice_results][nom_ville],1,'L',true);
                    }

                    $ville = $tab_evenements[$indice_results][nom_ville];

                    $x = 65; 
                    $this->SetXY(65,$y);
                    $this->SetFillColor(255,255,255);
                    $this->Multicell(100,6,$tab_evenements[$indice_results][titre],0,'L',true);
                    $ylibelle = $this->GetY();

                    $x = 170; 
                    $this->SetXY(170,$y);
                    $date_creation = $tab_evenements[$indice_results][date_creation];
                    $chainepublie = "Publié : ".substr($date_creation, 8, 2).'/'.substr($date_creation, 5, 2).'/'.substr($date_creation, 0, 4);
                    $this->Multicell(100,6,$chainepublie,0,'L',true);

                    $this->SetFillColor(255,255,255);
                    //$y = $this->GetY();
                    $this->SetXY(6,$ylibelle);
                    $this->SetFont('DejaVu','',9);
                    $chaine = substr(strip_tags(html_entity_decode(trim($tab_evenements[$indice_results][description]), ENT_QUOTES, 'UTF-8')), 0, 900);
                    $chaine = str_replace(CHR(10)," ",$chaine);
                    $chaine = str_replace(CHR(9),"",$chaine);
                    $chaine = str_replace(CHR(11),"",$chaine);
                    $chaine = str_replace(CHR(12),"",$chaine);
                    $chaine = str_replace(CHR(13)," ",$chaine);
                    $chaine = str_replace('
                        ','', nl2br($chaine));
                    $chaine = str_replace("&#39;","'",$chaine);
                    $chaine = str_replace("&quot;",'"',$chaine);
                    if (strlen($tab_evenements[$indice_results][description]) > 900) {
                        $chaine .= ' ... (lire la suite sur notre site)';
                    }
                    $this->Multicell(0,6, $chaine,0,'L',true);

                    $this->Ln(1);
                    $y = $this->GetY();
                    $this->SetXY(6,$y);
                    if ($tab_evenements[$indice_results][contact] != '') {
                        $chainecontact = "Contact : ".$tab_evenements[$indice_results][contact];
                        $this->Multicell(0,6, $chainecontact,0,'L',true);
                        $x =  $this->GetX() + 50;
                        $this->SetXY($x,$y);

                        // recherche du contact
                        $requete_contact =  "SELECT no_contactType, valeur FROM contact_contactType WHERE (no_contact = :no) AND no_contactType IN (1, 2) ORDER BY no_contactType";	
                        $results_contacts = $connexion->prepare($requete_contact);
                        $results_contacts->execute(array(':no'=>$tab_evenements[$indice_results][idcontact])) or die ("requete ligne 66: ".$requete);
                        $tab_contacts = $results_contacts->fetchAll(PDO::FETCH_ASSOC); 

                        if ($tab_contacts) {
                            foreach ($tab_contacts as $k => $v) {
                                $this->Multicell(80,6, $v[valeur],0,'L',true);
                                $x =  $this->GetX() + 90;
                                $this->SetXY($x,$y);
                            }
                        }
                    }

                    $ylibelle = $this->GetY() + 10;
                    $this->SetY($ylibelle);
                    $this->Ln(1);   
                    $y = $this->GetY();
                    $prec = strval($tab_evenements[$indice_results][pid]);
 
                } 
                else {
                    $y = $this->GetY();
                    $this->SetXY(6,$y);
                    if ($tab_evenements[$indice_results][contact] != '') {
                        $chainecontact = $tab_evenements[$indice_results][contact];
                        $x =  $this->GetX() + 15;
                        $this->SetXY($x,$y);
                        $this->Multicell(0,0, $chainecontact,0,'L',true);
                        $x =  $this->GetX() + 50;
                        $this->SetXY($x,$y);

                        // recherche du contact
                        $requete_contact =  "SELECT no_contactType, valeur FROM contact_contactType WHERE (no_contact = :no) AND no_contactType IN (1, 2) ORDER BY no_contactType";	
                        $results_contacts = $connexion->prepare($requete_contact);
                        $results_contacts->execute(array(':no'=>$tab_evenements[$indice_results][idcontact])) or die ("requete ligne 66: ".$requete);
                        $tab_contacts = $results_contacts->fetchAll(PDO::FETCH_ASSOC); 

                        if ($tab_contacts) {
                            foreach ($tab_contacts as $k => $v) {
                                $this->Multicell(80,0, $v[valeur],0,'L',true);
                                $x =  $this->GetX() + 90;
                                $this->SetXY($x,$y);
                            }
                        }
                    }
                    $ylibelle = $this->GetY() + 10;
                    $this->SetY($ylibelle);
                    $this->Ln(1);   
                    $y = $this->GetY();
                }
                $indice_results++;
//                $prec = strval($tab_evenements[$indice_results][pid]);
            }            

            // Restauración de colores y fuentes
            $this->SetFillColor(224,235,255);
            $this->SetTextColor(0);
            $this->SetFont('DejaVu','',10);	
        }
    }
}
$tab = array();
$is_agenda = 0; $is_annonces = 0;
$nbfiles = 0;
if ($_POST['agenda'] == 1) {
    $pdf= new PDF('P','mm');
    $pdf->SetDisplayMode(fullwidth,continuous);
    $pdf->AliasNbPages(); 
    $pdf->SetMargins(5,50,5); 
    $pdf->AddPage("P","A4"); 
    $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
    $pdf->SetFont('DejaVu','',10);	
    $pdf->duree($dia, 'agenda', $territoire['territoires_id']);
    $fin = anadirDias($dia,9);

    //dimension de la caja ville
    $stringville = maxStringVille($dia,$fin);
    //$tailleville = $pdf->GetStringWidth($stringville);
    $tailleville = 50;
    // redimension de la caja
//    if($tailleville>50){$tailleville=$tailleville*0.85;}    

    //dimension de la caja genre
    $stringgenre = maxStringgenre($dia,$fin);
    $taillegenre = $pdf->GetStringWidth($stringgenre);  
    // redimension de la caja
    if($taillegenre>15){$taillegenre=$taillegenre*0.95;}    

    $diactual = $dia;
    $tab_global = array();
    for($i=1;$i<12;$i++){		
        $tab_global = $pdf->FancyTable($diactual,$tailleville,$taillegenre, $territoire['territoires_id'], $tab_global);				
        $diactual = anadirDia($diactual); 
    }

    $now = date('Y-m-d');
    $file_agenda = '../../../../02_medias/14_lettreinfo_pdf_agenda/agenda_l'.$_POST['id'].'._t'.$territoire['territoires_id'].'.pdf';
    $name_agenda = 'agenda_l'.$_POST['id'].'._t'.$territoire['territoires_id'].'.pdf';
    $pdf->Output($file_agenda, 'F'); 
    $tab['agenda'] = $name_agenda;
    $is_agenda = 1; $nbfiles++;
}

if ($_POST['annonce'] == 1) {
    $pdf= new PDF('P','mm');
    $pdf->SetDisplayMode(fullwidth,continuous);
    $pdf->AliasNbPages(); 
    $pdf->SetMargins(5,50,5); 
    $pdf->AddPage("P","A4"); 
    $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
    $pdf->SetFont('DejaVu','',10);	
    $pdf->duree($dia, 'annonces', $territoire['territoires_id']);
    $fin = anadirDias($dia,9);

    //dimension de la caja ville
    $stringville = maxStringVille($dia,$fin);
    $tailleville = 50;   

    //dimension de la caja genre
    $stringgenre = maxStringgenre($dia,$fin);
    $taillegenre = $pdf->GetStringWidth($stringgenre);  
    // redimension de la caja
    if($taillegenre>15){$taillegenre=$taillegenre*0.95;}    

    $diactual = $dia;
    $pdf->FancyTableAnnonces($diactual,$tailleville,$taillegenre, $territoire['territoires_id']);				

    $now = date('Y-m-d');
    $file_annonces = '../../../../02_medias/15_lettreinfo_pdf_annonces/annonces_l'.$_POST['id'].'._t'.$territoire['territoires_id'].'.pdf';
    $name_annonces = 'annonces_l'.$_POST['id'].'._t'.$territoire['territoires_id'].'.pdf';
    $pdf->Output($file_annonces, 'F'); 
    $tab['annonces'] = $name_annonces;
    $is_annonces = 1; $nbfiles++;
}
$tab['is_agenda'] = $is_agenda; 
$tab['is_annonces'] = $is_annonces;
$tab['nb'] = $nbfiles;
$reponse = json_encode($tab); 
echo $reponse;
?>
