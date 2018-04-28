<?php include "Common.php"; ?>
<?php Session::verify(); ?>
<?php	
	$persistenceClient = $_SESSION["PersistenceClient"];
	$paypalClient = PaypalClient::fromConfiguration($configuration);
	$session = $_SESSION["Session"];
	$userLoggedIn = $session->user;
	$order = $userLoggedIn->orderCurrent;
	$storeURL = $configuration["StoreURL"];
	$paymentExecuteURL = $storeURL . "/OrderComplete.php";
	$paymentCancelURL = $storeURL . "/OrderPaymentCancelled.php";
	$paymentCreateResponse = $paypalClient->payForOrder($order, $paymentExecuteURL, $paymentCancelURL);
	// todo
	//$paypalClient->paymentExecuteFromJSON($paymentCreateResponse);
	$paymentCreateResponseAsLookup = JSONEncoder::jsonStringToLookup($paymentResponse);
	$paypalPaymentID = $paymentCreateResponseAsLookup["id"];
	header("Content-Type: application/json");
	echo "{\"id\":\"" . $paypalPaymentID. "\"}";
?>
