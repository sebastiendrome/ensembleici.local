<?php
if(!$previsualisation_validation)
	include_once('../../01_include/_connect.php');

$requete_collectif = "SELECT * FROM collectifs WHERE territoires_id=:t";
$res_collectif = $connexion->prepare($requete_collectif);
$res_collectif->execute(array(":t"=>$lettre_territoire)) or die ("requete ligne 19 : ".$requete_collectif);
$tab_collectif = $res_collectif->fetchAll();

if (sizeof($tab_collectif) != 0) { ?>
    <table style="width:100%;background-color:white;padding:20px 0;margin-top:40px;border-top: 1px solid #E3D6C7;" id="edito" border="0" cellpadding="0" cellspacing="0">
	<tr><td coldspan=5 style="font-size:16px;font-weight:bold;color:#E75B54;padding-left:20px;">Le collectif "Ensemble ici" :<br/></td></tr>
    <?php
    foreach ($tab_collectif as $k => $v) { ?>
        <td>
            <a href="<?php echo $v['url']; ?>" title="<?php echo $v['libelle']; ?>">
			<img src="http://www.ensembleici.fr/img/lettreinfo/<?php echo $v['image']; ?>" alt="<?php echo $v['libelle']; ?>" />
            </a>
        </td>
    <?php }
?>
    </tr>
<!--    <table style="width:100%;background-color:white;padding:20px 0;margin-top:40px;border-top: 1px solid #E3D6C7;" id="edito" border="0" cellpadding="0" cellspacing="0">
	<tr><td coldspan=5 style="font-size:16px;font-weight:bold;color:#E75B54;padding-left:20px;">Le collectif "Ensemble ici" :<br/></td></tr>
	<tr>
		<td><a href="http://www.tamtamdesbaronnies.com/" title="Le TamTam des Baronnies">
			<img src="http://www.ensembleici.fr/img/lettreinfo/partenaire-tamtam.jpg" alt="Le TamTam des Baronnies" />
		</a></td>
		<td><a href="http://www.africultures.com/" title="Africultures">
			<img src="http://www.ensembleici.fr/img/lettreinfo/partenaire-africultures.jpg" alt="Africultures" />
		</a></td>
		<td><a href="http://www.decor-asso.fr/" title="Association DECOR">
			<img src="http://www.ensembleici.fr/img/lettreinfo/partenaire-decor.jpg" alt="Association DECOR" />
		</a></td>
		<td><a href="http://mscurnier.canalblog.com/" title="Association pour l'animation sociale du Haut-nyonsais">
			<img src="http://www.ensembleici.fr/img/lettreinfo/partenaire-htnyonsais.jpg" alt="Association pour l'animation sociale du Haut-nyonsais" />
		</a></td>
	</tr>-->
<?
}

/*
On r�cup�re les partenaires � afficher dans la lettre.
*/
$requete_partenaires_lettre = "SELECT * FROM lettreinfo_partenaireinstitutionnel WHERE no_lettre=:no";
$res_partenaires_lettre = $connexion->prepare($requete_partenaires_lettre);
$res_partenaires_lettre->execute(array(":no"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_partenaires_lettre);
$tab_partenaires_lettre = $res_partenaires_lettre->fetchAll();
$liste_partenaires_lettre = $tab_partenaires_lettre[0]["liste"];

if($liste_partenaires_lettre!=""){
	$requete_partenaires = "SELECT * FROM partenaireinstitutionnel WHERE no IN (".$liste_partenaires_lettre.")";
	$res_partenaires = $connexion->prepare($requete_partenaires);
	$res_partenaires->execute() or die ("requete ligne 19 : ".$requete_partenaires);
	$tab_partenaires = $res_partenaires->fetchAll();

	//A surveiller (pas sur du r�sultat avec plus d'entr�es)
	$nb_partenaires = count($tab_partenaires);
	
	echo "<tr><td coldspan=5 style=\"font-size:16px;font-weight:bold;color:#E75B54;padding-left:20px;\">Les partenaires :<br/></td></tr>";

	for($i=0;$i<$nb_partenaires;$i++){
		if($i%5==0)
			echo "<tr>";
	?>
		<td style="text-align:center;">
		<?php
			if($tab_partenaires[$i]["libelle"]!="")
				$img = '<img src="http://www.ensembleici.fr/'.$tab_partenaires[$i]["image"].'" style="max-width:100px;" alt="'.$tab_partenaires[$i]["libelle"].'" title="'.$tab_partenaires[$i]["libelle"].'" />';
			else
				$img = '<img src="http://www.ensembleici.fr/'.$tab_partenaires[$i]["image"].'" style="max-width:100px;" />';
			if($tab_partenaires[$i]["url"]!="")
				echo '<a href="'.$tab_partenaires[$i]["url"].'" target="_blank">'.$img.'</a>';
			else
				echo $img;
		?>
		</td>
	<?php
		if($i%5==4)
			echo "</tr>";
	}
	if($nb_partenaires%5!=0){
		for($j=$nb_partenaires%5;$j<5;$j++){
			echo "<td></td>";
		}
		echo "</tr>";
	}
	?>
</table>
<?php
}
?>