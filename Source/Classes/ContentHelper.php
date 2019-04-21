<?php

class ContentHelper
{
	public static function licenseVerifyForProductID($productIDToVerify)
	{
		$session = $_SESSION["Session"];
		$userLoggedIn = $session->user;
		$licenses = $userLoggedIn->licenses;
		$isUserLicensedForProduct = false;
		foreach ($licenses as $license)
		{
			$licenseProductID = $license->productID;
			if ($licenseProductID == $productIDToVerify)
			{
				$isUserLicensedForProduct = true;
				break;
			}
		}
		if ($isUserLicensedForProduct == false)
		{
			echo "You do not yet have a license to access this content.  ";
			echo "You can buy a license by clicking the link below.<br />";
			echo "<a href='../../Pages/Product.php?productID=" . $productIDToVerify . "'>View Product</a>";
			die();
		}
	}
}

?>
