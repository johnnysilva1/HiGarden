<?php
    if (empty($_GET['selector']) || empty($_GET['validator'])) {
        echo 'Sua requisição não pode ser validada';
    } else {
        $selector = $_GET['selector'];
        $validator = $_GET['validator'];

        if (ctype_xdigit($selector) && ctype_xdigit($validator)) {
            ?>


<?php
    include_once 'header.php';

            include_once './helpers/session_helper.php'; ?>

<h1 class="header">Insira sua nova senha</h1>

<?php flash('newReset'); ?>

<form method="post" action="./controllers/ResetPasswords.php">
    <input type="hidden" name="type" value="reset">
    <input type="hidden" name="selector" value="<?php echo $selector; ?>">
    <input type="hidden" name="validator" value="<?php echo $validator; ?>">
    <input type="password" name="pwd" placeholder="Insira uma nova senha">
    <input type="password" name="pwd-repeat" placeholder="Repita a nova senha">
    <button type="submit" name="submit">Receber email</button>
</form>

<?php
    include_once 'footer.php';
?>

<?php
        } else {
            echo 'Sua requisição não pode ser validada';
        }
    }
?>