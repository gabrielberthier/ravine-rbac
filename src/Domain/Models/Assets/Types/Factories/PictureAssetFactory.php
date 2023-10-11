<?php

namespace RavineRbac\Domain\Models\Assets\Types\Factories;

use RavineRbac\Domain\Dto\Asset\Command\CreateAsset;
use RavineRbac\Domain\Models\Assets\AbstractAsset;
use RavineRbac\Domain\Models\Assets\PictureAsset;
use RavineRbac\Domain\Models\Assets\Types\Interfaces\ConstrainedAssetFactoryInterface;



class PictureAssetFactory implements ConstrainedAssetFactoryInterface
{
  public function create(CreateAsset $command): AbstractAsset
  {
    $asset = new PictureAsset();
    return $asset->fromCommand($command);
  }

  public function allowedFormats(): array|string
  {
    return [
      "BMP",
      "TIF",
      "TGA",
      "JPG",
      "PNG",
      'JPEG',
    ];
  }
}