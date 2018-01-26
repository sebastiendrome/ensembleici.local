<?php

function invertirFechas($fecha){
        $arrayfecha = explode("-",$fecha);
        return $arrayfecha[2]."-".$arrayfecha[1]."-".$arrayfecha[0];
}

function SemaineSuiv($enstring){
            $fecha = strtotime($enstring);    
            $fecha = $fecha + (7*24*60*60);
            $date = getdate($fecha);
            return $date[year]."-".$date[mon]."-".$date[mday];
}

function SemainePrec($enstring){
            $fecha = strtotime($enstring);    
            $fecha = $fecha - (7*24*60*60);
            $date = getdate($fecha);
            return $date[year]."-".$date[mon]."-".$date[mday];
}

function getMonthDays($Month, $Year){
	return date("d",mktime(0,0,0,$Month+1,0,$Year));
}
	
function diffdays($date1,$date2){
	$diff = abs(strtotime($date2) - strtotime($date1));
	$days = floor($diff / (60*60*24));
	return $days;
}
	
	
function PrimerDiaMes($mes,$anyo)
	{
		// fecha actual (por ejemplo 2005-08-03)
		$date = strtotime($anyo."-".$mes."-01");                        

		// desglosamos la fecha y obtenemos el aÃ±o, mes y dia del mes (0 hasta 31)
		$array_date = getdate($date);
		$year = $array_date["year"];
		$month = $array_date["mon"];
		$month_day = $array_date["mday"];

		// construimos una nueva fecha que empieza en el dia "1" por ejemplo 2005-08-01
		$array_date = getdate(mktime(0, 0, 0, $month, 1, $year));
		return $primerDia = $array_date["wday"];
	}
	
function ultimoDiaSemana($enstring)
    {
            $fechaentrada = strtotime($enstring);
            $arrayfecha = getdate($fechaentrada); 
                //0=domingo 6=sabado
            switch($arrayfecha['wday']){
                case 0:
                    $sumardias = 0;
                    break;
                default:
                    $sumardias = 7-$arrayfecha['wday'];
                    break;
            }
            $segundos = $sumardias * 24 * 60 * 60;
            $fechasalida = $fechaentrada + $segundos;
            $date = getdate($fechasalida);
            return $date[year]."-".$date[mon]."-".$date[mday];
    }

function primerDiaSemana($enstring){
            $fechaentrada = strtotime($enstring);
            $arrayfecha = getdate($fechaentrada); 
                //0=domingo 6=sabado
            switch($arrayfecha['wday']){
                case 0:
                    $restardias = 6;
                    break;
                default:
                    $restardias = $arrayfecha['wday']-1;
                    break;
            }
            $segundos = $restardias * 24 * 60 * 60;
            $fechasalida = $fechaentrada - $segundos;
            $date = getdate($fechasalida);
            return $date[year]."-".$date[mon]."-".$date[mday];
}

function anadirDia($enstring){
            $fecha = strtotime($enstring);    
            $fecha = $fecha + (24*60*60);
            $date = getdate($fecha);
            return $date[year]."-".$date[mon]."-".$date[mday];
}

function anadirDias($enstring,$i){
            $fecha = strtotime($enstring);    
            $fecha = $fecha + (24*60*60*$i);
            $date = getdate($fecha);
            return $date[year]."-".$date[mon]."-".$date[mday];
}
	
function nombreDelMes($mes,$anyo)
{
    // fecha actual (por ejemplo 2005-08-03)
    $date = strtotime($anyo."-".$mes."-01");

    // desglosamos la fecha y obtenemos el aÃ±o, mes y dia del mes (0 hasta 31)
    $array_date = getdate($date);
    $year = $array_date["year"];
    $month = $array_date["mon"];

    // construimos una nueva fecha que empieza en el dia "1" por ejemplo 2005-08-01
    $array_date = getdate(mktime(0, 0, 0, $month, 1, $year));
    return $array_date["month"];
}
	
function diaDeLaSemana($mes,$anyo)
	{
		$date = strtotime($anyo."-".$mes."-01");	
		$array_date = getdate($date);
		$temp = $array_date["wday"];
		if($temp<2) {
			$temp = $temp+7;
		}
		if($temp == 8){$temp = 1;}
		return $temp;
	}
	
function nomDuMois($mes){
    // $date = strtotime($anyo."-".$mes."-01");

    // $array_date = getdate($date);
    // $year = $array_date["year"];
    // $month = $array_date["mon"];

    // $array_date = getdate(mktime(0, 0, 0, $month, 1, $year));

    $sortie = "";		
    
    switch ($mes) {
        case 1:
            $sortie = "Janvier";
            break;
        case 2:
            $sortie = utf8_encode("Février");
            break;
        case 3:
            $sortie = "Mars";
            break;
        case 4:
            $sortie = "Avril";
            break;
        case 5:
            $sortie = "Mai";
            break;
        case 6:
            $sortie = "Juin";
            break;
        case 7:
            $sortie = "Juillet";
            break;
        case 8:
            $sortie = utf8_encode("Août");
            break;
        case 9:
            $sortie = "Septembre";
            break;
        case 10:
           $sortie = "Octobre";
            break;
        case 11:
           $sortie = "Novembre";
            break;
        case 12:
           $sortie = utf8_encode("Décembre");
            break;
    }
    return $sortie;		
}

function nomJourSemaine($fecha){
        $array_date = getdate(strtotime($fecha));
        switch ($array_date["wday"]) {
            case 1:
                $sortie = "Lundi";
                break;
            case 2:
                $sortie = "Mardi";
                break;
            case 3:
                $sortie = "Mercredi";
                break;
            case 4:
                $sortie = "Jeudi";
                break;
            case 5:
                $sortie = "Vendredi";
                break;
            case 6:
                $sortie = "Samedi";
                break;
            case 0:
                $sortie = "Dimanche";
                break;
            default:
                $sortie = "";
                break;                
        }
    return $sortie;		
}	

function maxStringVille($fecha1,$fecha2){
//    require ('_connect.php');
    require ('../../../01_include/_connect.php');
		$requete =  "SELECT  
			villes.nom_ville_maj as chaine,
			LENGTH(villes.nom_ville_maj) as length2
			FROM evenement, villes
			WHERE (((date_debut + INTERVAL 1 DAY > :fecha1) AND (date_debut - INTERVAL 1 DAY < :fecha2)) OR ((date_fin + INTERVAL 1 DAY > :fecha1) AND (date_fin - INTERVAL 1 DAY < :fecha2	)))
			AND DATEDIFF(date_fin ,date_debut)<8
			AND evenement.no_ville = villes.id
			ORDER BY length2 desc";	
			
		$results_evenements = $connexion->prepare($requete);
		$results_evenements->execute(array(':fecha1'=>$fecha1   , ':fecha2' => $fecha2)) or die ("requete ligne 221: ".$requete);
		$tab_evenements = $results_evenements->fetchAll(PDO::FETCH_ASSOC);    
		return $tab_evenements[0][chaine];
}

function maxStringgenre($fecha1,$fecha2){
//    require ('_connect.php');
    require ('../../../01_include/_connect.php');
		$requete =  "SELECT  
			genre.libelle as chaine,
			LENGTH(genre.libelle) as length2
			FROM evenement, genre
			WHERE (((date_debut + INTERVAL 1 DAY > :fecha1) AND (date_debut - INTERVAL 1 DAY < :fecha2)) OR ((date_fin + INTERVAL 1 DAY > :fecha1) AND (date_fin - INTERVAL 1 DAY < :fecha2	)))
			AND DATEDIFF(date_fin ,date_debut)<8
			AND evenement.no_genre = genre.no
			ORDER BY length2 desc";	
			
		$results_evenements = $connexion->prepare($requete);
		$results_evenements->execute(array(':fecha1'=>$fecha1   , ':fecha2' => $fecha2)) or die ("requete ligne 239: ".$requete);
		$tab_evenements = $results_evenements->fetchAll(PDO::FETCH_ASSOC);    
		return $tab_evenements[0][chaine];
}