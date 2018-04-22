<?php

include("JSONEncoder.php");

class PaypalClient
{
	public $clientID;
	public $clientSecret;

	public function __construct($clientID, $clientSecret)
	{
		$this->clientID = $clientID;
		$this->clientSecret = $clientSecret;
	}
	
	public function payForOrder($order)
	{
		$accessToken = $this->accessToken();

		$url = "https://api.sandbox.paypal.com/v1/payments/payment";

		$headersAsStrings = array
		(
			"Content-Type: application/json",
			"Authorization: Bearer " . $accessToken,
		);

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
				"return_url" => "https://localhost/Store",
				"cancel_url" => "https://localhost/Store"
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

		$response = WebClient::responseGetForRequest($url, "POST", $headersAsStrings, $requestBody);

		return $response;
	}

	public function accessToken()
	{
		$url = "https://api.sandbox.paypal.com/v1/oauth2/token";
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

}	
?>
