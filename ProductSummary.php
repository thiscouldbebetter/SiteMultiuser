<?php include "Common.php"; ?>

<html>

<?php PageWriter::elementHeadWrite("Products"); ?>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered"> 
		<label>Products Available:</label>
		<div>
		<?php 
			$persistenceClient = $_SESSION["PersistenceClient"];
			$session = $_SESSION["Session"];
			$userLoggedIn = $session->user;
			$productsAll = $persistenceClient->productsGetAll();
			foreach ($productsAll as $product)
			{	
				$productName = $product->name;
				$productPrice = $product->price;
				$productAsString = $productName . " ($" . $productPrice . ")";
				echo($productAsString);

				$productID = $product->productID;
				echo " ";
				$isProductLicensedByUserLoggedIn = $userLoggedIn->isProductWithIDLicensed($productID);
				if ($isProductLicensedByUserLoggedIn == true)
				{
					echo "(Owned)";
				}
				else
				{
					echo "<a href='Product.php?productID=" . $productID . "'>Details</a>";
				}
				echo("<br />");
			}
		?>	
		</div>
	</div>

	<?php PageWriter::footerWrite(); ?>
	
</body>
</html>
