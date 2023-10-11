<?php

namespace RavineRbac\Domain\Models\Assets;

use RavineRbac\Domain\Models\Assets\AbstractAsset;

class PictureAsset extends AbstractAsset
{
  public function __construct()
  {
    parent::__construct('picture');
  }
}
