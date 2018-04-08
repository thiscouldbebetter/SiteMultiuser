<?php include "Common.php"; ?>

<html>

<?php PageWriter::elementHeadWrite("Change Password"); ?>

<body>

	<div>

		<form action="" method="post">
			<label>Password:</label><br />
			<input name="Password" type="password"></input><br />
			<label>Password Confirmation:</label><br />
			<input name="PasswordConfirm" type="password"></input><br />
			<button type="submit">Change Password</button>
		</form>

		<?php
			$username = $_GET["username"];
			$passwordResetCode = $_GET["passwordResetCode"];
			$messagePasswordResetLinkNotValid = "The password reset link is not valid.";
			
			if (isset($username) == false || isset($passwordResetCode) == false)
			{
				echoStatusMessageAndExit($messagePasswordResetLinkNotValid);
			}
			
			$persistenceClient = $_SESSION["PersistenceClient"];
			$userFound = $persistenceClient->userGetByUsername($username);

			if ($userFound == null || $userFound->passwordResetCode != $passwordResetCode)
			{		
				echoStatusMessageAndExit($messagePasswordResetLinkNotValid);
			}
				
			$passwordCharactersRequired = 12;
			$messagePasswordsMustMatch = 
				"The values entered in the Password and Password Confirmation boxes must match.";
			$messagePasswordCriteria = 
				"Password must be at least " . $passwordCharactersRequired . " characters long, "
				. "and must contain uppercase letters, lowercase letters, and numerals.";
			$messageInitial = 
				"Enter and confirm a new password to change it.  " 
				. $messagePasswordCriteria
				. "  " . $messagePasswordsMustMatch;

			if 
			(
				isset($_POST["Password"]) == false 
				|| isset($_POST["PasswordConfirm"]) == false 
			)
			{		
				echoStatusMessageAndExit($messageInitial);
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
			
			$passwordSalt = User::passwordSaltGenerate();
			$passwordHashed = User::passwordHashWithSalt($passwordEntered, $passwordSalt);
			$userFound->passwordSalt = $passwordSalt;
			$userFound->passwordHashed = $passwordHashed;
			$userFound->passwordResetCode = null;
			$persistenceClient->userSave($userFound);

			$sessionToken = "todo";
			$now = new DateTime();
			$sessionNew = new Session(null, $userFound, $sessionToken, $now, $now, null);
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
