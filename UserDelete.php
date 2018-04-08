<?php include "Common.php"; ?>

<html>

<?php PageWriter::elementHeadWrite("Delete User"); ?>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label>Are you sure you want to delete the user
		<?php 
			$session = $_SESSION["Session"];
			$userLoggedIn = $session->user;
			echo($userLoggedIn->username);
		?>?
		</label><br />
		<label>To confirm, enter the username below and click the Delete User button.</label><br />
		<form action="UserDelete.php" method="post">
			<input name="UsernameToDelete"></input>
			<button type="submit">Delete User</button>
			<a href="User.php">Cancel</a>
		</form>
		
		<?php
			if (isset($_POST["UsernameToDelete"]) == true)
			{
				$usernameToDelete = $_POST["UsernameToDelete"];
				if ($usernameToDelete != $userLoggedIn->username)
				{
					echo "Username entered does not match.  User will not be deleted.";
				}
				else
				{
					$persistenceClient = $_SESSION["PersistenceClient"];
					$persistenceClient->userDeleteByID($userLoggedIn->userID);
					$_SESSION["Session"] = null;
					header("Location: UserLogin.php");
				}			
			}
		?>
	</div>

	<?php PageWriter::footerWrite(); ?>
	
</body>
</html>
