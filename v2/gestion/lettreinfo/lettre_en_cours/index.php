<?php
$no_lettre = intval($_GET["no"]);
require_once "../../../01_include/_var_ensemble.php";
include_once('../../../01_include/_connect.php');
$previsualisation_validation = true;
$root_site = str_replace("v2/","",$root_site);

// Infos de la lettre
$requete_l = "SELECT * FROM lettreinfo WHERE no=:no_l";
$res_l = $connexion->prepare($requete_l);
$res_l->execute(array(":no_l"=>$no_lettre)) or die ("requete ligne 10 : ".$requete_l);
$tab_l = $res_l->fetchAll();
$lettre_objet = $tab_l[0]["objet"];
$lettre_date_debut = $tab_l[0]["date_debut"];
$lettre_repertoire = $tab_l[0]["repertoire"];
$lettre_territoire = $tab_l[0]["territoires_id"];

$pdf_agenda = $tab_l[0]["pdf_agenda"];
$pdf_annonces = $tab_l[0]["pdf_annonces"];

// Dates à afficher sur la lettre
$date_debut = strtotime($lettre_date_debut);
$date_fin = $date_debut+10*24*60*60; //+10 jours
$aff_date = "Du ".date("d", $date_debut);
// Affichage conditionnel du mois et de l'année
if ((date("m", $date_debut) != date("m", $date_fin)) || (date("Y", $date_debut) != date("Y", $date_fin)) )
	$aff_date .= "/".date("m", $date_debut);
if (date("Y", $date_debut) != date("Y", $date_fin))
	$aff_date .= "/".date("Y", $date_debut);
$aff_date .= ' au '.date("d", $date_fin).'/'.date("m", $date_fin).'/'.date("Y", $date_fin);


//Les éventuelles publicités
$requete_publicite = "SELECT publicites.no,publicites.url_image,publicites.titre,publicites.site,lettreinfo_publicite.position FROM publicites JOIN lettreinfo_publicite ON lettreinfo_publicite.no_publicite=publicites.no WHERE lettreinfo_publicite.no_lettre=:no AND publicites.etat=1 ORDER BY lettreinfo_publicite.position";
$res_publicite = $connexion->prepare($requete_publicite);
$res_publicite->execute(array(":no"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_publicite);
$liste_publicite = $res_publicite->fetchAll();
$indice_publicite = 0;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Ensemble ici | <?php echo $lettre_objet; ?></title>
		<style type="text/css">
			#outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
			body{width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
			body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */

			/* Reset Styles */
			body{margin:0; padding:0;font-size: .9em;font-family:Gill Sans’,Corbel,Tahoma,sans-serif;}
			img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
			table td{border-collapse:collapse;}
			#backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}

			body, #backgroundTable{
				background-color:#F0EDEA;
			}
			#templateContainer{
				border: 1px solid #E3D6C7;
			}
			a {
				text-decoration: none;
				color: inherit;
			}
			h1, .h1{
				color:#E16A0C;
				display:block;
				font-family:Arial;
				font-size:34px;
				font-weight:bold;
				line-height:100%;
				margin-top:0;
				margin-right:0;
				margin-bottom:10px;
				margin-left:0;
				text-align:left;
			}
			h2, .h2{
				color:#2DABDA;
				display:block;
				font-family:Arial;
				font-size:30px;
				font-weight:bold;
				line-height:100%;
				margin-top:0;
				margin-right:0;
				margin-bottom:10px;
				margin-left:0;
				text-align:left;
			}
			#templateHeader{
				background-color:#FFFFFF;
				border-bottom:0;
			}
			.headerContent{
				color:#445158;
				font-family:Arial;
				font-size:34px;
				font-weight:bold;
				line-height:100%;
				padding:0;
				text-align:center;
				vertical-align:middle;
			}
			.headerContent a:link, .headerContent a:visited, /* Yahoo! Mail Override */ .headerContent a .yshortcuts /* Yahoo! Mail Override */{
				color:#2DABDA;
				font-weight:normal;
				text-decoration:underline;
			}

			#headerImage{
				height:auto;
				max-width:650px !important;
			}
			#templateContainer, .bodyContent{
				background-color:#FFFFFF;
			}
			.bodyContent div{
				color:#445158;
				font-family:Arial;
				font-size:14px;
				line-height:150%;
				text-align:left;
			}
			.bodyContent div a:link, .bodyContent div a:visited, /* Yahoo! Mail Override */ .bodyContent div a .yshortcuts /* Yahoo! Mail Override */{
				color:#2DABDA;
				font-weight:normal;
				text-decoration:underline;
			}
			.bodyContent img{
				display:inline;
				height:auto;
			}
			#templateFooter{
				background-color:#FFFFFF;
				border-top:0;
			}
			.footerContent div{
				color:#707070;
				font-family:Arial;
				font-size:12px;
				line-height:125%;
				text-align:left;
			}
			.footerContent div a:link, .footerContent div a:visited, /* Yahoo! Mail Override */ .footerContent div a .yshortcuts /* Yahoo! Mail Override */{
				color:#2DABDA;
				font-weight:normal;
				text-decoration:underline;
			}
			.footerContent img{
				display:inline;
			}
		</style>
	</head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    	<center>
        	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable" bgcolor="#F0EDEA">
            	<tr>
                	<td align="center" valign="top">
                        <!-- // Begin Template Preheader \\ -->
                        <table border="0" cellpadding="10" cellspacing="0" width="650" id="templatePreheader">
                            <tr>
                                <td valign="top" class="preheaderContent" height="122" style='margin:0;padding:0;'><table width="650" height="122" border="0" cellpadding="0" cellspacing="0">
										<tr><td colspan="4" rowspan="4" style='text-align:center;'>
		                                    <span style='font-size:11px;color:rgb(194, 186, 178)'><a href="<?php echo $lettre_repertoire; ?>index.php?v=[**idv**]">Si ce message ne s'affiche pas correctement, visualisez la version en ligne.</a></span><br/>

											<a href="http://www.ensembleici.fr/"><img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-03.jpg" width="391" height="122" alt="Ensemble ici : Tous acteurs de la vie locale"></a></td><td rowspan="3" style="vertical-align: top;"><img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-04.jpg" width="24" height="58" alt=""></td><td bgcolor="#4cbce6" colspan="2" style="height:58px;vertical-align:top;" align="center">
												<img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-05.jpg" width="213" height="34" alt="Lettre d'informations"><br/>
												<p style='height:18px;margin:0;padding:0;font-size:15px;color:#FFFFFF'><?php echo $aff_date; ?></p>
												<img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-09.jpg" width="213" height="6" alt="">
											</td><td colspan="2" rowspan="3" style="vertical-align: top;"><img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-06.jpg" width="24" height="58" alt=""></td></tr>
										<tr>
											<td colspan="2" bgcolor="#F0EDEA">&nbsp;</td>
												
										</tr>
										<tr>
											<td colspan="2" bgcolor="#F0EDEA">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="5" bgcolor="#F0EDEA">&nbsp;</td>
										</tr>
			                        </table>
                                </td>
                            </tr>
                        </table>
                        <!-- // End Template Preheader \\ -->
                    	<table border="0" cellpadding="0" cellspacing="0" width="650" id="templateContainer" style="border: 1px solid #E3D6C7;" bgcolor="#FFFFFF">
                        	<tr>
                            	<td align="center" valign="top" style="border-bottom: 1px solid #E3D6C7;">
                                    <!-- // Begin Template Header \\ -->
                                	<table border="0" cellpadding="0" cellspacing="0" width="650" id="templateHeader">
                                        <tr>
                                            <td class="headerContent"><a href="#edito"><img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-13.jpg" width="173" height="42" alt="Cette semaine" /></a><a href="#evenement"><img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-14.jpg" width="142" height="42" alt="évènements" /></a><a href="#petite_annonce"><img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-15.jpg" width="189" height="42" alt="Petites annonces" /></a><a href="#repertoire"><img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-16.jpg" width="146" height="42" alt="Répertoire" /></a></td>
                                        </tr>
                                    </table>
                                    <!-- // End Template Header \\ -->
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- // Begin Template Body \\ -->
                                	<table border="0" cellpadding="0" cellspacing="0" width="650" id="templateBody">
                                    	<tr>
                                            <td valign="top" class="bodyContent">
                                
                                                <!-- // Begin Module: Standard Content \\ -->
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="top" style="padding-top: 30px;">
    														<!-- [**CHGT_VILLE**] -->

<?php
/*
On récupère dans un premier temps les éventuelles publicités et leurs positions.
*/
//Si cette publicite existe, on l'insère
if($indice_publicite<count($liste_publicite)&&$liste_publicite[$indice_publicite]["position"]==1){
	$titre = $liste_publicite[$indice_publicite]["titre"];
	$url = $liste_publicite[$indice_publicite]["site"];
	$img = $root_site.$liste_publicite[$indice_publicite]["url_image"];
	$indice_publicite++;
	?>
	<table border="0" cellpadding="0" cellspacing="0" width="650" style="text-align:center;">
		<tr>
			<td>
				<a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $img; ?>" alt="<?php echo $titre; ?>" title="<?php echo $titre; ?>" /></a>
			</td>
		</tr>
	</table>
	<?php
}
include "edito.php";
//Si cette publicite existe, on l'insère
if($indice_publicite<count($liste_publicite)&&$liste_publicite[$indice_publicite]["position"]==2){
	$titre = $liste_publicite[$indice_publicite]["titre"];
	$url = $liste_publicite[$indice_publicite]["site"];
	$img = $root_site.$liste_publicite[$indice_publicite]["url_image"];
	$indice_publicite++;
	?>
	<table border="0" cellpadding="0" cellspacing="0" width="650" style="text-align:center;">
		<tr>
			<td>
				<a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $img; ?>" alt="<?php echo $titre; ?>" title="<?php echo $titre; ?>" /></a>
			</td>
		</tr>
	</table><?php
}

include "agenda.php";
//Si cette publicite existe, on l'insère
if($indice_publicite<count($liste_publicite)&&$liste_publicite[$indice_publicite]["position"]==3){
	$titre = $liste_publicite[$indice_publicite]["titre"];
	$url = $liste_publicite[$indice_publicite]["site"];
	$img = $root_site.$liste_publicite[$indice_publicite]["url_image"];
	$indice_publicite++;
	?>
	<table border="0" cellpadding="0" cellspacing="0" width="650" style="text-align:center;">
		<tr>
			<td>
				<a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $img; ?>" alt="<?php echo $titre; ?>" title="<?php echo $titre; ?>" /></a>
			</td>
		</tr>
	</table>
	<?php
}
include "petiteannonce.php";
//Si cette publicite existe, on l'insère
if($indice_publicite<count($liste_publicite)&&$liste_publicite[$indice_publicite]["position"]==4){
	$titre = $liste_publicite[$indice_publicite]["titre"];
	$url = $liste_publicite[$indice_publicite]["site"];
	$img = $root_site.$liste_publicite[$indice_publicite]["url_image"];
	$indice_publicite++;
	?>
	<table border="0" cellpadding="0" cellspacing="0" width="650" style="text-align:center;">
		<tr>
			<td>
				<a href="<?php echo $url; ?>" target="_blank"><img src="<?php echo $img; ?>" alt="<?php echo $titre; ?>" title="<?php echo $titre; ?>" /></a>
			</td>
		</tr>
	</table>
	<?php
}
include "repertoire.php";

include "partenaires.php";
?>
														</td>
                                                    </tr>
                                                </table>
                                                <!-- // End Module: Standard Content \\ -->
                                                
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- // End Template Body \\ -->
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- // Begin Template Footer \\ -->
                                	<table border="0" cellpadding="10" cellspacing="0" width="650" id="templateFooter">
                                    	<tr>
                                        	<td valign="top" class="footerContent" style="border-top: 1px solid #E3D6C7;">
                                            
                                                <!-- // Begin Module: Standard Footer \\ -->
                                                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="top" width="50%" align="center" style='font-style:italic; font-size:11px;color:#445158;line-height: 1.5em;'>
														Vous pouvez transférer cette lettre d'information :<br/>
														<?php
															$lien_envoyerami = $root_site."lettreinfo_envoyer_a_un_ami.html";
															echo "<a href=\"".$lien_envoyerami."\" style=\"text-decoration:none;\" target=\"_blank\"><img src=\"".$root_site."img/lettreinfo/envoyer_ami.jpg\" alt=\"Evnoyer à un ami\" /></a>";
														?>
														</td>
                                                        <td valign="top" width="50%" align="center" style='font-style:italic; font-size:11px;color:#445158;line-height: 1.5em;'>
															<!-- [**NON_INSCRIT**] -->
															Vous pouvez modifier votre ville de pr&eacute;f&eacute;rence :<br/>
															<?php
																//$lien_agenda = $root_site."inscription-simple.html?codoff=[**codoff**]";
                                                                                                                                $lien_agenda = $root_site."espace-personnel.html";
																echo "<a href=\"".$lien_agenda."\" style=\"text-decoration:none;\" target=\"_blank\"><img src=\"".$root_site."img/lettreinfo/modifier_mes_infos.jpg\" alt=\"Modifier mes informations\" /></a>";
															?>
															<!-- [**FIN_NON_INSCRIT**] -->
														</td>
													</tr>
                                                    <tr>
                                                        <td valign="top" colspan="2" align="center">
															<p style='font-style:italic; font-size:11px; color:#445158'>Vous recevez ce message car vous êtes abonné à la lettre d'informations <a style='font-style:italic; color:#445158' href='http://wwww.ensembleici.fr'> du site ensembleici.fr</a>.<br/>
															Conformément à la loi informatique et libertés n°78-17 du 6 Janvier 1978 vous disposez <br/>d'un droit d'accès et de rectification des informations vous concernant. 
															<br/><br/>
															<a href="<?php echo $root_site; ?>desinscription.html?codoff=[**codoff**]&typoff=[**typoff**]">Si vous souhaitez vous désinscrire, cliquez ici.</a>
															</p>

                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- // End Module: Standard Footer \\ -->
                                            
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- // End Template Footer \\ -->
                                </td>
                            </tr>
                        </table>
                        <br />
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>
