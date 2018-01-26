<?php
// include_once "../../../01_include/_var_ensemble.php";
if(!$previsualisation_validation)
	include_once('../../01_include/_connect.php');
//On récupère la liste des petites annonces pour no_lettre
$requete_liste = "SELECT liste_petiteannonce_valide FROM lettreinfo_petiteannonce WHERE no_lettre=:no_l";
$res_liste = $connexion->prepare($requete_liste);
$res_liste->execute(array(":no_l"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_liste);
$tab_liste = $res_liste->fetchAll();
$liste_valide = $tab_liste[0]["liste_petiteannonce_valide"];
if($liste_valide!=""){
	$requete_petiteannonce = "
		SELECT PA.no AS pa_no_pa,PA.url_image,PA.titre,PA.monetaire,PA.no_ville AS pa_no_ville,
		villes.nom_ville_maj AS ville, villes.nom_ville_url
		FROM petiteannonce PA 
		JOIN villes ON villes.id=PA.no_ville 
		WHERE PA.no IN (".$liste_valide.")";
	$res_petiteannonce = $connexion->prepare($requete_petiteannonce);
	$res_petiteannonce->execute(array(":no_l"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_petiteannonce);
	$liste_petiteannonce = $res_petiteannonce->fetchAll();
	?>
	<img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-28.jpg" width="650px" height="57px" alt="Petites annonces" style="padding-top:20px;padding-bottom:10px;width:650px;" id="petite_annonce" />
	<?php if ($pdf_annonces != '') {
            // on insère le PDF agenda
            ?>
        <div style='text-align:center;'>Si vous le souhaitez, vous pouvez consulter <a href='http://www.ensembleici.fr/02_medias/15_lettreinfo_pdf_annonces/<?= $pdf_annonces ?>' target='_blank'>la liste complète des petites annonces au format PDF</a></div><br/>
        <?php } ?>
        <table style="width:100%;background-color:white;" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td style="text-align:center">
			Un aper&ccedil;u des petites annonces partag&eacute;es sur <a href="http://www.ensembleici.fr">www.ensembleici.fr</a> ! <br/>&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<table style="width:100%;" border="0" cellpadding="0" cellspacing="0">
				<?php
				//On place les structures
				for($i=0;$i<count($liste_petiteannonce);$i++)
				{

					// Préparation du lien
					$lien = $root_site."petiteannonce.".$liste_petiteannonce[$i]["nom_ville_url"].".".url_rewrite($liste_petiteannonce[$i]["titre"]).".".$liste_petiteannonce[$i]["pa_no_ville"].".".$liste_petiteannonce[$i]["pa_no_pa"].".html";

					echo "<tr style=\"border:none;height:40px;border-bottom:1px solid #F0EDEA;border-top:1px solid #F0EDEA;\">
						<td style=\"width:30px;\"></td>";
						
						echo "<td><a href=\"".$lien."\" style=\"text-decoration:none;\" target=\"_blank\">";

						//Si une image existe, on la place.
						/* if($liste_petiteannonce[$i]["url_image"]!=""&&$liste_petiteannonce[$i]["url_image"]!=null){
							//On calcul ces dimmensions, afin de les reregler.
							list($largeur,$hauteur) = getimagesize("http://www.ensembleici.fr/".$liste_petiteannonce[$i]["url_image"]);
							$new_largeur = 80;
							$new_hauteur = $new_largeur*$hauteur/$largeur;
							if($new_hauteur>80){
								$new_hauteur = 80;
								$new_largeur = $new_hauteur*$largeur/$hauteur;
								$decalageX = (80-$new_largeur)/2;
								$decalageY = 0;
							}
							else{
								$decalageX = 0;
								$decalageY = (80-$new_hauteur)/2;
							}
							if(substr($liste_petiteannonce[$i]["url_image"],0,7)!="http://")
								$liste_petiteannonce[$i]["url_image"] = "http://www.ensembleici.fr/".$liste_petiteannonce[$i]["url_image"];
						
						<td style="width:90px;">
							<img src="<?php echo $liste_petiteannonce[$i]["url_image"]; ?>" style="width:<?php echo floor($new_largeur); ?>px;height:<?php echo floor($new_hauteur); ?>px;position:relative;left:<?php echo floor($decalageX); ?>px;top:<?php echo floor($decalageY); ?>px;" />
						</td> 
						
						}
						else{
							echo "<td></td>";
						}*/
						?>
							<span style="color:#b9ba35;font-size:16px;font-weight:bold;"><?php echo $liste_petiteannonce[$i]["titre"]; ?></span>
							<span><?php if ($liste_petiteannonce[$i]["monetaire"]) echo "&nbsp;&nbsp;&nbsp;<img src=\"http://www.ensembleici.fr/img/monetaire.png\" width=\"17px\" height=\"17px\" style=\"width:17px;height:17px;\" title=\"Annonce monétaire\" />"; ?></span>
						</a></td>
						<td style="color:#445158;text-align:center;">
							<?php echo "<a href=\"".$lien."\" style=\"text-decoration:none;\" target=\"_blank\">"; ?>
							<span style="font-weight:bold;"><?php echo $liste_petiteannonce[$i]["ville"]; ?></span><br/>
						</a></td>
						<td style="width:30px;"></td>
					</tr>
					<?php
				}
				?>
				<tr>
					<td style="padding-bottom:10px;padding-top:30px;text-align:center;" colspan="4"><br/>
					<?php
						//$lien_ajouter = $root_site."ajouter_une_petiteannonce.html";
                                                $lien_ajouter = $root_site."espace-personnel.petite-annonce.html";
						echo "<a href=\"".$lien_ajouter."\" style=\"text-decoration:none;\" target=\"_blank\"><img src=\"".$root_site."img/lettreinfo/ajouter_annonce.jpg\" alt=\"Ajouter une annonce\" /></a>";
					?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>
	<br/>
<?php
}
?>
