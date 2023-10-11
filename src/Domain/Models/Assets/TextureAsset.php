<?php

namespace RavineRbac\Domain\Models\Assets;

use RavineRbac\Domain\Models\Assets\AbstractAsset;

class TextureAsset extends AbstractAsset
{
  public function __construct()
  {
    parent::__construct('texture');
  }
}
