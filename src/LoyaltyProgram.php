<?php declare(strict_types=1);

namespace LoyaltyProgram;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;

use LoyaltyProgram\Service\CustomFields\LoyaltyCustomerFields;
use LoyaltyProgram\Service\CustomFields\LoyaltyProductFields;
use LoyaltyProgram\Service\CustomFields\LoyaltyOrderFields;

class LoyaltyProgram extends Plugin
{
    /**
     * Install
     * @param InstallContext $installContext
     * @return void
     */
    public function install(InstallContext $installContext): void
    {
        // Install fields to product
        (
            new LoyaltyProductFields(
                $this->container->get('custom_field_set.repository')
            )
        )->installCustomFields($installContext->getContext());

        // Install fields to customer
        (
        new LoyaltyCustomerFields(
            $this->container->get('custom_field_set.repository')
        )
        )->installCustomFields($installContext->getContext());

        // Install fields to order
        (
        new LoyaltyOrderFields(
            $this->container->get('custom_field_set.repository')
        )
        )->installCustomFields($installContext->getContext());
    }

    /**
     * Uninstall
     * @param UninstallContext $uninstallContext
     * @return void
     */
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        // uninstall product fields
        (
            new LoyaltyProductFields(
                $this->container->get('custom_field_set.repository')
            )
        )->removeCustomFields(
            $uninstallContext->getContext()
        );

        // uninstall customer fields
        (
            new LoyaltyCustomerFields(
                $this->container->get('custom_field_set.repository')
            )
        )->removeCustomFields(
            $uninstallContext->getContext()
        );

        // uninstall order fields
        (
        new LoyaltyOrderFields(
            $this->container->get('custom_field_set.repository')
        )
        )->removeCustomFields(
            $uninstallContext->getContext()
        );
    }

    /**
     * Activate
     * @param ActivateContext $activateContext
     * @return void
     */
    public function activate(ActivateContext $activateContext): void
    {
        // activate customer fields
        (
            new LoyaltyCustomerFields(
                $this->container->get('custom_field_set.repository')
            )
        )->activateCustomFields(
            $activateContext->getContext()
        );


        // activate product fields
        (
            new LoyaltyProductFields(
                $this->container->get('custom_field_set.repository')
            )
        )->activateCustomFields(
            $activateContext->getContext()
        );


        // activate order fields
        (
        new LoyaltyOrderFields(
            $this->container->get('custom_field_set.repository')
        )
        )->activateCustomFields(
            $activateContext->getContext()
        );
    }

    /**
     * Deactivate
     * @param DeactivateContext $deactivateContext
     * @return void
     */
    public function deactivate(DeactivateContext $deactivateContext): void
    {
        // deactivate customer fields
        (
            new LoyaltyCustomerFields(
                $this->container->get('custom_field_set.repository')
            )
        )->deactivateCustomFields(
            $deactivateContext->getContext()
        );

        // deactivate product fields
        (
            new LoyaltyProductFields(
                $this->container->get('custom_field_set.repository')
            )
        )->deactivateCustomFields(
                $deactivateContext->getContext()
        );

        // deactivate order fields
        (
        new LoyaltyOrderFields(
            $this->container->get('custom_field_set.repository')
        )
        )->deactivateCustomFields(
                $deactivateContext->getContext()
        );
    }

    /**
     * Update
     * @param UpdateContext $updateContext
     * @return void
     */
    public function update(UpdateContext $updateContext): void
    {
        // Update necessary stuff, mostly non-database related
    }

    /**
     * Post install
     * @param InstallContext $installContext
     * @return void
     */
    public function postInstall(InstallContext $installContext): void
    {
    }

    /**
     * Post update
     * @param UpdateContext $updateContext
     * @return void
     */
    public function postUpdate(UpdateContext $updateContext): void
    {
    }
}
