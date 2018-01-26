<?
header('Content-Type: text/html; charset=utf-8');	
?>

<link href="css/calendrier.css" rel="stylesheet" type="text/css">
<link href="http://www.ensembleici.fr/css/colorbox.css" rel="stylesheet"></link>
<link href="http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,700" type="text/css" rel="stylesheet"></link>
<script src="js/functions.js" type="text/javascript"></script>	


<?php	
	//connexion à la base de données
	require ('php/_connect.php');
	require ('php/funcionesfecha.php');
	
	if(isset($_POST['fecha']) and $_POST['fecha']!=""){
            $fechapivote = $_POST['fecha'];	
    }
    else{
            $hoy = getdate();
            $fechapivote = $hoy[year]."-".$hoy[mon]."-".$hoy[mday];
}

        //consulta a la base de datos para un dia                            
            $requete =  "SELECT  
                    evenement.no as no,
                    genre.no as nogenre, 
                    evenement.titre as titre,
                    genre.libelle as libelle,
                    evenement.no_ville as no_ville,
					evenement.adresse as direccion,
                    villes.nom_ville_maj as nom_ville
                    FROM evenement, villes, genre
                    WHERE date_debut <= :fechaact 					
                    AND date_fin >= :fechaact	
                    AND DATEDIFF(date_fin ,date_debut)<8
                    AND evenement.no_ville = villes.id
                    AND evenement.no_genre = genre.no
                    ORDER BY villes.nom_ville ASC";	

    $primerdia = primerDiaSemana($fechapivote);
    $diactual = $primerdia;
    $ultimodia = ultimoDiaSemana($fechapivote);
	
    echo "<div class=\"entete\">";
		echo "<div class=\"bandeau\"></div>";
		echo "<div class=\"texte\">";
			echo "Retrouvez le détail de l'agenda local sur le site <a href=\"http://www.ensembleici.fr\" target=\"_blank\" style=\"text-decoration: underline;\">www.ensembleici.fr</a> !</br>&nbsp;</br>
				Il vous est également aisé d'ajouter des informations via les formulaires du site !</br>";
		echo "</div>";
      
	echo "</div>";
	echo "<div>";
		
	    echo "<ul class=\"button-group\">";
			echo "<li><a href=\"agenda.php\" class=\"small button\">Mensuel</a></li>";
			echo "<li><a href=\"#\" onclick class=\"small button buttonactive\">Hebdomadaire</a></li>";
		echo "</ul>";


       // echo "<div class=\"enlaces position2\" style=\"display:none\"><a href=\"php/pdf.php?dia=".$diactual."\" target=\"_blank\">version imprimable</a></div>";        
                    $hui = getdate();
            $aujourdhui = $hui[year]."-".$hui[mon]."-".$hui[mday];
       // echo "<div class=\"enlaces position1\"><a class=\"versionImprimable\" href=\"php/pdf.php?dia=". $aujourdhui."\" target=\"_blank\">Version imprimable des dix prochains jours</a></div>";    
        
		
		echo "<ul class=\"button-group position1 \">";
			echo "<li><a  href=\"php/pdf.php?dia=".$primerdia."\" target=\"_blank\" class=\"small button2\">Imprimer</a></li>";			
		echo "</ul>";
	echo "</div>";
	echo "<div class=\"enlaces position2\">(L'agenda des dix prochains jours)</div>";    
	
	echo "<div class=\"divgeneral\">";
		echo "<table cellspacing=\"0px\" class=\"calendar2\">";
			echo "<tr class=\"cabecera1\">";
				echo "<th class=\"noborder jourdelasemaine fleches\">";
							echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name=\"semainePrecedente\">";
							echo "<input type=\"hidden\" name=\"fecha\" value=\"".SemainePrec($primerdia)."\">";
							echo "<input class=\"nav\" style=\"font-size:30px;\" type=\"image\" onclick=\"formSubmit()\" value=\"<\">";
							echo "</form>";
				echo"</th>";
				echo "<th colspan=\"3\" class=\"noborder cabecera1\">Semaine  <br/>du ".invertirFechas($primerdia)."   au ".invertirFechas($ultimodia)."</th>";
				echo "<th class=\"noborder jourdelasemaine fleches\">";
							echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name=\"semaineSuivante\">";
							echo "<input type=\"hidden\" name=\"fecha\" value=\"".SemaineSuiv($ultimodia)."\">";
							echo "<input class=\"nav\" style=\"font-size:30px;\" type=\"image\" onclick=\"formSubmit()\" value=\">\"></input>";
							echo "</form>";
				echo"</th>";
			echo "</tr>";           
			
			for($i=1;$i<8;$i++){
				echo"<tr>";
					if($i % 2 === 0){
						$fondfonce = "fondfonce";
					}
					else{
						$fondfonce = "fondfonce2";
					}
					
					$fechasinvertidas = explode("-",invertirFechas($diactual));
					echo"<td class=\"jourdelasemaine borderleft cabecera2\">".nomJourSemaine($diactual)."</br>".$fechasinvertidas[0]."</td>";
					 echo"<td colspan=\"4\" class=\"celuleevtslongueduree ".$fondfonce."\">";                         
						$results_evenements = $connexion->prepare($requete);
						$results_evenements->execute(array(':fechaact'=>$diactual)) or die ("requete ligne 60: ".$requete);
						$tab_evenements = $results_evenements->fetchAll(PDO::FETCH_ASSOC);    
						
						if(count($tab_evenements)>0){
							$indice_results = 0;											
							while($tab_evenements[$indice_results]){
								echo "<a href=\"http://www.ensembleici.fr/auto_previsu.php?id_ville=".$tab_evenements[$indice_results][no_ville]."&no_fiche=".$tab_evenements[$indice_results][no]."&type=evenement&liste=1\" target=\"_blank\">";
								echo $tab_evenements[$indice_results][titre];
								echo "</a>";
								
								$salida = $tab_evenements[$indice_results][libelle];
								if ($tab_evenements[$indice_results][direccion] && $tab_evenements[$indice_results][direccion]!=""){
													$salida = $salida.", ".$tab_evenements[$indice_results][direccion];
												}
								$salida = $salida.", ".$tab_evenements[$indice_results][nom_ville];
								
								echo $salida;	
								echo "</br>&nbsp;</br>";								
								$indice_results++;
							}									
						}
						else{
							echo "&nbsp;";
						}						
					 echo"</td>";            
				echo"</tr>";
				$diactual = anadirDia($diactual);
			}    
			echo "</table>";		

		// creation de la table des evenements longue duree
		echo "<table class=\"evtslongueduree evtslonguedureehebdo\">";
			echo "<tr class=\"cabecera1\">";
			  echo "<th class=\"noborder cabecera1\">Événements de longue durée</th>";
			echo "</tr>";	
			
			// Iniciamos variable flag para fondo alternativo
			$falgfond = 1;
			
			$requete =  "SELECT evenement.no as no,
								genre.no as nogenre, 
								evenement.titre as titre,
								genre.libelle as libelle,
								evenement.no_ville as no_ville,
								villes.nom_ville_maj as nom_ville,
								evenement.adresse as direccion,
								evenement.date_debut as debut,
								evenement.date_fin as fin
								FROM evenement, villes, genre
								WHERE date_debut <= :fin 					
								AND date_fin >= :inicio	
								AND DATEDIFF(date_fin ,date_debut)>7
								AND evenement.no_ville = villes.id
								AND evenement.no_genre != 16
								AND evenement.no_genre = genre.no
								ORDER BY villes.nom_ville ASC";		
								
			$results_recurrents = $connexion->prepare($requete);
			$results_recurrents->execute(array(':inicio'=>$primerdia,':fin'=>$ultimodia)) or die ("requete ligne 110: ".$requete);
			$tab_recurrents = $results_recurrents->fetchAll(PDO::FETCH_ASSOC);
			
			if(count($tab_recurrents)>0){
				$indice_results = 0;
				
				while($tab_recurrents[$indice_results]){
					// fond alternative
									if($falgfond == 0){
										$fondfonce = "fondfonce";
										$falgfond = 1;
									}
									else{
										$fondfonce = "fondfonce2";
										$falgfond = 0;
									}								
					echo "<tr><td class=\"celuleevtslongueduree borderleft ".$fondfonce."\">";									
					echo "<a href=\"http://www.ensembleici.fr/auto_previsu.php?id_ville=".$tab_recurrents[$indice_results][no_ville]."&no_fiche=".$tab_recurrents[$indice_results][no]."&type=evenement&liste=1\" target=\"_blank\">";
					echo $tab_recurrents[$indice_results][titre];
					echo "</a>";					
					$salida = $tab_recurrents[$indice_results][libelle];
					if ($tab_recurrents[$indice_results][direccion] && $tab_recurrents[$indice_results][direccion]!=""){
										$salida = $salida.", ".$tab_recurrents[$indice_results][direccion];
									}		
					$salida = $salida.", ".$tab_recurrents[$indice_results][nom_ville];									
					echo $salida;	
						//	echo "</br>&nbsp;</br>";
				//	echo diffdays($tab_recurrents[$indice_results][debut],$tab_recurrents[$indice_results][fin]);
					echo "</br>";
					if(diffdays($tab_recurrents[$indice_results][debut],$tab_recurrents[$indice_results][fin])<300){
						echo "(du ".invertirFechas($tab_recurrents[$indice_results][debut])." au ".invertirFechas($tab_recurrents[$indice_results][fin]).")";			
					}
					else{
						echo "(toute l'année)";
					}
					$indice_results++;
					echo "</td></tr>";
				}							
			}	
		echo "</table>";	
	echo "</div>";
?>