<?php

namespace RavineRbac\Domain\Repositories\Pagination;

use RavineRbac\Domain\Repositories\PersistenceOperations\Responses\PaginationResponse;

interface PaginationInterface
{
    public function paginate(
        int $page = 1,
        int $limit = 20
    ): PaginationResponse;
}
