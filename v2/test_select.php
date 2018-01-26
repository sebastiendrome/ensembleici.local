<?php
	require('01_include/_connect.php');
	
	$sql_sous_tag="SELECT * FROM sous_tag ORDER BY titre";
	$res_sous_tag = $connexion->prepare($sql_sous_tag);
	$res_sous_tag->execute() or die ("requete ligne 97 : ".$sql_sous_tag);
	$tab_sous_tag=$res_sous_tag->fetchAll();	
	
	$sql_tag="SELECT * FROM tag ORDER BY titre";
	$res_tag = $connexion->prepare($sql_tag);
	$res_tag->execute() or die ("requete ligne 97 : ".$sql_tag);
	$tab_tag=$res_tag->fetchAll();
		
	$sql_vie="SELECT * FROM vie ORDER BY libelle";
	$res_vie = $connexion->prepare($sql_vie);
	$res_vie->execute() or die ("requete ligne 97 : ".$sql_vie);
	$tab_vie=$res_vie->fetchAll();

?>
<script type="text/javascript">
function coche_all_tag (nb_tag)
{
	for (i=0;i<(nb_tag+1);i++)
	{
		document.activite.tag[i].checked=true;
	}
}


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
		xhr.open("GET", "vie_tag.php?keyword="+keyword, true);
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
	contents += "<table border='0' cellpadding='5' cellspacing='5'>";
	for (i=0;i<items.length;i++)
	{	
		if(i%2==0 || i==0)
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
}
</script>
<form name="activite" action="valide_test.php" method="post">
	<select name="form_vie" id="form_vie" onChange="return vie_tag(this.value)">
		<option value="0" selected>Choisissez vos vies</option>
		<?php
			for($indice_vie=0; $indice_vie<count($tab_vie); $indice_vie++)
			{
				echo "<option value=\"".$tab_vie[$indice_vie]['no']."\">".$tab_vie[$indice_vie]['libelle']."</option>";
			}
		?>
	</select>
	<br/>
	<div id="resultats"></div>
	<br/>	
	<input type="submit" name="Enregistrer" value="Enregistrer">
</form>