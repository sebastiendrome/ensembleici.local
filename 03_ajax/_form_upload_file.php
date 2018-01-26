<?php
header('Content-Type: text/plain; charset=UTF-8');
require('../01_include/_var_ensemble.php');
include "../01_include/_fonctions.php";
include "../01_include/_fonctions_gd.php";
$code_err = 0;
$tab_err = array(	"Ok",
					"Erreur lors du transfert du fichier",
					"Le fichier est trop volumineux, il ne doit pas dépasser ".(((int)$_POST["fichier_courant_poids_max"])/1000)."ko.",
					"Le fichier n'est pas suffisamment volumineux, il doit être de poids supérieur à ".(((int)$_POST["fichier_courant_poids_min"])/1000)."ko.",
					"Une erreur interne s'est produite lors de l'enregistrement du fichier. Veuillez réessayer.",
					"Le fichier fourni n'est pas au format demandé (".$_POST["fichier_courant_accept"].")");

if($_FILES["fichier_courant"]["error"]==0&&$_FILES["fichier_courant"]["size"]>0){
	if(isset($_POST["fichier_courant_accept"])&&!empty($_POST["fichier_courant_accept"])&&!strstr($_FILES["fichier_courant"]["type"],$_POST["fichier_courant_accept"])){
		$code_err = 5;
	}
	else if(isset($_POST["fichier_courant_poids_max"])&&!empty($_POST["fichier_courant_poids_max"])&&$_FILES["fichier_courant"]["size"]>$_POST["fichier_courant_poids_max"]){
		$code_err = 2;
	}
	else if(isset($_POST["fichier_courant_poids_min"])&&!empty($_POST["fichier_courant_poids_min"])&&$_FILES["fichier_courant"]["size"]<$_POST["fichier_courant_poids_min"]){
		$code_err = 3;
	}
	
	if($code_err==2&&strstr($_FILES["fichier_courant"]["type"],"image")){
		//Le fichier est trop volumineux, mais c'est une image, on va alors utiliser l'algo d'optimisation taille/poids/qualité d'image
		//On deplace le fichier temp vers un dossier temporaire controlé manuellement
		$chemin_courant = $_FILES["fichier_courant"]["tmp_name"];
		$ext = strrchr($_FILES["fichier_courant"]["name"],'.');
		$ext = substr($ext,1);
		$nouveau_chemin = "_form_file_tmp/".time().".".$ext;
		if(!move_uploaded_file($chemin_courant, $nouveau_chemin))
			$code_err = 4;
		else{
			$chemin_courant = optimisation_image_TaillePoidsQualite($nouveau_chemin,$_POST["fichier_courant_poids_max"]);
			if($chemin_courant!=false)
				$code_err = 0;
		}
	}
	else{
		$chemin_courant = $_FILES["fichier_courant"]["tmp_name"];
		$ext = strrchr($_FILES["fichier_courant"]["name"],'.');
		$ext = substr($ext,1);
		$nouveau_chemin = "_form_file_tmp/".time().".".$ext;
		if(!move_uploaded_file($chemin_courant, $nouveau_chemin))
			$code_err = 4;
	}
}
else{
	$code_err = 1;
}
//print_r($_SERVER);
$url_ajax = substr($_SERVER["SCRIPT_NAME"],1); //On enlève le premier "/"
$url_ajax = substr($url_ajax,0,strrpos($url_ajax, "/"));
$url_ajax .= '/';
echo json_encode(array("code_err"=>$code_err,"info"=>(($code_err==0)?$url_ajax.$nouveau_chemin:$tab_err[$code_err])));
?>
