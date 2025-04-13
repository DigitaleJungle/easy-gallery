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


    public function getAllFolders(int|string $folderId): array
    {
        //Get all direct children of the folder
        $childFolders = Craft::$app->assets->findFolders([
            'parentId' => $folderId,
        ]);
        $results = [];
        foreach ($childFolders as $childFolder) {
            // Convert the folder to a GalleryData object.
            $results[] = new GalleryData($childFolder);
            $results = array_merge($results, $this->getAllFolders($childFolder->id));
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

    public function getAllAssets(int|string $folderId, object|array|null $filters = null): array
    {
        // If Twig passes {"kind": ["image", "pdf"]}, that’s an array, so cast it to an object.
        if (is_array($filters)) {
            $filters = (object)$filters;
        }

        // Start by fetching the assets in the current folder.
        $allAssets = $this->getAssets($folderId, $filters);

        // Find all immediate child folders.
        $childFolders = Craft::$app->assets->findFolders([
            'parentId' => $folderId,
        ]);

        // Recursively collect assets from each child folder.
        foreach ($childFolders as $childFolder) {
            $allAssets = array_merge($allAssets, $this->getAllAssets($childFolder->id, $filters));
        }

        return $allAssets;
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

    public function getAllObjects(int|string $folderId, object|array|null $filters = null): array
    {
        // Convert an array of filters to an object, if needed.
        if (is_array($filters)) {
            $filters = (object) $filters;
        }

        // Get all folders, recursively.
        $allFolders = $this->getAllFolders($folderId);

        // Get all assets, recursively, optionally applying filters.
        $allAssets = $this->getAllAssets($folderId, $filters);

        // Merge them into one array, with folders first.
        return array_merge($allFolders, $allAssets);
    }

    /**
     * Returns an array of VolumeFolder objects starting at $folderId and
     * moving up through each parent until reaching the root.
     * The first folder in the array is $folderId; the last is the root folder.
     */
    private function getFolderAncestry(int $folderId): array
    {
        $chain = [];
        $current = Craft::$app->assets->getFolderById($folderId);

        while ($current) {
            $chain[] = $current;

            // Stop if there's no parent
            if (!$current->parentId) {
                break;
            }

            // Move upward to the parent
            $current = Craft::$app->assets->getFolderById($current->parentId);
        }

        return $chain;
    }


    public function getPath(int $startId, int $endId): array
    {
        // If $startId and $endId are the same, just return one folder
        if ($startId === $endId) {
            $folder = Craft::$app->assets->getFolderById($startId);
            return $folder ? [new GalleryData($folder)] : [];
        }
    
        // Ancestors from child up to root
        $startChain = $this->getFolderAncestry($startId); // array of VolumeFolder objects
        $endChain   = $this->getFolderAncestry($endId);
    
        // Put the startChain folders into a lookup so we can find the first match
        $seen = [];
        foreach ($startChain as $folder) {
            $seen[$folder->id] = $folder;
        }
    
        // Find the first folder in $endChain that’s also in $startChain
        $lcaFolder = null;
        foreach ($endChain as $folder) {
            if (isset($seen[$folder->id])) {
                $lcaFolder = $folder;
                break;
            }
        }
    
        // If no common ancestor is found, return empty (or handle however you prefer)
        if (!$lcaFolder) {
            return [];
        }
    
        // Build the path from $startId up to LCA
        $pathUp = [];
        foreach ($startChain as $folder) {
            $pathUp[] = $folder;
            if ($folder->id === $lcaFolder->id) {
                break;
            }
        }
    
        // Build the path from $endId up to LCA, then we'll reverse it
        $pathDown = [];
        foreach ($endChain as $folder) {
            if ($folder->id === $lcaFolder->id) {
                // We'll include LCA only once, so stop here
                break;
            }
            $pathDown[] = $folder;
        }
        $pathDown = array_reverse($pathDown);
    
        // Combine them, wrapping each folder in GalleryData
        $combined = array_merge($pathUp, $pathDown);
    
        // Finally, wrap in GalleryData objects (so it's consistent with the rest of your plugin)
        $results = [];
        foreach ($combined as $fldr) {
            $results[] = new GalleryData($fldr);
        }
    
        return $results;
    }  

}
