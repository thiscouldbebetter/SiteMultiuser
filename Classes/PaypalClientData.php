<?php

class PaypalClientData
{
	public $clientIDSandbox;
	public $clientIDProduction;
	public $isProductionEnabled;

	public function __construct($clientIDSandbox, $clientIDProduction, $isProductionEnabled)
	{
		$this->clientIDSandbox = $clientIDSandbox;
		$this->clientIDProduction = $clientIDProduction;
		$this->isProductionEnabled = $isProductionEnabled;
	}
}

?>