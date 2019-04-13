<?php

class User
{
	public $userID;
	public $username;
	public $emailAddress;
	public $passwordSalt;
	public $passwordHashed;
	public $passwordResetCode;
	public $isActive;
	public $licenses;
	public $orderCurrent;

	public function __construct($userID, $username, $emailAddress, $passwordSalt, $passwordHashed, $passwordResetCode, $isActive, $licenses)
	{
		$this->userID = $userID;
		$this->username = $username;
		$this->emailAddress = $emailAddress;
		$this->passwordSalt = $passwordSalt;
		$this->passwordHashed = $passwordHashed;
		$this->passwordResetCode = $passwordResetCode;
		$this->isActive = $isActive;
		$this->licenses = $licenses;

		$this->orderCurrent = Order::fromUserID($userID);
	}

	public static function dummy()
	{
		return new User(null, null, null, null, null, null, true, array() );
	}

	public function isProductWithIDLicensed($productIDToCheck)
	{
		$numberOfLicenses = $this->licenseCountForProductWithID($productIDToCheck, true);
		$hasLicenses = ($numberOfLicenses > 0);
		return $hasLicenses;
	}

	public function licenseCountForProductWithID($productIDToCheck, $breakAfterFirst)
	{
		$numberOfLicensesSoFar = 0;

		foreach ($this->licenses as $license)
		{
			$productID = $license->productID;
			if ($productID == $productIDToCheck)
			{
				$numberOfLicensesSoFar++;
				if ($breakAfterFirst)
				{
					break;
				}
			}
		}

		return $numberOfLicensesSoFar;
	}

	public static function passwordHashWithSalt($passwordAsPlaintext, $passwordSalt)
	{
		$passwordSalted = $passwordAsPlaintext . $passwordSalt;
		$passwordHashed = hash("sha256", $passwordSalted);
		return $passwordHashed;
	}

	public function passwordHash($passwordAsPlaintext)
	{
		return User::passwordHashWithSalt($passwordAsPlaintext, $this->passwordSalt);
	}

	public function passwordResetCodeQuoted()
	{
		$returnValue = $this->passwordResetCode;
		if ($returnValue == null)
		{
			$returnValue = "null";
		}
		else
		{
			$returnValue = "'" . $returnValue . "'";
		}
		return $returnValue;
	}

	public function refresh()
	{
		$session = $_SESSION["Session"];
		$persistenceClient = $_SESSION["PersistenceClient"];

		$this->licenses = $persistenceClient->licensesGetByUserID($this->userID);

		$session->user = $this;
	}
}

?>
