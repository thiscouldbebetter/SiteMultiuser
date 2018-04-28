<?php include "Common.php"; ?>
<?php Session::verify(); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Order Complete"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label>Payment for the current order was cancelled.</label><br />
		<a href="OrderDetails.php">Return to Order Details</a><br />
		<a href="User.php">Return to Account Details</a>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
