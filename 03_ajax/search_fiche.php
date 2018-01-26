<?php
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";

$return_code = '0';
$tab = array();

switch ($_POST['type']) {
    case 1 : $table = 'evenement'; $fields = 'no, titre, date_creation'; $cond = "titre LIKE '%".$_POST['nom']."%'"; break;
    case 2 : $table = 'petiteannonce'; $fields = 'no, titre, date_creation'; $cond = "titre LIKE '%".$_POST['nom']."%'"; break;
    case 3 : $table = 'structure'; $fields = 'no, nom as titre, date_creation'; $cond = "nom LIKE '%".$_POST['nom']."%'"; break;
    default: $table = 'evenement'; $fields = 'no, titre, date_creation'; $cond = "titre LIKE '%".$_POST['nom']."%'";break;
}

$requete_count = "SELECT COUNT(no) as nb FROM ".$table." WHERE ".$cond;
$tab_count = execute_requete($requete_count,array());
if (!empty($tab_count)) {
    if ($tab_count[0]['nb'] < 20) {
        $requete_fiches = "SELECT ".$fields." FROM ".$table." WHERE ".$cond." ORDER BY no DESC LIMIT 0,20";
        $tab_fiches = execute_requete($requete_fiches,array());
        if (!empty($tab_fiches)) {
            $html = "<table>";
            foreach ($tab_fiches as $k => $v) {
                if ($v['titre']) {
                    $html .= "<tr style='border-top: 1px dashed black;'><td style='text-align:left;padding-top:10px; padding-bottom:10px;'>";
                    $html .= "<span>".substr($v['titre'], 0, 60)."</span>";
                    $html .= "</td><td style='padding-top:10px; padding-bottom:10px;'><a style='cursor:pointer;' name='a_link_fiche' data-titre='".substr($v['titre'], 0, 60)."' data-ref='".$v['no']."'>Lier</a></td></tr>";
                }
            }
            $html .= "</table>";
        }
    }
    else {
        $html = "Le texte saisi génère trop de résultat. Veuillez affiner la recherche.";
    }
}

$tab['code'] = $return_code; 
$tab['html'] = $html;
$reponse = json_encode($tab); 
echo $reponse; 
?>
