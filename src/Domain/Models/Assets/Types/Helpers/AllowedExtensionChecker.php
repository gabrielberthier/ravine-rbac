<?php

namespace RavineRbac\Domain\Models\Assets\Types\Helpers;

use RavineRbac\Domain\Dto\Asset\Command\CreateAsset;
use RavineRbac\Domain\Models\Assets\Types\Interfaces\ConstrainedAssetFactoryInterface;

class AllowedExtensionChecker
{
  public function isAllowed(CreateAsset $command, ConstrainedAssetFactoryInterface $factory): bool
  {
    $converter = new ExtensionConverter();
    return in_array(
      strtoupper($command->extension()),
      $converter->formatsToUpper($factory)
    );
  }
}