<?php

namespace RavineRbac\Domain\Models\Assets\Types\Exceptions;

use RavineRbac\Domain\Exceptions\Protocols\DomainException as ProtocolsDomainException;


class NotAllowedAssetType extends ProtocolsDomainException
{
  public function __construct()
  {
    $this->message = "Asset type not found or supported";
  }
}
