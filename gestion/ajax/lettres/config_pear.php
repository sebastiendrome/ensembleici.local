 <?php
set_include_path(get_include_path() . ":/home/ensemble/01_include/PEAR");
require_once('Mail/Queue.php');
// Options  pour stocker les messages
// Le type, c'est le container utilis�, 
// pour le moment on peut choisir entre 'creole', 'db', 'mdb' et 'mdb2'
$db_options['type']       = 'db';
// the others are the options for the used container
// here are some for db
$db_options['dsn']        = 'mysql://ensemble:acxjsczCl@localhost/ensemble';
$db_options['mail_table'] = 'mail_queue';


$mail_options['driver']   = 'smtp';
$mail_options['host']     = 'localhost';
$mail_options['port']     = 8025;
$mail_options['auth']     = false;
$mail_options['username'] = 'contact@envolinfo.com';
$mail_options['password'] = 'sahune';
?>
