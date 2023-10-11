<?php

namespace RavineRbac\Domain\Models\Assets\Types\Interfaces;

interface ConstrainedAssetFactoryInterface extends AssetFactoryInterface
{
  public function allowedFormats(): array|string;
}
