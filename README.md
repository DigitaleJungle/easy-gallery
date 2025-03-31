# Easy Gallery

This plugin provides a new field type and extra functionality to easily create galleries. Create beautiful galleries using nothing more than your asset folders & subfolders.

With the Gallery Folder field, you select a specific folder from your defined asset volume(s).

### the Gallery Folder field:

**{{ myGalleryField }}:** 1
**{{ myGalleryField.id }}:** 1
**{{ myGalleryField.title }}:** Simple Volume

The plugin adds the possibility to fetch all objects within a specified folder (including the folders) and gives you the ability to render the folder using the native render() option and a specified twig template.

You're able to fetch multiple information on the folder as well:
**{{ folder.kind }}** => results in 'folder'
**{{ folder.title }}** => renders the title of the folder
**{{ folder.id }}** => returns the id of the folder
**{{ folder.folder }}** => name of the parent folder
**{{ folder.folderId }}** => id of the parent folder.

These methods combined allows you to loop over all objects and render them seemlessly.

Planned additions to the plugin:

- Generating an array of all folders between 2 folderId's given
- Possibility to fetch all descendants (not only direct children)

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

Easy Gallery provides 2 main functionalities.

1. It adds a new field type giving the user the option to select a folder within the specified volumes. Calling the value of this field returns you the selected folder ID.
   There are other values available as well:
   **{{ myEasyGalleryField }}** or **{{ myEasyGalleryField.id }}** => the ID of the selected folder
   **{{ myEasyGallerField.title }}** => The name of the selected folder
2. It adds a new service you can call using {{ craft.easyGallery }}
   **{{ craft.easyGallery.getObjects( *Folder Id, {filter}* ) }}**
   Query All objects from the specified folder, including folders
   **{{ craft.easyGallery.getFolders( *Folder Id* ) }}**
   Query all folders from a specified folder
   **{{ craft.easyGallery.getAssets( *Folder Id, {filter}* ) }}**
   Query all assets from a specified folder, allows optional filtering on the kind of object.

*{filter}* accepts all posssible assets filters. For example

```json
{
   "kind": ["option1", "option2"],
   "size": ">=100",
   "extension: "pdf"
}

```
