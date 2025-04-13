<?php declare(strict_types=1);

namespace LoyaltyProgram\Content\Customer;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class LoyaltyCustomerDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'loyalty_customer';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return LoyaltyCustomerCollection::class;
    }

    public function getEntityClass(): string
    {
        return LoyaltyCustomerEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('customer_id', 'customerId', CustomerDefinition::class))->addFlags(new Required()),
            new IntField('points', 'points'),
            new IntField('points_total', 'pointsTotal'),
            new IntField('points_pending', 'pointsPending'),
            new OneToOneAssociationField('customer', 'customer_id', 'id', CustomerDefinition::class, false)
        ]);
    }

}