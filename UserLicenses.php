<?php include "Common.php"; ?>
<?php PageWriter::sessionVerify(); ?>

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
			$licenseTransferTargetTypes = $persistenceClient->licenseTransferTypesGetAll();
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

					$productNameAndTransferData = $productName . " ";
					$transferTypeID = $license->transferTypeID;
					$licenseID = $license->licenseID;
					if ($transferTypeID == null)
					{
						$transferLink = "<a href='UserLicenseTransfer.php?licenseID=" . $licenseID . "'>Transfer</a>";
						$productNameAndTransferData = $productNameAndTransferData . $transferLink;
					}
					else
					{
						$transferType = $licenseTransferTargetTypes[$transferTypeID];
						$transferTarget = $license->transferTarget;
						$transferLink = "<a href='UserLicenseTransfer.php?licenseID=" . $licenseID . "'>Change</a>";
						$transferTargetAsString = " (pending transfer to '" . $transferTarget . "' - " . $transferLink . ")";
						$productNameAndTransferData = $productNameAndTransferData . $transferTargetAsString;
					}
					$licenseAsListItem = "<li>" . $productNameAndTransferData . "</li>";
					echo($licenseAsListItem);
				}
				echo "</ul>";
			}
		?>
		</div>

		<a href="ProductSummary.php">Browse Available Products</a><br />
		<a href='User.php'>Back to Account Details</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
