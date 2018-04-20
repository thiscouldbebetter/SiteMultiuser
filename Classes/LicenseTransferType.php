<?php

class LicenseTransferType
{
	public $licenseTransferTypeID;
	public $name;
	public $description;

	public function __construct($licenseTransferTypeID, $name, $description)
	{
		$this->licenseTransferTypeID = $licenseTransferTypeID;
		$this->name = $name;
		$this->description = $description;
	}
}

?>