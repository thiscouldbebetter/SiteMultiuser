<?php include "Common.php"; ?>

<html>
	<body>
		<p>
			<?php 
				Session::start();
				$persistenceClient = $_SESSION["PersistenceClient"];
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
				$paymentExecuteResponse = $paypalClient->paymentExecuteFromJSON($paymentCreateResponse);
				echo("paymentExecuteResponse is: " . $paymentExecuteResponse);
				echo "<br /><br />";
				$paymentResponseAsLookup = JSONEncoder::jsonStringToLookup($paymentResponse);
				$paypalPaymentID = $paymentResponseAsLookup["id"];
				$verificationResponse = $paypalClient->paymentVerifyByID($paypalPaymentID);
				echo ("verificationResponse is: " . $verificationResponse);
			?>
		</p>
	</body>
</html>
