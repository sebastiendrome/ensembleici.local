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
unset($data['image']); unset($data['page']); unset($data['user']); unset($data['ville']); unset($data['select_vie']); unset($data['tags']);
unset($data['contact']); unset($data['nom_contact']); unset($data['telephone_contact']); unset($data['telephone2_contact']); unset($data['email_contact']);
unset($data['liaisons']); unset($data['nom_liaison_fiche']); unset($data['select_liaison_fiche']);
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

$data["no_utilisateur_creation"] = $_SESSION["utilisateur"]["no"];
$data["validation"] = (($_SESSION["droit"]["no"] == 1) ? 1 : 0);

//On teste l'existence du champ date création qu'il faut remplir par défaut s'il existe
$req_existe_dateCreation = "SELECT column_name FROM information_schema.columns WHERE table_name=:t AND column_name='date_creation'";
if(count_requete($req_existe_dateCreation,array(":t"=>$table))>0) {
    $data["date_creation"] = date("Y-m-d H:i:s");
}
if($mapage != "editorial") {
    $data["etat"] = 1;
} else {	
    $data["etat"] = 0;
}

if (isset($_POST['image'])) {
    if ($mapage == 'structure') {
        $data['url_logo'] = '02_medias/'.$dossier.'/'.$_POST['image'];
    }
    else {
        $data['url_image'] = '02_medias/'.$dossier.'/'.$_POST['image'];
    }
}

if (isset($_POST['soustitre'])) {
    $data['sous_titre'] = $_POST['soustitre'];
    unset($data['soustitre']);
}

if (isset($_POST['date_debut'])) {
    unset($data['date_debut']); 
    $data['date_debut'] = substr($_POST['date_debut'], 6, 4).'-'.substr($_POST['date_debut'], 3, 2).'-'.substr($_POST['date_debut'], 0, 2);
}
if (isset($_POST['date_fin'])) {
    unset($data['date_fin']); 
    $data['date_fin'] = substr($_POST['date_fin'], 6, 4).'-'.substr($_POST['date_fin'], 3, 2).'-'.substr($_POST['date_fin'], 0, 2);
}
else {
    $data['date_fin'] = $data['date_debut'];
}

if (isset($_POST['heure_debut'])) {
    unset($data['heure_debut']);
    $data['heure_debut'] = str_replace('h', ':', $_POST['heure_debut']);
}
if (isset($_POST['heure_fin'])) {
    unset($data['heure_fin']);
    $data['heure_fin'] = str_replace('h', ':', $_POST['heure_fin']);
}

$chaine_champs = "";
$chaine_valeurs = "";
$params = array();
foreach($data as $k => $v){
    if (!empty($v)) {
        $chaine_champs .= (($chaine_champs!="") ?", " : "").$k;
        $chaine_valeurs .= (($chaine_valeurs!="") ? ", " : "").":".$k;
        $params[":".$k] = $v;
    }
}
$requete_insert = "INSERT INTO ".$table." (".$chaine_champs.") VALUES (".$chaine_valeurs.")";
$no_item = execute_requete($requete_insert,$params);

if (isset($_POST['tags'])) {
    $requete_insert_tag = "INSERT INTO ".$table."_tag(no_".$table.",no_tag) VALUES(:no,:not)";
    $les_tags = explode(",",$_POST["tags"]);
    foreach ($les_tags as $k => $v) {
        execute_requete($requete_insert_tag,array(":no" => $no_item, ":not" => $v));
    }
}

if (isset($_POST['contact']) && ($_POST['contact'] != '')) {
    // insertion du contact existant
    $requete_insert_contact = "INSERT INTO ".$table."_contact (no_".$table.",no_contact) VALUES(:no,:noc)";
    execute_requete($requete_insert_contact,array(":no" => $no_item, ":noc" => $_POST['contact']));
} 
else {
    if (isset($_POST['nom_contact'])) {
        // ajout nouveau contact
        $requete_insert_tabcontact = "INSERT INTO contact (nom, no_utilisateur_creation, date_creation, date_modification) VALUES(:nom,:no, :d, :d)";
        $no_contact = execute_requete($requete_insert_tabcontact,array(":nom" => $_POST['nom_contact'], ":no" => $_SESSION["utilisateur"]["no"], ":d" => date('Y-m-d H:i:s')));
        
        if (isset($_POST['telephone_contact'])) {
            $requete_insert_type = "INSERT INTO contact_contactType (no_contact, no_contactType, valeur, public) VALUES(:no,:type, :valeur, :public)";
            $no_contact = execute_requete($requete_insert_type,array(":no" => $no_contact, ":type" => 1, ":valeur" => $_POST['telephone_contact'], ":public" => 0));
        }
        if (isset($_POST['telephone2_contact'])) {
            $requete_insert_type = "INSERT INTO contact_contactType (no_contact, no_contactType, valeur, public) VALUES(:no,:type, :valeur, :public)";
            $no_contact = execute_requete($requete_insert_type,array(":no" => $no_contact, ":type" => 1, ":valeur" => $_POST['telephone2_contact'], ":public" => 0));
        }
        if (isset($_POST['email_contact'])) {
            $requete_insert_type = "INSERT INTO contact_contactType (no_contact, no_contactType, valeur, public) VALUES(:no,:type, :valeur, :public)";
            $no_contact = execute_requete($requete_insert_type,array(":no" => $no_contact, ":type" => 2, ":valeur" => $_POST['email_contact'], ":public" => 0));
        }
        
        $requete_insert_contact = "INSERT INTO ".$table."_contact (no_".$table.",no_contact) VALUES(:no,:noc)";
        execute_requete($requete_insert_contact,array(":no" => $no_item, ":noc" => $no_contact));
    }
}

if (isset($_POST['liaisons'])) {
    $tabliaison = explode(',', $_POST['liaisons']); 
    foreach ($tabliaison as $k => $v) {
        $laliaison = explode('-', $v);
        switch ($mapage) {
            case 'evenement' : $table_liaison = 'evenement_'; $champ1 = 'no_evenement'; break;
            case 'structure' : $table_liaison = 'structure_'; $champ1 = 'no_structure';  break;
            case 'petiteannonce' : $table_liaison = 'petiteannonce_'; $champ1 = 'no_petiteannonce'; break;
            default: break;
        }
        $table_liaison .= $laliaison[1];
        if (($table_liaison == 'evenement_evenement') || ($table_liaison == 'structure_structure') || ($table_liaison == 'petiteannonce_petiteannonce')) {
            $champ1 = 'no1'; $champ2 = 'no2';
        }
        else {
            $champ2 = 'no_'.$laliaison[1];
        }
        $requete_insert_liaison = "INSERT INTO ".$table_liaison." (".$champ1.", ".$champ2.") VALUES(:no1,:no2)";
        execute_requete($requete_insert_liaison,array(":no1" => $no_item, ":no2" => $laliaison[0]));
    }
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
