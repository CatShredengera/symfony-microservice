<?php

namespace App\Filter;

use App\DTO\PriceEnquiryInterface;
use App\Entity\Promotion;
use App\Filter\Modifier\Factory\PriceModifierFactoryInterface;

class LowestPriceFilter implements PriceFilterInterface
{
    public function __construct(private readonly PriceModifierFactoryInterface $priceModifierFactory)
    {
    }

    public function apply(PriceEnquiryInterface $enquire, Promotion ...$promotions): PriceEnquiryInterface
    {
        $price = $enquire->getProduct()->getPrice();
        $enquire->setPrice($price);
        $quantity = $enquire->getQuantity();
        $lowestPrice = $quantity * $price;

        foreach ($promotions as $promotion) {
            $priceModifier = $this->priceModifierFactory->create($promotion->getType());

            $modifiedPrice = $priceModifier->modify($price, $quantity, $promotion, $enquire);

            if ($modifiedPrice < $lowestPrice) {
                $enquire->setDiscountPrice($modifiedPrice);
                $enquire->setPromotionId($promotion->getId());
                $enquire->setPromotionName($promotion->getName());

                $lowestPrice = $modifiedPrice;
            }
        }

        return $enquire;
    }
}