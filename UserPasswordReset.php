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
			<button type="submit">Reset Password</button>
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
						$passwordResetCode = User::passwordResetCodeGenerate();

						$userFound->passwordResetCode = $passwordResetCode;
						$persistenceClient->userSave($userFound);

						$notificationMessage = 
							"A request has been made to reset your password.\n\n"
							. "If you made this request, enter the code below to reset your password:\n"
							. "\n"
							. $passwordResetCode
							. "\n\n"
							. "If you did not make this request, "
							. "it may indicate that an attempt has been made to hack your account.\n";

						$notificationToSend = new Notification($userFound->emailAddress, "Password Reset", $notificationMessage);
						$persistenceClient->notificationSave($notificationToSend);
						$notificationToSend->sendAsEmail();

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
