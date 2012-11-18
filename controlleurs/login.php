<?php
	$state = '';

	if(isset($_POST['name']) && isset($_POST['password'])){
		$connect = testLogin($_POST['name'], sha1($_POST['name'].$_POST['password']));
		if($connect){
			//On a réussi à se connecter => redirection
			connect($_POST['name'], $_POST['password']);
			header('Location: index.php');
		}
		else{ 
			//Sinon erreur
			$state .= '<div class="alert alert-error">Mauvaise combinaison login/mot de passe</div>';
		}
	}
	if(isset($_GET['deconnect'])){
		//On déconnecte
		deconnect();
		header('Location: index.php?p=login');
	}

	//On inclut la vue
	require_once(ROOT_PATH.VIEW_PATH.'login.php');
?>