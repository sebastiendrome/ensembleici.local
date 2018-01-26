<?php
header('Content-Type: text/xml'); 

echo ("<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>\n");

require('_connect.php');

/*********************************************
* Récupère les éléments du script ajax et    *
* opère la requête demandée. Les résultats   *
* sont ensuite moulinés en XML.              *
*********************************************/

$champs = $_GET['champs'];
$keyword = $_GET['keyword'];
$keyword2 = utf8_decode($_GET['keyword2']);
$varPOD = array();

/*********************************************
* Nomenclature requetes :                    *
* argument 1 : no                            *
* argument 2 : titre lien                    *
* argument 3-4 : condition pour faire        *
* apparaitre le bouton de choix.             *
*********************************************/
switch ($champs)
{
	//recherche
	case "structure":
		$sql = "
			SELECT structure.no, CONCAT_WS(' ', villes.code_postal, '- (', UCASE(villes.nom_ville_maj), ')'), structure.url_logo, structure.nom
			FROM structure, villes
			WHERE (structure.nom like :keyword OR structure.sous_titre like :keyword)";
		if ($keyword2 != "") 
		{
			$sql .= " AND villes.code_postal like :keyword2";
			$varPOD[':keyword2'] = $keyword2."%";
		}
		$sql .= " AND villes.id=structure.no_ville ORDER BY structure.nom";
		$varPOD[':keyword'] = "%".$keyword."%";
		$result = $connexion->prepare($sql);
		$result->execute($varPOD) or die (print_r($result->errorInfo()));
	break;
	case "annonce":
	case "evenement":
		if ($champs=="annonce")
			$type_genre = "A";
		else
			$type_genre = "E";

		$sql = "
			SELECT evenement.no, CONCAT_WS(' ', villes.code_postal, '- (', UCASE(villes.nom_ville_maj), ')'), evenement.url_image, evenement.titre
			FROM evenement, villes, genre
			WHERE evenement.titre like :keyword
			AND type_genre = :type_genre
			AND genre.no = evenement.no_genre";

		if ($keyword2 != "") 
		{
			$sql .= " AND villes.code_postal like :keyword2";
			$varPOD[':keyword2'] = $keyword2."%";
		}
		$sql .= " AND villes.id=evenement.no_ville ORDER BY evenement.titre";
		$varPOD[':keyword'] = "%".$keyword."%";
		$varPOD[':type_genre'] = $type_genre;
		
		$result = $connexion->prepare($sql);
		$result->execute($varPOD) or die (print_r($result->errorInfo()));
	break;
}

/***********************************
* Résolution SQl et concaténation  *
* en XML.                          *
***********************************/

//echo $sql;
//print_r($varPOD);

$result = $connexion->prepare($sql);
$result->execute($varPOD) or die (print_r($result->errorInfo()));	

	echo ("<retours>\n");
	while ($row = $result->fetch(PDO::FETCH_NUM))
	{
		$link_no = $row[0];
		
		$link_title = $row[3];
		$link_title = mb_ereg_replace("œ", "oe",$link_title); 
		$link_title = strip_tags($link_title);
		$link_title = htmlspecialchars($link_title);
		$link_cp_ville = $row[1];
		$link_image = $row[2];

		
		echo ("<item>\n");
			echo ("
					<no>$link_no</no>\n
				   <titre>".utf8_decode($link_title)."</titre>\n
				   <auto>$link_cp_ville</auto>\n
				   <url_img>$link_image</url_img>\n
			"); 
		echo ("</item>\n");
	}
	echo ("</retours>\n");

?>

