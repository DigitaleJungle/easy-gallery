<?php

namespace digitalejungle\crafteasygallery;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use digitalejungle\crafteasygallery\fields\GalleryField;
use digitalejungle\crafteasygallery\models\Settings;
use digitalejungle\crafteasygallery\services\GalleryService;
use digitalejungle\crafteasygallery\variables\GalleryVariable;
use yii\base\Event;

/**
 * Easy Gallery plugin
 *
 * @method static Gallery getInstance()
 * @method Settings getSettings()
 */
class Gallery extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    /**
     * Register components/services for this plugin.
     */
    public static function config(): array
    {
        return [
            'components' => [
                'galleryService' => [
                    'class' => GalleryService::class,
                ],
            ],
        ];
    }

    /**
     * Optionally customize the CP nav item.
     */
    public function getCpNavItem(): ?array
    {
        $nav = parent::getCpNavItem();
        // If you have a CP section, define it here if needed
        return $nav;
    }

    /**
     * Plugin initialization.
     */
    public function init(): void
    {
        parent::init();

        \Craft::info('init() - Easy Gallery is initializing', __METHOD__);

        $this->attachEventHandlers();

        // Defer registration until after Craft is fully initialized
        Craft::$app->onInit(function () {
            // Register a custom field type
            Craft::$app->fields->on(
                Fields::EVENT_REGISTER_FIELD_TYPES,
                function (RegisterComponentTypesEvent $event) {
                    $event->types[] = GalleryField::class;
                }
            );
        });

        // Register "easyGallery" as a global variable in Twig
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('easyGallery', GalleryVariable::class);
            }
        );
    }

    private function attachEventHandlers(): void
    {
        // Example: register your plugin’s own event handlers here if needed.
    }

        /**
     * Creates and returns the model used to store the plugin’s settings.
     **/
    protected function createSettingsModel(): Model
    {
        return new Settings();
    }

    protected function settingsHtml(): string
    {
        $volumes = Craft::$app->getVolumes();
        foreach ($volumes->getAllVolumes() as $source) {
            $destinationOptions[] = array('label' => $source->name, 'value' => $source->id);
        }
        return Craft::$app->view->renderTemplate(
            'easy-gallery/_settings',
            [
                'settings' => $this->getSettings(),
                'volumes' => $destinationOptions ?? null,
            ]
        );
    }
}
