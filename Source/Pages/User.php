<?php include("Common.php"); ?>
<?php Session::verify($configuration); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Account Details"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Acount Details:</b></label><br /><br />
		<label>Username:</label>
		<label>
		<?php
			$session = $_SESSION["Session"];
			$userLoggedIn = $session->user;
			echo($userLoggedIn->username);
		?>
		</label><br /><br />

		<a href="UserLogout.php">Log Out</a><br />
		<a href="UserDelete.php">Delete Account</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
