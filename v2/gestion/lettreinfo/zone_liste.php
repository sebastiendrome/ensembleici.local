<?php
//On récupère le nombre de membres inscrits
$nb_membre = 550;
$nb_membre_hors_liste = 400;
$nb_membre_total = 950;

echo "<b>".$nb_membre."</b>&nbsp;inscrits &agrave; la newsletter.";
echo "<br/>";
echo "<b>".$nb_membre_hors_liste."</b>&nbsp;adresses dans la liste de diffusion hors membre.";
echo "<br/>";
echo "<b>".$nb_membre_total."&nbsp;mails seront envoy&eacute;s.</b>";
echo "<br/>";
echo "<br/>";
echo "<input type=\"button\" value=\"continuer\" onclick=\"ouvrir_field('field_suivi');\" />";
?>