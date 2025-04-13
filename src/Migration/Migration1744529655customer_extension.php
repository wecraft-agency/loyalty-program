<?php declare(strict_types=1);

namespace LoyaltyProgram\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1744529655customer_extension extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1744529655;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `loyalty_customer` (
    `id` BINARY(16) NOT NULL,
    `customer_id` BINARY(16) NULL,
    `points` INT(11) NULL,
    `points_total` INT(11) NULL,
    `points_pending` INT(11) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
                CONSTRAINT `uniq.loyalty_customer.customer_id` UNIQUE (`customer_id`),
                CONSTRAINT `fk.loyalty_customer.customer_id` FOREIGN KEY (`customer_id`)
                    REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }
}
