<?php
//Connexion MySQL
require ('./_connect.php');
require_once('./AutoCompletionCPVille.class.php');
require_once('./_var_ensemble.php');

//Initialisation de la liste
$list = array();

$code_postal = explode(",",$departements_autorise);

//Construction de la requete
$strQuery = "SELECT code_postal as CP, nom_ville_maj as VILLE, id as NO FROM villes WHERE ";
if (isset($_POST["codePostal"]))
{
    $strQuery .= "code_postal LIKE :codePostal ";
	//$strQuery .= "code_postal LIKE :codePostal AND ( ";
}
else
{
    $strQuery .= "nom_ville_maj LIKE :ville ";
	//$strQuery .= "nom_ville_maj LIKE :ville AND ( ";
}

	/*
	for($i=0; $i<sizeof($code_postal); $i++) 
    { 
		if( $i = (sizeof($code_postal)-1) )
		{
			$strQuery .= "code_postal =  $code_postal[$i] ) ";
		}
		else
		{
			$strQuery .= "code_postal =  $code_postal[$i] OR ";
		}
	}
	*/
	
//Limite
/*if (isset($_POST["maxRows"]))
{
    $strQuery .= "LIMIT 0, :maxRows";
}*/
$strQuery .= "ORDER BY code_postal, nom_ville_maj";
$query = $connexion->prepare($strQuery);
if (isset($_POST["codePostal"]))
{
    $value = $_POST["codePostal"]."%";
    $query->bindParam(":codePostal", $value, PDO::PARAM_STR);
}
else
{
    $value = "%".strtoupper($_POST["ville"])."%";
    $query->bindParam(":ville", $value, PDO::PARAM_STR);
}
//Limite
/*if (isset($_POST["maxRows"]))
{
    $valueRows = intval($_POST["maxRows"]);
    $query->bindParam(":maxRows", $valueRows, PDO::PARAM_INT);
}*/

$query->execute();

$list = $query->fetchAll(PDO::FETCH_CLASS, "AutoCompletionCPVille");;

echo json_encode($list);
?>