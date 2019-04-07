<?php
class PaymentClient
{
	public static function fromConfigString()
	{
		// hack - Assuming Square.
		$configuration = $_SESSION["Configuration"];
		$configString = $configuration["PaymentClientConfig"];
		return PaymentClientSquare::fromConfigString($configString);
	}
}
?>
