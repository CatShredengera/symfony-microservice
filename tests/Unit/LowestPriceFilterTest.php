<?php

namespace App\Tests\Unit;

use App\DTO\LowestPriceEnquire;
use App\Entity\Product;
use App\Entity\Promotion;
use App\Filter\LowestPriceFilter;
use App\Tests\ServiceTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class LowestPriceFilterTest extends ServiceTestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testLowestPricePromotionsFilteringIsAppliedCorrectly(): void
    {
        $product = new Product();
        $product->setPrice(100);

        $enquiry = new LowestPriceEnquire();
        $enquiry->setProduct($product);
        $enquiry->setQuantity(5);
        $enquiry->setRequestDate('2023-11-23');
        $enquiry->setVoucherCode('OU812');

        $promotions = $this->promotionsDataProvider();

        $lowestPriceFilter = $this->container->get(LowestPriceFilter::class);

        $filteredEnquire = $lowestPriceFilter->apply($enquiry, ...$promotions);

        $this->assertSame(100, $filteredEnquire->getPrice());
        $this->assertSame(250, $filteredEnquire->getDiscountPrice());
        $this->assertSame('Black Friday half price sale', $filteredEnquire->getPromotionName());
    }

    public function promotionsDataProvider(): array
    {
        $promotionOne = new Promotion();
        $promotionOne->setName('Black Friday half price sale');
        $promotionOne->setAdjustment(0.5);
        $promotionOne->setCriteria(["from" => "2022-11-25", "to" => "2024-11-28"]);
        $promotionOne->setType('date_range_multiplier');

        $promotionTwo = new Promotion();
        $promotionTwo->setName('Voucher OU812');
        $promotionTwo->setAdjustment(100);
        $promotionTwo->setCriteria(["code" => "OU812"]);
        $promotionTwo->setType('fixed_price_voucher');

        $promotionThere = new Promotion();
        $promotionThere->setName('Buy one get one free');
        $promotionThere->setAdjustment(0.5);
        $promotionThere->setCriteria(["minimum_quantity" => 2]);
        $promotionThere->setType('even_items_multiplier');

        return [$promotionOne, $promotionTwo, $promotionThere];
    }
}