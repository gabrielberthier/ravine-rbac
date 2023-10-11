<?php

namespace RavineRbac\Infra\Persistence\RepositoryTraits;

use RavineRbac\Infrastructure\Uuid\UuidEncoder;

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
