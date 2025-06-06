<?php

namespace LoyaltyProgram\Service\CustomFields;

use Shopware\Core\Framework\Context;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

class LoyaltyProductFields
{
    public const FIELD_KEY = 'loyalty_product';

    /**
     * @var EntityRepository
     */
    protected EntityRepository $customFieldRepository;

    /**
     * @param EntityRepository $customFieldRepository
     */
    public function __construct(EntityRepository $customFieldRepository)
    {
        $this->customFieldRepository = $customFieldRepository;
    }


    /**
     * Get custom fields by field key
     * @param Context $context
     * @return IdSearchResult
     */
    private function getCustomFields(Context $context): IdSearchResult
    {
        return $this->customFieldRepository->searchIds(
            (
            new Criteria()
            )->addFilter(
                    new EqualsFilter('name', self::FIELD_KEY)
                ),
            $context
        );
    }

    /**
     * Set custom fields active
     * @param array $customFields
     * @param bool $active
     * @param Context $context
     * @return void
     */
    private function setCustomFieldsActive(array $customFields, bool $active, Context $context): void
    {
        $this->customFieldRepository->update(
            [
                [
                    'id' => $customFields[0],
                    'active' => $active
                ]
            ],
            $context
        );
    }

    /**
     * Remove custom fields
     * @param Context $context
     * @return void
     */
    public function removeCustomFields(Context $context): void
    {
        $customFields = $this->getCustomFields($context);

        if ( $customFields->getTotal() !== 0 ) {
            $this->customFieldRepository->delete(
                [
                    [
                        'id' => $customFields->getIds()[0]
                    ]
                ],
                $context
            );
        }
    }

    /**
     * Install custom fields
     * @param Context $context
     * @return void
     */
    public function installCustomFields(Context $context): void
    {
        $customFields = $this->getCustomFields($context);

        if ( $customFields->getTotal() === 0 ) {
            $customField = [
                'name' => self::FIELD_KEY,
                'active' => false,
                'config' => [
                    'label' => [
                        'en-GB' => 'Loyalty Program (Product)',
                        'de-DE' => 'Loyalty Programm (Produkt)'
                    ]
                ],
                'relations' => [
                    [
                        'entityName' => 'product'
                    ]
                ],
                'customFields' => [
                    [
                        'name' => self::FIELD_KEY . '_points',
                        'type' => CustomFieldTypes::NUMBER,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Points',
                                'de-DE' => 'Punkte'
                            ]
                        ]
                    ]
                ]
            ];
            $this->customFieldRepository->create([$customField], $context);
        }
    }

    /**
     * Activate custom fields
     * @param Context $context
     * @return void
     */
    public function activateCustomFields(Context $context): void
    {
        $this->setCustomFieldsActive(
            $this->getCustomFields($context)->getIds(),
            true,
            $context
        );
    }

    /**
     * Deactivate custom fields
     * @param Context $context
     * @return void
     */
    public function deactivateCustomFields(Context $context): void
    {
        $this->setCustomFieldsActive(
            $this->getCustomFields($context)->getIds(),
            false,
            $context
        );
    }
}