<?php include "Common.php"; ?>

<html>

<head><?php PageWriter::elementHeadWrite("User Logout"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<p>User logged out.</p>
		<a href='UserLogin.php'>Log In</a>
		<?php
			Session::stop();
			header("Location: UserLogin.php");
			die();
		?>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
