<?php declare(strict_types=1);

namespace LoyaltyProgram\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CheckoutOrderPlacedSubscriber implements EventSubscriberInterface
{
    private $requestStack;

    private EntityRepository $orderRepository;

    private EntityRepository $customerRepository;

    public function __construct(RequestStack $requestStack, EntityRepository $orderRepository, EntityRepository $customerRepository)
    {
        $this->requestStack = $requestStack;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutOrderPlacedEvent::class => 'onCheckoutOrderPlaced',
        ];
    }

    public function onCheckoutOrderPlaced(CheckoutOrderPlacedEvent $event): void
    {
        $order = $event->getOrder();
        $orderId = $order->getId();
        $customer = $order->getOrderCustomer()->getCustomer();
        $customerId = $customer->getId();
        $lineItems = $order->getLineItems();

        $orderPoints = 0;

        // return if customer is guest
        if ( $customer->getGuest() ) {
            return;
        }

        // iterate orderLineItems, calculate points if lineitem given points
        foreach ( $lineItems->getElements() as $lineItem ) {

            $lineItemPayload = $lineItem->getPayload();
            $lineItemQuantity = $lineItem->getQuantity();

            if ( $lineItemPayload['customFields']['loyalty_product_points'] ) {
                $orderPointsAdd = (int)$lineItemPayload['customFields']['loyalty_product_points'];

                $orderPoints = $orderPoints + ($lineItemQuantity * $orderPointsAdd);
            }
        }

        // return if orderPoints equals 0
        if ( $orderPoints === 0 ) {
            return;
        }

        // write data to order
        $this->orderRepository->update(
            [
                [
                    'id' => $orderId,
                    'customFields' => [
                        'loyalty_order_points' => (string)$orderPoints,
                    ]
                ]
            ],
            $event->getContext()
        );

        // handle points from customer
        $customerPoints = $customer->getCustomFields()['loyalty_customer_points'] ? (int)$customer->getCustomFields()['loyalty_customer_points'] : 0;
        $customerPoints = $customerPoints + $orderPoints;

        // write data to customer
        $this->customerRepository->update(
            [
                [
                    'id' => $customerId,
                    'customFields' => [
                        'loyalty_customer_points' => (string)$customerPoints,
                    ]
                ]
            ],
            $event->getContext()
        );
    }
}