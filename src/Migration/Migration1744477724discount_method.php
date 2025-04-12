<?php declare(strict_types=1);

namespace LoyaltyProgram\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1744477724discount_method extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1744477724;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
            ALTER TABLE `loyalty_reward`
                ADD COLUMN `discount_method` VARCHAR(255);
SQL;

        $connection->executeStatement($query);
    }
}
