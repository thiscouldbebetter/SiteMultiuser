<?php include "Common.php"; ?>

<html>
<body>

	<label>Product:</label>
	<label>
	<?php 
		$persistenceClient = $_SESSION["PersistenceClient"];			
		$productID = $_GET["ProductID"];
		$product = $persistenceClient->productGetByID($productID);
		$productName = $product->name;
		echo $productName
	?>
	</label>
	
</body>
</html>
