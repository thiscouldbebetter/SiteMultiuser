<?php include "Common.php"; ?>
<?php Session::verify(); ?>

<html>

<head><?php PageWriter::elementHeadWrite("User Logout"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<p>User logged out.</p>
		<a href='UserLogin.php'>Log In</a>
		<?php
			$session = $_SESSION["Session"];
			$now = new DateTime();
			$session->timeEnded = $now;
			$persistenceClient = $_SESSION["PersistenceClient"];
			$persistenceClient->sessionSave($session);
			session_destroy();
			header("Location: UserLogin.php");
			die();
		?>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
