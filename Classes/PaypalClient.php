<?php

include("JSONEncoder.php");

class PaypalClient
{
	public $isProductionModeEnabled;
	public $paypalURLRoot;
	public $clientID;
	public $clientSecret;

	public function __construct($isProductionModeEnabled, $paypalURLRoot, $clientID, $clientSecret)
	{
		$this->isProductionModeEnabled = $isProductionModeEnabled;
		$this->paypalURLRoot = $paypalURLRoot;
		$this->clientID = $clientID;
		$this->clientSecret = $clientSecret;
	}

	public static function fromConfiguration($configuration)
	{
		$isProductionModeEnabled = $configuration["PaypalProductionModeEnabled"];
		if ($isProductionModeEnabled == true)
		{
			$returnValue = new PaypalClient
			(
				$isProductionModeEnabled,
				"https://api.paypal.com/v1/",
				$configuration["PaypalClientIDProduction"],
				$configuration["PaypalClientSecretProduction"]
			);
		}
		else
		{
			$returnValue = new PaypalClient
			(
				$isProductionModeEnabled,
				"https://api.sandbox.paypal.com/v1/",
				$configuration["PaypalClientIDSandbox"],
				$configuration["PaypalClientSecretSandbox"]
			);
		}

		return $returnValue;
	}

	public function accessToken()
	{
		$url = $this->paypalURLRoot . "oauth2/token";
		$requestBody = "grant_type=client_credentials";
		$clientIDColonSecret = $this->clientID . ":" . $this->clientSecret;
		$clientIDColonSecretAsBase64 = base64_encode($clientIDColonSecret);
		$headerAccept = "Accept: application/json";
		$headerAuthorization = "Authorization: Basic " . $clientIDColonSecretAsBase64;
		$headersAsStrings = array( $headerAccept, $headerAuthorization );
		$response = WebClient::responseGetForRequest($url, "POST", $headersAsStrings, $requestBody);
		$responseAsLookup = JSONEncoder::jsonStringToLookup($response);
		$accessToken = $responseAsLookup["access_token"];
		return $accessToken;
	}
	
	public function payForOrder($order, $paymentExecuteURL, $paymentCancelURL)
	{
		$paymentItemsAsLookups = array();

		$currencyCode = "USD";

		$persistenceClient = $_SESSION["PersistenceClient"];
		$productsAll = $persistenceClient->productsGetAll();

		foreach ($order->productBatches as $productBatch)
		{
			$productID = $productBatch->productID;
			$product = $productsAll[$productID];
			$productBatchAsLookup = array 
			(
				"quantity" => $productBatch->quantity,
				"name" => $product->name,
				"price" => $product->price, // Unit price, not batch price.
				"currency" => $currencyCode,
				"description" => $product->name,
				"tax" => "0"
			);

			$paymentItemsAsLookups[] = $productBatchAsLookup;
		};

		$priceSubtotal = $order->priceSubtotal($productsAll);
		$priceTotal = $order->priceTotal($productsAll);

		$transactionAmount = array
		(
			"total" => $priceTotal,
			"currency" => $currencyCode,
			"details" => array
			(
				"subtotal" => $priceSubtotal,
				"shipping" => "0",
				"tax" => "0",
				"shipping_discount" => "0"
			)
		);

		$requestAsLookup = array
		(
			"intent" => "sale",	
			"redirect_urls" => array
			(
				"return_url" => $paymentExecuteURL,
				"cancel_url" => $paymentCancelURL
			),
			"payer" => array
			(
				"payment_method" => "paypal"
			),
			"transactions" =>
			[
				array
				(
					"amount" => $transactionAmount,
					"item_list" => array
					(
						"items" => $paymentItemsAsLookups
					),
					"description" => "(none)",
					"invoice_number" => "(none)",
					"custom" => ""
				)
			]
		);

		$requestBody = JSONEncoder::lookupToJSONString($requestAsLookup);

		$accessToken = $this->accessToken();
		$headersAsStrings = array
		(
			"Content-Type: application/json",
			"Authorization: Bearer " . $accessToken,
		);

		$paymentCreateURL = $this->paypalURLRoot . "payments/payment";
		$paymentCreateResponse = WebClient::responseGetForRequest
		(
			$paymentCreateURL, "POST", $headersAsStrings, $requestBody
		);

		return $paymentCreateResponse;
	}

	public function paymentExecuteFromJSON($paymentAsJSON)
	{
		// todo - Approval first.

		$paymentAsLookup = JSONEncoder::jsonStringToLookup($paymentAsJSON);
		$paypalPaymentID = $paymentAsLookup["id"];
		$paymentExecuteURL = $this->paypalURLRoot . "payments/payment/" . $paypalPaymentID;

		$accessToken = $this->accessToken();
		$headersAsStrings = array
		(
			"Content-Type: application/json",
			"Authorization: Bearer " . $accessToken,
		);

		$payerID = $paymentAsLookup["payer_id"];
		$paymentExecuteRequestBody = '{ "payerID": "' . $payerID . '" }';

		$paymentExecuteResponse = WebClient::responseGetForRequest
		(
			$paymentExecuteURL, "POST", $headersAsStrings, $paymentExecuteRequestBody
		);

		$paymentExecuteResponseAsLookup = JSONEncoder::jsonStringToLookup($paymentExecuteResponse);
		$paymentState = $paymentExecuteResponseAsLookup["state"];
		$wasPaymentApproved = ($paymentState == "approved");

		return $wasPaymentApproved;
	}

	public function paymentVerifyByID($paypalPaymentID)
	{
		if ($paypalPaymentID == null || $paypalPaymentID == "")
		{
			$returnValue = false;
		}
		else
		{
			$url = $this->paypalURLRoot . "payments/payment/" . $paypalPaymentID;

			$accessToken = $this->accessToken();

			$headersAsStrings = array
			(
				"Content-Type: application/json",
				"Authorization: Bearer " . $accessToken,
			);
			
			$response = WebClient::responseGetForRequest($url, "GET", $headersAsStrings, null);

echo ($response);

			// todo
			$returnValue = true;
		}

		return $returnValue;
	}
}	
?>
