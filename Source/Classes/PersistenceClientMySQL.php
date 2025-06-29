<?php

class PersistenceClientMySQL
{
	public function __construct($databaseServerName, $databaseUsername, $databasePassword, $databaseName)
	{
		$this->databaseServerName = $databaseServerName;
		$this->databaseUsername = $databaseUsername;
		$this->databasePassword = $databasePassword;
		$this->databaseName = $databaseName;
	}

	private function connect()
	{
		$databaseConnection = new mysqli($this->databaseServerName, $this->databaseUsername, $this->databasePassword, $this->databaseName);
		return $databaseConnection;
	}

	private function dateToString($date)
	{
		if ($date == null)
		{
			$returnValue = null;
		}
		else
		{
			$dateFormatString = "Y-m-d H:i:s";
			$returnValue = $date->format($dateFormatString);
		}

		return $returnValue;
	}

	public function notificationSave($notification)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		if ($notification->notificationID == null)
		{
			$queryText =
				"insert into Notification (Addressee, Subject, Body, TimeCreated, TimeSent)"
				. " values (?, ?, ?, ?, ?)";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"sssss",
				$notification->addressee, $notification->subject,
				$notification->body, $this->dateToString($notification->timeCreated),
				$this->dateToString($notification->timeSent)
			);
		}
		else
		{
			$queryText = "update Notification set Addressee = ?, Subject = ?, Body = ?, TimeCreated = ?, TimeSent = ? where NotificationID = ?";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"sssssi",
				$notification->addressee, $notification->subject,
				$notification->body, $this->dateToString($notification->timeCreated),
				$this->dateToString($notification->timeSent),
				$notification->notificationID
			);
		}
		$didSaveSucceed = $queryCommand->execute();

		if ($didSaveSucceed == false)
		{
			die("Could not write to database.");
		}
		else
		{
			$notificationID = mysqli_insert_id($databaseConnection);
			if ($notificationID != null)
			{
				$notification->notificationID = $notificationID;
			}
		}

		$databaseConnection->close();

		return $notification;
	}

	public function sessionGetCurrentByUserID($userID)
	{
		$databaseConnection = $this->connect();

		$queryText = "select s.* from Session s where s.UserID = ? and s.TimeEnded is null and s.TimeStarted = (select max(s1.TimeStarted) from Session s1 where s1.TimeStarted <= ? and s1.UserID = s.UserID)";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$now = new DateTime();
		$nowAsString = $this->dateToString($now);
		$queryCommand->bind_param("is", $userID, $nowAsString);
		$queryCommand->execute();
		$queryCommand->bind_result($sessionID, $userID, $deviceAddress, $timeStarted, $timeUpdated, $timeEnded);

		$session = null;
		while ($queryCommand->fetch())
		{
			$user = User::dummy();
			$user->userID = $userID;
			$session = new Session($sessionID, $user, $deviceAddress, $timeStarted, $timeUpdated, $timeEnded);
			break;
		}

		$databaseConnection->close();

		return $session;
	}

	public function sessionSave($session)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		$timeStartedAsString = $this->dateToString($session->timeStarted);
		$timeUpdatedAsString = $this->dateToString($session->timeUpdated);
		$timeEndedAsString = $this->dateToString($session->timeEnded);

		if ($session->sessionID == null)
		{
			$queryText =
				"insert into Session (UserID, DeviceAddress, TimeStarted, TimeUpdated, TimeEnded)"
				. " values (?, ?, ?, ?, ?)";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param("issss", $session->user->userID, $session->deviceAddress, $timeStartedAsString, $timeUpdatedAsString, $timeEndedAsString);
		}
		else
		{
			$queryText =
				"update Session set UserID = ?, DeviceAddress = ?, TimeStarted = ?, TimeUpdated = ?, TimeEnded = ? where SessionID = ?";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param("issssi", $session->user->userID, $session->deviceAddress, $timeStartedAsString, $timeUpdatedAsString, $timeEndedAsString, $session->sessionID);
		}

		$didSaveSucceed = $queryCommand->execute();

		if ($didSaveSucceed == false)
		{
			die("Could not write to database.");
		}
		else
		{
			$sessionID = mysqli_insert_id($databaseConnection);
			if ($sessionID != null)
			{
				$session->sessionID = $sessionID;
			}
		}

		$databaseConnection->close();

		return $session;
	}

	public function userDeleteByID($userID)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		$queryText = "update User set IsActive = 0 where UserID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $userID);
		$didDeleteSucceed = $queryCommand->execute();

		return $didDeleteSucceed;
	}

	public function userGetByID($userID)
	{
		$databaseConnection = $this->connect();
		$queryText = "select * from User where UserID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $userID);
		$returnValue = $this->userGetByQueryCommand($queryCommand);
		$databaseConnection->close();
		return $returnValue;
	}

	public function userGetByEmailAddress($emailAddress)
	{
		$databaseConnection = $this->connect();
		$queryText = "select * from User where EmailAddress = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("s", $emailAddress);
		$returnValue = $this->userGetByQueryCommand($queryCommand);
		$databaseConnection->close();
		return $returnValue;
	}

	public function userGetByUsername($username)
	{
		$databaseConnection = $this->connect();
		$queryText = "select * from User where Username = ? and IsActive = 1";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("s", $username);
		$returnValue = $this->userGetByQueryCommand($queryCommand);
		$databaseConnection->close();
		return $returnValue;
	}

	private function userGetByQueryCommand($queryCommand)
	{
		$queryCommand->execute();
		$queryCommand->bind_result($userID, $username, $emailAddress, $passwordSalt, $passwordHashed, $passwordResetCode, $isActive);
		$queryCommand->store_result();

		$numberOfRows = $queryCommand->num_rows();
		if ($numberOfRows == 0)
		{
			$userFound = null;
		}
		else
		{
			$queryCommand->fetch();

			$licenses = $this->licensesGetByUserID($userID);

			$userFound = new User
			(
				$userID, $username, $emailAddress, $passwordSalt,
				$passwordHashed, $passwordResetCode, $isActive, $licenses
			);
		}

		return $userFound;
	}

	public function userSave($user)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		if ($user->userID == null)
		{
			$queryText =
				"insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive)"
				. " values (?, ?, ?, ?, ?, ?)";
		}
		else
		{
			$queryText = "update User set username = ?, emailAddress = ?, passwordSalt = ?, passwordHashed = ?, passwordResetCode = ?, isActive=?";
		}

		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("sssssi", $user->username, $user->emailAddress, $user->passwordSalt, $user->passwordHashed, $user->passwordResetCode, $user->isActive);
		$didSaveSucceed = $queryCommand->execute();
		if ($didSaveSucceed == false)
		{
			die("Could not write to database.");
		}
		else
		{
			$userID = mysqli_insert_id($databaseConnection);
			if ($userID != null)
			{
				$user->userID = $userID;
			}
		}

		$databaseConnection->close();

		return $user;
	}
}

?>
