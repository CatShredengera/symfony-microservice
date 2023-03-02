<?php

namespace App\DTO;

interface PriceEnquiryInterface extends PromotionEnquireInterface
{
    public function setPrice(int $price);

    public function setDiscountPrice(int $discountPrice);

    public function getQuantity(): ?int;
}