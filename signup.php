<?php
include_once './helpers/session_helper.php';

include_once 'header.php';

?>

<main>
<form method='POST' action='./controllers/Users.php'>
    <h3 class='header'>Por favor, se registre</h3>
    <?php flash('register'); ?>
    <input type='hidden' name='type' value='register'>
    <input type='text' name='usersName' placeholder='Nome completo'>
    <input type='text' name='usersEmail' placeholder='Email'>
    <input type='text' name='usersUid' placeholder='Nome de usuário'>
    <input type='password' name='usersPwd' placeholder='Senha'>
    <input type='password' name='pwdRepeat' placeholder='Repita a senha'>
    <button type='submit' name='submit'>Me registre</button>
</form>
</main>

<?php

include_once 'footer.php';

?>