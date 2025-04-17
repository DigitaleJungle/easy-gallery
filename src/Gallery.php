<?php

namespace digitalejungle\crafteasygallery;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use craft\controllers\AssetsController;
use craft\events\ActionEvent;
use craft\web\Controller;
use craft\web\twig\variables\CraftVariable;
use digitalejungle\crafteasygallery\fields\GalleryField;
use digitalejungle\crafteasygallery\models\Settings;
use digitalejungle\crafteasygallery\services\GalleryService;
use digitalejungle\crafteasygallery\services\DisplayNameService;
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
    public string $schemaVersion = '1.2.1';
    public bool $hasCpSettings = true;
    private static ?string $folderName = null;
    private static ?string $existingFolderId = null;

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
                'displayNameService' => [
                    'class' => DisplayNameService::class,
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

        Event::on(
            AssetsController::class,
            Controller::EVENT_BEFORE_ACTION,
            function ($event) {
                if ($event->action->id === 'create-folder') {
                    $request = Craft::$app->getRequest();
                    self::$folderName = $request->getBodyParam('folderName');
                } else if ($event->action->id === 'rename-folder') {
                    $request = Craft::$app->getRequest();
                    self::$folderName = $request->getBodyParam('newName');
                    self::$existingFolderId = $request->getBodyParam('folderId');
                } else if ($event->action->id === 'delete-folder') {
                    $request = Craft::$app->getRequest();
                    self::$existingFolderId = $request->getBodyParam('folderId');
                    self::getInstance()->displayNameService->deleteFolder(self::$existingFolderId);
                    self::$existingFolderId = null;
                }
            }
        );

        Event::on(
            assetsController::class,
            Controller::EVENT_AFTER_ACTION,
            function ($event) {
                $actionId = $event->action->id;
                if (in_array($actionId, ['create-folder','rename-folder'], true) && self::$folderName !== null) {
                    $responseData = $event->result;
                    if (self::$existingFolderId !== null) {
                        $folderId = self::$existingFolderId;
                    } else {
                        $folderId = $responseData->data['folderId'] ?? null;
                    }
                    self::getInstance()->displayNameService->updateFolder($folderId, self::$folderName);
                    self::$folderName = null;
                    self::$existingFolderId = null;
                }
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
