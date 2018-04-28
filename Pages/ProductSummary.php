<?php include "Common.php"; ?>

<html>

<head><?php PageWriter::elementHeadWrite("Products"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Product Search:</b></label><br />
		<br />
		<div>
			<div>
				<label>Search Criteria:</label><br />
				<form action="" method="post">
				<label>Product Name:</label>
				<input name="ProductNamePartial"></input>
				<button type="submit">Search</button>
			</div>
		<div>
			<label>Search Results:</label><br />
			<br />
			<div>
			<?php
				$persistenceClient = $_SESSION["PersistenceClient"];
				$session = $_SESSION["Session"];
				if ($session != null)
				{
					$userLoggedIn = $session->user;
				}
				else
				{
					$userLoggedIn = User::dummy();
				}

				if (isset($_POST["ProductNamePartial"]) == false)
				{
					$productNamePartial = "";
				}
				else
				{
					$productNamePartial = $_POST["ProductNamePartial"];
				}
				$productsFound = $persistenceClient->productsSearch($productNamePartial);
				$numberOfProductsFound = count($productsFound);

				echo($numberOfProductsFound . " products found.<br />");

				echo("<ul>");
				foreach ($productsFound as $product)
				{
					$productName = $product->name;
					$productPrice = $product->price;
					$productAsString = $productName . " ($" . $productPrice . ") ";

					$productID = $product->productID;
					$productAsString = $productAsString . "<a href='Product.php?productID=" . $productID . "'>Details</a>";
					$isProductLicensedByUserLoggedIn = $userLoggedIn->isProductWithIDLicensed($productID);
					if ($isProductLicensedByUserLoggedIn == true)
					{
						$productAsString = $productAsString . " (Owned)";
					}
					$productAsListItem = "<li>" . $productAsString . "</li>";
					echo($productAsListItem);
				}
				echo("</ul>");
			?>
			</div>
		</div>
		<a href='OrderDetails.php'>Back to Current Order</a>
		</div>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
