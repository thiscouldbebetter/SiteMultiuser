<?php include("Common.php"); ?>
<?php Session::verify($configuration); ?>

<html>
<head>
	<?php PageWriter::elementHeadWrite("Order Payment"); ?>
</head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">

		<?php
			$session = $_SESSION["Session"];
			$userLoggedIn = $session->user;
			$orderCurrent = $userLoggedIn->orderCurrent;
			$persistenceClient = $_SESSION["PersistenceClient"];
			$productsAll = $persistenceClient->productsGetAll();
			$orderPriceTotal = $orderCurrent->priceTotal($productsAll);
			if ($orderPriceTotal == 0)
			{
				$userLoggedIn->orderCurrentComplete($paymentID, $persistenceClient);
				echo "Order completed successfully!";
			}
			else
			{
				echo "Order failed!  An attempt was made to complete a non-free order using the free payment process.";
			}
			echo "<br /><br />";
			echo "<a href='UserLicenses.php'>Show All User Licenses</a>";
		?>

		<br />

	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
