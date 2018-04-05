<?php include "Common.php"; ?>

<html>
<body>

<form action="UserNew.php" method="post">
	<label>Username:</label><br />
	<input name="Username"></input><br />
	<label>Email Address:</label><br />
	<input name="EmailAddress"></input><br />		
	<label>Password:</label><br />
	<input name="Password" type="password"></input><br />
	<button type="submit">Register as New User</button>
</form>

<?php

	$passwordCharactersRequired = 12;
	$statusMessagePasswordCriteria = 
		"Password must be at least " . $passwordCharactersRequired . " characters long, "
		. "and must contain uppercase letters, lowercase letters, and numerals.";
	$statusMessageInitial = 
		"Enter a username, email, and password to create a new user.  " . $statusMessagePasswordCriteria;

	if 
	(
		isset($_POST["Username"]) == false 
		|| isset($_POST["Password"]) == false 
		|| isset($_POST["EmailAddress"]) == false
	)
	{		
		echoStatusMessageAndExit($statusMessageInitial);
	}
		
	$usernameEntered = $_POST["Username"];
	$emailAddressEntered = $_POST["EmailAddress"];
		
	if ($usernameEntered == "" || $emailAddressEntered == "")
	{
		echoStatusMessageAndExit($statusMessageInitial);
	}
	
	$isEmailAddressWellFormed = true; // todo
	
	if ($isEmailAddressWellFormed == false)
	{
		echoStatusMessageAndExit("Email address does not have a valid format.");
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
		echoStatusMessageAndExit("Password does not meet requirements.  " . $statusMessagePasswordCriteria);		
	}

	$persistenceClient = $_SESSION["PersistenceClient"];
	$userFound = $persistenceClient->userGetByUsername($usernameEntered);

	if ($userFound != null)
	{		
		echoStatusMessageAndExit("A user with the specified username already exists.  Choose another username.");
	}
	
	$passwordSalt = User::passwordSaltGenerate();
	$passwordHashed = User::passwordHashWithSalt($passwordEntered, $passwordSalt);
	
	$userNew = new User(null, $usernameEntered, $emailAddressEntered, $passwordSalt, $passwordHashed, 1, array());
	$persistenceClient->userSave($userNew);

	$_SESSION["UserLoggedIn"] = $userNew;
	header("Location: User.php");
	
	$databaseConnection->close();		
	
	function echoStatusMessageAndExit($statusMessage)
	{
		echo $statusMessage;		
		die();
	}
	
?>

</body>
</html>
