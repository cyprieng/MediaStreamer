<?php
	//On inclut le modèle
	require_once(ROOT_PATH.MODELE_PATH.'podcast.php');
	require_once(ROOT_PATH.MODELE_PATH.'user.data.php');

	mustConnected(); //On vérifit que l'utilisateur est connecté

	$podcast = getPodcastUrl();
	if(!empty($podcast)){
		//On récupère les podcast
		$podcast = getPodcast(explode(', ', $podcast));
	}
	else{
		$podcast = array();
		echo '<div class="alert alert-error">Aucun podcast à lire</div>';
	}

	//On inclut la vue
	require_once(ROOT_PATH.VIEW_PATH.'podcast.php');
?>