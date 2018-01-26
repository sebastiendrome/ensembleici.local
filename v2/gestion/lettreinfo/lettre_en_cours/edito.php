<?php
if(!$previsualisation_validation)
	include('../../01_include/_connect.php');
	
//On r�cup�re la liste deqs �v�nements pour no_lettre
$requete_liste = "SELECT * FROM lettreinfo_edito WHERE no_lettre=:no_l";
$res_liste = $connexion->prepare($requete_liste);
$res_liste->execute(array(":no_l"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_liste);
$tab_liste = $res_liste->fetchAll();
$corps = $tab_liste[0]["corps"];
$mention_permanente = (bool)$tab_liste[0]["mention_permanente"];
if($mention_permanente){
    $requete = "SELECT territoires_id FROM lettreinfo WHERE no=".$no_lettre;
    $res_requete = $connexion->prepare($requete);
    $res_requete->execute() or die("erreur requête ligne 116 : ".$requete);
    $tabter = $res_requete->fetch();
    $territoire = $tabter['territoires_id'];
    
    $avant = (bool)$tab_liste[0]["avant"];
    $requete_mention = "SELECT corps FROM lettreinfo_edito WHERE no_lettre=0 AND territoires_id = ".$territoire;
    $res_mention = $connexion->prepare($requete_mention);
    $res_mention->execute() or die ("requete ligne 19 : ".$requete_mention);
    $tab_mention = $res_mention->fetchAll();
    $mention_permanente = $tab_mention[0]["corps"];
}
?>
<img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-22.jpg" width="650px" height="57" alt="Cette semaine" style="padding-top:20px;padding-bottom:10px;width:650px;" id="edito" />
<table style="width:100%;background-color:white;" border="0" cellpadding="20" cellspacing="0">
	<tr>
		<td>
			<?php
			if($mention_permanente!=false&&$avant){
				echo "<p>".$mention_permanente."</p><hr style='margin:10px 0;border:0;border-top:1px solid #E3D6C7;'/>";
			}
			?>
			<?php echo $corps; ?>
			<?php
			if($mention_permanente!=false&&!$avant){
				echo "<hr style='margin:10px 0;border:0;border-top:1px solid #E3D6C7;'/><p>".$mention_permanente."</p>";
			}
			?>
		</td>
	</tr>
</table>
<br/>
