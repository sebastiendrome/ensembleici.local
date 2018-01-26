<?php
/*****************************************************
Gestion des associations tags // sstags
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
?>

    <h3>Associer à un tag</h3>
    
<script type="text/javascript" src="../../js/fonction_auto_presentation.js"></script>
<script type="text/javascript">
$(function() { 
	$('#submit').click(function() {
		$('#form-ajout-tag').hide(0);
		var formData = $('form#form-ajout-tag').serialize();
		$.ajax({
			type : 'POST',
			url : 'doajouttag.php',
			dataType : 'json',
			data: formData,
			success : function(data){
				// Fermer la colorbox
  				parent.jQuery.fn.colorbox.close();
			},
			error:function (xhr, ajaxOptions, thrownError){
                alert(xhr.status);
                alert(thrownError);
            }
		});
		return false;
	});
});

function vie_tag(keyword)
{
	if (keyword == "0") alert('Vous devez choisir une activité valide');
	else
	{		
		var xhr = null;    
		if (window.XMLHttpRequest) 
		{ 
		    xhr = new XMLHttpRequest();
		}
		else if (window.ActiveXObject) 
		{
		    xhr = new ActiveXObject("Microsoft.XMLHTTP");
		}
		//on définit l'appel de la fonction au retour serveur
		xhr.onreadystatechange = function() 
		{ 
		    if(xhr.readyState == 4 && xhr.status == 200)
		    {
		    	list_results(xhr, keyword);
		    }
		    
		    else 
		    {	
		    	document.getElementById("resultats").innerHTML = "<img  src='../../img/image-loader.gif' alt='load' />";
		    }
		};
		//on appelle le fichier .php en lui envoyant les variables en get
		xhr.open("GET", "../../vie_tag.php?keyword="+keyword, true);
		xhr.send(null);
	}
	return false;
}

function list_results(xhr, keyword)
{
	var contents = ""; //contenu retour
	//récupération des noeuds XML
	var docXML= xhr.responseXML;
	var items = docXML.getElementsByTagName("item");
	// decompilation des resultats XML
	contents += "<table border='0' cellpadding='3' cellspacing='3'>";
	for (i=0;i<items.length;i++)
	{	
		if(i%3==0 || i==0)
		{
			if(i>0)
			{
				contents += "</tr>";
			}
			contents += "<tr>";
		}
		//récupération des données
		notemp = items[i].getElementsByTagName('no')[0];
		no = notemp.childNodes[0];
		titretemp = items[i].getElementsByTagName('titre')[0];
		titre = titretemp.childNodes[0];
		contents += "<td><input name='tag[]' value='"+no.nodeValue+"' type='checkbox' /> "+titre.nodeValue+"<br/></td>";
	}
	
	contents += "</table>";
	document.getElementById("resultats").innerHTML = contents;

	// Redimmensionne la box
	$('#ajouttag').colorbox.resize();

}
</script>
<?php
	//recuperation des vies
	$sql_vie="SELECT * FROM vie ORDER BY libelle";
	$res_vie = $connexion->prepare($sql_vie);
	$res_vie->execute() or die ("requete ligne 97 : ".$sql_vie);
	$tab_vie=$res_vie->fetchAll();

	$id_sstag = intval($_SESSION['id_sstag_passer']);

?>

<form name="EDconnexion" id="form-ajout-tag" action="" method="post" class="formA" accept-charset="UTF-8" onSubmit="return verif_evenement_etape2()">
<fieldset>
	<select name="form_vie" id="form_vie" onChange="return vie_tag(this.value)">
		<option value="" selected>Choisissez une thématique d'activité</option>
		<?php
			for($indice_vie=0; $indice_vie<count($tab_vie); $indice_vie++)
			{
				echo "<option value=\"".$tab_vie[$indice_vie]['no']."\">".$tab_vie[$indice_vie]['libelle']."</option>";
			}
		?>
	</select>
	<input type="hidden" value="<?php echo $id_sstag ?>" name="id_sstag">
	
	<br/>
	<div id="resultats"></div>
	<br/><br/>
	<center><a href="" id="submit" class="boutonbleu ico-fleche">Ajouter</a></center>
</fieldset>
</form>

