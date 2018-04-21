<?php

class Session
{
	public $sessionID;
	public $user;
	public $timeStarted;
	public $timeUpdated;
	public $timeEnded;

	public function __construct($sessionID, $user, $timeStarted, $timeUpdated, $timeEnded)
	{
		$this->sessionID = $sessionID;
		$this->user = $user;
		$this->timeStarted = $timeStarted;
		$this->timeUpdated = $timeUpdated;
		$this->timeEnded = $timeEnded;
	}

	public static function start() 
	{
		$configuration = include("Configuration.php");

		if (isset($_SESSION) == false)
		{
			session_start();
			if (isset($_SESSION["PersistenceClient"]) == false)
			{
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
	}

	public static function verify()
	{
		if (isset($_SESSION["Session"]) == false)
		{
			echo "Your session has expired (or no session was ever established).";
			echo "<a href='UserLogin.php'>Log In Again</a>";
			die();
		}
	}
}

?>
