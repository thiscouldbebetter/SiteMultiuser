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
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
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

	public function licenseGetByID($licenseID)
	{
		$returnValue = null;

		$databaseConnection = $this->connect();

		$queryText = "select * from License where LicenseID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $licenseID);
		$queryCommand->execute();
		$queryCommand->bind_result($licenseID, $userID, $productID, $transferTypeID, $transferTarget);

		while ($queryCommand->fetch())
		{
			$returnValue = new License($licenseID, $userID, $productID, $transferTypeID, $transferTarget);
			break;
		}

		$databaseConnection->close();

		return $returnValue;
	}

	public function licensesGetByTransferTarget($username, $emailAddress)
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from License where (TransferTypeID = 1 and TransferTarget = ?) or (TransferTypeID = 2 and TransferTarget = ?)";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("ss", $username, $emailAddress);
		$queryCommand->execute();
		$queryCommand->bind_result($licenseID, $userID, $productID, $transferTypeID, $transferTarget);

		while ($queryCommand->fetch())
		{
			$license = new License($licenseID, $userID, $productID, $transferTypeID, $transferTarget);
			$returnValues[] = $license;
		}

		$databaseConnection->close();

		return $returnValues;
	}

	public function licenseTransferTypesGetAll()
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from LicenseTransferType";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->execute();
		$queryCommand->bind_result($transferTypeID, $name, $description);

		while ($queryCommand->fetch())
		{
			$transferType = new LicenseTransferType($transferTypeID, $name, $description);
			$returnValues[$transferTypeID] = $transferType;
		}

		$databaseConnection->close();

		return $returnValues;
	}

	public function licenseSave($license)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		if ($license->licenseID == null)
		{
			$queryText =
				"insert into License (UserID, ProductID, TransferTypeID, TransferTarget)"
				. " values (?, ?, ?, ?)";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"iiis", $license->userID, $license->productID, $license->transferTypeID, $license->transferTarget
			);
		}
		else
		{
			$queryText = "update License set UserID = ?, ProductID = ?, TransferTypeID = ?, TransferTarget = ? where LicenseID = ?";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param
			(
				"iiisi", $license->userID, $license->productID, $license->transferTypeID, $license->transferTarget, $license->licenseID
			);
		}
		$didSaveSucceed = $queryCommand->execute();

		if ($didSaveSucceed == false)
		{
			die("Could not write to database.");
		}
		else
		{
			$licenseID = mysqli_insert_id($databaseConnection);
			if ($licenseID != null)
			{
				$license->licenseID = $licenseID;
			}
		}

		$databaseConnection->close();

		return $license;
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

	public function orderSave($order)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		if ($order->orderID == null)
		{
			$queryText =
				"insert into _Order (UserID, PromotionID, Status, TimeStarted, TimeUpdated, TimeCompleted, PaymentID)"
				. " values (?, ?, ?, ?, ?, ?, ?)";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param("issssss", $order->userID, $order->promotionID, $order->status, $this->dateToString($order->timeStarted), $this->dateToString($order->timeUpdated), $this->dateToString($order->timeCompleted), $order->paymentID);
		}
		else
		{
			$queryText =
				"update _Order set UserID = ?, PromotionID = ?, Status = ?, TimeStarted = ?, TimeUpdated = ?, TimeCompleted = ?, PaymentID = ?"
				. " where OrderID = ?";
			$queryCommand = mysqli_prepare($databaseConnection, $queryText);
			$queryCommand->bind_param("issssssi", $order->userID, $order->promotionID, $order->status, $this->dateToString($order->timeStarted), $this->dateToString($order->timeUpdated), $this->dateToString($order->timeCompleted), $order->paymentID, $order->orderID);
		}

		$didSaveSucceed = $queryCommand->execute();

		if ($didSaveSucceed == false)
		{
			die("Could not write to database.");
		}
		else
		{
			$orderID = mysqli_insert_id($databaseConnection);
			if ($orderID != null)
			{
				$order->orderID = $orderID;
			}
		}

		$orderProducts = $order->productBatches;
		foreach ($orderProducts as $productBatch)
		{
			$productBatch->orderID = $orderID;
			$this->orderProductSave($productBatch);
		}

		$databaseConnection->close();

		return $order;
	}

	public function orderGetByID($orderID)
	{
		$databaseConnection = $this->connect();

		$queryText = "select * from _Order where OrderID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $orderID);
		$ordersFound = $this->ordersGetByQueryCommand($queryCommand, $databaseConnection);
		$returnValue = null;
		if (count($ordersFound) > 0)
		{
			$returnValue = $ordersFound[0];
		}
		return $returnValue;
	}

	public function ordersGetByUserID($userID)
	{
		$databaseConnection = $this->connect();

		$queryText = "select * from _Order where UserID = ? order by TimeStarted desc";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $userID);
		$returnValues = $this->ordersGetByQueryCommand($queryCommand, $databaseConnection);
		return $returnValues;
	}

	private function ordersGetByQueryCommand($queryCommand, $databaseConnection)
	{
		$returnValues = array();

		$queryCommand->execute();
		$queryCommand->bind_result($orderID, $userID, $promotionID, $status, $timeStarted, $timeUpdated, $timeCompleted, $paymentID);

		while ($queryCommand->fetch())
		{
			$order = new Order($orderID, $userID, $promotionID, $status, $timeStarted, $timeUpdated, $timeCompleted, $paymentID, null);
			$returnValues[] = $order;
		}

		$databaseConnection->close();

		foreach ($returnValues as $order)
		{
			$orderID = $order->orderID;
			$productBatchesInOrder = $this->orderProductsGetByOrderID($orderID);
			$order->productBatches = $productBatchesInOrder;
		}

		return $returnValues;
	}

	public function orderProductsGetByOrderID($orderID)
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from Order_Product where OrderID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $orderID);
		$queryCommand->execute();
		$queryCommand->bind_result($orderProductID, $orderID, $productID, $quantity);

		while ($queryCommand->fetch())
		{
			$orderProduct = new Order_Product($orderProductID, $orderID, $productID, $quantity);
			$returnValues[] = $orderProduct;
		}

		$databaseConnection->close();

		return $returnValues;
	}

	public function orderProductSave($orderProduct)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		$queryText =
			"insert into Order_Product (OrderID, ProductID, Quantity)"
			. " values (?, ?, ?)";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("ssi", $orderProduct->orderID, $orderProduct->productID, $orderProduct->quantity);
		$didSaveSucceed = $queryCommand->execute();

		if ($didSaveSucceed == false)
		{
			die("Could not write to database.");
		}
		else
		{
			$orderProductID = mysqli_insert_id($databaseConnection);
			if ($orderProductID != null)
			{
				$orderProduct->orderProductID = $orderProductID;
			}
		}

		$databaseConnection->close();

		return $orderProduct;
	}

	public function productGetByID($productID)
	{
		$returnValue = null;

		$databaseConnection = $this->connect();

		$queryText = "select * from Product where ProductID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $productID);
		$queryCommand->execute();
		$queryCommand->bind_result($productID, $name, $imagePath, $price, $contentPath);

		while ($queryCommand->fetch())
		{
			$returnValue = new Product($productID, $name, $imagePath, $price, $contentPath);
			break;
		}

		$databaseConnection->close();

		return $returnValue;
	}

	public function productsGetAll()
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from Product";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->execute();
		$queryCommand->bind_result($productID, $name, $imagePath, $price, $contentPath);

		while ($queryCommand->fetch())
		{
			$product = new Product($productID, $name, $imagePath, $price, $contentPath);
			$returnValues[$productID] = $product;
		}

		$databaseConnection->close();

		return $returnValues;
	}

	public function productsSearch($productNamePartial)
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from Product where Name like '%" . $productNamePartial . "%'";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->execute();
		$queryCommand->bind_result($productID, $name, $imagePath, $price, $contentPath);

		while ($queryCommand->fetch())
		{
			$product = new Product($productID, $name, $imagePath, $price, $contentPath);
			$returnValues[$productID] = $product;
		}

		$databaseConnection->close();

		return $returnValues;
	}

	public function promotionGetByCode($promotionCode)
	{
		$returnValue = null;

		$databaseConnection = $this->connect();

		$queryText = "select * from Promotion where Code = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("s", $promotionCode);
		$queryCommand->execute();
		$queryCommand->bind_result($promotionID, $description, $discount, $code);

		while ($queryCommand->fetch())
		{
			$returnValue = new Promotion($promotionID, $description, $discount, $code, array() );
			break;
		}

		$databaseConnection->close();

		if ($returnValue != null)
		{
			$returnValue->products = $this->productsGetByPromotionID($promotionID);
		}

		return $returnValue;
	}

	private function productsGetByPromotionID($promotionID)
	{
		$databaseConnection = $this->connect();

		$returnValues = array();

		$queryText = "select p.* from Promotion_Product pp, Product p where p.ProductID = pp.ProductID and pp.PromotionID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $promotionID);
		$queryCommand->execute();
		$queryCommand->bind_result($productID, $name, $imagePath, $price, $contentPath);

		while ($queryCommand->fetch())
		{
			$product = new Product($productID, $name, $imagePath, $price, $contentPath);
			$returnValues[] = $product;
		}

		$databaseConnection->close();

		return $returnValues;
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

	public function licensesGetByUserID($userID)
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from License where UserID = ? order by ProductID";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $userID);
		$queryCommand->execute();
		$queryCommand->bind_result($licenseID, $userID, $productID, $transferTypeID, $transferTarget);

		while ($row = $queryCommand->fetch())
		{
			$license = new License($licenseID, $userID, $productID, $transferTypeID, $transferTarget);
			$returnValues[] = $license;
		}

		$databaseConnection->close();

		return $returnValues;
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
