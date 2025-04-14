<?php declare(strict_types=1);

namespace LoyaltyProgram\Content;

use LoyaltyProgram\Content\Customer\LoyaltyCustomerDefinition;
use LoyaltyProgram\Content\Redemption\RedemptionDefinition;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CustomerExtension extends EntityExtension {
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField('loyaltyCustomer', 'id', 'customer_id', LoyaltyCustomerDefinition::class, true)
        );
        $collection->add(
            new OneToManyAssociationField('loyaltyRedemptions', RedemptionDefinition::class,'customer_id', 'id')
        );
    }

    public function getDefinitionClass(): string
    {
        return CustomerDefinition::class;
    }
}