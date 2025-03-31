<?php

namespace digitalejungle\crafteasygallery\variables;

use digitalejungle\crafteasygallery\Gallery;
use digitalejungle\crafteasygallery\models\GalleryData;

/**
 * Access these methods via craft.easyGallery.* in Twig.
 */
class GalleryVariable
{
    /**
     * Returns a GalleryData object for a given folder ID.
     */
    public function getGallery(int|string $folderId): ?GalleryData
    {
        return Gallery::getInstance()->galleryService->getGallery($folderId);
    }

    /**
     * Returns child folders for a given folder ID, each as a GalleryData object.
     */
    public function getFolders(int|string $folderId): array
    {
        return Gallery::getInstance()->galleryService->getFolders($folderId);
    }

    /**
     * Returns assets for a given folder ID, optionally filtered by kind(s).
     */
    public function getAssets(int|string $folderId, object|array|null $filters = null): array
    {
        return Gallery::getInstance()->galleryService->getAssets($folderId, $filters);
    }

    /**
     * Returns all objects (folders + assets) in a folder.
     */
    public function getObjects(int|string $folderId, object|array|null $filters = null): array
    {
        return Gallery::getInstance()->galleryService->getObjects($folderId, $filters);
    }
}
