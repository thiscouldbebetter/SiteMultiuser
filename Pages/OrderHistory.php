<?php include "Common.php"; ?>
<?php Session::verify(); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Order Details"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Order History:</b></label><br /><br />
		<div>
			<?php
				$session = $_SESSION["Session"];
				$userLoggedIn = $session->user;
				$userID = $userLoggedIn->userID;
				$persistenceClient = $_SESSION["PersistenceClient"];
				$orders = $persistenceClient->ordersGetByUserID($userID);
				$productsAll = $persistenceClient->productsGetAll();
				$numberOfOrders = count($orders);
				if ($numberOfOrders == 0)
				{
					echo "(none)";
				}
				else
				{
					echo "<ul>";
					foreach ($orders as $order)
					{
						$orderTotal = $order->priceTotal($productsAll);
						$timeCompleted = $order->timeCompleted;
						$orderAsString = "$" . $orderTotal . ", " . count($order->productBatches) . " products";
						if ($order->timeCompleted == null)
						{
							$orderAsString = "never completed";
						}
						else
						{
							$orderAsString = $orderAsString . ", completed " . $timeCompleted;
						}
						$orderID = $order->orderID;
						$orderDetailLink = "<a href='OrderDetails.php?orderID=" . $orderID . "'>Details</a>";
						$orderAsListItem = "<li>" . $orderAsString . " " . $orderDetailLink . "</li>";
						echo $orderAsListItem;
					}
					echo "</ul>";
				}
			?>
			<br />
			<br />
		</div><br />

		<a href='User.php'>Back to Account Details</a><br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
