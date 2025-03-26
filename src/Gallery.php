<?php

namespace digitalejungle\crafteasygallery;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use craft\web\twig\variables\CraftVariable;
use digitalejungle\crafteasygallery\models\Settings;
use digitalejungle\crafteasygallery\services\GalleryService;
use digitalejungle\crafteasygallery\variables\GalleryVariable;
use yii\base\Event;

class Gallery extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    /* Registration of Sercive */
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

    /* CP nav item. */
    public function getCpNavItem(): ?array
    {
        $nav = parent::getCpNavItem();
        // You can define a control panel section here if needed.
        return $nav;
    }

    /* Plugin initialization. */
    public function init(): void
    {
        parent::init();
        $this->attachEventHandlers();

        // Defer registration of custom field types until after Craft is fully initialized
        Craft::$app->onInit(function () {
            Craft::$app->fields->on(
                Fields::EVENT_REGISTER_FIELD_TYPES,
                function (RegisterComponentTypesEvent $event) {
                    // Register the GalleryField as a custom field type
                    $event->types[] = \digitalejungle\crafteasygallery\fields\GalleryField::class;
                }
            );
        });

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {
                $variable = $event->sender;
                $variable->set('easyGallery', GalleryVariable::class);
            }
        );
    }

    /* Create a settings model for plugin-wide configuration. */
    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    /* Render the settings template for the plugin’s CP settings page. */
    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('easy-gallery/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    /* Attach any event handlers your plugin needs. */
    private function attachEventHandlers(): void
    {
        // Register event handlers here.
        // (see https://craftcms.com/docs/5.x/extend/events.html to get started)
    }
}
