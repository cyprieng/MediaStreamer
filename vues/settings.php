<div class="container hero-unit">
	<h3>Paramètres</h3>
	<?php echo $state; ?>
	<form class="form-horizontal" action="index.php?p=settings" method="post">
		<div class="control-group">
			<label class="control-label" for="inputOldPass">Ancien mot de passe</label>
			<div class="controls">
				<input type="password" name="oldPassword" id="inputOldPass" placeholder="Ancien mot de passe">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="inputPassword">Nouveau mot de passe</label>
			<div class="controls">
				<input type="password" name="password" id="inputPassword" placeholder="Ancien mot de passe">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="inputPodcast">Podcast</label>
			<div class="controls">
				<input class="span5" type="text" name="podcast" id="inputPodcast" placeholder="Podcast" value="<?php echo $podcast; ?>">
				<span>Séparer les URL par des virgules</span>
			</div>
		</div>

		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn btn-primary">Valider</button>
			</div>
		</div>
	</form>	
	<form class="form-horizontal" action="index.php?p=settings&lastfm=1" method="post">
		<div class="control-group">
			<div class="controls">
				<?php $session = getLastfmSession(); if(!empty($session)){ ?>
					<button type="button" class="btn disabled">Se connecter à lastfm</button>
				<?php }
				else{ ?>
					<button type="submit" class="btn ">Se connecter à lastfm</button>
				<?php } ?>
			</div>
		</div>
	</form>

	<?php
	//Liste des utilisateurs si admin
	if($admin){ ?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>#</th>
				<th>Nom</th>
				<th>Dossier musique</th>
				<th>Dossier vidéos</th>
				<th>Modifier</th>
				<th>Supprimer</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach($user as $tr){
				echo '<tr>';
				echo '<td>'.$tr['id'].'</td>';
				echo '<td>'.$tr['name'].'</td>';
				echo '<td>'.$tr['music_folder'].'</td>';
				echo '<td>'.$tr['video_folder'].'</td>';
				echo '<td><a href="index.php?p=edituser&user='.$tr['id'].'"/><i class="icon-wrench"></i></a></td>';
				echo '<td><a href="index.php?p=settings&deleteuser='.$tr['id'].'"/><i class="icon-remove"></i></a></td>';
				echo '</tr>';
			}
		?>
		</tbody>
	</table>

	<a href="index.php?p=edituser&user=add">Ajouter un utilisateur</a>
	<?php } ?>
</div>