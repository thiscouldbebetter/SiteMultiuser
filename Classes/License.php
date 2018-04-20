<?php

class License
{
	public $licenseID;
	public $userID;
	public $productID;
	public $transferTypeID;
	public $transferTarget;

	public function __construct($licenseID, $userID, $productID, $transferTypeID, $transferTarget)
	{
		$this->licenseID = $licenseID;
		$this->userID = $userID;
		$this->productID = $productID;
		$this->transferTypeID = $transferTypeID;
		$this->transferTarget = $transferTarget;
	}
}

?>
