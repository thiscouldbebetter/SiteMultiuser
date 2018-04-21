<?php include "Common.php"; ?>
<?php Session::verify(); ?>

<html>

<head><?php PageWriter::elementHeadWrite("License Transfer"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>License Transfer Claim:</b></label><br />
		<br />
		<div style="text-align:center">
			<div>
				<label>
				<?php
					$session = $_SESSION["Session"];
					$userLoggedIn = $session->user;
					$persistenceClient = $_SESSION["PersistenceClient"];
					$transfersIncoming = $persistenceClient->licensesGetByTransferTarget($userLoggedIn->username, $userLoggedIn->emailAddress);
					$licenseID = $_GET["licenseID"];
					foreach ($transfersIncoming as $license)
					{
						if ($license->licenseID == $licenseID)
						{
							$licenseToTransfer = $license;
							break;
						}
					}

					$message = "License does not exist or is not being transferred to current user.";
					if ($licenseToTransfer == null)
					{
						echo($message);
					}
					else
					{
						$transferTypeID = $licenseToTransfer->transferTypeID;
						$transferTarget = $licenseToTransfer->transferTarget;
						$isLicenseTargetedToUserLoggedIn =
						(
							( $transferTypeID = 1 && $transferTarget = $userLoggedIn->username )
							|| ( $transferTypeID = 2 && $transferTarget = $userLoggedIn->emailAddress )
						);
						if ($isLicenseTargetedToUserLoggedIn == true)
						{
							$productID = $licenseToTransfer->productID;
							$product = $persistenceClient->productGetByID($productID);
							$productName = $product->name;
							echo $productName;
						}
					}
				?>
				</label><br />
				<br />
				<img src='<?php echo($product->imagePath); ?>' /><br />
				<br />
				<label>from
					<?php
						$userTransferredFrom = $persistenceClient->userGetByID($licenseToTransfer->userID);
						echo $userTransferredFrom->username;
					?>
				</label>
				<br /><br />
				<form method="post">
					<?php
						if (isset($_POST["IsClaimed"]) == false)
						{
							echo "<button type='submit' name='IsClaimed'>Claim Transfer</button>";
						}
						else
						{
							$licenseToTransfer->userID = $userLoggedIn->userID;
							$licenseToTransfer->transferTypeID = null;
							$licenseToTransfer->transferTarget = null;
							$userLoggedIn->licenses[] = $licenseToTransfer;
							$persistenceClient->licenseSave($licenseToTransfer);
							echo "Transfer claimed!";
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
