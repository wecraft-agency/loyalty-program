<?php declare(strict_types=1);

namespace LoyaltyProgram\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1744556451discount_fields_to_rewards extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1744556451;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
            ALTER TABLE `loyalty_reward`
                ADD COLUMN `discount_percentage` INT(11) NULL,
                ADD COLUMN `discount_fixed` INT(11) NULL;
SQL;

        $connection->executeStatement($query);
    }
}
