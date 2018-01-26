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

if ($root_site != 'http://localhost/EnsembleIci/') {
    require_once 'config_pear.php';
}

$return_code = '0';
$tab = array();
$no = $_POST['ref'];

$requete_lettre = "SELECT * FROM lettreinfo WHERE no=:no_l";
$res_lettre = $connexion->prepare($requete_lettre);
$res_lettre->execute(array(":no_l"=>$no));
$tab_lettre = $res_lettre->fetch();

$territoire = $tab_lettre["territoires_id"];
$pdf_agenda = $tab_lettre["pdf_agenda"];
$pdf_annonces = $tab_lettre["pdf_annonces"];

$rep = str_replace("-","_",$tab_lettre["date_debut"]).'_'.$tab_lettre["territoires_id"];
$repertoire = $root_site."02_medias/10_lettreinfo/".$rep."/";
$rep = $root_serveur."02_medias/10_lettreinfo/".$rep."/";

$requete_update = "UPDATE lettreinfo SET repertoire=:r WHERE no=:no";
$res_update = $connexion->prepare($requete_update);
$res_update->execute(array(":no"=>$no,":r"=>$repertoire));

if(!is_dir($rep)){
    mkdir($rep);
}

ob_start(); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Ensemble ici | <?= $tab_lettre['objet']; ?></title>
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
            .headerContent a {
                padding: 0;
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
                                <td valign="top" class="preheaderContent" height="122" style='margin:0;padding:0;'>
                                    <table width="650" height="122" border="0" cellpadding="0" cellspacing="0">
                                        <tr><td colspan="4" rowspan="4" style='text-align:center;'>
                                            <span style='font-size:11px;color:rgb(194, 186, 178)'><a href="<?= $repertoire; ?>index.php?v=[**idv**]">Si ce message ne s'affiche pas correctement, visualisez la version en ligne.</a></span><br/>
                                            <a href="http://www.ensembleici.fr/"><img src="<?= $root_site;?>img/lettreinfo/lettreinfo-03.jpg" width="391" height="122" alt="Ensemble ici : Tous acteurs de la vie locale"></a>
                                            </td>
                                            <td rowspan="3" style="vertical-align: top;">
                                                <img src="<?= $root_site;?>img/lettreinfo/lettreinfo-04.jpg" width="24" height="58" alt="">
                                            </td>
                                            <td bgcolor="#4cbce6" colspan="2" style="height:58px;vertical-align:top;" align="center">
                                                <img src="<?= $root_site;?>img/lettreinfo/lettreinfo-05.jpg" width="213" height="34" alt="Lettre d'informations"><br/>
                                                <p style='height:18px;margin:0;padding:0;font-size:15px;color:#FFFFFF'><?php echo $aff_date; ?></p>
                                                <img src="<?= $root_site;?>img/lettreinfo/lettreinfo-09.jpg" width="213" height="6" alt="">
                                            </td>
                                            <td colspan="2" rowspan="3" style="vertical-align: top;">
                                                <img src="<?= $root_site;?>img/lettreinfo/lettreinfo-06.jpg" width="24" height="58" alt="">
                                            </td>
                                        </tr>
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
                                	<table border="0" cellpadding="0" cellspacing="0" width="650"  style='width:650px;' id="templateHeader">
                                        <tr>
                                            <td class="headerContent" style='width:650px;'>
                                                <table border="0" cellpadding="0" cellspacing="0" width="650" height='42'>
                                                    <tr>
                                                        <td>
                                                            <a href="#edito">
                                                                <img src="<?= $root_site;?>img/lettreinfo/lettreinfo-13.jpg" width="173" height="42" style='width:173px; height: 42px;' alt="Cette semaine" />
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="#evenement">
                                                                <img src="<?= $root_site;?>img/lettreinfo/lettreinfo-14.jpg" width="142" height="42" style='width:142px; height: 42px;' alt="évènements" />
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="#petite_annonce">
                                                                <img src="<?= $root_site;?>img/lettreinfo/lettreinfo-15.jpg" width="189" height="42" style='width:189px; height: 42px;' alt="Petites annonces" />
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="#repertoire">
                                                                <img src="<?= $root_site;?>img/lettreinfo/lettreinfo-16.jpg" width="146" height="42" style='width:146px; height: 42px;' alt="Répertoire" />
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
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
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="top" style="padding-top: 30px;">
                                                            <?php
                                                            include "create_lettre_edito.php";
                                                            include "create_lettre_agenda.php";
                                                            include "create_lettre_annonces.php";
                                                            include "create_lettre_repertoire.php";
                                                            include "create_lettre_partenaires.php";
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
                                                    <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                        <tr>
<!--                                                            <td valign="top" width="50%" align="center" style='font-style:italic; font-size:11px;color:#445158;line-height: 1.5em;'>
                                                                Vous pouvez transférer cette lettre d'information :<br/>
                                                                <?php
//                                                                    $lien_envoyerami = $root_site."lettreinfo_envoyer_a_un_ami.html";
//                                                                    echo "<a href=\"".$lien_envoyerami."\" style=\"text-decoration:none;\" target=\"_blank\"><img src=\"".$root_site."img/lettreinfo/envoyer_ami.jpg\" alt=\"Evnoyer à un ami\" /></a>";
                                                                ?>
                                                            </td>-->
                                                            <td valign="top" width="100%" align="center" style='font-style:italic; font-size:11px;color:#445158;line-height: 1.5em;'>
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
                                                                <p style='font-style:italic; font-size:11px; color:#445158'>
                                                                    Vous recevez ce message car vous êtes abonné à la lettre d'informations <a style='font-style:italic; color:#445158' href='http://wwww.ensembleici.fr'> du site ensembleici.fr</a>.<br/>
                                                                    Conformément à la loi informatique et libertés n°78-17 du 6 Janvier 1978 vous disposez <br/>d'un droit d'accès et de rectification des informations vous concernant. 
                                                                    <br/><br/>
                                                                    <a href="<?= $root_site; ?>desinscription.html?codoff=[**codoff**]&typoff=[**typoff**]">Si vous souhaitez vous désinscrire, cliquez ici.</a>
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

<?php 
$content = ob_get_clean();
$filename1 = $rep."index.php";
if (file_exists($filename1)) {
    unlink($filename1);
}
$content_PHP = preg_replace("#(\[\*\*idv\*\*\])#i","<?php echo \$_GET['v']; ?>",$content);
$content_PHP = str_replace('../', 'http://www.ensembleici.fr/', $content_PHP);
$content_PHP = "<?php if(\$_GET['v']=='') \$_GET['v']=9568; else \$_GET['v'] = (int)\$_GET['v']; ?>".$content_PHP;
$fp = fopen($filename1, 'x');
fwrite($fp, $content_PHP);
fclose($fp);

$content_txt = strip_tags($content);
$filename2 = $rep."txt.txt";
if (file_exists($filename2)) {
    unlink($filename2);
}
$fp2 = fopen($filename2, 'x');
fwrite($fp2, $content_txt);
fclose($fp2);

$content = str_replace('../', 'http://www.ensembleici.fr/', $content);

$requete_insert = "INSERT INTO lettreinfo_envoi(contenu_html,contenu_txt) VALUES(:html,:txt)";
$res_insert = $connexion->prepare($requete_insert);
$res_insert->execute(array(":txt"=>$content_txt,":html"=>$content));
$no_envoi = $connexion->lastInsertId();

$requete_update_lettre = "UPDATE lettreinfo SET no_envoi=:n WHERE no=:no";
$res_update_lettre = $connexion->prepare($requete_update_lettre);
$res_update_lettre->execute(array(":no" => $no, ":n" => $no_envoi));

if (($_POST['mail'] != '') && (filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) && ($root_site != 'http://localhost/EnsembleIci/')) {
    $file_denvoi_de_mail =& new Mail_Queue($db_options, $mail_options);
    
    $from = 'Ensemble Ici <newsletter@ensembleici.fr>';
    $destinataire = $_POST['mail'];
    $message = "<html><head></head><body>".$content."</body></html>";

    $entetes = array( 'From'    => $from,
        'To'      => $destinataire,
        'Subject' => "[TEST] ".$tab_lettre['objet'],
        'X-Sender' => '<www.ensembleici.fr>',
         'X-auth-smtp-user' => 'contact@envolinfo.com',
        'X-Priority' => 3,
        'X-Unsubscribe-Web' => '<http://www.ensembleici.fr/desinscription.html?codoff=0&typoff=0',
        'X-Unsubscribe-Email' => '<mailto:unsubscribe@ensembleici.fr>',
        'X-Mailer' => 'PHP/'.phpversion(),
        'Content-Type' => 'text/html; charset=utf-8',
        'Return-path' => "-f".$from
    );
    
    //On créait un message valide
    $mime =& new Mail_mime();
    $mime->setHTMLBody($message);
    $corps = $mime->get();
    $entetes = $mime->headers($entetes,true);
    //On place le message dans la file d'attente
    $put = $file_denvoi_de_mail->put( $from, $destinataire, $entetes, $corps );

    //On envoi le message
    $file_denvoi_de_mail->sendMailsInQueue();
}

$tab['code'] = $return_code;
$tab['repertoire'] = str_replace("-","_",$tab_lettre["date_debut"]).'_'.$tab_lettre["territoires_id"]; 
$reponse = json_encode($tab); 
echo $reponse;
?>
