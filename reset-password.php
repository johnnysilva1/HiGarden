<?php
    include_once 'header.php';

    include_once './helpers/session_helper.php';
?>


<main>
    <form method="POST" action="./controllers/ResetPasswords.php">
    <h1 class="header"> Alterar senha</h1>
    <?php flash('reset'); ?>
    <input type="hidden" name="type" value="send">
    <input type="text" name="usersEmail" placeholder="Email">
    <button type="submit" name="submit">Receber email</button>
</form>
</main>

<?php
    include_once 'footer.php';
?>