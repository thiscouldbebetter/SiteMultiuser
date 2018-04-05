<?php include "Common.php"; ?>

<html>
<body>

	<label>Products Available:</label>
	<div>
	<?php 
		$persistenceClient = $_SESSION["PersistenceClient"];
		$userLoggedIn = $_SESSION["UserLoggedIn"];
		$productsAll = $persistenceClient->productsGetAll();
		foreach ($productsAll as $product)
		{	
			$productName = $product->name;
			echo($productName);
			
			$productID = $product->productID;
			echo " ";
			$isProductOwnedByUserLoggedIn = $userLoggedIn->isProductWithIDOwned($productID);
			if ($isProductOwnedByUserLoggedIn == true)
			{
				echo "(Owned)";
			}
			else
			{
				echo "<a href='Product.php?ProductID=" . $productID . "'>Details</a>";
			}
			echo("<br />");
		}
	?>	
	</div>
	
</body>
</html>
