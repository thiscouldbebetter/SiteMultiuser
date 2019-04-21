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

	public static function start($configuration)
	{
		if (isset($_SESSION) == false)
		{
			session_start();
			$persistenceClient = new PersistenceClientMySQL
			(
				$configuration->databaseServerName,
				$configuration->databaseUsername,
				$configuration->databasePassword,
				$configuration->databaseName
			);

			$_SESSION["Configuration"] = $configuration;
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

	public static function verify($configuration)
	{
		$isSessionVerified = false;
		$messageToShow = null;

		$appDirectory = $configuration->appDirectory;
		$loginPageAddress = "/" . $appDirectory;

		$messageSessionExpired =
			"Your session has expired, or no session was ever established."
			. "  You may need to return to the home page and try to log in again."
			. " <a href='" . $loginPageAddress . "'>Home</a>";

		if (isset($_SESSION["Session"]) == false)
		{
			$messageToShow = $messageSessionExpired;
		}
		else
		{
			$sessionCurrent = $_SESSION["Session"];
			$userLoggedIn = $sessionCurrent->user;
			$userID = $userLoggedIn->userID;
			$persistenceClient = $_SESSION["PersistenceClient"];
			$sessionStored = $persistenceClient->sessionGetCurrentByUserID($userID);
			if ($sessionStored == null)
			{
				$messageToShow = $messageSessionExpired;
			}
			else if ($sessionCurrent->sessionID != $sessionStored->sessionID)
			{
				$messageToShow = $messageSessionExpired;
			}
			else
			{
				$now = new DateTime();
				$sessionCurrent->timeUpdated = $now;
				$deviceAddressCurrent = $_SERVER["SERVER_ADDR"];
				$deviceAddressStored = $sessionStored->deviceAddress;
				if ($deviceAddressCurrent != $deviceAddressStored)
				{
					$messageToShow = "A separate session has been started using your account from another device.";
				}
				else
				{
					$isSessionVerified = true;
				}
			}
		}

		if ($isSessionVerified)
		{
			$persistenceClient->sessionSave($sessionCurrent);
		}
		else
		{
			echo $messageToShow;
			die();
		}
	}
}

?>
