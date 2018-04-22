<?php include "Common.php"; ?>
<?php $configuration = include("Configuration.php"); ?>

<html>
	<body>
		<p>
			<?php 
				Session::start();
				$persistenceClient = $_SESSION["PersistenceClient"];
				$paypalClientData = $persistenceClient->paypalClientDataGet();
				$clientID = $paypalClientData->clientIDSandbox;
				$clientSecret = $paypalClientData->clientSecretSandbox;
				$paypalClient = new PaypalClient($clientID, $clientSecret);
				$productBatchesInOrder = array
				( 
					new Order_Product(null, null, 1, 1),
					new Order_Product(null, null, 2, 2),
				);
				$now = new DateTime();
				$order = new Order(null, null, null, "InProgress", $now, $now, null, null, $productBatchesInOrder);
				$storeURL = $configuration["StoreURL"];
				$paymentResponse = $paypalClient->payForOrder($order, "http://localhost", "http://localhost");
				echo("paymentResponse is: " . $paymentResponse);
				$paymentResponseAsLookup = JSONEncoder::jsonStringToLookup($paymentResponse);
				$paypalPaymentID = $paymentResponseAsLookup["id"];
				$verificationResponse = $paypalClient->paymentVerify($paypalPaymentID);
				echo ("verificationResponse is: " . $verificationResponse);
			?>
		</p>
	</body>
</html>
