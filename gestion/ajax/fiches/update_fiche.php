<?php
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
$data = $_POST;
unset($data['image']); unset($data['page']); unset($data['user']); unset($data['ville']); unset($data['select_vie']); unset($data['tags']); unset($data['no']);
 unset($data['select_liaison_fiche']); unset($data['contact']); unset($data['nom_contact']); unset($data['telephone_contact']); 
 unset($data['telephone2_contact']); unset($data['email_contact']);
$mapage = $_POST['page'];
if ($mapage == 'petite-annonce') {
    $mapage = 'petiteannonce';
}
switch ($mapage) {
    case 'evenement' : $table = 'evenement'; $dossier = '05_evenement'; break;
    case 'editorial' : $table = 'editorial'; $dossier = '12_editorial'; break;
    case 'structure' : $table = 'structure'; $dossier = '04_structure'; break;
    case 'forum' : $table = 'forum'; $dossier = '11_forum'; break;
    case 'petiteannonce' : $table = 'petiteannonce'; $dossier = '09_petiteannonce'; break;
    default: break;
}

//$data["no_utilisateur_creation"] = $_SESSION["utilisateur"]["no"];
$data["validation"] = (($_SESSION["droit"]["no"] == 1) ? 1 : 0);

//On teste l'existence du champ date création qu'il faut remplir par défaut s'il existe
$req_existe_dateCreation = "SELECT column_name FROM information_schema.columns WHERE table_name=:t AND column_name='date_modification'";
if(count_requete($req_existe_dateCreation,array(":t"=>$table))>0) {
    $data["date_modification"] = date("Y-m-d H:i:s");
}


if (isset($_POST['image'])) {
    if ($_POST['image'] != '') {
        if ($mapage == 'structure') {
            $data['url_logo'] = '02_medias/'.$dossier.'/'.$_POST['image'];
        }
        else {
            $data['url_image'] = '02_medias/'.$dossier.'/'.$_POST['image'];
        }
    }
    else {
        if ($mapage == 'structure') {
            $data['url_logo'] = '';
        }
        else {
            $data['url_image'] = '';
        }
    }
}

if (isset($_POST['soustitre'])) {
    $data['sous_titre'] = $_POST['soustitre'];
    unset($data['soustitre']);
}

if (isset($_POST['date_fin'])) {
    unset($data['date_fin']); 
    $data['date_fin'] = substr($_POST['date_fin'], 6, 4).'-'.substr($_POST['date_fin'], 3, 2).'-'.substr($_POST['date_fin'], 0, 2);
}
if (isset($_POST['date_debut'])) {
    unset($data['date_debut']); 
    $data['date_debut'] = substr($_POST['date_debut'], 6, 4).'-'.substr($_POST['date_debut'], 3, 2).'-'.substr($_POST['date_debut'], 0, 2);
}
if (isset($_POST['heure_debut'])) {
    unset($data['heure_debut']);
    $data['heure_debut'] = str_replace('h', ':', $_POST['heure_debut']);
}
if (isset($_POST['heure_fin'])) {
    unset($data['heure_fin']);
    $data['heure_fin'] = str_replace('h', ':', $_POST['heure_fin']);
}

$chaine_update = "";
$params = array(":no"=>$_POST["no"]);
foreach($data as $k => $v){
    if (!empty($v)) {
        $chaine_update .= (($chaine_update!="") ? ", " : "").$k."=:".$k;
        $params[":".$k] = $v;
    }
}

$requete_update = "UPDATE ".$table." SET ".$chaine_update." WHERE no=:no";
execute_requete($requete_update,$params);

// suppression de tous les tags liés à la fiche
$requete_delete_tags = "DELETE FROM ".$table."_tag WHERE no_".$table." =:no";
execute_requete($requete_delete_tags,array(":no" => $_POST["no"]));

// insertion des tags
if (isset($_POST['tags'])) {
    $requete_insert_tag = "INSERT INTO ".$table."_tag(no_".$table.",no_tag) VALUES(:no,:not)";
    $les_tags = explode(",",$_POST["tags"]);
    foreach ($les_tags as $k => $v) {
        execute_requete($requete_insert_tag,array(":no" => $_POST["no"], ":not" => $v));
    }
}
$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
