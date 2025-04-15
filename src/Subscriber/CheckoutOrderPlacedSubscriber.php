<?php declare(strict_types=1);

namespace LoyaltyProgram\Subscriber;

use LoyaltyProgram\Service\LineItemHandler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\CustomEntity\Xml\Entity;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Shopware\Core\Framework\Uuid\Uuid;

use LoyaltyProgram\Service\Points\PointsTrait;

class CheckoutOrderPlacedSubscriber implements EventSubscriberInterface
{
    /**
     * Trait
     */
    use PointsTrait;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EntityRepository
     */
    private EntityRepository $orderRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $customerRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $loyaltyCustomerRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $loyaltyOrderRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $loyaltyRedemptionRepository;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @param RequestStack $requestStack
     * @param EntityRepository $orderRepository
     * @param EntityRepository $customerRepository
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        RequestStack $requestStack,
        EntityRepository $orderRepository,
        EntityRepository $customerRepository,
        SystemConfigService $systemConfigService,
        EntityRepository $loyaltyCustomerRepository,
        EntityRepository $loyaltyOrderRepository,
        EntityRepository $loyaltyRedemptionRepository
    )
    {
        $this->requestStack = $requestStack;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->systemConfigService = $systemConfigService;
        $this->loyaltyCustomerRepository = $loyaltyCustomerRepository;
        $this->loyaltyOrderRepository = $loyaltyOrderRepository;
        $this->loyaltyRedemptionRepository = $loyaltyRedemptionRepository;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutOrderPlacedEvent::class => 'onCheckoutOrderPlaced',
        ];
    }

    /**
     * Set points pending, on order placed
     * @param CheckoutOrderPlacedEvent $event
     * @return void
     */
    public function onCheckoutOrderPlaced(CheckoutOrderPlacedEvent $event): void
    {
        // data
        $order = $event->getOrder();
        $orderId = $order->getId();

        // customer
        $customer = $order->getOrderCustomer()->getCustomer();
        $customerId = $customer->getId();

        // get current loyaltyCustomer extension
        $loyaltyCustomer = $this->getLoyaltyCustomer($this->loyaltyCustomerRepository, Context::createDefaultContext(), $customerId);

        // saleschannel
        $salesChannelId = $order->getSalesChannelId();

        // get calculation type
        $calculationType = $this->systemConfigService->get('LoyaltyProgram.config.pointsCalculationType', $salesChannelId);

        // return if customer is guest
        if ( $customer->getGuest() ) {
            return;
        }

        if ( $calculationType === 'price' ) {
            $pointsMultiplier = $this->systemConfigService->get('LoyaltyProgram.config.pointsCalculationPriceMultiplier', $salesChannelId);

            // calculate points by price
            $orderPoints = $this->getPointsByLineItemPrice($order, $event->getContext(), $pointsMultiplier);
        } else {
            $orderPoints = $this->getPointsByLineItemPoints($order, $event->getContext());
        }


        // create entry if points spent
        $spentPoints = 0;

        foreach ( $event->getOrder()->getLineItems()->getElements() as $lineItem) {
            if ( $lineItem->getType() === LineItemHandler::TYPE ) {
                $quantity = $lineItem->getQuantity();
                $points = $lineItem->getPayload()['points'];

                $spentPoints = $spentPoints + ($quantity * $points);
            }
        }

        if ( $spentPoints > 0 ) {
            // create redemption
            $this->loyaltyRedemptionRepository->create(
                [
                    [
                        'id' => Uuid::randomHex(),
                        'orderId' => $orderId,
                        'customerId' => $customerId,
                        'points' => (int)$spentPoints,
                        'type' => 'spent',
                        'status' => 'finished'
                    ]
                ],
                Context::createDefaultContext()
            );
        }

        // return if orderPoints equals 0
        if ( $orderPoints > 0 ) {
            // create redemption
            $this->loyaltyRedemptionRepository->create(
                [
                    [
                        'id' => Uuid::randomHex(),
                        'orderId' => $orderId,
                        'customerId' => $customerId,
                        'points' => (int)$orderPoints,
                        'type' => 'awarded',
                        'status' => 'pending'
                    ]
                ],
                Context::createDefaultContext()
            );
        }

        // update pending points
        $this->loyaltyCustomerRepository->update(
            [
                [
                    'id' => $loyaltyCustomer->getId(),
                    'points' => (int)$loyaltyCustomer->getPoints() - (int)$spentPoints,
                    'pointsPending' => (int)$loyaltyCustomer->getPointsPending() + (int)$orderPoints
                ]
            ],
            Context::createDefaultContext()
        );

        // write data to order
        $this->loyaltyOrderRepository->create(
            [
                [
                    'id' => Uuid::randomHex(),
                    'orderId' => $orderId,
                    'points' => (int)$orderPoints,
                    'pointsSpent' => (int)$spentPoints
                ]
            ],
            Context::createDefaultContext()
        );
    }
}