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
	$productBatchToSet = null;
	foreach ($productBatchesInOrder as $productBatch)
	{
		if ($productBatch->productID == $productID)
		{
			$productBatchToSet = $productBatch;
			break;
		}
	}

	if ($productBatchToSet == null)
	{
		$productBatchToSet = new Order_Product(null, $orderID, $productID, 1);
		$orderCurrent->productBatches[] = $productBatchToSet;
	}

	$productBatchToSet->quantity = $_GET["quantity"];

	$orderCurrent->productBatchesWithQuantityZeroRemove();

	$persistenceClient = $_SESSION["PersistenceClient"];
	$persistenceClient->orderSave($orderCurrent);

	header("Location: OrderCurrent.php");

?>	

</body>
</html>
