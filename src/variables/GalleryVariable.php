<?php

namespace digitalejungle\crafteasygallery\variables;

use digitalejungle\crafteasygallery\Gallery;

class GalleryVariable
{
    /* Example usage in Twig: {{ craft.easyGallery.getGallery(folderId) }} */
    public function getGallery(int $folderId)
    {
        // Access the plugin service
        return Gallery::getInstance()->galleryService->getGallery($folderId);
    }

    /**
     * You can add more methods here if you want, for example:
     * craft.easyGallery.getSubfolders(folderId)
     * craft.easyGallery.getAssets(folderId)
     * etc.
     */
}
