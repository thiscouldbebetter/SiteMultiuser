<?php

include("JSONEncoder.php");

class PaymentClientSquare
{
	public $accessToken;
	public $locationID;

	public function __construct($accessToken, $locationID)
	{
		$this->accessToken = $accessToken;
		$this->locationID = $locationID;
	}
	
	public static function fromConfigString($credentialsAsJSON)
	{		
		$credentialsAsLookup = JSONEncoder::jsonStringToLookup($credentialsAsJSON);
		$accessToken = $credentialsAsLookup["accessToken"];
		$locationID = $credentialsAsLookup["locationID"];
	
		return new PaymentClientSquare
		(
			$accessToken, $locationID
		);
	}
	
	public function payForOrderWithCardNonce($order, $cardNonce)
	{
		$paymentItemsAsLookups = array();

		$persistenceClient = $_SESSION["PersistenceClient"];
		$productsAll = $persistenceClient->productsGetAll();
		$priceSubtotal = $order->priceSubtotal($productsAll);
		$priceTotal = $order->priceTotal($productsAll);
				
		// Adapted from
		/// https://docs.connect.squareup.com/payments/sqpaymentform/setup#step-8-take-a-payment		

		/*
		curl --header "Content-Type: application/json" \
			 --header "Authorization: YOUR_ACCESS_TOKEN" \
			 --header "Accept: application/json"\
			 --request POST \
			 --data '{
				"idempotency_key": "UUID",
				"amount_money": {
				  "amount": CHARGE_AMOUNT,
				  "currency": "CURRENCY_CODE"
				},
				"card_nonce": "CARD_NONCE_FROM_PAYMENT_FORM"
			  }' \
			  https://connect.squareup.com/v2/locations/{YOUR_LOCATION_ID}/transactions
		*/		
		
		$idempotencyKey = MathHelper::randomCodeGenerate();
		$currencyCode = "USD";
		$priceTotalInCents = $priceTotal * 100;
		$amountMoney = array
		(
			"amount" => $priceTotalInCents,
			"currency" => $currencyCode,
		);

		$requestAsLookup = array
		(
			"idempotency_key" => $idempotencyKey,
			"amount_money" => $amountMoney,
			"card_nonce" => $cardNonce
		);

		$requestBody = JSONEncoder::lookupToJSONString($requestAsLookup);
		
		$headersAsStrings = array
		(
			"Content-Type: application/json",
			"Authorization: Bearer " . $this->accessToken
		);

		$paymentCreateURL = "https://connect.squareup.com/v2/locations/" . $this->locationID . "/transactions";
		$paymentCreateResponse = WebClient::responseGetForRequest
		(
			$paymentCreateURL, "POST", $headersAsStrings, $requestBody
		);
		//echo "paymentCreateResponse is " . $paymentCreateResponse;
		
		$responseAsLookup = JSONEncoder::jsonStringToLookup($paymentCreateResponse);
		
		if (isset($responseAsLookup["errors"]) == false)
		{
			$paymentID = "todo"; // todo
		}
		else
		{
			$errors = $responseAsLookup["errors"];
			//echo "errors is " . $errors[0]["detail"];
			$paymentID = null;
		}

		return $paymentID;
	}
}	
?>
