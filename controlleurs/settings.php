<?php
	//On inclut le modèle
	require_once(ROOT_PATH.MODELE_PATH.'settings.php');
	require_once(ROOT_PATH.MODELE_PATH.'user.data.php');
	require_once(ROOT_PATH.MODELE_PATH.'scrobbler.php');

	mustConnected(); //On vérifit que l'utilisateur est connecté
	
	//On vérifit que c'est un admin
	$admin = isAdmin();
	if($admin){$user = getUserArray();}

	$state = '';
	if(isset($_GET['lastfm'])){
		//On gère l'enregistrement de la session lastfm
		$session = connectLastFM();
		saveLastfmSession($session);
	}

	if(!empty($_GET['deleteuser'])){
		if(is_numeric($_GET['deleteuser'])){
			//Suppression d'utilisateur
			deleteUser($_GET['deleteuser']);
			header('Location: index.php?p=settings');
		}
	}

	if(!empty($_POST['oldPassword']) && !empty($_POST['password'])){
		//On sauvegarde le password
		$savePass = savePassword($_POST['oldPassword'], $_POST['password']);

		//On gère les erreurs
		if($savePass){
			$state .= '<div class="alert alert-success">Le mot de passe a été modifié</div>';
		}
		else{
			$state .= '<div class="alert alert-error">L\'ancien mot de passe est invalide</div>';
		}
	}

	if(!empty($_POST['podcast'])){
		//On sauvegarde les podcast
		$savePodcast = savePodcast($_POST['podcast']);

		//On gère les erreurs
		if($savePodcast){
			$state .= '<div class="alert alert-success">Les podcasts ont été modifiés</div>';
		}
		else{
			$state .= '<div class="alert alert-error">Une erreur est survenu lors de l\'enregistrement du podcast</div>';
		}
	}

	//On récuère les podcast pour le form
	$podcast = getPodcastUrl();
	$podcast = (empty($podcast)) ? '' : $podcast;

	//On inclut la vue
	require_once(ROOT_PATH.VIEW_PATH.'settings.php');
?>