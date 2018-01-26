<?php
function seconde_to_string($s){
	if($s>60){
		$nb_s = $s%60;
		$m = floor($s/60);
		if($m>60){
			$nb_m = $m%60;
			$h = floor($m/60);
			if($h>24){
				$nb_h = $h%24;
				$j = floor($h/24);
				return $j."j ".$nb_h."h ".$nb_m."m ".$nb_s."s";
			}
			else{
				return $h."h ".$nb_m."m ".$nb_s."s";
			}
		}
		else{
			return $m."m ".$nb_s."s";
		}
	}
	else{
		return $s."s";
	}
}
echo seconde_to_string(3664);
?>