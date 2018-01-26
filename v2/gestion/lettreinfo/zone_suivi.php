<label for="adresse_suivi">Adresse de suivi&nbsp;:&nbsp;</label><input type="text" id="adresse_suivi" />
<br/>
<input type="radio" id="mail_true" name="mail" checked="checked" /><label for="mail_true">&nbsp;m'envoyer un mail de suivi toutes les&nbsp;</label><select style="display:inline;"><option>2</option><option>5</option><option selected="selected">10</option><option>30</option><option>60</option></select><b>&nbsp;minutes.</b>
<br/>
<input type="radio" id="mail_false" name="mail" /><label for="mail_false">&nbsp;ne pas m'envoyer de mail de suivi.</label>
<br/>
<br/>
<input type="checkbox" checked="checked" id="envoi_fin" /><label for="envoi_fin">&nbsp;m'envoyer un mail lorsque l'envoi s'est termin&eacute;.</label>
<br/>
<br/>
<input type="button" value="continuer" onclick="ouvrir_field('field_envoi');" />