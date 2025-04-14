<?php declare(strict_types=1);

namespace LoyaltyProgram\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1744625974redemptions extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1744625974;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `loyalty_redemption` (
    `id` BINARY(16) NOT NULL,
    `points` INT(8) NOT NULL DEFAULT 1,
    `version_id` BINARY(16) NOT NULL,
    `order_id` BINARY(16) NOT NULL,
    `order_version_id` BINARY(16) NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `status` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    PRIMARY KEY (`id`, `version_id`),
            CONSTRAINT `fk.loyalty_redemption.order_id` 
                FOREIGN KEY (`order_id`, `order_version_id`) 
                REFERENCES `order` (`id`, `version_id`) 
                ON DELETE CASCADE ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);

        $sql = <<<SQL
        ALTER TABLE `loyalty_redemption`
        ADD COLUMN `customer_id` BINARY(16) NULL,
    ADD KEY `fk.loyalty_redemption.customer_id` (`customer_id`),
            ADD CONSTRAINT `fk.loyalty_redemption.customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        
SQL;
        $connection->executeStatement($sql);

    }
}
