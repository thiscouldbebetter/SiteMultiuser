<?php

class Order
{
	public $orderID;
	public $userID;
	public $promotion;
	public $status;
	public $timeStarted;
	public $timeUpdated;
	public $timeCompleted;
	public $paymentID;
	public $productBatches;

	public function __construct($orderID, $userID, $promotion, $status, $timeStarted, $timeUpdated, $timeCompleted, $paymentID, $productBatches)
	{
		$this->orderID = $orderID;
		$this->userID = $userID;
		$this->promotion = $promotion;
		$this->status = $status;
		$this->timeStarted = $timeStarted;
		$this->timeUpdated = $timeUpdated;
		$this->timeCompleted = $timeCompleted;
		$this->paymentID = $paymentID;
		$this->productBatches = $productBatches;
	}

	public function complete()
	{
		$this->status = "Complete";
		$now = new DateTime();
		$this->timeUpdated = $now;
		$this->timeCompleted = $now;
	}

	public function priceSubtotal($productsAll)
	{
		$returnValue = 0;

		foreach ($this->productBatches as $productBatch)
		{
			$batchPrice = $productBatch->price($productsAll);
			$returnValue += $batchPrice;
		}

		return $returnValue;
	}

	public function priceTotal($productsAll)
	{
		$returnValue = $this->priceSubtotal($productsAll);

		$promotion = $this->promotion;
		if ($promotion != null)
		{
			$doesPromotionApplyToOrder = $promotion->doesApplyToOrder($this);
			if ($doesPromotionApplyToOrder == true)
			{
				$discount = $promotion->discount;
				$returnValue -= $discount;
			}
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

			for ($i = 0; $i < $quantity; $i++)
			{
				$license = new License(null, $this->userID, $productID, null, null);
				$returnValues[] = $license;
			}
		}
		return $returnValues;
	}
}

?>
