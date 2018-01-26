<?php
	require ('01_include/_var_ensemble.php');
	require ('01_include/_connect.php');	
	
		//On instancie un nouveai doc
		$dom = new DomDocument(); 
		$dom->load('flux_rss.xml');

		//requete
		$reponse = $connexion->prepare('SELECT * FROM evenement ORDER BY no DESC LIMIT 5' );
		$reponse->execute();
			
		//addOneNews($file,$data_evt['titre'],$data_evt['date_debut'],$data_evt['sous_titre']);
					
		//Traitement
		while ($donnees = $reponse->fetch())
		{
							
			$title = $donnees['titre'];
			$date = $donnees['date_debut'];
			$description = $donnees['description'];
			$no_ville = $donnees['no_ville'];
			$no_objet = $donnees['no'];
			
			echo $date;
			// Récup de la ville
			$sql_ville="SELECT nom_ville_maj, nom_ville_url, nom_ville, code_postal
					  FROM `villes`
					  WHERE id=:no_ville";
			$resville = $connexion->prepare($sql_ville);
			$resville->execute(array(':no_ville'=>$donnees['no_ville'])) or die ("Erreur 52 : ".$sql_ville);
			$rowville=$resville->fetchAll();
			$nom_ville_url = $rowville[0][nom_ville_url];
			$nom_ville = $rowville[0][nom_ville];

			// Récup du genre
			if(no_genre)
			{
				$sql_genre="SELECT libelle
						  FROM `genre`
						  WHERE no=:no_genre";
				$resgenre = $connexion->prepare($sql_genre);
				$resgenre->execute(array(':no_genre'=>$donnees['no_genre'])) or die ("Erreur 61 : ".$sql_genre);
				$rowgenre=$resgenre->fetchAll();
				$nom_genre = $rowgenre[0][libelle];
			}
			
			$titre_pour_lien = coupe_chaine($title,130,false);
			if ($nom_genre)
				$titre_pour_lien = $nom_genre."-".$titre_pour_lien;
					  
			$lien_voir = strtolower($root_site."evenement".".".$nom_ville_url.".".url_rewrite($titre_pour_lien).".".$no_ville.".".$no_objet.".html");
			
							// supprimer les style ci dessous
							$description = preg_replace("/font-family\:.+?;/i", "", $description);
							$description = preg_replace("/font-family\:.+?\"/i", "", $description);
	
							$description = preg_replace("/background-color\:.+?;/i", "", $description);
							$description = preg_replace("/line-height\:.+?;/i", "", $description);
							// Supprimer les commentaires html (word)
							$description = preg_replace('/<!--.*?-->/s', '', $description);
							$description = preg_replace('/\\r\\n|\\r|\\n/', '<br />', $description);
							//$description = htmlentities($description);
							$description = coupe_chaine($description, 300,$aff_suite=true);
							
			
			//on crée tous les noeuds 
			$nouvelitem = $dom->createElement("item");
			$nouvelitem_title = $dom->createElement("title");
			$nouvelitem_description = $dom->createElement("description");
			$nouvelitem_author = $dom->createElement("author");
			$nouvelitem_link = $dom->createElement("link");
			
			//on créer les noeuds texte - titre
			$texte_title = $dom->createTextNode($nom_ville." : ".$title." | ".$date);
			$nouvelitem_title->appendChild($texte_title);
			//date
			$texte_description = $dom->createTextNode("<strong>".$nom_genre."</strong><br />".$description);
			$nouvelitem_description->appendChild($texte_description);
			//lieu
			$texte_author = $dom->createTextNode($date);
			$nouvelitem_author->appendChild($texte_author);
			//link
			$texte_link = $dom->createTextNode($lien_voir);
			$nouvelitem_link->appendChild($texte_link);
			
			//on place les noeuds au bon edroit
			$nouvelitem->appendChild($nouvelitem_title);
			$nouvelitem->appendChild($nouvelitem_description);
			$nouvelitem->appendChild($nouvelitem_author);
			$nouvelitem->appendChild($nouvelitem_link);
			
			$channel = $dom->getElementsByTagName("channel")->item(0);
			$channel->appendChild($nouvelitem);
			
			$dom->save('flux_rss.xml');
						
			}
			// Termine le traitement de la requête
			$reponse->closeCursor(); 
?>