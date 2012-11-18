<div class="container hero-unit">
	<h3>Connection</h3>
	<?php echo $state;?>
	<form class="form-horizontal" action="?p=login" method="post">
		<div class="control-group">
			<label class="control-label" for="inputNom">Nom</label>
			<div class="controls">
				<input name="name" type="text" id="inputNom" placeholder="Nom">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="inputPassword">Mot de Passe</label>
			<div class="controls">
				<input name="password" type="password" id="inputPassword" placeholder="Mot de Passe">
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label class="checkbox">
					<input type="checkbox"> Connexion automatique
				</label>
				<button type="submit" class="btn">Se connecter</button>
			</div>
		</div>
	</form>
</div>