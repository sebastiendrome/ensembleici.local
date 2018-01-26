<?php
ini_set('default_charset', 'utf-8');

$RAYON_TERRE = 6371;
function preparer_requete($requete){
	global $connexion;
	return $connexion->prepare($requete);
}
function execute_requete($requete,$param=null){
	global $connexion;
	$res = $connexion->prepare($requete);
	$res->execute($param) or die('<div style="background-color:green">'.$requete.'</div>');
	if(substr($requete,0,6)=="SELECT"||substr($requete,0,7)=="(SELECT")
		return $res->fetchAll(PDO::FETCH_ASSOC);
	else if(substr($requete,0,6)=="INSERT")
		return $connexion->lastInsertId();
	else if(substr($requete,0,6)=="UPDATE")
		return $res->rowCount();
	else if(substr($requete,0,6)=="DELETE")
		return $res->rowCount();
}
function count_requete($requete,$param=null){
	global $connexion;
	if(substr($requete,0,6)=="SELECT"||substr($requete,0,7)=="(SELECT"){
		$res = $connexion->prepare($requete);
		$res->execute($param) or die('<div style="background-color:yellow">'.$requete.'</div>');
		return $res->rowCount();
	}
}

function extraire_liste($type,$limite=0,$page=1,$conditions=array()){
	/***
	Récupération des variables globales
	**/
	global $ID_VILLE; //On récupère la ville
	global $RAYON_TERRE; //on récupère le rayon de la terre
	global $root_site_prod;
	/***
	Sécurité pour le nom des types (comme ça plusieurs sont acceptés)
	**/
	if($type=="evenement")
		$type = "agenda";
	else if($type=="structure")
		$type = "repertoire";
	//Sa latitude, sa longitude
	/*
	if(empty($conditions["admin"])||!$conditions["admin"]){
		$infos_ville = execute_requete("SELECT radians(latitude) AS latitude, radians(longitude) AS longitude FROM villes WHERE id=:id",array(":id"=>$ID_VILLE));
		$LATITUDE = $infos_ville[0]["latitude"];
		$LONGITUDE = $infos_ville[0]["longitude"];
		if(empty($conditions["distance"]))
			$conditions["distance"] = $distance;
	}
	else{
		if(empty($conditions["ville"]))
			$conditions["distance"] = -1;
		else{
			$ID_VILLE = $conditions["ville"];
			$conditions["distance"] = 0;
		}
		$conditions["etat"] = -1;
		$conditions["validation"] = -1;
		if(!empty($conditions["expire"])&&$conditions["expire"])
			$conditions["du"] = -1; //En passant "du" à -1, on retire la condition sur la date (-> les événements expirés seront aussi affichés)
	}*/
	/***
	Sécurité pour la limite et la page
	**/
	if($limite>100) //La limite maximum est 100 (sécurité)
		$limite = 100;
	if($page<1)
		$page = 1;
	/***
	En fonction du type, on initialise les données et la requête
	**/
	if($type=="editorial")
		include "_var_editorial.php";
	else if($type=="agenda")
		include "_var_agenda.php";
	else if($type=="petite-annonce")
		include "_var_petiteannonce.php";
	else if($type=="repertoire")
		include "_var_repertoire.php";
	else if($type=="forum")
		include "_var_forum.php";
	//On préoare la requête des coups de coeur
	$requete_nb_coupdecoeur = "SELECT COUNT(".$tablePrincipale."_coupdecoeur.no) AS nb FROM ".$tablePrincipale."_coupdecoeur WHERE ".$tablePrincipale."_coupdecoeur.no_".$tablePrincipale."=:no";
	$res_nb_coupdecoeur = preparer_requete($requete_nb_coupdecoeur);
	/*****
	Paramètres spéciale administration
	**/
	if(!empty($conditions["admin"])&&$conditions["admin"]){
		if(empty($conditions["ville"]))
			$conditions["distance"] = -1;
		else{
			$ID_VILLE = $conditions["ville"];
			$conditions["distance"] = 0;
		}
		if(!isset($conditions["etat"]))
			$conditions["etat"] = -1;
		if(!isset($conditions["validation"]))
			$conditions["validation"] = -1;
		if(!empty($conditions["expire"])&&$conditions["expire"])
			$conditions["du"] = -1; //En passant "du" à -1, on retire la condition sur la date (-> les événements expirés seront aussi affichés)
	}
	else if(!empty($conditions["espace_personnel"])&&$conditions["espace_personnel"]>0){
		$conditions["tri"] = "date_creation";
		$conditions["ordre"] = "DESC";
		
		//if(empty($conditions["ville"]))
			$conditions["distance"] = -1;
			$conditions["du"] = -1; //En passant "du" à -1, on retire la condition sur la date (-> les événements expirés seront aussi affichés)
		/*else{
			$ID_VILLE = $conditions["ville"];
			$conditions["distance"] = 0;
		}*/
		if(!isset($conditions["etat"]))
			$conditions["etat"] = -1;
		if(!isset($conditions["validation"]))
			$conditions["validation"] = -1;
		$conditions["utilisateur"] = $conditions["espace_personnel"];
	}
	else if(!empty($conditions["favoris"])&&$conditions["favoris"]>0){
		$champs_tri["date_favori"] = $tablePrincipale."_favoris.date_creation";
		
		$conditions["tri"] = "date_favori";
		$conditions["ordre"] = "DESC";
		
		$conditions["distance"] = -1;
		$conditions["etat"] = 1;
		$conditions["validation"] = -1;
		
		
	}
	else if(!empty($conditions["rss"])&&$conditions["rss"]>0){ //pour le moment que pour les événements
		$conditions["tri"] = "date";
		
		$conditions["distance"] = -1;
		$conditions["etat"] = 1;
		$conditions["validation"] = -1;
	}
	/*****
	Paramètres partie publique
	**/
	else{
		if(empty($conditions["tri"])||empty($champs_tri[$conditions["tri"]]))
			$conditions["tri"] = $tri_defaut;
		if($conditions["tri"]=="reputation"||$conditions["tri"]=="date_creation"||($type=="editorial"&&$conditions["tri"]=="date")||($type=="forum"&&$conditions["tri"]=="date"))
			$conditions["ordre"] = "DESC";
		$conditions["etat"] = 1;
		if($type!="editorial")
			$conditions["validation"] = -1;
		else
			$conditions["validation"] = 1;
	}
	/**
	Information sur la ville sélectionnée
	**/
	if(!empty($ID_VILLE)){
		$infos_ville = execute_requete("SELECT radians(latitude) AS latitude, radians(longitude) AS longitude FROM villes WHERE id=:id",array(":id"=>$ID_VILLE));
		$LATITUDE = $infos_ville[0]["latitude"];
		$LONGITUDE = $infos_ville[0]["longitude"];
	}
	/***
	On ajoute le champ distance (commun à toutes les tables)
	**/
        
	if(!empty($LATITUDE)&&!empty($LONGITUDE))
		$champs_liste[] = array("champ"=>"(acos(sin(".$LATITUDE.")*sin(radians(villes.latitude)) + cos(".$LATITUDE.")*cos(radians(villes.latitude))*cos(radians(villes.longitude)-".$LONGITUDE."))*".$RAYON_TERRE.")","alias"=>"distance");
		/*if(empty($conditions["distance"]))
			$conditions["distance"] = $distance;*/
	
		/*
	if(empty($conditions["admin"])||!$conditions["admin"]){
	
	}
	else{
	
	}*/
		
	/***
	Ici on prépare les conditionnelles communes
	**/
	
		/*$conditions["tri"] = $tri_defaut;
		echo $tri_defaut;*/
		
	/*if(empty($conditions["ordre"])&&!empty($ordre_default))
		$conditions["ordre"] = $ordre_defaut;*/
		
	if(empty($conditions["du"])&&!empty($du))
		$conditions["du"] = $du;
	/***
		On construit maintenant la requête avec les champs et les jointures
		**/
		//SELECT
		$SELECT = "SELECT ";
			$les_champs = "";
			for($r=0;$r<count($champs_liste);$r++){
				$les_champs .= (($les_champs!="")?", ":"").$champs_liste[$r]["champ"]." AS ".$champs_liste[$r]["alias"];
			}
		$SELECT .= $les_champs;
		// FROM & JOIN
		$FROM_JOIN = " FROM ".$tablePrincipale;
		$FROM_JOIN .= " LEFT JOIN utilisateur ON utilisateur.no=".$tablePrincipale.".no_utilisateur_creation";			//UTILISATEUR
		if($type=="agenda")
			$FROM_JOIN .= " LEFT JOIN genre ON genre.no=".$tablePrincipale.".no_genre";	//GENRE
		else if($type=="repertoire"||$type=="structure")
			$FROM_JOIN .= " LEFT JOIN statut ON structure.no_statut=statut.no";											//STATUT
		else if($type=="petite-annonce"){
			$FROM_JOIN .= " LEFT JOIN petiteannonce_type ON petiteannonce_type.no=".$tablePrincipale.".no_petiteannonce_type";							//Type d'annonce
		}
		else if($type=="forum"){
			$FROM_JOIN .= " LEFT JOIN forum_type ON forum.no_forum_type=forum_type.no";
			//$FROM_JOIN .= " LEFT JOIN messageForum ON messageForum.no_forum=forum.no";									//MESSAGE FORUM
			$FROM_JOIN .= " LEFT JOIN message ON message.no_forum=forum.no";
		}
		$FROM_JOIN .= " LEFT JOIN villes ON villes.id=".$tablePrincipale.".no_ville";//VILLE
                if(!empty($conditions["territoire"])) {
			$FROM_JOIN .= " LEFT JOIN communautecommune_ville T ON villes.id=T.no_ville LEFT JOIN communautecommune C ON T.no_communautecommune = C.no";//VILLE
		}
                if(!empty($conditions["ht"])) {
			$FROM_JOIN .= " LEFT JOIN communautecommune_ville T ON villes.id=T.no_ville LEFT JOIN communautecommune C ON T.no_communautecommune = C.no";//VILLE
		}
		if(!empty($conditions["tags"]))
			$FROM_JOIN .= " LEFT JOIN ".$tablePrincipale."_tag ON ".$tablePrincipale."_tag.no_".$tablePrincipale."=".$tablePrincipale.".no";								
			//affichage des favoris
		if(!empty($conditions["favoris"])&&$conditions["favoris"]>0)
			$FROM_JOIN .= " LEFT JOIN ".$tablePrincipale."_favoris ON ".$tablePrincipale."_favoris.no_".$tablePrincipale."=".$tablePrincipale.".no";	
		// ORDER BY
		$ORDER_BY = " ORDER BY ";
		if($type=="agenda"&&(empty($conditions["admin"])||!$conditions["admin"]))
			$ORDER_BY .= "evenement_long, ";
		$ORDER_BY .= $champs_tri[$conditions["tri"]];
		/*if((!empty($conditions["admin"])&&$conditions["admin"])&&!empty($conditions["ordre"])&&$conditions["ordre"]=="DESC")
			$ORDER_BY = str_replace(","," DESC,",$ORDER_BY." DESC");*/
		if(!empty($conditions["ordre"])&&$conditions["ordre"]=="DESC")
			$ORDER_BY = str_replace(","," DESC,",$ORDER_BY." DESC");
		
	// WHERE
	if(!empty($conditions["no"])){ //FICHE
		$les_conditions .= (($les_conditions!="")?" AND ":"").$tablePrincipale.".no=:no";
		$param[":no"] = $conditions["no"];
	}
	else{ //LISTE
		if(!empty($conditions["etat"])&&$conditions["etat"]>-1){
			$les_conditions .= (($les_conditions!="")?" AND ":"").$tablePrincipale.".etat=:etat";
			$param[":etat"] = $conditions["etat"];
		}
		if(!empty($conditions["validation"])&&$conditions["validation"]>-1){
			$les_conditions .= (($les_conditions!="")?" AND ":"").$tablePrincipale.".validation=:validation";
			$param[":validation"] = $conditions["validation"];
		}
                if(!empty($conditions["territoire"])) {
			$les_conditions .= (($les_conditions!="")?" AND ":"")."C.territoires_id=:territoire";
			$param[":territoire"] = $conditions["territoire"];
		}
                if(!empty($conditions["ht"])) {
			$les_conditions .= (($les_conditions!="")?" AND ":"")."C.territoires_id IS NULL";
		}
		/*if(!empty($conditions["illustre_seulement"])&&$conditions["illustre_seulement"]){
			$les_conditions .= (($les_conditions!="")?" AND ":"")."evenement.url_image<>''";
		}*/
		if(!empty($conditions["du"])&&$conditions["du"]!=-1){ //Date de limite inférieure
			$les_conditions .= (($les_conditions!="")?" AND ":"").$tablePrincipale.".".$champ_dateDu.">=:du";
			$param[":du"] = $conditions["du"];
		}
		if(!empty($conditions["au"])){ //Date de limite supérieure
			$les_conditions .= (($les_conditions!="")?" AND ":"").$tablePrincipale.".date_debut<=:au";
			$param[":au"] = $conditions["au"];
		}
		if(!empty($conditions["utilisateur"])){ //Date de limite supérieure
			$les_conditions .= (($les_conditions!="")?" AND ":"").$tablePrincipale.".no_utilisateur_creation=:utilisateur";
			$param[":utilisateur"] = $conditions["utilisateur"];
		}
		if(!empty($conditions["courte_duree_seulement"])&&$conditions["courte_duree_seulement"]){ //Evenement de plus d'un mois à bannir
			$les_conditions .= (($les_conditions!="")?" AND ":"").$tablePrincipale.".date_fin<=DATE_ADD(".$tablePrincipale.".date_debut, INTERVAL 31 DAY)";
		}
		if(!empty($conditions["illustree_seulement"])&&$conditions["illustree_seulement"]){
			if($type!="structure")
				$champ_image = "url_image";
			else
				$champ_image = "url_logo";
			$les_conditions .= (($les_conditions!="")?" AND ":"").$tablePrincipale.".".$champ_image." IS NOT NULL AND ".$tablePrincipale.".".$champ_image."<>''";
		}
		if(!empty($conditions["tags"])){
			$les_conditions .= (($les_conditions!="")?" AND ":"").$tablePrincipale."_tag.no_tag IN (".$conditions["tags"].")";
		}
		if(!empty($conditions["favoris"])&&$conditions["favoris"]>0){
			$les_conditions .= (($les_conditions!="")?" AND ":"").$tablePrincipale."_favoris.no_utilisateur=:no_utilisateur_favoris";
			$param[":no_utilisateur_favoris"] = $conditions["favoris"];
		}
		if(!empty($conditions["recherche"])){
                    
			$keyword = preg_replace("/[^a-z0-9À-ÿ.@]/iu",' ', strtolower(urldecode($conditions["recherche"])));
                        $keyword2 = preg_replace("/[^a-z0-9À-ÿ.@][']/iu",' ', strtolower(urldecode($conditions["recherche"])));
				//Sinon on recherche dans pseudo, email de utilisateur et dans nom_ville_maj et dans titre. (1 utilisateur, 1 ville, 1 item)
				//On la passe dans les regex
				$les_mots_cles = array(); //Ce tableau contient les regex pour souligner la recherche.
				$tab_mot_rech = explode(" ",$keyword);
                                $tab_mot_rech2 = explode(" ",$keyword2);
				for($i_rech=0;$i_rech<count($tab_mot_rech);$i_rech++){
					if(strlen($tab_mot_rech[$i_rech])>0){
						//REGEX POUR LE SURLIGNE RECHERCHE
						$mot_cle = preg_replace('#[aãàâä]#iu', '([aãàâä]|(&a[a-z]{3,6};))', $tab_mot_rech2[$i_rech]); //(&a[a-z]{3,6};)
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
						$les_conditions .= (($les_conditions!="")?" AND ":"")."(".$tablePrincipale.".".$champ_titre." LIKE '%".$mot_cle_recherche."%')";
					}
				}
		}
		
		// REQUETE COUNT VILLE
		$WHERE_COUNT_VILLE = (($les_conditions!="")?$les_conditions." AND ":"").$tablePrincipale.".no_ville=:id_ville";
		if($type=="forum"||!empty($conditions["tags"]))
			$GROUP_BY_VILLE = " GROUP BY ".$tablePrincipale.".no";
		//if($type=="forum"||!empty($conditions["tags"]))
		//	$WHERE_COUNT_VILLE .= " GROUP BY ".$tablePrincipale.".no";
		$param_count_ville = $param;
		$param_count_ville[":id_ville"] = $ID_VILLE;
		
		// REQUETE COUNT TOTAL
		$WHERE_COUNT_TOTAL = $les_conditions;
		if($type=="forum"||!empty($conditions["tags"]))
			$GROUP_BY_TOTAL = " GROUP BY ".$tablePrincipale.".no";
		//if($type=="forum"||!empty($conditions["tags"]))
		//	$WHERE_COUNT_TOTAL .= " GROUP BY ".$tablePrincipale.".no";
		$param_count_total = $param;
		
		
		if($conditions["distance"]!=-1){ //-1 = tous
			if($conditions["distance"]>0){ //Rayon en kilomètre
				// REQUETE COUNT PROCHE
				$WHERE_COUNT_PROCHE = (($les_conditions!="")?$les_conditions." AND ":"")."(acos(sin(".$LATITUDE.")*sin(radians(latitude)) + cos(".$LATITUDE.")*cos(radians(latitude))*cos(radians(longitude)-".$LONGITUDE."))*".$RAYON_TERRE.") <= :distance";
//				if($type=="forum"||!empty($conditions["tags"]))
//					$GROUP_BY_PROCHE = " GROUP BY ".$tablePrincipale.".no";
                                if($type=="forum"||!empty($conditions["tags"]))
					$GROUP_BY_PROCHE = "";
				$param_count_proche = $param;
				$param_count_proche[":distance"] = $conditions["distance"];
				
				$les_conditions .= (($les_conditions!="")?" AND ":"")."(acos(sin(".$LATITUDE.")*sin(radians(latitude)) + cos(".$LATITUDE.")*cos(radians(latitude))*cos(radians(longitude)-".$LONGITUDE."))*".$RAYON_TERRE.") <= :distance";
				$param[":distance"] = $conditions["distance"];
			}
			else{ //Ville seulement
				$les_conditions .= (($les_conditions!="")?" AND ":"").$tablePrincipale.".no_ville=:id_ville";
				$param[":id_ville"] = $ID_VILLE;
				//if($conditions["elargir"])
			}
		}
		else{
			/*if($type=="forum"){
				$les_conditions .= (($les_conditions!="")?" AND ":"")."forum_type.no>1";
			}*/
			null;
		}
	}
	
	if($type=="forum"){
		if(!empty($ID_VILLE)){
			$les_conditions .= (($les_conditions!="")?" AND ":"")."(forum_type.no>1 OR (forum_type.no=1 AND forum.no_ville=:id_ville))";
			if(empty($param[":id_ville"]))
				$param[":id_ville"] = $ID_VILLE;
		}
		else
			$les_conditions .= (($les_conditions!="")?" AND ":"")."forum_type.no>1";
		
		$ORDER_BY = str_replace("ORDER BY ",'ORDER BY est_citoyen DESC, ',$ORDER_BY);
	}
	if($type=="forum"||!empty($conditions["tags"]))
		$GROUP_BY = " GROUP BY ".$tablePrincipale.".no";
		//$les_conditions .= " GROUP BY ".$tablePrincipale.".no";
	
	
	if(!empty($les_conditions))
		$WHERE = " WHERE ".$les_conditions;
	else
		$WHERE = "";
	if(!empty($GROUP_BY))
		$WHERE .= $GROUP_BY;
	
	// REQUETE COUNT VILLE
	$REQUETE_COUNT_VILLE = "SELECT COUNT(DISTINCT ".$tablePrincipale.".no) AS nb".$FROM_JOIN.((!empty($WHERE_COUNT_VILLE))?(" WHERE ".$WHERE_COUNT_VILLE):'').$GROUP_BY_VILLE;
	$count_ville = execute_requete($REQUETE_COUNT_VILLE,$param_count_ville);
	$count_ville = $count_ville[0]["nb"];
//        echo $REQUETE_COUNT_VILLE;
	
	// REQUETE COUNT TOTAL
	$REQUETE_COUNT_TOTAL = "SELECT COUNT(DISTINCT ".$tablePrincipale.".no) AS nb".$FROM_JOIN.((!empty($WHERE_COUNT_TOTAL))?" WHERE ".$WHERE_COUNT_TOTAL:"").$GROUP_BY_TOTAL;
	$count_total = execute_requete($REQUETE_COUNT_TOTAL,$param_count_total);
	$count_total = $count_total[0]["nb"];
	//etat, validation, du
	/*
	$where_count = " WHERE ";
	$param_count = array();
	
	if(!empty($conditions["du"])&&$conditions["du"]!=-1){ //Date de limite inférieure
		$where_count .= (($les_conditions!="")?" AND ":"").$tablePrincipale.".".$champ_dateDu.">=:du";
		$param_count[":du"] = $conditions["du"];
	}*/
	
	//$requete_count_ville = "SELECT COUNT("..".no) AS nb FROM ".." WHERE ".;
	
	//if($type=="forum")
		//echo $REQUETE_COUNT_TOTAL;
	//REQUETE COUNT PROCHE (si le rayon est défini)
	if(!empty($WHERE_COUNT_PROCHE)){
		//if($type=="forum")
			//echo $REQUETE_COUNT_PROCHE;
            if ($tablePrincipale != 'structure') {
                $WHERE_COUNT_PROCHE .= " AND ".$tablePrincipale.".titre <> ''";
            }
            $REQUETE_COUNT_PROCHE = "SELECT COUNT(DISTINCT ".$tablePrincipale.".no) AS nb".$FROM_JOIN.((!empty($WHERE_COUNT_PROCHE))?(" WHERE ".$WHERE_COUNT_PROCHE):'').$GROUP_BY_PROCHE;
            $count_proche = execute_requete($REQUETE_COUNT_PROCHE,$param_count_proche);
            $count_proche = $count_proche[0]["nb"];
	}
	
	//COUNT PAGE (nombre de page)
	$count_page = ceil((($conditions["distance"]==0)?$count_ville:(($count_proche>0)?$count_proche:$count_total))/$limite);
	if($page>$count_page&&$count_page>0)
		$page = $count_page;
	
	//LIMIT
	$limite_deb = ($page-1)*$limite;
	$LIMIT = " LIMIT ".$limite_deb.",".$limite;
	
	// REQUETE PRINCIPALE
	$REQUETE = $SELECT;
	$REQUETE .= $FROM_JOIN;
	$REQUETE .= $WHERE;
	$REQUETE .= $ORDER_BY;
	$REQUETE .= $LIMIT;
		//Si on a demandé d'élargir la recherche aux villes proches (on fait une union avec cette dernière)
		if($conditions["distance"]==0&&$conditions["elargir"]){
			$nb_restant = $limite-count_requete($REQUETE,$param);
			//S'il n'y a pas assez d'événement avec la requete actuelle.
			if($nb_restant>0){
				//On réalise la même avec le paramètre de distance
				$REQUETE_DIST =	$SELECT;
				$REQUETE_DIST .= $FROM_JOIN;
				$REQUETE_DIST .= " WHERE ".str_replace("no_ville=:id_ville","no_ville<>:id_ville",$les_conditions);
				$REQUETE_DIST .= " ORDER BY ".$champs_tri["distance"];
				$REQUETE_DIST .= " LIMIT ".$nb_restant;
				$REQUETE = "(".$REQUETE.") UNION (".$REQUETE_DIST.") ORDER BY distance,RAND()";
			}
		}
	
	//print_r($param);
	//echo $REQUETE.'<hr/>';
	
	
	//REQUETE COUNT PROCHE
	
	//echo $REQUETE;
	/*
	// COUNT VILLE
	$requete_countVille = "SELECT COUNT(".$tablePrincipale.".no) AS nb".$FROM_JOIN;
	//$requete_count_total = $requete_count." WHERE ".$tablePrincipale.".etat=1 AND ".$tablePrincipale.".validation=1 AND ".$tablePrincipale.".no_ville=:id_ville";
	$requete_countVille .= " WHERE ".$les_conditions;
	//On exécute le compteur
	$cptFiltreVille = execute_requete($requete_countVille,$param);
		$nbItem_filtreVille = $cptFiltreVille[0]["nb"];
	//$cpt_total = execute_requete($requete_count_total,array(":id_ville"=>$ID_VILLE));
		//$nb_item_total = $cpt_total[0]["nb"];
	*/
	//On exécute la requête
	//if($type=="forum")
		//echo $REQUETE;
	$tab = execute_requete($REQUETE,$param);
	$return = array();
	//On formate les données
	for($i=0;$i<count($tab);$i++){
		$return[$i] = array();
		//NO
		$return[$i]["no"] = $tab[$i]["no"];
		//Image
		if(!empty($tab[$i]["image"])){
			if(substr($tab[$i]["image"],0,7)!="http://"&&substr($tab[$i]["image"],0,8)!="https://")
				$return[$i]["image"] = $root_site_prod.$tab[$i]["image"];
			else
				$return[$i]["image"] = $tab[$i]["image"];
		}
		else{
			null;
		}
		//Titre
		if(!empty($tab[$i]["titre"])){
			if(empty($conditions["recherche"]))
				$return[$i]["titre"] = $tab[$i]["titre"];
			else
				$return[$i]["titre"] = surligne_recherche($tab[$i]["titre"],$les_mots_cles);
			$return[$i]["titresub"] = strip_tags($tab[$i]["titre"]);
			if(strlen($return[$i]["titresub"])>30)
				$return[$i]["titresub"] = substr($return[$i]["titresub"],0,30)." [...]";
		}
		//Description coupé pour l'affichage liste
		$return[$i]["descriptionsub"] = "";
		//Sous titre
		if(!empty($tab[$i]["sous_titre"])){
			$return[$i]["sous_titre"] = $tab[$i]["sous_titre"];
			
			//$return[$i]["descriptionsub"] .= '<h4>'.$return[$i]["sous_titre"].'</h4>';
			/*
			$return[$i]["sous_titresub"] = strip_tags($tab[$i]["sous_titre"]);
			if(strlen($return[$i]["sous_titresub"])>30)
				$return[$i]["sous_titresub"] = substr($return[$i]["sous_titresub"],0,30)." [...]";*/
		}
		//chapo
		if(!empty($tab[$i]["chapo"])){
			$return[$i]["chapo"] = $tab[$i]["chapo"];
			
			//$return[$i]["descriptionsub"] .= ((!empty($return[$i]["descriptionsub"]))?' ':'').$return[$i]["chapo"];
			$return[$i]["descriptionsub"] = $return[$i]["chapo"];
			
			//$return[$i]["descriptionsub"] = strip_tags(html_entity_decode($tab[$i]["chapo"],ENT_QUOTES,'UTF-8'));
			//if(strlen($return[$i]["chaposub"])>350)
			//	$return[$i]["chaposub"] = substr($return[$i]["chaposub"],0,350)." [...]";
		}
		//Description
		if(!empty($tab[$i]["description"])){
			$return[$i]["description"] = $tab[$i]["description"];
			if(empty($return[$i]["descriptionsub"]))
				$return[$i]["descriptionsub"] = $return[$i]["description"];
			/*$return[$i]["descriptionsub"] = strip_tags(html_entity_decode($tab[$i]["description"],ENT_QUOTES,'UTF-8'));
			if(strlen($return[$i]["descriptionsub"])>350)
				$return[$i]["descriptionsub"] = substr($return[$i]["descriptionsub"],0,350)." [...]";*/
		}
		//Description coupée pour l'affichage liste.
		//$return[$i]["descriptionsub"] = utf8_encode(utf8_decode(strip_tags(html_entity_decode($return[$i]["descriptionsub"],ENT_QUOTES,'UTF-8'))));
		
		$return[$i]["descriptionsub"] = strip_tags(html_entity_decode($return[$i]["descriptionsub"],ENT_QUOTES,'UTF-8'));
		
		/*if(!empty($return[$i]["sous_titre"]))
			$return[$i]["descriptionsub"] = '<h4>'.$return[$i]["sous_titre"].'</h4>'.$return[$i]["descriptionsub"];*/
		//$return[$i]["descriptionsub"] = utf8_encode(utf8_decode(strip_tags(html_entity_decode($return[$i]["descriptionsub"],ENT_QUOTES,'UTF-8'))));
		//$return[$i]["descriptionsub"] = strip_tags($return[$i]["descriptionsub"]);
		//$return[$i]["descriptionsub"] = strip_tags(html_entity_decode( (((!empty($return[$i]["sous_titre"]))?$return[$i]["sous_titre"].' ':'').((!empty($return[$i]["chapo"]))?$return[$i]["chapo"].' ':'').$return[$i]["description"]) ,ENT_QUOTES,'UTF-8' ));
		if(strlen($return[$i]["descriptionsub"])>350)
			$return[$i]["descriptionsub"] = mb_substr($return[$i]["descriptionsub"],0,350,'UTF-8')." [...]";
		
		
		
		//Notes
		if(!empty($tab[$i]["notes"]))
			$return[$i]["notes"] = $tab[$i]["notes"];
		//Dates
		if(!empty($tab[$i]["date_creation"]))
			$return[$i]["date_creation"] = datefr($tab[$i]["date_creation"]);
		if(!empty($tab[$i]["date_modification"]))
			$return[$i]["date_modification"] = datefr($tab[$i]["date_modification"],true);
		//Genre
		if(!empty($tab[$i]["genre"]))
			$return[$i]["genre"] = $tab[$i]["genre"];
		//Ville
		if(!empty($tab[$i]["ville"]))
			$return[$i]["ville"] = $tab[$i]["ville"];
		//Pseudo
		if(!empty($tab[$i]["pseudo"]))
			$return[$i]["pseudo"] = $tab[$i]["pseudo"];
		//No_utilisateur_creation
		if(!empty($tab[$i]["no_utilisateur"]))
			$return[$i]["no_utilisateur"] = $tab[$i]["no_utilisateur"];
		//Etat
		if(!empty($tab[$i]["etat"]))
			$return[$i]["etat"] = $tab[$i]["etat"];
		//Validation
		if(!empty($tab[$i]["validation"]))
			$return[$i]["validation"] = $tab[$i]["validation"];
		//no_ville
		if(!empty($tab[$i]["no_ville"]))
			$return[$i]["no_ville"] = $tab[$i]["no_ville"];
		//nb aime
		if(isset($tab[$i]["nb_aime"])){
			if(!empty($tab[$i]["nb_aime"]))
				$return[$i]["nb_aime"] = $tab[$i]["nb_aime"];
			else
				$return[$i]["nb_aime"] = 0;
			
			$res_nb_coupdecoeur->execute(array(":no"=>$tab[$i]["no"]));
			$tab_nb_coupdecoeur = $res_nb_coupdecoeur->fetchAll(PDO::FETCH_ASSOC);
			if(!empty($tab_nb_coupdecoeur)&&!empty($tab_nb_coupdecoeur[0]["nb"]))
				$return[$i]["nb_aime"] = (int)$return[$i]["nb_aime"]+(int)$tab_nb_coupdecoeur[0]["nb"];
		}
			
		//En fonction du type, les données sont plus ou moins différentes.
		if($type=="editorial"){
			$return[$i]["date_mise_en_ligne"] = str_replace('<br />',' ',date_fr_precise($tab[$i]["date_modification"]));
			//On récupère les fichiers
			$res_fichiers->execute(array(":no"=>$tab[$i]["no"]));
			$fichiers = $res_fichiers->fetchAll();
			$div_fichier = '<div class="home_editorial_bloc_fichiers">';
			for($f=0;$f<count($fichiers);$f++){
				$div_fichier .= '<img src="'.$fichiers[$f]["icone"].'" />';
			}
			$div_fichier .= '</div>';
			$return[$i]["div_fichier"] = $div_fichier;
			if((bool)$tab[$i]["afficher_signature"]&&!empty($tab[$i]["signature"]))
				$return[$i]["pseudo"] = $tab[$i]["signature"];
		}
		else if($type=="agenda"){
			$return[$i]["date_fin"] = datefr($tab[$i]["date_fin"]);
			$return[$i]["date_debut"] = datefr($tab[$i]["date_debut"]);
			if($tab[$i]["date_debut"]!=$tab[$i]["date_fin"]){
				$return[$i]["datehome"] = datehome($tab[$i]["date_debut"]).'<div class="jr">&rsaquo;</div>'.datehome($tab[$i]["date_fin"]);
				$return[$i]["date_du_au"] = "Du ".datefr($tab[$i]["date_debut"])." au ".datefr($tab[$i]["date_fin"]);
			}
			else{
				$return[$i]["datehome"] = datehome($tab[$i]["date_debut"]);
				$return[$i]["date_du_au"] = "Le ".datefr($tab[$i]["date_debut"]);
			}
		}
		else if($type=="repertoire"||$type=="structure"){
			$return[$i]["statut"] = $tab[$i]["statut"];
		}
		else if($type=="petite-annonce"){
			$return[$i]["date_fin"] = datefr($tab[$i]["date_fin"]);
			$return[$i]["monetaire"] = (bool)$tab[$i]["monetaire"];
			$return[$i]["prix"] = str_replace('.00','',$tab[$i]["prix"]);
		}
		else if($type=="forum"){
			$return[$i]["derniere_reponse"] = str_replace('<br />',' ',date_fr_precise($tab[$i]["derniere_reponse"]));
			$return[$i]["est_citoyen"] = (bool)$tab[$i]["est_citoyen"];
			if($return[$i]["sous_titre"]==$return[$i]["ville"])
				$return[$i]["sous_titre"] = a_le_ville($return[$i]["sous_titre"]);
		}
	}
	$nbItem = $nbItem_filtreVille+$nbItem_filtreProche;
	$nb_page = ceil($nbItem/$limite);
	return array("count_ville"=>$count_ville,"count_proche"=>$count_proche,"count_total"=>$count_total,"count_page"=>$count_page,"liste"=>$return);
}

function extraire_fiche($type,$no){
	$extraction = "fiche";
	global $ID_VILLE;
	global $root_site_prod;
	//En fonction du type, on initialise les données et la requête
	if($type=="editorial"){
		include "_var_editorial.php";
	}
	else if($type=="agenda"||$type=="evenement"){
		$type="agenda";
		include "_var_agenda.php";
	}
	else if($type=="petite-annonce"){
		include "_var_petiteannonce.php";
	}
	else if($type=="repertoire"||$type=="structure"){
		$type = "repertoire";
		include "_var_repertoire.php";
	}
	else if($type=="forum"){
		include "_var_forum.php";
	}
	//SELECT
	$SELECT = "SELECT ";
		$les_champs = "";
		for($r=0;$r<count($champs_fiche);$r++){
			$les_champs .= (($les_champs!="")?", ":"").$champs_fiche[$r]["champ"]." AS ".$champs_fiche[$r]["alias"];
		}
	$SELECT .= $les_champs;
	// FROM & JOIN
	$FROM_JOIN = " FROM ".$tablePrincipale;
	$FROM_JOIN .= " LEFT JOIN utilisateur ON utilisateur.no=".$tablePrincipale.".no_utilisateur_creation";	//UTILISATEUR
	if($type=="agenda")
		$FROM_JOIN .= " LEFT JOIN genre ON genre.no=".$tablePrincipale.".no_genre";								//GENRE
	else if($type=="repertoire"){
		$FROM_JOIN .= " LEFT JOIN statut ON statut.no=".$tablePrincipale.".no_statut";							//Statut
	}
	else if($type=="petite-annonce"){
		$FROM_JOIN .= " LEFT JOIN petiteannonce_type ON petiteannonce_type.no=".$tablePrincipale.".no_petiteannonce_type";							//Type d'annonce
		$FROM_JOIN .= " LEFT JOIN ".$tablePrincipale."_contact ON ".$tablePrincipale."_contact.no_".$tablePrincipale."=".$tablePrincipale.".no";
		$FROM_JOIN .= " LEFT JOIN contact ON ".$tablePrincipale."_contact.no_contact=contact.no";
	}
	else if($type=="forum"){
		$FROM_JOIN .= " LEFT JOIN forum_type ON forum_type.no=forum.no_forum_type";								//Type de forum
		$FROM_JOIN .= " LEFT JOIN messageForum ON messageForum.no_forum=forum.no";							//Message forum
	}
	$FROM_JOIN .= " LEFT JOIN villes ON villes.id=".$tablePrincipale.".no_ville";								//VILLE
	
	$WHERE = " WHERE ".$tablePrincipale.".no=:no";
	$param = array("no"=>$no);
	
	$REQUETE = $SELECT.$FROM_JOIN.$WHERE;
	
	$tab = execute_requete($REQUETE,$param);
	
	$return = array();
	if(count($tab)>0){
		//NO
		$return["no"] = $tab[0]["no"];
		//Image
		if(!empty($tab[0]["image"])){
			if(substr($tab[0]["image"],0,7)!="http://")
				$return["image"] = $root_site_prod.$tab[0]["image"];
			else
				$return["image"] = $tab[0]["image"];
		}
		else{
			if($type=="petite-annonce"||$type=="repertoire")
				$return["image"] = $root_site_prod."img/logo-ensembleici_fb.jpg";
		}
			//Copyright
			if(!empty($tab[0]["copyright"]))
				$return["copyright"] = $tab[0]["copyright"];
			//Légende
			if(!empty($tab[0]["legende"]))
				$return["legende"] = $tab[0]["legende"];
		//Titre
		if(!empty($tab[0]["titre"])){
			$return["titre"] = $tab[0]["titre"];
			if(strlen($tab[0]["titre"])>30)
				$return["titresub"] = substr($tab[0]["titre"],0,30)." [...]";
			else
				$return["titresub"] = $tab[0]["titre"];
		}
		//Sous titre
		if(!empty($tab[0]["sous_titre"])){
			$return["sous_titre"] = $tab[0]["sous_titre"];
			$return["sous_titresub"] = strip_tags($tab[0]["sous_titre"]);
			if(strlen($return["sous_titresub"])>30)
				$return["sous_titresub"] = substr($return["sous_titresub"],0,30)." [...]";
		}
		//Site
		if(!empty($tab[0]["site"])){
			$return["site"] = $tab[0]["site"];
		}
		else if(!empty($tab[0]["site_internet"])){
			$return["site"] = $tab[0]["site_internet"];
		}
		//Facebook
		if(!empty($tab[0]["facebook"])){
			$return["facebook"] = $tab[0]["facebook"];
		}
		//Coordonnées
		$return["coordonnees"] = "";
			$return["telephone"] = $tab[0]["telephone"];
			$return["telephone2"] = $tab[0]["telephone2"];
			if(!empty($return["telephone"])){
				$return["coordonnees"] = '<b>Tél.</b>&nbsp;:&nbsp;'.$return["telephone"];
				if(!empty($return["telephone2"]))
					$return["coordonnees"] .= '&nbsp;|&nbsp;'.$return["telephone2"];
			}
			if(!empty($tab[0]["email"])){
				$return["coordonnees"] .= (($return["coordonnees"]!="")?'<br />':'').'<b>Email</b>&nbsp;:&nbsp;'.$tab[0]["email"];
			}
			if(!empty($tab[0]["site"])){
				$return["coordonnees"] .= (($return["coordonnees"]!="")?'<br />':'').'<b>Site</b>&nbsp;:&nbsp;'.$tab[0]["site"];
			}
			if(!empty($tab[0]["nomadresse"])){
				$return["coordonnees"] .= (($return["coordonnees"]!="")?'<br />':'').'<b>Lieu</b>&nbsp;:&nbsp;'.$tab[0]["nomadresse"];
			}
			if(!empty($tab[0]["adresse"])){
				$return["coordonnees"] .= (($return["coordonnees"]!="")?'<br />':'').'<b>Adresse</b>&nbsp;:&nbsp;'.$tab[0]["adresse"].'<br/>'.$CODE_POSTAL.'&nbsp;-&nbsp;'.$NOM_VILLE_MAJ;
			}
			
			
		//Description
		if(!empty($tab[0]["description"])){
			$return["description"] = $tab[0]["description"];
			if(strlen($tab[0]["description"])>350)
				$return["descriptionsub"] = substr($tab[0]["description"],0,350)." [...]";
			else
				$return["descriptionsub"] = $tab[0]["description"];
		}
		if(!empty($tab[0]["description_complementaire"]))
			$return["description_complementaire"] = $tab[0]["description_complementaire"];
		//Dates
		if(!empty($tab[0]["date_creation"])){
			$test_heures = explode(" ",$tab[0]["date_creation"]);
			$return["date_creation"] = datefr($tab[0]["date_creation"],(count($test_heures)>1));
		}
		if(!empty($tab[0]["date_modification"]))
			$return["date_modification"] = datefr($tab[0]["date_modification"],true);
		//Genre
		if(!empty($tab[0]["genre"]))
			$return["genre"] = $tab[0]["genre"];
			if(!empty($tab[0]["no_genre"]))
				$return["no_genre"] = $tab[0]["no_genre"];
		//Lieux
		//Ville
		if(!empty($tab[0]["ville"]))
			$return["ville"] = $tab[0]["ville"];
			//no_ville
			if(!empty($tab[0]["no_ville"]))
				$return["no_ville"] = $tab[0]["no_ville"];
			//code postal
			if(!empty($tab[0]["cp"]))
				$return["cp"] = $tab[0]["cp"];
			//nom_adresse
			if(!empty($tab[0]["nom_adresse"]))
				$return["nom_adresse"] = $tab[0]["nom_adresse"];
			//adresse
			if(!empty($tab[0]["adresse"]))
				$return["adresse"] = $tab[0]["adresse"];
			//email
			if(!empty($tab[0]["email"]))
				$return["email"] = $tab[0]["email"];
			//telephone
			if(!empty($tab[0]["telephone"]))
				$return["telephone"] = formate_telephone($tab[0]["telephone"]);
			//telephone2
			if(!empty($tab[0]["telephone2"]))
				$return["telephone2"] = formate_telephone($tab[0]["telephone2"]);
				
			//Contact
			if(!empty($tab[0]["no_contact"]))
				$return["no_contact"] = $tab[0]["no_contact"];
		
		//Etat et validation et nb_aime
		$return["etat"] = $tab[0]["etat"];
		$return["validation"] = $tab[0]["validation"];
		$return["nb_aime"] = $tab[0]["nb_aime"];
		
		$requete_nb_coupdecoeur = "SELECT COUNT(".$tablePrincipale."_coupdecoeur.no) AS nb FROM ".$tablePrincipale."_coupdecoeur WHERE ".$tablePrincipale."_coupdecoeur.no_".$tablePrincipale."=:no";
		$tab_nb_coupdecoeur = execute_requete($requete_nb_coupdecoeur,array(":no"=>$no));
		if(!empty($tab_nb_coupdecoeur)&&!empty($tab_nb_coupdecoeur[0]["nb"]))
			$return["nb_aime"] = (int)$return["nb_aime"]+(int)$tab_nb_coupdecoeur[0]["nb"];
		
			
		//Pseudo
		if(!empty($tab[0]["pseudo"]))
			$return["pseudo"] = $tab[0]["pseudo"];
		//no_utilisateur
		if(!empty($tab[0]["no_utilisateur"]))
			$return["no_utilisateur"] = $tab[0]["no_utilisateur"];
		//email_utilisateur
		if(!empty($tab[0]["email_utilisateur"]))
			$return["email_utilisateur"] = $tab[0]["email_utilisateur"];
			
		//En fonction du type, les données sont plus ou moins différentes.
		if($type=="editorial"){
			$return["notes"] = $tab[0]["notes"];
			$return["chapo"] = $tab[0]["chapo"];
			$return["afficher_signature"] = (bool)$tab[0]["afficher_signature"];
			$return["date_mise_en_ligne"] = date_fr_precise($tab[0]["date_modification"]);
			if((bool)$tab[0]["afficher_signature"]&&!empty($tab[0]["signature"]))
				$return["pseudo"] = $tab[0]["signature"];
			//On récupère les fichiers
			$res_fichiers->execute(array(":no"=>$tab[0]["no"]));
			$fichiers = $res_fichiers->fetchAll();
			$div_fichier = '<div class="home_editorial_bloc_fichiers">';
			for($f=0;$f<count($fichiers);$f++){
				$div_fichier .= '<img src="'.$fichiers[$f]["icone"].'" />';
			}
			$div_fichier .= '</div>';
			$return["div_fichier"] = $div_fichier;
			
			
			$return["fichiers_audio"] = extraire_fichiers_audio_edito($tab[0]["no"]);
		}
		else if($type=="agenda"){
			$return["date_fin"] = datefr($tab[0]["date_fin"]);
			$return["date_debut"] = datefr($tab[0]["date_debut"]);
			if(!empty($tab[0]["heure_fin"]))
				$return["heure_fin"] = heurefr($tab[0]["heure_fin"]);
			if(!empty($tab[0]["heure_debut"]))
				$return["heure_debut"] = heurefr($tab[0]["heure_debut"]);
			if($tab[0]["date_debut"]!=$tab[0]["date_fin"]){
				$return["datehome"] = datehome($tab[0]["date_debut"]).'<div class="jr">&rsaquo;</div>'.datehome($tab[0]["date_fin"]);
				$return["date_du_au"] = "Du ".datefr($tab[0]["date_debut"])." au ".datefr($tab[0]["date_fin"]);
				$return["date_du_au_precise"] = date_fr_precise($tab[0]["date_debut"].' '.$tab[0]["heure_debut"],$tab[0]["date_fin"].' '.$tab[0]["heure_fin"]);
			}
			else{
				if(!empty($return["heure_fin"])&&!empty($return["heure_debut"])&&$return["heure_fin"]!=$return["heure_debut"]){
					$return["date_du_au_precise"] = date_fr_precise($tab[0]["date_debut"]).' de '.$return["heure_debut"].' à '.$return["heure_fin"];
				}
				else{
					$return["datehome"] = datehome($tab[0]["date_debut"]);
					$return["date_du_au"] = "Le ".datefr($tab[0]["date_debut"]);
					$return["date_du_au_precise"] = date_fr_precise($tab[0]["date_debut"].' '.$tab[0]["heure_debut"]);
				}
			}
		}
		else if($type=="repertoire"){
			$return["no_statut"] = $tab[0]["no_statut"];
		}
		else if($type=="petite-annonce"){
			$return["date_fin"] = datefr($tab[0]["date_fin"]);
			$return["monetaire"] = (bool)$tab[0]["monetaire"];
			$return["prix"] = str_replace('.00','',$tab[0]["prix"]);
			$return["rayonmax"] = $tab[0]["rayonmax"];
			$return["no_petiteannonce_type"] = $tab[0]["no_petiteannonce_type"];
		}
		else if($type=="forum"){
			$return["signature"] = $tab[0]["signature"];
			$return["afficher_signature"] = (bool)$tab[0]["afficher_signature"];
			$return["no_forum_type"] = $tab[0]["no_forum_type"];
			$return["derniere_reponse"] = str_replace('<br />',' ',date_fr_precise($tab[0]["derniere_reponse"]));
			$return["est_citoyen"] = (bool)$tab[0]["est_citoyen"];
		}
	}
	return $return;
}

function extraire_fichiers_audio_edito($no){
	$requete = "SELECT fichieraudio.no, fichieraudio.site, fichieraudio.url, fichieraudio.titre, fichieraudio.auteur FROM fichieraudio
				JOIN fichier_galerie ON fichieraudio.no=fichier_galerie.no_fichier
				JOIN galerie ON fichier_galerie.no_galerie=galerie.no
				JOIN editorial_galerie ON editorial_galerie.no_galerie=galerie.no
				WHERE editorial_galerie.no_editorial=:no";
	$param = array(":no"=>$no);
	return execute_requete($requete,$param);
}

function extraire_editorial_ei(){
	//On récupère le dernier éditorial ensemble-ici
	return '<h3>Ensembleici fait peau neuve&nbsp;!</h3><p>Ensembleici vous accueille aujourd\'hui sur sa nouvelle version.</p><p>Un nouveau menu vous permet de vous deplacer auisément entre les différentes parties du site.</p><p>Un éditorial est désormais en ligne, vivez l\'actualité autour de chez vous!</p><p>Le site est maintenant compatible tablettes et mobiles !</p>';
}


//On récupère la liste des types d'annonces
function get_petiteannonceTypes(){
	return execute_requete("SELECT no, libelle FROM petiteannonce_type ORDER BY no");
}
function creer_input_petiteannonceTypes($no_petiteannonceType){
	$les_petiteannonceTypes = get_petiteannonceTypes();
	$select_petiteannonceTypes = '<select name="BDDno_petiteannonce_type" id="BDDno_petiteannonce_type">';
		if($no_petiteannonceType==0)
			$select_petiteannonceTypes .= '<option value="0">Séléctionner un Type</option>';
		for($i=0;$i<count($les_petiteannonceTypes);$i++){
			$select_petiteannonceTypes .= '<option value="'.$les_petiteannonceTypes[$i]["no"].'"'.(($no_petiteannonceType!=$les_petiteannonceTypes[$i]["no"])?'':' selected="selected"').'>';
				$select_petiteannonceTypes .= $les_petiteannonceTypes[$i]["libelle"];
			$select_petiteannonceTypes .= '</option>';
		}
	$select_petiteannonceTypes .= '</select>';
	return $select_petiteannonceTypes;
}

//On récupère la liste des types de forums
function get_forumTypes(){
	return execute_requete("SELECT no, libelle FROM forum_type ORDER BY no");
}
function creer_input_forumTypes($no_forumType){
	$les_forumTypes = get_forumTypes();
	if($no_forumType!=1){$i_depart=1;$i_fin=count($les_forumTypes);$disabled='';}
	else{$i_depart = 0;$i_fin=1;$disabled=' disabled="disabled"';}
	$select_forumType = '<select name="BDDno_forum_type" id="BDDno_forum_type"'.$disabled.'>';
		if($no_forumType==0)
			$select_forumType .= '<option value="0">Séléctionner un Type</option>';
		for($i=$i_depart;$i<$i_fin;$i++){
			$select_forumType .= '<option value="'.$les_forumTypes[$i]["no"].'"'.(($no_forumType!=$les_forumTypes[$i]["no"])?'':' selected="selected"').'>';
				$select_forumType .= $les_forumTypes[$i]["libelle"];
			$select_forumType .= '</option>';
		}
	$select_forumType .= '</select>';
	return $select_forumType;
}

//On récupère la liste des statuts.
function get_statuts(){
	return execute_requete("SELECT no, libelle FROM statut ORDER BY libelle");
}
function creer_input_statuts($no_statut=0){
	$les_statuts = get_statuts();
	$select_statut = '<select name="BDDno_statut" id="BDDno_statut">';
		if($no_statut==0)
			$select_statut .= '<option value="0">Séléctionner un statut</option>';
		for($i=0;$i<count($les_statuts);$i++){
			$select_statut .= '<option value="'.$les_statuts[$i]["no"].'"'.(($no_statut!=$les_statuts[$i]["no"])?'':' selected="selected"').'>';
				$select_statut .= $les_statuts[$i]["libelle"];
			$select_statut .= '</option>';
		}
	$select_statut .= '</select>';
	return $select_statut;
}

//On récupère la liste des genres.
function get_genres(){
	return execute_requete("SELECT no, libelle, IF(type_genre='E','événement','atelier') AS type FROM genre ORDER BY libelle, type");
}
function creer_input_genres($no_genre=0){
	$les_genres = get_genres();
	$select_genre = '<select name="BDDno_genre" id="BDDno_genre">';
		if($no_genre==0)
			$select_genre .= '<option value="0">Séléctionner un genre</option>';
		for($i=0;$i<count($les_genres);$i++){
			$select_genre .= '<option value="'.$les_genres[$i]["no"].'"'.(($no_genre!=$les_genres[$i]["no"])?'':' selected="selected"').'>';
				$select_genre .= $les_genres[$i]["libelle"].' ('.$les_genres[$i]["type"].')';
			$select_genre .= '</option>';
		}
	$select_genre .= '</select>';
	return $select_genre;
}

function get_vies($no_tag=0){
	$requete = "SELECT * FROM vie";
	if($no_tag>0){
		$requete .= " JOIN vie_tag ON vie_tag.no_vie=vie.no WHERE vie_tag.no_tag=:no";
		$param = array(":no"=>$no_tag);
	}
	else
		$param = array();
	$requete .= " ORDER BY libelle";
	return execute_requete($requete,$param);
}

function get_ficheTags($x_type,$x_no,$demande_classe=false){
	if($x_type=="petite-annonce")
		$x_type = "petiteannonce";
	else if($x_type=="repertoire")
		$x_type = "structure";
	else if($x_type=="agenda")
		$x_type = "evenement";
	$table_liaison_tag=$x_type.'_tag';
	
	$requete_tag="SELECT tag.no, tag.titre";
	$requete_tag.=" FROM tag";
	$requete_tag.= " JOIN ".$table_liaison_tag." ON ".$table_liaison_tag.".no_tag=tag.no";
	$requete_tag.=" WHERE ".$table_liaison_tag.".no_".$x_type."=:no";
	
	if(!$demande_classe)
		return execute_requete($requete_tag,array(":no"=>$x_no));
	else{
		$les_tags = execute_requete($requete_tag,array(":no"=>$x_no));
		$return = array();
		for($t=0;$t<count($les_tags);$t++){
			$les_vies_tag = get_vies($les_tags[$t]["no"]);
			$classe_vie = "";
			for($v=0;$v<count($les_vies_tag);$v++){
				$classe_vie .= " ".url_rewrite($les_vies_tag[$v]["libelle"]);
			}
			$return[] = array("no"=>$les_tags[$t]["no"],"titre"=>$les_tags[$t]["titre"],"class"=>$classe_vie);
		}
		return $return;
	}
}

function get_tags_depuis_liste($liste_no,$demande_classe=false){
	$requete_tag = "SELECT tag.no,tag.titre FROM tag WHERE tag.no IN (".$liste_no.")";
	if(!$demande_classe){
		return execute_requete($requete_tag,$param);
	}
	else{
		$les_tags = execute_requete($requete_tag,$param);
		$return = array();
		for($t=0;$t<count($les_tags);$t++){
			$les_vies_tag = get_vies($les_tags[$t]["no"]);
			$classe_vie = "";
			for($v=0;$v<count($les_vies_tag);$v++){
				$classe_vie .= " ".url_rewrite($les_vies_tag[$v]["libelle"]);
			}
			$return[] = array("no"=>$les_tags[$t]["no"],"titre"=>$les_tags[$t]["titre"],"class"=>$classe_vie);
		}
		return $return;
	}
}

function get_tags($vie="",$exception="",$recherche="",$demande_classe=false){
	$param = array();
	$requete_tag = "SELECT tag.no,tag.titre FROM tag";
	$where_tag = "";
	if(!empty($vie)){
		$where_tag .= " JOIN vie_tag ON vie_tag.no_tag=tag.no WHERE vie_tag.no_vie=:v";
		$param[":v"] = $vie;
	}
	if(!empty($exception)){
		$where_tag .= ((!empty($where_tag))?" AND":" WHERE")." tag.no NOT IN(".$exception.")";
	}
	if(!empty($recherche)){
		//On supprime les accents
		$recherche = htmlentities($recherche, ENT_NOQUOTES,'utf-8'); 
		$recherche = preg_replace('#&([A-Za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $recherche);
		$recherche = preg_replace('#&([A-Za-z]{2})(?:lig);#', '\1', $recherche); // pour les ligatures e.g. '&oelig;'
		$recherche = preg_replace('#&[^;]+;#', '', $recherche); // supprime les autres caractères
		// On supprime les caractères spéciaux, ponctuation, ...
		$recherche = preg_replace('/[^A-Za-z0-9]+/', ' ', $recherche);
		// On remplace les tirets multiples qui se suivent par un seul tiret
		$recherche = preg_replace('# {2,}#', ' ', $recherche);
		
		$tab_mot_rech = explode(" ",$recherche);
		for($i_rech=0;$i_rech<count($tab_mot_rech);$i_rech++){
			if(strlen($tab_mot_rech[$i_rech])>0){
				$where_tag .= ((!empty($where_tag))?" AND":" WHERE")." titre LIKE :recherche".$i_rech;
				if(strlen($tab_mot_rech[$i_rech])>2)
					$param[":recherche".$i_rech] = "%".$tab_mot_rech[$i_rech]."%";
				else
					$param[":recherche".$i_rech] = $tab_mot_rech[$i_rech]."%";
			}
		}
		
	}
	$requete_tag .= $where_tag." ORDER BY titre";
	if(!$demande_classe){
		return execute_requete($requete_tag,$param);
	}
	else{
		$les_tags = execute_requete($requete_tag,$param);
		$return = array();
		for($t=0;$t<count($les_tags);$t++){
			$les_vies_tag = get_vies($les_tags[$t]["no"]);
			$classe_vie = "";
			for($v=0;$v<count($les_vies_tag);$v++){
				$classe_vie .= " ".url_rewrite($les_vies_tag[$v]["libelle"]);
			}
			$return[] = array("no"=>$les_tags[$t]["no"],"titre"=>$les_tags[$t]["titre"],"class"=>$classe_vie);
		}
		return $return;
	}
}

//On récupère les communautés de communes
function get_communautescommunes(){
	return execute_requete("SELECT communautecommune.no, communautecommune.libelle, communautecommune.no_ville, villes.nom_ville_maj AS ville FROM communautecommune JOIN villes ON villes.id=communautecommune.no_ville ORDER BY no DESC");
}

function creer_input_communautescommunes($no_ville=0){
	global $ID_VILLE;
	if(empty($no_ville))
		$no_ville = $ID_VILLE;
	$les_cc = get_communautescommunes();
	$select_cc = '<select name="BDDno_ville_cc" id="BDDno_ville_cc" style="text-align:center;">';
		if($no_genre==0)
			$select_cc .= '<option value="0">Où êtes vous ?</option>';
		for($i=0;$i<count($les_cc);$i++){
			$select_cc .= '<option value="'.$les_cc[$i]["no_ville"].'"'.(($no_ville!=$les_cc[$i]["no_ville"])?'':' selected="selected"').'>';
				$select_cc .= $les_cc[$i]["libelle"].' ('.$les_cc[$i]["ville"].')';
			$select_cc .= '</option>';
		}
	$select_cc .= '</select>';
	return $select_cc;
}

//On récupère la liste des types de contacts.
function get_contactType(){
	return execute_requete("SELECT no, libelle FROM contactType ORDER BY no");
}
function creer_input_contactType($no_contactType=0,$id="BDDno_contactType"){
	$les_contactType = get_contactType();
	$select_contactType = '<select name="'.$id.'" id="'.$id.'" onchange="if(typeof(changer_type_contact)==\'function\')changer_type_contact(this);">';
		//if($no_contactType==0)
			$select_contactType .= '<option value="0">Autre (choisir)</option>';
		for($i=0;$i<count($les_contactType);$i++){
			$select_contactType .= '<option value="'.$les_contactType[$i]["no"].'"'.(($no_contactType!=$les_contactType[$i]["no"])?'':' selected="selected"').'>';
				$select_contactType .= $les_contactType[$i]["libelle"];
			$select_contactType .= '</option>';
		}
	$select_contactType .= '</select>';
	return $select_contactType;
}
//On récupère la liste des types de contacts.
function get_contactRole(){
	return execute_requete("SELECT no, libelle FROM role ORDER BY libelle");
}
function creer_input_contactRole($no_contactRole=0,$id="BDDno_contactRole",$display_none=false){
	$les_contactRole = get_contactRole();
	$select_contactRole = '<select name="'.$id.'" id="'.$id.'"'.(($display_none)?' style="display:none;"':'').'>';
		//if($no_contactRole==0)
			$select_contactRole .= '<option value="0">Rôle du contact</option>';
		for($i=0;$i<count($les_contactRole);$i++){
			$select_contactRole .= '<option value="'.$les_contactRole[$i]["no"].'"'.(($no_contactRole!=$les_contactRole[$i]["no"])?'':' selected="selected"').'>';
				$select_contactRole .= $les_contactRole[$i]["libelle"];
			$select_contactType .= '</option>';
		}
	$select_contactRole .= '</select>';
	return $select_contactRole;
}

function a_droit($page,$no_utilisateur=false,$ecriture=false){ //ecriture=false: lecture | ecriture=true : ecriture
	if(!empty($_SESSION['droit'])){ //Administrateur / éditeur
		if($_SESSION['droit']['no']>1){ //Éditeur
			if($no_utilisateur!=false){ //C'est une fiche...
				if($page=="evenement"||$page=="structure"){
					if($ecriture)
						return true;
					else
						return false;
				}
				else if($page=="editorial"||$page=="petite-annonce"||$page=="forum"){
					if($no_utilisateur==$_SESSION["utilisateur"]["no"])
						return true;
					else
						return false;
				}
				else{
					return false;
				}
			}
			else{ //C'est une liste
				//Ici quoi qu'il arrive, du coup nous sommes en mode lecture.
					//On vérifie juste du coup que l'utilisateur soit bien dans une page dont il a le droit de voir le contenu.
				$requete = "SELECT administrationMenu.url_rewrite FROM administrationMenu JOIN droit_administrationMenu ON droit_administrationMenu.no_administrationMenu=administrationMenu.no WHERE droit_administrationMenu.no_droit=:d AND administrationMenu.url_rewrite=:p";
				if(count_requete($requete,array(":d"=>$_SESSION['droit']['no'],":p"=>$page))>0)
					return true;
				else
					return false;
			}
		}
		else //Adminitrateur (tous les droits)
			return true;
	}
	else
		return false;
}

function est_connecte(){
	if(!empty($_SESSION)){ //La session n'est pas vide
		if(!empty($_SESSION['id_connexion'])){ //L'utilisateur a un id de connexion, il faut le tester 
			$requete_test = "SELECT no FROM utilisateur WHERE id_connexion=:id AND email=:e";
			$params = array(":id"=>$_SESSION["id_connexion"],":e"=>$_SESSION["utilisateur"]["email"]);
			if(count_requete($requete_test,$params)==1){ // L'utilisateur est bien connecté
				return true;
			}
			else{ // L'utilisateur s'est sûrement connecté ailleurs entre temps.
				//On vide alors la session pour ne plus passer dans cette conditionnelle, et on retourne false
				deconnexion();
				return false;
			}
		}
		else{
			return false;
		}
	}
	else //Session vide : utilisateur déconnecté.
		return false;
}

function deconnexion(){
	if(isset($_COOKIE[session_name()]))
		setcookie(session_name(),"",time()-3600,"/");
	//On vide la session
	$_SESSION = array();
	session_destroy();
}

function connexion($email,$mdp,$admin=false){
	if(!empty($email)&&!empty($mdp)){
		global $cle_cryptage;
		//On récupère l'utilisateur en fonction de son email et mot de passe
		$requete = "SELECT utilisateur.no,utilisateur.email,utilisateur.pseudo,utilisateur.no_ville,IF(utilisateur.mot_de_passe=:mdp,1,0) AS bon_mdp, droit.no AS no_droit, droit.libelle AS libelle_droit, C.territoires_id, T.facebook, T.code_ua FROM utilisateur LEFT JOIN droit_utilisateur ON droit_utilisateur.no_utilisateur=utilisateur.no LEFT JOIN droit ON droit_utilisateur.no_droit=droit.no LEFT JOIN communautecommune_ville V ON utilisateur.no_ville = V.no_ville LEFT JOIN communautecommune C ON V.no_communautecommune = C.no LEFT JOIN territoires T ON C.territoires_id = T.id WHERE utilisateur.etat=1 AND utilisateur.email=:email";
		$tab_requete = execute_requete($requete,array(":email"=>$email,":mdp"=>md5($email.$mdp.$cle_cryptage)));
		if(count($tab_requete)>0){ //Compte existant (email)
			$connect = $tab_requete[0]; //On récupère le résultat
			if($connect["bon_mdp"]>0){ //Le mot de passe est correct
				$_SESSION["id_connexion"] = id_aleatoire();
				$_SESSION["derniere_connexion"] = date("Y-m-d H:i:s");
				$_SESSION["id_ville"] = $connect["no_ville"];
				
				//On remplit alors la session
				$_SESSION["utilisateur"] = array();
				$_SESSION["utilisateur"]["no"] = $connect["no"];
				$_SESSION["utilisateur"]["pseudo"] = $connect["pseudo"];
				$_SESSION["utilisateur"]["email"] = $connect["email"];
				$_SESSION["utilisateur"]["no_ville"] = $connect["no_ville"];
                                $default_territoire = 1;
                                $default_facebook = 'https://www.facebook.com/ensembleici';
                                $default_code_ua = 'UA-32761608-1';
                                if ($connect["territoires_id"] != '') {
                                    $default_territoire = $connect["territoires_id"];
                                    $default_facebook = $connect["facebook"];
                                    $default_code_ua = $connect["code_ua"];
                                }
                                $_SESSION["utilisateur"]["territoire"] = $default_territoire;
                                $_SESSION["utilisateur"]["facebook"] = $default_facebook;
                                $_SESSION["utilisateur"]["code_ua"] = $default_code_ua;
				
				//$return = array(true,array("message"=>"Bienvenue ".$_SESSION["utilisateur"]["pseudo"]." !","pseudo"=>$_SESSION["utilisateur"]["pseudo"]));
				
				$_SESSION["droit"] = array();
				//Ici l'utilisateur est donc bien connecté, il ne manque plus qu'a tester ses droits.
				if(!empty($connect["no_droit"])){ //L'utilisateur a des droits particuliers, on les renseigne
					$_SESSION["droit"]["no"] = $connect["no_droit"];
					$_SESSION["droit"]["libelle"] = $connect["libelle_droit"];
					
					/*$return[1]["fonction"] = $_SESSION["droit"]["libelle"];
					$return[1]["menu"] = $tab_menu*/
				}
				
				//On met à jour la dernière connexion, le cookie de ville, et l'id de connexion
				$requete_idConnect = "UPDATE utilisateur SET utilisateur.id_connexion=:id WHERE utilisateur.no=:no";
				execute_requete($requete_idConnect,array(":id"=>$_SESSION["id_connexion"],":no"=>$_SESSION["utilisateur"]["no"]));
				
				
				$requete_lastConnect = "INSERT INTO utilisateur_connexions(quand,IP,email,user_agent) VALUES(:d,:ip,:e,:user_agent)";
				execute_requete($requete_lastConnect,array(":d"=>$_SESSION["derniere_connexion"],":ip"=>$_SERVER['REMOTE_ADDR'],":e"=>$_SESSION["utilisateur"]["email"],":user_agent"=>$_SERVER['HTTP_USER_AGENT']));
				
				
				setcookie("id_ville", $no_ville, time() + 365*24*3600,"/", null, false, true);
				
				
				if($admin&&empty($_SESSION["droit"])){
					return array(false,"Le compte \"".$email."\" ne possède pas les autorisations nescessaires pour accéder à cet espace.");
				}
				else{
					/*if($admin){
						$requete_menu = "SELECT * FROM  administrationMenu JOIN droit_administrationMenu ON droit_administrationMenu.no_administrationMenu=administrationMenu.no WHERE droit_administrationMenu.no_droit=:no_droit";
						$tab_menu = execute_requete($requete_menu,array(":no_droit"=>$_SESSION["droit"]["no"]));
					}*/
					return array(true,"Bienvenue ".$_SESSION["utilisateur"]["pseudo"]." !");
				}
			}
			else{
				return array(false,"Mot de passe incorrecte, vous pouvez le réinitialiser en cliquant sur \"Mot de passe oublié\"");
			}
		}
		else{
			return array(false,"Aucun compte existant pour l'adresse mail : ".$email);
		}
	}
	else{
		return array(false,"Vous devez saisir votre adresse mail et votre mot de passe");
	}
}

function surligne_recherche($phrase,$tab_mot_cle){
	$phrase = strip_tags($phrase);
	$tab_remplacement = array();
	$tab_a_remplacer = array();
	for($i=0;$i<count($tab_mot_cle);$i++){
		if($tab_mot_cle[$i]!=""){
			if(preg_match("#(".$tab_mot_cle[$i].")#iu", $phrase, $result)){
				$phrase = str_replace($result[0],"|!".$i."!|",$phrase);
				$tab_remplacement[] = "<span class=\"surligne\">".$result[0]."</span>";
				$tab_a_remplacer[] = "|!".$i."!|";			
			}
		}
	}
	return str_replace($tab_a_remplacer, $tab_remplacement, $phrase);
}

function a_le_ville($ville){
	if(substr($ville,0,4)=="LES ")
		$ville = "aux ".substr($ville,4,strlen($ville));
	else if(substr($ville,0,3)=="LE ")
		$ville = "au ".substr($ville,3,strlen($ville));
	else
		$ville = "à ".$ville;
	return $ville;
}

function contenu_colonne_droite($chaine){ //Chaine correspond soit à un groupe don|publicite(x)|commentaire|signaler|tag|contenu_bloc[x] soit au nom d'un groupe "pré-rempli" ci-dessous
	global $root_site;
	$groupe_param = array(	"liste"=>"tag|commentaire|publicite(3)",
							"fiche"=>"contenu_bloc[1]|signaler|publicite(2)",
							"recherche"=>"contenu_bloc[1]|commentaire|publicite(2)",
							"espace_perso"=>"contenu_bloc[4]",
							"autres_pages"=>"contenu_bloc[1]|commentaire");
	if(strpos($chaine,"|")===false&&isset($groupe_param[$chaine]))
		$param = explode("|",$groupe_param[$chaine]);
	else
		$param = explode("|",$chaine);
	$reg_publicite = "#publicite\(([0-9]+)\)#i";
	$reg_contenu_bloc = "#contenu_bloc\[([0-9]+)\]#i";
	$return = '';
	for($i=0;$i<count($param);$i++){
		//$return .= '<div>';
			if($param[$i]=="tag"){
				$return .= get_bloc_thematique();
			}
			else if($param[$i]=="commentaire"){
				$return .= get_bloc_commentaire();
			}
			/*else if($param[$i]=="don"){
				$return .= get_bloc_don();
				$return .= '<div id="faire_un_don">';
					$return .= '<a href="faire_un_don.html"><img alt="Faire un don" src="'.$root_site.'img/faire_un_don.png" /></a>';
				$return .= '</div>';
			}
			else if($param[$i]=="signaler"){
				$return .= get_bloc_signaler();
			}*/
			else if(preg_match($reg_publicite,$param[$i],$nb_publicite)){
				$return .= get_bloc_publicite($nb_publicite[1]);
			}
			else if(preg_match($reg_contenu_bloc,$param[$i],$no_contenu_bloc)){
				$return .= get_contenu_bloc($no_contenu_bloc[1]);
			}
		//$return .= '</div>';
	}
	/*for($param as $cle=>$valeur){
		if($valeur){
		
		}
	}*/
	return $return;
}
function get_contenu_bloc($no){
	if(empty($no)) $no=1;
        $territoire = 1;
        if (isset($_SESSION["utilisateur"]["territoire"])) {
            $territoire = $_SESSION["utilisateur"]["territoire"];
        }
	$requete = "SELECT titre, contenu FROM contenu_blocs WHERE ref=:no AND etat=1 AND territoires_id = ".$territoire;
	$tab_bloc = execute_requete($requete,array(":no"=>$no));
	if(count($tab_bloc)>0){
		return '<div class="bloc_colonne_droite"><h3>'.$tab_bloc[0]["titre"].'</h3><p>'.$tab_bloc[0]["contenu"].'</p></div>';
	}
	else{
            $requete = "SELECT titre, contenu FROM contenu_blocs WHERE no=:no AND etat=1";
            $tab_bloc = execute_requete($requete,array(":no"=>$no));
            if(count($tab_bloc)>0){
		return '<div class="bloc_colonne_droite"><h3>'.$tab_bloc[0]["titre"].'</h3><p>'.$tab_bloc[0]["contenu"].'</p></div>';
            }
            else {
		return "";
            }
	}
}
function get_bloc_signaler(){
	return '<div class="bloc_colonne_droite"><h3>signaler</h3><p>Le contenu de cette page parait indésirable?<br /><input type="button" value="Signaler cette page" /></p></div>';
}
function get_bloc_don(){
	return '<div class="bloc_colonne_droite"><h3>Faire un don</h3><p>Vous souhaitez soutenir le projet ensemble-ici?<br /><input type="button" value="Faire un don" /></p></div>';
}
function get_bloc_thematique(){
	global $VIE;
	global $LISTE_TAGS;
	global $NOM_VILLE_URL;
	global $ID_VILLE;
	global $PAGE_COURANTE;
	global $DU;
	global $DU_URL;
	global $TRI;
	global $DISTANCE;
	//Calcul du prefixe et suffixe des urls en fonction des paramètres globaux
	$url_sans_tag = $NOM_VILLE_URL.'.'.$ID_VILLE.'.'.$PAGE_COURANTE;
	$prefixe_url = $url_sans_tag.'.tag';
	$suffixe_url = '';
	if($DISTANCE!=0)
		$suffixe_url .= ".".(($DISTANCE>0)?$DISTANCE."km":"tous");
	if(!empty($DU))
		$suffixe_url .= ".du-".$DU_URL;
	if(!empty($TRI))
		$suffixe_url .= '.'.$TRI;
	$suffixe_url .= '.html';
	$url_sans_tag .= $suffixe_url;
	
	$les_vies = get_vies();
	//$LISTE_TAGS = "31,1355304057,17";
	$les_tags = get_tags($VIE,$LISTE_TAGS,"",true);
	if(!empty($LISTE_TAGS))
		$les_tags_select = get_tags_depuis_liste($LISTE_TAGS,true);
	else
		$les_tags_select = array();
	$return = '<div id="boite_tag_publique" class="vie-toute">';
		$return .= '<div class="libelle">Thématiques</div>';
		$return .= '<select onchange="set_vie(this);">';
			$return .= '<option value="vie-toute_0">Toutes les vies</option>';
		for($i_vie=0;$i_vie<count($les_vies);$i_vie++){
			$return .= '<option value="'.url_rewrite($les_vies[$i_vie]["libelle"]).'_'.$les_vies[$i_vie]["no"].'">'.$les_vies[$i_vie]["libelle"].'</option>';
		}
		$return .= '</select>';
		$return .= '<div>';
			$return .= '<input type="text" class="recherche_tag" title="rechercher un tag" />';
			//$return .= '<input type="button" class="recherche_tag" />';
		$return .= '</div>';
		//On affiche les tags sélectionnés par l'utilisateur
		$return .= '<div id="liste_tag_select">';
			if(count($les_tags_select)==1){
				$return .= '<a href="'.$url_sans_tag.'" class="un_tag '.$les_tags_select[0]["class"].'" id="tag_'.$les_tags_select[0]["no"].'">'.$les_tags_select[0]["titre"].'</a>';
			}
			else{
				for($i_tag=0;$i_tag<count($les_tags_select);$i_tag++){
					//$return .= '<div onclick="tag_click(this);" class="un_tag '.$les_tags_select[$i_tag]["class"].'" id="tag_'.$les_tags_select[$i_tag]["no"].'">'.$les_tags_select[$i_tag]["titre"].'</div>';
					$return .= '<a href="'.$prefixe_url.retirer_liste(str_replace(',','-',$LISTE_TAGS),$les_tags_select[$i_tag]["no"],"-").$suffixe_url.'" class="un_tag '.$les_tags_select[$i_tag]["class"].'" onclick="tag_click_public(this);" id="tag_'.$les_tags_select[$i_tag]["no"].'">'.$les_tags_select[$i_tag]["titre"].'</a>';
				}
			}
		$return .= '</div>';
		
		$return .= '<div id="liste_tag">';
			//On affiche les tags disponibles selon la recherche, la vie, et non sélectionnés par l'utilisateur
		for($i_tag=0;$i_tag<count($les_tags);$i_tag++){
			//$return .= '<div onclick="tag_click(this);" class="un_tag '.$les_tags[$i_tag]["class"].'" id="tag_'.$les_tags[$i_tag]["no"].'">'.$les_tags[$i_tag]["titre"].'</div>';
			$return .= '<a href="'.$prefixe_url.ajouter_liste(str_replace(',','-',$LISTE_TAGS),$les_tags[$i_tag]["no"],"-").$suffixe_url.'" onclick="tag_click_public(this);" class="un_tag '.$les_tags[$i_tag]["class"].'" id="tag_'.$les_tags[$i_tag]["no"].'">'.$les_tags[$i_tag]["titre"].'</a>';
		}
		$return .= '</div>';
	$return .= "</div>";
	return $return;
}
function get_bloc_publicite($nb){
	require_once("fonctions_rss.php"); //Pour les publicités utilisant les flux rss de partenaires
        $territoire = 1;
        if (isset($_SESSION["utilisateur"]["territoire"])) {
            $territoire = $_SESSION["utilisateur"]["territoire"];
        }
	$requete = "SELECT no, titre, contenu, url_image, site FROM publicites WHERE etat=1 AND type = 1 AND validite_du<=NOW() AND validite_au>=NOW() AND territoires_id = ".$territoire." ORDER BY RAND() LIMIT ".$nb;
	$tab_pub = execute_requete($requete);
	$return = '';
	for($i=0;$i<count($tab_pub);$i++){
		$return .= '<div class="bloc_colonne_droite">';
			$return .= '<a href="'.$tab_pub[$i]["site"].'">';
				$return .= '<h4>'.(($tab_pub[$i]["no"]==4)?'Actualité du ':'').$tab_pub[$i]["titre"].'</h4>';
				$return .= '<img src="'.$tab_pub[$i]["url_image"].'" />';
				if(!empty($tab_pub[$i]["contenu"]))
					$return .= '<p>'.$tab_pub[$i]["contenu"].'</p>';
			$return .= '</a>';
			if($tab_pub[$i]["no"]==4)
				$return .= RSS_Display("http://www.ceder-provence.fr/spip.php?page=backend", 3);
		$return .= '</div>';
	}
	return $return;
}

function get_bloc_commentaire(){
    $territoire = 1;
    if (isset($_SESSION["utilisateur"]["territoire"])) {
        $territoire = $_SESSION["utilisateur"]["territoire"];
    }
    $requete = "SELECT contenu FROM contenu_blocs WHERE ref = 23 AND territoires_id = ".$territoire;
    $tab_com = execute_requete($requete);
    $return = '<div class="bloc_colonne_droite">';
        $return .= $tab_com[0]['contenu'];
//            $return .= '<p>Le site « Ensemble ici » est un projet associatif, évolutif et collaboratif. Si vous rencontrez des problèmes techniques sur le site ou souhaitez nous soumettre vos idées pour faciliter l’utilisation des services, cliquez ici pour nous envoyer vos commentaires ou corrections.</p><p>Nous traiterons votre message dans les meilleurs délais.</p><p style="text-align:right">Le collectif « Ensemble ici »</p>';
//            $return .= '<a href="faire-un-don.html"><img alt="Faire un don" src="'.$root_site.'img/faire_un_don.png" /></a>';
    $return .= '</div>';
    return $return;


}


function date_fr_precise($d1,$d2=""){
	$tab_jour = array("lundi","mardi","mercredi","jeudi","vendredi","samedi","dimanche");
	$tab_mois = array("janvier","fevrier","mars","avril","mai","juin","juillet","aout","septembre","octobre","novembre","décembre");
	
	$_d1 = explode(" ",$d1);
	$d1 = $_d1[0];
		$time1 = strtotime($d1);
	$date1 = $tab_jour[(((int)date("N",$time1))-1)].' '.((date("j",$time1)!=1)?date("j",$time1):'1<sup>er</sup>').' '.$tab_mois[(((int)date("n",$time1))-1)].' '.date('Y',$time1);
	if(count($_d1)>1&&!empty($_d1[1])){ //Il y a les heures
		$h1 = $_d1[1];
		$_h1 = explode(":",$h1);
		$heure1 = 'à '.$_h1[0].'h'.$_h1[1];
	}
	
	if(!empty($d2)){
		$_d2 = explode(" ",$d2);
		$d2 = $_d2[0];
			$time2 = strtotime($d2);
		$date2 = $tab_jour[(((int)date("N",$time2))-1)].' '.((date("j",$time2)!=1)?date("j",$time2):str_replace('1','1<sup>er</sup>',date("j",$time2))).' '.$tab_mois[(((int)date("n",$time2))-1)].' '.date('Y',$time2);
		if(count($_d2)>1&&!empty($_d2[1])){ //Il y a les heures
			$h2 = $_d2[1];
			$_h2 = explode(":",$h2);
			$heure2 = 'à '.$_h2[0].'h'.$_h2[1];
		}
		
		return 'Du '.$date1.((!empty($heure1))?' '.$heure1:'').'<br />au '.$date2.((!empty($heure2))?' '.$heure2:'');
	}
	else{
		return $date1.((!empty($heure1))?'<br />'.$heure1:'');
	}
}

function ajouter_liste($l,$v,$s=','){
	$_l = explode($s,$l);
	if(!dans_tab($_l,$v)){
		return (strlen($l)>1)?($l.$s.$v):$v;
	}
	else
		return $l;
}
function retirer_liste($l,$v,$s=','){
	$_l = explode($s,$l);
	$_l = retirer_tab($_l,$v);
	return implode($s,$_l);
}
function dans_tab($t,$v){
	$i=0;
	while($i<count($t)&&$t[$i]!=$v){$i++;}
	return ($i<count($t));
}
function retirer_tab($t,$v){
	$i=0;
	while($i<count($t)&&$t[$i]!=$v){$i++;}
	if($i<count($t)){
		for($j=$i;$j<count($t)-1;$j++){$t[$j]=$t[$j+1];}
		array_pop($t);
		return $t;
	}
	else
		return $t;
}

function formate_telephone($num,$separateur=" "){
	$reg_numero = "#[^0-9]#";
	$num = preg_replace($reg_numero,"",$num);
	$indicatif_pays = (substr($num,0,1)!=0);//Indicatif + 33, etc.
	if($indicatif_pays){
		$indicatif = substr($num,0,3);
		$num = substr($num,3,strlen($num));
	}
	//On coupe  num tous les deux caractères
	$num_return = "";
	for($a=0;$a<strlen($num);$a++){
		if($a%2==0)
			$num_return .= $separateur;
		$num_return .= $num[$a];
	}
	if($indicatif_pays)
		$num_return = "+".$indicatif.$num_return;
	else
		$num_return = substr($num_return,1,strlen($num_return));
	return $num_return;
}
function debug($var){
    $debug = debug_backtrace(); 
    echo '<p>&nbsp;</p><p><a href="#" onclick="$(this).parent().next(\'ol\').slideToggle(); return false;"><strong>'.$debug[0]['file'].' </strong> l.'.$debug[0]['line'].'</a></p>'; 
    echo '<ol style="display:none;">'; 
    foreach($debug as $k=>$v){ if($k>0){
            echo '<li><strong>'.$v['file'].' </strong> l.'.$v['line'].'</li>'; 
    }}
    echo '</ol>'; 
    echo '<pre>';
    print_r($var);
    echo '</pre>'; 
}
?>
