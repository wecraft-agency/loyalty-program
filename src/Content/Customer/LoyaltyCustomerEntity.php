<?php declare(strict_types=1);

namespace LoyaltyProgram\Content\Customer;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use SHopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class LoyaltyCustomerEntity extends Entity
{
    use EntityIdTrait;

    protected string $customerId;

    protected ?int $points = null;

    protected ?int $pointsTotal = null;

    protected ?int $pointsPending = null;

    protected ?CustomerEntity $customer = null;

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getCustomer(): CustomerEntity
    {
        return $this->customer;
    }

    public function setCustomer(CustomerEntity $customer): void
    {
        $this->customer = $customer;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): void
    {
        $this->points = $points;
    }

    public function getPointsTotal(): ?int
    {
        return $this->pointsTotal;
    }

    public function setPointsTotal(?int $pointsTotal): void
    {
        $this->pointsTotal = $pointsTotal;
    }

    public function getPointsPending(): ?int
    {
        return $this->pointsPending;
    }

    public function setPointsPending(?int $pointsPending): void
    {
        $this->pointsPending = $pointsPending;
    }
}