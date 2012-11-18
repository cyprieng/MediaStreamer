<?php
	//On inclut le modèle
	require_once(ROOT_PATH.MODELE_PATH.'settings.php');
	require_once(ROOT_PATH.MODELE_PATH.'user.data.php');

	mustConnected(); //On vérifit que l'utilisateur est connecté
	
	//On vérifit que c'est un admin
	$admin = isAdmin();
	if(!$admin){exit('');}

	$state = '';
	$user = array(
		'id'			=>	'',
		'name'			=>	'',
		'music_folder'	=>	'',
		'video_folder'	=>	''
		);

	if(isset($_GET['user'])){
		if(is_numeric($_GET['user'])){ //Modification
			if(isset($_POST['music_folder']) && isset($_POST['video_folder'])){ //On sauvegarde les données
				saveUser($_GET['user'], null, null, $_POST['music_folder'], $_POST['video_folder']);
				$state .= '<div class="alert alert-success">L\'utilisateur a bien été modifié</div>';
			}

			//On récupère les données de l'user
			$user = getUserArray();
			$user = $user[$_GET['user']];
		}
		else if($_GET['user'] == 'add'){ //Ajout d'utilisateur
			if(!empty($_POST['name']) && !empty($_POST['password']) && !empty($_POST['music_folder']) && !empty($_POST['video_folder'])){

				if(preg_match('/^[a-z0-9_-]{3,16}$/', $_POST['name'])){
					$name = $_POST['name'];
					
					if(preg_match('/^[a-z0-9_-]{6,255}$/', $_POST['password'])){
						$password = $_POST['password'];
						
						//Si tout les champs sont bon, on enregistre et on redirige
						saveUser($_GET['user'], $name, $password, $_POST['music_folder'], $_POST['video_folder']);
						header('Location: index.php?p=settings');
					}
				}
			}
		}
	}

	//On inclut la vue
	require_once(ROOT_PATH.VIEW_PATH.'edituser.php');
?>