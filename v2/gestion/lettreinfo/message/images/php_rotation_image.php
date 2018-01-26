<?php
// Fichier et degrés de rotation
$filename = $_GET["img"];
$degrees = $_GET["deg"];

// Content type
header('Content-type: image/png');

// Chargement
list($largeur, $hauteur, $type) = getimagesize($filename);
$source = imagecreatefrompng($filename);

// Rotation
$rotate = imagerotate($source, $degrees, 0);

//si on pivote pas l'image à 180 degrès alors largeur et hauteur sont inversé.
if($degrees==90||$degrees==270){
	$int = $largeur;
	$largeur = $hauteur;
	$hauteur = $int;
}

$image_finale = ImageCreateTrueColor($largeur, $hauteur);
imagealphablending($image_finale,FALSE);
imagesavealpha($image_finale,TRUE);
ImageCopyResampled($image_finale, $rotate, 0, 0, 0, 0, $largeur, $hauteur, $largeur, $hauteur);
// Affichage

imagepng($image_finale);


// $theimage = "img.png";
// $im = imagecreatefrompng($theimage);
// $size = getimagesize($theimage);
// $w = $size[0];
// $h = $size[1];
// $im2 =  imagecreatetruecolor($w/2,$h/2);
// imagecopyresampled($im2,$im,0,0,0,0,$w/2,$h/2,$w,$h);

// header("Content-type: image/png" );
// imagepng($im2);

?>
