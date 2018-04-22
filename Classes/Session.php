<?php

class Session
{
	public $sessionID;
	public $user;
	public $deviceAddress;
	public $timeStarted;
	public $timeUpdated;
	public $timeEnded;

	public function __construct($sessionID, $user, $deviceAddress, $timeStarted, $timeUpdated, $timeEnded)
	{
		$this->sessionID = $sessionID;
		$this->user = $user;
		$this->deviceAddress = $deviceAddress;
		$this->timeStarted = $timeStarted;
		$this->timeUpdated = $timeUpdated;
		$this->timeEnded = $timeEnded;
	}

	public static function start()
	{
		if (isset($_SESSION) == false)
		{
			session_start();
			$configuration = include("Configuration.php");
			$persistenceClient = new PersistenceClientMySQL
			(
				$configuration["DatabaseServerName"],
				$configuration["DatabaseUsername"],
				$configuration["DatabasePassword"],
				$configuration["DatabaseName"]
			);
			$_SESSION["PersistenceClient"] = $persistenceClient;
		}
	}

	public static function stop()
	{
		$session = $_SESSION["Session"];
		$persistenceClient = $_SESSION["PersistenceClient"];
		$now = new DateTime();
		$session->timeEnded = $now;
		$persistenceClient->sessionSave($session);
		session_destroy();
	}

	public static function verify()
	{
		$messageSessionExpired =
			"Your session has expired (or no session was ever established)."
			. "<a href='UserLogin.php'>Log In Again</a>";

		if (isset($_SESSION["Session"]) == false)
		{
			echo $messageSessionExpired;
			die();
		}
		else
		{
			$persistenceClient = $_SESSION["PersistenceClient"];
			$sessionCurrent = $_SESSION["Session"];
			$userLoggedIn = $sessionCurrent->user;
			$userID = $userLoggedIn->userID;
			$sessionStored = $persistenceClient->sessionGetCurrentByUserID($userID);
			if ($sessionStored == null)
			{
				echo $messageSessionExpired;
				die();
			}
			else if ($sessionCurrent->sessionID != $sessionStored->sessionID)
			{
				echo $messageSessionExpired;
				die();
			}
			else
			{
				$now = new DateTime();
				$sessionCurrent->timeUpdated = $now;
				$deviceAddressCurrent = $_SERVER["SERVER_ADDR"];
				$deviceAddressStored = $sessionStored->deviceAddress;
				if ($deviceAddressCurrent != $deviceAddressStored)
				{
					echo "A separate session has been started using your account from another device.";
					die();
				}
				else
				{
					$persistenceClient->sessionSave($sessionCurrent);
				}
			}
		}
	}
}

?>
