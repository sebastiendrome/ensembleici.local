<?php
	include('_connect.php');
	
	$sql_tag="SELECT no, titre FROM tag WHERE no NOT IN (SELECT no_tag FROM tag_sous_tag)";
	$res_tag = $connexion->prepare($sql_tag);
	$res_tag->execute() or die ("requete ligne 45 : ".$sql_tag); 
	$tab_tag = $res_tag->fetchAll();
	
	for($indice_tag=0; $indice_tag<count($tab_tag); $indice_tag++)
	{
		//insertion en sous tag
		$sql_ss_tag="INSERT INTO sous_tag (titre) VALUES (:titre)";
		$result = $connexion->prepare($sql_ss_tag);
		$result->execute(array(":titre"=>$tab_tag[$indice_tag]['titre'])) or die (print_r($result->errorInfo()));
		$no_sous_tag=$connexion->lastInsertId();
		//insertion tag_sous_tag
		$sql_ss_tag="INSERT INTO tag_sous_tag (no_tag, no_sous_tag) VALUES (:no_tag, :no_sous_tag)";
		$result = $connexion->prepare($sql_ss_tag);
		$result->execute(array(":no_tag"=>$tab_tag[$indice_tag]['no'], ":no_sous_tag"=>$no_sous_tag)) or die (print_r($result->errorInfo()));
	}
?>