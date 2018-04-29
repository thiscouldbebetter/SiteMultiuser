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
	
	public function payForOrder($order)
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
				"return_url" => "http://localhost",
				"cancel_url" => "http://localhost"
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

	public function paymentExecuteFromIDAndPayerID($paymentID, $payerID)
	{
		$paymentAsJSON = $this->paymentGetbyID($paymentID);
		$paymentAsLookup = JSONEncoder::jsonStringToLookup($paymentAsJSON);
		$paymentAsLookup["payer_id"] = $payerID;
		unset($paymentAsLookup["id"]);
		unset($paymentAsLookup["update_time"]);
		unset($paymentAsLookup["payer"]);
		unset($paymentAsLookup["create_time"]);
		unset($paymentAsLookup["state"]);
		unset($paymentAsLookup["links"]);
		unset($paymentAsLookup["redirect_urls"]);
		unset($paymentAsLookup["cart"]);
		unset($paymentAsLookup["intent"]);

		$paymentExecuteRequestBody = JSONEncoder::lookupToJSONString($paymentAsLookup);

		$paymentExecuteURL = $this->paypalURLRoot . "payments/payment/" . $paymentID . "/execute";

		$accessToken = $this->accessToken();
		$headersAsStrings = array
		(
			"Content-Type: application/json",
			"Authorization: Bearer " . $accessToken,
		);

		$paymentExecuteResponse = WebClient::responseGetForRequest
		(
			$paymentExecuteURL, "POST", $headersAsStrings, $paymentExecuteRequestBody
		);

		$paymentExecuteResponseAsLookup = JSONEncoder::jsonStringToLookup($paymentExecuteResponse);
		$paymentState = $paymentExecuteResponseAsLookup["state"];
		$wasPaymentApproved = ($paymentState == "approved");

		return $wasPaymentApproved;
	}

	public function paymentGetByID($paymentID)
	{
		if ($paymentID == null || $paymentID == "")
		{
			$returnValue = null;
		}
		else
		{
			$url = $this->paypalURLRoot . "payments/payment/" . $paymentID;

			$accessToken = $this->accessToken();

			$headersAsStrings = array
			(
				"Content-Type: application/json",
				"Authorization: Bearer " . $accessToken,
			);
			
			$returnValue = WebClient::responseGetForRequest($url, "GET", $headersAsStrings, null);
		}

		return $returnValue;
	}

	public function paymentVerifyByID($paymentID)
	{
		$paymentRetrieved = $this->paymentGetByID($paymentID);
		$paymentAsLookup = JSONEncoder::jsonStringToLookup($paymentRetrieved);
		$state = $paymentAsLookup["state"];
		$returnValue = ($state == "approved");
		return $returnValue;
	}
}	
?>
