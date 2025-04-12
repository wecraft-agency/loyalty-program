<?php declare(strict_types=1);
namespace LoyaltyProgram\Service\Points;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Checkout\Cart\Cart;

trait PointsTrait {
    /**
     * Set points
     * @param EntityRepository $entityRepository
     * @param Context $context
     * @param $points
     * @param $entityId
     * @param $entity
     * @return void
     */
    public function setPoints(EntityRepository $entityRepository, Context $context, $points, $entityId, $entity, $fieldKey = 'points' ) {
        $entityRepository->update(
            [
                [
                    'id' => $entityId,
                    'customFields' => [
                        'loyalty_'.$entity.'_'.$fieldKey => $points,
                    ]
                ]
            ],
            $context
        );
    }

    /**
     * @param CustomerEntity $customerRepository
     * @param Context $context
     * @return int
     */
    public function getPointsByCustomer(CustomerEntity $customerRepository, Context $context ) {
        return $customerRepository->getCustomFields()['loyalty_customer_points'] ? (int)$customerRepository->getCustomFields()['loyalty_customer_points'] : 0;
    }

    /**
     * @param CustomerEntity $customerRepository
     * @param Context $context
     * @return int
     */
    public function getPointsPendingByCustomer(CustomerEntity $customerRepository, Context $context ) {
        return $customerRepository->getCustomFields()['loyalty_customer_points_pending'] ? (int)$customerRepository->getCustomFields()['loyalty_customer_points_pending'] : 0;
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

            if ( $lineItemPayload['customFields']['loyalty_product_points'] ) {
                $orderPointsAdd = (int)$lineItemPayload['customFields']['loyalty_product_points'];

                $orderPoints = $orderPoints + ($lineItemQuantity * $orderPointsAdd);
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