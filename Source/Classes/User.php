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

	public function __construct($userID, $username, $emailAddress, $passwordSalt, $passwordHashed, $passwordResetCode, $isActive)
	{
		$this->userID = $userID;
		$this->username = $username;
		$this->emailAddress = $emailAddress;
		$this->passwordSalt = $passwordSalt;
		$this->passwordHashed = $passwordHashed;
		$this->passwordResetCode = $passwordResetCode;
	}

	public static function dummy()
	{
		return new User(null, null, null, null, null, null, true);
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

		$session->user = $this;
	}
}

?>
