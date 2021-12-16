<?php if (PHP_SESSION_NONE == session_status()) {
    session_start();

    //adicioneu outro
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>PHP</title>
        <link rel="stylesheet" href="./assets/css/style.css?v=<?php echo time(); ?>">       
    </head>
<body>
    
<nav class="navbar">
	<ul class="navbar-nav">

	<a href="index.php" class="nav-link">
		<li class="nav-item"> 
			Home
		</li>
	</a>
	
	<?php if (!isset($_SESSION['usersId'])) { ?>
		<a href="signup.php" class="nav-link">
			<li class="nav-item">
				Sign Up
			</li>
		</a>

		<a href="login.php" class="nav-link">
			<li class="nav-item">
				Login
			</li>
		</a>

	<?php } else { ?>
		<a href="./controllers/Users.php?q=logout" class="nav-link">
			<li class="nav-item">
				Logout
			</li>
		</a>
	<?php } ?>

	</ul>
</nav>