<?php declare(strict_types=1);

namespace LoyaltyProgram\Core\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;

use LoyaltyProgram\Service\Points\PointsTrait;

class PointsCartProcessor implements CartProcessorInterface
{
    /**
     * Trait
     */
    use PointsTrait;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(SystemConfigService $systemConfigService) {
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * Add points to cart, calculatet by config calculaten type
     * @param CartDataCollection $data
     * @param Cart $original
     * @param Cart $cart
     * @param SalesChannelContext $context
     * @param CartBehavior $behavior
     * @return void
     */
    public function process(CartDataCollection $data, Cart $original, Cart $cart, SalesChannelContext $context, CartBehavior $behavior): void
    {
        $points = 0;

        // salesChannelId
        $salesChannelId = $context->getSalesChannelId();

        // get calculation type
        $calculationType = $this->systemConfigService->get('LoyaltyProgram.config.pointsCalculationType', $salesChannelId);


        if ( $calculationType === 'price' ) {
            $pointsMultiplier = $this->systemConfigService->get('LoyaltyProgram.config.pointsCalculationPriceMultiplier', $salesChannelId);
            // calculate points by price
            $points = $this->getPointsByLineItemPrice($cart, $context, $pointsMultiplier);
        } else {
            // calculate points by product
            $points = $this->getPointsByLineItemPoints($cart, $context);
        }

        $data->set('loyalty_points_total', $points);
    }
}