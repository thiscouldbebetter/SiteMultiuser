<?php include("Common.php"); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Change Password"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">

		<label><b>Change Password:</b></label><br /><br />
		<form action="" method="post">
			<label>Username:</label><br />
			<input name="Username"></input></br />
			<label>Password Reset Code:</label><br />
			<input name="PasswordResetCode"></input></br />
			<label>Password:</label><br />
			<input name="Password" type="password"></input><br />
			<label>Password Confirmation:</label><br />
			<input name="PasswordConfirm" type="password"></input><br /><br />
			<button type="submit">Change Password</button>
		</form>

		<?php
			if
			(
				isset($_POST["Username"]) == false
				|| isset($_POST["PasswordResetCode"]) == false
				|| isset($_POST["Password"]) == false
				|| isset($_POST["PasswordConfirm"]) == false
			)
			{
				PageWriter::displayStatusMessage("All fields are required.  The password reset code should have been sent to the email address associated with this account.");
			}
			else
			{
				$username = $_POST["Username"];
				$passwordResetCode = $_POST["PasswordResetCode"];
				$password = $_POST["Password"];
				$passwordConfirm = $_POST["PasswordConfirm"];

				$persistenceClient = $_SESSION["PersistenceClient"];
				$userFound = $persistenceClient->userGetByUsername($username);

				if ($userFound == null || $userFound->passwordResetCode != $passwordResetCode)
				{
					PageWriter::displayStatusMessage("Either the username or password reset code entered were invalid.");
				}
				else
				{
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
						PageWriter::displayStatusMessage($messageInitial);
					}
					else
					{
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
							PageWriter::displayStatusMessage("Password does not meet requirements.  " . $messagePasswordCriteria);
						}
						else
						{
							$passwordConfirmationEntered = $_POST["PasswordConfirm"];
							if ($passwordEntered != $passwordConfirmationEntered)
							{
								PageWriter::displayStatusMessage($messagePasswordsMustMatch);
							}
							else
							{
								$passwordSalt = MathHelper::randomCodeGenerate();
								$passwordHashed = User::passwordHashWithSalt($passwordEntered, $passwordSalt);
								$userFound->passwordSalt = $passwordSalt;
								$userFound->passwordHashed = $passwordHashed;
								$userFound->passwordResetCode = null;
								$persistenceClient->userSave($userFound);

								$deviceAddress = $_SERVER["SERVER_ADDR"];
								$now = new DateTime();
								$sessionNew = new Session(null, $userFound, $deviceAddress, $now, $now, null);
								$persistenceClient->sessionSave($sessionNew);

								$_SESSION["Session"] = $sessionNew;
								header("Location: User.php");

								$databaseConnection->close();
							}
						}
					}
				}
			}

		?>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
