<?php declare(strict_types=1);

namespace LoyaltyProgram\Content\Customer;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class LoyaltyCustomerCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return LoyaltyCustomerEntity::class;
    }
}