<?php
	include '01_include/_connect.php';
	
	//on recupere les informations de nyons
	$select_nyons="SELECT * FROM villes WHERE nom_ville_maj = 'NYONS'";
	$res_nyons= $connexion->prepare($select_nyons);
	$res_nyons->execute() or die ("requete ligne 16 : ".$select_nyons);
	$tab_nyons=$res_nyons->fetchAll();
	
	$nom_nyons=$tab_nyons[0]['nom_ville_maj'];
	$latitude_nyons=$tab_nyons[0]['latitude'];
	$longitude_nyons=$tab_nyons[0]['longitude'];

	$select_drome="SELECT * FROM villes WHERE departement=26 LIMIT 0, 10";
	$res_drome= $connexion->prepare($select_drome);
	$res_drome->execute() or die ("requete ligne 16 : ".$select_drome);
	$tab_drome=$res_drome->fetchAll();
	
	for($indice_drome=0; $indice_drome<count($tab_drome); $indice_drome++)
	{
		$nom_ville=$tab_drome[$indice_drome]['nom_ville_maj'];
		$latitude_ville=$tab_drome[$indice_drome]['latitude'];
		$longitude_ville=$tab_drome[$indice_drome]['longitude'];
		
		$url="http://open.mapquestapi.com/directions/v0/route?outFormat=xml&from=".$latitude_nyons.",".$longitude_nyons."&to=".$latitude_ville.",".$longitude_ville;
		$rss_file = file_get_contents($url);
		$xml = new SimpleXMLElement($rss_file); 
		
		echo $nom_nyons." --> ".$nom_ville."<br/>";
		echo "Distance : ".($xml->route->legs->leg->distance*1.61)." km<br/>";
		echo "Dur&eacute;e : ".$xml->route->legs->leg->formattedTime."<br/><br/>----------<br/>";
		
	
	}
	

	//$url1 ='http://open.mapquestapi.com/directions/v0/route?outFormat=xml&from=44.3667,5.13333&to=44.1833,5.43333';
	/*$url2 ='http://open.mapquestapi.com/directions/v0/route?outFormat=xml&from=44.3667,5.13333&to=44.9333,5.03333';
	
	//$rss_file1 = file_get_contents($url1); 
	$rss_file2 = file_get_contents($url2);
	
	//$xml1 = new SimpleXMLElement($rss_file1); 
	$xml2 = new SimpleXMLElement($rss_file2); 
	echo "Nyons --> Montbrun-les-bains<br/>";
	echo "Distance : ".$xml1->route->legs->leg->distance." km<br/>";
	echo "Dur&eacute;e : ".$xml1->route->legs->leg->formattedTime."<br/><br/><br/>";
	
	echo "Nyons --> Mont&eacute;lier<br/>";
	echo "Distance : ".$xml2->route->legs->leg->distance." km<br/>";
	echo "Dur&eacute;e : ".$xml2->route->legs->leg->formattedTime."<br/><br/><br/>";*/
	
	
?>