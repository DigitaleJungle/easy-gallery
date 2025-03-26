<?php

namespace digitalejungle\crafteasygallery\services;

use Craft;
use craft\base\Component;
use craft\elements\Asset;
use craft\models\VolumeFolder;
use digitalejungle\crafteasygallery\models\GalleryData;

class GalleryService extends Component
{
    /**
     * Fetch a single folder by ID wrapped in a GalleryData object.
     */
    public function getGallery(int|string $folderId): ?GalleryData
    {
        $folder = Craft::$app->assets->getFolderById($folderId);
        if (!$folder) {
            return null; // No folder found
        }
        return new GalleryData($folder);
    }

    /**
     * Return an array of direct child folders for the given folder ID.
     */
    public function getFolders(int|string $folderId): array
    {
        $childFolders = Craft::$app->assets->findFolders([
            'parentId' => $folderId,
        ]);

        // Wrap each folder in a GalleryData object, if you prefer:
        $results = [];
        foreach ($childFolders as $childFolder) {
            $results[] = new GalleryData($childFolder);
        }

        return $results;
    }

    /**
     * Return all assets in a folder, optionally filtered by kind(s).
     */
    public function getAssets(int|string $folderId, string|array|null $assetTypes = null): array
    {
        $query = Asset::find()->folderId($folderId);
        $assets = $query->all();

        if (!$assetTypes) {
            return $assets;
        }

        if (is_string($assetTypes)) {
            $assetTypes = [$assetTypes];
        }

        return array_filter($assets, fn($asset) => in_array($asset->kind, $assetTypes, true));
    }

    /**
     * Return everything in a folder (subfolders + assets).
     * Subfolders returned as GalleryData objects, assets as elements.
     */
    public function getObjects(int|string $folderId, string|array|null $assetTypes = null): array
    {
        $folders = $this->getFolders($folderId);
        $assets = $this->getAssets($folderId, $assetTypes);

        // Merge the arrays (subfolders first, then assets).
        return array_merge($folders, $assets);
    }
}
