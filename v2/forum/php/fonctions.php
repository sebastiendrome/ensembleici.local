<?php
	//connexion à la base de données
	require ('_connect.php');
			
			
function extraireLien($chaine)
{	
	$tab = explode("src=", htmlentities($chaine));	
	$string1 = htmlentities($tab[1]);	
	$tab2 = explode("&amp;quot;", $string1);		
	return $tab2[1];
}

function ajouterVue()
{	
	require ('_connect.php');
	$sql="UPDATE fichiers 
		SET nb_vue = nb_vue+1
		WHERE no = :no";		
		
	$result = $connexion->prepare($sql);
	$result->execute(array(':no'=>$_POST['no'])) or die ("requete ligne 24 : ".$sql);
}

function nlToBr($t){
    $t = str_replace("<br/>","<br />",$t);
    $t = str_replace("<br>","<br />",$t);
    $t = str_replace("<br >","<br />",$t);
    $t = str_replace("\r\n","<br />",$t);
    $t = str_replace("\n\r","<br />",$t);
    $t = str_replace("\r","<br />",$t);
    $t = str_replace("\n","<br />",$t);
    return $t;
} 

function nlToBrSimple($t){
    $t = str_replace("<br/>","<br />",$t);
    $t = str_replace("<br>","<br />",$t);
    $t = str_replace("<br >","<br />",$t);
    $t = str_replace("\r\n","",$t);
    $t = str_replace("\n\r","",$t);
    $t = str_replace("\r","",$t);
    $t = str_replace("\n","",$t);
    return $t;
}
?>