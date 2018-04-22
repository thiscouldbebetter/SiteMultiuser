<?php

class PaypalClientData
{
	public $clientIDSandbox;
	public $clientSecretSandbox;
	public $clientIDProduction;
	public $clientSecretProduction;
	public $isProductionEnabled;

	public function __construct($clientIDSandbox, $clientSecretSandbox, $clientIDProduction, $clientSecretProduction, $isProductionEnabled)
	{
		$this->clientIDSandbox = $clientIDSandbox;
		$this->clientSecretSandbox = $clientSecretSandbox;
		$this->clientIDProduction = $clientIDProduction;
		$this->clientSecretProduction = $clientSecretProduction;
		$this->isProductionEnabled = $isProductionEnabled;
	}
}

?>
