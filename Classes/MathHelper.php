<?php

class MathHelper
{
	public static function randomCodeGenerate()
	{
		$passwordSalt = decHex(rand()) . decHex(rand()) . decHex(rand()) . decHex(rand());
		$passwordSalt = str_pad($passwordSalt, 32, "0", STR_PAD_LEFT);
		return $passwordSalt;
	}
}

?>
