<?php include "Common.php"; ?>

<html>

<head><?php PageWriter::elementHeadWrite("Licenses Owned"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Licenses Owned:</b></label><br />
		<form name="formLicensesOwned" action="" method="post">
		<div>
		<?php 
			$session = $_SESSION["Session"];
			$userLoggedIn = $session->user;
			$licenses = $userLoggedIn->licenses;
			$persistenceClient = $_SESSION["PersistenceClient"];
			$productsAll = $persistenceClient->productsGetAll();
			$numberOfLicenses = count($licenses);
			if ($numberOfLicenses == 0)
			{
				echo "(no items)";
			}
			else
			{
				echo "<ul>";
				foreach ($licenses as $license)
				{
					$productID = $license->productID;
					$product = $productsAll[$productID];
					$productName = $product->name;
					$licenseAsListItem = "<li>" . $productName . "</li>";
					echo($licenseAsListItem);
				}
				echo "</ul>";
			}
		?>
		</div><br />

		<a href="ProductSummary.php">Browse Available Products</a><br />
		<a href='User.php'>Back to Account Details</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
