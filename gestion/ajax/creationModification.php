<?php
header('Content-Type: text/plain; charset=UTF-8');
//require('/home/ensemble/www/00_dev_sam/01_include/_var_ensemble.php');
//include "/home/ensemble/www/00_dev_sam/01_include/_fonctions.php";
require('/home/ensemble/01_include/_var_ensemble.php');
include "/home/ensemble/01_include/_fonctions.php";
//1. On vérifie la connexion
if(est_connecte()){
//if(true){
	//2. On récupère la table en fonction du type
	if(!empty($_POST["type"])&&($_POST["type"]=="evenement"||$_POST["type"]=="editorial"||$_POST["type"]=="forum"||$_POST["type"]=="structure"||$_POST["type"]=="petite-annonce")&&!empty($_POST["no"])){
		//3. On vérifie les droits en fonction de ce que l'utilisateur souhaite faire.
		if(a_droit($_POST["type"],(!empty($_POST["no_utilisateur_creation"])?$_POST["no_utilisateur_creation"]:false),true)){
			$reponse = array(true);
			if($_POST["type"]=="evenement"){
				$nomTablePrincipale = "evenement";
				$dossier_image = "05_evenement";
			}
			else if($_POST["type"]=="editorial"){
				$nomTablePrincipale = "editorial";
				$dossier_image = "12_editorial";
			}
			else if($_POST["type"]=="structure"){
				$nomTablePrincipale = "structure";
				$dossier_image = "04_structure";
			}
			else if($_POST["type"]=="forum"){
				$nomTablePrincipale = "forum";
				$dossier_image = "11_forum";
			}
			else{
				$nomTablePrincipale = "petiteannonce";
				$dossier_image = "09_petiteannonce";
			}
			$regex_heure = "#^[0-9]{2}:[0-9]{2}:[0-9]{2}$#i";
			$regex_date = "#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#i";
			$regex_prix = "#^[0-9]+(\.[0-9]*)?$#i";
			$regex_telephone = "#^\+?[0-9]{10,11}#";
			//2. On vérifie les valeurs
				//2.1 Champs obligatoires
			if(isset($_POST["titre"])&&empty($_POST["titre"])){
				$reponse = array(false,"Vous devez saisir un titre");
			}
			else if(isset($_POST["no_genre"])&&empty($_POST["no_genre"])){
				$reponse = array(false,"Vous devez séléctionner un genre");
			}
			else if(isset($_POST["no_statut"])&&empty($_POST["no_statut"])){
				$reponse = array(false,"Vous devez séléctionner un statut");
			}
			else if(isset($_POST["no_petiteannonce_type"])&&empty($_POST["no_petiteannonce_type"])){
				$reponse = array(false,"Vous devez séléctionner un type d'annonce");
			}
			else if(isset($_POST["no_forum_type"])&&empty($_POST["no_forum_type"])){
				$reponse = array(false,"Vous devez séléctionner un type de forum");
			}
			else if(isset($_POST["no_ville"])&&empty($_POST["no_ville"])){
				$reponse = array(false,"Vous devez séléctionner une ville");
			}
			else if(isset($_POST["date_debut"])&&empty($_POST["date_debut"])){
				$reponse = array(false,"Vous devez saisir une date de début");
			}
			else if($_POST["type"]=="petite-annonce"&&!preg_match($regex_date,datesql($_POST["date_fin"]))){
				$reponse = array(false,"Vous devez saisir une date de fin dans un format valide (jj/mm/aaaa)");
			}
			else if($_POST["type"]=="petite-annonce"&&date("Y-m-d")>datesql($_POST["date_fin"])){
				$reponse = array(false,"La date de fin ne peut pas être antérieure à aujourd'hui");
			}
			else if(isset($_POST["pseudo"])&&empty($_POST["pseudo"])){
				$reponse = array(false,"Vous devez choisir un nom d'utilisateur");
			}
			else if(isset($_POST["tags"])&&empty($_POST["tags"])){
				$reponse = array(false,"Vous devez choisir un tag");
			}
			else if(!empty($_POST["date_debut"])&&!empty($_POST["date_fin"])&&$_POST["date_debut"]>$_POST["date_fin"]){
				$reponse = array(false,"L'événement ne peut pas se terminer avant d'avoir commencé!");
			}
			else{
				//2.2 Vérification du format
				if(!empty($_POST["site"])){
					if(!filter_var($_POST["site"], FILTER_VALIDATE_URL))
						$reponse = array(false,"l'url n'est pas valide");
				}
				if(!empty($_POST["facebook"])){
					if(!filter_var($_POST["facebook"], FILTER_VALIDATE_URL)||!strstr($_POST["facebook"],"facebook"))
						$reponse = array(false,"Il semblerait que l'adresse saisie ne corresponde pas à une page facebook");
				}
				if(!empty($_POST["email_contact"])){
					if(!filter_var($_POST["email_contact"], FILTER_VALIDATE_EMAIL))
						$reponse = array(false,"l'email n'est pas valide");
				}
				if(!empty($_POST["date_debut"])){
					$_POST["date_debut"] = datesql($_POST["date_debut"]);
					if(!preg_match($regex_date,$_POST["date_debut"]))
						$reponse = array(false,"Date de début invalide");
				}
				if(!empty($_POST["date_fin"])){
					$_POST["date_fin"] = datesql($_POST["date_fin"]);
					if(!preg_match($regex_date,$_POST["date_fin"]))
						$reponse = array(false,"Date de fin invalide");
					else if($_POST["no"]==-1&&date("Y-m-d")>$_POST["date_fin"])
						$reponse = array(false,"La date de fin ne peut pas être antérieure à aujourd'hui");
				}
				else{
					if(!empty($_POST["date_debut"])){
						$_POST["date_fin"] = $_POST["date_debut"];
						if($_POST["no"]==-1&&date("Y-m-d")>$_POST["date_fin"])
							$reponse = array(false,"La date ne peut pas être antérieure à aujourd'hui");
					}
				}
				if(!empty($_POST["heure_debut"])){
					$_POST["heure_debut"] = str_replace("h",":",$_POST["heure_debut"]);
					$_POST["heure_debut"] = $_POST["heure_debut"].":00";
					if(!preg_match($regex_heure,$_POST["heure_debut"]))
						$reponse = array(false,"Heure de début invalide");
				}
				if(!empty($_POST["heure_fin"])){
					$_POST["heure_fin"] = str_replace("h",":",$_POST["heure_fin"]);
					$_POST["heure_fin"] = $_POST["heure_fin"].":00";
					if(!preg_match($regex_heure,$_POST["heure_fin"]))
						$reponse = array(false,"Heure de fin invalide");
				}
				if(!empty($_POST["prix"])){
					$_POST["prix"] = str_replace(",",".",$_POST["prix"]);
					$_POST["prix"] = str_replace("e",".",$_POST["prix"]);
					$_POST["prix"] = str_replace("€",".",$_POST["prix"]);
					if(!preg_match($regex_prix,$_POST["prix"]))
						$reponse = array(false,"Prix au format invalide");
				}
				if((!empty($_POST["telephone"])&&!preg_match($regex_telephone,$_POST["telephone"]))||(!empty($_POST["telephone2"])&&!preg_match($regex_telephone,$_POST["telephone2"]))||(!empty($_POST["mobile"])&&!preg_match($regex_telephone,$_POST["mobile"]))){
						$reponse = array(false,"Numéro de téléphone invalide");
				}
				if(!empty($_POST["url_logo"]))
					$_POST["url_image"] = $_POST["url_logo"];
				if(!empty($_POST["url_image"])){
					$fichier = basename($_POST["url_image"]);
					$url_source = "_form_file_tmp/".$fichier;
					if(is_file("./".$url_source)){ //Il faut deplacer l'image dans le dossier final
						$_f = explode(".",$fichier);
						$nom_fichier = $_f[0]."_".url_rewrite(substr($_POST["titre"],0,10)).".".$_f[1];
						$url_cible = "../../02_medias/".$dossier_image."/".$nom_fichier;
						copy("./".$url_source,"./".$url_cible);
						unlink("./".$url_source);
						$url_dev = substr($_SERVER["SCRIPT_NAME"],1); //On enlève le premier "/"
						$url_dev = substr($url_dev,0,strpos($url_dev, "/"));
						if(substr($url_dev,0,2)!="00")
							$_POST["url_image"] = "02_medias/".$dossier_image."/".$nom_fichier;
						else
							$_POST["url_image"] = $url_dev."/02_medias/".$dossier_image."/".$nom_fichier;
					}
				}
				if(!empty($_POST["url_logo"])){
					$_POST["url_logo"] = $_POST["url_image"];
					unset($_POST["url_image"]);
				}
				if(!empty($_POST["pseudo"])){ //Il faut alors renseigner le pseudo de l'utilisateur
					//On vérifie d'abord que le pseudo sélectionné n'existe pas déjà pour quelqu'un
					$requete_existe_pseudo = "SELECT pseudo FROM utilisateur WHERE pseudo=:p";
					if(count_requete($requete_existe_pseudo,array(":p"=>urldecode($_POST["pseudo"])))==0){ //On peut utiliser ce pseudo
						$requete_pseudo = "UPDATE utilisateur SET pseudo=:p WHERE no=:no";
						execute_requete($requete_pseudo,array(":p"=>urldecode($_POST["pseudo"]),":no"=>$_SESSION["utilisateur"]["no"]));
						$_SESSION["utilisateur"]["pseudo"] = $_POST["pseudo"];
					}
					else //Ce pseudo est déjà utilisé
						$reponse = array(false,"Oups, ce nom d'utilisateur est déjà pris...");
				}
				//Signature des articles
				if(isset($_POST["afficher_signature"])&&$_POST["afficher_signature"]==0&&empty($_POST["signature"])){
					$reponse = array(false,"Vous devez utiliser une signature anonyme.");
				}
				else if(isset($_POST["afficher_signature"])&&$_POST["afficher_signature"]==1&&!empty($_POST["signature"])){
					$_POST["signature"] = "";
				}
				//Coordonnées des autres fiches
				if(isset($_POST["contact_mes_coordonnees"])&&$_POST["contact_mes_coordonnees"]==1){
					//On récupère les coordonnées de l'utilisateur
			
				}
		
				if($reponse[0]){
					if($_POST["no"]!=-1){ //UPDATE
						//if($_POST["type"]=="evenement"||$_POST["type"]=="structure"||$_POST["type"]=="editorial"){
							//3. On récupère tout les champs de la table
							$requete_colonne = "SELECT information_schema.COLUMNS.COLUMN_NAME AS nom FROM information_schema.COLUMNS WHERE information_schema.COLUMNS.TABLE_NAME=:t AND information_schema.COLUMNS.COLUMN_NAME<>'no' AND information_schema.COLUMNS.COLUMN_NAME<>'apparition_lettre' AND information_schema.COLUMNS.COLUMN_NAME<>'nb_aime'";
							$tab_colonne = execute_requete($requete_colonne,array(":t"=>$nomTablePrincipale));
								//On créait la chaine des champs
							$les_champs = "";
							for($i=0;$i<count($tab_colonne);$i++){
								$les_champs .= (($les_champs!="")?",":"").$tab_colonne[$i]["nom"];
							}
							//3. VERSION -> On enregistre la version
							$les_tags = get_ficheTags($_POST["type"],$_POST["no"]);
							$liste_tags = "";
							for($i=0;$i<count($les_tags);$i++){
								$liste_tags .= (($liste_tags!="")?',':'').$les_tags[$i]["no"];
							}
							$requete_copie = "INSERT INTO ".$nomTablePrincipale."_temp(".$les_champs.",tags) SELECT ".$les_champs.",'".$liste_tags."' AS tags FROM ".$nomTablePrincipale." where ".$nomTablePrincipale.".no=:no";
							$no_temp = execute_requete($requete_copie,array(":no"=>$_POST["no"]));
							$requete_modif = "INSERT INTO ".$nomTablePrincipale."_modification(no_".$nomTablePrincipale.",no_utilisateur,no_".$nomTablePrincipale."_temp) VALUES(:no,:nouser,:notemp)";
							$no_modif = execute_requete($requete_modif,array(":no"=>$_POST["no"],":nouser"=>$_SESSION["utilisateur"]["no"],":notemp"=>$no_temp));
					
							//TODO ajouter liste des tags actuels
					
						//}
						//5. On créait la requête update
						$chaine_update = "";
						$params = array(":no"=>$_POST["no"]);
						foreach($_POST as $cle=>$valeur){
							if($cle!="no"&&$cle!="type"&&$cle!="pseudo"&&$cle!="tags"&&$cle!="no_utilisateur_creation"&&!strstr($cle,"contact")){
								//if(!empty($valeur)){
									$chaine_update .= (($chaine_update!="")?", ":"").$cle."=:".$cle;
									$params[":".$cle] = $valeur;
								//}
							}
						}
						$requete_update = "UPDATE ".$nomTablePrincipale." SET ".$chaine_update." WHERE no=:no";
				
				
						//On insère maintenant les tags
						//1. On supprime les tags qui ne font pas parti de la liste
						$requete_del_tag = "DELETE FROM ".$nomTablePrincipale."_tag WHERE ".$nomTablePrincipale."_tag.no_".$nomTablePrincipale."=:no";
						if(!empty($_POST["tags"])){
							$requete_del_tag .= " AND ".$nomTablePrincipale."_tag.no_tag NOT IN (".$_POST["tags"].")";
							execute_requete($requete_del_tag,array(":no"=>$_POST["no"]));
							$les_tags = explode(",",$_POST["tags"]);
							$requete_test = "SELECT * FROM ".$nomTablePrincipale."_tag WHERE ".$nomTablePrincipale."_tag.no_".$nomTablePrincipale."=:no AND ".$nomTablePrincipale."_tag.no_tag=:not";
							$requete_insert = "INSERT INTO ".$nomTablePrincipale."_tag(no_".$nomTablePrincipale.",no_tag) VALUES(:no,:not)";
							for($t=0;$t<count($les_tags);$t++){
								if(count_requete($requete_test,array(":no"=>$_POST["no"],":not"=>$les_tags[$t]))==0)
									execute_requete($requete_insert,array(":no"=>$_POST["no"],":not"=>$les_tags[$t]));
							}
						}
						else{
							execute_requete($requete_del_tag,array(":no"=>$_POST["no"]));
						}
				
						//6. On execute la requête update
						//$res = $connexion->prepare($requete_update);
						//$res->execute($params) or die("moderfok");
						if(execute_requete($requete_update,$params)>0||$_POST["tags"]!=$liste_tags){ //On test si l'update affecte une ligne TODO Ajouter ||$_POST["tags"]!=les_tags
							//Oui : on met à jour la validation
							$test_validation = "SELECT validation FROM ".$nomTablePrincipale." WHERE validation>0 AND no=:no";
							if(count_requete($requete_validation,array(":no"=>$_POST["no"]))>0){
								$requete_validation = "UPDATE ".$nomTablePrincipale." SET validation=2 WHERE no=:no";
								execute_requete($requete_validation,array(":no"=>$_POST["no"]));
							}
							$reponse[1] = "Les données sont correctement enregistrées";
						}
						else{
							//On supprime alors les sauvegardes inutiles
							$requete_delCopie = "DELETE FROM ".$nomTablePrincipale."_temp WHERE no=:no";
							$requete_delModif = "DELETE FROM ".$nomTablePrincipale."_temp WHERE no=:no";
							execute_requete($requete_delCopie,array(":no"=>$no_temp));
							execute_requete($requete_delModif,array(":no"=>$no_modif));
							$reponse[1] = "Vous n'avez apporté aucune modification";
						}
					
					}
					else{ //INSERT
						//On test l'existance du champ date création qu'il faut remplir par défaut s'il existe
						$req_existe_dateCreation = "SELECT column_name FROM information_schema.columns WHERE table_name=:t AND column_name='date_creation'";
						if(count_requete($req_existe_dateCreation,array(":t"=>$nomTablePrincipale))>0)
							$_POST["date_creation"] = date("Y-m-d H:i:s");
						//no_utilisateur_creation
						$_POST["no_utilisateur_creation"] = $_SESSION["utilisateur"]["no"];
						//On regarde maintenant le niveau de validation en fonction des droits, du type, etc.
						$_POST["validation"] = (($_SESSION["droit"]["no"]==1)?1:0);
						if($type!="editorial")
							$_POST["etat"] = 1;
						else //Edito non actifs au départ
							$_POST["etat"] = 0;
						$chaine_champs = "";
						$chaine_valeurs = "";
						foreach($_POST as $cle=>$valeur){
							if($cle!="no"&&$cle!="type"&&$cle!="pseudo"&&$cle!="tags"&&!strstr($cle,"contact")){
								if(!empty($valeur)){
									$chaine_champs .= (($chaine_champs!="")?", ":"").$cle;
									$chaine_valeurs .= (($chaine_valeurs!="")?", ":"").":".$cle;
									$params[":".$cle] = $valeur;
								}
							}
						}
						$requete_insert = "INSERT INTO ".$nomTablePrincipale."(".$chaine_champs.") VALUES(".$chaine_valeurs.")";
						/*$res = $connexion->prepare($requete_insert);
						$res->execute($params) or die("moderfok");*/
						$no_item = execute_requete($requete_insert,$params);
				
						//On insère maintenant les tags
						//1. On supprime les tags qui ne font pas parti de la liste
						$requete_del_tag = "DELETE FROM ".$nomTablePrincipale."_tag WHERE ".$nomTablePrincipale."_tag.no_".$nomTablePrincipale."=:no";
						if(!empty($_POST["tags"])){
							$requete_del_tag .= " AND ".$nomTablePrincipale."_tag.no_tag NOT IN (".$_POST["tags"].")";
							execute_requete($requete_del_tag,array(":no"=>$no_item));
							$les_tags = explode(",",$_POST["tags"]);
							$requete_test = "SELECT * FROM ".$nomTablePrincipale."_tag WHERE ".$nomTablePrincipale."_tag.no_".$nomTablePrincipale."=:no AND ".$nomTablePrincipale."_tag.no_tag=:not";
							$requete_insert = "INSERT INTO ".$nomTablePrincipale."_tag(no_".$nomTablePrincipale.",no_tag) VALUES(:no,:not)";
							for($t=0;$t<count($les_tags);$t++){
								if(count_requete($requete_test,array(":no"=>$no_item,":not"=>$les_tags[$t]))==0)
									execute_requete($requete_insert,array(":no"=>$no_item,":not"=>$les_tags[$t]));
							}
						}
						else{
							execute_requete($requete_del_tag,array(":no"=>$no_item));
						}
				
				
						$reponse[1] = "Enregistrement effectué avec succés";
					}
				}
			}
		}
		else
			$reponse = array(false,'[DROIT]');
	}
	else
		$reponse = array(false,"Une erreur est survenue");
}
else{
	$reponse = array(false,'[CONNEXION]');
}
echo json_encode($reponse);
?>
