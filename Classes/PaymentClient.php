<?php
class PaymentClient
{	
	public static function fromConfigString($configString)
	{
		// hack - Assuming Square.
		$configuration = include("Configuration.php");
		$configString = $configuration["PaymentClientConfig"];
		return PaymentClientSquare::fromConfigString($configString);
	}
}	
?>
