<?php

namespace App\Filter\Modifier;

use App\DTO\PromotionEnquireInterface;
use App\Entity\Promotion;

class EvenItemsMultiplier implements PriceModifierInterface
{
    public function modify(int $price, int $quantiy, Promotion $promotion, PromotionEnquireInterface $enquire): int
    {
        if ($quantiy < 2) {
            return $price * $quantiy;
        }

        $oddCount = $quantiy % 2;

        $evenCount = $quantiy - $oddCount;

        return (($evenCount * $price) * $promotion->getAdjustment()) + ($oddCount * $price);
    }
}