<?php

namespace digitalejungle\crafteasygallery\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Html;
use craft\models\VolumeFolder;
use digitalejungle\crafteasygallery\models\GalleryData;

/**
 * Your custom field that picks a folder and normalizes to GalleryData.
 */
class GalleryField extends Field
{
    /**
     * @var array Volume IDs selected in field settings
     */
    public array $volumeIds = [];

    public static function displayName(): string
    {
        return Craft::t('app', 'Gallery Folder');
    }


    public static function icon(): string
    {
        return '@digitalejungle/crafteasygallery/field-icon.svg';
    }


    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        // Validate that each ID is an integer
        $rules[] = [['volumeIds'], 'each', 'rule' => ['integer']];
        return $rules;
    }

    /**
     * Render the Volume checkboxes in the field settings.
     */
    public function getSettingsHtml(): ?string
    {
        $volumes = Craft::$app->volumes->getAllVolumes();
        $checkboxes = [];

        foreach ($volumes as $volume) {
            $checkboxes[] = Html::checkbox(
                'volumeIds[]',
                in_array($volume->id, $this->volumeIds, false),
                [
                    'value' => $volume->id,
                    'label' => $volume->name,
                ]
            );
        }

        return '<div class="field">' .
            '<div class="heading"><label>Volumes in scope</label></div>' .
            '<div class="input">' . implode('<br>', $checkboxes) . '</div>' .
            '</div>';
    }

    /**
     * Render the dropdown in the entry form.
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // If the value is already normalized, extract the folder ID for comparisons.
        if ($value instanceof GalleryData) {
            $value = $value->getId();
        }

        // Build an initial placeholder option.
        $optionsHtml = '<option disabled selected>-- Choose a folder --</option>';

        // For each selected volume, get its root folder & render options.
        foreach ($this->volumeIds as $volumeId) {
            $volume = Craft::$app->volumes->getVolumeById($volumeId);
            if (!$volume) {
                continue;
            }

            $rootFolder = Craft::$app->assets->getRootFolderByVolumeId($volumeId);
            if (!$rootFolder) {
                continue;
            }

            // Always render the root folder option.
            $isSelected = ((int)$value === (int)$rootFolder->id) ? 'selected' : '';
            $optionsHtml .= "<option value=\"{$rootFolder->id}\" {$isSelected}>{$volume->name}</option>";

            // Get direct children of the root folder and render them recursively.
            $children = Craft::$app->assets->findFolders(['parentId' => $rootFolder->id]);
            foreach ($children as $childFolder) {
                $optionsHtml .= $this->_renderFolderOptions($childFolder, $value, 1);
            }
        }

        // Wrap the <select> with Craft’s .select wrapper for styling.
        $selectHtml = "<select id=\"{$this->handle}\" name=\"{$this->handle}\">{$optionsHtml}</select>";
        return Html::tag('div', $selectHtml, ['class' => 'select']);
    }

    /**
     * Recursively build options from a folder down.
     */
    private function _renderFolderOptions(VolumeFolder $folder, $selectedValue, int $level): string
    {
        // For subfolders only, add indentation: repeat spaces per level and a single hyphen.
        $prefix = $level > 0 ? str_repeat('&nbsp;&nbsp;&nbsp;', $level) . '- ' : '';
        $isSelected = ((int)$folder->id === (int)$selectedValue) ? 'selected' : '';
        $html = "<option value=\"{$folder->id}\" {$isSelected}>{$prefix}{$folder->name}</option>";

        // Recurse any children
        $children = Craft::$app->assets->findFolders(['parentId' => $folder->id]);
        foreach ($children as $childFolder) {
            $html .= $this->_renderFolderOptions($childFolder, $selectedValue, $level + 1);
        }

        return $html;
    }

    /**
     * Normalize the stored value to a `GalleryData` object.
     */
    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        // If it's already normalized, just return it.
        if ($value instanceof GalleryData) {
            return $value;
        }

        if (!$value) {
            return null;
        }

        $folderId = (int)$value;
        $folder = Craft::$app->assets->getFolderById($folderId);
        if (!$folder) {
            return null;
        }

        return new GalleryData($folder);
    }

    public function serializeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        if ($value instanceof GalleryData) {
            return $value->id;
        }
        return $value;
    }
}
