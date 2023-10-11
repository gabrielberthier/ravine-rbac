<?php

namespace RavineRbac\Domain\Models\Assets\Types\Factories;

use RavineRbac\Domain\Dto\Asset\Command\CreateAsset;
use RavineRbac\Domain\Models\Assets\AbstractAsset;
use RavineRbac\Domain\Models\Assets\TextureAsset;
use RavineRbac\Domain\Models\Assets\Types\Interfaces\ConstrainedAssetFactoryInterface;



class TextureAssetFactory implements ConstrainedAssetFactoryInterface
{
  public function create(CreateAsset $command): AbstractAsset
  {
    $asset = new TextureAsset();
    return $asset->fromCommand($command);
  }

  public function allowedFormats(): array|string
  {
    return [
      "BMP",
      "JPG",
      "PNG",
      'JPEG',
    ];
  }
}