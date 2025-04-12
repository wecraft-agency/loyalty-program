<?php declare(strict_types=1);

namespace LoyaltyProgram\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1744473924fields extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1744473924;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
            ALTER TABLE `loyalty_reward`
                ADD COLUMN `points` INT(8) NOT NULL DEFAULT 1,
                ADD COLUMN `type` VARCHAR(255) NOT NULL;
SQL;

        $connection->executeStatement($query);
    }
}
