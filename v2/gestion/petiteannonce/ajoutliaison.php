<?php
/*****************************************************
Ajout d'une liaison
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

if (isset($_GET['type_A'])) $type_A = url_rewrite($_GET['type_A']);
if (isset($_GET['no_A'])) $no_A = intval($_GET['no_A']);

if ($type_A && $no_A)
{
?>

    <h3>Ajouter une liaison</h3>
<script type="text/javascript">
$(function() {
	Tester_idliaison();
	
	$('#submit').click(function() {
		$('#form-ajout-liaison').hide(0);
		var formData = $('form#form-ajout-liaison').serialize();
		$.ajax({
			type : 'POST',
			url : 'doajoutliaison.php',
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

	$( "#nom_liaison_B" ).autocomplete({
		source: function( request, response ) {
		    $.ajax({
			url: "recherche_liaison.php",
			dataType: "json",
			data: {
			    no_A: $("#no_A").val(),
			    type_A: $("#type_A").val(),
			    type_B: $("#type_B").val(),
			    term: request.term
			},
			success: function( data ) {
			    response(data);
			}
		    });
		},
		minLength: 3,
		delay: 0,
		autoFocus: true,
		select: function( event, ui ) {
			$('#nom_liaison_B').val(ui.item.label);
			$('#no_B').val(ui.item.value);
			Tester_idliaison();
			return false;
		}
	});

	$("#nom_liaison_B").on("input", null, null, function(event){
	    $('#no_B').val("");
	    $("#liee-valide").hide();
	});
});

function Tester_idliaison()
{
	var valiv = $("#no_B").val();
	if($.trim(valiv)=="")
	{
		$("#liee-valide").hide();
		$("#nom_liaison_B").val('');
		return false;
	}
	else
	{
		$('#liee-valide').show("slow");
		return true;
	}
}

</script>
<form name="EDconnexion" id="form-ajout-liaison" action="" method="post" class="formA">
	<input type="hidden" value="<?php echo $type_A ?>" name="type_A" id="type_A">
	<input type="hidden" value="<?php echo $no_A ?>" name="no_A" id="no_A">

<fieldset>
	<ul>
		<li><label for="type_B">Type : </label>
		<select name="type_B" id="type_B">
			<option value="" selected>Type</option>
			<option value="evenement">Evènement</option>
			<option value="structure">Structure</option>
			<option value="petiteannonce">Petite annonce</option>
		</select></li>
		<li><input type="hidden" id="no_B" name="no_B" />
		<label for="nom_liaison_B">Recherche : </label>
		<input type="text" id="nom_liaison_B" name="nom_liaison_B" size="60" /> <img src="<?php echo $root_site; ?>img/tick-vert.png" alt="Liaison validée" id="liee-valide" /></li>
	</ul>

	<center><a href="" id="submit" class="boutonbleu ico-fleche">Ajouter</a></center>
</fieldset>
</form>

<?php
}
else
{
        $_SESSION['message'] .= "Type et/ou numéro source incorrect(s).";
?>
	<script type="text/javascript">
		$(function() {
			// Fermer la colorbox
			parent.jQuery.fn.colorbox.close();
		});
	</script>
<?php
}
?>