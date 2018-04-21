<?php include "Common.php"; ?>
<?php Session::verify(); ?>

<html>

<head><?php PageWriter::elementHeadWrite("Promotion Details"); ?></head>

<body>

	<?php PageWriter::headerWrite(); ?>

	<div class="divCentered">
		<label><b>Promotion Search:</b></label><br />
		<br />
		<div>
			<form method="post">
				<label>Code:</label>
				<input name="PromotionCode" value=""></input>
				<button type="submit">Search by Code</button><br />
			</form>

			<?php
				if (isset($_POST["PromotionCode"]) == false)
				{
					$promotion = null;
				}
				else
				{
					$promotionCode = $_POST["PromotionCode"];
					$persistenceClient = $_SESSION["PersistenceClient"];
					$promotion = $persistenceClient->promotionGetByCode($promotionCode);
					if ($promotion == null)
					{
						echo "No promotion was found with the entered code.";
					}
					else
					{
						$_SESSION["PromotionCode"] = $promotionCode;
						header("Location: Promotion.php");
					}
				}
			?>
		</div>
		<br />
		<a href="OrderCurrent.php">Back to Order</a><br />
		<a href="ProductSummary.php">Browse Products</a>
	</div>

	<?php PageWriter::footerWrite(); ?>

</body>
</html>
