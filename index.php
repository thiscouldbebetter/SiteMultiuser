<html>
<?php include("Classes/PageWriter.php"); ?>
<?php $configuration = include("Pages/Configuration.php"); ?>

<head>
	<title><?php echo ($configuration["SiteTitle"]); ?> - Welcome</title>
	<link rel='stylesheet' href='Pages/Style.css'>
</head>

<body>

	<script id='scriptHeader' type='text/javascript' src='Pages/Header.js'></script>

	<div class="divCentered">
		<label><b>Welcome!</b></label><br />
		<br />
		<a href="Pages/UserLogin.php">Log In</a><br />
	</div>

	<script id='scriptHeader' type='text/javascript' src='Pages/Footer.js'></script>

</body>

</html>
