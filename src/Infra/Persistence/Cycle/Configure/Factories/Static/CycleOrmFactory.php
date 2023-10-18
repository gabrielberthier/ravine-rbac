<?php

declare(strict_types=1);

namespace RavineRbac\Infra\Persistence\Cycle\Configure\Factories\Static;

use Cycle\ORM;
use Cycle\Database\DatabaseManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Spiral\Tokenizer\ClassLocator;
use Cycle\Schema;
use Cycle\Annotated;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;

class CycleOrmFactory
{
    public static function createOrm(
        DatabaseManager $databaseManager,
        ContainerInterface $containerInterface
    ) {
        $root = dirname(__DIR__, 3);

        $finder = (new Finder())
            ->files()
            ->in([
                $root . "/Entities",
                $root . "/Entities",
            ]);
        $classLocator = new ClassLocator($finder);
        $database = $databaseManager;
        $schemaCompiler = new Schema\Compiler();

        $schema = $schemaCompiler->compile(new Schema\Registry($database), [
            new Schema\Generator\ResetTables(),
            // re-declared table schemas (remove columns)
            new Annotated\Embeddings($classLocator),
            // register embeddable entities
            new Annotated\Entities($classLocator),
            // register annotated entities
            new Annotated\TableInheritance(),
            // register STI/JTI
            new Annotated\MergeColumns(),
            // add @Table column declarations
            new Schema\Generator\GenerateRelations(),
            // generate entity relations
            new Schema\Generator\GenerateModifiers(),
            // generate changes from schema modifiers
            new Schema\Generator\ValidateEntities(),
            // make sure all entity schemas are correct
            new Schema\Generator\RenderTables(),
            // declare table schemas
            new Schema\Generator\RenderRelations(),
            // declare relation keys and indexes
            new Schema\Generator\RenderModifiers(),
            // render all schema modifiers
            new Annotated\MergeIndexes(),
            // add @Table column declarations
            new Schema\Generator\SyncTables(),
            // sync table changes to database
            new Schema\Generator\GenerateTypecast(),
            // typecast non string columns
        ]);
        $schema = new ORM\Schema($schema);

        $commandGenerator = new EventDrivenCommandGenerator(
            $schema,
            $containerInterface
        );

        $ormFactory = new ORM\Factory($database);
        $ormFactory = $ormFactory->withCollectionFactory(
            'doctrine',
            // Alias
            new ORM\Collection\DoctrineCollectionFactory,
            \Doctrine\Common\Collections\Collection::class // <= Base collection
        );

        $orm = new ORM\ORM(
            $ormFactory,
            $schema,
            $commandGenerator
        );

        return $orm;
    }
}