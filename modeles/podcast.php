<?php
	/* 
	Retourne une arborescence des podcasts
	@params	urls	array des urls des podcast
	@return	array[numéro podcast] [0]:nom [1-2...][0]:titre podcast n [1-2...][1]:url podcast n
	*/
	function getPodcast($urls){
		$i=0;
		foreach($urls as $url){
			$podcast[$i][] = $url; //Nom du podcast dans case 0

			if($flux = simplexml_load_file(trim($url))){
				$donnee = $flux->channel;
				//Lecture des données
				foreach($donnee->item as $valeur){
					//Affichages des données
					$podcast[$i][] = array($valeur->enclosure['url'], $valeur->title);
				}
			}
			$i++;
		}
		return $podcast;
	}
?>