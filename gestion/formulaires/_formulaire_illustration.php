<?php
$contenu .= '<div class="bloc" id="illustration">';
	$contenu .= '<div>';
		$contenu .= '<h1><b>Illustration</b></h1>';
		
//			if($PAGE=="evenement"||$PAGE=="editorial"){
//				$contenu .= '<div style="text-align:center;">';
//					$contenu .= '<div class="infos">';
//						$contenu .= 'N\'oubliez pas de choisir une image si vous souhaitez que votre  '.$libelle_item.' soit mis'.((!$est_feminin)?'':'e').' en avant';
//					$contenu .= '</div>';
//				$contenu .= '</div>';
//			}
                        if ($url_image != '') {
                            $contenu .= "<div id='exist_image_name' data-init='1'>";
                            $contenu .= "<img src='".$url_image."' style='max-height:200px; max-width:200px;' /><br/><br/>"; 
                            $contenu .= '<input id="btn_del_img_fiche" class="btn btn-danger" value="Supprimer l\'image" />';
                            $contenu .= "</div>";
                            $contenu .= "<div id='plupload' class='hide'>";
                        }
                        else {
                            $contenu .= "<div id='exist_image_name'></div>";
                            $contenu .= "<div id='plupload'>";
                        }
//                        $contenu .= '<div id="browse"><input class="btn btn-success" value="Charger l\'image" /></div>';
                        $contenu .= '<div id="browse"><a class="btn btn-success">Charger l\'image</a></div>';
                        $contenu .= "</div><br/>";
                        $contenu .= "<div id='progressgen' style='color:#790000; font-weight: bolder;'></div>";
                        $contenu .= "<div id='filelist' class='hide'></div><br/>";
			$contenu .= '<table>';
				$contenu .= '<tr>';
//					$contenu .= '<td rowspan="2" style="text-align:right;">';
////						$contenu .= '<input type="file" id="BDD'.(($PAGE!="structure")?'url_image':'url_logo').'" name="BDD'.(($PAGE!="structure")?'url_image':'url_logo').'" class="fichier poids[10|500] type[image] url['.(($url_image!=false)?str_replace("http://www.ensembleici.fr/","",$url_image):'').']" />';
//                                                $contenu .= '<input type="file" id="BDD'.(($PAGE!="structure")?'url_image':'url_logo').'" name="BDD'.(($PAGE!="structure")?'url_image':'url_logo').'" class="fichier poids[10|500] type[image] url['.(($url_image!=false)?str_replace("http://www.ensembleici.fr/","",$url_image):'').']" />';
//					$contenu .= '</td>';
                                        $contenu .= '<td class="entete"><label for="BDDcopyright">Copyright : </label></td>';
					$contenu .= '<td style="text-align: left;">';
						$contenu .= '<input type="text" id="BDDcopyright" name="BDDcopyright" value="'.$copyright.'" title="copyright" class="moyen_input" />';
					$contenu .= '</td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td style="text-align: left;" colspan="2">';
                                                $contenu .= '<h2>Légende</h2>';
						$contenu .= '<textarea type="text" maxlength="255" id="BDDlegende" name="BDDlegende" title="légende de l\'image" class="moyen_input">'.$legende.'</textarea>';
					$contenu .= '</td>';
				$contenu .= '</tr>';
			$contenu .= '</table>';
		
	$contenu .= '</div>';
$contenu .= '</div>';
?>
