<?php include "Common.php"; ?>
<?php Session::verify(); ?>
<?php	
	$paymentID = $_POST["paymentID"];
	$payerID = $_POST["payerID"];
	$paypalClient = PaypalClient::fromConfiguration($configuration);
	$wasExecuteSuccessful = $paypalClient->paymentExecuteFromIDAndPayerID($paymentID, $payerID);
	if ($wasExecuteSuccessful == true)
	{
		$session = $_SESSION["Session"];
		$userLoggedIn = $session->user;
		$orderCurrent = $userLoggedIn->orderCurrent;
		$orderCurrent->paymentID = $paymentID;
	}
?>
