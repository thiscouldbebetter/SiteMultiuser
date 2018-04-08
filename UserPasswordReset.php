<?php include "Common.php"; ?>

<html>

<?php PageWriter::elementHeadWrite("Reset Password"); ?>

<body>

	<div>

		<form action="UserPasswordReset.php" method="post">
			<label><b>Reset Password</b></label><br />
			<label>Username:</label><br />
			<input name="Username"></input><br />
			<label>Email Address:</label><br />
			<input name="EmailAddress"></input><br />
			<button type="submit">Reset Password</button>
		</form>

		<?php
			$messageInstructions = 
				"Enter a valid username and its associated email address."
				. "  A reset password link will be sent via email to the specified address.";

			if (isset($_POST["Username"]) == false || isset($_POST["EmailAddress"]) == false)
			{
				echoStatusMessageAndExit($messageInstructions);
			}
				
			$usernameEntered = $_POST["Username"];
			$emailAddressEntered = $_POST["EmailAddress"];
			
			if ($usernameEntered == "" || $emailAddressEntered == "")
			{
				echoStatusMessageAndExit($messageInstructions);
			}

			$persistenceClient = $_SESSION["PersistenceClient"];
			$userFound = $persistenceClient->userGetByUsername($usernameEntered);
			
			$messageUsernameOrEmailAddressInvalid = 
				"Either no user with the specified username exists, "
				. "or the email address specified did not match the one associated with the username.";
			
			if ($userFound == null)
			{		
				echoStatusMessageAndExit($messageUsernameOrEmailAddressInvalid);
			}
			else 
			{
				if ($userFound->emailAddress != $emailAddressEntered)
				{
					echoStatusMessageAndExit($messageUsernameOrEmailAddressInvalid);	
				}
			}
				
			$passwordResetCode = User::passwordResetCodeGenerate();
			
			$userFound->passwordResetCode = $passwordResetCode;
			$persistenceClient->userSave($userFound);
			$passwordResetURL = "http://localhost/OnlineStore/UserPasswordChange.php?username=" . $userFound->username . "&passwordResetCode=" . $passwordResetCode; // todo
				
			$notificationMessage = 
				"A request has been made to reset your password.\n"
				. "If you made this request, visit the link below to reset your password:\n"
				. "\n"
				. $passwordResetURL
				. "\n\n"
				. "If you did not make this request, "
				. "it may indicate that an attempt has been made to hack your account.\n";

			$notificationToSend = new Notification($userFound->emailAddress, "Password Reset", $notificationMessage);
			$persistenceClient->notificationSave($notificationToSend); // todo
			
			echoStatusMessageAndExit("A password reset link has been sent via email to the specified address.");
			
			function echoStatusMessageAndExit($statusMessage)
			{
				echo $statusMessage;		
				die();
			}		
			
		?>
	</div>

</body>
</html>
