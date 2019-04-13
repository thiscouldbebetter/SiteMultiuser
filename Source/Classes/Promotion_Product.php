<?php

class Promotion_Product
{
	public $promotionProductID;
	public $promotionID;
	public $productID;

	public function __construct($promotionProductID, $promotionID, $productID)
	{
		$this->promotionProductID = $promotionProductID;
		$this->promotionID = $promotionID;
		$this->productID = $productID;
	}
}

?>