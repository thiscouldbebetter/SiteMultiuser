<?php include "Common.php"; ?>
<?php PageWriter::sessionVerify(); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Order Details"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Current Order:</b></label><br /><br />
		<form name="formOrderCurrent" action="" method="post">
		<div>
			<?php
				$session = $_SESSION["Session"];
				$userLoggedIn = $session->user;
				$orderCurrent = $userLoggedIn->orderCurrent;
				$orderID = $orderCurrent->orderID;
				$productBatchesInOrder = $orderCurrent->productBatches;
				$persistenceClient = $_SESSION["PersistenceClient"];
				$productsAll = $persistenceClient->productsGetAll();
				$numberOfBatches = count($productBatchesInOrder);
				if ($numberOfBatches == 0)
				{
					echo "(no items)";
				}
				else
				{
					echo "<ul>";
					for ($i = 0; $i < count($orderCurrent->productBatches); $i++)
					{
						$productBatch = $orderCurrent->productBatches[$i];
						$productID = $productBatch->productID;
						$controlID = "Product" . $productID . "Quantity";
						if (isset($_POST[$controlID]) == true)
						{
							$quantityChanged = $_POST[$controlID];
							unset($_POST[$controlID]) ;
							$productBatch->quantity = $quantityChanged;
						}
						$product = $productsAll[$productID];
						$productName = $product->name;
						$productAsString = $productName;
						$quantity = $productBatch->quantity;
						$productQuantitySelect = " x <input id='" . $controlID . "' name='" . $controlID . "' type='number' value='" . $quantity . "' onchange='document.forms[0].submit();'></input>\n";
						$productAsString = $productAsString . $productQuantitySelect;
						$productPricePerUnit = $product->price;
						$productAsString = $productAsString . "@ $" . $productPricePerUnit . " each = $" . ($productPricePerUnit * $quantity);
						$productRemoveLink = " <a href='OrderProductQuantitySet.php?productID=" . $productID . "&quantity=0'>Remove</a>";
						$productAsString = $productAsString . $productRemoveLink;
						$productAsListItem = "<li>" . $productAsString . "</li>";
						echo($productAsListItem);
					}
					echo "</ul>";
				}
			?>
			<br />
			<br />
			<label><b>Promotion:</b></label><br /><br />
			<label>
				<?php
					$promotion = $orderCurrent->promotion;
					if ($promotion == null)
					{
						echo "(none)";
						$promotionAddLink = " <a href='PromotionSearch.php'>Add a Promotion by Code</a>";
						echo $promotionAddLink;
					}
					else
					{
						echo $promotion->description;
						$promotionRemoveLink = " <a href='OrderPromotionRemove.php'>Remove</a>";
						echo $promotionRemoveLink;
					}
				?>
			</label>
		</div><br />

		<?php if ($numberOfBatches > 0) echo"<a href='OrderCheckout.php'>Checkout</a>" ?><br />
		<a href="ProductSummary.php">Browse Available Products</a><br />
		<a href='User.php'>Back to Account Details</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
