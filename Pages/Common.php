<?php

$siteRoot = $_SERVER["DOCUMENT_ROOT"] . "/Store/";
$classRoot = $siteRoot . "Classes/";
include($classRoot . "License.php");
include($classRoot . "LicenseTransferType.php");
include($classRoot . "MathHelper.php");
include($classRoot . "Notification.php");
include($classRoot . "Order.php");
include($classRoot . "Order_Product.php");
include($classRoot . "PageWriter.php");
include($classRoot . "PaymentClient.php");
include($classRoot . "PaymentClientSquare.php");
include($classRoot . "PersistenceClientMySQL.php");
include($classRoot . "Product.php");
include($classRoot . "Promotion.php");
include($classRoot . "Promotion_Product.php");
include($classRoot . "Session.php");
include($classRoot . "User.php");
include($classRoot . "WebClient.php");

$configuration = include($siteRoot . "Configuration.php");

Session::start($configuration);

?>
