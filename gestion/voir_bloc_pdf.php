<?php if ($malettre['pdf_agenda'] != '') { ?>
    <div class="col-sm-12">
        <b>Lien avec Fichier PDF agenda affiché dans la lettre :</b> Si vous le souhaitez, vous pouvez consulter <a href='http://www.ensembleici.fr/02_medias/14_lettreinfo_pdf_agenda/<?= $malettre['pdf_agenda'] ?>' target='_blank'>l'agenda complet au format PDF </a>
    </div>
    <br/><br/>
<?php } ?>

<?php if ($malettre['pdf_agenda'] != '') { ?>
    <div class="col-sm-12">
        <b>Lien avec Fichier PDF annonces affiché dans la lettre :</b> Si vous le souhaitez, vous pouvez consulter <a href='http://www.ensembleici.fr/02_medias/15_lettreinfo_pdf_annonces/<?= $malettre['pdf_annonces'] ?>' target='_blank'>la liste complète des petites annonces au format PDF</a>
    </div>
    <br/>
<?php } ?>