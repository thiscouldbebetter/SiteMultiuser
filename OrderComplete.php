<?php include "Common.php"; ?>
$configuration = include("Configuration.php");

<html>

<head><?php PageWriter::elementHeadWrite("Order Complete"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Completed Order:</b></label><br />
		<div>
		<?php
			$session = $_SESSION["Session"];
			$userLoggedIn = $session->user;
			$orderCurrent = $userLoggedIn->orderCurrent;
			$now = new DateTime();
			$userLoggedIn->orderCurrent = new Order(null, $userLoggedIn->userID, null, "InProgress", $now, $now, null, array() );
			$orderCurrent->complete();
			$persistenceClient = $_SESSION["PersistenceClient"];
			$persistenceClient->orderSave($orderCurrent);
			$licensesFromOrder = $orderCurrent->toLicenses();
			foreach ($licensesFromOrder as $license)
			{
				$persistenceClient->licenseSave();
				$userLoggedIn->licenses[] = $license;
			}
			$productBatchesInOrder = $orderCurrent->productBatches;
			$productsAll = $persistenceClient->productsGetAll();
			$numberOfBatches = count($productBatchesInOrder);
			if ($numberOfBatches == 0)
			{
				echo "(no items)";
			}
			else
			{
				for ($i = 0; $i < count($orderCurrent->productBatches); $i++)
				{
					$productBatch = $orderCurrent->productBatches[$i];
					$productID = $productBatch->productID;
					$product = $productsAll[$productID];
					$productName = $product->name;
					$quantity = $productBatch->quantity;
					$productAsString = $productName . " (x" . $quantity . ")";
					echo($productAsString);
					echo("<br />");
				}
			}
		?>
		</div>
		<div id="divStatusMessage">This order is complete.</div>
		<a href="User.php">Return to User Page</a>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
