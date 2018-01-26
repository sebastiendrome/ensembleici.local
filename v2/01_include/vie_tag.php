<?php
header('Content-Type: text/xml'); 

echo ("<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>\n");

require('01_include/_connect.php');

/*********************************************
* R�cup�re les �l�ments du script ajax et    *
* op�re la requ�te demand�e. Les r�sultats   *
* sont ensuite moulin�s en XML.              *
*********************************************/


$keyword = $_GET['keyword'];
$varPOD = array();

/*********************************************
* Nomenclature requetes :                    *
* argument 1 : no                            *
* argument 2 : titre lien                    *
* argument 3-4 : condition pour faire        *
* apparaitre le bouton de choix.             *
*********************************************/
$sql = "SELECT no, titre
		FROM tag, vie_tag
		WHERE no_vie = :keyword AND no_tag=no ORDER BY titre";
		$varPOD[':keyword'] = $keyword;
		$result = $connexion->prepare($sql);
		$result->execute($varPOD) or die (print_r($result->errorInfo()));

/***********************************
* R�solution SQl et concat�nation  *
* en XML.                          *
***********************************/

$result = $connexion->prepare($sql);
$result->execute($varPOD) or die (print_r($result->errorInfo()));	

	echo ("<retours>\n");
	while ($row = $result->fetch(PDO::FETCH_NUM))
	{
		$link_no = $row[0];
		
		$link_title = $row[1];
		$link_title = mb_ereg_replace("�", "oe",$link_title); 
		$link_title = strip_tags($link_title);
		$link_title = htmlspecialchars($link_title);

		
		echo ("<item>\n");
		echo ("
					<no>$link_no</no>\n
					<titre>".utf8_decode($link_title)."</titre>\n
			"); 
		echo ("</item>\n");
	}
	echo ("</retours>\n");

?>

