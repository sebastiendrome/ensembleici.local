<?php
// Appelé pour filtres de l'affichage du repertoire
require_once ('01_include/_connect.php');
require_once ('01_include/_var_ensemble.php');

if ($_POST['url'])
    $lien_filtre_tri = $_POST['url'];
?>
<script type="text/javascript" src="js/fonction_auto_presentation.js"></script>
<script type="text/javascript">
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
		    	document.getElementById("resultats").innerHTML = "<img  src='img/image-loader.gif' alt='load' />";
		    }
		};
		//on appelle le fichier .php en lui envoyant les variables en get
		xhr.open("GET", "tag_sous_tag.php?keyword="+keyword, true);
		xhr.send(null);
	}
	return false;
}

function list_results(xhr, keyword)
{
	var contents = ""; //contenu retour
	var contentstable = ""; //contenu tableau
	var contentscoche = ""; //contenu case cocher tout
	var des_res = 0;
	//récupération des noeuds XML
	var docXML= xhr.responseXML;
	var items = docXML.getElementsByTagName("item");
	// decompilation des resultats XML
	contents += "<br/><table border='0' cellpadding='3' cellspacing='3' id='cases'>";
	for (i=0;i<items.length;i++)
	{
		des_res = 1;
		if(i%3==0 || i==0)
		{
			if(i>0)
			{
				contentstable += "</tr>";
			}
			contentstable += "<tr>";
		}
		//récupération des données
		notemp = items[i].getElementsByTagName('no')[0];
		no = notemp.childNodes[0];
		titretemp = items[i].getElementsByTagName('titre')[0];
		titre = titretemp.childNodes[0];
		contentstable += "<td><input name='sous_tag[]' value='"+no.nodeValue+"' type='checkbox' /> "+titre.nodeValue.charAt(0).toUpperCase() + titre.nodeValue.substring(1).toLowerCase()+"<br/></td>";
	}
	if (contentstable!="") {
		contents += contentstable;
		contentscoche = "<br/><input type='checkbox' name='cocheTout' id='cocheTout'/> <label for='cocheTout' id='cocheText'>Tout cocher</span>";
	} else {
		contents += "<tr><td>Aucune thématique.</td></tr>";
	}
	contents += "</table>" + contentscoche;
	document.getElementById("resultats").innerHTML = contents;

}
</script>
<?php
	// Récup tags
	$sql_tag="SELECT * FROM tag ORDER BY titre";
	$res_tag = $connexion->prepare($sql_tag);
	$res_tag->execute() or die ("Erreur 109 : ".$sql_tag);
	$tab_tag=$res_tag->fetchAll();
?>

<form name="ESstagsStructs" id="ESstagsStructs" action="<?php echo $lien_filtre_tri; ?>" method="post" accept-charset="UTF-8">
<select name="form_vie" id="form_vie" onChange="return vie_tag(this.value)">
		<option value="" selected>Choisissez une famille d'activité</option>
		<?php
			for($indice_tag=0; $indice_tag<count($tab_tag); $indice_tag++)
			{
				echo "<option value=\"".$tab_tag[$indice_tag]['no']."\">".$tab_tag[$indice_tag]['titre']."</option>";
			}
		?>
	</select>
	<br/>
	<div id="resultats"></div>
	<center><input type="submit" id="submit" class="boutonbleu ico-fleche" value="Filtrer"></center>
</form>
