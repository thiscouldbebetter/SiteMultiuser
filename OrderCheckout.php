<?php include "Common.php"; ?>
<?php Session::verify(); ?>
<?php $configuration = include("Configuration.php"); ?>

<html>
<head>
	<?php PageWriter::elementHeadWrite("Order Checkout"); ?>
	<script src="https://www.paypalobjects.com/api/checkout.js"></script>
</head>
<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Checkout Order:</b></label><br /><br />
		<label>Items in Order:</label>
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
						$productBatchPrice = bcdiv($productBatchPrice, 1, 2);
						$productAsString = $productName . " (x" . $quantity . ") = $" . $productBatchPrice;
						$productAsListItem = "<li>" . $productAsString . "</li>";
						echo($productAsListItem);
					}
					echo("</ul>");
				}
			?>
			<br />
			<label>Subtotal: </label><label>$<?php echo ( bcdiv($orderCurrent->priceSubtotal($productsAll), 1, 2) ); ?></label><br /><br />

			<label>Promotion: </label>
			<label>
				<?php
					$promotion = $orderCurrent->promotion;
					if ($promotion == null)
					{
						echo "(none)";
					}
					else
					{
						echo $promotion->description;
						$doesPromotionApplyToOrder = $promotion->doesApplyToOrder($orderCurrent);
						echo "<br />";
						if ($doesPromotionApplyToOrder == true)
						{
							echo "<br />Promotion Discount: $" . bcdiv($promotion->discount, 1, 2);
						}
						else
						{
							echo "(WARNING - PROMOTION DOES NOT APPLY TO THIS ORDER!)";
						}
					}
				?>
			</label><br />
			<br />
			<label>Total Price: </label><label>$<?php echo bcdiv( $orderCurrent->priceTotal($productsAll), 1, 2 ); ?></label><br /><br />

		</div>

		<a href="OrderDetails.php">Modify Order</a><br /><br />

		<div id="divStatusMessage">This order is ready for payment.</div>

		<div id="paypal-button"></div>

		<script>
			var CREATE_PAYMENT_URL  = 'OrderPayment.php';
			var EXECUTE_PAYMENT_URL = 'OrderComplete.php';

			paypal.Button.render({

				env: '<?php if ($paypalClientData->isProductionEnabled) { echo "production"; } else { echo "sandbox"; } ?>',

				commit: true, // Show a 'Pay Now' button

				payment: function() {
					return paypal.request.post(CREATE_PAYMENT_URL).then(function(data) {
						return data.id;
					});
				},

				onAuthorize: function(data) {
					return paypal.request.post(EXECUTE_PAYMENT_URL, {
						paymentID: data.paymentID,
						payerID:   data.payerID
					}).then(function() {
						window.location = "OrderComplete.php";
					});
				}

			}, '#paypal-button');
		</script>
	<br />

		<a href="User.php">Back to Account Details</a><br />

	</div>

</body>
</html>
