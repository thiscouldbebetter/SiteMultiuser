<?php include "Common.php"; ?>

<html>

<head><?php PageWriter::elementHeadWrite("Product Details"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Product Details:</b></label><br />
		<br />
		<div style="text-align:center">
			<?php
				$persistenceClient = $_SESSION["PersistenceClient"];
				$productID = $_GET["productID"];
				$product = $persistenceClient->productGetByID($productID);
				if ($product == null)
				{
					echo "No product with the specified ID could be found.";
				}
				else
				{
					$productName = $product->name;
					echo "<label>" . $productName . "</label><br />";
					echo "<img src='" .$product->imagePath . "' /><br />";
					echo "<label>Price: " . $product->price . "</label><br/>";
					if ($product->isActive)
					{
						echo
							"<a href='OrderProductQuantitySet.php?productID=" . $productID . "&quantity=1'>"
							. "Add Product to Current Order"
							. "</a>";
					}
					else
					{
						echo "<label>This product is no longer available for purchase.</label>";
					}
				}
			?>

			<br />

		</div>
		<br />

		<a href="ProductSummary.php">Browse Other Products</a>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
