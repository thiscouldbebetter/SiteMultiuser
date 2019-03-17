<?php include "Common.php"; ?>

<html>

<head><?php PageWriter::elementHeadWrite("Products"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

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

		$productNamePartial = (isset($_POST["ProductNamePartial"]) ? $_POST["ProductNamePartial"] : "");
		$productsPerPage = (isset($_POST["ProductsPerPage"]) ? $_POST["ProductsPerPage"] : 10);
		$pageNumber = (isset($_POST["PageNumber"]) ? $_POST["PageNumber"] : 1);
		$pageIndex = $pageNumber - 1;

		$numberOfProductsFound =
			$persistenceClient->productsSearchCount($productNamePartial);
		$productsFound =
			$persistenceClient->productsSearch($productNamePartial, $productsPerPage, $pageIndex);

		$numberOfPages = ceil($numberOfProductsFound / $productsPerPage);		
	?>

	<div class="divCentered">
		<label><b>Product Search:</b></label><br />
		<br />
		<div>
			<div>
				<label>Search Criteria:</label><br />
				<form action="" method="post">
					<label>Product Name:</label>
					<input name="ProductNamePartial" value="<?php echo $productNamePartial; ?>"></input>
					<button type="submit">Search</button>
				</form>
			</div>
		<div>
			<label>Search Results:</label><br />
			<br />
			<div>
				<?php
					echo($numberOfProductsFound . " products found.<br /><br />");
				?>

				<form action="" method="post">

					<label>Results per Page:</label>
					<select
						name="ProductsPerPage"
						value="<?php echo $productsPerPage; ?>"
						onchange="this.form.submit();"
					>
						<?php
							$pageSizes = [ 10, 20, 50 ];
							foreach ($pageSizes as $pageSizeAvailable)
							{
								$isPageSizeSelected = ($pageSizeAvailable == $productsPerPage);
								$pageSizeAsOption =
									"<option value='" . $pageSizeAvailable . "' "
									. ($isPageSizeSelected ? "selected='true'" : "")
									. ">"
									. $pageSizeAvailable
									. "</option>";
								echo $pageSizeAsOption;
							}
						?>
					</select>

					<label>Page Number:</label>
					<input
						name="PageNumber"
						type="number"
						style="width:4em"
						value="<?php echo $pageNumber; ?>"
						onchange="this.form.submit();"
					>
					</input>
					<label> of </label>
					<input
						type="number"
						style="width:4em"
						disabled="true"
						value="<?php echo $numberOfPages; ?>"
					>
					</input>
				</form>

				<table style="border:1px solid" width="100%">
					<thead>
						<th>Name</th>
						<th>Price</th>
						<th>Details</th>
						<th>Owned</th>
					</thead>
					<?php
						foreach ($productsFound as $product)
						{
							$tableRow = "<tr>";

							$productName = $product->name;
							$tableCell = "<td>" . $productName . "</td>";
							$tableRow = $tableRow . $tableCell;

							$productPrice = $product->price;
							$tableCell = "<td>$" . $productPrice . "</td>";
							$tableRow = $tableRow . $tableCell;

							$productID = $product->productID;
							$tableCell = "<td><a href='Product.php?productID=" . $productID . "'>Details</a></td>";
							$tableRow = $tableRow . $tableCell;

							$numberOfLicensesHeld = $userLoggedIn->licenseCountForProductWithID($productID, false);
							$tableCell = ($numberOfLicensesHeld > 0 ? $numberOfLicensesHeld : "");
							$tableCell = "<td>". $tableCell . "</td>";
							$tableRow = $tableRow . $tableCell;

							$tableRow = $tableRow . "</tr>";
							echo($tableRow);
						}
					?>
				</table>
			</div>
		</div>
		<br />
		<a href='OrderDetails.php'>View Current Order</a>
		</div>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
