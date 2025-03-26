<?php

namespace digitalejungle\crafteasygallery\models;

use craft\models\VolumeFolder;

/**
 * A simple container for a single folder.
 */
class GalleryData
{
    public VolumeFolder $folder;

    public function __construct(VolumeFolder $folder)
    {
        $this->folder = $folder;
    }

    /**
     * Return the folder’s ID.
     */
    public function getId(): int
    {
        return $this->folder->id;
    }

    /**
     * Return the folder’s name or a cleaned-up variant.
     */
    public function getTitle(bool $clean = false): string
    {
        if ($clean) {
            // Example: replace hyphens with spaces
            return str_replace('-', ' ', $this->folder->name);
        }
        return $this->folder->name;
    }

    /**
     * For consistency, say "folder" as the "kind." 
     */
    public function getKind(): string
    {
        return 'folder';
    }

    /**
     * Casting to string returns the folder ID.
     */
    public function __toString(): string
    {
        return (string)$this->folder->id;
    }
}
