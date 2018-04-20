<?php

class Order_Product
{
	public function __construct($orderProductID, $orderID, $productID, $quantity)
	{
		$this->orderProductID = $orderProductID;
		$this->orderID = $orderID;
		$this->productID = $productID;
		$this->quantity = $quantity;
	}

	public function price($productsAll)
	{
		$product = $productsAll[$this->productID];
		$pricePerUnit = $product->price;
		$priceForBatch = $pricePerUnit * $this->quantity;
		return $priceForBatch;
	}
}

?>