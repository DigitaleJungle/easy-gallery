<?php

namespace digitalejungle\crafteasygallery\models;

use Craft;
use craft\models\VolumeFolder;
use craft\elements\Asset;

/**
 * A custom data wrapper for a selected folder.
 */
class GalleryData
{
    public VolumeFolder $folder;

    // Optional caching props
    private ?array $_childFolders = null;
    private ?array $_childAssets = null;

    public function __construct(VolumeFolder $folder)
    {
        $this->folder = $folder;
    }

    /**
     * Returns the folder’s label (its name).
     */
    public function getTitle(?bool $clean = false): string
    {
        if ($clean) {
            // Example: replace all hyphens with spaces
            return str_replace('-', ' ', $this->folder->name);
        }
        return $this->folder->name;
    }

    /**
     * Returns the folder’s ID.
     */
    public function getId(): int
    {
        return $this->folder->id;
    }

    /**
     * Returns the folder type (just "folder").
     */
    public function getKind(): string
    {
        return 'folder';
    }

    /**
     * Returns an array of all direct child nodes (folders + assets).
     * Optionally filtered by asset kind(s) if $assetType is passed.
     */
    public function getObjects(string|array|null $assetType = null): array
    {
        // Start with child folders
        $nodes = $this->getFolders();

        // Then append child assets (filtered)
        $assets = $this->getAssets($assetType);
        return array_merge($nodes, $assets);
    }

    /**
     * Returns an array of assets in this folder, possibly filtered by $assetType.
     */
    public function getAssets(string|array|null $assetType = null): array
    {
        // Only query once
        if ($this->_childAssets === null) {
            $this->_childAssets = Asset::find()
                ->folderId($this->folder->id)
                ->all();
        }

        if ($assetType) {
            if (is_string($assetType)) {
                $assetType = [$assetType];
            }
            return array_filter($this->_childAssets, function ($asset) use ($assetType) {
                return in_array($asset->kind, $assetType, true);
            });
        }

        return $this->_childAssets;
    }

    /**
     * Returns an array of direct child folders, each wrapped as GalleryData.
     */
    public function getFolders(): array
    {
        if ($this->_childFolders !== null) {
            return $this->_childFolders;
        }

        $childFolders = Craft::$app->assets->findFolders([
            'parentId' => $this->folder->id,
        ]);

        $this->_childFolders = [];
        foreach ($childFolders as $childFolder) {
            $this->_childFolders[] = new self($childFolder);
        }

        return $this->_childFolders;
    }

    /**
     * Magic getter for Twig: {{ folder.title }}, etc.
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return null;
    }

    /**
     * When cast to a string, return the folder ID.
     */
    public function __toString(): string
    {
        return (string)$this->folder->id;
    }
}
