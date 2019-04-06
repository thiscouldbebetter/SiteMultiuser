<?php include "Common.php"; ?>
<?php Session::verify(); ?>

<html>
<head>
	<?php PageWriter::elementHeadWrite("Order Payment"); ?>	
</head>
	
<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
	
		<?php

			// The following code was adapted from an example at the URL
			// https://docs.connect.squareup.com/payments/sqpaymentform/setup
			
			// Fail if the payment form didn't send a value for `nonce` to the server
			$nonce = $_POST['nonce'];
			if (is_null($nonce)) {
			  error_log("No nonce was passed to the payment execution page.");
			  echo "Invalid card data!";
			  http_response_code(422);
			  return;
			}
			
			// end code from Square

			$session = $_SESSION["Session"];
			$userLoggedIn = $session->user;
			$orderCurrent = $userLoggedIn->orderCurrent;
			$persistenceClient = $_SESSION["PersistenceClient"];
						
			$paymentClient = PaymentClient::fromConfigString();

			$paymentID = $paymentClient->payForOrderWithCardNonce($orderCurrent, $nonce);
			
			if ($paymentID != null)
			{
				$orderCurrent->complete($paymentID);
				$persistenceClient->orderSave($orderCurrent);
				$userLoggedIn->orderCurrent = Order::fromUserID($orderCurrent->userID);
				echo "Payment successful!";
				echo "<br /><br />";
				echo "<a href='UserLicenses.php'>Show All User Licenses</a>";
			}
			else
			{
				echo "Payment failed!  The order could not be completed.";
				echo "<br /><br />";
				echo "<a href='OrderCheckout.php'>Back to Checkout</a>";
			}
		?>
		
		<br />
				
	</div>	

	<?php PageWriter::footerWrite(); ?>
		
</body>
</html>
