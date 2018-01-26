<?php
$no_lettre = $_REQUEST["no_lettre"];
$etape = urldecode($_REQUEST["e"]);

$aide = array(
	"generalites"=>array("G&eacute;n&eacute;ralit&eacute;s","Les g&eacute;n&eacute;ralit&eacute;s permettent : <br/><br/><ul><li>de g&eacute;n&eacute;rer un objet, celui-ci sera utilis&eacute; comme objet dans le mailing et permettra aussi de nommer la lettre sur le site.</li><li>de g&eacute;n&eacute;rer la date de d&eacute;but de la lettre. Cette date permet nottament le calcul des &eacute;v&eacute;n&eacute;ments &agrave; afficher (&agrave; partir de l&agrave; +8 jours). Mais aussi c'est celle qui apparaitra sur le site.</li>"),
	"edito"=>array("Editoriel","Cette partie permet d'afficher un texte personnalis&eacute; chaque semaine.<br/>Saisir simplement le texte, puis valider l'&eacute;tape afin de l'enregistrer"),
	"agenda"=>array("Agenda","Ici s'affiche la liste des &eacute;v&eacute;n&eacute;ments. Les stages, cours et ateliers sont marqu&eacute; de la couleur <span style='background-color:#FEE1E1'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>, les &eacute;v&eacute;nements ponctuels sont marqu&eacute;s en blanc.<br/>Vous pouvez aussi promouvoir un &eacute;v&eacute;nement qui n'apparait pas dans cette liste."),
	"repertoire"=>array("Repertoire","Ici s'affiche la liste des structures saisies sur le site depuis la cr&eacute;ation de la derni&egrave;re lettre d'information. Vous pouvez en ajouter et en retirer.<br/>Vous pouvez aussi promouvoir une structure qui n'apparait pas dans cette liste."),
	"petiteannonce"=>array("Petites annonces","Ici s'affiche la liste des petites annonces saisies sur le site depuis la cr&eacute;ation de la derni&egrave;re lettre d'information.<br/>Vous pouvez aussi promouvoir des petites annonces qui n'apparaissent pas dans cette liste."));
echo "<h1>".$aide[$etape][0]."</h1><br/>";
echo $aide[$etape][1];
?>