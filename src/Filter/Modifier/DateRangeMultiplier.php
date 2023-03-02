<?php

namespace App\Filter\Modifier;

use App\DTO\PromotionEnquireInterface;
use App\Entity\Promotion;

class DateRangeMultiplier implements PriceModifierInterface
{
    public function modify(int $price, int $quantiy, Promotion $promotion, PromotionEnquireInterface $enquire): int
    {
        $requestDate = date_create($enquire->getRequestDate());
        $from = date_create($promotion->getCriteria()['from']);
        $to = date_create($promotion->getCriteria()['to']);

        if (!($requestDate >= $from && $requestDate < $to)) {
            return $price * $quantiy;
        }

        return  ($price * $quantiy) * $promotion->getAdjustment();
    }
}