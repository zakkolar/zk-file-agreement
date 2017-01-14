# File Agreement/Manager
WordPress plugin to force users to agree to terms before downloading files

## Installation

### Manual Installation
1. Copy the entire `zk-file-agreement` folder to `wp-content/plugins` in your Wordpress installation
2. Navigate to the "Plugins" menu from your Wordpress dashboard
3. Activate "File Agreement/Manager"

### Automatic Installation
1. Download `zk-file-agreement` as a .zip file
2. Navigate to the "Plugins" menu from your Wordpress dashboard
3. Click "Add new"
4. Click "Upload plugin"
5. Browse for `zk-file-agreement.zip` and click "Install now"
6. Activate "File Agreement/Manager" from the plugin menu

## Usage

### Adding files
1. From the Wordpress Administration section, navigate to the "Protected Files" page listed in the left menu and click "Add new."
2. Type the name of the file in the Title box.
3. Type a description for the file in the text editor.
4. Browse for the file you'd like to attach in the File section.
5. Choose a category for the file in the "File Category" box below the "Publish" button. **A file cannot be displayed on your site without being assigned to a category.**
6. Click "Publish" when you are ready for the file to appear on your site.

### Displaying file lists
1. Navigate to the edit screen for the page/post on which you'd like to display the file list.
2. Place the following shortcode where you'd like the file list to appear on the page:

  ```
  [file_agreement_download category="(category name)" table_class="(css class for table)"]
  ```
  Replace (category name) with the name of the category you assigned your file(s) (including spaces). Optionally, you can add a css class that will be applied to the table when it is displayed on the page.

This shortcode will display a table of the files in the category and their descriptions. When users click on the name of a file, the agreement message will be shown and they will be prompted to agree. Any content outside of the shortcode will dispaly both in the table view and agreement view.

## Settings

From the Wordpress Administration section, navigate to the "File Agreement" page listed in the left menu. Several settings are available:

**Agreement Message**: The statement users must agree to before downloading files. It will be shown every time they select a file to download.

**Agreement Button Text**: The text of the button users click to affirm they agree and download the chosen file. Use `[name]` to include the name of the file in the button.

**Download Slug**: The URL users will be directed to upon clicking the agreement button where the file will be downloaded, relative to the home page of the site.

## MIME Types

For security purposes, the allowed MIME types for files uploaded with this plugin are whitelisted. By default, the plugin allows PDFs, Zip files, and common Microsoft Office files. To allow more MIME types, edit `zk-file-agreement/allowed-mime-types.php` and add the new types to the `$supported_types` array.

## File Security

**This plugin is not 100% secure.** Files are uploaded using the default Wordpress behavior for uploading files (i.e. they are placed into `/wp-content/uploads/[year]/[month]/[filename]`. A random string of characters is added to the filename when it is initially uploaded to add a level of obfuscation. This link is never publicly displayed to users - the file is dynamically output on the page corresponding to the download slug. However, if a user knows (or guesses) the path relative to the `/wp-content/uploads` directory, they will be able to access the file without agreeing to the terms. This is unlikely, but **not** impossible.

