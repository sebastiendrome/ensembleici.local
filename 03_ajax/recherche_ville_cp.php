<?php
include "../01_include/_var_ensemble.php";
//On récupère la ville et le code postal (mot clé)
$keyword = preg_replace("/[^a-z0-9À-ÿ]/iu",' ', strtolower(urldecode($_POST['m'])));

//On la passe dans les regex
$les_mots_cles = array(); //Ce tableau contient les regex pour souligner la recherche.
$tab_mot_rech = explode(" ",$keyword);
$cond = "";
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
		
		$cond .= (($cond!="")?" AND ":"")."(villes.nom_ville_maj LIKE '%".strtoupper($mot_cle_recherche)."%' OR villes.code_postal LIKE '%".strtoupper($mot_cle_recherche)."%')";
	}
}
//On recherche dans la table ville une entrée dont le nom ou le code postal corredpondraient aux mots clés.
$requete = "SELECT villes.nom_ville_maj, villes.id, villes.code_postal FROM villes WHERE ".$cond." LIMIT 5";
$res = $connexion->prepare($requete);
$res->execute();
$tab = $res->fetchAll();
$return = array();
for($i=0;$i<count($tab);$i++){
	$return[] = array("no"=>$tab[$i]["id"],"cp"=>$tab[$i]["code_postal"],"libelle"=>$tab[$i]["nom_ville_maj"],"url"=>url_rewrite($tab[$i]["nom_ville_maj"]));
}
echo json_encode($return);
?>
