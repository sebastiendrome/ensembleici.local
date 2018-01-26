<?php
session_name("EspacePerso2");
session_start();
//require('/home/ensemble/www/00_dev_sam/01_include/_var_ensemble.php');
//include "/home/ensemble/www/00_dev_sam/01_include/_fonctions.php";
require('/home/ensemble/01_include/_var_ensemble.php');
include "/home/ensemble/01_include/_fonctions.php";
if(!empty($_POST["type"])&&($_POST["type"]=="evenement"||$_POST["type"]=="editorial"||$_POST["type"]=="forum"||$_POST["type"]=="structure"||$_POST["type"]=="petite-annonce")){
	$reponse = array(true);
	if($_POST["type"]=="evenement"){
		$nomTablePrincipale = "evenement";
		$nomChampTitre = "titre";
	}
	else if($_POST["type"]=="editorial"){
		$nomTablePrincipale = "editorial";
		$nomChampTitre = "titre";
	}
	else if($_POST["type"]=="structure"){
		$nomTablePrincipale = "structure";
		$nomChampTitre = "nom";
	}
	else if($_POST["type"]=="forum"){
		$nomTablePrincipale = "forum";
		$nomChampTitre = "titre";
	}
	else{
		$nomTablePrincipale = "petiteannonce";
		$nomChampTitre = "titre";
	}

	$keyword = strtolower(urldecode($_POST['m']));
	$regex_chiffre = "#^[0-9]+$#iu";
	$regex_mail = "#^[a-z0-9._%+-]+@[A-Z0-9.-]*$#iu";

	if(preg_match($regex_chiffre,$keyword)){ //C'est un chiffre
		//On recherche dans cp puis dans no_utilisateur, puis dans no_ville, puis dans no_item (3cp ou on complète avec no_item/no_user/no_ville)
		$requete_cp = "SELECT 'code_postal' AS type, villes.code_postal AS libelle FROM villes WHERE villes.code_postal LIKE '%".$keyword."%' GROUP BY villes.code_postal LIMIT 3";
		$tab_cp = execute_requete($requete_cp);
		if(count($tab_cp)>=3){ //Si on a assez de résultats, on retourne les codes postaux
			$reponse = $tab_cp;
		}
		else{ //Sinon on recherche alors dans no_item, puis no_utilisateur, puis no_ville
			$requete_ville = "SELECT 'ville' AS type, villes.nom_ville_maj AS libelle, villes.id AS no FROM villes WHERE villes.id LIKE '%".$keyword."%' GROUP BY villes.id LIMIT 3";
			$tab_ville = execute_requete($requete_ville);
			$requete_item = "SELECT '".$_POST["type"]."' AS type, ".$nomTablePrincipale.".".$nomChampTitre." AS libelle, ".$nomTablePrincipale.".no FROM ".$nomTablePrincipale." WHERE ".$nomTablePrincipale.".no LIKE '%".$keyword."%' GROUP BY evenement.no LIMIT 3";
			$tab_item = execute_requete($requete_item);
			$requete_utilisateur = "SELECT 'utilisateur' AS type, IF(utilisateur.pseudo='',utilisateur.email,utilisateur.pseudo) AS libelle, utilisateur.no FROM utilisateur WHERE utilisateur.no LIKE '%".$keyword."%' GROUP BY utilisateur.no LIMIT 3";
			$tab_utilisateur = execute_requete($requete_utilisateur);
			//On fusionne les 4 tableaux
			$reponse = array_merge($tab_cp,array_merge($tab_item,array_merge($tab_utilisateur,$tab_ville)));
		}
	}
	else{
		if(preg_match($regex_mail,$keyword)){ //Ça ressemble à un email
			//On recherche alors les emails dans utilisateur
			$requete_utilisateur = "SELECT 'utilisateur' AS type, IF(utilisateur.pseudo='',utilisateur.email,utilisateur.pseudo) AS libelle, utilisateur.no FROM utilisateur WHERE email LIKE '%".$keyword."%' LIMIT 3";
			$tab_utilisateur = execute_requete($requete_utilisateur);
			if(count($tab_utilisateur)>=3){
				$reponse = $tab_utilisateur;
			}
			else{ //Sinon on recherche alors dans le champ email de l'item (s'il existe)
				$requete_item = "SELECT '".$_POST["type"]."' AS type, ".$nomTablePrincipale.".".$nomChampTitre." AS libelle, ".$nomTablePrincipale.".no FROM ".$nomTablePrincipale." WHERE email LIKE '%".$keyword."%' LIMIT 3";
				$tab_item = execute_requete($requete_item);
				//On fusionne les deux tableaux
				$reponse = array_merge($tab_utilisateur,$tab_item);
			}
		}
		else{ //C'est une chaine normale
			//On la sécurise
			$keyword = preg_replace("/[^a-z0-9À-ÿ.@]/iu",' ', strtolower(urldecode($_POST['m'])));
				//Sinon on recherche dans pseudo, email de utilisateur et dans nom_ville_maj et dans titre. (1 utilisateur, 1 ville, 1 item)
				//On la passe dans les regex
				$les_mots_cles = array(); //Ce tableau contient les regex pour souligner la recherche.
				$tab_mot_rech = explode(" ",$keyword);
				$cond_cp = "";
				$cond_ville = "";
				$cond_utilisateur = "";
				for($i_rech=0;$i_rech<count($tab_mot_rech);$i_rech++){
					if(strlen($tab_mot_rech[$i_rech])>0){
						//REGEX POUR LE SURLIGNE RECHERCHE
						$mot_cle = preg_replace('#[aãàâä]#iu', '([aãàâä]|(&a[a-z]{3,6};))', $tab_mot_rech[$i_rech]); //(&a[a-z]{3,6};)
						$mot_cle = preg_replace('#[eéèëê]#iu', '([eéèëê]|(&e[a-z]{3,6};))', $mot_cle); //(&e[a-z]{3,6};)
						$mot_cle = preg_replace('#[iìîî]#iu', '([iìîî]|(&i[a-z]{3,6};))', $mot_cle); //(&i[a-z]{3,6};)
						$mot_cle = preg_replace('#[oõòöô]#iu', '([oõòöô]|(&o[a-z]{3,6};))', $mot_cle); //(&o[a-z]{3,6};)
						$mot_cle = preg_replace('#[uùûü]#iu', '([uùûü]|(&u[a-z]{3,6};))', $mot_cle); //(&u[a-z]{3,6};)
						$mot_cle = preg_replace('#[cç]#iu', '([cç]|(&ccedil;))', $mot_cle); //(&ccedil;)
		
						//REGEX POUR LA REQUETE
						$mot_cle_recherche = preg_replace('#[ãàâä]#iu', 'a', $tab_mot_rech[$i_rech]); //(&a[a-z]{3,6};)
						$mot_cle_recherche = preg_replace('#[éèëê]#iu', 'e', $mot_cle_recherche); //(&e[a-z]{3,6};)
						$mot_cle_recherche = preg_replace('#[ìîî]#iu', 'i', $mot_cle_recherche); //(&i[a-z]{3,6};)
						$mot_cle_recherche = preg_replace('#[õòöô]#iu', 'o', $mot_cle_recherche); //(&o[a-z]{3,6};)
						$mot_cle_recherche = preg_replace('#[ùûü]#iu', 'u', $mot_cle_recherche); //(&u[a-z]{3,6};)
						$mot_cle_recherche = preg_replace('#[ç]#iu', 'c', $mot_cle_recherche); //(&ccedil;)
		
						$les_mots_cles[] = $mot_cle;
						$cond_item .= (($cond_item!="")?" AND ":"")."(".$nomTablePrincipale.".".$nomChampTitre." LIKE '%".$mot_cle_recherche."%'".(($_POST["type"]=="evenement"||$_POST["type"]=="structure")?" OR ".$nomTablePrincipale.".email LIKE '%".$mot_cle_recherche."%')":")");
						$cond_ville .= (($cond_ville!="")?" AND ":"")."(villes.nom_ville_maj LIKE '%".strtoupper($mot_cle_recherche)."%' OR villes.code_postal LIKE '%".$mot_cle_recherche."%')";
						$cond_utilisateur .= (($cond_utilisateur!="")?" AND ":"")."(utilisateur.email LIKE '%".$mot_cle_recherche."%' OR utilisateur.pseudo LIKE '%".$mot_cle_recherche."%')";
					}
				}
				$requete_ville = "SELECT 'ville' AS type, villes.nom_ville_maj AS libelle, villes.id AS no FROM villes WHERE ".$cond_ville." GROUP BY villes.id LIMIT 3";
				$requete_utilisateur = "SELECT 'utilisateur' AS type, IF(utilisateur.pseudo='',utilisateur.email,utilisateur.pseudo) AS libelle, utilisateur.no FROM utilisateur WHERE ".$cond_utilisateur." GROUP BY utilisateur.no LIMIT 3";
				$requete_item = "SELECT '".$_POST["type"]."' AS type, ".$nomTablePrincipale.".".$nomChampTitre." AS libelle, ".$nomTablePrincipale.".no FROM ".$nomTablePrincipale." WHERE ".$cond_item." GROUP BY ".$nomTablePrincipale.".no LIMIT 3";
				$tab_item = execute_requete($requete_item);
				$tab_utilisateur = execute_requete($requete_utilisateur);
				$tab_ville = execute_requete($requete_ville);
				if(count($tab_ville)==1){ //On a trouvé UNE SEULE ville.
					$reponse = array_merge($tab_ville,array_merge($tab_item,$tab_utilisateur));
				}
				else{
					if(count($tab_utilisateur)==1){ //On a trouvé UN SEUL utilisateur.
						$reponse = array_merge($tab_utilisateur,array_merge($tab_item,$tab_ville));
					}
					else{
						$reponse = array_merge($tab_item,array_merge($tab_utilisateur,$tab_ville));
					}
				}
		}
	}
}

echo json_encode(array_slice($reponse,0,3));
?>
