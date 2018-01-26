<?php
//---------------------------------------------------------------------
// Fonction de redimensionnement d'image au niveau de la largeur
//---------------------------------------------------------------------
// recupere une image (jpg) envoyée via formulaire avec enregistrement
// et redimensionnement dans un répertoire donné
//---------------------------------------------------------------------
//Parametres d'entrés :
//		- $nom_files_formulaire => nom du champ formulaire de type FILES 
//		- $repertoire_upload => nom du répertoire pour l'enregistrement de notre image
//		- $poids => poids maximal de notre fichier avant upload	
//		- $largeur => largeur de callage max/min de redimensionnement
//		- $hauteur => hauteur de callage max/min de redimensionnement
//Parametres de sortis :
//		- tableau de valeur :
//			*tab[0]=> 1 une erreur s'est produite | 0 tout est ok
//			*tab[1]=> message d'erreur vide si tout est ok
//			*tab[2]=> adresse de notre fichier après redimensionnement vide si il y a eu une erreur
//------------------------------------------------------------------------
Function redimensionne_img ($nom_files_formulaire, $repertoire_upload, $poids, $largeur, $hauteur)
{

	$bool_erreur=0;
	$msg_erreur="";
	$url="";
	$width_max = $largeur;
	$height_max = $hauteur;
	$poids_max = $poids*1024*1024;
	$nouveau_rep=$repertoire_upload;
	$extension_img= array('.jpg', '.jpeg', '.JPG', '.JPEG');
	if(is_uploaded_file($_FILES[$nom_files_formulaire]["tmp_name"]))
	{
		$nom_image=$_FILES[$nom_files_formulaire]['name']; 
		$recup_extension = strrchr($nom_image, ".");
		$tmp= $_FILES[$nom_files_formulaire]['tmp_name'];
		if(in_array($recup_extension, $extension_img))
		{
			if($_FILES[$nom_files_formulaire]['size'] <= $poids_max)
			{
				$nom_img=uniqid('').".jpg";
				$url=$nouveau_rep.$nom_img;
				move_uploaded_file($tmp,$url);
				$infos_img = getimagesize($url);
				$largeur_photo = $infos_img[0];
				$hauteur_photo = $infos_img[1];
				if($largeur_photo <= $width_max){
					$width_max = $largeur_photo;
				}
				else
				{
					$hauteur_photo = round(($hauteur_photo*$width_max)/$largeur_photo);
					$largeur_photo = $width_max;
				}
				if($hauteur_photo <= $height_max){
					$height_max = $hauteur_photo;
				}
				else
				{
					$largeur_photo = round(($largeur_photo*$height_max)/$hauteur_photo);
					$hauteur_photo = $height_max;
				}

				/*if($hauteur_photo > $height_max){
					$largeur_photo = round(($largeur_photo*$height_max)/$hauteur_photo);
					$hauteur_photo = $height_max;
				}*/
				$chemin_nouvelle_photo = imagecreatefromjpeg($url);
				$nouvelle_photo = imagecreatetruecolor($width_max,$height_max);
				imagefill($nouvelle_photo, 0, 0, 0xFFFFFF);
				$position_l = 0;
				$position_h = 0;
				if ($largeur_photo < $width_max){
					$position_l = round(($width_max-$largeur_photo)/2); // Milieu largeur
				}
				if($hauteur_photo < $height_max){
					$position_h = round(($height_max-$hauteur_photo)/2); // Milieu hauteur
				}
				imagecopyresampled($nouvelle_photo, $chemin_nouvelle_photo, $position_l, $position_h, 0,0,$largeur_photo, $hauteur_photo, $infos_img[0], $infos_img[1]);
				imagejpeg($nouvelle_photo,$url,75);			
			}
			else
			{
				$bool_erreur++;
				$msg_erreur="L\'image (photo) est trop lourde (".$poids."Mo max)";
			}
		}
		else
		{
			$bool_erreur++;
			$msg_erreur="Format de fichier (photo) non correct: .jpeg ou .jpg";
		}
	}
	else
	{
		$bool_erreur++;
		$msg_erreur="Vous devez uploader une image comme photo";
	}
	return array($bool_erreur, $msg_erreur, $url);
}
//---------------------------------------------------------------------
// Fin Fonction de redimensionnement d'image
//---------------------------------------------------------------------
?>