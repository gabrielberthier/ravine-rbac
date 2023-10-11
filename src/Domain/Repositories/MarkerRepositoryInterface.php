<?php

declare(strict_types=1);

namespace RavineRbac\Domain\Repositories;

use RavineRbac\Domain\Contracts\ModelInterface;
use RavineRbac\Domain\Models\Marker\Marker;
use RavineRbac\Domain\Models\Museum;
use RavineRbac\Domain\Repositories\PersistenceOperations\Responses\ResultSetInterface;

interface MarkerRepositoryInterface
{
    public function add(Marker $model): bool;

    /**
     * @var Marker[]
     *
     * @param mixed $page
     * @param mixed $limit
     */
    public function findAllByMuseum(int|Museum $museum, bool $paginate = false, $page = 1, $limit = 20): ResultSetInterface;

    public function findByID(int $id): ?Marker;

    public function update(int $id, array $values): ?Marker;

    public function delete(ModelInterface|int $subject): ?Marker;

    public function all(bool $paginate = false, int $page = 1, int $limit = 20): ResultSetInterface;
}