<?php
	//On inclut le modèle
	require_once(ROOT_PATH.MODELE_PATH.'home.php');
	require_once(ROOT_PATH.MODELE_PATH.'user.data.php');

	mustConnected(); //On vérifit que l'utilisateur est connecté

	$video_path = getVideoPath();
	if(is_dir($video_path)){
		//On récupère l'arborescence
		$arborescence = getArborescence($video_path, explode(', ', VIDEO_EXTENSION));
		unset($arborescence[0]);
	}
	else{ //Le dossier n'en est pas un
		$arborescence = array();
		echo '<div class="alert alert-error">Dossier "'.$video_path.'" invalide</div>';
	}
	 
	//On inclut la vue
	require_once(ROOT_PATH.VIEW_PATH.'video.php');
?>