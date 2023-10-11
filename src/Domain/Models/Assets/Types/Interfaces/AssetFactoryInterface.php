<?php

namespace RavineRbac\Domain\Models\Assets\Types\Interfaces;

use RavineRbac\Domain\Dto\Asset\Command\CreateAsset;
use RavineRbac\Domain\Models\Assets\AbstractAsset;

interface AssetFactoryInterface
{
  public function create(CreateAsset $command): AbstractAsset;
}