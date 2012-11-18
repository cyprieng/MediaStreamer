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
						<div class="row">
							<div class="span8">
								<ul class="nav">
									<li <?php if($p == '') echo 'class="active"' ?>><a href="index.php">Accueil</a></li>
									<li <?php if($p == 'video') echo 'class="active"' ?>><a href="index.php?p=video">Vidéo</a></li>
									<li <?php if($p == 'podcast') echo 'class="active"' ?>><a href="index.php?p=podcast">Podcast</a></li>
									<?php if(isConnected()){?><li <?php if($p == 'settings' || $p == 'edituser') echo 'class="active"' ?>><a href="index.php?p=settings">Paramètres</a></li><?php } ?>
								</ul>
							</div>
							<div class="span1">
								<ul class="nav">
									<?php if(isConnected()){?><li><a href="index.php?p=login&deconnect=1">Déconnection</a></li><?php } ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>