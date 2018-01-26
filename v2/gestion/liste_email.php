<?php
include '../_connect.php';
$tab_email=array('robert.gleize@wanadoo.fr','philippe.altier@libertysurf.fr','brigitteaudibert@wanadoo.fr','raymard@ladrome.fr','olivier@africultures.com','claudine.martin26@orange.fr','fabien.begnis@orange.fr','bernard.yan@laposte.net','mi.billaud@laposte.net','georges.bontemps@laposte.net','gbravais@sfr.fr','bruasnath@orange.fr','vinceburnett@orange.fr','xianjeannot@gmail.com','chatquilouche@free.fr','sudtrad@orange.fr','christinecrozel@orange.fr','asautepage@voila.fr','dominique.wyns@wanadoo.fr','ne.pimprenelle@orange.fr','elisatabet@hotmail.fr','carlos.miranda3@wanadoo.fr','marie1fernandez@yahoo.fr','e_garreau@hotmail.com','marie-noelle.gemonet824@orange.fr','brigitte.gleize@club-internet.fr','patrickgrezat@orange.fr','hammond.nl@orange.fr','guy.castelly26@orange.fr','betty.gruere@gmail.com','rameljl@free.fr','marieclaudejoly@wanadoo.fr','claude.attanasio@wanadoo.fr','val.eric26@orange.fr','auberge30pas@wanadoo.fr','catherine950.martel@laposte.net','vlecarnet@orange.fr','loubiere.fernande@orange.fr','vandam.traduction@wanadoo.fr','gite.la.ramagne@gmail.com','roland.oli@orange.fr','catherine.parratte@nordnet.fr','f.michoulier@free.fr','thierry.paolazzi@wanadoo.fr','pauline.mohair@hotmail.fr','roger.pasturel@wanadoo.fr','chloerigal@hotmail.com','eleonore.r@hotmail.fr','lucienne.serain@orange.fr','serge.pauthe@orange.fr','vgaudtourre@orange.fr','sylvietremblay.drome@nordnet.fr');

$nb_insertion=0;

for($indice_email=0;$indice_email<count($tab_email);$indice_email++)
{
	$sql="SELECT * FROM utilisateur WHERE email=:email";
	$res = $connexion->prepare($sql);
	$res->execute(array(":email"=>$tab_email[$indice_email])) or die ("requete ligne 45 : ".$sql); 
	$tab_utilisateur = $res->fetchAll();
	
	if(count($tab_utilisateur)==0)
	{
		$sql="SELECT * FROM newsletter WHERE email=:email";
		$res = $connexion->prepare($sql);
		$res->execute(array(":email"=>$tab_email[$indice_email])) or die ("requete ligne 45 : ".$sql); 
		$tab_newsletter = $res->fetchAll();
		
		if(count($tab_newsletter)==0)
		{
			//on insere dans newsletter
			$sql="INSERT INTO newsletter (email, no_ville, etat) VALUES (:email, :no_ville, :etat)";
			$res = $connexion->prepare($sql);
			$res->execute(array(":email"=>$tab_email[$indice_email],":no_ville"=>9585,":etat"=>1)) or die ("requete ligne 45 : ".$sql); 
			$tab_newsletter = $res->fetchAll();
			
			echo "nouvelle insertion : ".$tab_email[$indice_email]."<br>";
			$nb_insertion++;
		}
	}
}
echo "Total : ".$nb_insertion;

?>