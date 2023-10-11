<?php

namespace RavineRbac\Infra\Persistence\RepositoryTraits;

use RavineRbac\infra\Uuid\UuidEncoder;

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
