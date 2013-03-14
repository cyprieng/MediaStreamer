<div class="container-fluid">
	<div class="span8 offset1">
		<div class="login">
			<div class="login-screen">
				<div class="login-icon">
					<img src="img/logo.png" alt="Welcome" />
					<h4>Welcome to <small>MediaStreamer</small></h4>
				</div>

				<div class="login-form">
					<?php echo $state;?>
					<form class="form-horizontal" action="?p=login" method="post">
						<div class="control-group">
							<input type="text" name="name" class="login-field" value="" placeholder="Nom" id="inputNom" />
						</div>

						<div class="control-group">
							<input type="password" name="password" class="login-field" value="" placeholder="Mot de Passe" id="inputPassword" />
						</div>
						<br/>
						<button type="submit" class="btn">Se connecter</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>