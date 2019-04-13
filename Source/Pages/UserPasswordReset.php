<?php include "Common.php"; ?>

<html>

<head><?php PageWriter::elementHeadWrite("Reset Password"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">

		<form action="UserPasswordReset.php" method="post">
			<label><b>Reset Password:</b></label><br /><br />
			<label>Username:</label><br />
			<input name="Username" autofocus></input><br />
			<label>Email Address:</label><br />
			<input name="EmailAddress"></input><br />
			<br />
			<button type="submit">Reset Password</button><br />
			<br />
			<a href="UserLogin.php">Log In</a>
		</form>

		<?php
			$messageInstructions =
				"Enter a valid username and its associated email address."
				. "  A password reset code will be sent via email to the specified address.";

			if (isset($_POST["Username"]) == false || isset($_POST["EmailAddress"]) == false)
			{
				PageWriter::displayStatusMessage($messageInstructions);
			}
			else
			{
				$usernameEntered = $_POST["Username"];
				$emailAddressEntered = $_POST["EmailAddress"];

				if ($usernameEntered == "" || $emailAddressEntered == "")
				{
					PageWriter::displayStatusMessage($messageInstructions);
				}
				else
				{
					$persistenceClient = $_SESSION["PersistenceClient"];
					$userFound = $persistenceClient->userGetByUsername($usernameEntered);

					$messageUsernameOrEmailAddressInvalid =
						"Either no user with the specified username exists, "
						. "or the email address specified did not match the one associated with the username.";

					if ($userFound == null)
					{
						PageWriter::displayStatusMessage($messageUsernameOrEmailAddressInvalid);
					}
					else if ($userFound->emailAddress != $emailAddressEntered)
					{
						PageWriter::displayStatusMessage($messageUsernameOrEmailAddressInvalid);
					}
					else
					{
						$passwordResetCode = MathHelper::randomCodeGenerate();

						$userFound->passwordResetCode = $passwordResetCode;
						$persistenceClient->userSave($userFound);

						$notificationMessage =
							"A request has been made to reset your password.\n\n"
							. "If you made this request, enter the code below when prompted to reset your password:\n"
							. "\n"
							. $passwordResetCode
							. "\n\n"
							. "If you did not make this request, "
							. "it may indicate that an attempt has been made to hack your account.\n";

						$now = new DateTime();
						$notificationToSend = new Notification(null, $userFound->emailAddress, "Password Reset", $notificationMessage, $now, null);
						$persistenceClient->notificationSave($notificationToSend);
						$notificationToSend->sendAsEmail($persistenceClient);
						header("Location: UserPasswordChange.php");

						PageWriter::displayStatusMessage("A password reset link has been sent via email to the specified address.");
					}
				}
			}
		?>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
