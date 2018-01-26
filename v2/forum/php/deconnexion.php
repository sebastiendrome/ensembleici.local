<?php
session_name("EspacePerso");
session_start();
session_unset ();
session_destroy ();
header("Location:".$_POST['redirec']);
?>