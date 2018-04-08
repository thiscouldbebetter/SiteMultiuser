<?php include "Common.php"; ?>

<html>

<?php PageWriter::elementHeadWrite("User Logout"); ?>

<body>

	<div>
		<p>User logged out.</p>
		<a href='UserLogin.php'>Log In</a>
		<?php	
			session_destroy();
			header("Location: UserLogin.php");
			die();
		?>
	</div>

</body>
</html>
