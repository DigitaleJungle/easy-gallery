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

    public function deleteFolder(string $id): void
    {
        $ids = $this->getAllFolderIds($id);


        $ids[] = (int)$id;
        Craft::$app->db->createCommand()
            ->delete(
                '{{%easygallery_folderdisplayname}}',
                ['folderId' => $ids] 
            )
            ->execute();
    }

    private function getAllFolderIds(int $parentId): array
    {
        $children = Craft::$app->assets->findFolders([
            'parentId' => $parentId,
        ]);

        $ids = [];

        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids   = array_merge($ids, $this->getAllFolderIds($child->id));
        }

        return $ids;
    }

    public function getDisplayName(int $folderId): ?string
    {
        $stored = (new \craft\db\Query())
            ->select(['displayName'])
            ->from('{{%easygallery_folderdisplayname}}')
            ->where(['folderId' => $folderId])
            ->scalar() ?: null;

        if ($stored === null) {
            return Craft::$app->assets->getFolderById($folderId)->name;
        }

    return $stored; 
    }
}