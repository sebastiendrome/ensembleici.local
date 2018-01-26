<div id="etapes_form">
<?php
// Affichage des étapes en haut des formulaire d'ajout / modif d'un evt / structure

if ($type) {
        if ($type=="evenement")
        {
                // Sur étape 5, le nofiche est déjà assigné par autoprevisu
                if (!$no_fiche) $no_fiche = $_SESSION['no_evenement'];
                $etape_arret = $_SESSION['etape_arret_form_evt'];
        }
        elseif ($type=="structure")
        {
                if (!$no_fiche) $no_fiche = $_SESSION['no_structure'];
                $etape_arret = $_SESSION['etape_arret_form_str'];
        }
        elseif ($type=="petiteannonce")
        {
                if (!$no_fiche) $no_fiche = $_SESSION['no_pa'];
                $etape_arret = $_SESSION['etape_arret_form_pa'];
        }

        if ($no_fiche)
        {
        
                $ce_fichier = pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME);
                $separateur = "<img src=\"img/fleche-bleu.png\" width=\"12\" class=\"separateur\" />";
                $etapes_desactive = false;
                
                $num_etape = 1;
                $etapes_lib_1 = "1<span class=\"descr\">Généralités</span>";
                $etapes_fichier = "auto_".$type."_etape".$num_etape.".php";
                if ($ce_fichier=="auto_".$type."_etape$num_etape.php")
                {
                        echo "<div class=\"etape_active\">$etapes_lib_1</div>";
                        if($etape_arret<$num_etape)
                          $etape_arret= $num_etape;
                }
                else
                        echo "<a href=\"auto_".$type."_etape1.php\" title=\"1. Généralités\">$etapes_lib_1</a>";
                
                // Separateur
                echo $separateur;
                
                $num_etape = 2;
                $etapes_lib_2 = "2<span class=\"descr\">Thématiques</span>";
                $etapes_fichier = "auto_".$type."_etape".$num_etape.".php";
                // On est dans l'ajout d'un evt, et à une étape d'après
                if ((!$mode_modification) && ($num_etape>$etape_arret) && ($ce_fichier<>$etapes_fichier)) $etapes_desactive = true;
                if (($ce_fichier=="auto_".$type."_etape$num_etape.php")||($etapes_desactive))
                {
                        echo "<div class=\"";
                        if ($etapes_desactive)
                        {
                                echo "desactive";
                        }
                        else
                        {
                                echo "etape_active";
                                if($etape_arret<$num_etape)
                                  $etape_arret= $num_etape;
                                  $verif_js_desactive = true;
                        }
                        echo "\">$etapes_lib_2</div>";
                }
                else
                        echo "<a href=\"auto_".$type."_etape2.php\" title=\"2. Activités\">$etapes_lib_2</a>";
                
                // Separateur
                echo $separateur;
                
                $num_etape = 3;
                $etapes_lib_3 = "3<span class=\"descr\">Détails</span>";
                $etapes_fichier = "auto_".$type."_etape".$num_etape.".php";
                if ((!$mode_modification) && ($num_etape>$etape_arret) && ($ce_fichier<>$etapes_fichier)) $etapes_desactive = true;
                if (($ce_fichier=="auto_".$type."_etape$num_etape.php")||($etapes_desactive))
                {
                        echo "<div class=\"";
                        if ($etapes_desactive)
                        {
                                echo "desactive";
                        }
                        else
                        {
                                echo "etape_active";
                                if($etape_arret<$num_etape)
                                  $etape_arret= $num_etape;
                        }
                        echo "\">$etapes_lib_3</div>";
                }
                else
                        echo "<a href=\"auto_".$type."_etape3.php\" title=\"3. Détails\">$etapes_lib_3</a>";
                
                // Separateur
                echo $separateur;
                
                $num_etape = 4;
                $etapes_lib_4 = "4<span class=\"descr\">Illustration</span>";
                $etapes_fichier = "auto_".$type."_etape".$num_etape.".php";
                if ((!$mode_modification) && ($num_etape>$etape_arret) && ($ce_fichier<>$etapes_fichier)) $etapes_desactive = true;
                if (($ce_fichier=="auto_".$type."_etape$num_etape.php")||($etapes_desactive))
                {
                        echo "<div class=\"";
                        if ($etapes_desactive)
                        {
                                echo "desactive";
                        }
                        else
                        {
                                echo "etape_active";
                                if($etape_arret<$num_etape)
                                  $etape_arret= $num_etape;
                        }
                        echo "\">$etapes_lib_4</div>";
                }
                else
                        echo "<a href=\"auto_".$type."_etape4.php\" title=\"4. Illustration\">$etapes_lib_4</a>";
                
                // Separateur
                echo $separateur;
                
                $num_etape = 5;
                $etapes_lib_5 = "5<span class=\"descr\">Validation</span>";
                $etapes_fichier = "auto_previsu.php";
                if ((!$mode_modification) && ($num_etape>$etape_arret) && ($ce_fichier<>$etapes_fichier)) $etapes_desactive = true;
                if (($ce_fichier=="auto_previsu.php")||($etapes_desactive))
                {
                        echo "<div class=\"";
                        if ($etapes_desactive)
                        {
                                echo "desactive";
                        }
                        else
                        {
                                echo "etape_active";
                                if($etape_arret<$num_etape)
                                  $etape_arret= $num_etape;
                        }
                        echo "\">$etapes_lib_5</div>";
                }
                elseif($no_fiche)
                        echo "<a href=\"auto_previsu.php?no_fiche=".$no_fiche."&type=".$type."&etape_validation=1\" title=\"5. Validation\">$etapes_lib_5</a>";
        
                // Enregistre en session
                if ($type=="evenement")
                        $_SESSION['etape_arret_form_evt'] = $etape_arret; 
                elseif ($type=="structure")
                        $_SESSION['etape_arret_form_str'] = $etape_arret;
                elseif ($type=="petiteannonce")
                        $_SESSION['etape_arret_form_pa'] = $etape_arret;
        }
}
?>
</div>
<?php
// pour afficher une alerte en cas de modification et clic sur une autre étape.
if (!$verif_js_desactive)
{
?>
<script type='text/javascript' src="js/jquery.dirtyform.min.js"></script>
<script type='text/javascript'>
$(function(){
  // $('form#EIForm').dirtyForms('ignoreClass':'.ui-datepicker-next, .ui-datepicker-prev');
});
</script>
<?php
}
?>