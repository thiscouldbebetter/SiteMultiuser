<?php

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

class PageWriter
{
	public static function displayStatusMessage($statusMessage)
	{
		echo "<pre>" . wordwrap($statusMessage) . "</pre>";
	}

	public static function elementHeadWrite($pageTitle)
	{
		$configuration = include("Configuration.php");
		$siteTitle = $configuration["SiteTitle"];
		echo("<title>" . $siteTitle . " - " . $pageTitle . "</title>");
		echo("<link rel='stylesheet' href='Style.css'>");
	}

	public static function footerWrite()
	{
		echo("<script id='scriptFooter' type='text/javascript' src='Footer.js'></script>");
	}

	public static function headerWrite()
	{
		echo("<script id='scriptHeader' type='text/javascript' src='Header.js'></script>");
	}

	public static function sessionVerify()
	{
		if (isset($_SESSION["Session"]) == false)
		{
			echo "Your session has expired.";
			echo "<a href='UserLogin.php'>Log In Again</a>";
			die();
		}
	}
}

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

		$queryText =
			"insert into _Order (UserID, Status, TimeCompleted)"
			. " values (?, ?, ?)";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("sss", $order->userID, $order->status, $this->dateToString($order->timeCompleted));
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

		return $notification;
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
		$queryCommand->bind_param("ssi", $orderProduct->orderID, $order->productID, $order->quantity);
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

		return $notification;
	}

	public function paypalClientDataGet()
	{
		$returnValue = null;

		$databaseConnection = $this->connect();

		$queryText = "select * from PaypalClientData";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->execute();
		$queryCommand->bind_result($clientIDSandbox, $clientIDProduction, $isProductionEnabled);
		$queryCommand->fetch();

		$returnValue = new PaypalClientData($clientIDSandbox, $clientIDProduction, $isProductionEnabled);

		return $returnValue;
	}

	public function productGetByID($productID)
	{
		$returnValue = null;

		$databaseConnection = $this->connect();

		$queryText = "select * from Product where ProductID = ?";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$queryCommand->bind_param("i", $productID);
		$queryCommand->execute();
		$queryCommand->bind_result($productID, $name, $imagePath, $price);

		while ($queryCommand->fetch())
		{
			$returnValue = new Product($productID, $name, $imagePath, $price);
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
		$queryCommand->bind_result($productID, $name, $imagePath, $price);

		while ($queryCommand->fetch())
		{
			$product = new Product($productID, $name, $imagePath, $price);
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
		$queryCommand->bind_result($productID, $name, $imagePath, $price);

		while ($queryCommand->fetch())
		{
			$product = new Product($productID, $name, $imagePath, $price);
			$returnValues[$productID] = $product;
		}

		$databaseConnection->close();

		return $returnValues;
	}

	public function sessionSave($session)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error)
		{
			die("Could not connect to database.");
		}

		$queryText =
			"insert into Session (UserID, TimeStarted, TimeUpdated, TimeEnded)"
			. " values (?, ?, ?, ?)";
		$queryCommand = mysqli_prepare($databaseConnection, $queryText);
		$timeStartedAsString = $this->dateToString($session->timeStarted);
		$timeUpdatedAsString = $this->dateToString($session->timeUpdated);
		$timeEndedAsString = $this->dateToString($session->timeEnded);
		$queryCommand->bind_param("isss", $session->user->userID, $timeStartedAsString, $timeUpdatedAsString, $timeEndedAsString);

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

	private function licensesGetByUserID($userID)
	{
		$returnValues = array();

		$databaseConnection = $this->connect();

		$queryText = "select * from License where UserID = ?";
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

class License
{
	public $licenseID;
	public $userID;
	public $productID;
	public $transferTypeID;
	public $transferTarget;

	public function __construct($licenseID, $userID, $productID, $transferTypeID, $transferTarget)
	{
		$this->licenseID = $licenseID;
		$this->userID = $userID;
		$this->productID = $productID;
		$this->transferTypeID = $transferTypeID;
		$this->transferTarget = $transferTarget;
	}
}

class LicenseTransferType
{
	public $licenseTransferTypeID;
	public $name;
	public $description;

	public function __construct($licenseTransferTypeID, $name, $description)
	{
		$this->licenseTransferTypeID = $licenseTransferTypeID;
		$this->name = $name;
		$this->description = $description;
	}
}

class MathHelper
{
	public static function randomCodeGenerate()
	{
		$passwordSalt = decHex(rand()) . decHex(rand()) . decHex(rand()) . decHex(rand());
		$passwordSalt = str_pad($passwordSalt, 32, "0", STR_PAD_LEFT);
		return $passwordSalt;
	}
}


class Notification
{
	public $notificationID;
	public $addressee;
	public $subject;
	public $body;
	public $timeCreated;
	public $timeSent;

	public function __construct($notificationID, $addressee, $subject, $body, $timeCreated, $timeSent)
	{
		$this->notificationID = $notificationID;
		$this->addressee = $addressee;
		$this->subject = $subject;
		$this->body = $body;
		$this->timeCreated = $timeCreated;
		$this->timeSent = $timeSent;
	}

	public function sendAsEmail($persistenceClient)
	{
		$configuration = include("Configuration.php");
		$isEmailEnabled = $configuration["EmailEnabled"];
		if ($isEmailEnabled == true)
		{
			mail($this->addressee, $this->subject, $this->body);
			$now = new DateTime();
			$this->timeSent = $now;
			$persistenceClient->notificationSave($this);
		}
	}
}

class Order
{
	public $orderID;
	public $userID;
	public $status;
	public $timeCompleted;
	public $productBatches;

	public function __construct($orderID, $userID, $status, $timeCompleted, $productBatches)
	{
		$this->orderID = $orderID;
		$this->userID = $userID;
		$this->status = $status;
		$this->timeCompleted = $timeCompleted;
		$this->productBatches = $productBatches;
	}

	public function complete()
	{
		$this->status = "Complete";
		$this->timeCompleted = new DateTime();
	}

	public function priceTotal($productsAll)
	{
		$returnValue = 0;

		foreach ($this->productBatches as $productBatch)
		{
			$productID = $productBatch->productID;
			$product = $productsAll[$productID];
			$productPrice = $product->price;
			$returnValue += $productPrice;
		}

		return $returnValue;
	}

	public function productBatchesWithQuantityZeroRemove()
	{
		for ($i = 0; $i < count($this->productBatches); $i++)
		{

			$productBatch = $this->productBatches[$i];
			$quantity = $productBatch->quantity;
			if ($quantity <= 0)
			{
				array_splice($this->productBatches, $i, 1);
				$i--;
			}
		}

	}

	public function toLicenses()
	{
		$returnValues = array();
		foreach ($this->productBatches as $productBatch)
		{
			$productID = $productBatch->productID;
			$quantity = $productBatch->quantity;

			for ($i = 0; $i < quantity; $i++)
			{
				$license = new License(null, $this->userID, $productID, null, null);
				$returnValues[] = $license;
			}
		}
		return $returnValues;
	}
}

class Order_Product
{
	public function __construct($orderProductID, $orderID, $productID, $quantity)
	{
		$this->orderProductID = $orderProductID;
		$this->orderID = $orderID;
		$this->productID = $productID;
		$this->quantity = $quantity;
	}

	public function price($productsAll)
	{
		$product = $productsAll[$this->productID];
		$pricePerUnit = $product->price;
		$priceForBatch = $pricePerUnit * $this->quantity;
		return $priceForBatch;
	}
}

class PaypalClientData
{
	public $clientIDSandbox;
	public $clientIDProduction;
	public $isProductionEnabled;

	public function __construct($clientIDSandbox, $clientIDProduction, $isProductionEnabled)
	{
		$this->clientIDSandbox = $clientIDSandbox;
		$this->clientIDProduction = $clientIDProduction;
		$this->isProductionEnabled = $isProductionEnabled;
	}
}

class Product
{
	public $productID;
	public $name;
	public $imagePath;
	public $price;

	public function __construct($productID, $name, $imagePath, $price)
	{
		$this->productID = $productID;
		$this->name = $name;
		$this->imagePath = $imagePath;
		$this->price = $price;
	}
}

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

		$this->orderCurrent = new Order(null, $this->userID, "InProgress", null, array());
	}

	public static function dummy()
	{
		return new User(null, null, null, null, null, null, true, array() );
	}

	public function isProductWithIDLicensed($productIDToCheck)
	{
		$returnValue = false;

		foreach ($this->licenses as $license)
		{
			$productID = $license->productID;
			if ($productID == $productIDToCheck)
			{
				$returnValue = true;
				break;
			}
		}

		return $returnValue;
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
}

?>
