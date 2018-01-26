<?php
session_name("EspacePerso2");
session_start();
//On supprime le cookie de session
if(isset($_COOKIE[session_name()]))
	setcookie(session_name(),"",time()-3600,"/");
//On vide la session
$_SESSION = array();
session_destroy();
?>
