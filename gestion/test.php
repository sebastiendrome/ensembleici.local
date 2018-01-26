<?php
$url = "www.ensembleici.fr";
//$url = "http://www.ensembleici.fr";
if(substr($url,0,7)!="http://"&&substr($url,0,8)!="https://")
	$url = "http://".$url;
//$url = "test";
var_dump(filter_var($url, FILTER_VALIDATE_URL));
if(filter_var($url, FILTER_VALIDATE_URL)!=false){
	print_r(get_headers($url));
}
else
	echo "url invalide";
?>