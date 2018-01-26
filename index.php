<?php
/***
Ce fichier permet d'afficher toutes les pages.
On utilise la variable $lignes générée par le fichier _init_page.php
Sur les navigateurs compatibles ajax, cette page n'est appelée uniquement lors de l'arrivée sur ensemble-ici
Les autres chargements sont fait en ajax, et integrés par une boucle for identique à celle ci-dessous mais en javascript
**/
//1. Initialisation de la session
include "01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "01_include/_init_var.php";
//5. On charge maintenant le contenu de la page.
include "01_include/_init_page.php";

//6. On affiche maintenant la page
	//HEADER
include "01_include/struct_header.php";
	//PAGE
for($i=0;$i<count($lignes);$i++){
	if($i>=1)
		$row_invisible = " invisible"; //Permet un affichage en douceur
	else
		$row_invisible = "";
	if(empty($lignes[$i]["class"]))
		echo '<div class="row'.$row_invisible.'">';
	else
		echo '<div class="row'.$row_invisible.' '.$lignes[$i]["class"].'">';
	$les_lignes = $lignes[$i]["lignes"];
	for($j=0;$j<count($les_lignes);$j++){
		if($les_lignes[$j]["titre"]!=null&&$les_lignes[$j]["titre"]){
			$titre_bloc = '<div class="titre_bloc"></div>';
		}
		else{
			$titre_bloc = '';
		}
		if($les_lignes[$j]["id"]!="home_editorial_ei"&&$les_lignes[$j]["id"]!="colonne_droite"){
			$contenu = $les_lignes[$j]["contenu"].$titre_bloc;
			if($les_lignes[$j]["id"]!="home_editorial"&&$les_lignes[$j]["id"]!="colonne_gauche")
				$bloc_invisible = "";
			else
				$bloc_invisible = " invisible";
		}
		else{
			$contenu = '<div class="invisible">'.$les_lignes[$j]["contenu"].$titre_bloc."</div>";
			$bloc_invisible = "";
		}
	
		echo '<div class="bloc'.$bloc_invisible.' '.$les_lignes[$j]["class"].'" id="'.$les_lignes[$j]["id"].'"><div><div>'.$contenu.'</div></div>';
		echo '</div>';
	}
	echo '</div>';
}
//FOOTER
include "01_include/struct_footer.php";
// FENETRES MODALES
include "01_include/struct_modal.php";
?>
