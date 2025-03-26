<?php

namespace digitalejungle\crafteasygallery;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use digitalejungle\crafteasygallery\models\Settings;

/**
 * Easy Gallery plugin
 *
 * @method static Gallery getInstance()
 * @method Settings getSettings()
 * @author Digitale Jungle <info@digitalejungle.be>
 * @copyright Digitale Jungle
 * @license https://craftcms.github.io/license/ Craft License
 */
class Gallery extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => [
                // Define component configs here...
            ],
        ];
    }

    public function init(): void
    {
        parent::init();
        Craft::setAlias('@digitalejungle/crafteasygallery', __DIR__);
        $this->attachEventHandlers();

        // Any code that creates an element query or loads Twig should be deferred until
        // after Craft is fully initialized, to avoid conflicts with other plugins/modules
        Craft::$app->onInit(function () {
            Craft::$app->fields->on(
                \craft\services\Fields::EVENT_REGISTER_FIELD_TYPES,
                function (\craft\events\RegisterComponentTypesEvent $event) {
                    $event->types[] = \digitalejungle\crafteasygallery\fields\GalleryField::class;
                }
            );
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('easy-gallery/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/5.x/extend/events.html to get started)
    }
}
