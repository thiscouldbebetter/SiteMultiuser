<?php

class WebClient
{
	public static function responseGetForRequest($url, $methodName, $headersAsStrings, $requestBody)
	{
		$curlClient = curl_init($url);
		curl_setopt($curlClient, CURLOPT_RETURNTRANSFER, true);
		if ($methodName == "POST")
		{
			if ($headersAsStrings != null)
			{
				curl_setopt($curlClient, CURLOPT_HTTPHEADER, $headersAsStrings);
			}
			curl_setopt($curlClient, CURLOPT_POST, true);
			curl_setopt($curlClient, CURLOPT_POSTFIELDS, $requestBody);
		}
		$responseBody = curl_exec($curlClient);
		curl_close($curlClient);
		return $responseBody;
	}
}

?>
