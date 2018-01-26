<?php
//On récupère les header et footer html
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_header.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
$HEADER_HTML = curl_exec($ch);
curl_close($ch);
//On récupère les header et footer html
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_footer.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
$FOOTER_HTML = curl_exec($ch);
curl_close($ch);


function formater_objet($o){
	$o = html_entity_decode($o);
	$o = strip_tags($o);

	// $o = preg_replace("#\s([a-z])(\’|\')\s([a-z][a-z]+)#i", ' $1 $2', $o);
	// $o = preg_replace("#\s([a-z])\s(\’|\')\s([a-z][a-z]+)#i", ' $1 $2', $o);
	// $o = preg_replace("#\s([a-z])\s(\’|\')([a-z][a-z]+)#i", ' $1 $2', $o);
	// $o = preg_replace("#\s([a-z])\s([a-z][a-z]+)#i", ' $1 $2', $o);
	$o = str_replace("'"," ",$o);
	$o = str_replace('"',"",$o);
	
// Pour les accents (a, e, i, o, u)
	$o = preg_replace('#[ãàâä]#iu', 'a', $o); //(&a[a-z]{3,6};)
	$o = preg_replace('#[éèëê]#iu', 'e', $o); //
	$o = preg_replace('#[õòöô]#iu', 'o', $o); //(&o[a-z]{3,6};)
	$o = preg_replace('#[ìîî]#iu', 'i', $o); //(&i[a-z]{3,6};)
	$o = preg_replace('#[ùûü]#iu', 'u', $o); //(&u[a-z]{3,6};)
	$o = preg_replace('#ç#iu', 'c', $o); //(&ccedil;)
	
	$o = trim($o);
	return $o;
}

//On récupère les informations que l'on a pas sur le message et sur l'utilisateur.
$requete_pseudo = "SELECT pseudo FROM utilisateur WHERE no=:no";
$res_pseudo = $connexion->prepare($requete_pseudo);
$res_pseudo->execute(array(":no"=>$no_utilisateur));
$tab_pseudo = $res_pseudo->fetchAll();
$pseudo = $tab_pseudo[0]["pseudo"];

$requete_forum = "SELECT titre, no_forum_type, no_ville FROM forum WHERE no=:no";
$res_forum = $connexion->prepare($requete_forum);
$res_forum->execute(array(":no"=>$no_forum));
$tab_forum = $res_forum->fetchAll();

if($tab_forum[0]["no_forum_type"]==1){ //FORUM CITOYEN
	$sql_ville="SELECT * FROM villes WHERE id = :idville";
	$res_ville = $connexion->prepare($sql_ville);
	$res_ville->execute(array(':idville'=>$tab_forum[0]["no_ville"]));
	$tab_ville = $res_ville->fetch(PDO::FETCH_ASSOC);
	$titre_ville = ucfirst(strtolower($tab_ville["nom_ville_maj"]));
	$titre_sujet = "Forum citoyen de ".$titre_ville;
}
else
	$titre_sujet = $tab_forum[0]["titre"];

$contenu_message = utf8_encode(strip_tags(html_entity_decode($contenu)));
if(strlen($contenu_message)>150)
	$contenu_message_court = substr($contenu_message,0,150)." [...]";
else
	$contenu_message_court = $contenu_message;

/*
Message de base
[**pseudo**] a répondu à un message pour lequel vous avez porté de l'intérêt dans le forum [**titre**]
[**contenu**]
<a href=[]>Répondre</a>

Pour ne plus suivre ce message, cliquez <a href="[**no_forum**]&[**no_message**]">ici</a>.

*/

$url_desinscription = $root_site."desinscription.forum.".url_rewrite($titre_sujet).".".$no_forum.".".$no_message.".[**no_user**].html";
$url_message = $root_site."forum.".url_rewrite($titre_sujet).".".$no_forum.".html#message".(($commentaire)?$no_message:$no_message_poste);
$url_forum = $root_site."forum.".url_rewrite($titre_sujet).".".$no_forum.".html";



if(!$commentaire){
	$BODY_HTML = "<center>Bonjour [**pseudo_user**],<br /><b>".$pseudo."</b> vient d'&eacute;crire un nouveau message dans : <a style=\"text-decoration:none;font-weight:400;color:rgb(21,170,158);text-align:center;font-size:20px;margin-top:18px;display:block;\" href=\"".$url_forum."\">".$titre_sujet."</a>";
	$BODY_HTML .= "<p style=\"background-color:rgb(240, 237, 234);border:1px solid rgb(227, 214, 199);border-radius:5px;padding:10px;margin:10px;font-size:16px;\">".$contenu_message_court."</p><br/>";
	$BODY_HTML .= "Pour le lire ou y r&eacute;pondre, cliquez <a href=\"".$url_message."\">ici</a></center>";
	$BODY_HTML .= "<div style=\"font-size: 11px; font-style: italic;padding-left:50px;padding-right:50px;padding-top:30px;text-align:center; \">Si vous souhaitez ne plus recevoir de mail pour vous avertir des nouveaux messages ajout&eacute;s sur ce forum, merci de cliquer <a href=\"".$url_desinscription."\">ici</a></div></center>";

	$HEADER_TXT = "ensembleici.fr\r\n";
	$BODY_TXT = "Bonjour [**pseudo_user**],\r\n".$pseudo." vient d'écrire un nouveau message dans \" ".$titre_sujet." \" \r\n";
	$BODY_TXT = "<< ".$contenu_message_court." >>\r\n";
	$BODY_TXT .= "Pour le lire ou y répondre, copiez/collez cette adresse dans votre navigateur : ".$url_message."\r\n";
	$FOOTER_TXT = "\r\n Si vous souhaitez ne plus recevoir de mail pour vous avertir des nouveaux messages ajoutés sur ce forum, copiez/collez cette adresse dans votre navigateur : ".$url_desinscription;

	$OBJET = "nouveau message : ".$titre_sujet;
}
else{
	$BODY_HTML = "<center>Bonjour [**pseudo_user**],<br /><b>".$pseudo."</b> vient de commenter votre message dans : <a style=\"text-decoration:none;font-weight:bold;color:rgb(21,170,158);text-align:center;font-size:20px;margin-top:18px;display:block;\" href=\"".$url_forum."\">".$titre_sujet."</a>";
	$BODY_HTML .= "<br/>";
	$BODY_HTML .= "Pour le lire ou y r&eacute;pondre, cliquez <a href=\"".$url_message."\">ici</a></center>";
	$BODY_HTML .= "<div style=\"font-size: 11px; font-style: italic;padding-left:50px;padding-right:50px;padding-top:30px;text-align:center; \">Si vous souhaitez ne plus recevoir de mail pour vous avertir des nouveaux messages ajout&eacute;s sur ce forum, merci de cliquer <a href=\"".$url_desinscription."\">ici</a></div></center>";

	$HEADER_TXT = "ensembleici.fr\r\n";
	$BODY_TXT = "Bonjour [**pseudo_user**],\r\n".$pseudo." vient de commenter votre message dans \" ".$titre_sujet." \" \r\n";
	$BODY_TXT .= "Pour le lire ou y répondre, copiez/collez cette adresse dans votre navigateur : ".$url_message."\r\n";
	$FOOTER_TXT = "\r\n Si vous souhaitez ne plus recevoir de mail pour vous avertir des nouveaux messages ajoutés sur ce forum, copiez/collez cette adresse dans votre navigateur : ".$url_desinscription;

	$OBJET = "nouveau commentaire : ".$titre_sujet;
}
$OBJET = formater_objet($OBJET);

$CONTENU_MAIL_HTML_TEMP = $HEADER_HTML.$BODY_HTML.$FOOTER_HTML;
$CONTENU_MAIL_TXT_TEMP = $HEADER_TXT.$BODY_TXT.$FOOTER_TXT;

//On a no_forum, no_message, et no_utilisateur.
$requete_abonne = "SELECT utilisateur.email AS e, utilisateur.pseudo AS p, utilisateur.no FROM utilisateur JOIN forum_inscription ON utilisateur.no=forum_inscription.no_utilisateur WHERE forum_inscription.no_utilisateur<>:nou AND forum_inscription.no_forum=:nof AND forum_inscription.no_message=:nom AND forum_inscription.inscrit=1";
$res_abonne = $connexion->prepare($requete_abonne);
$res_abonne->execute(array(":nou"=>$no_utilisateur,":nof"=>$no_forum,":nom"=>$no_message));
$tab_abonne = $res_abonne->fetchAll();
//On envoi maintenant tout les emails.
for($i=0;$i<count($tab_abonne);$i++){
	$CONTENU_MAIL_HTML = str_replace("[**no_user**]",$tab_abonne[$i]["no"],str_replace("[**pseudo_user**]",$tab_abonne[$i]["p"],$CONTENU_MAIL_HTML_TEMP));
	$CONTENU_MAIL_TXT = str_replace("[**no_user**]",$tab_abonne[$i]["no"],str_replace("[**pseudo_user**]",$tab_abonne[$i]["p"],$CONTENU_MAIL_TXT_TEMP));
	$MAIL_DESTINATAIRE = $tab_abonne[$i]["e"];
	include "envoyer_un_mail.php";
}

include "envoyer_alerte_admin.php";

?>
