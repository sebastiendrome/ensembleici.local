<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="fr"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="fr"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="fr"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="fr"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Ensemble ici : Administration du site | <?php echo $TitrePage;?></title>
  <meta name="viewport" content="width=device-width">
  <meta name="robots" content="none">
  <link rel="stylesheet" href="<?php echo $root_site; ?>css/reset.css">
  <link rel="stylesheet" href="<?php echo $root_site; ?>css/style.css">
  <link rel="stylesheet" href="<?php echo $root_site; ?>css/adminstyle.css?v=<?=@filemtime('style.css')?>" type="text/css" media="screen" />
  <link rel="stylesheet" href="<?php echo $root_site; ?>css/colorbox.css">
  <link rel="stylesheet" href="<?php echo $root_site; ?>css/formulaires.css">
  <link rel="shortcut icon" href="<?php echo $root_site; ?>favicon.ico" >
  <script type="text/javascript">
    /* pour ie */
    document.createElement('header');
    document.createElement('nav');
    document.createElement('article');
    document.createElement('section');
    document.createElement('footer');
  </script>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php echo $root_site; ?>js/jquery-1.7.1.min.js"><\/script>')</script>
  <!-- Lightbox -->
  <script type="text/javascript" src="<?php echo $root_site; ?>js/jquery.colorbox-min.js"></script>
  <script type="text/javascript">
    $(function() {
	  $(".agrandir").colorbox({
      });
    });
  </script>		
  <!-- Jquery UI -->
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="<?php echo $root_site; ?>css/jquery-ui-1.8.21.custom.css" type="text/css" />
  <!-- Jquery TimePicker -->
	<script type="text/javascript" src="<?php echo $root_site; ?>js/timepicker.js"></script>
	<link rel="stylesheet" href="<?php echo $root_site; ?>css/csstimepicker.css">  
  <!-- Specifs -->
  <?php echo $ajout_header; ?>
</head>
<body>


<div id="ad-marge">
<div id="ad">
	<div id="ad-header">
		<img src="<?php echo $root_site; ?>img/ensembleici-bandeau-600.jpg" alt="Ensemble ici" />
		<h1>
		Administration</h1>
		<?php if ($_SESSION['UserConnecte_email'])
		{
			echo "<p class=\"user_connecte\">Vous êtes connecté en ";
			echo '<a href="'.$root_site.'espace_personnel.html" title="Modifier espace perso">';
			echo $_SESSION['UserConnecte_email']."</a></p>";
		}
		?>
	</div>
	<div id="ad-menu">
			<?php include "inc-menu.php"; ?>
	</div>
	<div id="ad-content">
	<h2>
		<a href="admin.php"><?php echo $titrecateg; ?></a>
		<?php echo ($titrepage?" > ".$titrepage:""); ?>
	</h2>
	<p class="message"><?php echo $messager; ?></p>