<?php
/*****************************************************
Gestion des tags associés
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
?>

    <h3>Ajouter un sous-tag</h3>
    
<script type="text/javascript" src="../../js/fonction_auto_presentation.js"></script>
<script type="text/javascript">
	$(function() { 
		$('#submit').click(function() {
			$('#form-ajout-sstag').hide(0);
			var formData = $('form#form-ajout-sstag').serialize();
			$.ajax({
				type : 'POST',
				url : 'doajoutsstag.php',
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
		xhr.open("GET", "../../tag_sous_tag.php?keyword="+keyword, true);
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
		contents += "<td><input name='sous_tag[]' value='"+no.nodeValue+"' type='checkbox' /> "+titre.nodeValue.charAt(0).toUpperCase() + titre.nodeValue.substring(1).toLowerCase()+"<br/></td>";
	}
	
	contents += "</table>";
	document.getElementById("resultats").innerHTML = contents;

	// Redimmensionne la box
	$('#ajoutsstag').colorbox.resize();

}
</script>
<?php
	// Récup tag
	$sql_tag="SELECT * FROM tag ORDER BY titre";
	$res_tag = $connexion->prepare($sql_tag);
	$res_tag->execute() or die ("Erreur 97 : ".$sql_tag);
	$tab_tag=$res_tag->fetchAll();

	$id_structure = intval($_SESSION['id_structure_passer']);
	// unset($_SESSION['id_structure_passer']);
?>

<form name="EDconnexion" id="form-ajout-sstag" action="" method="post" class="formA" accept-charset="UTF-8">
<fieldset>
	<strong>1.</strong> <select name="form_vie" id="form_vie" onChange="return vie_tag(this.value)">
		<option value="" selected>Choisissez une thématique d'activité</option>
		<?php
			for($indice_tag=0; $indice_tag<count($tab_tag); $indice_tag++)
			{
				echo "<option value=\"".$tab_tag[$indice_tag]['no']."\">".$tab_tag[$indice_tag]['titre']."</option>";
			}
		?>
	</select> <sup>*</sup>
	<br/>
	<div id="resultats"></div>
	<br/><br/>
	<label class="labellarge">2. Description de cette activit&eacute; : <sup>(non obligatoire)</sup><br/></label><br/>
	<textarea id="description" name="description" cols="60" rows="4"><?php echo $sous_tag_description ?></textarea>
	<input type="hidden" value="<?php echo $id_structure ?>" name="id_structure">
	<br/><br/>
	<center><a href="" id="submit" class="boutonbleu ico-fleche">Ajouter</a></center>
</fieldset>
</form>

