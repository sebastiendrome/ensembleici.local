<?php
if(!$previsualisation_validation)
	include('../../01_include/_connect.php');
//On récupère la liste des événements pour no_lettre
$requete_liste = "SELECT liste_evenement_valide FROM lettreinfo_agenda WHERE no_lettre=:no_l";
$res_liste = $connexion->prepare($requete_liste);
$res_liste->execute(array(":no_l"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_liste);
$tab_liste = $res_liste->fetchAll();
$liste_valide = $tab_liste[0]["liste_evenement_valide"];
if($liste_valide!=""){
	$requete_agenda = "SELECT
							E.no AS evt_no_evt,E.url_image,E.titre,E.sous_titre,E.date_debut,E.date_fin, E.no_ville AS evt_no_ville,
							genre.libelle AS genre,
							genre.type_genre AS a_e,
							villes.nom_ville_maj AS ville,
							villes.nom_ville_url,
							IFNULL(communautecommune_ville.no_communautecommune,0) AS no_cc,
							IFNULL(communautecommune.libelle,'[]') AS lib_cc
						FROM evenement E
						JOIN villes ON villes.id=E.no_ville
						JOIN genre ON genre.no=E.no_genre
						LEFT JOIN communautecommune_ville ON communautecommune_ville.no_ville = villes.id
						LEFT JOIN communautecommune ON communautecommune_ville.no_communautecommune = communautecommune.no
						WHERE E.no IN (".$liste_valide.")
						ORDER BY lib_cc,E.date_debut,E.date_fin,villes.nom_ville_maj";
	$res_agenda = $connexion->prepare($requete_agenda);
	$res_agenda->execute(array(":no_l"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_agenda);
	// $liste_agenda_temp = $res_agenda->fetchAll();
	$liste_agenda = $res_agenda->fetchAll();

	if(!isset($previsualisation_validation)||!$previsualisation_validation)
	{
		?>
		<style type="text/css">
		table{font-family:'Gill Sans',Corbel,Tahoma,'sans-serif';}
		</style>
		<?php
	}
	?><img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-24.jpg" width="650px" height="57" alt="Evènements" style="padding-top:20px;padding-bottom:10px;width:650px;" id="evenement" />
	
        <?php if ($pdf_agenda != '') {
            // on insère le PDF agenda
            ?>
        <div style='text-align:center;'>Si vous le souhaitez, vous pouvez consulter <a href='http://www.ensembleici.fr/02_medias/14_lettreinfo_pdf_agenda/<?= $pdf_agenda ?>' target='_blank'>l'agenda complet au format PDF </a></div><br/>
        <?php } ?>
        
        <table style="width:100%;background-color:white;" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<table style="width:100%;" border="0" cellpadding="0" cellspacing="0">
				<?php
				//On place les événéments
				$communaute_commune_courante = -1;
				$colonne = 0; // colonne du td
				$premiere_cc = true; // premiere comunauté de communes

				for($i=0;$i<count($liste_agenda);$i++)
				{
					$colonne++;
					if ($colonne > 3) $colonne = 1;

					if($communaute_commune_courante!=$liste_agenda[$i]["no_cc"]) 
					{
						if ($colonne != 1)
						{
							// complete la ligne
							$reste_col = 3 - $colonne; //[Modification Sam 04/07/13] (il y avait écrit 4 - $colonne) or il n'y a que 3 colonnes
							while ($reste_col) {
								echo "<td width='33%' style='width:33%;padding:10px;border-bottom:1px solid #E3D6C7;'>&nbsp;</td>";
								$reste_col--;
							}
							echo "</tr>";
							$colonne = 1;
						}

						$communaute_commune_courante=$liste_agenda[$i]["no_cc"];
						if ($premiere_cc) { $bordcc = "2"; $premiere_cc = false; } else $bordcc = "1";
						echo '<tr><td colspan="3" style="height:40px;background-color:#F0EDEA;border-bottom:2px solid #E3D6C7;border-top:'.$bordcc.'px solid #E3D6C7;font-weight:bold;color:#445158;padding-left:20px;padding-top:3px;font-size:17px;text-transform:uppercase;">';
							if($liste_agenda[$i]["lib_cc"]!="[]") echo trim($liste_agenda[$i]["lib_cc"]); else echo "Ailleurs dans le coin";
						echo '</td></tr>';
					}

					// Préparation du lien
					$lien = $root_site."evenement.".$liste_agenda[$i]["nom_ville_url"].".".url_rewrite($liste_agenda[$i]["titre"]).".".$liste_agenda[$i]["evt_no_ville"].".".$liste_agenda[$i]["evt_no_evt"].".html";

					// Nouvelle ligne ?
					if ($colonne == 1)
						echo "<tr>";

						if ($colonne == 3) 
							$borderl = "";
						else
							$borderl = "border-right:1px solid #E3D6C7;";

						echo "<td width='33%' style='width:33%;vertical-align:top;padding:10px;border-bottom:1px solid #E3D6C7;".$borderl."'>";
						echo "<a href=\"".$lien."\" style=\"text-decoration:none;\" target=\"_blank\">";
						
						// conteneur de l'img
						echo "<div style='width:196px;height:120px;overflow:hidden;vertical-align:middle;'>";

						// Si une image existe, on la place.
						if($liste_agenda[$i]["url_image"]!=""&&$liste_agenda[$i]["url_image"]!=null) 
						{
							if(substr($liste_agenda[$i]["url_image"],0,7)!="http://")
								$liste_agenda[$i]["url_image"] = "http://www.ensembleici.fr/".$liste_agenda[$i]["url_image"];
							
							// Dimmensions img
							list($largeur,$hauteur) = getimagesize($liste_agenda[$i]["url_image"]);
							if($largeur!=0&&$hauteur!=0){
								$largeur_img = 196;
								$hauteur_img = 120;

								$new_largeur = $largeur_img;
								$new_hauteur = $new_largeur*$hauteur/$largeur;
								if($new_hauteur>$largeur_img){
									$new_hauteur = $largeur_img;
									$new_largeur = $new_hauteur*$largeur/$hauteur;
								}
								// centrer horizontalement
								if($new_largeur<$largeur_img)
									$margin_x = floor(($largeur_img-$new_largeur)/2);
								else
									$margin_x = 0;

								// centrer vertivalement (en + ou en -)
								$margin_y = floor(($hauteur_img-$new_hauteur)/2);
							}
							else{
								$liste_agenda[$i]["url_image"] = "http://www.ensembleici.fr/img/logo-ensembleici_nl.jpg";
								$new_hauteur = 120;
								$new_largeur = 196;
							}

						?>
							<img src="<?php echo $liste_agenda[$i]["url_image"]; ?>" style="width:<?php echo floor($new_largeur); ?>px;height:<?php echo floor($new_hauteur); ?>px;margin:<?php echo $margin_y; ?>px auto 0 <?php echo $margin_x; ?>px;" />
						<?php
						}
						else
						{
							// Image toujours non existante ? => image par défaut
							echo "<img src=\"http://www.ensembleici.fr/img/logo-ensembleici_nl.jpg\" style=\"width:196px;height:120px;margin:0;\" />";
						}
						echo "</div><br/>" // fin img

						?>
							
								<span style="font-size:16px;font-weight:bold;color:#E75B54;"><?php echo $liste_agenda[$i]["titre"]; ?></span>
								<br/><span style="color:#445158;"><?php echo $liste_agenda[$i]["genre"]; ?> , <?php echo $liste_agenda[$i]["ville"]; ?></span><br/>
								<strong style="color:black;font-weight:bold;font-size:11px;"><?php echo affiche_date_evt($liste_agenda[$i]["date_debut"],$liste_agenda[$i]["date_fin"]); ?></strong>
						</a></td>

					<?php
					if ($colonne == 3)
						echo "</tr>";

				} // Fin for

				if ($colonne < 3) {
					// complete la ligne
					$reste_col = 3 - $colonne;
					while ($reste_col) {
						echo "<td width='33%' style='width:33%;padding:30px;border-bottom:1px solid #E3D6C7;font-style:italic; color:#E16A0C'><a style='font-style:italic;color:#E16A0C;text-decoration:none;display: block;width:100%' href='http://www.ensembleici.fr'>Retrouvez l'agenda complet sur www.ensembleici.fr...</a></td>";
						$reste_col--;
					}
					echo "</tr>";				
				}

				?>
				</table>
			</td>
		</tr>
		<tr>
			<td style="padding-bottom:10px;padding-top:30px;text-align:center;"><br/>
				<?php
					/* $lien_cours = $root_site."lettreinfos.marche-brocante.tag.[**idv**].1356529681.1.html";
					echo "<a href=\"".$lien_cours."\" style=\"text-decoration:none;\" target=\"_blank\"><img src=\"".$root_site."img/lettreinfo/voir_cours.jpg\" alt=\"Voir les cours\" width=\"168\" style=\"width:168px;margin:0;\" /></a>";*/
					$lien_marches = $root_site."lettreinfos.marche-brocante.tag.[**idv**].1356529681.1.html";
					echo "<a href=\"".$lien_marches."\" style=\"text-decoration:none;\" target=\"_blank\"><img src=\"".$root_site."img/lettreinfo/voir_marches.jpg\" alt=\"Voir les marchés\" width=\"266px\" style=\"width:266px;margin:0;\" /></a>";
					$lien_agenda = $root_site."lettreinfos.[**idv**].tout.agenda.html";
					echo "<a href=\"".$lien_agenda."\" style=\"text-decoration:none;\" target=\"_blank\"><img src=\"".$root_site."img/lettreinfo/voir_agenda.jpg\" alt=\"Voir tout l'agenda\" width=\"216px\" style=\"width:216px;margin:0;\" /></a>";
					//$lien_ajouter = $root_site."ajouter_un_evenement.html";
                                        $lien_ajouter = $root_site."espace-personnel.agenda.html";
					echo "<br/><br/><a href=\"".$lien_ajouter."\" style=\"text-decoration:none;\" target=\"_blank\"><img src=\"".$root_site."img/lettreinfo/ajouter_evenement.jpg\" alt=\"Ajouter un événement\" /></a>";
				?>
			</td>
		</tr>
	</table>
	<br/>
<?php
}
/*
if(count($liste_agenda_rec)>0){
?>
	<table style="width:100%;background-color:white;" id="stage_cours_atelier">
		<tr>
			<td style="witdh:100%;text-align:right;font-size:22px;color:#E75B54;background-color:#445158;padding:5px;border-top:5px solid #E75B54;">
				<img src="http://www.ensembleici.fr/img/puce-titre.png" style="vertical-align:middle;" />&nbsp;&nbsp;Stages, cours et ateliers
			</td>
		</tr>
		<tr>
			<td>
				<table style="width:100%;">
				<?php
				//On place les événéments
				$communaute_commune_courante = -1;
				for($i=0;$i<count($liste_agenda_rec);$i++){
					if($communaute_commune_courante!=$liste_agenda_rec[$i]["no_cc"]){
						$communaute_commune_courante=$liste_agenda_rec[$i]["no_cc"];
					?>
					<tr style="height:30px;"><td colspan="5" style="background-color:#F0EDEA;font-weight:bold;color:#445158;text-indent:10px;font-size:16px;"><?php if($liste_agenda_rec[$i]["lib_cc"]!="[]") echo $liste_agenda_rec[$i]["lib_cc"]; else echo "Ailleurs dans le coin"; ?></td></tr>
					<?php
					}
					?>
					<tr style="border:none;height:80px;border-bottom:1px solid #F0EDEA;border-top:1px solid #F0EDEA;">
						<a href="" style="text-decoration:none;" target="_blank">
							<td style="width:30px;">
							</td>
							<?php
							//Si une image existe, on la place.
							if($liste_agenda_rec[$i]["url_image"]!=""&&$liste_agenda_rec[$i]["url_image"]!=null){
								if(substr($liste_agenda_rec[$i]["url_image"],0,7)!="http://")
									$liste_agenda_rec[$i]["url_image"] = "http://www.ensembleici.fr/".$liste_agenda_rec[$i]["url_image"];
								//On calcul ces dimmensions, afin de les reregler.
								list($largeur,$hauteur) = getimagesize($liste_agenda_rec[$i]["url_image"]);
								$new_largeur = 80;
								$new_hauteur = $new_largeur*$hauteur/$largeur;
								if($new_hauteur>80){
									$new_hauteur = 80;
									$new_largeur = $new_hauteur*$largeur/$hauteur;
								}
								if($new_largeur<80)
									$margin_x = (80-$new_largeur)/2;
								else
									$margin_x = 0;
							?>
									<td style="width:90px;">
										<img src="<?php echo $liste_agenda_rec[$i]["url_image"]; ?>" style="width:<?php echo floor($new_largeur); ?>px;height:<?php echo floor($new_hauteur); ?>px;margin:auto;margin-left:<?php echo $margin_x; ?>px;" />
									</td>
							<?php
							}
							else{
								echo "<td></td>";
							}
							if($liste_agenda_rec[$i]["date_debut"]==$liste_agenda_rec[$i]["date_fin"])
								$date = "le <b>".datefr($liste_agenda_rec[$i]["date_debut"])."</b>";
							else
								$date = "du <b>".datefr($liste_agenda_rec[$i]["date_debut"])."</b> au <b>".datefr($liste_agenda_rec[$i]["date_fin"])."</b>";
							?>
							<td>
								<a href="test.php" style="text-decoration:none;color:none;" target="_blank">
									<span style="font-size:15px;font-weight:bold;color:#E75B54;"><?php echo $liste_agenda_rec[$i]["titre"]; ?></span>
									<br/><span style="text-indent:10px;color:#445158;"><?php echo $liste_agenda_rec[$i]["genre"]; ?></span>
								</a>
							</td>
							<td style="width:180px;text-align:center;color:#445158;">
								<span style="font-weight:bold;"><?php echo $liste_agenda_rec[$i]["ville"]; ?></span><br/>
								<span><?php echo $date; ?></span>
							</td>
							<td style="width:30px;">
							</td>
						</a>
					</tr>
				<?php
				}
				?>
				</table>
			</td>
		</tr>
	</table>
	<br/>
<?php
}
*/
?>
