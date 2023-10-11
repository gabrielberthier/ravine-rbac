<?php

namespace RavineRbac\Infra\Persistence\Abstraction;

use RavineRbac\Domain\Contracts\ModelInterface;
use RavineRbac\Infrastructure\Persistence\TargetRepositories\DoctrineTargetRepository;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use RavineRbac\Infrastructure\Persistence\Contracts\RepositoryInterface;

/**
 * @template T
 * 
 * @extends AbstractRepository<T>
 */
abstract class DoctrineAbstractCrud extends AbstractRepository
{
    public function __construct(protected EntityManager $em)
    {
    }

    public function repository(): RepositoryInterface
    {
        $repository = $this->em->getRepository($this->entity());

        return new DoctrineTargetRepository($repository);
    }

    /**
     * @param T|int $subject
     * 
     * @return ?T
     */
    public function delete(ModelInterface|int $subject): ?object
    {
        if (is_int($subject)) {
            $subject = $this->findByID($subject);
        }

        if ($subject !== null) {
            $this->em->remove($subject);
            $this->em->flush();
        }

        return $subject;
    }

    /**
     * @param T $model
     */
    public function insert(object $model): void
    {
        $this->em->persist($model);
        $this->em->flush();
    }
}
