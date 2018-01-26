<?php
require_once("01_include/fonctions_rss.php");
echo "<div id=\"bloc_rss\">";
echo RSS_Display("http://www.ceder-provence.fr/spip.php?page=backend", 15);
echo "</div>";
?>
