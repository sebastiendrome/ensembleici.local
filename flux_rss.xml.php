<?php
header('Content-Type: application/rss+xml; charset=UTF-8');
//1. Récupération des variables principales et indispensables
include "01_include/_var_ensemble.php";
//2. Récupération des fonctions utiles ou indispensables
include "01_include/_fonctions.php";
//3. Récupération des 15 derniers événements
$tab_rss = extraire_liste("evenement",15,1,array("rss"=>1));
$tab_rss = $tab_rss["liste"];

$return =  '<?xml version="1.0" encoding="UTF-8"?>';
$return .=  '<rss version="2.0">';
	$return .= '<channel>';
		$return .= '<title>Ensemble ici, tous acteurs de la vie locale</title>';
		$return .= '<link>http://www.ensembleici.fr</link>';
		$return .= '<description>Tous les évènements à deux pas de chez vous</description>';
		for($i=0;$i<count($tab_rss);$i++){
			$return .= '<item>';
				$return .= '<title>'.$tab_rss[$i]["ville"].' : '.$tab_rss[$i]["titre"].'</title>';
				//$return .= '<description>'.$tab_rss[$i]["date_du_au"].' : '.html_entity_decode(strip_tags($tab_rss[$i]["description"]),ENT_QUOTES,'UTF-8').'</description>';
				$return .= '<description>'.$tab_rss[$i]["date_du_au"].'</description>';
				$return .= '<author></author>';
				$return .= '<link>'.$root_site.'agenda.'.url_rewrite($tab_rss[$i]["ville"]).'.'.url_rewrite($tab_rss[$i]["titre"]).'.'.$tab_rss[$i]["no_ville"].'.'.$tab_rss[$i]["no"].'.html</link>';
			$return .= '</item>';
		}
	$return .= '</channel>';
$return .= '</rss>';

$return = str_replace("\r","\n",$return);
$return = str_replace("\n\n","\n",$return);
$return = str_replace("\n"," ",$return);
$return = trim($return);
echo $return;
?>
