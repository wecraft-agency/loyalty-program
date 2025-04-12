<?php declare(strict_types=1);

namespace LoyaltyProgram\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1744469099translation_reward extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1744469099;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `loyalty_reward_translation` (
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `description` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    `loyalty_reward_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`loyalty_reward_id`,`language_id`),
    KEY `fk.loyalty_reward_translation.loyalty_reward_id` (`loyalty_reward_id`),
    KEY `fk.loyalty_reward_translation.language_id` (`language_id`),
    CONSTRAINT `fk.loyalty_reward_translation.loyalty_reward_id` FOREIGN KEY (`loyalty_reward_id`) REFERENCES `loyalty_reward` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.loyalty_reward_translation.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }
}
