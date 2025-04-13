<?php declare(strict_types=1);

namespace LoyaltyProgram\Content\Order;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use SHopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class LoyaltyOrderEntity extends Entity
{
    use EntityIdTrait;

    protected string $orderId;

    protected ?int $points = null;

    protected ?OrderEntity $order = null;

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getOrder(): OrderEntity
    {
        return $this->order;
    }

    public function setOrder(OrderEntity $order): void
    {
        $this->order = $order;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): void
    {
        $this->points = $points;
    }
}