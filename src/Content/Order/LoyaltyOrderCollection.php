<?php declare(strict_types=1);

namespace LoyaltyProgram\Content\Order;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class LoyaltyOrderCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return LoyaltyOrderEntity::class;
    }
}