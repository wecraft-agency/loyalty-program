<?php declare(strict_types=1);

namespace LoyaltyProgram\Core\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PointsCartProcessor implements CartProcessorInterface
{
    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
    {

        $newData = $data->get('loyalty_product_points');
        $points = 0;

        // Do stuff to the `$toCalculate` cart with your new data
        foreach ($toCalculate->getLineItems()->getFlat() as $lineItem) {
            if ( isset($lineItem->getPayload()['customFields']) && $lineItem->getPayload()['customFields']['loyalty_product_points'] ) {
                $quantity = $lineItem->getQuantity();
                $itemPoints = (int)$lineItem->getPayload()['customFields']['loyalty_product_points'];
                $points = (int)($points + ($quantity * $itemPoints));
            }
        }

        $data->set('loyalty_points_total', $points);
    }
}