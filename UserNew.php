<?php include "Common.php"; ?>

<html>

<?php PageWriter::elementHeadWrite("New User"); ?>

<body>

	<div>

		<form action="UserNew.php" method="post">
			<label><b>Register New User</b></label><br />
			<label>Username:</label><br />
			<input name="Username"></input><br />
			<label>Email Address:</label><br />
			<input name="EmailAddress"></input><br />
			<label>Password:</label><br />
			<input name="Password" type="password"></input><br />
			<label>Password Confirmation:</label><br />
			<input name="PasswordConfirm" type="password"></input><br />
			<button type="submit">Register as New User</button>
		</form>

		<?php

			$passwordCharactersRequired = 12;
			$messagePasswordsMustMatch = 
				"The values entered in the Password and Password Confirmation boxes must match.";
			$messagePasswordCriteria = 
				"Password must be at least " . $passwordCharactersRequired . " characters long, "
				. "and must contain uppercase letters, lowercase letters, and numerals.";
			$messageInitial = 
				"Enter a username, email, and password to create a new user.  " 
				. $messagePasswordCriteria
				. "  " . $messagePasswordsMustMatch;

			if 
			(
				isset($_POST["Username"]) == false 
				|| isset($_POST["Password"]) == false 
				|| isset($_POST["PasswordConfirm"]) == false 
				|| isset($_POST["EmailAddress"]) == false
			)
			{		
				echoStatusMessageAndExit($messageInitial);
			}
				
			$usernameEntered = $_POST["Username"];
			$emailAddressEntered = $_POST["EmailAddress"];
				
			if ($usernameEntered == "" || $emailAddressEntered == "")
			{
				echoStatusMessageAndExit($messageInitial);
			}
			
			$isEmailAddressWellFormed = filter_var($emailAddressEntered, FILTER_VALIDATE_EMAIL);
			
			if ($isEmailAddressWellFormed == false)
			{
				echoStatusMessageAndExit("Email address specified did not have a valid format.");
			}
			
			$passwordEntered = $_POST["Password"];	
			$doesPasswordMeetCriteria = false;
			if (strlen($passwordEntered) >= $passwordCharactersRequired)
			{
				$doesPasswordContainUppercase = ( preg_match('/[A-Z]/', $passwordEntered) == 1 );
				$doesPasswordContainLowercase = ( preg_match('/[a-z]/', $passwordEntered) == 1 );
				$doesPasswordContainNumeral = ( preg_match('/[0-9]/', $passwordEntered) == 1 );
				
				if 
				(
					$doesPasswordContainUppercase == true 
					&& $doesPasswordContainLowercase == true 
					&& $doesPasswordContainNumeral == true
				)
				{
					$doesPasswordMeetCriteria = true;
				}
			}
			
			if ($doesPasswordMeetCriteria == false)
			{
				echoStatusMessageAndExit("Password does not meet requirements.  " . $messagePasswordCriteria);		
			}
			
			$passwordConfirmationEntered = $_POST["PasswordConfirm"];
			if ($passwordEntered != $passwordConfirmationEntered)
			{
				echoStatusMessageAndExit($messagePasswordsMustMatch);	
			}
			
			$persistenceClient = $_SESSION["PersistenceClient"];
			$userFound = $persistenceClient->userGetByUsername($usernameEntered);

			if ($userFound != null)
			{		
				echoStatusMessageAndExit("A user with the specified username already exists.  Choose another username.");
			}
			
			$passwordSalt = User::passwordSaltGenerate();
			$passwordHashed = User::passwordHashWithSalt($passwordEntered, $passwordSalt);
			$passwordResetCode = null;
			$isActive = 1;
			
			$userNew = new User
			(
				null, $usernameEntered, $emailAddressEntered, 
				$passwordSalt, $passwordHashed, $passwordResetCode, $isActive, array()
			);
			$persistenceClient->userSave($userNew);

			$sessionToken = "todo";
			$now = new DateTime();
			$sessionNew = new Session(null, $userNew, "sessionToken", $now, $now, null);
			$persistenceClient->sessionSave($sessionNew);
			
			$_SESSION["Session"] = $sessionNew;
			header("Location: User.php");
			
			$databaseConnection->close();		
			
			function echoStatusMessageAndExit($statusMessage)
			{
				echo $statusMessage;		
				die();
			}
			
		?>
	</div>

</body>
</html>
