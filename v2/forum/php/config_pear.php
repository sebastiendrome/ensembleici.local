 <?php
//set_include_path(get_include_path() . ":/home/ensemble/www/gestion/lettreinfo/PEAR");
set_include_path(get_include_path() . ":/home/ensemble/gestion/lettreinfo/PEAR");
require_once "Mail/Queue.php";
/**
Configuration
**/
// Options  pour stocker les messages
// Le type, c'est le container utilis�, 
// pour le moment on peut choisir entre 'creole', 'db', 'mdb' et 'mdb2'
$db_options['type']       = 'db';
// the others are the options for the used container
// here are some for db
//$db_options['dsn']        = 'mysql://db_ensemble:UgMwZ0EeR8ythmCL@sql1/db_ensemble';
$db_options['dsn']        = 'mysql://ensemble:acxjsczCl@localhost/ensemble';
$db_options['mail_table'] = 'mail_queue';

// Voici les options pour envoyer les messages eux-m�mes
// ce sont les options requises pour la classe mail,
// particuli�rement utilis� pour Mail::factory()
//$mail_options['driver']   = 'smtp';
//$mail_options['host']     = 'mail.africultures.com';
//$mail_options['port']     = 587;
//$mail_options['localhost'] = 'localhost'; //optional Mail_smtp parameter
//$mail_options['auth']     = false;
//$mail_options['username'] = 'newsletter@africultures.info';
//$mail_options['password'] = 'afrilettre';

$mail_options['driver']   = 'smtp';
$mail_options['host']     = 'localhost';
$mail_options['port']     = 8025;
$mail_options['auth']     = false;
$mail_options['username'] = 'contact@envolinfo.com';
$mail_options['password'] = 'sahune';
?>