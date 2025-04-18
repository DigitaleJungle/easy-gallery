<?php

namespace digitalejungle\crafteasygallery\models;

use Craft;
use craft\models\VolumeFolder;
use digitalejungle\crafteasygallery\Gallery;
use digitalejungle\crafteasygallery\models\settings;
use Twig\Markup;

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
     * Return the folder’s name.
     */
    public function getfileName(): string
    {
        return $this->currentFolder->name;
    }
    public function getSlug(): string
    {
        return mb_strtolower($this->currentFolder->name, 'UTF-8');
    }
    public function getTitle(): string
    {
        return Gallery::getInstance()
        ->displayNameService
        ->getDisplayName($this->currentFolder->id);
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
    public function render(array $variables = []): Markup
    {
        $settings = Gallery::getInstance()->getSettings();
        $template = $settings->folderTemplate ?: null;
        $variables['entry'] = $this;
        if ($template) {
            $output = Craft::$app->view->renderTemplate($template, $variables);
        } else {
            $output = '<p>' . $this->getTitle() . '</p>';
        }

        return new Markup($output, Craft::$app->view->getTwig()->getCharset());
    }
}
