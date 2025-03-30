# Easy Gallery

This plugin provides a new field type and extra functionality to easily create galleries. Create beautiful galleries using nothing more than your asset folders & subfolders.

## Requirements

This plugin requires Craft CMS 5.0.0 or later, and PHP 8.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Easy Gallery”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require digitale-jungle/craft-easy-gallery

# tell Craft to install the plugin
./craft plugin/install easy-gallery

```

## Usage
Easy Gallery provides 2 main pieces.
1. It adds a new field type giving the user the option to select a folder.
    Calling the value of this field returns you the selected folder ID.
    There are other values available as well:
    {{ myEasyGalleryField }} or {{ myEasyGalleryField.id }} => the ID of the selected folder
    {{ myEasyGallerField.title }} => The name of the selected folder
2. It adds a new service you can call using {{ craft.easyGallery(folderID) }}

This extra service gives you the possibility to loop over all content within a specified folder.
{{ craft.easyGallery(folderID). }}