<?php
    include_once 'header.php';
?>

<main class='main-index'>
<?php if (isset($_SESSION['usersId'])) {
    include_once 'user-view.php';
} else {
    echo '<h1>Olá, Visitante</h1>';
}
?> 
</main>
<?php
    include_once 'footer.php';
?>