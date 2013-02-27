<?php
	require_once(ROOT_PATH.MODELE_PATH.'getid3/getid3.php'); //Bibliothèque tag audio

	/* 
	Récupère les paroles d'une musique
	@params	file	Fichier musique
	@return	Paroles
	@see	http://lyrics.wikia.com
	*/
	function lyrics($file){
		//On récupère les tag de la musique
		$getID3 = new getID3;
		$tag = $getID3->analyze($file);

		//On récupère le titre en fonction de la présence tu tag
		$title = (isset($tag['tags_html']['id3v1']['title'][0])) ? $tag['tags_html']['id3v1']['title'][0] : substr(strrchr($file,'/'),1);
		$artist = (isset($tag['tags_html']['id3v1']['artist'][0])) ? $tag['tags_html']['id3v1']['artist'][0] : '';

		//On récupère la page
		$lyrics = file_get_contents("http://lyrics.wikia.com/".urlencode(preg_replace('/ /', '_', $artist)).":".urlencode(preg_replace('/ /', '_', $title)));

		//On charge le dom
		$DOM = new DOMDocument;
		@$DOM->loadHTML($lyrics);

		$ok = false; //Var de test

		//On récupère le div de la bonne classe
		$items = $DOM->getElementsByTagName('div');
		for ($i = 0; $i < $items->length; $i++){
			if($items->item($i)->getAttribute('class') == 'WikiaPageContentWrapper'){
				$items = $items->item($i);
				$ok = true;
				break;
			}
		}
		if(!$ok) return 'Not Found';$ok = false; //On test si on a trouvé le div avec la class

		//On récupère le div de la bonne classe
		$items = $items->getElementsByTagName('div');
		for ($i = 0; $i < $items->length; $i++){
			if($items->item($i)->getAttribute('class') == 'mw-content-ltr'){
				$items = $items->item($i);
				$ok = true;
				break;
			}
		}
		if(!$ok) return 'Not Found';$ok = false; //On test si on a trouvé le div avec la class

		//On récupère le div de la bonne classe
		$items = $items->getElementsByTagName('div');
		for ($i = 0; $i < $items->length; $i++){
			if($items->item($i)->getAttribute('class') == 'lyricbox'){
				$items = $items->item($i);
				$ok = true;
				break;
			}
		}
		if(!$ok) return 'Not Found'; //On test si on a trouvé le div avec la class

		//On supprime les éléments qui ne nous intéresse pas
		$del = $items->getElementsByTagName('div');
		for ($i = 0; $i < $del->length; $i++)
			$del->item($i)->nodeValue = '';

		return $items->c14n(false); //On retourne les paroles
	}
?>