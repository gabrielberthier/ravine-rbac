<?php

declare(strict_types=1);

namespace Core\Data\BehaviourComponents;

use Core\Data\Factories\CycleDatabaseManagerFactory;

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