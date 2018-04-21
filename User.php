<?php include("Common.php"); ?>
<?php Session::verify(); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Account Details"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Acount Details:</b></label><br /><br />
		<label>Username:</label>
		<label>
		<?php
			$session = $_SESSION["Session"];
			$userLoggedIn = $session->user;
			echo($userLoggedIn->username);
		?>
		</label><br /><br />

		<label>Licenses:</label>
		<?php
			$licensesOwned = $userLoggedIn->licenses;
			$numberOfLicensesOwned = count($licensesOwned);
			$persistenceClient = $_SESSION["PersistenceClient"];
			$transfersIncoming = $persistenceClient->licensesGetByTransferTarget($userLoggedIn->username, $userLoggedIn->emailAddress);
			$numberOfTransfersIncoming = count($transfersIncoming);
			echo("(" . $numberOfLicensesOwned . " owned, " . $numberOfTransfersIncoming . " incoming transfers) ");
			echo("<a href='UserLicenses.php'>Details</a>");
		?>
		<br /><br />

		<label>Current Order:</label>
		<?php
			$orderCurrent = $userLoggedIn->orderCurrent;
			$productBatchesInOrder = $orderCurrent->productBatches;
			$numberOfBatches = count($productBatchesInOrder);
			echo("(" . $numberOfBatches . " item(s)) ");
		?>
		<a href='OrderCurrent.php'>Details</a><br />

		<br />

		<a href="ProductSummary.php">Browse Available Products</a><br />
		<br />
		<a href="UserLogout.php">Log Out</a><br />
		<a href="UserDelete.php">Delete Account</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
