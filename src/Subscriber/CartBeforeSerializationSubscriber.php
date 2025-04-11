<?php declare(strict_types=1);
namespace LoyaltyProgram\Subscriber;

use Shopware\Core\Checkout\Cart\Event\CartBeforeSerializationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CartBeforeSerializationSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            CartBeforeSerializationEvent::class => 'onCartBeforeSerialization',
        ];
    }

    public function onCartBeforeSerialization(CartBeforeSerializationEvent $event): void
    {
        $allowed = $event->getCustomFieldAllowList();
        $allowed[] = 'loyalty_product_points';

        $event->setCustomFieldAllowList($allowed);
    }
}