<?php declare(strict_types=1);

namespace LoyaltyProgram\Content;

use LoyaltyProgram\Content\Customer\LoyaltyCustomerDefinition;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CustomerExtension extends EntityExtension {
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField('loyaltyCustomer', 'id', 'customer_id', LoyaltyCustomerDefinition::class, true)
        );
    }

    public function getDefinitionClass(): string
    {
        return CustomerDefinition::class;
    }
}