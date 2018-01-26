<?php
// ----------------------------------------
// Fonctions 
// ----------------------------------------
error_reporting(E_ALL ^ E_NOTICE);	//On ne rapporte pas les erreurs NOTICE

 // création d'un identifiant aléatoire
 function id_aleatoire(){
  $elements = "abcdefghijklmnopqrstuvwxyz0123456789AZERTYUIOPMLKJHGFDSQWXCVBN";
  srand(time());
  for ($ligne=0;$ligne<30;$ligne++) {
  	@$retour.=substr($elements,(rand()%(strlen($elements))),1);
  	}
  return $retour;
}

function datefr($date_us,$avecheure=false) { 
  $annee = substr($date_us,0,4);
  $mois = substr($date_us,5,2);
  $jour = substr($date_us,8,2);
  $date_fr = $jour."/".$mois."/".$annee;
  
  if ($avecheure)
  {
   $heu = substr($date_us,11,2);
   $min = substr($date_us,14,2);
   $sec = substr($date_us,17,2);
   $date_fr .= " à ".$heu."h".$min;//.":".$sec;
  }
  return $date_fr;
}
function heurefr($heuresql,$avecsec=false){
	$_h = explode(":",$heuresql);
	return $_h[0]."h".$_h[1].(($avecsec)?"m".$_h[2]."s":"");
}

function datesql($date_fr) { 
  $annee = substr($date_fr,6,4);
  $mois = substr($date_fr,3,2);
  $jour = substr($date_fr,0,2);
  $date_sql = $annee."-".$mois."-".$jour;
  return $date_sql;
} 

// Fonction pour affichage de la date (Du...au...)
function affiche_date_evt($date_du="",$date_au="")
{

  $jour = array("dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi"); 
  $mois = array("","janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre");

  // Date debut
  if (($date_du && !$date_au)||($date_du == $date_au))
  {
    $date_du = strtotime($date_du);
    $date_au = strtotime($date_au);
    // Pas de date de fin
      $aff_date = ucfirst($jour[date("w", $date_du)]).' '.date("d", $date_du).' '.$mois[date("n", $date_du)].' '.date("Y", $date_du);
  } 
  elseif ($date_du && $date_au && ($date_du != $date_au))
  {
    $date_du = strtotime($date_du);
    $date_au = strtotime($date_au);
    // Pas de date de fin
      $aff_date = "du ".$jour[date("w", $date_du)].' '.date("d", $date_du);
      // mois différent (ou année différente)
      if ((date("n", $date_du) != date("n", $date_au)) || (date("Y", $date_du) != date("Y", $date_au)) )
        $aff_date .= " ".$mois[date("n", $date_du)];
      // année différent
      if (date("Y", $date_du) != date("Y", $date_au))
        $aff_date .= " ".date("Y", $date_du);

      $aff_date .= ' au '.$jour[date("w", $date_au)].' '.date("d", $date_au).' '.$mois[date("n", $date_au)].' '.date("Y", $date_au);
  }

  if ($aff_date) return ($aff_date);
}

function redirige($url)
{
 die('<meta http-equiv="refresh" content="0;URL='.$url.'">');
}

/** vérifie le bon format de l'adresse email
 *  @param string $email email à tester
 *  @return true si l'adresse est valide, sinon false
 */
function valid_email($email)
{
  $regexp="/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
  if ( !preg_match($regexp, $email) ) {
       return false;
  }
  return true;
}

/** Prépare une chaîne pour son utilisation dans une URL
 *  @param string $url chaîne à convertir
 *  @return $url : la chaîne formatée
 */
 /*
function url_rewrite($url)
{
 if ($url)
 {
  // Mise en minuscule
  $url = strtolower($url);
  //On supprime les accents
  $url = htmlentities($url, ENT_NOQUOTES,'utf-8'); 
  $url = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $url);
  $url = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $url); // pour les ligatures e.g. '&oelig;'
  $url = preg_replace('#&[^;]+;#', '', $url); // supprime les autres caractères
  // On supprime les caractères spéciaux, ponctuation, ...
  $url = preg_replace('/[^A-Za-z0-9-]+/', '-', $url);
  // On remplace les tirets multiples qui se suivent par un seul tiret
  $url = preg_replace('#-{2,}#', '-', $url);
  // Renvoi url rewritée
  return $url;
 }
}*/
function url_rewrite($url)
{
 if(!empty($url))
 {
  //On supprime les accents
  $url = htmlentities($url, ENT_NOQUOTES,'utf-8'); 
  $url = preg_replace('#&([A-Za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $url);
  $url = preg_replace('#&([A-Za-z]{2})(?:lig);#', '\1', $url); // pour les ligatures e.g. '&oelig;'
  $url = preg_replace('#&[^;]+;#', '', $url); // supprime les autres caractères
  // On supprime les caractères spéciaux, ponctuation, ...
  $url = preg_replace('/[^A-Za-z0-9-]+/', '-', $url);
  // On remplace les tirets multiples qui se suivent par un seul tiret
  $url = preg_replace('#-{2,}#', '-', $url);
  // Renvoi url rewritée
  return strtolower($url);
 }
}


/** Couper une chaine au nb_car caractere
 *  @param string $chaine : chaîne à couper
 *  @param int $nb_car : nombre de carractères maximum
 *  @param booleen $aff_suite : affiche [...] si vrai
 *  @return $url : la chaîne coupée
 */
function coupe_chaine($chaine, $nb_car,$aff_suite=false)
{
  if(strlen($chaine)>=$nb_car)
  {
   // Supprime les tags html
   $chaine=strip_tags($chaine);
   // Met la portion de chaine dans $chaine
   $chaine=substr($chaine,0,$nb_car); 
   // position du dernier espace
   $espace=strrpos($chaine," "); 
   // test si il ya un espace
   if($espace)
    // si ya 1 espace, coupe de nouveau la chaine
    $chaine=substr($chaine,0,$espace);
   // Ajoute ... à la chaine
   if ($aff_suite) $chaine .= ' [...]';
  }
 // Renvoi url rewritée
 return $chaine;
}

/** Prépare une chaîne pour affichage comme un numéro de tel
 *  @param string $numTel chaîne à convertir
 *  @return $numTel : la chaîne formatée
 */
function FormatTel($numTel) {
  $i=0;
  $j=0;
  $formate = "";
  while ($i<strlen($numTel)) { //tant qu il y a des caracteres
    if ($j < 2) {
	  if (preg_match('/^[0-9]$/', $numTel[$i])) { //si on a bien un chiffre on le garde
		$formate .= $numTel[$i];
		$j++;
	  }
	  $i++;
    }
    else { //si on a mis 2 chiffres a la suite on met un espace
      $formate .= " ";
      $j=0;
    }
  }
  return $formate;
}

/** Formate l'affichage d'un prix
 *  @param string $prix chaîne à convertir
 *  @return $prix : la chaîne formatée
 */
function FormatPrix($prix,$no_symbol = false) {

  if(($prix)&&($prix!="0.00"))
  {
    $prix = number_format($prix, 2, ',', ' ');
    // Supprime les décimales si nulles
    if (substr($prix, -1, 2)=="00") $prix = str_replace(",00","",$prix);
    // Symbole euro
    if (!$no_symbol) $prix = $prix." &euro;";
  }

  return $prix;
}
/** Prépare une chaîne en enlevant les infos inutiles 
 *  @param string $text : chaîne à convertir, $allowed_tags : tags autorisés, non supprimés
 *  @return $text : la chaîne formatée
 */
function strip_word_html($text, $allowed_tags = '<b><i><sup><sub><em><strong><u><br>') 
{
    mb_regex_encoding('UTF-8'); 
    //replace MS special characters first 
    $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u'); 
    $replace = array('\'', '\'', '"', '"', '-'); 
    $text = preg_replace($search, $replace, $text); 
    //make sure _all_ html entities are converted to the plain ascii equivalents - it appears 
    //in some MS headers, some html entities are encoded and some aren't 
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8'); 
    //try to strip out any C style comments first, since these, embedded in html comments, seem to 
    //prevent strip_tags from removing html comments (MS Word introduced combination) 
    if(mb_stripos($text, '/*') !== FALSE){ 
        $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm'); 
    } 
    //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be 
    //'<1' becomes '< 1'(note: somewhat application specific) 
    $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text); 
    $text = strip_tags($text, $allowed_tags); 
    //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one 
    $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text); 
    //strip out inline css and simplify style tags 
    $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu'); 
    $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>'); 
    $text = preg_replace($search, $replace, $text); 
    //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears 
    //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains 
    //some MS Style Definitions - this last bit gets rid of any leftover comments */ 
    $num_matches = preg_match_all("/\<!--/u", $text, $matches); 
    if($num_matches){ 
          $text = preg_replace('/\<!--(.)*--\>/isu', '', $text); 
    } 
    return $text; 
}

/** Teste si un fichier distant existe
 *  @param string $webfile : Fichier à tester
 *  @return $fp : true / false : fichier existant ou non
 */
function fichier_existant($webfile)
{
  $file_headers = @get_headers($webfile);
  if($file_headers[0] == 'HTTP/1.1 404 Not Found')
      return false;
  else
      return true;
}

/*
function est_connecte(){
	global $connexion;
	if(count($_SESSION)>0&&isset($_SESSION["UserConnecte"])&&isset($_SESSION["UserConnecte_email"])){ //Une session existe.
		//On compare alors les id aléatoires et l'adresse mail avec ceux de la session.
		$requete = "SELECT no FROM utilisateur WHERE id_connexion=:id AND email=:e";
		$res = $connexion->prepare($requete);
		$res->execute(array(":id"=>$_SESSION["UserConnecte"],":e"=>$_SESSION["UserConnecte_email"]));
		$tab = $res->fetchAll();
		$retour = (count($tab)>0);
		if(!$retour){
			//L'utilisateur s'est sûrement connecté ailleurs, on vide la session.
			unset($_SESSION);
			session_close();
		}
	}
	else $retour = false;
	return $retour;
}*/

function est_admin(){
	global $connexion;
	if(est_connecte()){
		$requete = "SELECT no FROM utilisateur WHERE id_connexion=:id AND email=:e AND droits='A'";
		$res = $connexion->prepare($requete);
		$res->execute(array(":id"=>$_SESSION["UserConnecte"],":e"=>$_SESSION["UserConnecte_email"]));
		$tab = $res->fetchAll();
		$retour = (count($tab)>0);
	}
	else $retour = false;
	return $retour;
}

function datehome($date_us){
	$MOIS = array("Jan","fev","mars","avr","mai","juin","juil","aout","sept","oct","nov","déc");
	$_d = explode(" ",$date_us);
	$_d = explode("-",$_d[0]);
	$annee = $_d[0];
	$mois = $MOIS[(((int)$_d[1])-1)];
	$jour = $_d[2];
	return '<div class="jr">'.$jour.'</div><div class="ms_an"><div>'.$mois.'</div><div>'.$annee.'</div></div>';
}
?>
