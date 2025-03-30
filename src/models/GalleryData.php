<?php

namespace digitalejungle\crafteasygallery\models;

use Craft;
use craft\models\VolumeFolder;
use digitalejungle\crafteasygallery\Gallery;
use digitalejungle\crafteasygallery\models\settings;

/**
 * A simple container for a single folder.
 */
class GalleryData
{
    public VolumeFolder $currentFolder;

    public function __construct(VolumeFolder $folder)
    {
        $this->currentFolder = $folder;
    }

    /**
     * Return the folder’s ID.
     */
    public function getId(): int
    {
        return $this->currentFolder->id;
    }

    /**
     * Return the folder’s name or a cleaned-up variant.
     */
    public function getTitle(bool $clean = false): string
    {
        if ($clean) {
            // Example: replace hyphens with spaces
            return str_replace('-', ' ', $this->currentFolder->name);
        }
        return $this->currentFolder->name;
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
        return (string)$this->currentFolder->id;
    }

    /**
     * Return the parent folder (if it exists).
     *
     * Note: In Twig, {{ node.folder }} calls getFolder() because of how
     * Craft/Twig treat object property lookups vs. getter methods.
     */
    public function getFolder(): ?VolumeFolder
    {
        if (!$this->currentFolder->parentId) {
            return null;
        }

        return Craft::$app->assets->getFolderById($this->currentFolder->parentId);
    }

    /**
     * Returning the Parent's ID if it's exists
     */
    public function getFolderId(): ?int
    {
        return $this->currentFolder->parentId ?: null;
    }


    /**
     * Renders the folder and makes it available as 'entry' in the custom template
     */
    public function render(array $variables = []): string
    {
        $settings = Gallery::getInstance()->getSettings();
        $template = $settings->folderTemplate ?: null;
        $variables['entry'] = $this;
        if ($template) {
            return Craft::$app->view->renderTemplate($template, $variables);
        }

        return $this->getTitle();
    }
}
