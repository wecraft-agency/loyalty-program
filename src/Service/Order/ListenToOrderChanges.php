<?php declare(strict_types=1);

namespace LoyaltyProgram\Service\Order;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Checkout\Order\Event\OrderStateMachineStateChangeEvent;
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
     * @param RequestStack $requestStack
     * @param EntityRepository $orderRepository
     * @param EntityRepository $customerRepository
     */
    public function __construct(RequestStack $requestStack, EntityRepository $orderRepository, EntityRepository $customerRepository)
    {
        $this->requestStack = $requestStack;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
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

        // return if customer is guest
        if ( $orderCustomer->getGuest() ) {
            return;
        }

        $orderCustomerId = $orderCustomer->getId();
        $customerPoints = $this->getPointsByCustomer($orderCustomer, $event->getContext());
        $customerPointsPending = $this->getPointsPendingByCustomer($orderCustomer, $event->getContext());

        // points get
        $orderPoints = $this->getPointsByLineItemPoints($order, $event->getContext());

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

        // return if customer is guest
        if ( $orderCustomer->getGuest() ) {
            return;
        }

        $orderCustomerId = $orderCustomer->getId();
        $customerPoints = $this->getPointsByCustomer($orderCustomer, $event->getContext());
        $customerPointsPending = $this->getPointsPendingByCustomer($orderCustomer, $event->getContext());

        // points get
        $orderPoints = $this->getPointsByLineItemPoints($order, $event->getContext());

        // calculate
        $calculatePendingPoints = $customerPointsPending - $orderPoints;
        $calculateCustomerPoints = $customerPoints + $orderPoints;

        // add points to customer, remove from pending
        $this->setPoints($this->customerRepository, $event->getContext(), $calculateCustomerPoints, $orderCustomerId, 'customer');
        $this->setPoints($this->customerRepository, $event->getContext(), $calculatePendingPoints, $orderCustomerId, 'customer', 'points_pending');
    }

}