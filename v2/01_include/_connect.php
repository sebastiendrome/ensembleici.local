<?php
    // http://srv776.sd-france.net/phpmyadmin/
    try
    {
//        $connexion = new PDO('mysql:host=sql1;dbname=db_ensemble','db_ensemble', 'UgMwZ0EeR8ythmCL'); 
//        $connexion = new PDO('mysql:host=localhost;dbname=db_ensemble','root', 'sahune');   
        $connexion = new PDO('mysql:host=localhost;dbname=ensemble','ensemble', 'acxjsczCl');
       	$connexion->exec("SET CHARACTER SET utf8");
       	$connexion->exec("SET NAMES utf8");
    }
    catch(Exception $e)
    {
        echo 'Erreur : '.$e->getMessage().'<br />';
        echo 'N� : '.$e->getCode();
    }
?>