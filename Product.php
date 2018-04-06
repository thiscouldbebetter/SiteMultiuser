<?php include "Common.php"; ?>

<html>
<body>

	<label><b>Product Details</b></label><br />
	<label>Product:</label>
	<label>
	<?php 
		$persistenceClient = $_SESSION["PersistenceClient"];			
		$productID = $_GET["productID"];
		$product = $persistenceClient->productGetByID($productID);
		$productName = $product->name;
		echo $productName;
		echo " <a href='OrderProductAdd.php?productID=" . $productID . "'>Add to Current Order</a>";
		
	?>
	</label>
	
	
</body>
</html>
