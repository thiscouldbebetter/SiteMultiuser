<?php include "Common.php"; ?>

<html>
<body>

	<label>Username:</label>
	<label>
	<?php 
		$user = $_SESSION["UserLoggedIn"];
		echo($user->username);
	?>
	</label>
	<a href="UserLogout.php">Log Out</a> <a href="UserDelete.php">Delete</a>
	<br />
	
	<label>Products Owned:</label>
	<div>
	<?php 
		$persistenceClient = $_SESSION["PersistenceClient"];
		$products = $persistenceClient->productsGetAll();
		foreach ($user->userProductsOwned as $userProduct)
		{
			$productID = $userProduct->productID;
			$product = $products[$productID];
			$productName = $product->name;
			echo($productName);	
		}
	?>	
	</div>
	
	<a href="ProductSummary.php">Browse Available Products</a>
	
</body>
</html>
