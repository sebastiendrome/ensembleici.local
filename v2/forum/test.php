<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_header.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
$HEADER_HTML = curl_exec($ch);
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_footer.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
$FOOTER_HTML = curl_exec($ch);
curl_close($ch);

echo $HEADER_HTML;
echo "ICI LE CORPS DU MESSAGE";
echo $FOOTER_HTML;
?>