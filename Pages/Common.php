<?php

$configuration = include("../Configuration.php");

$errorReportingEnabled = $configuration["ErrorReportingEnabled"];
error_reporting($errorReportingEnabled ? 1 : 0);

$documentRoot = $_SERVER["DOCUMENT_ROOT"] . "/";
$appDirectory = $configuration["AppDirectory"];
$appRoot = $documentRoot . $appDirectory . "/";
$classRoot = $appRoot . "Classes/";
$includePaths = $appRoot . ":" . $classRoot;
set_include_path($includePaths);

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
