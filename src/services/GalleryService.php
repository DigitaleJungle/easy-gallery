<?php

namespace digitalejungle\crafteasygallery\services;

use Craft;
use craft\base\Component;
use craft\elements\Asset;
use craft\models\VolumeFolder;
use digitalejungle\crafteasygallery\models\GalleryData;

class GalleryService extends Component
{
    public function getGallery(int $folderId): ?int
    {
        return $folderId;
    }
}
