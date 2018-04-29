<?php include "Common.php"; ?>
<?php Session::verify(); ?>
<?php	
	$persistenceClient = $_SESSION["PersistenceClient"];
	$paypalClient = PaypalClient::fromConfiguration($configuration);
	$session = $_SESSION["Session"];
	$userLoggedIn = $session->user;
	$order = $userLoggedIn->orderCurrent;
	$storeURL = $configuration["StoreURL"];
	$paymentCreateResponse = $paypalClient->payForOrder($order);
	$paymentCreateResponseAsLookup = JSONEncoder::jsonStringToLookup($paymentCreateResponse);
	$paypalPaymentID = $paymentCreateResponseAsLookup["id"];
	header("Content-Type: application/json");
	echo "{\"id\":\"" . $paypalPaymentID. "\"}";
?>
