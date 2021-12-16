<?php
    include_once 'header.php';
?>

<main>
<h1>Olá, <?php if (isset($_SESSION['usersId'])) {
    echo explode(' ', $_SESSION['usersName'])[0];
} else {
    echo 'Visitante';
}
?> </h1>
</main>
<?php
    include_once 'footer.php';
?>