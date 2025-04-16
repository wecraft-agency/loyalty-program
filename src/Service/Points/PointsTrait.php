<?php declare(strict_types=1);
namespace LoyaltyProgram\Service\Points;

use LoyaltyProgram\Service\LineItemHandler;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Checkout\Cart\Cart;

trait PointsTrait {

    /**
     * Get LoyaltyCustomer by customer id
     *
     * @param EntityRepository $entityRepository
     * @param Context $context
     * @return mixed|\Shopware\Core\Framework\DataAbstractionLayer\Search\TElement|null
     */
    public function getLoyaltyCustomer(EntityRepository $entityRepository, Context $context, $customerId) {

        // find entities
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('customerId', [$customerId]));

        // result
        $loyaltyCustomerResult = $entityRepository->search($criteria, $context);

        if ( $loyaltyCustomerResult->getTotal() > 0 ) {
            return $loyaltyCustomerResult->first();
        }

        // create customer
        $newLoyaltyCustomerId = Uuid::randomHex();

        $entityRepository->create([[
            'id' => $newLoyaltyCustomerId,
            'customerId' => $customerId,
        ]], $context);

        // return customer by id created
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('customerId', [$customerId]));
        $loyaltyCustomerResult = $entityRepository->search($criteria, $context);

        return $loyaltyCustomerResult->first();
    }

    /**
     * Get redemption by order id
     *
     * @param EntityRepository $entityRepository
     * @param Context $context
     * @param $orderId
     * @return mixed|\Shopware\Core\Framework\DataAbstractionLayer\Search\TElement|null
     */
    public function getRedemptionByOrder(EntityRepository $entityRepository, Context $context, $orderId) {
        // find entities
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('orderId', [$orderId]));

        // result
        $loyaltyRedemptionResult = $entityRepository->search($criteria, $context);

        if ( $loyaltyRedemptionResult->getTotal() > 0 ) {
            return $loyaltyRedemptionResult->first();
        }

        return null;
    }

    /**
     * @param OrderEntity|Cart $orderRepository
     * @param Context|SalesChannelContext $context
     * @return int
     */
    public function getSpentPointsByLineItems(OrderEntity|Cart $orderRepository, Context|SalesChannelContext $context) {
        $lineItems = $orderRepository->getLineItems();
        $orderSpentPoints = 0;

        foreach ( $lineItems->getElements() as $lineItem ) {
            if ( $lineItem->getType() === LineItemHandler::TYPE && isset($lineItem->getPayload()['points']) ) {
                $points = $lineItem->getPayload()['points'];
                $quantity = $lineItem->getQuantity();

                $orderSpentPoints = $orderSpentPoints + ((int)$points * (int)$quantity);
            }
        }

        return $orderSpentPoints;
    }

    /**
     * @param OrderEntity|Cart $orderRepository
     * @param Context|SalesChannelContext $context
     * @return int
     */
    public function getPointsByLineItemPoints(OrderEntity|Cart $orderRepository, Context|SalesChannelContext $context) {
        $lineItems = $orderRepository->getLineItems();
        $orderPoints = 0;

        foreach ( $lineItems->getElements() as $lineItem ) {
            $lineItemPayload = $lineItem->getPayload();
            $lineItemQuantity = $lineItem->getQuantity();

            if ( isset($lineItemPayload['customFields']['loyalty_product_points']) ) {
                if ( $lineItemPayload['customFields']['loyalty_product_points'] ) {
                    $orderPointsAdd = (int)$lineItemPayload['customFields']['loyalty_product_points'];

                    $orderPoints = $orderPoints + ($lineItemQuantity * $orderPointsAdd);
                }
            }
        }

        return $orderPoints;
    }

    /**
     * Calculate points by price
     * @param OrderEntity|Cart $orderRepository
     * @param Context|SalesChannelContext $context
     * @param float $multiplier
     * @return int
     */
    public function getPointsByLineItemPrice(OrderEntity|Cart $orderRepository, Context|SalesChannelContext $context, float $multiplier) {
        $lineItems = $orderRepository->getLineItems();
        $orderPoints = 0;


        foreach ( $lineItems->getElements() as $lineItem ) {
            $getUnitPrice = $lineItem->getPrice()->getUnitPrice();
            $lineItemQuantity = $lineItem->getQuantity();

            $productPoints = ($lineItemQuantity * ($getUnitPrice  * $multiplier));

            // round up
            $productPoints = ceil($productPoints / 10) * 10;

            // add to orderpoints
            $orderPoints = $productPoints + $orderPoints;
        }

        return $orderPoints;
    }
}