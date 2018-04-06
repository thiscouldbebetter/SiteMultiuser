<?php include "Common.php"; ?>

<html>
<body>

<?php 	

	$productID = $_GET["productID"];
	$session = $_SESSION["Session"];
	$userLoggedIn = $session->user;	
	$orderCurrent = $userLoggedIn->orderCurrent;
	$orderID = $orderCurrent->orderID;
	$productBatchesInOrder = $orderCurrent->productBatches;		
	$productBatchExisting = null;
	foreach ($productBatchesInOrder as $productBatch)
	{
		if ($productBatch->productID == $productID)
		{
			$productBatchExisting = $productBatch;
			break;
		}
	}
	if ($productBatchExisting != null)
	{
		$message = 
			"The specified product is already included in the current order."
			. "   To change the desired quantity, visit the <a href='OrderCurrent.php'>Current Order</a> page.";
		echo($message);
	}
	else
	{
		$productBatchNew = new Order_Product(null, $orderID, $productID, 1);
		$orderCurrent->productBatches[] = $productBatchNew;
		$persistenceClient = $_SESSION["PersistenceClient"];
		$persistenceClient->orderSave($orderCurrent);
		header("Location: OrderCurrent.php");
	}
?>	

</body>
</html>
