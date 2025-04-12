<?php declare(strict_types=1);

namespace LoyaltyProgram\Content\Reward\Aggregate;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                         add(SurchargeDiscountTranslationEntity $entity)
 * @method void                         set(string $key, SurchargeDiscountTranslationEntity $entity)
 * @method SurchargeDiscountTranslationEntity[]    getIterator()
 * @method SurchargeDiscountTranslationEntity[]    getElements()
 * @method SurchargeDiscountTranslationEntity|null get(string $key)
 * @method SurchargeDiscountTranslationEntity|null first()
 * @method SurchargeDiscountTranslationEntity|null last()
 */
class RewardTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return RewardTranslationDefintition::class;
    }
}
