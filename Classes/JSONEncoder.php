<?php

class JSONEncoder
{
	public static function jsonStringToLookup($jsonString)
	{
		$lookup = json_decode
		(
			$jsonString, 
			true // Deserialize to associative array.
		);
		return $lookup;
	}

	public static function lookupToJSONString($lookup)
	{
		$jsonString = json_encode($lookup);
		return $jsonString;
	}
}

?>
