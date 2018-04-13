<?php include "Common.php"; ?>

<html>

<head><?php PageWriter::elementHeadWrite("Products"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered"> 
		<label><b>Products Available:</b></label>
		<div>
		<?php 
			$persistenceClient = $_SESSION["PersistenceClient"];
			$session = $_SESSION["Session"];
			$userLoggedIn = $session->user;
			$productsAll = $persistenceClient->productsGetAll();
			echo("<ul>");
			foreach ($productsAll as $product)
			{
				$productName = $product->name;
				$productPrice = $product->price;
				$productAsString = $productName . " ($" . $productPrice . ") ";

				$productID = $product->productID;
				$isProductLicensedByUserLoggedIn = $userLoggedIn->isProductWithIDLicensed($productID);

				if ($isProductLicensedByUserLoggedIn == true)
				{
					$productAsString = $productAsString . "(Owned)";
				}
				else
				{
					$productAsString = $productAsString . "<a href='Product.php?productID=" . $productID . "'>Details</a>";
				}
				$productAsListItem = "<li>" . $productAsString . "</li>";
				echo($productAsListItem);
			}
			echo("</ul>");
		?>
		<a href='OrderCurrent.php'>Back to Current Order</a>
		</div>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
