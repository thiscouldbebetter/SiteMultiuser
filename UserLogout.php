<?php include "Common.php"; ?>

<html>

<?php PageWriter::elementHeadWrite("User Logout"); ?>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<p>User logged out.</p>
		<a href='UserLogin.php'>Log In</a>
		<?php	
			session_destroy();
			header("Location: UserLogin.php");
			die();
		?>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
