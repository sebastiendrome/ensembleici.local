                    <div class="centrer"><img src="img/bandeau-colorbox.png" width="500" alt="Bienvenue sur « Ensemble ici » !" /></div>
                    <p>Pour naviguer sur le site, nous vous invitons à choisir une commune.</p>
                    <h3>Communes les plus actives sur le site :</h3>
                    <div class="blocC">
			<div class="villes-actives">
			    <a href="nyons.9568.choix.html" title="NYONS" class="boutonbleu ico-map">NYONS</a>
			    <a href="buis-les-baronnies.9424.choix.html" title="BUIS-LES-BARONNIES" class="boutonbleu ico-map">BUIS-LES-BARONNIES</a>
			    <a href="remuzat.9608.choix.html" title="REMUZAT" class="boutonbleu ico-map">REMUZAT</a>
			</div>
		    </div>

		<h3>Rechercher une commune</h3>
                <form id="RechercheVille" name="RechercheVille" action="index.php" method="post" accept-charset="UTF-8" class="formA blocC">
			    <p class="note">Saisir les premières lettres, puis choisir la commune dans les propositions :</p>
                            <ul>
                                    <li>
                                            <label for="ville">Ville :</label>
                                            <input type="text" name="rech_ville" id="ville" />
                                    </li>
                                    <li>
                                            <label for="cp">Code postal :</label>
                                            <input type="text" name="rech_cp" id="cp" size="6"/>
                                    </li>
                            </ul>
			    <input type="hidden" name="rech_idville" id="id_ville"/>
			    <div class="boutons">
				    <input type="submit" class="boutonbleu ico-fleche" title="Choisir la commune sélectionnée" value="Choisir cette commune">
			    </div>
                </form>

                <div class="clear"></div>
		<script type="text/javascript">
		    var cache = {};
		    $("#cp, #ville, #id_ville").autocomplete({
			    source: function (request, response)
			    {
				    //Si la réponse est dans le cache
				    if (('FR' + '-' + request.term) in cache)
				    {
					    response($.map(cache['FR' + '-' + request.term], function (item)
					    {

						    return {
							    label: item.CP + ", " + item.VILLE,
							    value: function ()
							    {
								    if ($(this).attr('id') == 'cp')
								    {
									    $('#ville').val(item.VILLE);
										$('#id_ville').val(item.NO);
									    return item.CP;
								    }
								    else
								    {
									    $('#cp').val(item.CP);
										$('#id_ville').val(item.NO);
									    return item.VILLE;
								    }
							    }
						    }
					    }));
				    }
				    //Sinon -> Requete Ajax
				    else
				    {
					    var objData = {};
					    if ($(this.element).attr('id') == 'cp')
					    {
						    objData = { codePostal: request.term, pays: 'FR', maxRows: 10 };
					    }
					    else
					    {
						    objData = { ville: request.term, pays: 'FR', maxRows: 10 };
					    }
					    $.ajax({
						    url: "01_include/AutoCompletion.php",
						    dataType: "json",
						    data: objData,
						    type: 'POST',
						    success: function (data)
						    {
							    //Ajout de reponse dans le cache
							    cache[('FR' + '-' + request.term)] = data;
							    response($.map(data, function (item)
							    {

								    return {
									    label: item.CP + ", " + item.VILLE,
									    value: function ()
									    {
										    if ($(this).attr('id') == 'cp')
										    {
											    $('#ville').val(item.VILLE);
												$('#id_ville').val(item.NO);
											    return item.CP;
										    }
										    else
										    {
											    $('#cp').val(item.CP);
												$('#id_ville').val(item.NO);
												return item.VILLE;
										    }
									    }
								    }
							    }));
						    }
					    });
				    }
			    },
			    minLength: 3,
			    delay: 300
		    });
		</script>