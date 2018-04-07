<?php include "Common.php"; ?>

<html>
<body>
	<label><b>Current Order:</b></label><br />
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
				echo($productName);
				echo("\n");
				$quantity = $productBatch->quantity;
				$productQuantitySelect = " x <input id='" . $controlID . "' name='" . $controlID . "' type='number' value='" . $quantity . "' onchange='document.forms[0].submit();'></input>\n";
				echo($productQuantitySelect);
				$productPricePerUnit = $product->price;
				echo("@ $" . $productPricePerUnit . " each = $" . ($productPricePerUnit * $quantity) );
				$productRemoveLink = " <a href='OrderProductQuantitySet.php?productID=" . $productID . "&quantity=0'>Remove</a>";
				echo($productRemoveLink);
				echo("<br />");
			}
		}
	?>	
	</div>
	<a href='OrderCheckout.php'>Checkout</a>
		
	<a href="ProductSummary.php">Browse Available Products</a>
	
</body>
</html>
