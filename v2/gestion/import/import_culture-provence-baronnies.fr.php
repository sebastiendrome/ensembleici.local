<?php
// 50 évènements en cours et à venir. L'évènement le plus proche dans le temps, après les évènements encore en cours, est en haut de la liste.
session_name("EspacePerso");
session_start();
require_once "config.php";

ini_set('soap.wsdl_cache_enabled', 0);

// Fonction pour log, archivage automatique par année
function errlogtxt($errtxt){
	$fp = fopen("log-import.txt",'a+');
	fseek($fp,SEEK_END);
	$nouverr=$errtxt."\r\n";
	fputs($fp,$nouverr);
	fclose($fp);
}

try {
	errlogtxt("Importation des évènement de www.culture-provence-baronnies.fr lancée le ".date("d/m/Y à H:i:s")."\n");
	
	$client = new SoapClient('http://www.culture-provence-baronnies.fr/websrv_events.wsdl', array('encoding'=>'UTF-8'));
	$oReturn =  $client ->getEvents("africultures","soap!2605");
	$table_evt = "evenement";
	
	if(is_string($oReturn)){
		echo $oReturn;
	}else{
		foreach($oReturn->Array as $ligne){
			
			$description_complementaire = "";
			$adresse = "";
			$noville = 0;
			$site = "";
			$i++;
			
			// L'évènement est déjà dans notre base ?
			$sql_evenement="SELECT no FROM ".$table_evt." WHERE source_id=:source_id";
			$res_evenement = $connexion->prepare($sql_evenement);
			$res_evenement->execute(array(':source_id'=>$ligne->string[0])) or die ("Erreur 46 : ".$sql_evenement);
			$tab_evenement = $res_evenement->fetchAll();

			if (count($tab_evenement) == 0)
			{
				// Description complementaire
				// Résa obligatoire
				if ($ligne->string[4]=="oui")
				$description_complementaire .= "Réservation obligatoire<br/>";
				// Tarif
				if ($ligne->string[6])
				{
					$description_complementaire .= "Tarif : ".$ligne->string[6];
					if (is_int($ligne->string[6])) $description_complementaire .= " &euro;";
					$description_complementaire .= "<br/>";
				}
				// Fichier joint
				if ($ligne->string[16])
				$description_complementaire .= "<a href=\"".$ligne->string[16]."\" title=\"Plus d'infos\">Plus d'infos (Fichier attaché)</a><br/>";
				// Importé depuis
				$description_complementaire .= "<p><a href=\"http://www.culture-provence-baronnies.fr\" title=\"Evènement importé depuis Culture Provence Baronnies\">Evènement importé depuis<br/><img src=\"/img/logo-culture-provence-baronnies.jpg\" alt=\"Evènement importé depuis Culture Provence Baronnies\" /></a></p>";
			
				// Adresse
				$adresse = $ligne->string[9];
				if ($ligne->string[10]) $adresse .= "<br/>".$ligne->string[10];
				if ($ligne->string[11]) $adresse .= "<br/>".$ligne->string[11];
	
				// Site web
				if ($ligne->string[2] != "N/C")
					$site = $ligne->string[2];
				else
					$site = "";

				// Image
				if ($ligne->string[17])
					$image = $ligne->string[17];
				else
					$image = "";
					
				// recuperation de l'id de la ville
				$nom_ville = str_replace(", DROME", "", $ligne->string[13]);
				$sql_villes = "SELECT id FROM villes
						WHERE ((code_postal=:code_postal)
						OR (code_insee=:code_postal))
						AND nom_ville_maj=:nom_ville_maj";
				$res_villes = $connexion->prepare($sql_villes);
				$res_villes->execute(array(":code_postal"=>$ligne->string[12],":nom_ville_maj"=>$nom_ville)) or die ("Erreur 78 : ".$sql_villes);
				$tab_villes=$res_villes->fetchAll();
				if (count($tab_villes) > 0)
					$noville = $tab_villes[0]['id'];
				else
					$noville = 0;
	
				//insertion des donnees
				$sql_insere_evenement = "INSERT INTO ".$table_evt." (
					date_debut,
					date_fin,
					titre,
					description,
					description_complementaire,
					url_image,
					site,
					telephone,
					no_utilisateur_creation,
					date_creation,
					adresse,
					no_ville,
					source_nom,
					source_id,
					validation,
					etat
				)
				VALUES 
				(
					:date_debut,
					:date_fin,
					:titre,
					:description,
					:description_complementaire,
					:url_image,
					:site,
					:telephone,
					0,
					NOW(),
					:adresse,
					:no_ville,
					'Culture Provence Baronnies',
					:source_id,
					0,
					0
				)";
				$insert = $connexion->prepare($sql_insere_evenement);
				$insert->execute(array(
					':date_debut'=>$ligne->string[7],
					':date_fin'=>$ligne->string[8],
					':titre'=>$ligne->string[1],
					':description'=>$ligne->string[3],
					':description_complementaire'=>$description_complementaire,
					':url_image'=>$image,
					':site'=>$site,
					':telephone'=>$ligne->string[5],
					':adresse'=>$adresse,
					':no_ville'=>$noville,
					':source_id'=>$ligne->string[0]
				)) or die ("Erreur 90 ($i): ".$sql_insere_evenement);
				
				// Log de l'evt importé
				$no_evt_ei = $connexion->lastInsertId();
				if ($evt_importe) $evt_importe .= ", ";
				$evt_importe .= $ligne->string[0]." (".$no_evt_ei.")"; 
			}
			else
			{
				// Log evt déjà importé
				if ($deja_importe) $deja_importe .= ", ";
				$deja_importe .= $ligne->string[0]." (".$tab_evenement[0]['no'].")"; 
			}
		}
		if ($deja_importe) errlogtxt("\nNuméros CPB (EI) des évènements déjà importés, non réimportés : \n".$deja_importe."\n");
		if ($evt_importe) errlogtxt("\nNuméros CPB (EI) des évènements importés : \n".$evt_importe."\n");

		errlogtxt("\nImportation terminée le ".date("d/m/Y à H:i:s")."\n\n* * * * * * * * * * * * * * * * *\n");
		$message = "Importation terminée avec succès.";

	}

} catch (SoapFault $fault) {
	errlogtxt("\nErreur de l'importation le ".date("d/m/Y à H:i:s")."\n".$fault."\n* * * * * * * * * * * * * * * * *\n\n");
	$message = "Erreur lors de l'importation. (voir le fichier log)";
}
if ($message) $_SESSION['message'] .= $message."<br/>";
header("location:admin.php");
exit();
?>