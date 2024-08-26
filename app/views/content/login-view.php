<div class="main-container">
	<form class="box login" action="" method="POST" autocomplete="off">
		<p class="has-text-centered">
			<i class="fas fa-user-circle fa-5x"></i>
		</p>
		<h5 class="title is-5 has-text-centered">Inicia sesión con tu cuenta</h5>

		<?php
		if (isset($_POST['email']) && isset($_POST['password'])) {
			$insLogin->iniciarSesionControlador();
		}
		?>

		<div class="field">
			<label class="label"><i class="fas fa-envelope"></i> &nbsp; Correo electrónico</label>
			<div class="control">
				<input class="input" type="email" name="email" required>
			</div>
		</div>

		<div class="field">
			<label class="label"><i class="fas fa-key"></i> &nbsp; Contraseña</label>
			<div class="control">
				<input class="input" type="password" name="password" required>
			</div>
		</div>

		<p class="has-text-centered mb-4 mt-3">
			<button type="submit" class="button is-info is-rounded">LOG IN</button>
		</p>
	</form>
</div>