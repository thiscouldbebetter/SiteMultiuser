<?php include("Common.php"); ?>
<?php Session::verify($configuration); ?>

<html>
<head>
	<?php PageWriter::elementHeadWrite("Order Checkout"); ?>

	<!-- Square -->
	<script type="text/javascript" src="https://js.squareup.com/v2/paymentform"></script>
	<script type="text/javascript">
		<?php
			$configString = $configuration->paymentClientConfig;
			$configLookup = JSONEncoder::jsonStringToLookup($configString);
			$applicationID = $configLookup["applicationID"];
			$locationID = $configLookup["locationID"];
		?>
		var applicationId = "<?php echo $applicationID; ?>";
		var locationId = "<?php echo $locationID; ?>";
	</script>
	<script type="text/javascript" src="Square/sqpaymentform-basic.js"></script>

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
				$productBatchesInOrder = $orderCurrent->productBatches;
				$persistenceClient = $_SESSION["PersistenceClient"];
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

		<?php if ( count($orderCurrent->productBatches) == 0) : ?>

			<div id="divStatusMessage">There are no products in this order!</div>
			<br />

		<?php elseif ($orderCurrent->priceTotal($productsAll) == 0) : ?>

			<div id="divStatusMessage">This order is free, and requires no payment.</div>
			<br />
			<a href="OrderPaymentExecuteFree.php">Complete Order</a><br />

		<?php else : ?>
			<div id="divStatusMessage">This order is ready for payment.</div>
			<br />

			<!-- Square -->

			<div>Pay with Square:</div>

			<div id="form-container">
			  <div id="sq-ccbox">
				<!--
				  Be sure to replace the action attribute of the form with the path of
				  the Transaction API charge endpoint URL you want to POST the nonce to
				  (for example, "/process-card")
				-->
				<form id="nonce-form" novalidate action="OrderPaymentExecuteSquare.php" method="post">
				  <fieldset>
					<span class="label">Card Number:</span>
					<div id="sq-card-number"></div>

					<div class="third">
					  <span class="label">Expiration:</span>
					  <div id="sq-expiration-date"></div>
					</div>

					<div class="third">
					  <span class="label">CVV:</span>
					  <div id="sq-cvv"></div>
					</div>

					<div class="third">
					  <span class="label">Postal Code:</span>
					  <div id="sq-postal-code"></div>
					</div>
				  </fieldset>

				  <button id="sq-creditcard" class="button-credit-card" onclick="requestCardNonce(event)">Pay with Square</button>

				  <div id="error"></div>

				  <!--
					After a nonce is generated it will be assigned to this hidden input field.
				  -->
				  <input type="hidden" id="card-nonce" name="nonce">
				</form>
			  </div> <!-- end #sq-ccbox -->

			</div> <!-- end #form-container -->

			<br />

			<script type="text/javascript">
				document.addEventListener("DOMContentLoaded", function(event) {
					if (SqPaymentForm.isSupportedBrowser()) {
					  paymentForm.build();
					  paymentForm.recalculateSize();
					}
				});
			</script>

			<!-- end Square -->
		<?php endif; ?>

		<a href="User.php">Back to Account Details</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
