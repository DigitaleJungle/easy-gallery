<?php

namespace digitalejungle\crafteasygallery\services;

use Craft;
use craft\base\Component;

class DisplayNameService extends Component
{
    public function UpdateFolder(string $id, string $name): void
    {
        $table = Craft::$app->db->schema->getTableSchema('{{%easygallery_folderdisplayname}}');
        if ($table === null) {
            die("table does not exist");
        } else {

            Craft::$app->db->createCommand()
            ->upsert(
                '{{%easygallery_folderdisplayname}}',
                [
                    'folderId'    => $id,
                    'displayName' => $name,
                ]
            )
            ->execute();
        }
    }
}