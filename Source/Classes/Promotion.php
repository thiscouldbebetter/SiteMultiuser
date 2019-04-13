<?php

class Promotion
{
	public $promotionID;
	public $description;
	public $discount;
	public $code;
	public $products;

	public function __construct($promotionID, $description, $discount, $code, $products)
	{
		$this->promotionID = $promotionID;
		$this->description = $description;
		$this->discount = $discount;
		$this->code = $code;
		$this->products = $products;
	}

	public function doesApplyToOrder($order)
	{
		$areAllProductsFromPromotionInOrderSoFar = true;

		$productsInPromotion = $this->products;
		$productBatchesInOrder = $order->productBatches;

		foreach ($productsInPromotion as $productInPromotion)
		{
			$productIDFromPromotion = $productInPromotion->productID;

			$isProductInOrder = false;
			foreach ($productBatchesInOrder as $productBatchInOrder)
			{
				$productIDFromOrder = $productBatchInOrder->productID;
				if ($productIDFromOrder == $productIDFromPromotion)
				{
					$isProductInOrder = true;
					break;
				}
			}

			if ($isProductInOrder == false)
			{
				$areAllProductsFromPromotionInOrderSoFar = false;
				break;
			}
		}

		return $areAllProductsFromPromotionInOrderSoFar;
	}
}

?>