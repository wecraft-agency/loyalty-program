<?php declare(strict_types=1);
namespace LoyaltyProgram\Service\Points;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

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
     * @param OrderEntity $orderRepository
     * @param Context $context
     * @return void
     */
    public function getPointsByLineItemPoints(OrderEntity $orderRepository, Context $context) {
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
}