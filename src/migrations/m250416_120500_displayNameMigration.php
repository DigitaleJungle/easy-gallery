<?php

namespace digitalejungle\crafteasygallery\migrations;

use Craft;
use craft\records\VolumeFolder;
use craft\db\Migration;

/**
 * m250416_120500_displayNameMigration migration.
 */
class m250416_120500_displayNameMigration extends Migration
{
    public function safeUp(): void
    {
        $table = Craft::$app->db->schema->getTableSchema('{{%easygallery_folderdisplayname}}');
        if ($table === null) {
            $this->createTable(
                '{{%easygallery_folderdisplayname}}',
                [
                    'id' => $this->primaryKey(),
                    'folderId' => $this->integer()->notNull(),
                    'displayName' => $this->string()->notNull(),
                ]
            );
            $this->createIndex(
                null,
                '{{%easygallery_folderdisplayname}}',
                ['folderId'],
                true
            );
        }

        $folders = VolumeFolder::find()->all();

        foreach ($folders as $folderRecord) {
            $this->insert(
                '{{%easygallery_folderdisplayname}}',
                [
                    'folderId' => $folderRecord->id,
                    'displayName' => $folderRecord->name,
                ]
            );
        }
    }

    public function safeDown(): void
    {
        $this->dropTableIfExists('{{%easygallery_folderdisplayname}}');
    }
}
