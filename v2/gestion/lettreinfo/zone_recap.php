<?php
//On récupère les informations sur la lettre d'information
?>
<table style="width:100%;">
<tr>
<td><label for="objet">Objet&nbsp;:&nbsp;</label><span><?php echo $objet; ?></span></td>
<td rowspan="2"><input type="button" value="Visualiser" onclick="ouvrir_lettre();" /></td>
</tr>
<tr>
<td><label for="date">Dates&nbsp;:&nbsp;</label><span><?php echo $dates; ?></span></td>
</tr>
</table>
<br/>
<br/>
<input type="button" value="continuer" onclick="<?php if($repertoire==0) echo "ouvrir_field('field_liste');"; ?>" />