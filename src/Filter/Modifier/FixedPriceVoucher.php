<?php

namespace App\Filter\Modifier;

use App\DTO\PromotionEnquireInterface;
use App\Entity\Promotion;

class FixedPriceVoucher implements PriceModifierInterface
{
    public function modify(int $price, int $quantiy, Promotion $promotion, PromotionEnquireInterface $enquire): int
    {
        if (!($enquire->getVoucherCode() === $promotion->getCriteria()['code'])) {
            return $price * $quantiy;
        }
        return $promotion->getAdjustment() * $quantiy;
    }
}