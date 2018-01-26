<?php
session_name("EspacePerso");
session_start();
require ('../../../01_include/_var_ensemble.php');
require ('../../../01_include/_connect.php');

$dirname = '../../../img/';
$tabtype = array('image/png', 'image/jpg', 'image/jpeg', 'image/gif');
$file = $_FILES['logo_new_part'];
//$tabname = explode('.', $file['name']);
//$name = $tabname[0].uniqid().'.'.$tabname[1];
$name = $file['name'];
$type = $file['type'];

if (!in_array($type, $tabtype)) {
    die ('{"error":true, "message":"Type de fichier incorrect"}');
}

if (filesize($file['tmp_name']) > 5000000) {
    die ('{"error":true, "message":"Image trop volumineuse"}');
}

if (file_exists($dirname.$name)) {
    die ('{"error":true, "message":"Le document a déjà été chargé"}');
}

if (!move_uploaded_file($file['tmp_name'], $dirname.$name)) {
    die ('{"error":true, "message":"Impossible de charger le fichier"}');
}


die ('{"error":false}');


?>