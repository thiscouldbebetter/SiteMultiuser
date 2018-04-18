<?php include "Common.php"; ?>

<html>

<head><?php PageWriter::elementHeadWrite("Verify New Account"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">

		<form action="UserNewVerify.php" method="post">
			<label><b>Verify New Account:</b></label><br />
			<br />
			<label>Verification Code:</label><br />
			<input name="VerificationCode"></input><br />
			<br />
			<button type="submit">Verify New Account</button>
		</form>		

		<?php
		
			$userToCreate = $_SESSION["UserToCreate"];

			if ($userToCreate == null)
			{
				PageWriter::displayStatusMessage("There is no new user to verify!");
			}
			else
			{
				$messageInstructions =
					"Enter the verification code that was sent to the specified email address to complete the account creation process.";

				if(isset($_POST["VerificationCode"]) == false)
				{
					PageWriter::displayStatusMessage($messageInstructions);
				}
				else
				{
					$verificationCodeEntered = $_POST["VerificationCode"];
					$verificationCodeFromSession = $_SESSION["VerificationCode"];
					
					if ($verificationCodeEntered == "")
					{
						PageWriter::displayStatusMessage($messageInstructions);
					}
					else if ($verificationCodeEntered != $verificationCodeFromSession)
					{
						PageWriter::displayStatusMessage("The entered verification code is not valid.");
					}
					else
					{
						$persistenceClient = $_SESSION["PersistenceClient"];
						$persistenceClient->userSave($userToCreate);

						$now = new DateTime();
						$sessionNew = new Session(null, $userToCreate, $now, $now, null);						
						$persistenceClient->sessionSave($sessionNew);

						$_SESSION["Session"] = $sessionNew;
						header("Location: User.php");
					}
				}
			}

		?>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
