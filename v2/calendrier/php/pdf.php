<?
// header('Content-Type: text/html; charset=iso-8859-1');
require "tfpdf.php"; 
require('funcionesfecha.php');
require ('_connect.php');

if($_GET['dia']!="" )
{   
// aqui hay que meter el while
    $dia = $_GET['dia'] ; 
}
else
{
	$dia = date('Y-m-d'); 
}	

	class PDF extends tFPDF{
		function Header()	{
            $this->AddFont('DejaVuB','B','DejaVuSansCondensed-Bold.ttf',true); 
			$this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);        
			$this->Image('../images/logomini.png',10,5,'','','','http://www.ensembleici.fr/');		
            $this->SetFont('DejaVuB','B',10);	
            $this->SetTextColor(55,93,142);
            $this->SetXY(135,10);			
			$this->Multicell(0,6,'Retrouvez le détail de l\'agenda local sur le site www.ensembleici.fr !',0,'C');
            $this->SetXY(135,25);				
			$this->SetFont('DejaVu','',9);	
			$this->Multicell(0,6,'Il vous est également aisé d\'ajouter des informations via les formulaires du site !',0,'C');
            $this->Ln(5);
        }
		
		function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('DejaVu','',10);	
			//$this->Cell(0,10,'http://www.ensembleici.fr/calendrier/hebdo.php                                                Page '.$this->PageNo().'/{nb}',0,0,'R');
		}
    
        function duree($fecha){
            $this->SetFont('DejaVuB','B',14);	
            $this->SetXY(50,30);
            $this->SetFillColor(85, 211, 255);
			$this->SetTextColor(255);
            $semaine = "Du ".invertirFechas($fecha)." au ".invertirFechas(anadirDias($fecha,9));
            $this->Cell(80,10,$semaine,0,0,'C',true);
            $this->Ln(6);
        }    
     	
		function FancyTable($diactual,$tailleville,$taillegenre){
            require ('_connect.php');
			// Ajouts des fonts
			$this->AddFont('DejaVuB','B','DejaVuSansCondensed-Bold.ttf',true);
			$this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
			$this->AddFont('ArimoB','B','Arimo-Bold.ttf',true);			
            $this->AddFont('Arimo','','Arimo-Regular.ttf',true);		
			
            //Requete à la base de données
            $requete =  "SELECT  
                    evenement.no as no,
					evenement.heure_debut as heure_debut,
					evenement.heure_fin as heure_fin,
                    genre.no as nogenre, 
                    evenement.titre as titre,
                    genre.libelle as libelle,
                    evenement.no_ville as no_ville,
					evenement.adresse as direccion,
                    villes.nom_ville_maj as nom_ville
                    FROM evenement, villes, genre
                    WHERE date_debut - INTERVAL 1 DAY  < :fechaact 					
                    AND date_fin + INTERVAL 1 DAY  > :fechaact	
                    AND DATEDIFF(date_fin ,date_debut)<8
                    AND evenement.no_ville = villes.id
                    AND evenement.no_genre = genre.no
                    ORDER BY villes.nom_ville ASC";	
                    
                $results_evenements = $connexion->prepare($requete);
                $results_evenements->execute(array(':fechaact'=>$diactual)) or die ("requete ligne 66: ".$requete);
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
					while($tab_evenements[$indice_results]){      
						$this->SetXY(10,$y);
						$this->SetFillColor(255,255,204);
						$this->SetDrawColor(45, 171, 218);
						$this->SetFont('DejaVuB','B',10);
						$x = $tailleville;
						
						if ($tab_evenements[$indice_results][nom_ville]!=$ville){
							$this->Multicell($x,6,$tab_evenements[$indice_results][nom_ville],1,'L',true);
						}
						
						$ville = $tab_evenements[$indice_results][nom_ville];
						$y2 = $this->GetY();
						if($y2<$y){
							$y=$y2-6;
						}
						
						$x = $x + 12;						
						$this->SetXY($x,$y);
						$this->SetFillColor(250,250,240);
						$this->SetFont('DejaVu','',10);						
						$this->Multicell($taillegenre,6,$tab_evenements[$indice_results][libelle],0,'L',true);

										
						$x = $x + $taillegenre + 2;
						$this->SetXY($x,$y);
						$this->SetFillColor(250,250,250);						
						$tailletitre2 = $this->GetStringWidth($tab_evenements[$indice_results][titre]);  
						// $tailletitre2 = " - ".strlen($tab_evenements[$indice_results][titre]);
						// $tailletitre += $tailletitre2+6;
						$tailletitre = 198 - $x - 20;

						//$tailletitre = $tailletitre -5;
						//$this->Multicell($tailletitre,6,$tab_evenements[$indice_results][titre],0,'L',true);
						$this->Multicell($tailletitre,6,$tab_evenements[$indice_results][titre],0,'L',true);
						$ylibelle = $this->GetY();
						
						$x = 180;
						$this->SetXY($x,$y);
												
						$horaire = "";
						if ($tab_evenements[$indice_results][heure_debut] != ""){$horaire=substr($tab_evenements[$indice_results][heure_debut],0,-3);}
						if ($tab_evenements[$indice_results][heure_fin] != ""){$horaire.=" - ".substr($tab_evenements[$indice_results][heure_fin],0,-3);}	
						if($horaire == "") {$this->SetFillColor(255,255,255);}
						else {$this->SetFillColor(250,250,250);}
						$this->Multicell(0,6,$horaire,0,'L',true);
						
						$this->SetY($ylibelle);
						$this->Ln(1);   
						$y = $this->GetY();
						$indice_results++;
					}            
						 
					// Restauración de colores y fuentes
					$this->SetFillColor(224,235,255);
					$this->SetTextColor(0);
					$this->SetFont('DejaVu','',10);	
				}	
        }
        
        function FancyTableLongueDuree($diainicio, $diafin){
            require ('_connect.php');
			// Ajouts des fonts
			$this->AddFont('DejaVuB','B','DejaVuSansCondensed-Bold.ttf',true);
			$this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
			$this->AddFont('ArimoB','B','Arimo-Bold.ttf',true);			
            $this->AddFont('Arimo','','Arimo-Regular.ttf',true);	
            
            //Requete à la base de données
          $requete =  "SELECT evenement.no as no,
								genre.no as nogenre, 
								evenement.titre as titre,
								genre.libelle as libelle,
								evenement.no_ville as no_ville,
								villes.nom_ville_maj as nom_ville,
								evenement.adresse as direccion,
								evenement.date_debut as debut,
								evenement.date_fin as fin,
								evenement.date_debut as date_debut,
								evenement.date_fin as date_fin
								FROM evenement, villes, genre
								WHERE date_debut <= :inicio 					
								AND date_fin >= :fin	
								AND DATEDIFF(date_fin ,date_debut)>7
								AND evenement.no_ville = villes.id
								AND evenement.no_genre != 24
								AND evenement.no_genre != 16
								AND evenement.no_genre = genre.no
								ORDER BY villes.nom_ville ASC, date_debut ASC";
                    
                $results_recurrents = $connexion->prepare($requete);
                $results_recurrents->execute(array(':inicio'=>$diainicio,':fin'=>$diafin)) or die ("requete ligne 163: ".$requete);
                $tab_evenements = $results_recurrents->fetchAll(PDO::FETCH_ASSOC);		
                
                // talla maxima nom_ville
                $indice=0;
                $taille_max_nom_ville = 0;
                while($tab_evenements[$indice]){
                        if(strlen($tab_evenements[$indice][nom_ville])>$taille_max_nom_ville){
                                $taille_max_nom_ville = strlen($tab_evenements[$indice][nom_ville]);
                        }
                         $indice++;   
                }
                              
                // talla maxima genre libelle
                $indice=0;
                $taille_max_libelle = 0;
                while($tab_evenements[$indice]){
                        $auxlibelle = $tab_evenements[$indice][libelle];
                        if(strlen($auxlibelle)>$taille_max_libelle){
                                $taille_max_libelle = strlen($auxlibelle);
                        }
                        $indice++;   
                }  
            
                // talla maxima evenement titre
                $indice=0;
                $taille_max_titre = 0;
                while($tab_evenements[$indice]){
                        if(strlen($tab_evenements[$indice][titre])>$taille_max_titre){
                                $taille_max_titre = strlen($tab_evenements[$indice][titre]);
                        }
                        $indice++;   
                }
                
                // Compruebo si hay eventos
				if($tab_evenements){
					$this->Ln( );
					$this->SetTextColor(0);
					$this->SetLineWidth(.3);					
                                                        
                    $indice_results = 0;			
                    $y = $this->GetY();
					$ville = "";
					$semaine_before = "";
					while($tab_evenements[$indice_results]){      
						$this->SetXY(10,$y);                       
						$this->SetFillColor(255, 215, 120);
						$this->SetTextColor(0);
						$this->SetDrawColor(247, 165, 70);
						$this->SetFont('DejaVuB','B',10);	
                        $taillex = $taille_max_nom_ville*2.5 ;    
						if ($ville !=$tab_evenements[$indice_results][nom_ville]){
							if($y>258){$this->Ln(20);}	
							$this->SetX(10);
							$this->Ln(4); 
							$this->Multicell($taillex,6,$tab_evenements[$indice_results][nom_ville],1,'L',true);	
							$this->Ln(2); 
							$semaine_before = "";
						}							
					                    
                        $y2 = $this->GetY();
						if($y2<$y){
							$y=$y2-6;
						}						
				   
                        $semaine = "Du ".invertirFechas($tab_evenements[$indice_results][date_debut])." au ".invertirFechas($tab_evenements[$indice_results][date_fin])."";
                        $taillex  = $this->GetStringWidth($semaine);
						$taillex = $taillex*1.1;						
						if(diffdays($tab_evenements[$indice_results][date_debut],$tab_evenements[$indice_results][date_fin])>300){
							$semaine = "Toute l'année";
						}
						
                        $this->SetX(12);     
						$y = $this->GetY();				
						$this->SetFillColor(255,255,204);
						$this->SetDrawColor(45, 171, 218);
						$this->SetFont('DejaVuB','B',10);
						if($semaine_before != $semaine){$this->Multicell($taillex,6,$semaine,1,'C',true);}
                        $semaine_before =$semaine;
						
						$y2 = $this->GetY();
						if($y2<$y){
							$y=$y2-6;
						}
						
						$x = $taillex+ 14;
						$this->SetXY($x,$y);
						$this->SetFont('DejaVu','',10);	
						$this->SetFillColor(250,250,240);			
                        $taillex = $taille_max_libelle*2;                   
						$this->Multicell($taillex,6,$tab_evenements[$indice_results][libelle],0,'L',true);
						
                        $y2 = $this->GetY();
						if($y2<$y){
							$y=$y2-6;
						}
						$x = $x + $taillex + 2;
						$this->SetXY($x,$y);
						$this->SetFillColor(250,250,250);                    
						$this->Multicell(0,6,$tab_evenements[$indice_results][titre],0,'L',true);
                        
						
                        $this->Ln(1);  
						$y = $this->GetY();
						$ville = $tab_evenements[$indice_results][nom_ville];
						$indice_results++;
					}            
						 
					// Restauración de colores y fuentes
					$this->SetFillColor(224,235,255);
					$this->SetTextColor(0);
					$this->SetFont('DejaVu','',10);	
				}	
            }
	}
	
	$pdf= new PDF('P','mm');
    $pdf->SetDisplayMode(fullwidth,continuous);
	$pdf->AliasNbPages(); 
	$pdf->SetMargins(5,50,5); 
	$pdf->AddPage("P","A4"); 
    $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
	$pdf->SetFont('DejaVu','',10);	
	$pdf->duree($dia);
    $fin = anadirDias($dia,9);
    
    //dimension de la caja ville
    $stringville = maxStringVille($dia,$fin);
	$tailleville = $pdf->GetStringWidth($stringville);
    // redimension de la caja
    if($tailleville>50){$tailleville=$tailleville*0.85;}    
    
    //dimension de la caja genre
    $stringgenre = maxStringgenre($dia,$fin);
	$taillegenre = $pdf->GetStringWidth($stringgenre);  
    // redimension de la caja
    if($taillegenre>15){$taillegenre=$taillegenre*0.95;}    
      
    $diactual = $dia;
	 
	for($i=1;$i<11;$i++){		
        $pdf->FancyTable($diactual,$tailleville,$taillegenre);				
		$diactual = anadirDia($diactual);                 
    }

        // Salto a la página siguiente
        $pdf->AddPage();
        $pdf->SetFont('DejaVuB','B',14);	
        $pdf->SetXY(50,35);
        $pdf->SetFillColor(85, 211, 255);
		$pdf->SetTextColor(255);        
        $pdf->Cell(80,10,'Événements de longue durée',0,0,'C',true);
        $pdf->Ln(6);
        $pdf->FancyTableLongueDuree($diactual,anadirDias($diactual,10));
	 
	$pdf->Output(); 

?>