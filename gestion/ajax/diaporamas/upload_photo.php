<?php
//1. Initialisation de la session
include "../../../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../../../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../../../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../../../01_include/_init_var.php";

$tabtype = array('image/png', 'image/jpg', 'image/jpeg', 'image/gif');
$dirname = $root_serveur.'img/diapo-index/';
$file = $_FILES['file'];
$tabname = explode('.', $file['name']);
$name = $tabname[0].uniqid().'.'.$tabname[1];
$type = $file['type'];

if (!in_array($type, $tabtype)) {
    die ('{"error":true, "message":"Type de fichier incorrect"}');
}

if (filesize($file['tmp_name']) > 1000000) {
    die ('{"error":true, "message":"Image trop volumineuse"}');
}

if (file_exists($dirname.'/'.$name)) {
    die ('{"error":true, "message":"Le document a déjà été chargé"}');
}

if (!move_uploaded_file($_FILES['file']['tmp_name'], $dirname.'/'.$name) ) {
    die ('{"error":true, "message":"Impossible de charger le fichier"}');
}

$source = $root_site.'img/diapo-index/'.$name;

$tabphoto     = getimagesize($dirname.'/'.$name);
$hauteur      = $tabphoto[1];
$largeur      = $tabphoto[0];

if (($hauteur != 183) || ($largeur != 693)) {
    die ('{"error":true, "message":"Le format du fichier est incorrect. Votre image doit respecter la taille de 693x183"}');
}

die ('{"error":false, "fichier" : "'.$name.'", "source" : "'.$source.'"}');
?>
