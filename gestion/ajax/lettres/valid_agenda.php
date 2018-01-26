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

$return_code = '0';
$tab = array();
$no = $_POST['no'];

if (isset($_SESSION["utilisateur"]["territoire"])) {
    // recherche de l'existence d'un enregistrement lettreinfo agenda
    $requete_info_agenda = "SELECT * FROM lettreinfo_agenda WHERE no_lettre=:no";
    $res_info_agenda = $connexion->prepare($requete_info_agenda);
    $res_info_agenda->execute(array(":no"=>$no));
    $tab_info_agenda = $res_info_agenda->fetch();
    
    if (isset($tab_info_agenda['liste_evenement_valide'])) {
        // mise à jour
        $requete = "UPDATE lettreinfo_agenda SET liste_evenement_complete = :e, liste_evenement = :e, liste_evenement_valide = :e, date_modification = CURRENT_TIMESTAMP, etape_valide = 1 WHERE no_lettre = :no";
        $res_requete = $connexion->prepare($requete);
        $res_requete->execute(array(":no" => $no, ":e" => $_POST['liste']));
    }
    else {
        // insertion
        $requete = "INSERT INTO  lettreinfo_agenda(no_lettre, liste_evenement_complete, liste_evenement, liste_evenement_valide, date_modification, etape_valide) 
            VALUES(:no, :e, :e, :e, CURRENT_TIMESTAMP, 1)";
        $res_requete = $connexion->prepare($requete);
        $res_requete->execute(array(":no" => $no, ":e" => $_POST['liste']));
    }

}
else {
    $return_code = '10';
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
