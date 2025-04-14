<?php declare(strict_types=1);

namespace LoyaltyProgram\Content\Redemption;

use LoyaltyProgram\Content\Reward\Aggregate\RewardTranslationDefintition;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\VersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;

class RedemptionDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'loyalty_redemption';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('customer_id', 'customerId', CustomerDefinition::class))->addFlags(new Required()),
            (new VersionField())->addFlags(new ApiAware()),
            (new FkField('order_id', 'orderId', OrderDefinition::class))->addFlags(new Required()),
            (new StringField('type','type'))->addFlags(new Required(), new ApiAware()),
            (new StringField('status','status'))->addFlags(new Required(), new ApiAware()),
            new IntField('points', 'points'),
            (new ReferenceVersionField(OrderDefinition::class))->addFlags(new Required()),


            new OneToOneAssociationField('order', 'order_id', 'id', OrderDefinition::class, false),
            (new ManyToOneAssociationField('customer', 'customer_id', CustomerDefinition::class, 'id', true))->addFlags(new ApiAware())
        ]);
    }

    public function getEntityClass(): string
    {
        return RedemptionEntity::class;
    }
    public function getCollectionClass(): string
    {
        return RedemptionCollection::class;
    }
}