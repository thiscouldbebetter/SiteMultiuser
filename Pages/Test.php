<?php include "Common.php"; ?>

<html>
	<body>
		<p>
			<?php 
				$paypalClient = PaypalClient::fromConfiguration($configuration);
				$productBatchesInOrder = array
				( 
					new Order_Product(null, null, 1, 1),
					new Order_Product(null, null, 2, 2),
				);
				$now = new DateTime();
				$order = new Order(null, null, null, "InProgress", $now, $now, null, null, $productBatchesInOrder);
				$storeURL = $configuration["StoreURL"];
				$paymentCreateResponse = $paypalClient->payForOrder($order, "http://localhost", "http://localhost");
				echo("paymentCreateResponse is: " . $paymentCreateResponse);
				$paymentCreateResponseAsLookup = JSONEncoder::jsonStringToLookup($paymentCreateResponse);
				$paymentID = $paymentCreateResponseAsLookup["id"];
				$paymentRetrievedAsJSON = $paypalClient->paymentGetByID($paymentID);
				echo ("paymentRetrievedAsJSON is " . $paymentRetrievedAsJSON);
				$paymentRetrievedAsLookup = JSONEncoder::jsonStringToLookup($paymentRetrievedAsJSON);
				$payerID = $paymentRetrievedAsLookup["payer_id"];
				$paymentExecuteResponse = $paypalClient->paymentExecuteByIDAndPayerID($paymentID, $payerID);
				echo("paymentExecuteResponse is: " . $paymentExecuteResponse);
				echo "<br /><br />";
				$paypalPaymentID = $paymentResponseAsLookup["id"];
				$verificationResponse = $paypalClient->paymentVerifyByID($paypalPaymentID);
				echo ("verificationResponse is: " . $verificationResponse);
			?>
		</p>
	</body>
</html>
