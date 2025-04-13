<?php declare(strict_types=1);

namespace LoyaltyProgram\Service\Order;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Checkout\Order\Event\OrderStateMachineStateChangeEvent;
use Shopware\Core\System\System;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use LoyaltyProgram\Service\Points\PointsTrait;

class ListenToOrderChanges implements EventSubscriberInterface
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
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @param RequestStack $requestStack
     * @param EntityRepository $orderRepository
     * @param EntityRepository $customerRepository
     */
    public function __construct(
        RequestStack $requestStack,
        EntityRepository $orderRepository,
        EntityRepository $customerRepository,
        SystemConfigService $systemConfigService,
        EntityRepository $loyaltyCustomerRepository
    )
    {
        $this->requestStack = $requestStack;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->systemConfigService = $systemConfigService;
        $this->loyaltyCustomerRepository = $loyaltyCustomerRepository;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'state_enter.order_transaction.state.paid' => 'onOrderTransactionStateChangePaid',
            'state_enter.order.state.cancelled' => 'onOrderTransactionStateChangeCancelled',
        ];
    }

    /**
     * Remove points from pending, if transaction failed/cancelled
     * @param OrderStateMachineStateChangeEvent $event
     * @return void
     */
    public function onOrderTransactionStateChangeCancelled(OrderStateMachineStateChangeEvent $event): void
    {
        // order
        $order = $event->getOrder();

        // customer
        $orderCustomer = $order->getOrderCustomer()->getCustomer();

        // saleschannel
        $salesChannelId = $order->getSalesChannelId();

        // get calculation type
        $calculationType = $this->systemConfigService->get('LoyaltyProgram.config.pointsCalculationType', $salesChannelId);

        // return if customer is guest
        if ( $orderCustomer->getGuest() ) {
            return;
        }

        $orderCustomerId = $orderCustomer->getId();

        // loyalty customer
        $loyaltyCustomer = $this->getLoyaltyCustomer($this->loyaltyCustomerRepository, Context::createDefaultContext(), $orderCustomerId);

        // pending points
        $customerPointsPending = $loyaltyCustomer->getPointsPending();

        // points get
        if ( $calculationType === 'price' ) {
            $pointsMultiplier = $this->systemConfigService->get('LoyaltyProgram.config.pointsCalculationPriceMultiplier', $salesChannelId);
            // calculate points by price
            $orderPoints = $this->getPointsByLineItemPrice($order, $event->getContext(), $pointsMultiplier);
        } else {
            $orderPoints = $this->getPointsByLineItemPoints($order, $event->getContext());
        }

        // calculate points
        $calculatePendingPoints = $customerPointsPending - $orderPoints;

        // update pending points
        $this->loyaltyCustomerRepository->update(
            [
                [
                    'id' => $loyaltyCustomer->getId(),
                    'pointsPending' => (int)$calculatePendingPoints,
                ]
            ],
            Context::createDefaultContext()
        );
    }

    /**
     * Remove points from pending, add to points when transactions state paid
     * @param OrderStateMachineStateChangeEvent $event
     * @return void
     */
    public function onOrderTransactionStateChangePaid(OrderStateMachineStateChangeEvent $event): void
    {
        // order
        $order = $event->getOrder();

        // customer
        $orderCustomer = $order->getOrderCustomer()->getCustomer();

        // saleschannel
        $salesChannelId = $order->getSalesChannelId();

        // get calculation type
        $calculationType = $this->systemConfigService->get('LoyaltyProgram.config.pointsCalculationType', $salesChannelId);

        // return if customer is guest
        if ( $orderCustomer->getGuest() ) {
            return;
        }

        $orderCustomerId = $orderCustomer->getId();


        // loyalty customer
        $loyaltyCustomer = $this->getLoyaltyCustomer($this->loyaltyCustomerRepository, Context::createDefaultContext(), $orderCustomerId);

        // Customer points
        $customerPoints = $loyaltyCustomer->getPoints();
        $customerPointsTotal = $loyaltyCustomer->getPointsTotal();
        $customerPointsPending = $loyaltyCustomer->getPointsPending();

        // points get
        if ( $calculationType === 'price' ) {
            $pointsMultiplier = $this->systemConfigService->get('LoyaltyProgram.config.pointsCalculationPriceMultiplier', $salesChannelId);
            // calculate points by price
            $orderPoints = $this->getPointsByLineItemPrice($order, $event->getContext(), $pointsMultiplier);
        } else {
            $orderPoints = $this->getPointsByLineItemPoints($order, $event->getContext());
        }

        // calculate
        $calculatePendingPoints = $customerPointsPending - $orderPoints;
        $calculateCustomerPoints = $customerPoints + $orderPoints;
        $calculateTotalPoints = $customerPointsTotal + $orderPoints;

        // add points to customer, remove from pending
        $this->loyaltyCustomerRepository->update(
            [
                [
                    'id' => $loyaltyCustomer->getId(),
                    'pointsPending' => (int)$calculatePendingPoints,
                ],
                [
                    'id' => $loyaltyCustomer->getId(),
                    'points' => (int)$calculateCustomerPoints,
                ],
                [
                    'id' => $loyaltyCustomer->getId(),
                    'pointsTotal' => (int)$calculateTotalPoints,
                ]
            ],
            Context::createDefaultContext()
        );
    }

}