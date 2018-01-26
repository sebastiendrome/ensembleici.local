<?php
if(!$previsualisation_validation)
	include_once('../../01_include/_connect.php');
//On r�cup�re la liste deqs �v�nements pour no_lettre
$requete_liste = "SELECT liste_structure_valide FROM lettreinfo_repertoire WHERE no_lettre=:no_l";
$res_liste = $connexion->prepare($requete_liste);
$res_liste->execute(array(":no_l"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_liste);
$tab_liste = $res_liste->fetchAll();
$liste_valide = $tab_liste[0]["liste_structure_valide"];
if($liste_valide!=""){
	$requete_structure = "
		SELECT S.no AS st_no_st,S.url_logo,S.nom,S.sous_titre,S.no_ville AS st_no_ville,
			statut.libelle AS statut, 
			villes.nom_ville_maj AS ville, villes.nom_ville_url
		FROM structure S
			JOIN statut ON statut.no=S.no_statut 
			JOIN villes ON villes.id=S.no_ville 
		WHERE S.no IN (".$liste_valide.")";
	$res_structure = $connexion->prepare($requete_structure);
	$res_structure->execute(array(":no_l"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_structure);
	$liste_structures = $res_structure->fetchAll();
	?>
	<img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-30.jpg" width="650" height="57" alt="R�pertoire" style="padding-top:20px;padding-bottom:10px;width:650px;" id="repertoire" />
	<table style="width:100%;background-color:white;" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td style="text-align:center">
			Un aper&ccedil;u du r&eacute;pertoire partag&eacute; sur <a href="http://www.ensembleici.fr">www.ensembleici.fr</a> ! <br/>&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<table style="width:100%;" border="0" cellpadding="0" cellspacing="0">
					<?php
					//On place les structures
					for($i=0;$i<count($liste_structures);$i++)
					{

						// Pr�paration du lien
						$lien = $root_site."structure.".$liste_structures[$i]["nom_ville_url"].".".url_rewrite($liste_structures[$i]["nom"]).".".$liste_structures[$i]["st_no_ville"].".".$liste_structures[$i]["st_no_st"].".html";


					echo "<tr style=\"border:none;height:50px;border-bottom:1px solid #F0EDEA;border-top:1px solid #F0EDEA;\">
						<td style=\"width:30px;\"></td>";
						
						echo "<td><a href=\"".$lien."\" style=\"text-decoration:none;\" target=\"_blank\">";

						/*
							// Si une image existe, on la place.
							if($liste_structures[$i]["url_logo"]!=""&&$liste_structures[$i]["url_logo"]!=null){
								//On calcul ces dimmensions, afin de les reregler.
								list($largeur,$hauteur) = getimagesize("http://www.ensembleici.fr/".$liste_structures[$i]["url_logo"]);
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
								if(substr($liste_structures[$i]["url_logo"],0,7)!="http://")
									$liste_structures[$i]["url_logo"] = "http://www.ensembleici.fr/".$liste_structures[$i]["url_logo"];
							
							<td style="width:90px;">
								<img src="<?php echo $liste_structures[$i]["url_logo"]; ?>" style="width:<?php echo floor($new_largeur); ?>px;height:<?php echo floor($new_hauteur); ?>px;margin:auto;margin-left:<?php echo $margin_x; ?>px;" />
							</td>
							}
							else{
								echo "<td></td>";
							}
							*/ 
							?>
								<span style="color:#F6AE48;font-size:16px;font-weight:bold;"><?php echo $liste_structures[$i]["nom"]; ?></span>
								<br/>
								<span><?php echo $liste_structures[$i]["sous_titre"]; ?></span>
							</a></td>
							<td style="color:#445158;text-align:center;">
							<?php echo "<a href=\"".$lien."\" style=\"text-decoration:none;\" target=\"_blank\">"; ?>
								<strong style="font-weight:bold;"><?php echo $liste_structures[$i]["ville"]; ?></strong><br/>
								<?php if($liste_structures[$i]["statut"]!="Autre"){ ?><span><?php echo $liste_structures[$i]["statut"]; ?></span><?php } ?>
							</a></td>
							<td style="width:30px;"></td>
						</tr>
						<?php
					}
					?>
					<tr>
						<td style="padding-bottom:10px;padding-top:30px;text-align:center;" colspan="4"><br/>
						<?php
							//$lien_ajouter = $root_site."ajouter_une_structure.html";
                                                        $lien_ajouter = $root_site."espace-personnel.structure.html";
							echo "<a href=\"".$lien_ajouter."\" style=\"text-decoration:none;\" target=\"_blank\"><img src=\"".$root_site."img/lettreinfo/ajouter_structure.jpg\" alt=\"Ajouter une structure\" /></a>";
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
