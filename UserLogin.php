<?php include("Common.php"); ?>

<html>

<?php PageWriter::elementHeadWrite("User Login"); ?>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<form action="UserLogin.php" method="post">
			<label><b>Log In</b></label><br />
			<label>Username:</label><br />
			<input name="Username"></input><br />
			<label>Password:</label><br />
			<input name="Password" type="password"></input><br />
			<button type="submit">Log In</button>
			<a href="UserPasswordReset.php">Forgot Password</a>
			<a href="UserNew.php">Register as New User</a>
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
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>

</html>
