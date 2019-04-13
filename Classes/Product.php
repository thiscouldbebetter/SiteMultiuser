<?php

class Product
{
	public $productID;
	public $name;
	public $imagePath;
	public $price;
	public $contentPath;
	public $isActive;

	public function __construct($productID, $name, $imagePath, $price, $contentPath, $isActive)
	{
		$this->productID = $productID;
		$this->name = $name;
		$this->imagePath = $imagePath;
		$this->price = $price;
		$this->contentPath = $contentPath;
		$this->isActive = $isActive;
	}
}

?>
