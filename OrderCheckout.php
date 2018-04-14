<?php include "Common.php"; ?>
<?php PageWriter::sessionVerify(); ?>

<html>
<head>
	<?php PageWriter::elementHeadWrite("Account Details"); ?>
	<script src="https://www.paypalobjects.com/api/checkout.js"></script>
</head>
<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Checkout Order:</b></label><br />
		<div>
		<?php 
			$session = $_SESSION["Session"];
			$userLoggedIn = $session->user;
			$orderCurrent = $userLoggedIn->orderCurrent;
			$orderID = $orderCurrent->orderID;
			$productBatchesInOrder = $orderCurrent->productBatches;
			$persistenceClient = $_SESSION["PersistenceClient"];
			$paypalClientData = $persistenceClient->paypalClientDataGet();
			$productsAll = $persistenceClient->productsGetAll();
			$numberOfBatches = count($productBatchesInOrder);

			if ($numberOfBatches == 0)
			{
				echo "(no items)";
			}
			else
			{
				echo("<ul>");
				for ($i = 0; $i < count($orderCurrent->productBatches); $i++)
				{
					$productBatch = $orderCurrent->productBatches[$i];
					$productID = $productBatch->productID;
					$product = $productsAll[$productID];
					$productName = $product->name;
					$quantity = $productBatch->quantity;
					$productBatchPrice = $productBatch->price($productsAll);
					$productAsString = $productName . " (x" . $quantity . ") = $" . $productBatchPrice;
					$productAsListItem = "<li>" . $productAsString . "</li>";
					echo($productAsListItem);
				}
				echo("</ul>");
			}
		?>
		</div>

		<label>Total Price: </label><label>$<?php echo $orderCurrent->priceTotal($productsAll); ?></label><br /><br />

		<a href="OrderCurrent.php">Modify Order</a><br /><br />

		<div id="divStatusMessage">This order is ready for payment.</div>

		<div id="divPaypalButton"></div>
		<script>
			paypal.Button.render
			({
				env: "<?php echo($paypalClientData->isProductionEnabled == 1 ? "production" : "sandbox"); ?>",
				client: 
				{ 
					sandbox: "<?php echo($paypalClientData->clientIDSandbox); ?>",
					production: "<?php echo($paypalClientData->clientIDProduction); ?>" 
				},
				commit: true, // Show a 'Pay Now' button.
				style: { color: "gold", size: "small" },
				payment: function(data, actions) 
				{
					var transactionTotal = <?php echo( $orderCurrent->priceTotal($productsAll) ); ?>;
					var transactionTotalAsString = "" + transactionTotal;
					var transactionAmount = { total: transactionTotalAsString, currency: "USD" };
					var transaction = { amount: transactionAmount };
					var payment = { payment: { transactions: [ transaction ] } };
					var returnValue = actions.payment.create(payment);
					return returnValue;
				},

				onAuthorize: function(data, actions) 
				{
					return actions.payment.execute().then(function(payment) 
					{
						var divStatusMessage = document.getElementById("divStatusMessage");
						divStatusMessage.innerHTML = "Payment for this order was successful.";
						window.location = "OrderComplete.php";
					});
				},

				onCancel: function(data, actions) 
				{
					divStatusMessage.innerHTML = "Payment for this order has been cancelled.";
				},

				onError: function(err) 
				{
					divStatusMessage.innerHTML = "An error occurred while processing payment for this order.";
				}
			}, "#divPaypalButton");
		</script><br />

		<a href="User.php">Back to Account Details</a><br />

	</div>

</body>
</html>
