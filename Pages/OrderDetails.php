<?php include "Common.php"; ?>
<?php Session::verify(); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Order Details"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Products in Order:</b></label><br /><br />
		<form action="" method="post">
		<div>
			<?php
				$session = $_SESSION["Session"];
				$userLoggedIn = $session->user;
				$persistenceClient = $_SESSION["PersistenceClient"];
				if (isset($_GET["orderID"]) == true)
				{
					$orderID = $_GET["orderID"];
					$order = $persistenceClient->orderGetByID($orderID);
					if ($order->userID != $userLoggedIn->userID)
					{
						$order = null;
					}
				}
				else
				{
					$order = $userLoggedIn->orderCurrent;
				}
				if ($order == null)
				{
					echo "No order found!";
					die();
				}
				else
				{
					$orderID = $order->orderID;
					$productBatchesInOrder = $order->productBatches;
					$productsAll = $persistenceClient->productsGetAll();
					$numberOfBatches = count($productBatchesInOrder);
					if ($numberOfBatches == 0)
					{
						echo "(no items)";
					}
					else
					{
						echo "<ul>";
						for ($i = 0; $i < count($order->productBatches); $i++)
						{
							$productBatch = $order->productBatches[$i];
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
							$productPricePerUnit = $product->price;
							$productBatchPrice = ($productPricePerUnit * $quantity);
							if ($order->timeCompleted == null)
							{
								$productQuantitySelect = " x <input id='" . $controlID . "' name='" . $controlID . "' type='number' value='" . $quantity . "' onchange='document.forms[0].submit();'></input>\n";
								$productAsString = $productAsString . $productQuantitySelect;
								$productAsString = $productAsString . "@ $" . $productPricePerUnit . " each = $" . $productBatchPrice;
								$productRemoveLink = " <a href='OrderProductQuantitySet.php?productID=" . $productID . "&quantity=0'>Remove</a>";
								$productAsString = $productAsString . $productRemoveLink;
							}
							else
							{
								$productAsString .= " x " . $quantity . " @ $" . $productPricePerUnit . " each = $" . $productBatchPrice;
							}
							$productAsListItem = "<li>" . $productAsString . "</li>";
							echo($productAsListItem);
						}
						echo "</ul>";
					}
				}
			?>
			<br />
			<br />
			<label><b>Promotion:</b></label><br /><br />
			<label>
				<?php
					$promotion = $order->promotion;
					if ($order->timeCompleted == null)
					{
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
					}
					else
					{
						if ($promotion == null)
						{
							echo "(none)";
						}
						else
						{
							echo $promotion->description . " ($" . $promotion->discount . " off)";
						}
					}
				?>
			</label>
		</div><br />

		<?php if ($order->timeCompleted == null && $numberOfBatches > 0) echo"<a href='OrderCheckout.php'>Checkout</a>" ?><br />
		<a href="ProductSummary.php">Browse Available Products</a><br />
		<a href='User.php'>Back to Account Details</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
