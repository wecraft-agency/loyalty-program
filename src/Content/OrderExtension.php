<?php declare(strict_types=1);

namespace LoyaltyProgram\Content;

use LoyaltyProgram\Content\Order\LoyaltyOrderDefinition;
use LoyaltyProgram\Content\Redemption\RedemptionDefinition;

use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class OrderExtension extends EntityExtension {
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToOneAssociationField('loyaltyOrder', 'id', 'order_id', LoyaltyOrderDefinition::class, true))->addFlags(new ApiAware(), new CascadeDelete())
        );
        $collection->add(
            (new OneToOneAssociationField('loyaltyRedemption', 'id', 'order_id', RedemptionDefinition::class, true))->addFlags(new ApiAware(), new CascadeDelete())
        );
    }

    public function getDefinitionClass(): string
    {
        return OrderDefinition::class;
    }
}