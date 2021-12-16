<?php
    include_once './helpers/session_helper.php';

    include_once 'header.php';
?>
<main>
	<form class="form-login" action="./controllers/Users.php" method="POST">
		<input type="hidden" name="type" value="login">
		<h3>LOGIN</h3>
		<p>Por favor, insira suas credenciais.</p>
		<?php flash('login'); ?>
		<input type="text" name="name/email" id="usuario" placeholder="Usuário" autofocus>
		<input type="password" name="usersPwd" id="senha" placeholder="Senha">
		<button type="submit" name="submit">LOGIN</button>
		<p class="mensagem"> Gostaria de se registrar? <a href="./signup.php">Criar uma conta</a></p>
		<a href="./reset-password.php"><p id="resetSenha">Esqueci minha senha</p></a>
	</form>
</main>

<?php
    include_once 'footer.php';
?>