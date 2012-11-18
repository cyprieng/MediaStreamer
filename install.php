<?php
	include 'global/init.php';
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>MediaStreamer</title>
		
	
		<meta http-equiv="Pragma" content="no-cache">
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="<?php echo CSS_PATH; ?>bootstrap.min.css" rel="stylesheet">
		<link href="<?php echo CSS_PATH; ?>jquery-ui.css" rel="stylesheet">
		<link href="<?php echo CSS_PATH; ?>main.css" rel="stylesheet">
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo IMAGE_PATH; ?>favicon.png" />
	</head>
	<body>

		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					</a>
					<a class="brand" href="index.php">MediaStreamer</a>
					<div class="nav-collapse collapse">
						<ul class="nav">
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="container hero-unit">
			<h3>Installation</h3>
			
			<?php
				if(isset($_POST['name']) && isset($_POST['password']) && isset($_POST['serverSQL']) && isset($_POST['userSQL']) && isset($_POST['passwordSQL']) && isset($_POST['baseSQL'])){
					try{
						$bdd = new PDO('mysql:host='.$_POST['serverSQL'].';dbname='.$_POST['baseSQL'].'', $_POST['userSQL'], $_POST['passwordSQL']);

						$rootPath = dirname(__FILE__).DIRECTORY_SEPARATOR; //chemin pour php
						$htmlPath = mb_substr($_SERVER['REQUEST_URI'],0,-mb_strlen(strrchr($_SERVER['REQUEST_URI'],"/"))).DIRECTORY_SEPARATOR; //chemin pour html
						
						if(preg_match('/^[a-z0-9_-]{3,16}$/', $_POST['name'])){
							$name = $_POST['name'];
							if(preg_match('/^[a-z0-9_-]{6,255}$/', $_POST['password'])){
								$password = $_POST['password'];
								$password = sha1($name.$password);

								$bdd->exec('CREATE TABLE user (id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT, name VARCHAR(16) NOT NULL, password VARCHAR(255) NOT NULL, admin BOOLEAN NOT NULL DEFAULT \'0\', music_folder VARCHAR(255), video_folder VARCHAR(255), podcast TEXT, lastfm_session VARCHAR(255), PRIMARY KEY (id))ENGINE=INNODB;');

								$req = $bdd->prepare('INSERT INTO user(name, password, admin) VALUES(:name, :password, \'1\')');
								$req->execute(array(
									'name'		=>	$name,
									'password'	=>	$password
									));

								//On créé la config
								$configFile = fopen(GLOBAL_PATH.'config.php', 'a');
								
								ftruncate($configFile,0);
								fputs($configFile, "<?php\n");
								fputs($configFile, "	define('ROOT_PATH', '".$rootPath."');\n");
								fputs($configFile, "	define('ROOT_PATH_HTML', '".$htmlPath."');\n");
								fputs($configFile, "\n");
								fputs($configFile, "	//Identifiants pour la BDD\n");
								fputs($configFile, "	define('SQL_DSN',		'mysql:dbname=".$_POST['baseSQL'].";host=".$_POST['serverSQL']."');\n");
								fputs($configFile, "	define('SQL_USERNAME',	'".$_POST['userSQL']."');\n");
								fputs($configFile, "	define('SQL_PASSWORD',	'".$_POST['passwordSQL']."');\n");
								fputs($configFile, "\n");
								fputs($configFile, "	//Chemin d'accès\n");
								fputs($configFile, "	define('MODELE_PATH',		'modeles/');\n");
								fputs($configFile, "	define('CONTROLLER_PATH',	'controlleurs/');\n");
								fputs($configFile, "	define('VIEW_PATH',			'vues/');\n");
								fputs($configFile, "	define('GLOBAL_PATH',		'global/');\n");
								fputs($configFile, "	define('IMAGE_PATH',		'img/');\n");
								fputs($configFile, "	define('CSS_PATH',			'css/');\n");
								fputs($configFile, "	define('JS_PATH',			'js/');\n");
								fputs($configFile, "\n");
								fputs($configFile, "	//Extension autorisée\n");
								fputs($configFile, "	define('MUSIC_EXTENSION',	'mp3');\n");
								fputs($configFile, "	define('VIDEO_EXTENSION',	'avi');\n");
								fputs($configFile, "?>");
								 
								fclose($configFile);
								

								die('<div class="alert alert-success">L\'installation s\'est bien déroulée. Vous pouvez supprimer le fichier install.php</div>');
							}
							else{
								echo '<div class="alert alert-error">Erreur : Votre mot de passe doit contenir uniquement des lettres, chiffres et \'_\', \'-\', avec au moins 6 caractères.</div>';
							}
						}
						else{
							echo '<div class="alert alert-error">Erreur : Votre nom d\'utilisateur doit contenir uniquement des lettres, chiffres et \'_\', \'-\', avec 3 à 16 caractères.</div>';
						}
						
					}
					catch (Exception $e){
						echo '<div class="alert alert-error">Erreur : ' . $e->getMessage().'</div>';
					}
				}

				//On définit les variable pour les insérer dans le formulaire
				$name = (isset($_POST['name'])) ? $_POST['name'] : '';
				$password = (isset($_POST['password'])) ? $_POST['password'] : '';
				$serverSQL = (isset($_POST['serverSQL'])) ? $_POST['serverSQL'] : '';
				$userSQL = (isset($_POST['userSQL'])) ? $_POST['userSQL'] : '';
				$passwordSQL = (isset($_POST['passwordSQL'])) ? $_POST['passwordSQL'] : '';
				$baseSQL = (isset($_POST['baseSQL'])) ? $_POST['baseSQL'] : '';
			?>
			<form class="form-horizontal" action="install.php" method="post">
				<div class="control-group">
					<label class="control-label" for="inputName">Nom admin</label>
					<div class="controls">
						<input type="text" name="name" id="inputName" placeholder="Nom admin" value="<?php echo $name;?>">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputPassword">Mot de passe admin</label>
					<div class="controls">
						<input type="password" name="password" id="inputPassword" placeholder="Mot de passe admin" value="<?php echo $password;?>">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputServerSQL">Serveur MySQL</label>
					<div class="controls">
						<input type="text" name="serverSQL" id="inputServerSQL" placeholder="Serveur MySQL" value="<?php echo $serverSQL;?>">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputUserSQL">Utilisateur MySQL</label>
					<div class="controls">
						<input type="text" name="userSQL" id="inputUserSQL" placeholder="Utilisateur MySQL" value="<?php echo $userSQL;?>">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputPasswordSQL">Mot de passe MySQL</label>
					<div class="controls">
						<input type="password" name="passwordSQL" id="inputPasswordSQL" placeholder="Mot de passe MySQL" value="<?php echo $passwordSQL;?>">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputBaseSQL">Base MySQL</label>
					<div class="controls">
						<input type="text" name="baseSQL" id="inputBaseSQL" placeholder="Base MySQL" value="<?php echo $baseSQL;?>">
					</div>
				</div>

				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn btn btn-primary">Valider</button>
					</div>
				</div>
			</form>			
		</div>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script src="<?php echo JS_PATH; ?>bootstrap.min.js"></script>
		<script src="<?php echo JS_PATH; ?>jquery-ui.js"></script>
	</body>
</html>