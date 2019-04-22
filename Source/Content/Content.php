<?php
	include("Common.php");
	Session::verify();
	$productID = $_GET["productID"];
	ContentHelper::licenseVerifyForProductID($productID);
	$persistenceClient = $_SESSION["PersistenceClient"];
	$product = $persistenceClient->productGetByID($productID);
	$productScriptPath = "Products/" . $product->contentPath . "/Source/Program.js";
	$productScriptFile = fopen($productScriptPath, "r");
	$productScriptText = fread($productScriptFile, filesize($productScriptPath));
	fclose($productScriptFile);
?>

<html>
<body>

<script type="text/javascript">
<?php echo $productScriptText; ?>
</script>

</body>
</html>
