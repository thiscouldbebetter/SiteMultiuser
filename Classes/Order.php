<?php

class Order
{
	public $orderID;
	public $userID;
	public $status;
	public $timeCompleted;
	public $productBatches;

	public function __construct($orderID, $userID, $status, $timeCompleted, $productBatches)
	{
		$this->orderID = $orderID;
		$this->userID = $userID;
		$this->status = $status;
		$this->timeCompleted = $timeCompleted;
		$this->productBatches = $productBatches;
	}

	public function complete()
	{
		$this->status = "Complete";
		$this->timeCompleted = new DateTime();
	}

	public function priceTotal($productsAll)
	{
		$returnValue = 0;

		foreach ($this->productBatches as $productBatch)
		{
			$productID = $productBatch->productID;
			$product = $productsAll[$productID];
			$productPrice = $product->price;
			$returnValue += $productPrice;
		}

		return $returnValue;
	}

	public function productBatchesWithQuantityZeroRemove()
	{
		for ($i = 0; $i < count($this->productBatches); $i++)
		{

			$productBatch = $this->productBatches[$i];
			$quantity = $productBatch->quantity;
			if ($quantity <= 0)
			{
				array_splice($this->productBatches, $i, 1);
				$i--;
			}
		}

	}

	public function toLicenses()
	{
		$returnValues = array();
		foreach ($this->productBatches as $productBatch)
		{
			$productID = $productBatch->productID;
			$quantity = $productBatch->quantity;

			for ($i = 0; $i < quantity; $i++)
			{
				$license = new License(null, $this->userID, $productID, null, null);
				$returnValues[] = $license;
			}
		}
		return $returnValues;
	}
}

?>