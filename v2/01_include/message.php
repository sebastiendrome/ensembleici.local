<?php
session_name("EspacePerso");
session_start();

// Affichage du message s'il existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);
echo $messager;
?>