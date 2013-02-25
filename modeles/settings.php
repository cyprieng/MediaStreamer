<?php
	/* 
	Sauvegarde la session lastfm
	@params	session		Session lastfm
	@return	false si erreur, true sinon
	*/
	function saveLastfmSession($session){
		if(isConnected()){
			$id = getId();

			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
			$query=$bdd->prepare('UPDATE user SET lastfm_session =:session WHERE name =:name && password =:password');
			$query->execute(array(
				'name' => $id[0],
				'password' => $id[1],
				'session'	=>	$session
			));
			return true;
		}
		else{return false;}
	}

	/* 
	Sauvegarde le password si l'ancien correspond
	@params	old		Ancien password
	@params	new		Nouveau password
	@return	false si erreur, true sinon
	*/
	function savePassword($old, $new){
		if(isConnected()){
			$id = getId();
			$testOld = testLogin($id[0], sha1($id[0].$old)); //On vérifit l'ancien mdp

			if($testOld){
				$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
				$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
				$query=$bdd->prepare('UPDATE user SET password =:new WHERE name =:name && password =:old');
				$query->execute(array(
					'name' => $id[0],
					'old' => sha1($id[0].$old),
					'new'	=>	sha1($id[0].$new)
				));

				connect($id[0], $new); //On se connecte
				return true;
			}
			else{return false;}
		}
		else{return false;}
	}

	/* 
	Sauvegarde la liste des podcasts
	@params	podcast		Liste des podcasts (url1, url2...)
	@return	false si erreur, true sinon
	*/
	function savePodcast($podcast){
		if(isConnected()){
			$id = getId();

			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
			$query=$bdd->prepare('UPDATE user SET podcast =:podcast WHERE name =:name && password =:password');
			$query->execute(array(
				'name' => $id[0],
				'password' => $id[1],
				'podcast'	=>	$podcast
			));
			return true;
		}
		else{return false;}
	}

	/* 
	Récupère les infos des utilisateurs
	@return	tableau des users
	*/
	function getUserArray(){
		$user = array();
		$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
		$query=$bdd->query('SELECT id, name, music_folder, video_folder, podcast, lastfm_session FROM user ORDER BY id');

		while($donnees = $query->fetch()){
			$user[$donnees['id']] = array(
				'id'				=>	$donnees['id'],
				'name'				=>	$donnees['name'],
				'music_folder'		=>	$donnees['music_folder'],
				'video_folder'		=>	$donnees['video_folder'],
				'podcast'			=>	$donnees['podcast'],
				'lastfm_session'	=>	$donnees['lastfm_session'],
				);
		}
		$query->closeCursor();
		return $user;
	}

	/* 
	Modification ou crééation d'un user (par admin)
	@params	id				Id de l'user (modification)
	@params	name			Nom de l'user (ajout)
	@params	password		Password de l'user (ajout)	
	@params	music_folder	Dossier musique de l'user (modification & ajout)
	@params	video_folder	Dossier vidéo de l'user (modification & ajout)
	@return	false si erreur, true sinon
	*/
	function saveUser($id, $name, $password, $music_folder, $video_folder){
		if(is_numeric($id)){ //Modification user
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
			$query=$bdd->prepare('UPDATE user SET music_folder =:music_folder, video_folder = :video_folder  WHERE id =:id');
			$query->execute(array(
				'id' 			=> $id,
				'music_folder'	=> $music_folder,
				'video_folder'	=> $video_folder
			));

			return true;
		}
		else if($id = 'add'){ //Ajout user
			$password = sha1($name.$password);

			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
			$req = $bdd->prepare('INSERT INTO user(name, password, music_folder, video_folder) VALUES(:name, :password, :music_folder, :video_folder)');
			$req->execute(array(
				'name'		=>	$name,
				'password'	=>	$password,
				'music_folder'	=> $music_folder,
				'video_folder'	=> $video_folder
				));

			return true;
		}
		else{return false;}
	}

	/* 
	Suppression d'un user (par admin)
	@params	id		Id de l'user
	*/
	function deleteUser($id){
		$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
		$req = $bdd->prepare('DELETE FROM user WHERE id =:id');

		$req->execute(array('id' => $id));
	}
?>