<?php
	require_once(ROOT_PATH.MODELE_PATH.'getid3/getid3.php'); //Bibliothèque tag audio

	/* 
	Récupère les paroles d'une musique
	@params	file	Fichier musique
	@return	Paroles
	@see	lyrdb API
	*/
	function lyrics($file){
		//On récupère les tag de la musique
		$getID3 = new getID3;
		$tag = $getID3->analyze($file);

		//On récupère le titre en fonction de la présence tu tag
		$title = (isset($tag['tags_html']['id3v1']['title'][0])) ? $tag['tags_html']['id3v1']['title'][0] : substr(strrchr($file,'/'),1);

		//On récupère la page
		$res = file_get_contents("http://webservices.lyrdb.com/lookup.php?q=".urlencode($title)."&for=fullt&agent=agent");
		
		//On récupère l'id des paroles
		preg_match('#^[0-9]+#', $res, $id);
		if(!is_numeric($id[0])){return 'No lyrics';}

		//On récupère les paroles correspondant à l'id
		$lyrics = file_get_contents("http://webservices.lyrdb.com/getlyr.php?q=".$id[0]);
		return $lyrics;
	}
?>