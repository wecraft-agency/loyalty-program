<?php declare(strict_types=1);

namespace LoyaltyProgram\Service\Order;

use Shopware\Core\Checkout\Order\OrderEvents;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ListenToOrderChanges implements EventSubscriberInterface
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
            OrderEvents::ORDER_WRITTEN_EVENT => 'onOrderWrittenEvent',
        ];
    }

    public function onOrderWrittenEvent(EntityWrittenEvent $event): void
    {
        if ( $event->getContext()->getVersionId() !== Defaults::LIVE_VERSION ) {
            return;
        }

        // Do stuff
        // @todo: remove points, when order is cancelled?
        // @todo: add points if order completed?
        // You can move logic from LoyaltyProgram/Subscriber/CheckoutOrderPlacedSubscriper to here, managed by order state change ( complete, sent, or something like that ).
    }

}