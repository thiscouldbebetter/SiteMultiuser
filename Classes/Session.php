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
}

?>