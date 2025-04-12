<?php declare(strict_types=1);

namespace LoyaltyProgram\Service\Order;

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
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @param RequestStack $requestStack
     * @param EntityRepository $orderRepository
     * @param EntityRepository $customerRepository
     */
    public function __construct(RequestStack $requestStack, EntityRepository $orderRepository, EntityRepository $customerRepository, SystemConfigService $systemConfigService)
    {
        $this->requestStack = $requestStack;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'state_enter.order_transaction.state.paid' => 'onOrderTransactionStateChangePaid',
            'state_enter.order_transaction.state.failed' => 'onOrderTransactionStateChangeCancelled',
            'state_enter.order_transaction.state.cancelled' => 'onOrderTransactionStateChangeCancelled',
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
        $customerPointsPending = $this->getPointsPendingByCustomer($orderCustomer, $event->getContext());

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

        // remove from panding
        $this->setPoints($this->customerRepository, $event->getContext(), $calculatePendingPoints, $orderCustomerId, 'customer', 'points_pending');
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
        $customerPoints = $this->getPointsByCustomer($orderCustomer, $event->getContext());
        $customerPointsPending = $this->getPointsPendingByCustomer($orderCustomer, $event->getContext());

        // points get
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

        // add points to customer, remove from pending
        $this->setPoints($this->customerRepository, $event->getContext(), $calculateCustomerPoints, $orderCustomerId, 'customer');
        $this->setPoints($this->customerRepository, $event->getContext(), $calculatePendingPoints, $orderCustomerId, 'customer', 'points_pending');
    }

}