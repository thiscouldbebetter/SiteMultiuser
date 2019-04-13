<?php include "Common.php"; ?>

<html>

<head><?php PageWriter::elementHeadWrite("New Account"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">

		<form action="UserNew.php" method="post">
			<label><b>Create New Account:</b></label><br />
			<br />
			<label>Username:</label><br />
			<input name="Username"></input><br />
			<label>Email Address:</label><br />
			<input name="EmailAddress"></input><br />
			<label>Password:</label><br />
			<input name="Password" type="password"></input><br />
			<label>Password Confirmation:</label><br />
			<input name="PasswordConfirm" type="password"></input><br />
			<br />
			<button type="submit">Create New Account</button>
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
				PageWriter::displayStatusMessage($messageInitial);
			}
			else
			{
				$usernameEntered = $_POST["Username"];
				$emailAddressEntered = $_POST["EmailAddress"];

				if ($usernameEntered == "" || $emailAddressEntered == "")
				{
					PageWriter::displayStatusMessage($messageInitial);
				}
				else
				{
					$isEmailAddressWellFormed = filter_var($emailAddressEntered, FILTER_VALIDATE_EMAIL);

					if ($isEmailAddressWellFormed == false)
					{
						PageWriter::displayStatusMessage("Email address specified did not have a valid format.");
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
						else if ($passwordEntered != $_POST["PasswordConfirm"])
						{
							PageWriter::displayStatusMessage($messagePasswordsMustMatch);
						}
						else
						{
							$persistenceClient = $_SESSION["PersistenceClient"];
							$userFound = $persistenceClient->userGetByUsername($usernameEntered);

							if ($userFound != null)
							{
								PageWriter::displayStatusMessage("A user with the specified username already exists.  Choose another username.");
							}
							else
							{
								$userFound = $persistenceClient->userGetByEmailAddress($emailAddressEntered);

								if ($userFound != null)
								{
									PageWriter::displayStatusMessage("A user with the specified email address already exists.");
								}
								else
								{
									$passwordSalt = MathHelper::randomCodeGenerate();
									$passwordHashed = User::passwordHashWithSalt($passwordEntered, $passwordSalt);
									$passwordResetCode = null;
									$isActive = 1;

									$userNew = new User
									(
										null, $usernameEntered, $emailAddressEntered,
										$passwordSalt, $passwordHashed, $passwordResetCode,
										$isActive, array()
									);
									$_SESSION["UserToCreate"] = $userNew;
									$verificationCodeGenerated = MathHelper::randomCodeGenerate();
									$_SESSION["VerificationCode"] = $verificationCodeGenerated;

									$notificationMessage =
										"A new user account associated with this email address is in the process of being created on our site.\n\n"
										. "If you made this request, enter the code below when prompted to verify the account:\n"
										. "\n"
										. $verificationCodeGenerated
										. "\n\n"
										. "If you did not make this request, "
										. "it may indicate that an attempt has been made to use your email address inappropriately.\n";

									$now = new DateTime();
									$notificationToSend = new Notification(null, $userNew->emailAddress, "New Account Verification", $notificationMessage, $now, null);
									$persistenceClient->notificationSave($notificationToSend);
									$notificationToSend->sendAsEmail($persistenceClient);

									header("Location: UserNewVerify.php");
								}
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
