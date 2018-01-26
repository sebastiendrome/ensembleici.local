<?php
/*************************************************************************************
Ce fichier permet de coloriser une image avec la couleur voulue
	Entrées : $_GET["uri"], $_GET["c"]
	Sorties : L'image à l'adresse $_GET["uri"] colorisée avec la couleur $_GET["c"]
*************************************************************************************/
header('Content-Type: image/png');
if(isset($_GET["uri"])&&$_GET["uri"]!=null&&$_GET["uri"]!=""&&isset($_GET["c"])&&$_GET["c"]!=null&&$_GET["c"]!=""){
	function imagecreatefrom($photo){
		$photo = str_replace("/peigne.jpg","/peigne_old.jpg",$photo);
		list($width, $height, $type, $attr) = @getimagesize($photo);
		//1 = GIF , 2 = JPG , 3 = PNG , 5 = PSD , 6 = BMP
		if($type==1){
			$source_prod = imagecreatefromgif($photo); //on ouvre l'image source
		}
		elseif($type==2){
			$source_prod = imagecreatefromjpeg($photo); //on ouvre l'image source
		}
		elseif($type==3){
			$source_prod = imagecreatefrompng($photo); //on ouvre l'image source
		}
		return $source_prod;
	}
	//0. On vérifie les paramètres.
	$colorize = true;
	$url_image = true;
	// if(substr($_GET["uri"],0,7)!="http://")
		// $_GET["uri"] = "http://www.sudplanete.net/".$_GET["uri"];
	list($x,$y) = @getimagesize($_GET["uri"]);
	if($x==0||$y==0)
		$url_image = false;
	$_GET["c"] = urldecode($_GET["c"]);
	if(substr_count($_GET["c"], ',')==2){ //S'il y a des virgules dans la couleur, elle est au format rvb
		$c = explode(",",$_GET["c"]);
		$color = array("r"=>$c[0],"v"=>$c[1],"b"=>$c[2]);
	}
	else{ //Sinon elle est surement au format hexa.
		//On regarde alors s'il y a le dièse ou pas.
		if(substr($_GET["c"],0,1)=="#"){
			$_GET["c"] = substr($_GET["c"],1,(strlen($_GET["c"])-1));
		}
		//On regarde ensuite la taille de la chaine (3 ou 6)
		if(strlen($_GET["c"])==3){
			$color = array("r"=>hexdec(substr($_GET["c"], 0, 1).substr($_GET["c"], 0, 1)),"v"=>hexdec(substr($_GET["c"], 1, 1).substr($_GET["c"], 1, 1)),"b"=>hexdec(substr($_GET["c"], 2, 1).substr($_GET["c"], 2, 1)));
		}
		else if(strlen($_GET["c"])==6){
			$color = array("r"=>hexdec(substr($_GET["c"], 0, 2)),"v"=>hexdec(substr($_GET["c"], 2, 2)),"b"=>hexdec(substr($_GET["c"], 4, 2)));
		}
		else{
			$colorize = false;
		}
	}
	//Si tous les paramètres sont bon
	if(!$colorize&&$url_image){
		echo "couleur invalide";
	}
	else if($colorize&&!$url_image){
		echo "url invalide";
	}
	else if(!$colorize&&!$url_image){
		echo "url et couleur valides";
	}
	else{
		//1. On créait l'image
		$image = imagecreatefrom($_GET["uri"]);
		imagealphablending($image,false);
		imagesavealpha($image,true);
		//2. On transforme la couleur passée en paramètre.
		// imagefilter($image,IMG_FILTER_GRAYSCALE);
		imagefilter($image,IMG_FILTER_COLORIZE,$color["r"],$color["v"],$color["b"]);
		// imagefilter($image,IMG_FILTER_BRIGHTNESS,-25);
		// imagefilter($image,IMG_FILTER_CONTRAST,3);
		
		imagepng($image);
	}
}
else{ //On renvoi une image 20x20 avec fond noir.
	echo "paramètres invalides";
}
?>