<?php include "Common.php"; ?>
<?php Session::verify(); ?>

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
			$orderCompleted = $userLoggedIn->orderCurrent;
			$orderCompleted->complete();
			$persistenceClient = $_SESSION["PersistenceClient"];
			$persistenceClient->orderSave($orderCompleted);

			$licensesFromOrder = $orderCompleted->toLicenses();
			foreach ($licensesFromOrder as $license)
			{
				$persistenceClient->licenseSave($license);
				$userLoggedIn->licenses[] = $license;
			}
			$productBatchesInOrder = $orderCompleted->productBatches;
			$productsAll = $persistenceClient->productsGetAll();
			$numberOfBatches = count($productBatchesInOrder);
			if ($numberOfBatches == 0)
			{
				echo "(no items)";
			}
			else
			{
				for ($i = 0; $i < count($orderCompleted->productBatches); $i++)
				{
					$productBatch = $orderCompleted->productBatches[$i];
					$productID = $productBatch->productID;
					$product = $productsAll[$productID];
					$productName = $product->name;
					$quantity = $productBatch->quantity;
					$productAsString = $productName . " (x" . $quantity . ")";
					echo($productAsString);
					echo("<br />");
				}
			}

			//$now = new DateTime();
			//$userLoggedIn->orderCurrent = new Order(null, $userLoggedIn->userID, null, "InProgress", $now, $now, null, null, array() );
		?>
		</div>
		<br />
		<div id="divStatusMessage">This order is complete.</div>
		<br />
		<a href="User.php">Return to Account Details</a>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
