<?php declare(strict_types=1);

namespace LoyaltyProgram\Content\Reward\Aggregate;

use LoyaltyProgram\Content\Reward\RewardEntity;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Shopware\Core\System\Language\LanguageEntity;

class RewardTranslationEntity extends TranslationEntity
{
    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var strong|null
     */
    protected $description;

    /**
     * @var string
     */
    protected $loyaltyRewardId;

    protected RewardEntity $reward;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getLoyaltyRewardId(): string
    {
        return $this->loyaltyRewardId;
    }

    /**
     * @param string $loyaltyRewardId
     */
    public function setLoyaltyRewardId(string $loyaltyRewardId): void
    {
        $this->loyaltyRewardId = $loyaltyRewardId;
    }

    public function getReward(): RewardEntity
    {
        return $this->reward;
    }

    public function setReward(RewardEntity $reward): void
    {
        $this->reward = $reward;
    }

    /**
     * @return string
     */
    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    /**
     * @param string $languageId
     */
    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }

    /**
     * @return LanguageEntity|null
     */
    public function getLanguage(): ?LanguageEntity
    {
        return $this->language;
    }

    /**
     * @param LanguageEntity|null $language
     */
    public function setLanguage(?LanguageEntity $language): void
    {
        $this->language = $language;
    }
}