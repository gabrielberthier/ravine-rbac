<?php

namespace RavineRbac\Domain\Repositories;

use RavineRbac\Domain\Models\Museum;
use RavineRbac\Domain\Models\Security\SignatureToken;

interface SignatureTokenRetrieverInterface
{
    public function findFromMuseum(Museum $museum): ?SignatureToken;
}
