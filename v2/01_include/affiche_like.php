<!-- bouton j'aime-- -->
<?php
//on compte combien de visiteur ont aimé l'occurence
$sql_compte = "SELECT nb_aime FROM ".type_objet." WHERE no=:no_occurence";
$res_compte = $connexion->prepare($sql_compte);
$res_compte->execute(array(':no_occurence'=>$t_evts["no"])) or die ("Erreur ".__LINE__." : ".$sql_compte);;
while($lignes=$res_compte->fetch(PDO::FETCH_OBJ))
{
	//on prend le nombre de like de l'occurence
	$nb_like = $lignes->nb_aime;
}
			
if($nb_like > 0)
{
	//Si au moins une personne aime, on affiche le nombre de like
	echo "<a title=\"Aimer cet &eacute;v&eacute;nement\" name=\"".$nb_like."\" class=\"ico-like-list infobulle-b\" id=\"".type_objet."\" rel=\"".$t_evts["no"]."\"><sup><em>".$nb_like."</em></sup></a>";
}
else
{
	//sinon, on affiche seulement le pouce
	echo "<a title=\"Aimer cet &eacute;v&eacute;nement\" name=\"".$nb_like."\" class=\"ico-like-list infobulle-b\" id=\"".type_objet."\" rel=\"".$t_evts["no"]."\"></a>";
}
?>
            
<script type="text/javascript">	    
	//on lance les requettes d'ajout like
	$(".ico-like-list").click(function(){
		var param = 'id_recu=';
		param += $(this).attr('rel');
		param += '&type=';
		param += $(this).attr("id");
		param += '&nb_like=';
		param += $(this).attr("name");
		$(this).load('01_include/ajout_like.php',param);
		$(this).addClass('desactive');
	});
</script>
<!-- Fin bouton j'aime-- -->