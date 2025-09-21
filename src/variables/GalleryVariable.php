<?php

namespace digitalejungle\crafteasygallery\variables;

use digitalejungle\crafteasygallery\Gallery;
use digitalejungle\crafteasygallery\models\GalleryData;
use \craft\elements\db\AssetQuery;

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

    public function getAllFolders(int|string $folderId): array
    {
        return Gallery::getInstance()->galleryService->getAllFolders($folderId);
    }

    /**
     * Returns assets for a given folder ID, optionally filtered by kind(s).
     */
    public function getAssets(int|string $folderId, object|array|null $filters = null): array
    {
        return Gallery::getInstance()->galleryService->getAssets($folderId, $filters)->all();
    }

    public function getAssetQuery(int|string $folderId, object|array|null $filters = null): AssetQuery
    {
        return Gallery::getInstance()->galleryService->getAssets($folderId, $filters);
    }

    public function getAllAssets(int|string $folderId, object|array|null $filters = null): array
    {
        return Gallery::getInstance()->galleryService->getAllAssets($folderId, $filters)->all();
    }

    public function getAllAssetQuery(int|string $folderId, object|array|null $filters = null): AssetQuery
    {
        return Gallery::getInstance()->galleryService->getAllAssets($folderId, $filters);
    }


    /**
     * Returns all objects (folders + assets) in a folder.
     */
    public function getObjects(int|string $folderId, object|array|null $filters = null): array
    {
        return Gallery::getInstance()->galleryService->getObjects($folderId, $filters);
    }

    public function getAllObjects(int|string $folderId, object|array|null $filters = null): array
    {
        return Gallery::getInstance()->galleryService->getAllObjects($folderId, $filters);
    }

    /**
     * Creates the path from folder 1 to 2
     */
    
    public function getPath(int|string $startId, int|string $endId): array
    {
        return Gallery::getInstance()->galleryService->getPath($startId, $endId);
    }

}
