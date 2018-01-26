<?
// header('Content-Type: text/html; charset=iso-8859-1');
header('Content-Type: text/html; charset=utf-8');	
?>

<link href="css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
<link href="css/calendrier.css" rel="stylesheet" type="text/css">
<link href="http://www.ensembleici.fr/css/colorbox.css" rel="stylesheet"></link>
<link href="http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,700" type="text/css" rel="stylesheet"></link>
<script src="js/functions.js" type="text/javascript"></script>	
<script src="http://www.ensembleici.fr/js/jquery.1.8.3.min.js"></script>
<script src="http://www.ensembleici.fr/js/jquery.colorbox-min.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="js/jquery.mCustomScrollbar.concat.min.js"></script>

<script>
    (function($){
        $(window).load(function(){
            $(".divcontenido").mCustomScrollbar();
        });
    })(jQuery);
</script>

<script type="text/javascript"> 
	  $(document).ready(function(){
			/*$(".inline").colorbox({html: $($(this).attr('rel')).html() }); */
			$(".inline").colorbox({inline:true, width:"50%"});
		});	
</script>

<?php	
	//connexion à la base de données
	require ('php/_connect.php');
	require ('php/funcionesfecha.php');
	
	$fecha = getdate();
	$diasemana = $fecha[wday]; // este es mi puntero
	$diames = $fecha[mday]; 
	$mes = $fecha[mon];
	$meshoy = $mes;
	$anio = $fecha[year];
	$aniohoy = $anio;
	
	echo "<div class=\"entete\">";
		echo "<div class=\"bandeau\"></div>";
		echo "<div class=\"texte\">";		
			echo "Retrouvez le détail de l'agenda local sur le site <a href=\"http://www.ensembleici.fr\" target=\"_blank\" style=\"text-decoration: underline;\">www.ensembleici.fr</a> !</br>&nbsp;</br>
				Il vous est également aisé d'ajouter des informations via les formulaires du site !</br>";
		echo "</div>";
	echo "</div>";
	
	echo "<div>";
		
	    echo "<ul class=\"button-group\">";
			echo "<li><a href=\"#\" class=\"small button buttonactive\">Mensuel</a></li>";
			echo "<li><a href=\"hebdo.php\" class=\"small button\">Hebdomadaire</a></li>";
		echo "</ul>";
				          
            $hui = getdate();
            $aujourdhui = $hui[year]."-".$hui[mon]."-".$hui[mday];   
		
		echo "<ul class=\"button-group position1 \">";
			echo "<li><a  href=\"php/pdf.php?dia=". $aujourdhui."\" target=\"_blank\" class=\"small button2\">Imprimer</a></li>";			
		echo "</ul>";
	echo "</div>";
	echo "<div class=\"enlaces position2\">(L'agenda des dix prochains jours)</div>";

	echo "</div>";
		for ($i=1;$i<13;$i++){

			if($mes>12){
				$mes = $mes % 12;
				$anio++;
			}
			
			// initialisation du style de la div
			if($mes==$meshoy && $anio==$aniohoy){
				$estilo="display: inline";
			}
			else{
				$estilo="display: none";
			}
			
			// nombre de jours qu'il y a dans le mois actuel et jour de la semaine par lequel commence le mois
			$numerodiasmes = getMonthDays($mes,$anio);
			$diasemana = diaDeLaSemana($mes,$anio);
			
			$div_act = "calendrier_".$mes."_".$anio;
			
			// div_prec
			$mes_prec = 0;
			$anio_prec = 0;
			if($mes == $meshoy && $anio == $aniohoy){
				$mes_prec = $mes;
				$anio_prec = $anio;
			}
			else{
				if($mes==1){
					$mes_prec = 12;
					$anio_prec = $anio - 1;
				}
				else{
					$mes_prec = $mes - 1;
					$anio_prec = $anio;
				}
			}
			$div_prec = "calendrier_".$mes_prec."_".$anio_prec;			
			
			// div_suiv		
			$mes_suiv = 0;
			$anio_suiv = 0;			
			
			$mes_ultimo = $meshoy-1;
			$anio_ultimo = $aniohoy;
			if($mes_ultimo == 0){
				$mes_ultimo = 12;
			}
			else{
				$anio_ultimo = $anio_ultimo + 1;
			}
			
			if($anio == $anio_ultimo && $mes == $mes_ultimo){
				$mes_suiv = $mes;
				$anio_suiv = $anio;
			}
			else{
				$mes_suiv = $mes + 1;
				$anio_suiv = $anio;
				if($mes_suiv == 13){
					$mes_suiv = 1;
					$anio_suiv = $anio + 1;
				}			
			}
			$div_suiv = "calendrier_".$mes_suiv."_".$anio_suiv;		
				
			echo "<div style=\"".$estilo."\" class=\"divgeneral\" id=\"".$div_act."\">";
				// creation de la table du mois
				echo "<table class=\"calendar borderleft\" cellspacing=\"0px\">";
					echo "<tr class=\"cabecera1\">";
					  echo "<th><a class=\"nav fleches\" href=\"#\" onclick=\"activerDesactiverDisplay('".$div_prec."','".$div_act."');\"> < </a></th>";
					  echo "<th colspan=\"5\" class=\"cabecera1\">".utf8_decode(nomDuMois($mes))." ".$anio."</th>";
					  echo "<th>";					  
					  echo "<a class=\"nav\" href=\"#\" onclick=\"activerDesactiverDisplay('".$div_suiv."','".$div_act."');\" style=\"font-size: 30px; font-weight: bolder;\"> > </a>";
					  echo "</th>";
					echo "</tr>";
					echo "<tr class=\"cabecera2 mois\">";
						echo "<th>Lundi</th>";
						echo "<th>Mardi</th>";
						echo "<th>Mercredi</th>";
						echo "<th>Jeudi</th>";
						echo "<th>Vendredi</th>";
						echo "<th>Samedi</th>";
						echo "<th>Dimanche</th>";
					echo "</tr>";
					
					$contmois=1;
					while($contmois<$numerodiasmes){
						echo "<tr>";	
						$contsem=1;		
						while($contsem<8){	
							if (($contsem < $diasemana && $contmois==1)||($contmois>$numerodiasmes)){
							$afficher = "";
							$fondfonce = "";
							}
							else {
								// creation du string fechaDeLaCelda '2010-12-31'
								$fechaDeLaCelda = $anio."-".$mes."-".$contmois;
								$afficher = $contmois;
								
								// fond alternative
									if($contmois % 2 === 0){
										$fondfonce = "fondfonce";
									}
									else{
										$fondfonce = "fondfonce2";
									}								
								}														
								
							// requete a la base de donnees et insertion dans la celule								
							echo "<td><div class=\"divcelda ".$fondfonce."\">";
								echo "<span class=\"numerito\" >".$afficher."</span></br>&nbsp;</br>";
								echo "<div class=\"divcontenido\" id=\"".$fechaDeLaCelda."\">";
								
								if ($afficher!=""){
										$requete =  "SELECT  
										evenement.no as no,
										genre.no as nogenre, 
										evenement.titre as titre,
										evenement.nomadresse as direccion,
										genre.libelle as libelle,
										evenement.no_ville as no_ville,
										villes.nom_ville_maj as nom_ville
										FROM evenement, villes, genre
										WHERE date_debut <= :fechaact 					
										AND date_fin >= :fechaact	
										AND DATEDIFF(date_fin ,date_debut)<15
										AND evenement.no_ville = villes.id
										AND evenement.no_genre = genre.no
										ORDER BY villes.nom_ville ASC";						
										
										$results_evenements = $connexion->prepare($requete);
										$results_evenements->execute(array(':fechaact'=>$fechaDeLaCelda)) or die ("requete ligne 60: ".$requete);
										$tab_evenements = $results_evenements->fetchAll(PDO::FETCH_ASSOC);
										
										if(count($tab_evenements)>0){
											$indice_results = 0;
											
											while($tab_evenements[$indice_results]){
												echo "<a href=\"http://www.ensembleici.fr/auto_previsu.php?id_ville=".$tab_evenements[$indice_results][no_ville]."&no_fiche=".$tab_evenements[$indice_results][no]."&type=evenement&liste=1\" target=\"_blank\">";
												echo $tab_evenements[$indice_results][titre];
												echo "</a>";
												$salida = $tab_evenements[$indice_results][libelle];											
												$salida = $salida.", ".$tab_evenements[$indice_results][nom_ville];
												echo $salida;
												echo "</br>&nbsp;</br>";
												$indice_results++;
											}									
										}
									$contmois++;	
								}
							echo "</div></div></td>";
							$contsem++;
						}		
						echo "</tr>";
					}	
				echo "</table>";					
				
				// creation de la table des evenements recurrents du mois
				echo "<table class=\"evtslongueduree\" cellspacing=\"0px\">";
					echo "<tr>";
					  echo "<th class=\"cabecera1\">Événements de longue durée</th>";
					echo "</tr>";	
					
					// Iniciamos variables
					$fecha_inicio = $anio."-".$mes."-1";
					$fecha_fin = $anio."-".$mes."-".$numerodiasmes;
					$falgfond = 1;
					
					$requete =  "SELECT evenement.no as no,
										genre.no as nogenre, 
										evenement.titre as titre,
										genre.libelle as libelle,
										evenement.no_ville as no_ville,
										villes.nom_ville_maj as nom_ville
										FROM evenement, villes, genre
										WHERE date_debut <= :fin 					
										AND date_fin >= :inicio	
										AND DATEDIFF(date_fin ,date_debut)>14
										AND evenement.no_ville = villes.id
										AND evenement.no_genre != 16
										AND evenement.no_genre = genre.no
										ORDER BY villes.nom_ville ASC";		
										
					$results_recurrents = $connexion->prepare($requete);
					$results_recurrents->execute(array(':inicio'=>$fecha_inicio,':fin'=>$fecha_fin)) or die ("requete ligne 98: ".$requete);
					$tab_recurrents = $results_recurrents->fetchAll(PDO::FETCH_ASSOC);
					
					if(count($tab_recurrents)>0){
						$indice_results = 0;
						
						while($tab_recurrents[$indice_results]){
							// fond alternative
									if($falgfond === 0){
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
							echo $tab_recurrents[$indice_results][libelle].", ".$tab_recurrents[$indice_results][nom_ville];
							echo "</br></hr>";
							$indice_results++;
							echo "</td></tr>";
						}							
					}	
				echo "</table>";		
			echo "</div>";	
			$mes++;	
		}
?>



