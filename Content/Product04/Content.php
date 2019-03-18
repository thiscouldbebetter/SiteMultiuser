<?php include($_SERVER["DOCUMENT_ROOT"] . "/Store/Pages/Common.php"); ?>
<?php Session::verify(); ?>
<?php PageWriter::licenseVerifyForProductID(4); ?>

<html>
<body>

<p>If you can see this, you have a valid license for this product.</p>

</body>
</html>
