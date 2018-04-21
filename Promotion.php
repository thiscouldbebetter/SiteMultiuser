<?php include "Common.php"; ?>
<?php Session::verify(); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Promotion Details"); ?></head>

<body>

	<?php
		$promotionCode = $_SESSION["PromotionCode"];
		$persistenceClient = $_SESSION["PersistenceClient"];
		$promotion = $persistenceClient->promotionGetByCode($promotionCode);

		if ($promotion != null)
		{
			$session = $_SESSION["Session"];
			$userLoggedIn = $session->user;
			$orderCurrent = $userLoggedIn->orderCurrent;

			if (isset($_POST["Submitted"]) == true)
			{
				$orderCurrent->promotion = $promotion;
				header("Location: OrderCurrent.php");
			}
		}
	?>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Promotion Details:</b></label><br />
		<br />
		<div>
			<form method="post">
				<label>Code:</label>
				<label><?php echo $promotion->code; ?></label><br />
				<br />
				<div>
					<label>Descripton:</label>
					<label>
						<?php
							if ($promotion != null)
							{
								$promotionDescription = $promotion->description;
								echo $promotionDescription;
							}
						?>
					</label><br />
					<br />
					<label>Products:</label>
					<ul>
						<?php
							if ($promotion != null)
							{
								foreach ($promotion->products as $product)
								{
									$productName = $product->name;
									$productAsListItem = "<li>" . $productName . "</li>\n";
									echo $productAsListItem;
								}
							}
						?>
					</ul>
					<label>Discount: $<?php if ($promotion != null) { echo($promotion->discount); } ?></label><br/>
				</div>
				<br />
				<button type="submit" Name="Submitted">Add Promotion to Order</button>
			</form>
		</div>
		<a href="OrderCurrent.php">Back to Order</a><br />
		<a href="ProductSummary.php">Browse Products</a>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
