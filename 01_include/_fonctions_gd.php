<?php
function chercherIndicePremiereLargeurInferieure($tab,$largeur,$i=0){
	while($i<count($tab)&&$tab[$i]>=$largeur){
		$i++;
	}
	return $i;
}
function optimisation_image_TaillePoidsQualite($url_image,$poids_max,$indice=-1){
	// 0. On récupère le poids de l'image et déclare le tableau de tailes
	$tab_largeurs_images = array(2560,2048,1920,1680,1440,1280,1024,800,640,320,256,128,96,64,48,32,24,16);
	/*print_r($informations_image);
	echo filesize($url_image);*/
	$poids_courant = filesize($url_image);
	clearstatcache();
	//list($largeur_courante) = @getimagesize($url_image);
	$informations_image = @getimagesize($url_image);
		$largeur_courante = $informations_image[0];
	if($poids_courant>$poids_max){ //Le poids est plus lourd
		if($indice==-1){ //Premier passage
		// 1. On regarde la qualité de l'image
				//le taux de compression c'est assez facile à calculer : tu compares la taille du fichier à ce que ça prendrait sans compression (nombre de pixels x nombre d'octets par pixel, en général 3) : http://forum.webrankinfo.com/moyen-recuperer-qualite-une-jpeg-t30057.html
			/*// 1.1. On récupère le nombre de pixels (que l'on multiplie par 3)
			$poids_theorique = $informations_image[0]*$informations_image[1]*3;
			// 1.2. On a donc maintenant le taux de compression.
			$compression = floor($poids_courant/$poids_theorique*100);*/
			// 1.3. S'il est supérieur à 70%, on compresse de la différence qu'il y a entre le taux actuel et 70% (ex: si le taux courant est de 80%, alors on enregistre à 90%)
			$url_image = compression($url_image,70);
			// 1.4. On passe l'indice à zéro.
			$indice = 0;
		}
		else{
		// 2. Maintenant, il ne s'agit plus de la qualité de l'image, on va réduire sa taille
			// 2.1. On récupère la taille de l'image
			//$largeur_courante = $informations_image[0];
			// 2.2. On récupère le premier indice où la largeur est inférieure
			$indice = chercherIndicePremiereLargeurInferieure($tab_largeurs_images,$largeur_courante,$indice);
			// 2.3. On redimmensionne l'image
			if($indice<count($tab_largeurs_images))
				$url_image = redimmension($url_image,$tab_largeurs_images[$indice]);
		}
		
		if($indice<count($tab_largeurs_images)){ //On peut continuer
			return optimisation_image_TaillePoidsQualite($url_image,$poids_max,$indice);
		}
		else{ //On n'a vraiment pas trouvé de solutions...
			return false;
		}
		
		//return $url_image;
	}
	else{ //Il n'y a (plus) aucune raison d'optimiser le poids de l'image
		return $url_image;
	}
}
function compression($url){
	//1. On ouvre l'image.
	$image = imageCreateFrom($url);
	//2. On l'ecrase avec une qualité de 70
	$url = enregistrerImage($image,$url,70);
	//3. On retourne la nouvelle url.
	return $url;
}
function redimmension($url,$new_width){
	//1. On ouvre l'image.
	$image = imageCreateFrom($url);
	//2. On récupère ses dimmensions.
	list($width, $height, $type, $attr) = @getimagesize($url);
	//3. On calcule la nouvelle hauteur à partir de la nouvelle largeur.
	$new_height = floor($height*$new_width/$width);
	//4. On redimmensionne l'image.
	$new_image = redimmensionGD($image,$new_width,$new_height);
	//5. On enregistre l'image.
	$url = enregistrerImage($new_image,$url);
	//6. On retourne l'url (qui n'a pas changé)
	return $url;
}
function imageToType($url){
	list($width, $height, $type, $attr) = @getimagesize($url);
	//1 = GIF , 2 = JPG , 3 = PNG , 5 = PSD , 6 = BMP
	return (($type==1)?("gif"):(($type==2)?("jpg"):(($type==3)?("png"):(($type==5)?("psd"):(($type==6)?("bmp"):(false))))));
}
function imageCreateFrom($url){
	//$url = stripslashes($url);
	$type = imageToType($url);
	if($type=="gif"){
		$image = imagecreatefromgif($url); //on ouvre l'image source
	}
	else if($type=="jpg"){
		$image = imagecreatefromjpeg($url); //on ouvre l'image source
	}
	else if($type=="png"){
		$image = imagecreatefrompng($url); //on ouvre l'image source
	}
	return $image;
}
function enregistrerImage($image,$url,$qualite=100){
	$type = imageToType($url);
	if($type=="gif"){
		imagegif($image,$url,$qualite);
	}
	else if($type=="jpg"){
		imagejpeg($image,$url,$qualite);
	}
	else if($type=="png"){
		$qualite = floor($qualite*9/100);
		imagepng($image,$url,$qualite);
	}
	imagedestroy($image);
	return $url;
}
function redimmensionGD($img,$x,$y){
	$image = imagecreatetruecolor($x,$y);
	imagealphablending($image,false);
	imagesavealpha($image,true);
	imagecopyresized($image,$img,0,0,0,0, imagesx($image), imagesy($image), imagesx($img), imagesy($img)); //on copie un morceau de l'image
	imagedestroy($img);
	return $image;
}
?>
