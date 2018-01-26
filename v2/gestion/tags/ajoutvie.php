<?php
/*****************************************************
Ajout d'une association tag / vie
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
?>

<h3>Associer une vie</h3>
<script type="text/javascript">
	$(function() { 
		$('#submit').click(function() {
			$('#form-ajout-bis').hide(0);
			var formData = $('form#form-ajout-bis').serialize();
			$.ajax({
				type : 'POST',
				url : 'doajoutvie.php',
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
</script>

<?php
	//recuperation des vies
	$sql_vie="SELECT * FROM vie ORDER BY libelle";
	$res_vie = $connexion->prepare($sql_vie);
	$res_vie->execute() or die ("requete ligne 15 : ".$sql_vie);
	$tab_vie=$res_vie->fetchAll();

	$id_tag = intval($_SESSION['id_tag_passer']);
	// unset($_SESSION['id_tag_passer']);
?>

<form name="EDconnexion" id="form-ajout-bis" action="" method="post" class="formA" accept-charset="UTF-8">
<fieldset>
	<label>Choisissez une vie : </label>
	<select name="form_vie" id="form_vie">
		<?php
			for($indice_vie=0; $indice_vie<count($tab_vie); $indice_vie++)
			{
				echo "<option value=\"".$tab_vie[$indice_vie]['no']."\">".$tab_vie[$indice_vie]['libelle']."</option>";
			}
		?>
	</select>
	<input type="hidden" value="<?php echo $id_tag ?>" name="id_tag">
	
	<br/><br/>
	<center><a href="" id="submit" class="boutonbleu ico-fleche">Associer</a></center>
</fieldset>
</form>

