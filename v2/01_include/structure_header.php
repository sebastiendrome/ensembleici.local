<?php
require_once ('01_include/_var_ensemble.php');
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="fr"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="fr"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="fr"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="fr"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Ensemble ici | <?php echo $titre_page;?></title>
  <meta name="description" content="<?php echo $meta_description; ?>">
  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Handlee' type='text/css'>
  <link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="css/colorbox.css">
  <link rel="stylesheet" href="css/formulaires.css">
  <link rel="stylesheet" href="css/jquery-ui-1.8.21.custom.css" type="text/css" />
  <link rel="shortcut icon" href="favicon.ico" >
  <script type="text/javascript">
  /* pour ie */
  document.createElement('header');
  document.createElement('nav');
  document.createElement('article');
  document.createElement('section');
  document.createElement('footer');
  </script>
  
  <script src="js/_javascript.js"></script>
  
  <script src="js/jquery.1.8.3.min.js"></script>
  <script src="js/jquery-ui.1.9.2.min.js"></script>

  <!-- Lightbox -->
  <script type="text/javascript" src="js/jquery.colorbox-min.js"></script>
  <!-- Infobulle -->
  <script type="text/javascript" src="js/jquery.poshytip.min.js"></script>
  <script type="text/javascript" src="js/commun.js"></script>
	<!-- Jquery TimePicker -->
	<script type="text/javascript" src="js/timepicker.js"></script>
	<link rel="stylesheet" href="css/csstimepicker.css"> 
  <!-- Specifs -->
  <?php echo $ajout_header;
  if ($diaporama) {
    ?>
    <!-- Diaporama -->
    <script type="text/javascript" src="js/jquery.cycle.lite.js"></script>
    <script type="text/javascript">
    $(function(){
     // Diaporama
     $('#diaporama').cycle({ delay:  1200, speed:  800 });
    });
    </script>
    <?php
  }
  ?>
  <script>
  /* $(function() {
    $( "#tabs" ).tabs({
      beforeLoad: function( event, ui ) {
        ui.jqXHR.error(function() {
          ui.panel.html(
            "Cette page est en cours de construction..." );
        });
      }
    });
  });*/ 
  </script>
</head>
<body onscroll="scrolling();">
  <header id="hautpage" class="wrapper">
    <div id="bandeau">
      <div id="diaporama">
    <?php // diaporama sur page d'accueil
    if ($diaporama)
    {
     ?>
     <img src="img/diapo-index/diapo-11.jpg" alt="Ensemble ici" width="693" height="183" />
     <img src="img/diapo-index/diapo-12.jpg" alt="Ensemble ici" width="693" height="183" />
     <img src="img/diapo-index/diapo-13.jpg" alt="Ensemble ici" width="693" height="183" />
     <img src="img/diapo-index/diapo-14.jpg" alt="Ensemble ici" width="693" height="183" />
     <img src="img/diapo-index/diapo-15.jpg" alt="Ensemble ici" width="693" height="183" />
     <?php
   }
   else
   {
     ?>
     <img src="img/diapo-index/diapo-1<?php echo rand(1,5); ?>.jpg" alt="Ensemble ici" width="693" height="183" />
     <?php
   }
   ?>
 </div>
 <div id="slogan">
  <img src="img/bandeau-slogan.png" alt="Tous acteurs de la vie locale" width="438" height="34" />
</div>
<div id="cache">
  <img src="img/bandeau-cache.png" alt="Ensemble ici" width="322" height="183" />
</div>
<div id="copyright">
  <a href="http://davidsimonphotographies.wordpress.com/" title="David Simon Photographe" target="_blank">&copy; David Simon</a>
</div>
</div>
<div id="title">
  <a href="<?php echo $root_site; ?>" title="Retour à l'accueil">
    <img src="img/logo-ensembleici.png" alt="Ensemble ici" width="187" height="149" />
  </a>
</div>
<div id="choisissez-vie">
  <img src="img/choisissez-votre-vie.png" alt="Choisissez votre vie !" width="73" height="70" />
</div>
<?php
      // le nom de la ville selectionnée
if ((!empty($id_ville))&&(!$titre_ville_url))
{
 require_once ('01_include/_connect.php');
 $sql_ville="SELECT nom_ville_maj,nom_ville_url FROM villes WHERE id = :idville";
 $res_ville = $connexion->prepare($sql_ville);
 $res_ville->execute(array(':idville'=>$id_ville));
 $tab_ville = $res_ville->fetch(PDO::FETCH_ASSOC);
 $titre_ville = $tab_ville["nom_ville_maj"];
 $titre_ville_url = $tab_ville["nom_ville_url"];
}
?>
<nav>
  <ul id="mainmenu">
   <li class="gris home">
    <a href="<?php
    echo $root_site;
      // Lien vers la ville
    if ((!empty($id_ville))&&(!empty($titre_ville_url)))
    {
     echo $titre_ville_url.".".$id_ville.".html";
   }
   ?>" title="Retour à l'accueil" class="infobulle-b">
   <div class="lien">
    <img src="img/icone-home.png" alt="Retour à l'accueil" />
  </div>
</a>
        </li><!--
        <?php
  // Données du menu (modifiables, dans l'ordre d'affichage)
  $liens_couleurs = array( "rouge", "bleu", "vert", "pomme", "orange" );
  $liens_ids = array( 1,3,2,4,5 );
  $liens_noms = array( "pratique", "associative", "professionnelle", "culturelle", "touristique" );
  // Génère les liens du menu
  for ($i = 0; $i <= 4; $i++) {
    // Vie active ?
    if ($id_vie == $liens_ids[$i])
    {
       $class_vie = " actif";
    }
    else
    {
       $class_vie = "";
    }
    echo "--><li class=\"".$liens_couleurs[$i].$class_vie."\">
      <a href=\"";
      if (!empty($id_ville))
      {
        // Liens vers la vie
        echo $titre_ville_url.".".url_rewrite("vie ".$liens_noms[$i]).".".$id_ville.".$liens_ids[$i].html";
      }
      else
      {
        // Liens vers l'accueil
        echo $root_site;
      }
      echo "\" title=\"Vie ".$liens_noms[$i];
      if($titre_ville) echo " à ".$titre_ville;
      echo "\">
        <span class=\"vie\">Vie</span> ".$liens_noms[$i]."
      </a>
    </li>";
    if($i != 4) echo "<!--"; // Sauf sur le denier
  }
  ?>
      </ul>
    </nav>
  </header>
  <div id="barressmenu" class="wrapper">
    <h1<?php if ($page_ville) echo " id=\"h1ville\"";?>>
  <?php
    if (!empty($titre_page_bleu))
    {
      echo $titre_page_bleu;
    }
    else
    {
      echo $titre_page;
    }
  ?>
  </h1>
    <?php
    if ($page_ville)
      echo '<a href="ville-changer.html" title="Modifier la commune" class="boutonbleu ico-map">Modifier</a>';
    ?>
    <div id="inscription_lettre">
      <a href="lettreinfo_inscription.html" title="Inscrivez-vous à la lettre d'info">
       <img src="img/lettreinfo-inscription.png" alt="Inscrivez-vous à la lettre d'info" width="142" height="29" />
      </a>
    </div>
    <div id="recherche">
      <form method="post" action="recherche.php" id="form-recherche">
        <input maxlength="100" type="text" class="champ" value="Recherche" id="chaine" name="mot" />
      </form>
    </div>
  </div>
  <div id="contenu" class="wrapper" role="main">
    <!-- Menu de gauche -->
    <div id="colonne1">
      <ul>
       <?php
    // Utilisateur connecté ? Verif simple, pour données non privées
       if (isset($_SESSION['UserConnecte']))
         $label_EP = "Mon espace personnel";
       else
         $label_EP = "Identifiez-vous";
    // Lien Agenda et Repertoire globaux
    /*   

      <li><a href="<?php echo $lien_rep_glo; ?>" title="Tout le répertoire" class="rapide infobulle rd-repertoire">Tout le répertoire</a></li>
      <li><a href="<?php echo $lien_age_glo; ?>" title="Tout l’agenda" class="rapide infobulle rd-agenda">Tout l’agenda</a></li>

    if ((!empty($id_ville))&&(!empty($titre_ville_url)))
       {
         $lien_age_glo = $root_site.$titre_ville_url.".".$id_ville.".tout.agenda.html";
         $lien_rep_glo = $root_site.$titre_ville_url.".".$id_ville.".tout.repertoire.html";
       }
       else
       {
         $lien_rep_glo = $lien_age_glo = $root_site."index.html";
       } */
       ?>
       <li><a href="espace_personnel.html" title="<?php echo $label_EP; ?>" class="rapide infobulle rd-login"><?php echo $label_EP; ?></a></li>
       <li><a href="espace_personnel.html" title="Ajouter ou modifier une information" class="rapide infobulle rd-add">Ajouter ou modifier une information</a></li>
      <li><a href="6.animations.html" title="Animations" class="rapide infobulle rd-animations">Animations</a></li>
     </ul>
     <ul class="second">
       <li><a href="5.vie_du_projet.html" title="Vie du Projet" class="rapide infobulle rd-vieprojet">Vie du Projet</a></li>
       <li><a href="guide_utilisation_du_site.html" title="Guide d'utilisation" class="rapide infobulle rd-guide dernier">Guide d'utilisation</a></li>
      <li><a href="http://www.facebook.com/ensembleici" title="Page Facebook" class="rapide infobulle rd-facebook dernier"target="_blank">Suivez nous sur notre page Facebook</a></li>
    </ul>
    <div id="ajoutez-votre-info">
     <img src="img/ajoutez-votre-info.png" alt="Ajoutez votre info" width="73" height="70" />
   </div>
 </div>
