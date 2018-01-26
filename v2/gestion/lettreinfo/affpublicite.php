<?php
session_name("EspacePerso");
session_start();
require_once('config.php');
$no_lettre = $_POST["id"];

//On récupère la liste deqs événements pour no_lettre

$requete_publicite = "SELECT publicites.no,publicites.url_image,publicites.titre,publicites.site,lettreinfo_publicite.position FROM publicites JOIN lettreinfo_publicite ON lettreinfo_publicite.no_publicite=publicites.no WHERE lettreinfo_publicite.no_lettre=:no AND publicites.etat=1 ORDER BY lettreinfo_publicite.position";
$res_publicite = $connexion->prepare($requete_publicite);
$res_publicite->execute(array(":no"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_publicite);
$liste_publicite = $res_publicite->fetchAll();
$indice_publicite = 0;
?>
<div style="background-color:white;width:650px;border:1px solid #E3D6C7;margin:auto;position:relative;">
	<img src="<?php echo $root_site;?>/gestion/lettreinfo/img/lettreinfo_entete.png" width="650" height="194" alt="en-tete" style="padding-top:0px;padding-bottom:20px;" onmouseover="affiche_btn_pub(1);" onmouseout="masquer_btn_pub(1);" />
	<?php
		//Si cette publicite existe, on l'insère
		if($indice_publicite<count($liste_publicite)&&$liste_publicite[$indice_publicite]["position"]==1){
			$pub_1_titre = $liste_publicite[$indice_publicite]["titre"];
			$pub_1_url = $liste_publicite[$indice_publicite]["site"];
			$pub_1_img = $liste_publicite[$indice_publicite]["url_image"];
			$indice_publicite++;
			$pub_1 = true;
		}
		else{
			$pub_1 = false;
		}
	?>
	<img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-22.jpg" width="650" height="57" alt="cette semaine" style="padding-top:20px;padding-bottom:10px;" onmouseover="affiche_btn_pub(1,2);" onmouseout="masquer_btn_pub(1,2);" />
	<?php
		//Si cette publicite existe, on l'insère
		if($indice_publicite<count($liste_publicite)&&$liste_publicite[$indice_publicite]["position"]==2){
			$pub_2_titre = $liste_publicite[$indice_publicite]["titre"];
			$pub_2_url = $liste_publicite[$indice_publicite]["site"];
			$pub_2_img = $liste_publicite[$indice_publicite]["url_image"];
			$indice_publicite++;
			$pub_2 = true;
		}
		else{
			$pub_2 = false;
		}
	?>
	<img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-24.jpg" width="650" height="57" alt="Evenements" style="padding-top:20px;padding-bottom:10px;" onmouseover="affiche_btn_pub(2,3);" onmouseout="masquer_btn_pub(2,3);" />
	<?php
		//Si cette publicite existe, on l'insère
		if($indice_publicite<count($liste_publicite)&&$liste_publicite[$indice_publicite]["position"]==3){
			$pub_3_titre = $liste_publicite[$indice_publicite]["titre"];
			$pub_3_url = $liste_publicite[$indice_publicite]["site"];
			$pub_3_img = $liste_publicite[$indice_publicite]["url_image"];
			$indice_publicite++;
			$pub_3 = true;
		}
		else{
			$pub_3 = false;
		}
	?>
	<img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-28.jpg" width="650" height="57" alt="Repertoire" style="padding-top:20px;padding-bottom:10px;" onmouseover="affiche_btn_pub(3,4);" onmouseout="masquer_btn_pub(3,4);" />
	<?php
		//Si cette publicite existe, on l'insère
		if($indice_publicite<count($liste_publicite)&&$liste_publicite[$indice_publicite]["position"]==4){
			$pub_4_titre = $liste_publicite[$indice_publicite]["titre"];
			$pub_4_url = $liste_publicite[$indice_publicite]["site"];
			$pub_4_img = $liste_publicite[$indice_publicite]["url_image"];
			$indice_publicite++;
			$pub_4 = true;
		}
		else{
			$pub_4 = false;
		}
	?>
	<img src="<?php echo $root_site;?>img/lettreinfo/lettreinfo-30.jpg" width="650" height="57" alt="Petites annonces" style="padding-top:20px;padding-bottom:10px;" onmouseover="affiche_btn_pub(4);" onmouseout="masquer_btn_pub(4);" />
	<?php
	if(!$pub_1){
	?>
		<div style="position:absolute;top:200px;background-image:url('img/btn_ajout_pub.png');width:100%;height:20px;cursor:pointer;display:none;" id="pub_1" class="ajout_pub" onmouseover="btn_pub_over(1);" onmouseout="btn_pub_out(1);"></div>
	<?php
	}
	else{
	?>
		<div style="position:absolute;top:200px;background-image:url('img/btn_ajout_pub_fin.png');width:100%;height:20px;cursor:pointer;color:white;font-weight:bold;text-align:center;font-size:16px;" id="pub_1" class="ajout_pub" onmouseover="btn_pub_over(1);" onmouseout="btn_pub_out(1);"><?php echo $pub_1_titre; ?></div>
		<div style="position:absolute;width:100%;height:auto;top:0px;left:0px;background-color:white;display:none;border:1px solid #E3D6C7;overflow:hidden;background-image:url('<?php echo $root_site.$pub_1_img; ?>');background-position:center;background-repeat:no-repeat;box-shadow: 0px 0px 25px #aaa;" id="pub_1_info" onmouseout="masque_rendu(1)" onmouseover="if(!PUBLICITE_OVER['pub_1']) PUBLICITE_OVER['pub_1']=true;"><img class="img_pub" src="<?php echo $root_site.$pub_1_img; ?>" /><img style="cursor:pointer;margin:10px;" src="img/ico_delete.png" onmouseover="ico_over()" onmouseout="ico_out()" onclick="supprimer_publicite(1,this.parentNode);" /><img style="cursor:pointer;margin:10px;" src="img/ico_modif.png" onmouseover="ico_over()" onmouseout="ico_out()" class="modif_pub" id="modifpub_1" /></div>
	<?php
	}
	if(!$pub_2){
	?>
		<div style="position:absolute;top:297px;background-image:url('img/btn_ajout_pub.png');width:100%;height:20px;cursor:pointer;display:none;" id="pub_2" class="ajout_pub" onmouseover="btn_pub_over(2);" onmouseout="btn_pub_out(2);"></div>
	<?php
	}
	else{
	?>
		<div style="position:absolute;top:297px;background-image:url('img/btn_ajout_pub_fin.png');width:100%;height:20px;cursor:pointer;color:white;font-weight:bold;text-align:center;font-size:16px;" id="pub_2" class="ajout_pub" onmouseover="btn_pub_over(2);" onmouseout="btn_pub_out(2);"><?php echo $pub_2_titre; ?></div>
		<div style="position:absolute;width:100%;height:auto;top:0px;left:0px;background-color:white;display:none;border:1px solid #E3D6C7;overflow:hidden;background-image:url('<?php echo $root_site.$pub_2_img; ?>');background-position:center;background-repeat:no-repeat;box-shadow: 0px 0px 25px #aaa;" id="pub_2_info" onmouseout="masque_rendu(2)" onmouseover="if(!PUBLICITE_OVER['pub_2']) PUBLICITE_OVER['pub_2']=true;"><img class="img_pub" src="<?php echo $root_site.$pub_2_img; ?>" /><img style="cursor:pointer;margin:10px;" src="img/ico_delete.png" onmouseover="ico_over()" onmouseout="ico_out()" onclick="supprimer_publicite(2,this.parentNode);" /><img style="cursor:pointer;margin:10px;" src="img/ico_modif.png" onmouseover="ico_over()" onmouseout="ico_out()" class="modif_pub" id="modifpub_2" /></div>
	<?php
	}
	if(!$pub_3){
	?>
		<div style="position:absolute;top:389px;background-image:url('img/btn_ajout_pub.png');width:100%;height:20px;cursor:pointer;display:none;" id="pub_3" class="ajout_pub" onmouseover="btn_pub_over(3);" onmouseout="btn_pub_out(3);"></div>
	<?php
	}
	else{
	?>
		<div style="position:absolute;top:389px;background-image:url('img/btn_ajout_pub_fin.png');width:100%;height:20px;cursor:pointer;color:white;font-weight:bold;text-align:center;font-size:16px;" id="pub_3" class="ajout_pub" onmouseover="btn_pub_over(3);" onmouseout="btn_pub_out(3);"><?php echo $pub_3_titre; ?></div>
		<div style="position:absolute;width:100%;height:auto;top:0px;left:0px;background-color:white;display:none;border:1px solid #E3D6C7;overflow:hidden;background-image:url('<?php echo $root_site.$pub_3_img; ?>');background-position:center;background-repeat:no-repeat;box-shadow: 0px 0px 25px #aaa;" id="pub_3_info" onmouseout="masque_rendu(3)" onmouseover="if(!PUBLICITE_OVER['pub_3']) PUBLICITE_OVER['pub_3']=true;"><img class="img_pub" src="<?php echo $root_site.$pub_3_img; ?>" /><img style="cursor:pointer;margin:10px;" src="img/ico_delete.png" onmouseover="ico_over()" onmouseout="ico_out()" onclick="supprimer_publicite(3,this.parentNode);" /><img style="cursor:pointer;margin:10px;" src="img/ico_modif.png" onmouseover="ico_over()" onmouseout="ico_out()" class="modif_pub" id="modifpub_3" /></div>
	<?php
	}
	if(!$pub_4){
	?>
		<div style="position:absolute;top:481px;background-image:url('img/btn_ajout_pub.png');width:100%;height:20px;cursor:pointer;display:none;" id="pub_4" class="ajout_pub" onmouseover="btn_pub_over(4);" onmouseout="btn_pub_out(4);"></div>
	<?php
	}
	else{
	?>
		<div style="position:absolute;top:481px;background-image:url('img/btn_ajout_pub_fin.png');width:100%;height:20px;cursor:pointer;color:white;font-weight:bold;text-align:center;font-size:16px;" id="pub_4" class="ajout_pub" onmouseover="btn_pub_over(4);" onmouseout="btn_pub_out(4);"><?php echo $pub_4_titre; ?></div>
		<div style="position:absolute;width:100%;height:auto;top:0px;left:0px;background-color:white;display:none;border:1px solid #E3D6C7;overflow:hidden;background-image:url('<?php echo $root_site.$pub_4_img; ?>');background-position:center;background-repeat:no-repeat;box-shadow: 0px 0px 25px #aaa;" id="pub_4_info" onmouseout="masque_rendu(4)" onmouseover="if(!PUBLICITE_OVER['pub_4']) PUBLICITE_OVER['pub_4']=true;"><img class="img_pub" src="<?php echo $root_site.$pub_4_img; ?>" /><img style="cursor:pointer;margin:10px;" src="img/ico_delete.png" onmouseover="ico_over()" onmouseout="ico_out()" onclick="supprimer_publicite(4,this.parentNode);" /><img style="cursor:pointer;margin:10px;" src="img/ico_modif.png" onmouseover="ico_over()" onmouseout="ico_out()" class="modif_pub" id="modifpub_4" /></div>
	<?php
	}
	?>
</div>
<script type="text/javascript">
LES_DIV_PUBLICITES_OVER = {"pub_1":false,"pub_2":false,"pub_3":false,"pub_4":false};
LES_BTN_PUBLICITES_OVER = {"pub_1":false,"pub_2":false,"pub_3":false,"pub_4":false};
LES_PUBLICITES_COMPLETE = {"pub_1":<?php if($pub_1) echo "true"; else echo "false"; ?>,"pub_2":<?php if($pub_2) echo "true"; else echo "false"; ?>,"pub_3":<?php if($pub_3) echo "true"; else echo "false"; ?>,"pub_4":<?php if($pub_4) echo "true"; else echo "false"; ?>};
ICONE_OVER = false;
PUBLICITE_OVER = {"pub_1":false,"pub_2":false,"pub_3":false,"pub_4":false};
function affiche_btn_pub(num1,num2){
	if(!LES_PUBLICITES_COMPLETE["pub_"+num1]){
		if(document.getElementById("pub_"+num1).style.display!="block")
			document.getElementById("pub_"+num1).style.display = "block";
		LES_DIV_PUBLICITES_OVER["pub_"+num1] = true;
	}
	// if(typeof(num2)!="undefined"&&document.getElementById("pub_"+num2).style.display!="block"){
	if(typeof(num2)!="undefined"){
		if(!LES_PUBLICITES_COMPLETE["pub_"+num2]){
			if(document.getElementById("pub_"+num2).style.display!="block")
				document.getElementById("pub_"+num2).style.display = "block";
			LES_DIV_PUBLICITES_OVER["pub_"+num2] = true;
		}
	}
}

function masquer_btn_pub(num1,num2,retirer){
	if(typeof(retirer)=="undefined"){
		if(typeof(num2)!="undefined")
			LES_DIV_PUBLICITES_OVER["pub_"+num2] = false;
		else
			num2 = false;
		LES_DIV_PUBLICITES_OVER["pub_"+num1] = false;
		setTimeout("masquer_btn_pub("+num1+","+num2+",true)",300);
	}
	else{
		if(document.getElementById("pub_"+num1).style.display!="none"&&!LES_DIV_PUBLICITES_OVER["pub_"+num1]&&!LES_BTN_PUBLICITES_OVER["pub_"+num1]&&!LES_PUBLICITES_COMPLETE["pub_"+num1])
			document.getElementById("pub_"+num1).style.display = "none";
		if(num2!=0&&document.getElementById("pub_"+num2).style.display!="none"&&!LES_DIV_PUBLICITES_OVER["pub_"+num2]&&!LES_BTN_PUBLICITES_OVER["pub_"+num2]&&!LES_PUBLICITES_COMPLETE["pub_"+num2])
			document.getElementById("pub_"+num2).style.display = "none";
	}
}

function btn_pub_over(n){
	LES_BTN_PUBLICITES_OVER["pub_"+n] = true;
	if(LES_PUBLICITES_COMPLETE["pub_"+n])
		affiche_rendu(n);
}

function affiche_rendu(n){
	//La publicité existe dans la lettre, on affiche son rendu.
	var div = document.getElementById("pub_"+n+"_info");
		div.style.display = "block";
	if(div.firstChild.getAttribute("class")=="img_pub"){
		var ligne = document.getElementById("pub_"+n);
		div.style.height = div.firstChild.offsetHeight+30+"px";
		div.removeChild(div.firstChild);
		div.style.top = ligne.offsetTop-div.offsetHeight/2+"px";
	}
}
	function ico_over(){
		ICONE_OVER = true;
	}
	function ico_out(){
		ICONE_OVER = false;
	}

function masque_rendu(n,r){
	if(typeof(r)!="undefined"){ //La souris quitte le rendu, on le masque
		if(!ICONE_OVER&&!PUBLICITE_OVER["pub_"+n])
			document.getElementById("pub_"+n+"_info").style.display = "none";
	}
	else{
		PUBLICITE_OVER["pub_"+n] = false;
		setTimeout("masque_rendu("+n+",true)",100);
	}
}

function btn_pub_out(n){
	LES_BTN_PUBLICITES_OVER["pub_"+n] = false;
}

function supprimer_publicite(n,div){
	//On supprime dans la bdd la publicité de la position n pour no_lettre
	var xhr = getXhr();
		xhr.open("POST", "ajax/del_pub.php", false);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xhr.send("no_lettre=<?php echo $no_lettre; ?>&pos="+n);
	var reponse = eval("("+xhr.responseText+")");
	if(reponse){
		//On modifie l'affichage (et les variables d'affichage)
		LES_PUBLICITES_COMPLETE["pub_"+n] = false;
		document.getElementById("pub_"+n).style.backgroundImage = "url('img/btn_ajout_pub.png')";
		vide(document.getElementById("pub_"+n));
		//On supprime le div de visualisation
		div.parentNode.removeChild(div);
	}
	else{
		alert("une erreur est survenue ! Veuillez réessayer ...");
	}
}

$(".ajout_pub").live('click', function(){
	$.fn.colorbox({
	  href:"ajoutpublicite.php?no_lettre=<?php echo $no_lettre; ?>&pos="+this.id.split("_")[1],
	  width:"650px",
	  onClosed:function(){
		  $("#zone_publicite").load("affpublicite.php", {id:<?php echo $no_lettre; ?>});
		  $(".message").load("../inc-message.php");
	  },
	  onComplete : function() { 
			$(this).colorbox.resize();
	  }
	});
	return false; 
});

$(".modif_pub").live('click', function(){
	$.fn.colorbox({
	  href:"ajoutpublicite.php?no_lettre=<?php echo $no_lettre; ?>&pos="+this.id.split("_")[1]+"&existe=1",
	  width:"650px",
	  onClosed:function(){
		  $("#zone_publicite").load("affpublicite.php", {id:<?php echo $no_lettre; ?>});
		  $(".message").load("../inc-message.php");
	  },
	  onComplete : function() { 
			$(this).colorbox.resize();
	  }
	});
	return false; 
});
</script>