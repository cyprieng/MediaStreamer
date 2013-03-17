<?php
	//Fichier de récupération des medias
	require_once('../global/init.php');
	require_once(ROOT_PATH.MODELE_PATH.'login.php');
	require_once(ROOT_PATH.MODELE_PATH.'user.data.php');
	require_once(ROOT_PATH.MODELE_PATH.'settings.php');
	
	if(!empty($_GET['u']) && !empty($_GET['p'])){ //Via API
		//On vérifit les identifiants
		$login = testLogin($_GET['u'], $_GET['p']);
		if(!$login){exit('');}

		if(!isset($user)){
			//On récupère les infos sur l'user
			$user = getUserArray();
			foreach($user as $key => $us){
				if($us['name'] == $_GET['u']){$user = $user[$key];break;}
			}
		}

		//On vérifit qu'on est dans le bon dossier
		if(preg_match('/\.\./', $_GET['file']) || (stripos($_GET['file'], $user['music_folder']) === false && stripos($_GET['file'], $user['video_folder']) === false && !preg_match('/https?:\/\//', $_GET['file']))){exit('ok');}
	}
	else{
		mustConnected(); //Sinon on doit être connecté

		//On vérifit qu'on est dans le bon dossier
		if(preg_match('/\.\./', $_GET['file']) || (stripos($_GET['file'], getMusicPath()) === false && stripos($_GET['file'], getVideoPath()) === false && !preg_match('/https?:\/\//', $_GET['file']))){exit('');}
	}

	$extension = substr(strrchr($_GET['file'],'.'),1);

	//On ajoute le header en fonction de l'extension
	if($extension == 'mp3'){
		//HEADER MP3
		header('Content-Type: audio/mpeg');
		header('Content-Disposition: inline;filename="test.mp3"');
		if(filesize($_GET['file']) != false){header('Content-length: '.filesize($_GET['file']));}
		header('Cache-Control: no-cache');
		header("Content-Transfer-Encoding: binary"); 
	}
	else if($extension == 'avi'){
		//HEADER AVI
		header ("Content-type: video/avi");
	}

	$fp = fopen($_GET['file'],"rb"); //lecture du fichier en binaire
	while (!feof($fp)) { //on parcourt toutes les lignes
		echo fgets($fp, 4096); // lecture du contenu de la ligne
	}
?>