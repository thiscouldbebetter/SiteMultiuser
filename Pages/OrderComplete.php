<?php include "Common.php"; ?>
<?php Session::verify(); ?>

<html>
<body>

	<?php
		$paymentID = $_POST["paymentID"];
		$paypalClient = PaypalClient::fromConfiguration($configuration);
		$isPaymentValid = $paypalClient->paymentVerifyByID($paymentID);

		if ($isPaymentValid == true)
		{
			$session = $_SESSION["Session"];
			$userLoggedIn = $session->user;
			$orderCompleted = $userLoggedIn->orderCurrent;
			$orderCompleted->complete($paymentID);
			$persistenceClient = $_SESSION["PersistenceClient"];
			$persistenceClient->orderSave($orderCompleted);

			$licensesFromOrder = $orderCompleted->toLicenses();
			foreach ($licensesFromOrder as $license)
			{
				$persistenceClient->licenseSave($license);
				$userLoggedIn->licenses[] = $license;
			}
		}
	?>

</body>
</html>
