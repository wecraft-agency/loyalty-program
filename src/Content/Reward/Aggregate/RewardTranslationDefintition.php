<?php declare(strict_types=1);

namespace LoyaltyProgram\Content\Reward\Aggregate;

use LoyaltyProgram\Content\Reward\RewardDefinition;

use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Field;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class RewardTranslationDefintition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'loyalty_reward_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return RewardTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return RewardTranslationEntity::class;
    }

    public function getParentDefinitionClass(): string
    {
        return RewardDefinition::class;
    }


    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new ApiAware()),
            (new LongTextField('description', 'description'))->addFlags(new AllowHtml(), new ApiAware()),
        ]);
    }
}