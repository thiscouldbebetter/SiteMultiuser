<?php include "Common.php"; ?>

<html>
<body>

<p>User logged out.</p>

<?php	
	session_destroy();
	header("Location: UserLogin.php");
	die();
?>

</body>
</html>