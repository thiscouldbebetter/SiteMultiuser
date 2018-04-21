<?php include "Common.php"; ?>
<?php Session::verify(); ?>

<html>

<head><?php PageWriter::elementHeadWrite("License Transfer"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>License Transfer:</b></label><br />
		<br />
		<div style="text-align:center">
			<div>
				<label>
				<?php
					$session = $_SESSION["Session"];
					$userLoggedIn = $session->user;
					$licenses = $userLoggedIn->licenses;
					$licenseID = $_GET["licenseID"];
					foreach ($licenses as $license)
					{
						if ($license->licenseID == $licenseID)
						{
							$licenseToTransfer = $license;
							break;
						}
					}
					if ($licenseToTransfer == null || $licenseToTransfer->userID != $userLoggedIn->userID)
					{
						echo("License does not exist or is not owned by current user.");
						die();
					}
					$productID = $licenseToTransfer->productID;
					$persistenceClient = $_SESSION["PersistenceClient"];
					$product = $persistenceClient->productGetByID($productID);
					$productName = $product->name;
					echo $productName;
				?>
				</label><br />
				<br />
				<img src='<?php echo($product->imagePath); ?>' /><br />
			</div>
			<br />
			<form method="post">
				<label>Transfer Type:</label>
				<select id="selectTransferType" name="TransferTypeID" onchange="selectTransferType_Changed(event);">
					<option value="0">None</option>
					<?php
						if (isset($_POST["TransferTypeID"]) == true)
						{
							$transferTypeIDSelected = $_POST["TransferTypeID"];
						}
						else
						{
							$transferTypeIDSelected = null;
						}

						$licenseTransferTypes = $persistenceClient->licenseTransferTypesGetAll();

						foreach ($licenseTransferTypes as $transferType)
						{
							$transferTypeID = $transferType->licenseTransferTypeID;
							$selected = ($transferTypeID == $transferTypeIDSelected ? "selected" : "");
							echo "<option value='". $transferTypeID . "' " . $selected . ">" . $transferType->description . "</option>\n";
						}
					?>
				</select>
				<br />
				<div id="divTransferTarget" <?php if ($transferTypeIDSelected >= 1 && $transferTypeIDSelected <= 3) {} else { echo("style='display:none'"); } ?> >
					<label id="labelTransferTarget">Transfer Target:</label>
					<input id="inputTransferTarget" name="TransferTarget"></input>
					<input id="inputRandomCode" style="display:none" value='<?php echo MathHelper::randomCodeGenerate(); ?>'></input>
				</div>
				<button type="submit">Save</button>
			</form>
		</div>

		<div>
		<?php
			if (isset($_POST["TransferTypeID"]) && isset($_POST["TransferTarget"]) )
			{
				$transferTypeID = $_POST["TransferTypeID"];
				$transferTarget = $_POST["TransferTarget"];

				$isTransferValid = false;

				if ($transferTypeID == 0)
				{
					$transferTypeID = null;
					$transferTarget = null;
					$isTransferValid = true;
				}
				else if ($transferTarget == "")
				{
					echo "No transfer target specified.";
				}
				else if ($transferTypeID == 1) // username
				{
					$userFound = $persistenceClient->userGetByUsername($transferTarget);
					if ($userFound == null)
					{
						echo "No user exists with the specified username.";
					}
					else
					{
						$isTransferValid = true;
					}
				}
				else if ($transferTypeID == 2) // email address
				{
					$isTransferValid = true;
				}
				else if ($transferTypeID == 3) // transfer code
				{
					$isTransferValid = true; // todo
				}

				if ($isTransferValid == true)
				{
					$licenseToTransfer->transferTypeID = $transferTypeID;
					$licenseToTransfer->transferTarget = $transferTarget;
					$persistenceClient->licenseSave($licenseToTransfer);

					header("Location: UserLicenses.php");
				}
			}
		?>
		</div>

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
