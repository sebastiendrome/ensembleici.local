<?php
include "../../../01_include/_var_ensemble.php";
require_once('../../../01_include/_connect.php');
$root_site = str_replace('v2/','',$root_site);
if(isset($_POST["no"])&&$_POST["no"]!=null&&$_POST["no"]!=""&&$_POST["no"]!=0){
	function difference_url($url2,$url1=''){
		 //Cette fonction renvoie le chemin pour aller de url1 vers url2
		//difference_url(url_a_atteindre[,url_de_reference])
			//si url_de_reference n'est pas sp�cifi�e, alors on se base sur le fichier actuel
			//url_a_atteindre doit obligatoirement �tre un fichier et non un dossier
			//si le nom de domaine � la base est diff�rent, la fonction retourne false
		if($url1==''){
			$url1=$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
		}
		//1. On enl�ve http:// et l'�ventuel slash de fin de dossier
		if(substr($url1,0,7)=="http://")
			$url1 = substr($url1,8,strlen($url1));
		if(substr($url2,0,7)=="http://")
			$url2 = substr($url2,7,strlen($url2));
		if(substr($url2,-1,1)=="/")
			$url2 = substr($url2,0,strlen($url2)-1);
		//2. on explode par rapport aux "/"
		$les_parties_url1 = explode("/",$url1);
		$les_parties_url2 = explode("/",$url2);
		$i = 0;
		if($les_parties_url1[$i]==$les_parties_url2[$i]&&!is_dir($url1)){ //On v�rifie d�j� que le nom de domaine est identique
			$i++;//On passe alors au premier dossier
			while($i<count($les_parties_url1)-1&&$i<count($les_parties_url2)-1&&$les_parties_url1[$i]==$les_parties_url2[$i]){
				$i++;
			}
			if($i==count($les_parties_url1)-1){
				//On est all� jusqu'� la fin de l'url 1
				//Le fichier de l'url 2 est alors soit dans le m�me dossier, soit dans un sous dossier.
				if($i==count($les_parties_url2)-1){
					//Le fichier est dans le m�me dossier.
					$url_fin = $les_parties_url2[$i];
				}
				else{
					//Le fichier est dans un sous dossier.
					$url_fin = "";
					for($j=$i;$j<count($les_parties_url2);$j++){
						if($url_fin!="")
							$url_fin.="/";
						$url_fin.=$les_parties_url2[$j];
					}
				}
			}
			else{
				//On n'est pas all� jusqu'au bout de la premi�re url
				if($i==count($les_parties_url2)-1){
					//On est all� jusqu'a la fin de url2, ce qui signifie que le fichier d'url 2 se situe dans un dossier parent
					$url_fin = "";
					for($j=$i;$j<count($les_parties_url1)-1;$j++){
						$url_fin.="../";
					}
					$url_fin.=$les_parties_url2[count($les_parties_url2)-1];
				}
				else{
					//On n'est all� ni au bout d'une url, ni au bout de l'autre : le fichier est dans deux dossiers diff�rents
					//1. On remonte url1
					$url_fin = "";
					for($j=$i;$j<count($les_parties_url1)-1;$j++){
						$url_fin.="../";
					}
					//2. On descend url2
					$url_fin2 = "";
					for($j=$i;$j<count($les_parties_url2);$j++){
						if($url_fin2!="")
							$url_fin2.="/";
						$url_fin2.=$les_parties_url2[$j];
					}
					$url_fin .= $url_fin2;
				}
			}
			if(!strpos($les_parties_url2[count($les_parties_url2)-1], "."))
				$url_fin .= "/";
			return $url_fin;
		}
		else{
			return false;
		}
	}
	$no = $_POST["no"];
	if(isset($_POST["cancel"])&&$_POST["cancel"]==1){ //Annuler validation
		//On r�cup�re le repertoire, et no_envoi de la lettre
		$requete_lettre = "SELECT repertoire,no_envoi FROM lettreinfo WHERE no=:no";
		$res_lettre = $connexion->prepare($requete_lettre);
		$res_lettre->execute(array(":no"=>$no)) or die("erreur requ�te ligne 116 : ".$requete_lettre);
		$tab_lettre = $res_lettre->fetchAll();
		$repertoire = $tab_lettre[0]["repertoire"];
		$no_envoi = $tab_lettre[0]["no_envoi"];
		
		//On le supprime
		if(is_dir(difference_url($repertoire))){
			if(is_file(difference_url($repertoire."index.html")))
				unlink(difference_url($repertoire."index.html"));
			if(is_file(difference_url($repertoire."index.php")))
				unlink(difference_url($repertoire."index.php"));
			if(is_file(difference_url($repertoire."txt.txt")))
				unlink(difference_url($repertoire."txt.txt"));
			rmdir(difference_url($repertoire));
		}
		
		//On supprime l'entr�e correspondant � no_envoi.
		$requete_delete = "DELETE FROM lettreinfo_envoi WHERE no=:no";
		$res_delete = $connexion->prepare($requete_delete);
		$res_delete->execute(array(":no"=>$no_envoi)) or die("erreur requ�te ligne 116 : ".$requete_delete);
		
		//On repasse repertoire � vide et no_envoi � 0
		$requete_update = "UPDATE lettreinfo SET repertoire='',no_envoi=0 WHERE no=:no";
		$res_update = $connexion->prepare($requete_update);
		$res_update->execute(array(":no"=>$no)) or die("erreur requ�te ligne 116 : ".$requete_update);
		
		$reponse = array(true,utf8_encode("la lettre d'information n'est plus valid�e"));
	}
	else{ //Validation
		//On r�cup�re la date de d�but de la lettre
		$requete_lettre = "SELECT date_debut, territoires_id FROM lettreinfo WHERE no=:no";
		$res_lettre = $connexion->prepare($requete_lettre);
		$res_lettre->execute(array(":no"=>$no)) or die("erreur requ�te ligne 116 : ".$requete_lettre);
		$tab_lettre = $res_lettre->fetchAll();
		$rep = str_replace("-","_",$tab_lettre[0]["date_debut"]).'_'.$tab_lettre[0]["territoires_id"];
		$repertoire = $root_site."02_medias/10_lettreinfo/".$rep."/";
		$url_courante = $root_admin."lettreinfo/lettre_en_cours/index.php?no=".$no;
		
		//On enregistre lettreinfo_envoi.no et repertoire dans lettreinfo pour la ligne correspondant � la lettre.
		$requete_update = "UPDATE lettreinfo SET repertoire=:r WHERE no=:no";
		$res_update = $connexion->prepare($requete_update);
		$res_update->execute(array(":no"=>$no,":r"=>$repertoire)) or die("erreur requ�te ligne 116 : ".$requete_update);
		
		//On execute la lettre et r�cup�re le texte HTML
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_courante);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$txt_HTML = curl_exec($ch);
		curl_close($ch);
		
		$txt_PHP = preg_replace("#(\[\*\*idv\*\*\])#i","<?php echo \$_GET['v']; ?>",$txt_HTML);
		$txt_PHP = "<?php if(\$_GET['v']=='') \$_GET['v']=9568; else \$_GET['v'] = (int)\$_GET['v']; ?>".$txt_PHP;
		
		//On r�cup�re le texte TXT
		$txt_TXT = strip_tags($txt_HTML);
		//On enregistre les deux fichiers dans le repertoire correspondant � date_debut
		if(!is_dir(difference_url($repertoire))){
			mkdir(difference_url($repertoire));
		}
			//On ouvre index.php et le rempli du code html
			$index = fopen(difference_url($repertoire."index.php"), "w");
			fwrite($index, $txt_PHP);
			fclose($index);
			//On ouvre txt.html et le rempli du code txt
			$index_txt = fopen(difference_url($repertoire."txt.txt"), "w");
			fwrite($index_txt, $txt_TXT);
			fclose($index_txt);
		
		//On cr�ait une ligne dans lettreinfo_envoi
		$requete_insert = "INSERT INTO lettreinfo_envoi(contenu_html,contenu_txt) VALUES(:html,:txt)";
		$res_insert = $connexion->prepare($requete_insert);
		$res_insert->execute(array(":txt"=>$txt_TXT,":html"=>$txt_HTML)) or die("erreur requ�te ligne 116 : ".$requete_insert);
		$no_envoi = $connexion->lastInsertId();
		
		//On enregistre lettreinfo_envoi.no et repertoire dans lettreinfo pour la ligne correspondant � la lettre.
		$requete_update = "UPDATE lettreinfo SET no_envoi=:n WHERE no=:no";
		$res_update = $connexion->prepare($requete_update);
		$res_update->execute(array(":no"=>$no,":n"=>$no_envoi)) or die("erreur requ�te ligne 116 : ".$requete_update);
		
		//On renvoi lettreinfo_envoi.no et repertoire.
		$reponse = array(true,$no_envoi,$repertoire);
	}
}
else{
	$reponse = array(false,utf8_encode("une erreur est survenue !"));
}
echo json_encode($reponse)
?>
