<?php
	//On inclut le modèle
	require_once(ROOT_PATH.MODELE_PATH.'home.php');
	require_once(ROOT_PATH.MODELE_PATH.'user.data.php');

	mustConnected(); //On vérifit que l'utilisateur est connecté
	
	$music_path = getMusicPath();
	if(is_dir($music_path)){
		//On récupère l'arborescence
		$arborescence = getArborescence($music_path, explode(', ', MUSIC_EXTENSION));
	}
	else{ //Le dossier n'en est pas un
		$arborescence = array();
		echo '<div class="alert alert-error">Dossier "'.$music_path.'" invalide</div>';
	}

	//On inclut la vue
	require_once(ROOT_PATH.VIEW_PATH.'home.php');
?>