<?php

class Product
{
	public $productID;
	public $name;
	public $imagePath;
	public $price;
	public $contentPath;

	public function __construct($productID, $name, $imagePath, $price, $contentPath)
	{
		$this->productID = $productID;
		$this->name = $name;
		$this->imagePath = $imagePath;
		$this->price = $price;
		$this->contentPath = $contentPath;
	}
}

?>
