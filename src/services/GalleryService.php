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
    public function getAssets(int|string $folderId, object|array|null $filters = null): array
    {
        // If Twig passes {"kind": ["image", "pdf"]}, that’s an array, so cast it to an object.
        if (is_array($filters)) {
            $filters = (object)$filters;
        }
    
        // Start building the Asset query.
        $query = Asset::find()->folderId($folderId);
    
        // If a filters object was given, apply each filter method directly to the query.
        if ($filters) {
            foreach (get_object_vars($filters) as $property => $value) {
                // Check if $query has a method named after this property.
                // For example, 'height' => we call $query->height($value).
                if (method_exists($query, $property)) {
                    $query->$property($value);
                } else {
                    // Optionally, log or handle unknown filter keys.
                    // e.g. Craft::warning("Unknown filter $property", __METHOD__);
                }
            }
        }
    
        // Finally, fetch the results after query modifications.
        return $query->all();
    }

    /**
     * Return everything in a folder (subfolders + assets).
     * Subfolders returned as GalleryData objects, assets as elements.
     */
    public function getObjects(int|string $folderId, object|array|null $filters = null): array
    {
        if (is_array($filters)) {
            $filters = (object)$filters;
        }
    
        $folders = $this->getFolders($folderId);
        $assets = $this->getAssets($folderId, $filters);
    
        // Merge the arrays (subfolders first, then assets).
        return array_merge($folders, $assets);
    }
}
