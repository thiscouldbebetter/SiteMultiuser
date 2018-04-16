<?php include "Common.php"; ?>

<html>

<head><?php PageWriter::elementHeadWrite("Product Details"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Product Details:</b></label><br />
		<br />
		<div style="text-align:center">
		<label>
		<?php
			$persistenceClient = $_SESSION["PersistenceClient"];
			$productID = $_GET["productID"];
			$product = $persistenceClient->productGetByID($productID);
			$productName = $product->name;
			echo $productName;
		?>
		</label><br />
		<br />
		<img src='<?php echo($product->imagePath); ?>' /><br />
		<br />
		<label>Price: $<?php echo($product->price); ?></label><br/>
		</div>
		<br />
		<a href='OrderProductQuantitySet.php?productID=<?php echo($productID); ?>&quantity=1'>Add Product to Current Order</a><br />
		<br />
		</label>
		<a href="ProductSummary.php">Browse Other Products</a>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
