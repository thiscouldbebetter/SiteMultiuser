<?php include "Common.php"; ?>

<html>
<body>
	<label><b>User Details</b></label><br />
	<label>Username:</label>
	<label>
	<?php 
		$session = $_SESSION["Session"];
		$userLoggedIn = $session->user;
		echo($userLoggedIn->username);
	?>
	</label>
	<a href="UserLogout.php">Log Out</a> <a href="UserDelete.php">Delete</a>
	<br />
	
	<label>Licenses Owned:</label>
	<div>
	<?php 
		$persistenceClient = $_SESSION["PersistenceClient"];
		$productsAll = $persistenceClient->productsGetAll();
		$licenses = $userLoggedIn->licenses;
		$numberOfLicenses = count($licenses);
		if ($numberOfLicenses == 0)
		{
			echo "(none)";
			echo("<br />");
		}
		else
		{
			foreach ($licenses as $license)
			{
				$productID = $license->productID;
				$product = $productsAll[$productID];
				$productName = $product->name;
				echo($productName);
				echo("<br />");
			}
		}
	?>	
	</div>
	
	<label>Current Order:</label>
	<?php 	
		$orderCurrent = $userLoggedIn->orderCurrent;
		$productBatchesInOrder = $orderCurrent->productBatches;
		$numberOfBatches = count($productBatchesInOrder);
		echo("(" . $numberOfBatches . " item(s)) ");
		echo("<a href='OrderCurrent.php'>Details</a>");
	?>	
	<br />
		
	<a href="ProductSummary.php">Browse Available Products</a>
	
</body>
</html>
