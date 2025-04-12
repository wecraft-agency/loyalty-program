<?php declare(strict_types=1);

namespace LoyaltyProgram\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;

/**
 * @internal
 */
class Migration1744471343media extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1744471343;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
            ALTER TABLE `loyalty_reward`
            ADD COLUMN `media_id` BINARY(16) NULL,
            ADD KEY `fk.loyalty_reward.media_id` (`media_id`),
            ADD CONSTRAINT `fk.loyalty_reward.media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SQL;

        $connection->executeStatement($query);

        $this->updateInheritance($connection, 'media', 'loyaltyRewards');
    }
}
