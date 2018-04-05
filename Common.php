<?php

if (isset($_SESSION) == false)
{
	session_start();
	if (isset($_SESSION["PersistenceClient"]) == false)
	{
		$persistenceClient = new PersistenceClientMySQL("localhost", "root", "Password42", "Store");
		$_SESSION["PersistenceClient"] = $persistenceClient;
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
		$databaseConnection = new mysqli($this->databaseServerName, $this->databaseUsername, $this->databasePassword, $this->databaseName);
		return $databaseConnection;
	}

	public function productGetByID($productID)
	{	
		$returnValue = null;
		
		$databaseConnection = $this->connect();
		
		$queryText = "select * from Product where ProductID = " . $productID;
		$queryResult = $databaseConnection->query($queryText);
		
		while ($row = $queryResult->fetch_assoc())
		{
			$productID = $row["ProductID"];		
			$name = $row["Name"];
			
			$returnValue = new Product($productID, $name);
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
		$queryResult = $databaseConnection->query($queryText);
		while ($row = $queryResult->fetch_assoc())
		{
			$productID = $row["ProductID"];
			$name = $row["Name"];
			
			$product = new Product($productID, $name);
			$returnValues[] = $product;
		}
		
		$databaseConnection->close();
		
		return $returnValues;
	}
	
	public function userDeleteByID($userID)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error) 
		{
			die("Could not connect to database.");
		} 
		
		$queryText = "update User set IsActive = 0 where UserID = " . $userID;
		$didDeleteSucceed = $databaseConnection->query($queryText);
		return $didDeleteSucceed;
	}
	
	public function userGetByUsername($username)
	{
		$databaseConnection = $this->connect();

		if ($databaseConnection->connect_error) 
		{
			die("Could not connect to database.");
		} 
				
		$queryText = "select * from User where Username = '" . $username . "' and IsActive = 1";
		$queryResult = $databaseConnection->query($queryText);
		
		$numberOfRows = mysqli_num_rows($queryResult);
		if ($numberOfRows == 0)
		{
			$userFound = null;
		}
		else
		{
			$row = $queryResult->fetch_assoc();
			
			$userID = $row["UserID"];
			$username = $row["Username"];
			$emailAddress = $row["EmailAddress"];
			$passwordSalt = $row["PasswordSalt"];
			$passwordHashed = $row["PasswordHashed"];
			$isActive = $row["IsActive"];
			$userProductsOwned = $this->userGetByUsername_UserProductsOwned($databaseConnection, $userID);
			
			$userFound = new User($userID, $username, $emailAddress, $passwordSalt, $passwordHashed, $isActive, $userProductsOwned);
		}
		
		$databaseConnection->close();
						
		return $userFound;
	}
	
	private function userGetByUsername_UserProductsOwned($databaseConnection, $userID)
	{		
		$returnValues = array();
		
		$queryText = "select * from User_Product where UserID = " . $userID;
		$queryResult = $databaseConnection->query($queryText);
		
		while ($row = $queryResult->fetch_assoc())
		{
			$userProductID = $row["UserProductID"];
			$productID = $row["ProductID"];
			$userProduct = new UserProduct($userProductID, $userID, $productID);
			$returnValues[] = $userProduct;
		}
		
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
				"insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed, IsActive)"
				. " values ('" . $user->username . "', '" . $user->emailAddress . "', '" . $user->passwordSalt . "', '" . $user->passwordHashed . "', " . $user->isActive . ")";
		}
		else
		{
			$queryText = 
				"insert into User (UserID, Username, EmailAddress, PasswordSalt, PasswordHashed, IsActive)"
				. " values (" . $user->userID . ", " . "'" . $user->username . "', '" . $user->emailAddress . "', '" . $user->passwordSalt . "', '" . $user->passwordHashed . "', " . $user->isActive . ")"
				. " on duplicate key update"
				. " username = '" . $user->username . "', emailAddress='" . $user->emailAddress . ", passwordSalt = '" . $user->passwordSalt . ", passwordHashed = '" . $user->passwordhashed . "', isActive=" . $user->isActive;
		}
		$didSaveSucceed = $databaseConnection->query($queryText);
		
		if ($didSaveSucceed == false)
		{
			die($queryText);
			die("Could not write to database.");
		}
		else
		{			
			$userID = mysqli_insert_id();
			if ($userID != null)
			{
				$user->UserID = $userID;
			}
		}
		
		$databaseConnection->close();
						
		return $user;
	}
}

class Order
{
	public $userID;
	public $productIDs;
	
	public function _construct($userID, $productIDs)
	{
		$this->userID = $userID;
		$this->productIDs = $productIDs;
	}
	
	public function toUserProducts()
	{
		$returnValues = array();
		foreach ($this->productIDs as $productID)
		{
			$userProduct = new UserProduct($this->userID, $productID);
			$returnValues[] = $userProduct;
		}
		return $returnValues;
	}
}

class Product
{
	public $productID;
	public $name;
	
	public function __construct($productID, $name)
	{
		$this->productID = $productID;
		$this->name = $name;
	}
}

class User
{
	public $userID;
	public $username;
	public $emailAddress;	
	public $passwordSalt;
	public $passwordHashed;
	public $isActive;
	public $userProductsOwned;
	
	public function __construct($userID, $username, $emailAddress, $passwordSalt, $passwordHashed, $isActive, $userProductsOwned)
	{
		$this->userID = $userID;
		$this->username = $username;
		$this->emailAddress = $emailAddress;		
		$this->passwordSalt = $passwordSalt;
		$this->passwordHashed = $passwordHashed;
		$this->isActive = $isActive;
		$this->userProductsOwned = $userProductsOwned;
	}
	
	public function isProductWithIDOwned($productIDToCheck)
	{
		$returnValue = false;
		
		foreach ($this->userProductsOwned as $userProduct)
		{
			$productIDOwned = $userProduct->productID;
			if ($productIDOwned == $productIDToCheck)
			{
				$returnValue = true;
				break;
			}
		}
		
		return $returnValue;
	}
		
	public static function passwordSaltGenerate()
	{
		$passwordSalt = str_pad(strval(rand()), 9, "0", STR_PAD_LEFT);
		return $passwordSalt;
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
}

class UserProduct
{
	public $userProductID;
	public $userID;
	public $productID;
		
	public function __construct($userProductID, $userID, $productID)
	{
		$this->userProductID = $userProductID;
		$this->userID = $userID;
		$this->productID = $productID;
	}
}
	
?>
