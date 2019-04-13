<html>
<?php $configuration = include("../Configuration.php"); ?>

<head>
	<title><?php echo ($configuration["SiteTitle"]); ?> - Welcome</title>
	<link rel='stylesheet' href='Style.css'>
</head>

<body>

	<script id='scriptHeader' type='text/javascript' src='Header.js'></script>

	<div class="divCentered">
		<label><b>Welcome!</b></label><br />
		<br />
		<a href="UserLogin.php">Log In</a><br />
	</div>

	<script id='scriptHeader' type='text/javascript' src='Footer.js'></script>

</body>

</html>
