<?php

namespace App\Tests\Unit;

use App\DTO\LowestPriceEnquire;
use App\Entity\Promotion;
use App\Filter\Modifier\DateRangeMultiplier;
use App\Filter\Modifier\EvenItemsMultiplier;
use App\Filter\Modifier\FixedPriceVoucher;
use App\Tests\ServiceTestCase;

class PriceMidifiersTest extends ServiceTestCase
{
    public function testDateRangeMultiplierReturnsACorrectlyModifiedPrice(): void
    {
        $enquiry = new LowestPriceEnquire();
        $enquiry->setQuantity(5);
        $enquiry->setRequestDate('2022-11-27');

        $promotion = new Promotion();
        $promotion->setName('Black Friday half price sale');
        $promotion->setAdjustment(0.5);
        $promotion->setCriteria(["from" => "2022-11-25", "to" => "2024-11-28"]);
        $promotion->setType('date_range_multiplier');

        $dataRangeModified = new DateRangeMultiplier();

        $modifiedPrice = $dataRangeModified->modify(100, 5, $promotion, $enquiry);

        $this->assertEquals(250, $modifiedPrice);
    }

    public function testFixedPriceVoucherReturnsACorrectlyModifiedPrice(): void
    {
        $fixedPriceVoucher = new FixedPriceVoucher();

        $promotion = new Promotion();
        $promotion->setName('Voucher OU812');
        $promotion->setAdjustment(100);
        $promotion->setCriteria(["code" => "OU812"]);
        $promotion->setType('fixed_price_voucher');

        $enquiry = new LowestPriceEnquire();
        $enquiry->setQuantity(5);
        $enquiry->setVoucherCode('OU812');

        $modifiedPrice = $fixedPriceVoucher->modify(150, 5, $promotion, $enquiry);

        $this->assertEquals(500, $modifiedPrice);
    }

    public function testEvenItemsMultiplierReturnsACorrectlyModifiedPrice(): void
    {
        $eventItemsMultiplier = new EvenItemsMultiplier();

        $promotion = new Promotion();
        $promotion->setName('Buy one get one free');
        $promotion->setAdjustment(0.5);
        $promotion->setCriteria(["minimum_quantity" => "2"]);
        $promotion->setType('even_items_multiplier');

        $enquiry = new LowestPriceEnquire();
        $enquiry->setQuantity(5);

        $modifiedPrice = $eventItemsMultiplier->modify(100, 5, $promotion, $enquiry);

        $this->assertEquals(300, $modifiedPrice);
    }

    public function testEvenItemsMultiplierCorrectlyCalculatedAlternatives(): void
    {
        $eventItemsMultiplier = new EvenItemsMultiplier();

        $promotion = new Promotion();
        $promotion->setName('Buy one get one half price');
        $promotion->setAdjustment(0.75);
        $promotion->setCriteria(["minimum_quantity" => "2"]);
        $promotion->setType('even_items_multiplier');

        $enquiry = new LowestPriceEnquire();
        $enquiry->setQuantity(5);

        $modifiedPrice = $eventItemsMultiplier->modify(100, 5, $promotion, $enquiry);

        $this->assertEquals(400, $modifiedPrice);
    }
}