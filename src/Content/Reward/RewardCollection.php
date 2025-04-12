<?php declare(strict_types=1);

namespace LoyaltyProgram\Content\Reward;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(RewardEntity $entity)
 * @method void               set(string $key, RewardEntity $entity)
 * @method RewardEntity[]    getIterator()
 * @method RewardEntity[]    getElements()
 * @method RewardEntity|null get(string $key)
 * @method RewardEntity|null first()
 * @method RewardEntity|null last()
 */
class RewardCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return RewardEntity::class;
    }
}