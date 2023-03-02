<?php

namespace App\Event;

use App\DTO\PromotionEnquireInterface;
use Symfony\Contracts\EventDispatcher\Event;

class AfterDtoCreatedEvent extends Event
{
    public const NAME = 'dto.created';

    public function __construct(protected PromotionEnquireInterface $dto)
    {
    }

    public function getDto(): PromotionEnquireInterface
    {
        return $this->dto;
    }
}