<?php include "Common.php"; ?>

<html>

<?php PageWriter::elementHeadWrite("Product Details"); ?>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Product Details</b></label><br />
		<label>Product:</label>
		<label>
		<?php 
			$persistenceClient = $_SESSION["PersistenceClient"];			
			$productID = $_GET["productID"];
			$product = $persistenceClient->productGetByID($productID);
			$productName = $product->name;
			$productPrice = $product->price;
			$productAsString = $productName . " ($" . $productPrice . ")";
			echo $productAsString;
			echo " <a href='OrderProductQuantitySet.php?productID=" . $productID . "&quantity=1'>Add to Current Order</a>";
			
		?>
		</label>
	</div>

	<?php PageWriter::footerWrite(); ?>
	
</body>
</html>
