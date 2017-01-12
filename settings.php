<?php
/**
 * @internal    never define functions inside callbacks.
 *              these functions could be run multiple times; this would result in a fatal error.
 */
 
/**
 * custom option and settings
 */
function zk_file_agreement_settings_init()
{
    // register a new setting for "zk_file_agreement" page
    register_setting('zk_file_agreement', 'zk_file_agreement_field_message');
    register_setting('zk_file_agreement', 'zk_file_agreement_field_button');
    register_setting('zk_file_agreement', 'zk_file_agreement_download_slug');
 
    // register a new section in the "zk_file_agreement" page
    add_settings_section(
        'zk_file_agreement_section_developers',
        __('General settings', 'zk_file_agreement'),
        'zk_file_agreement_section_developers_cb',
        'zk_file_agreement'
    );
 
    // register a new field in the "zk_file_agreement_section_developers" section, inside the "zk_file_agreement" page
    add_settings_field(
        'zk_file_agreement_field_message', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __('Agreement Message', 'zk_file_agreement'),
        'zk_file_agreement_field_message_cb',
        'zk_file_agreement',
        'zk_file_agreement_section_developers',
        [
            'label_for'         => 'zk_file_agreement_field_message',
            'class'             => 'zk_file_agreement_row',
        ]
    );

    add_settings_field(
        'zk_file_agreement_field_button', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __('Agreement Button Text', 'zk_file_agreement'),
        'zk_file_agreement_field_button_cb',
        'zk_file_agreement',
        'zk_file_agreement_section_developers',
        [
            'label_for'         => 'zk_file_agreement_field_button',
            'class'             => 'zk_file_agreement_row',
        ]
    );

    add_settings_field(
        'zk_file_agreement_download_slug', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __('Download Slug', 'zk_file_agreement'),
        'zk_file_agreement_field_slug_cb',
        'zk_file_agreement',
        'zk_file_agreement_section_developers',
        [
            'label_for'         => 'zk_file_agreement_download_slug',
            'class'             => 'zk_file_agreement_row',
        ]
    );
}
 
/**
 * register our zk_file_agreement_settings_init to the admin_init action hook
 */
add_action('admin_init', 'zk_file_agreement_settings_init');
 
/**
 * custom option and settings:
 * callback functions
 */
 
// developers section cb
 
// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function zk_file_agreement_section_developers_cb($args)
{
    
}
 
// pill field cb
 
// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function zk_file_agreement_field_message_cb($args)
{
    // get the value of the setting we've registered with register_setting()
    $zk_file_agreement_field_message = get_option('zk_file_agreement_field_message');
    // output the field
    ?>
    <textarea class='large-text' rows='5' id="zk_file_agreement_field_message"
            
            name="zk_file_agreement_field_message"
    ><?= $zk_file_agreement_field_message ?></textarea>
    <p class="description">
        <?= esc_html('This is the messages that will be displayed to users before they can download files.', 'zk_file_agreement'); ?>
    </p>
    <?php
}

function zk_file_agreement_field_button_cb($args)
{
    // get the value of the setting we've registered with register_setting()
    $zk_file_agreement_field_button = get_option('zk_file_agreement_field_button');
    // output the field
    ?>
    <input type='text' value="<?= $zk_file_agreement_field_button ?>" class='large-text' id="zk_file_agreement_field_button"
            
            name="zk_file_agreement_field_button"
    >
    <p class="description">
        <?= esc_html('This is the text that will display inside the "agree" button. Use [name] as a placeholder for the actual file name.', 'zk_file_agreement'); ?>
    </p>
    <?php
}

function zk_file_agreement_field_slug_cb($args)
{
    // get the value of the setting we've registered with register_setting()
    $zk_file_agreement_download_slug = get_option('zk_file_agreement_download_slug','download');
    // output the field
    ?>
    <input type='text' value="<?= $zk_file_agreement_download_slug ?>" class='large-text' id="zk_file_agreement_download_slug"
            
            name="zk_file_agreement_download_slug"
    >
    <p class="description">
        <?= esc_html('The &quot;slug&quot; for the download page. After confirming they agree to the terms, users will be redirected to the URL '.get_home_url().'/[slug]', 'zk_file_agreement'); ?>
    </p>
    <?php
}
 
/**
 * top level menu
 */
function zk_file_agreement_options_page()
{
    // add top level menu page
    add_menu_page(
        'File Agreement Settings',
        'File Agreement',
        'manage_options',
        'zk_file_agreement',
        'zk_file_agreement_options_page_html'
    );
}
 
/**
 * register our zk_file_agreement_options_page to the admin_menu action hook
 */
add_action('admin_menu', 'zk_file_agreement_options_page');
 
/**
 * top level menu:
 * callback functions
 */
function zk_file_agreement_options_page_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
 
    // add error/update messages
 
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
        // add settings saved message with the class of "updated"
        add_settings_error('zk_file_agreement_messages', 'zk_file_agreement_message', __('Settings Saved', 'zk_file_agreement'), 'updated');
    }
 
    // show error/update messages
    settings_errors('zk_file_agreement_messages');
    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "zk_file_agreement"
            settings_fields('zk_file_agreement');
            // output setting sections and their fields
            // (sections are registered for "zk_file_agreement", each field is registered to a specific section)
            do_settings_sections('zk_file_agreement');
            // output save settings button
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}