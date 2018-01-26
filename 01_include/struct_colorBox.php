<?php
//1. Initialisation de la session
include "_session_start.php";
//2. Récupération des variables principales et indispensables
include "_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "_init_var.php";
$param_javascript = ((!empty($_POST["ne_pas_fermer"]))?"true":"false").',\''.$_POST["fonction_sortie"].'\'';
echo '<div>';
	if(empty($_POST["sans_image_header"]))
		echo '<img src="http://www.ensembleici.fr/img/bandeau-colorbox.png" />';
	if($_POST["colorbox"]=="ville"){
		echo '<p>Pour naviguer sur le site, nous vous invitons à choisir une commune.</p>';
		echo '<h3>Rechercher une commune</h3>';
		echo '<div>';
			echo '<input type="text" id="recherche_ville" autocomplete="off" class="recherche_ville" title="code postal, ville" name="code postal, ville" />';
			echo '<br/>';
			echo '<div id="recherche_ville_liste"><div></div></div>';
		echo '</div>';
		if(!est_connecte()){
			echo '<div>';
				echo 'ou <input type="button" value="Se connecter" id="colorbox_connexion" class="ico fleche" onclick="fenetre_connexion('.$param_javascript.');" />';
			echo '</div>';
		}
		
		echo '<br /><h3>Qu\'est-ce que le projet « Ensemble ici » ?</h3>';
		echo '<p>';
			echo '« Ensemble ici » est une initiative indépendante et citoyenne qui vise à faciliter les échanges et l\'information de tous styles entre habitants et acteurs locaux. On sent bien aujourd\'hui la nécessite de rompre avec  l\'individualisme ambiant, dans un monde qui se déshumanise. Internet nous offre la possibilité de développer des outils au service de tous : profitons-en! Nous pouvons ainsi mutualiser les informations, partager des services, donner de la visibilité aux initiatives locales, et mieux savoir ce qui anime notre région, tant au niveau culturel que social et citoyen !';
		echo '</p>';
	}
	else if($_POST["colorbox"]=="connexion"){
		if(!est_connecte()){
			echo '<p>Pour vous connecter, saisissez vos identifiants.</p>';
			echo '<h3>Connexion</h3>';
			$contenu = "";
			$fonction_sortie_connexion = urlencode($_POST["fonction_sortie"]);
			include 'struct_form_connexion.php';
			echo $contenu;
			echo '<div>';
				echo '<br/>';
				echo 'ou <input type="button" value="Créer un compte" id="colorbox_nouveau_compte" class="ico nouveau_compte" onclick="fenetre_nouveau_compte('.$param_javascript.');" />';
			echo '</div>';
			if($_POST["ne_pas_fermer"]==1){
				echo '<div>';
					echo 'ou <input type="button" value="Sélectionner une ville" id="colorbox_ville" onclick="fenetre_ville('.$param_javascript.');" />';
				echo '</div>';
			}
		}
		else{
			echo '<div>';
				echo '<p>Vous êtes déjà connecté!</p>';
				echo '<input type="button" value="Sélectionner une ville" id="colorbox_ville" class="ico map" onclick="fenetre_ville('.$param_javascript.');" />';
			echo '</div>';
		}
	}
	else if($_POST["colorbox"]=="nouveau_compte"){
		echo '<h3>Créer un compte</h3>';
		include "struct_form_inscription.php";
		echo $contenu;
		if($_POST["ne_pas_fermer"]==1){
			echo '<div>';
				echo 'ou <input type="button" value="Sélectionner une ville" id="colorbox_ville" onclick="fenetre_ville('.$param_javascript.');" />';
			echo '</div>';
		}
	}
	else if($_POST["colorbox"]=="newsletter"){
		echo '<h3>Inscription à la lettre d\'information</h3>';
		include 'struct_form_newsletter.php';
		echo $contenu;
		if(!est_connecte()){
			echo '<div>';
					echo '<br/>';
					echo 'ou <input type="button" value="Créer un compte" id="colorbox_nouveau_compte" class="ico nouveau_compte" onclick="fenetre_nouveau_compte('.$param_javascript.');" />';
					echo '<p>Vous pourrez ainsi gérer facilement votre abonnement à notre lettre d\'information dans votre espace personnel.</p>';
			echo '</div>';
		}
		else{
			echo '<div>';
					echo '<p>Vous pouvez gérer votre abonnement à notre lettre d\'information depuis votre espace personnel.</p>';
			echo '</div>';
		}
	}
	else if($_POST["colorbox"]=="contact"){
		if(est_connecte()){
			//1. On regarde si l'utilisateur possède déjà un contact
			$requete_utilisateur = "SELECT contact.nom, contact.no, IFNULL(contact_contactType.valeur,'') AS email FROM contact JOIN utilisateur ON utilisateur.no_contact=contact.no LEFT JOIN contact_contactType ON contact_contactType.no_contact=contact.no WHERE utilisateur.no=:no AND (contact_contactType.no_contactType IS NULL OR contact_contactType.no_contactType=2)";
			$tab_utilisateur = execute_requete($requete_utilisateur,array(":no"=>$_SESSION["utilisateur"]["no"]));
			//Si l'utilisateur n'a pas de contact
			if(empty($tab_utilisateur)||empty($tab_utilisateur[0]["no"])){
				//On créait alors le contact pour l'utilisateur en cours (comme ça on pourra lui afficher directement)
					//Création de l'entrée "contact"
				$requete_contact = "INSERT INTO contact(nom,no_utilisateur_creation) VALUES(:pseudo,:no)";
				$no_contact_utilisateur = execute_requete($requete_contact,array(":pseudo"=>$_SESSION["utilisateur"]["pseudo"],":no"=>$_SESSION["utilisateur"]["no"]));
					//Création de l'email
					$requete_email = "INSERT INTO contact_contactType(no_contact,no_contactType,valeur,public) VALUES(:no_contact,2,:valeur,0)";
					execute_requete($requete_email,array(":no_contact"=>$no_contact_utilisateur,":valeur"=>$_SESSION["utilisateur"]["email"]));
					//Liaison entre contact et utilisateur
				//$requete_utilisateur = "SELECT * FROM utilisateur WHERE no=:no";
				$requete_utilisateur = "UPDATE utilisateur SET no_contact=:no_contact WHERE no=:no";
				execute_requete($requete_utilisateur,array(":no"=>$_SESSION["utilisateur"]["no"],":no_contact"=>$no_contact_utilisateur));
				$email = $_SESSION["utilisateur"]["email"];
				$nom = $_SESSION["utilisateur"]["pseudo"];
			}
			else{
				$no_contact_utilisateur = $tab_utilisateur[0]["no"];
				$email = $tab_utilisateur[0]["email"];
				$nom = $tab_utilisateur[0]["nom"];
			}
		}
		else{
			$no_contact_utilisateur = 0;
			$email = "";
			$nom = "";
		}
		if(!empty($_POST["no_contact"])){
			//On récupère les informations sur le contact
			$requete_contact = "SELECT * FROM contact WHERE no=:no";
			$tab_contact = execute_requete($requete_contact,array(":no"=>$_POST["no_contact"]));
			//On récupère la liste des moyens de contacts
			$requete_les_contacts = "SELECT * FROM contact_contactType WHERE no_contact=:no";
			$tab_les_contacts = execute_requete($requete_les_contacts,array(":no"=>$_POST["no_contact"]));
			echo '<h3>'.$tab_contact[0]["nom"].'</h3>';
			$possede_email = false;
			echo '<div class="fiche_contact">';
				for($i=0;$i<count($tab_les_contacts);$i++){
					echo '<div>';
						if($tab_les_contacts[$i]["no_contactType"]==1)
							echo '<a href="tel:'.$tab_les_contacts[$i]["valeur"].'">'.formate_telephone($tab_les_contacts[$i]["valeur"]).'</a>';
						else if($tab_les_contacts[$i]["no_contactType"]==2&&!$possede_email){
							echo '<div class="span_courriel" onclick="afficher_formulaire_courriel(this.parentNode.parentNode);">Envoyer un courriel</div>';
							$possede_email = true;
						}
						else if($tab_les_contacts[$i]["no_contactType"]>2){
							echo '<a target="_blank" href="'.$tab_les_contacts[$i]["valeur"].'">'.$tab_les_contacts[$i]["valeur"].'</a>';
						}
					echo '</div>';
				}
				if($possede_email){
					echo '<form class="formulaire_contact" onsubmit="return envoyer_courriel();" method="post" action="">';
						echo '<input id="input_no_contact" name="input_no_contact" type="hidden" value="'.$_POST["no_contact"].'" />';
						echo '<input type="text" title="Votre nom" value="'.$nom.'" id="input_contact_libelle" name="input_contact_libelle" />';
						echo '<br />';
						echo '<input id="input_email_expediteur" name="input_email_expediteur" type="text" title="votre adresse mail" value="'.$email.'" />';
						echo '<br />';
						echo '<textarea id="textarea_contenu_mail" name="textarea_contenu_mail" title="Contenu de votre message ..."></textarea>';
						echo '<br />';
						include "struct_captcha.php";
						echo $contenu;
						echo '<input type="submit" value="Envoyer" class="ico fleche" />';
					echo '</form>';
				}
			echo '</div>';
		}
		else{
			echo '<h3>Oups</h3>';
			echo '<p>Une erreur s\'est produite, veuillez réessayer</p>';
		}
	}
        else if($_POST["colorbox"]=="contactbis"){
		if(est_connecte()){
			//1. On regarde si l'utilisateur possède déjà un contact
			$requete_utilisateur = "SELECT contact.nom, contact.no, IFNULL(contact_contactType.valeur,'') AS email FROM contact JOIN utilisateur ON utilisateur.no_contact=contact.no LEFT JOIN contact_contactType ON contact_contactType.no_contact=contact.no WHERE utilisateur.no=:no AND (contact_contactType.no_contactType IS NULL OR contact_contactType.no_contactType=2)";
			$tab_utilisateur = execute_requete($requete_utilisateur,array(":no"=>$_SESSION["utilisateur"]["no"]));
			//Si l'utilisateur n'a pas de contact
			if(empty($tab_utilisateur)||empty($tab_utilisateur[0]["no"])){
				//On créait alors le contact pour l'utilisateur en cours (comme ça on pourra lui afficher directement)
					//Création de l'entrée "contact"
				$requete_contact = "INSERT INTO contact(nom,no_utilisateur_creation) VALUES(:pseudo,:no)";
				$no_contact_utilisateur = execute_requete($requete_contact,array(":pseudo"=>$_SESSION["utilisateur"]["pseudo"],":no"=>$_SESSION["utilisateur"]["no"]));
					//Création de l'email
					$requete_email = "INSERT INTO contact_contactType(no_contact,no_contactType,valeur,public) VALUES(:no_contact,2,:valeur,0)";
					execute_requete($requete_email,array(":no_contact"=>$no_contact_utilisateur,":valeur"=>$_SESSION["utilisateur"]["email"]));
					//Liaison entre contact et utilisateur
				//$requete_utilisateur = "SELECT * FROM utilisateur WHERE no=:no";
				$requete_utilisateur = "UPDATE utilisateur SET no_contact=:no_contact WHERE no=:no";
				execute_requete($requete_utilisateur,array(":no"=>$_SESSION["utilisateur"]["no"],":no_contact"=>$no_contact_utilisateur));
				$email = $_SESSION["utilisateur"]["email"];
				$nom = $_SESSION["utilisateur"]["pseudo"];
			}
			else{
				$no_contact_utilisateur = $tab_utilisateur[0]["no"];
				$email = $tab_utilisateur[0]["email"];
				$nom = $tab_utilisateur[0]["nom"];
			}
		}
		else{
			$no_contact_utilisateur = 0;
			$email = "";
			$nom = "";
		}
		if(!empty($_POST["no_contact"])){
			//On récupère les informations sur le contact
			$requete_contact = "SELECT * FROM contact WHERE no=:no";
			$tab_contact = execute_requete($requete_contact,array(":no"=>$_POST["no_contact"]));
			//On récupère la liste des moyens de contacts
			$requete_les_contacts = "SELECT * FROM contact_contactType WHERE no_contact=:no";
			$tab_les_contacts = execute_requete($requete_les_contacts,array(":no"=>$_POST["no_contact"]));
			echo '<h3>'.$tab_contact[0]["nom"].'</h3>';
			$possede_email = false;
			echo '<div class="fiche_contact">';
				for($i=0;$i<count($tab_les_contacts);$i++){
					echo '<div>';
						if($tab_les_contacts[$i]["no_contactType"]==1)
							echo '<a href="tel:'.$tab_les_contacts[$i]["valeur"].'">'.formate_telephone($tab_les_contacts[$i]["valeur"]).'</a>';
						else if($tab_les_contacts[$i]["no_contactType"]==2&&!$possede_email){
							echo '<div class="span_courriel" onclick="afficher_formulaire_courriel(this.parentNode.parentNode);">Envoyer un courriel</div>';
							$possede_email = true;
						}
						else if($tab_les_contacts[$i]["no_contactType"]>2){
							echo '<a target="_blank" href="'.$tab_les_contacts[$i]["valeur"].'">'.$tab_les_contacts[$i]["valeur"].'</a>';
						}
					echo '</div>';
				}
				if($possede_email){
					echo '<form class="formulaire_contact" onsubmit="return envoyer_courriel();" method="post" action="">';
						echo '<input id="input_no_contact" name="input_no_contact" type="hidden" value="'.$_POST["no_contact"].'" />';
						echo '<input type="text" title="Votre nom" value="'.$nom.'" id="input_contact_libelle" name="input_contact_libelle" />';
						echo '<br />';
						echo '<input id="input_email_expediteur" name="input_email_expediteur" type="text" title="votre adresse mail" value="'.$email.'" />';
						echo '<br />';
						echo '<textarea id="textarea_contenu_mail" name="textarea_contenu_mail" title="Contenu de votre message ..."></textarea>';
						echo '<br />';
						include "struct_captcha.php";
						echo $contenu;
						echo '<input type="submit" value="Envoyer" class="ico fleche" />';
					echo '</form>';
				}
			echo '</div>';
		}
		else{
			echo '<h3>Oups</h3>';
			echo '<p>Une erreur s\'est produite, veuillez réessayer</p>';
		}
	}
	else if($_POST["colorbox"]=="mdp_oublie"){
		echo '<h3>Mot de passe oublié</h3>';
		echo '<p>Saisissez votre adresse mail. Un lien vous permettant de réinitiliser votre mot de passe vous sera immédiatement envoyé.</p>';
		echo '<form action="" method="post" onsubmit="return mdp_oublie();">';
			echo '<input type="text" title="Nom d\'utilisateur" id="input_email_mdp_oublie" name="input_email_mdp_oublie" />';
			echo '<br />';
			echo '<input type="submit" value="continuer" class="ico fleche" />';
		echo '</form>';
	}
	else if($_POST["colorbox"]=="pseudo"){
		echo '<h3>Modifier votre nom d\'utilisateur</h3>';
		echo '<p>Le nom d\'utiisateur vous permet d\'ajouter des messages/commentaires sur les pages et forums d\'ensembleici.fr</p>';
		echo '<form action="" method="post" onsubmit="return modifier_pseudo();">';
			echo '<input type="text" title="Nom d\'utilisateur" id="input_pseudo" name="input_pseudo" />';
			echo '<br />';
			echo '<input type="submit" value="enregistrer" class="ico fleche" />';
		echo '</form>';
	}
	else if($_POST["colorbox"]=="creationModificationContact"){
            if (!empty($_POST["no_contact"]) && ($_POST["no_contact"] == -1)) {
                $requete_mescontact_agenda = "SELECT DISTINCT(C.no), C.nom FROM evenement_contact A, evenement E, contact C WHERE E.no = A.no_evenement AND C.no = A.no_contact AND A.traite = 0 AND E.no_utilisateur_creation = ".$_SESSION["utilisateur"]["no"];
                $tab_mescontact_agenda = execute_requete($requete_mescontact_agenda,array());
                $requete_mescontact_annonce = "SELECT DISTINCT(C.no), C.nom FROM petiteannonce_contact A, petiteannonce E, contact C WHERE E.no = A.no_petiteannonce AND C.no = A.no_contact AND A.traite = 0 AND E.no_utilisateur_creation = ".$_SESSION["utilisateur"]["no"];
                $tab_mescontact_annonce = execute_requete($requete_mescontact_annonce,array());
                $requete_mescontact_structure = "SELECT DISTINCT(C.no), C.nom FROM structure_contact A, structure E, contact C WHERE E.no = A.no_structure AND C.no = A.no_contact AND A.traite = 0 AND E.no_utilisateur_creation = ".$_SESSION["utilisateur"]["no"];
                $tab_mescontact_structure = execute_requete($requete_mescontact_structure,array());
                $nb_lignes = sizeof($tab_mescontact_agenda) + sizeof($tab_mescontact_annonce) + sizeof($tab_mescontact_structure);
                $tabcontact = array();
                
                if ($nb_lignes > 0) {
                    echo '<h3>Ajout d\'un contact</h3>';
                    echo '<p>';
                    ?>
                    <div>Vous pouvez choisir un contact dans la liste ci-dessous pour afficher ses informations</div>
                    <?php
                    if (sizeof($tab_mescontact_agenda) > 0) {
                        foreach ($tab_mescontact_agenda as $k => $v) {
                            if (!in_array($v['no'], $tabcontact)) {
                            ?>
                            <div style="text-align: left; margin-left: 20px;">
                                <a data-ref="<?= $v['no'] ?>" style="cursor:pointer;" onclick="fenetre_creationModificationContact(<?= $v['no'] ?>);"><?= $v['nom'] ?></a>
                            </div>
                        <?php 
                            array_push($tabcontact, $v['no']);
                            }
                        }
                    }
                    if (sizeof($tab_mescontact_annonce) > 0) {
                        foreach ($tab_mescontact_annonce as $k => $v) { 
                            if (!in_array($v['no'], $tabcontact)) {
                            ?>
                            <div style="text-align: left; margin-left: 20px;">
                                <a data-ref="<?= $v['no'] ?>" style="cursor:pointer;" onclick="fenetre_creationModificationContact(<?= $v['no'] ?>);"><?= $v['nom'] ?></a>
                            </div>
                        <?php 
                            array_push($tabcontact, $v['no']);
                            }
                        }
                    }
                    if (sizeof($tab_mescontact_structure) > 0) {
                        foreach ($tab_mescontact_structure as $k => $v) { 
                            if (!in_array($v['no'], $tabcontact)) {
                        ?>
                            <div style="text-align: left; margin-left: 20px;">
                                <a data-ref="<?= $v['no'] ?>" style="cursor:pointer;" onclick="fenetre_creationModificationContact(<?= $v['no'] ?>);"><?= $v['nom'] ?></a>
                            </div>
                        <?php 
                            array_push($tabcontact, $v['no']);
                            }
                        }
                    }
                    echo '<br/>';
                    echo "<div>ou saisir un nouveau contact</div>";
                    echo '<form action="" method="post" onsubmit="return creationModificationContact();">';
                        echo '<input type="hidden" value="0" name="no_contact" id="no_contact" />';
                        echo '<input type="text" title="Nom du contact" value="'.$tab_nom_contact[0]["nom"].'" id="input_contact_nom" name="input_contact_nom" style="min-width:400px;width:80px;text-align:center;" />';
                        echo '<table id="liste_type_contact" style="min-width:400px;width:80px;margin:auto;">';
                                for($i=0;$i<count($tab_contact);$i++){
                                        echo '<tr class="'.url_rewrite($tab_contact[$i]["libelle"]).'">';
                                                echo '<td>';
                                                        echo creer_input_contactType($tab_contact[$i]["no_contactType"],'select_contact_'.$i);
                                                echo '</td>';
                                                echo '<td>';
                                                        echo '<input type="text" title="Sélectionnez un type" value="'.$tab_contact[$i]["valeur"].'" style="margin:0em;" name="input_contact_'.$i.'" id="input_contact_'.$i.'" onkeyup="verifier_contact_vide(this.parentNode.parentNode.parentNode)" />';
                                                echo '</td>';
                                                echo '<td style="min-width:75px;">';
                                                        echo '<input type="checkbox" '.(((bool)$tab_contact[$i]["public"])?'checked="checked" ':'').'id="afficher_contact_'.$i.'" name="afficher_contact_'.$i.'" /><label for="afficher_contact_'.$i.'" style="position:relative;top:-3px;">&nbsp;Public</label>';
                                                echo '</td>';
                                        echo '</tr>';
                                }
                                if(!empty($tab_contact)){
                                        echo '<tr class="">';
                                                echo '<td>';
                                                        echo creer_input_contactType(0,'select_contact_'.$i);
                                                echo '</td>';
                                                echo '<td>';
                                                        echo '<input type="text" title="Sélectionnez un type" style="margin:0em;" name="input_contact_'.$i.'" id="input_contact_'.$i.'" onkeyup="verifier_contact_vide(this.parentNode.parentNode.parentNode)" />';
                                                echo '</td>';
                                                echo '<td style="min-width:75px;">';
                                                        echo '<input type="checkbox" id="afficher_contact_'.$i.'" name="afficher_contact_'.$i.'" /><label for="afficher_contact_'.$i.'" style="position:relative;top:-3px;">&nbsp;Public</label>';
                                                echo '</td>';
                                        echo '</tr>';
                                }
                                else{
                                        echo '<tr class="email">';
                                                echo '<td>';
                                                        echo creer_input_contactType(2,'select_contact_0');
                                                echo '</td>';
                                                echo '<td>';
                                                        echo '<input type="text" title="Sélectionnez un type" style="margin:0em;" name="input_contact_0" id="input_contact_0" onkeyup="verifier_contact_vide(this.parentNode.parentNode.parentNode)" />';
                                                echo '</td>';
                                                echo '<td style="min-width:75px;">';
                                                        echo '<input type="checkbox" id="afficher_contact_0" name="afficher_contact_0" /><label for="afficher_contact_0" style="position:relative;top:-3px;">&nbsp;Public</label>';
                                                echo '</td>';
                                        echo '</tr>';
                                        echo '<tr class="telephone">';
                                                echo '<td>';
                                                        echo creer_input_contactType(1,'select_contact_1');
                                                echo '</td>';
                                                echo '<td>';
                                                        echo '<input type="text" title="Sélectionnez un type" style="margin:0em;" name="input_contact_1" id="input_contact_1" onkeyup="verifier_contact_vide(this.parentNode.parentNode.parentNode)" />';
                                                echo '</td>';
                                                echo '<td style="min-width:75px;">';
                                                        echo '<input type="checkbox" id="afficher_contact_1" name="afficher_contact_1" /><label for="afficher_contact_1" style="position:relative;top:-3px;">&nbsp;Public</label>';
                                                echo '</td>';
                                        echo '</tr>';
                                }
                                /*echo '<tr class="email">';
                                        echo '<td>';
                                                echo 'select type contact';
                                        echo '</td>';
                                        echo '<td>';
                                                echo '<input type="text" title="Adresse mail" name="contact_email1" name="contact_email1" />';
                                        echo '</td>';
                                        echo '<td>';
                                                echo '<input type="checkbox" id="afficher_contact_email1" name="afficher_contact_email1" /><label for="afficher_contact_email1">&nbsp;Public</label>';
                                        echo '</td>';*/
                                echo '</tr>';
                        echo '</table>';

                        echo '<input type="submit" value="enregistrer" class="ico fleche" />';
                    echo '</form>';
                    echo '</p>';
                }
                else {
                    $requete_contact = "INSERT INTO contact(nom,no_utilisateur_creation) VALUES(:pseudo,:no)";
                    $no_contact_utilisateur = execute_requete($requete_contact,array(":pseudo"=>$_SESSION["utilisateur"]["pseudo"],":no"=>$_SESSION["utilisateur"]["no"]));
                    
                    //Création de l'email
                    $requete_email = "INSERT INTO contact_contactType(no_contact,no_contactType,valeur,public) VALUES(:no_contact,2,:valeur,0)";
                    execute_requete($requete_email,array(":no_contact"=>$no_contact_utilisateur,":valeur"=>$_SESSION["utilisateur"]["email"]));
                    
                    //Liaison entre contact et utilisateur
                    $requete_utilisateur = "UPDATE utilisateur SET no_contact=:no_contact WHERE no=:no";
                    execute_requete($requete_utilisateur,array(":no"=>$_SESSION["utilisateur"]["no"],":no_contact"=>$no_contact_utilisateur));
                        
                    echo '<h3>Ajout d\'un contact</h3>';
                    echo '<p>';
                    echo '<input type="checkbox" id="est_moi" name="est_moi" onclick="fenetre_creationModificationContact('.$no_contact_utilisateur.')" /><label for="est_moi">Il s\'agit de moi.</label>';
                    echo '</p>';
                    echo '<p>';
                    
                    echo '<form action="" method="post" onsubmit="return creationModificationContact();">';
                        echo '<input type="hidden" value="'.$_POST["no_contact"].'" name="no_contact" id="no_contact" />';
                        echo '<input type="text" title="Nom du contact" value="'.$tab_nom_contact[0]["nom"].'" id="input_contact_nom" name="input_contact_nom" style="min-width:400px;width:80px;text-align:center;" />';
                        echo '<table id="liste_type_contact" style="min-width:400px;width:80px;margin:auto;">';
                                for($i=0;$i<count($tab_contact);$i++){
                                        echo '<tr class="'.url_rewrite($tab_contact[$i]["libelle"]).'">';
                                                echo '<td>';
                                                        echo creer_input_contactType($tab_contact[$i]["no_contactType"],'select_contact_'.$i);
                                                echo '</td>';
                                                echo '<td>';
                                                        echo '<input type="text" title="Sélectionnez un type" value="'.$tab_contact[$i]["valeur"].'" style="margin:0em;" name="input_contact_'.$i.'" id="input_contact_'.$i.'" onkeyup="verifier_contact_vide(this.parentNode.parentNode.parentNode)" />';
                                                echo '</td>';
                                                echo '<td style="min-width:75px;">';
                                                        echo '<input type="checkbox" '.(((bool)$tab_contact[$i]["public"])?'checked="checked" ':'').'id="afficher_contact_'.$i.'" name="afficher_contact_'.$i.'" /><label for="afficher_contact_'.$i.'" style="position:relative;top:-3px;">&nbsp;Public</label>';
                                                echo '</td>';
                                        echo '</tr>';
                                }
                                if(!empty($tab_contact)){
                                        echo '<tr class="">';
                                                echo '<td>';
                                                        echo creer_input_contactType(0,'select_contact_'.$i);
                                                echo '</td>';
                                                echo '<td>';
                                                        echo '<input type="text" title="Sélectionnez un type" style="margin:0em;" name="input_contact_'.$i.'" id="input_contact_'.$i.'" onkeyup="verifier_contact_vide(this.parentNode.parentNode.parentNode)" />';
                                                echo '</td>';
                                                echo '<td style="min-width:75px;">';
                                                        echo '<input type="checkbox" id="afficher_contact_'.$i.'" name="afficher_contact_'.$i.'" /><label for="afficher_contact_'.$i.'" style="position:relative;top:-3px;">&nbsp;Public</label>';
                                                echo '</td>';
                                        echo '</tr>';
                                }
                                else{
                                        echo '<tr class="email">';
                                                echo '<td>';
                                                        echo creer_input_contactType(2,'select_contact_0');
                                                echo '</td>';
                                                echo '<td>';
                                                        echo '<input type="text" title="Sélectionnez un type" style="margin:0em;" name="input_contact_0" id="input_contact_0" onkeyup="verifier_contact_vide(this.parentNode.parentNode.parentNode)" />';
                                                echo '</td>';
                                                echo '<td style="min-width:75px;">';
                                                        echo '<input type="checkbox" id="afficher_contact_0" name="afficher_contact_0" /><label for="afficher_contact_0" style="position:relative;top:-3px;">&nbsp;Public</label>';
                                                echo '</td>';
                                        echo '</tr>';
                                        echo '<tr class="telephone">';
                                                echo '<td>';
                                                        echo creer_input_contactType(1,'select_contact_1');
                                                echo '</td>';
                                                echo '<td>';
                                                        echo '<input type="text" title="Sélectionnez un type" style="margin:0em;" name="input_contact_1" id="input_contact_1" onkeyup="verifier_contact_vide(this.parentNode.parentNode.parentNode)" />';
                                                echo '</td>';
                                                echo '<td style="min-width:75px;">';
                                                        echo '<input type="checkbox" id="afficher_contact_1" name="afficher_contact_1" /><label for="afficher_contact_1" style="position:relative;top:-3px;">&nbsp;Public</label>';
                                                echo '</td>';
                                        echo '</tr>';
                                }
                                /*echo '<tr class="email">';
                                        echo '<td>';
                                                echo 'select type contact';
                                        echo '</td>';
                                        echo '<td>';
                                                echo '<input type="text" title="Adresse mail" name="contact_email1" name="contact_email1" />';
                                        echo '</td>';
                                        echo '<td>';
                                                echo '<input type="checkbox" id="afficher_contact_email1" name="afficher_contact_email1" /><label for="afficher_contact_email1">&nbsp;Public</label>';
                                        echo '</td>';*/
                                echo '</tr>';
                        echo '</table>';

                        echo '<input type="submit" value="enregistrer" class="ico fleche" />';
                    echo '</form>';
                    echo '</p>';
                }
            } 
            else {
		//1. On regarde si l'utilisateur possède déjà un contact
		$requete_utilisateur = "SELECT contact.no AS no_contact FROM contact JOIN utilisateur ON utilisateur.no_contact=contact.no WHERE utilisateur.no=:no";
		$tab_utilisateur = execute_requete($requete_utilisateur,array(":no"=>$_SESSION["utilisateur"]["no"]));
		//Si l'utilisateur n'a pas de contact
		if(empty($tab_utilisateur)||empty($tab_utilisateur[0]["no_contact"])){
			//On créait alors le contact pour l'utilisateur en cours (comme ça on pourra lui afficher directement)
				//Création de l'entrée "contact"
			$requete_contact = "INSERT INTO contact(nom,no_utilisateur_creation) VALUES(:pseudo,:no)";
			$no_contact_utilisateur = execute_requete($requete_contact,array(":pseudo"=>$_SESSION["utilisateur"]["pseudo"],":no"=>$_SESSION["utilisateur"]["no"]));
				//Création de l'email
				$requete_email = "INSERT INTO contact_contactType(no_contact,no_contactType,valeur,public) VALUES(:no_contact,2,:valeur,0)";
				execute_requete($requete_email,array(":no_contact"=>$no_contact_utilisateur,":valeur"=>$_SESSION["utilisateur"]["email"]));
				//Liaison entre contact et utilisateur
			//$requete_utilisateur = "SELECT * FROM utilisateur WHERE no=:no";
			$requete_utilisateur = "UPDATE utilisateur SET no_contact=:no_contact WHERE no=:no";
			execute_requete($requete_utilisateur,array(":no"=>$_SESSION["utilisateur"]["no"],":no_contact"=>$no_contact_utilisateur));
		}
		else
			$no_contact_utilisateur = $tab_utilisateur[0]["no_contact"];
		
		//2. On récupère maintenant les informations sur le contact demandé
		if(!empty($_POST["no_contact"])){
			$requete_contact = "SELECT contact_contactType.*, contactType.libelle FROM contact_contactType JOIN contactType ON contact_contactType.no_contactType=contactType.no WHERE contact_contactType.no_contact=:no";
			$tab_contact = execute_requete($requete_contact,array(":no"=>$_POST["no_contact"]));
			
			$requete_nom_contact = "SELECT * FROM contact JOIN contact_contactType ON contact_contactType.no_contact=contact.no WHERE contact.no=:no";
			$tab_nom_contact = execute_requete($requete_nom_contact,array(":no"=>$_POST["no_contact"]));
		}
		else{
			$tab_nom_contact = array();
			$tab_contact = array();
		}
		
		//3. Si l'utilisateur a les droits admin, ou qu'il s'agit du créateur du contact concerné, ou que le contcat est vide
		if($_SESSION["droit"]["no"]==1||(!empty($tab_nom_contact)&&$tab_nom_contact[0]["no_utilisateur_creation"]==$_SESSION["utilisateur"]["no"])||empty($tab_nom_contact)){
			echo '<h3>Ajout d\'un contact</h3>';
			echo '<p>';
				if(empty($_POST["no_contact"])){
					if(!empty($no_contact_utilisateur)){
						echo '<input type="checkbox" id="est_moi" name="est_moi" onclick="fenetre_creationModificationContact('.$no_contact_utilisateur.')" /><label for="est_moi">Il s\'agit de moi.</label>';
					}
					else{ //Normalement on ne passe jamais dans ce else grace à la requête précédente
						echo '<input type="checkbox" id="est_moi" name="est_moi" /><label for="est_moi">Il s\'agit de moi.</label>';
					}
				}
				else{
					if($no_contact_utilisateur==$_POST["no_contact"]){
						echo '<input type="checkbox" checked="checked" id="est_moi" name="est_moi" onclick="fenetre_creationModificationContact(0)" /><label for="est_moi">Il s\'agit de moi.</label>';
					}
				}
			echo '</p>';
			echo '<form action="" method="post" onsubmit="return creationModificationContact();">';
				echo '<input type="hidden" value="'.$_POST["no_contact"].'" name="no_contact" id="no_contact" />';
				echo '<input type="text" title="Nom du contact" value="'.$tab_nom_contact[0]["nom"].'" id="input_contact_nom" name="input_contact_nom" style="min-width:400px;width:80px;text-align:center;" />';
				echo '<table id="liste_type_contact" style="min-width:400px;width:80px;margin:auto;">';
					for($i=0;$i<count($tab_contact);$i++){
						echo '<tr class="'.url_rewrite($tab_contact[$i]["libelle"]).'">';
							echo '<td>';
								echo creer_input_contactType($tab_contact[$i]["no_contactType"],'select_contact_'.$i);
							echo '</td>';
							echo '<td>';
								echo '<input type="text" title="Sélectionnez un type" value="'.$tab_contact[$i]["valeur"].'" style="margin:0em;" name="input_contact_'.$i.'" id="input_contact_'.$i.'" onkeyup="verifier_contact_vide(this.parentNode.parentNode.parentNode)" />';
							echo '</td>';
							echo '<td style="min-width:75px;">';
								echo '<input type="checkbox" '.(((bool)$tab_contact[$i]["public"])?'checked="checked" ':'').'id="afficher_contact_'.$i.'" name="afficher_contact_'.$i.'" /><label for="afficher_contact_'.$i.'" style="position:relative;top:-3px;">&nbsp;Public</label>';
							echo '</td>';
						echo '</tr>';
					}
					if(!empty($tab_contact)){
						echo '<tr class="">';
							echo '<td>';
								echo creer_input_contactType(0,'select_contact_'.$i);
							echo '</td>';
							echo '<td>';
								echo '<input type="text" title="Sélectionnez un type" style="margin:0em;" name="input_contact_'.$i.'" id="input_contact_'.$i.'" onkeyup="verifier_contact_vide(this.parentNode.parentNode.parentNode)" />';
							echo '</td>';
							echo '<td style="min-width:75px;">';
								echo '<input type="checkbox" id="afficher_contact_'.$i.'" name="afficher_contact_'.$i.'" /><label for="afficher_contact_'.$i.'" style="position:relative;top:-3px;">&nbsp;Public</label>';
							echo '</td>';
						echo '</tr>';
					}
					else{
						echo '<tr class="email">';
							echo '<td>';
								echo creer_input_contactType(2,'select_contact_0');
							echo '</td>';
							echo '<td>';
								echo '<input type="text" title="Sélectionnez un type" style="margin:0em;" name="input_contact_0" id="input_contact_0" onkeyup="verifier_contact_vide(this.parentNode.parentNode.parentNode)" />';
							echo '</td>';
							echo '<td style="min-width:75px;">';
								echo '<input type="checkbox" id="afficher_contact_0" name="afficher_contact_0" /><label for="afficher_contact_0" style="position:relative;top:-3px;">&nbsp;Public</label>';
							echo '</td>';
						echo '</tr>';
						echo '<tr class="telephone">';
							echo '<td>';
								echo creer_input_contactType(1,'select_contact_1');
							echo '</td>';
							echo '<td>';
								echo '<input type="text" title="Sélectionnez un type" style="margin:0em;" name="input_contact_1" id="input_contact_1" onkeyup="verifier_contact_vide(this.parentNode.parentNode.parentNode)" />';
							echo '</td>';
							echo '<td style="min-width:75px;">';
								echo '<input type="checkbox" id="afficher_contact_1" name="afficher_contact_1" /><label for="afficher_contact_1" style="position:relative;top:-3px;">&nbsp;Public</label>';
							echo '</td>';
						echo '</tr>';
					}
					/*echo '<tr class="email">';
						echo '<td>';
							echo 'select type contact';
						echo '</td>';
						echo '<td>';
							echo '<input type="text" title="Adresse mail" name="contact_email1" name="contact_email1" />';
						echo '</td>';
						echo '<td>';
							echo '<input type="checkbox" id="afficher_contact_email1" name="afficher_contact_email1" /><label for="afficher_contact_email1">&nbsp;Public</label>';
						echo '</td>';*/
					echo '</tr>';
				echo '</table>';
			
				echo '<input type="submit" value="enregistrer" class="ico fleche" />';
			echo '</form>';
		}
		else{
			if(!empty($tab_nom_contact)){
				$requete_contact_public = "SELECT contact_contactType.*, contactType.libelle FROM contact_contactType JOIN contactType ON contact_contactType.no_contactType=contactType.no WHERE contact_contactType.no_contact=:no AND contact_contactType.public=1";
				$tab_contact_public = execute_requete($requete_contact_public,array(":no"=>$_POST["no_contact"]));
				echo '<h3>'.$tab_nom_contact[0]["nom"].'</h3>';
				if(count($tab_contact_public)==0)
					echo '<p>Les informations sur ce contact ne sont pas publiques</p>';
				else{
					if(count($tab_contact_public)>0&&count($tab_contact_public)<count($tab_contact))
						echo '<p>Certaines informations non publiques ne sont pas affichées.</p>';
				}
				echo '<table id="liste_type_contact" style="min-width:400px;width:80px;margin:auto;">';
					for($i=0;$i<count($tab_contact_public);$i++){
						for($i=0;$i<count($tab_contact);$i++){
							echo '<tr class="'.url_rewrite($tab_contact[$i]["libelle"]).'">';
								echo '<td>';
									echo $tab_contact[$i]["libelle"];
								echo '</td>';
								echo '<td>';
									echo '<span>'.$tab_contact[$i]["valeur"].'</span>';
								echo '</td>';
							echo '</tr>';
						}
					}
				echo '</table>';
			}
		}
                
            }  
                
                
	}
echo '</div>';
?>
