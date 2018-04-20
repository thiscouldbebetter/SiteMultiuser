<?php

include("Classes/License.php");
include("Classes/LicenseTransferType.php");
include("Classes/MathHelper.php");
include("Classes/Notification.php");
include("Classes/Order.php");
include("Classes/Order_Product.php");
include("Classes/PageWriter.php");
include("Classes/PaypalClientData.php");
include("Classes/PersistenceClientMySQL.php");
include("Classes/Product.php");
include("Classes/Session.php");
include("Classes/User.php");

$configuration = include("Configuration.php");

if (isset($_SESSION) == false)
{
	session_start();
	if (isset($_SESSION["PersistenceClient"]) == false)
	{
		$persistenceClient = new PersistenceClientMySQL
		(
			$configuration["DatabaseServerName"],
			$configuration["DatabaseUsername"],
			$configuration["DatabasePassword"],
			$configuration["DatabaseName"]
		);
		$_SESSION["PersistenceClient"] = $persistenceClient;
	}
}

?>
