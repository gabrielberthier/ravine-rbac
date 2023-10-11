<?php

namespace RavineRbac\Domain\Models\Assets\Types\Factories;

use RavineRbac\Domain\Dto\Asset\Command\CreateAsset;
use RavineRbac\Domain\Models\Assets\AbstractAsset;
use RavineRbac\Domain\Models\Assets\ThreeDimensionalAsset;
use RavineRbac\Domain\Models\Assets\Types\Interfaces\ConstrainedAssetFactoryInterface;


class ThreeDimensionalAssetFactory implements ConstrainedAssetFactoryInterface
{
    public function create(CreateAsset $command): AbstractAsset
    {
        $asset = new ThreeDimensionalAsset();
        return $asset->fromCommand($command);
    }

    public function allowedFormats(): array|string
    {
        return [
            'obj',
            'fbx',
            'glb',
            'gltf'
        ];
    }
}