<?php

namespace App\Infra\Persistence\RepositoryTraits;

use App\Infrastructure\Uuid\UuidEncoder;

trait UuidFinderTrait
{
    public function findOneByEncodedUuid(string $encodedUuid)
    {
        $decodedUuid = UuidEncoder::decode($encodedUuid);

        return $this->findOneBy([
            'uuid' => $decodedUuid,
        ]);
    }
}
