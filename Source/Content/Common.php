<?php

$configuration = include("../../Configuration.php");

include("ContentHelper.php");
include("License.php");
include("LicenseTransferType.php");
include("MathHelper.php");
include("Notification.php");
include("Order.php");
include("Order_Product.php");
include("PageWriter.php");
include("PaymentClient.php");
include("PaymentClientSquare.php");
include("PersistenceClientMySQL.php");
include("Product.php");
include("Promotion.php");
include("Promotion_Product.php");
include("Session.php");
include("User.php");
include("WebClient.php");

Session::start($configuration);

?>
