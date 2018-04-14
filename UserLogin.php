<?php include("Common.php"); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Account Login"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<form action="UserLogin.php" method="post">

			<label><b>Account Login:</b></label><br /><br />

			<label>Username:</label><br />
			<input name="Username"></input><br />
			<label>Password:</label><br />
			<input name="Password" type="password"></input><br />

			<br/>
			<button type="submit">Log In</button>
		</form>

		<?php
			$passwordCharactersRequired = 12;
			$messagePasswordCriteria = 
				"Passwords must be at least " . $passwordCharactersRequired . " characters long, "
				. "and must contain uppercase letters, lowercase letters, and numerals.";

			$messageInstructions = "Enter a valid username and password to log in.  " . $messagePasswordCriteria;

			if (isset($_POST["Username"]) == false || isset($_POST["Password"]) == false)
			{
				PageWriter::displayStatusMessage($messageInstructions);
			}
			else
			{
				$usernameEntered = $_POST["Username"];
				$passwordEntered = $_POST["Password"];

				if ($usernameEntered == "" || $passwordEntered == "")
				{
					PageWriter::displayStatusMessage(messageInstructions);
				}
				else
				{
					$persistenceClient = $_SESSION["PersistenceClient"];
					$userFound = $persistenceClient->userGetByUsername($usernameEntered);

					if ($userFound == null)
					{
						PageWriter::displayStatusMessage("Username or password not valid.");
					}
					else 
					{
						$passwordEnteredHashed = $userFound->passwordHash($passwordEntered);
						if ($userFound->passwordHashed != $passwordEnteredHashed)
						{
							PageWriter::displayStatusMessage("Username or password not valid.");
						}
						else
						{
							$sessionToken = "todo";
							$now = new DateTime();
							$sessionNew = new Session(null, $userFound, $sessionToken, $now, $now, null);
							$_SESSION["Session"] = $sessionNew;
							$persistenceClient->sessionSave($sessionNew);

							header("Location: User.php");

							$databaseConnection->close();
						}
					}
				}
			}
		?>

		<a href="UserPasswordReset.php">Forgot Password</a><br />
		<a href="UserNew.php">Create New Account</a>

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>

</html>
