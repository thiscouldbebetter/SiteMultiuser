<?php include "Common.php"; ?>
<?php PageWriter::sessionVerify(); ?>

<html>

<head><?php PageWriter::elementHeadWrite("License Transfer"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>License Transfer Claim:</b></label><br />
		<br />
		<div style="text-align:center">
			<div>
				<form method="post">
					<label>Vendor Username:</label>
					<input name="VendorUsername"></input><br />

					<label>Product Name:</label>
					<select Name="ProductID">
						<?php
							$persistenceClient = $_SESSION["PersistenceClient"];
							$productsAll = $persistenceClient->productsGetAll();
							foreach ($productsAll as $product)
							{
								$productID = $product->productID;
								$productName = $product->name;
								$productAsOption = "<option value='" . $productID . "'>" . $productName . "</option>";
								echo $productAsOption;
							}
						?>
					</select><br />

					<label>Transfer Code:</label>
					<input name="TransferCode"></input><br />
					<br />

					<button type='submit'>Claim Transfer</button>


					<?php
						$messageInstructions = "Specify a valid vendor username, product, and transfer code.";

						if
						(
							(isset($_POST["VendorUsername"]) == false)
							|| (isset($_POST["ProductID"]) == false)
							|| (isset($_POST["TransferCode"]) == false)
						)
						{
							PageWriter::displayStatusMessage($messageInstructions);
						}
						else
						{
							$vendorUsername = $_POST["VendorUsername"];
							$productID = $_POST["ProductID"];
							$transferCodeEntered = $_POST["TransferCode"];

							$vendorUser = $persistenceClient->userGetByUsername($vendorUsername);
							if ($vendorUser == null)
							{
								PageWriter::displayStatusMessage($messageInstructions);
							}
							else
							{
								if ($productID == null)
								{
									PageWriter::displayStatusMessage($messageInstructions);
								}
								else
								{
									$licenseMatching = null;
									foreach ($vendorUser->licenses as $license)
									{
										$transferTypeID = $license->transferTypeID;
										$transferCode = $license->transferTarget;

										if ($transferTypeID == 3 && $transferCode == $transferCodeEntered)
										{
											$licenseMatching = $license;
											break;
										}
									}

									if ($licenseMatching == null)
									{
										PageWriter::displayStatusMessage($messageInstructions);
									}
									else
									{
										$licenseToTransfer->userID = $userLoggedIn->userID;
										$licenseToTransfer->transferTypeID = null;
										$licenseToTransfer->transferTarget = null;
										$userLoggedIn->licenses[] = $licenseToTransfer;
										$persistenceClient->licenseSave($licenseToTransfer);
										echo "Transfer claimed successfully!";
									}
								}
							}
						}
					?>
				</form>
			</div>
		</div>
		<br />
		<a href="UserLicenses.php">Back to All User Licenses</a>
	</div>

	<?php PageWriter::footerWrite(); ?>

	<script type="text/javascript">

		// event handlers
		function selectTransferType_Changed(event)
		{
			var selectTransferType = event.target;
			var transferTypeID = selectTransferType.value;
			var divTransferTarget = document.getElementById("divTransferTarget");
			if (transferTypeID == null || transferTypeID == 0)
			{
				divTransferTarget.style.display = "none";
			}
			else
			{
				divTransferTarget.style.display = "inline";

				var inputTransferTarget = document.getElementById("inputTransferTarget");
				inputTransferTarget.value = "";
				inputTransferTarget.readonly = false;

				var transferTarget;
				var transferTargetTypeName;

				if (transferTypeID == 1)
				{
					transferTargetTypeName = "Username";
					transferTarget = "";
				}
				else if (transferTypeID == 2)
				{
					transferTargetTypeName = "Email Address";
					transferTarget = "";
				}
				else if (transferTypeID == 3)
				{
					transferTargetTypeName = "Transfer Code";
					inputTransferTarget.readonly = true;
					var inputRandomCode = document.getElementById("inputRandomCode");
					transferTarget = inputRandomCode.value;
				}
				else
				{
					throw("Unrecognized transfer type!");
				}

				var labelTransferTarget = document.getElementById("labelTransferTarget");
				labelTransferTarget.innerText = transferTargetTypeName + ":";
				inputTransferTarget.value = transferTarget;
			}
		}

	</script>

</body>
</html>
