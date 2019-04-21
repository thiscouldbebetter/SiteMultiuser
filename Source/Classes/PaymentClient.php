<?php
class PaymentClient
{
	public static function fromConfigString()
	{
		// hack - Assuming Square.
		$configuration = $_SESSION["Configuration"];
		$configString = $configuration->paymentClientConfig;
		return PaymentClientSquare::fromConfigString($configString);
	}
}
?>
