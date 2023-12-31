<?php

namespace RavineRbac\Infra\Persistence\PersistenceUtils;

use RavineRbac\Domain\Repositories\PersistenceOperations\Responses\ResultSetInterface;
use RavineRbac\Domain\Repositories\PersistenceOperations\Responses\SearchResult;
use RavineRbac\infra\Persistence\Doctrine\Pagination\PaginationService;
use Doctrine\ORM\EntityManagerInterface as EntityManager;

class ItemsRetriever
{
    public function __construct(private EntityManager $em)
    {
    }

    public function findAll(string $className, bool $paginate = false, $page = 1, $limit = 20): ResultSetInterface
    {
        if ($paginate && $page && $limit) {
            $query = $this->em
                ->createQueryBuilder()
                ->select('m')
                ->from($className, 'm');

            $pagination = new PaginationService($query);

            return $pagination->paginate($page, $limit);
        }

        return new SearchResult($this->em->getRepository($className)->findAll());
    }
}