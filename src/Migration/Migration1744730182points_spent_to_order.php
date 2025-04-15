<?php declare(strict_types=1);

namespace LoyaltyProgram\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1744730182points_spent_to_order extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1744730182;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
            ALTER TABLE `loyalty_order`
                ADD COLUMN `points_spent` INT(11) NULL;
SQL;

        $connection->executeStatement($query);
    }
}
