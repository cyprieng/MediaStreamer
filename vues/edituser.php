<div class="container hero-unit">
	<h3>Paramètres</h3>
	<?php echo $state; ?>
	<form class="form-horizontal" action="index.php?p=edituser&user=<?php echo $_GET['user']; ?>" method="post">
		<?php if($_GET['user'] == 'add'){?>
			<div class="control-group">
				<label class="control-label" for="inputName">Nom</label>
				<div class="controls">
					<input type="text" name="name" id="inputName" placeholder="Nom">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="inputPassword">Mot de passe</label>
				<div class="controls">
					<input type="password" name="password" id="inputPassword" placeholder="Mot de passe">
				</div>
			</div>
		<?php } ?>
		<div class="control-group">
			<label class="control-label" for="inputMusicFolder">Dossier musique</label>
			<div class="controls">
				<input type="text" name="music_folder" id="inputMusicFolder" placeholder="Dossier musique" value="<?php echo $user['music_folder']; ?>">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="inputVideoFolder">Dossier vidéos</label>
			<div class="controls">
				<input type="text" name="video_folder" id="inputVideoFolder" placeholder="Dossier vidéos" value="<?php echo $user['video_folder']; ?>">
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn btn-primary">Valider</button>
			</div>
		</div>
	</form>	
</div>