<?php declare(strict_types=1);

namespace LoyaltyProgram\Service;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItemFactoryHandler\LineItemFactoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class LineItemHandler implements LineItemFactoryInterface
{
    public const TYPE = 'reward-product';

    public function supports(string $type): bool
    {
        return $type === self::TYPE;
    }

    public function create(array $data, SalesChannelContext $context): LineItem
    {
        return new LineItem($data['id'], self::TYPE, $data['referencedId'] ?? null, 1);
    }

    public function update(LineItem $lineItem, array $data, SalesChannelContext $context): void
    {
        if (isset($data['referencedId'])) {
            $lineItem->setReferencedId($data['referencedId']);
        }
    }
}