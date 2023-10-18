<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\BehaviourComponents;

use RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Static\CycleDatabaseManagerFactory;

class DatabaseCleaner
{
    public static function truncate(): void
    {
        DatabaseCleaner::truncateCycleDatabase();
    }

    public static function truncateCycleDatabase(): void
    {
        $dbal = CycleDatabaseManagerFactory::createDatabase();
        $db = $dbal->database('default');

        // delete all FKs first
        foreach ($db->getTables() as $table) {
            $schema = $table->getSchema();
            foreach ($schema->getForeignKeys() as $foreign) {
                $schema->dropForeignKey($foreign->getColumns());
            }

            $schema->save(\Cycle\Database\Driver\HandlerInterface::DROP_FOREIGN_KEYS);
        }

        // delete tables
        foreach ($db->getTables() as $table) {
            $schema = $table->getSchema();
            $schema->declareDropped();
            $schema->save();
        }
    }
}