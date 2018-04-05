<?php include "Common.php"; ?>

<html>
<body>

<form action="UserLogin.php" method="post">
	<label>Username:</label><br />
	<input name="Username"></input><br />
	<label>Password:</label><br />
	<input name="Password" type="password"></input><br />
	<button type="submit">Log In</button>
	<a href="UserNew.php">Register as New User</a>
</form>

<html>
<body>

<?php
	if (isset($_POST["Username"]) == false || isset($_POST["Password"]) == false)
	{
		echoStatusMessageAndExit("Enter a valid username and password to log in.");
	}
		
	$usernameEntered = $_POST["Username"];
	$passwordEntered = $_POST["Password"];
	
	if ($usernameEntered == "" || $passwordEntered == "")
	{
		echoStatusMessageAndExit("Enter a valid username and password to log in.");
	}

	$persistenceClient = $_SESSION["PersistenceClient"];
	$userFound = $persistenceClient->userGetByUsername($usernameEntered);
	
	if ($userFound == null)
	{		
		echoStatusMessageAndExit("Username or password not valid.");
	}
	else 
	{
		$passwordEnteredHashed = $userFound->passwordHash($passwordEntered);
		if ($userFound->passwordHashed != $passwordEnteredHashed)
		{
			echoStatusMessageAndExit("Username or password not valid.");	
		}
	}

	$_SESSION["UserLoggedIn"] = $userFound;	
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