<?php include("Common.php"); ?>
<?php Session::verify(); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Licenses Owned"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<form name="formLicensesOwned" action="" method="post">
		<div>
			<label><b>Licenses Owned:</b></label><br />

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
							$contentLink = "<a href='" . $product->contentPath . "'>Content</a>";
							$transferLink = "<a href='LicenseTransfer.php?licenseID=" . $licenseID . "'>Transfer</a>";
							$productNameAndTransferData = $productNameAndTransferData . $contentLink . " " . $transferLink;
						}
						else
						{
							$transferType = $licenseTransferTargetTypes[$transferTypeID];
							$transferTarget = $license->transferTarget;
							$transferLink = "<a href='LicenseTransfer.php?licenseID=" . $licenseID . "'>Change</a>";
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
		<div>
			<label><b>License Transfers Incoming from Other Users:</b></label><br />
			<div>
				<?php
					$transfersIncoming = $persistenceClient->licensesGetByTransferTarget($userLoggedIn->username, $userLoggedIn->emailAddress);
					$numberOfTransfersIncoming = count($transfersIncoming);
					if ($numberOfTransfersIncoming == 0)
					{
						echo "(no items)";
					}
					else
					{
						echo "<ul>";
						foreach ($transfersIncoming as $transfer)
						{
							$userID = $transfer->userID;
							$userTransferring = $persistenceClient->userGetByID($userID);
							$productID = $transfer->productID;
							$product = $productsAll[$productID];
							$productName = $product->name;
							$licenseID = $transfer->licenseID;
							$claimLink = "<a href='LicenseTransferClaim.php?licenseID=" . $licenseID. "'>Claim</a>";
							$transferAsString = "'" . $productName . "' from " . $userTransferring->username . " - " . $claimLink;
							$transferAsListItem = "<li>" . $transferAsString . "</li>";
							echo($transferAsListItem);
						}
						echo "</ul>";
					}
				?>
			</div>
			<a href='LicenseTransferClaimByCode.php'>Claim a License by Transfer Code</a>

		</div>
		<br />
		<a href="ProductSummary.php">Browse Available Products</a><br />
		<a href='User.php'>Back to Account Details</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
