<?php

namespace SmsAssistent\Admin;

// If this file is called directly, abort.
if (!defined('ABSPATH')) exit;

/**
 * Settings of the admin area.
 * Add the appropriate suffix constant for every field ID to take advantage the standardized sanitizer.
 *
 * @since      1.0.0
 *
 * @package    Sms_Assistent_Wc
 * @subpackage Sms_Assistent_Wc/Admin
 * @author     Anarchy <info@koshelev.ml>
 */
class Sms_Assistent_Wc_Settings {
    const TEXT_SUFFIX = '-tx';
    const TEXTAREA_SUFFIX = '-ta';
    const CHECKBOX_SUFFIX = '-cb';
    const RADIO_SUFFIX = '-rb';
    const SELECT_SUFFIX = '-sl';

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     */
    private $pluginSlug;

    /**
     * The slug name for the menu.
     * Should be unique for this menu page and only include
     * lowercase alphanumeric, dashes, and underscores characters to be compatible with sanitize_key().
     *
     * @since    1.0.0
     */
    private $menuSlug;

    /**
     * General settings' group name.
     *
     * @since    1.0.0
     */
    private $generalOptionGroup;
    private $newStatusOptionGroup;
    private $newCustomerOptionGroup;

    /**
     * General settings' section.
     * The slug-name of the section of the settings page in which to show the box.
     *
     * @since    1.0.0
     */
    private $generalSettingsSectionId;
    private $newStatusCustomerSettingsSectionId;
    private $newStatusManagerSettingsSectionId;
    private $newCustomerCustomerSettingsSectionId;
    private $newCustomerManagerSettingsSectionId;

    /**
     * General settings page.
     * The slug-name of the settings page on which to show the section.
     *
     * @since    1.0.0
     */
    private $generalPage;
    private $newStatusPage;
    private $newCustomerPage;

    /**
     * Name of general options. Expected to not be SQL-escaped.
     *
     * @since    1.0.0
     */
    private $generalOptionName;
    private $newStatusOptionName;
    private $newCustomerOptionName;

    /**
     * Collection of options.
     *
     * @since    1.0.0
     */
    private $generalOptions;
    private $newStatusOptions;
    private $newCustomerOptions;

    /**
     * Ids of general setting fields.
     */
    private $generalActive;
    private $generalUsername;
    private $generalPassword;
    private $generalToken;
    private $generalSender;
    private $generalBaseUrl;

    /**
     * Ids of new status setting fields.
     */
    private $newStatusCustomerActive;
    private $newStatusCustomerTemplate;
    private $newStatusManagerActive;
    private $newStatusManagerPhones;
    private $newStatusManagerTemplate;

    /**
     * Ids of new customer setting fields.
     */
    private $newCustomerCustomerActive;
    private $newCustomerCustomerTemplate;
    private $newCustomerManagerActive;
    private $newCustomerManagerPhones;
    private $newCustomerManagerTemplate;

    /**
     * Initialize the class and set its properties.
     *
     * @param   $pluginSlug    string  The name of this plugin.
     * @since   1.0.0
     */
    public function __construct($pluginSlug) {
        $this->pluginSlug = $pluginSlug;
        $this->menuSlug = $this->pluginSlug;

        /**
         * General settings
         */
        $this->generalOptionGroup = $pluginSlug . '-general-option-group';
        $this->generalSettingsSectionId = $pluginSlug . '-general-section';
        $this->generalPage = $pluginSlug . '-general';
        $this->generalOptionName = $pluginSlug . '-general';

        $this->generalActive = 'general-active' . self::CHECKBOX_SUFFIX;
        $this->generalUsername = 'general-username' . self::TEXT_SUFFIX;
        $this->generalPassword = 'general-password' . self::TEXT_SUFFIX;
        $this->generalToken = 'general-token' . self::TEXT_SUFFIX;
        $this->generalSender = 'general-sender' . self::TEXT_SUFFIX;
        $this->generalBaseUrl = 'general-base-url' . self::TEXT_SUFFIX;

        /**
         * New Order Status settings
         */
        $this->newStatusOptionGroup = $pluginSlug . '-new-status-option-group';
        $this->newStatusCustomerSettingsSectionId = $pluginSlug . '-new-status-customer-section';
        $this->newStatusManagerSettingsSectionId = $pluginSlug . '-new-status-manager-section';
        $this->newStatusPage = $pluginSlug . '-new-status';

        $this->newStatusOptionName = $pluginSlug . '-new-status';

        $this->newStatusCustomerActive = 'new-status-customer-active' . self::CHECKBOX_SUFFIX;
        $this->newStatusCustomerTemplate = 'new-status-customer-template' . self::TEXTAREA_SUFFIX;
        $this->newStatusManagerActive = 'new-status-manager-active' . self::CHECKBOX_SUFFIX;
        $this->newStatusManagerPhones = 'new-status-manager-phones' . self::TEXT_SUFFIX;
        $this->newStatusManagerTemplate = 'new-status-manager-template' . self::TEXTAREA_SUFFIX;

        /**
         * New Customer settings
         */
        $this->newCustomerOptionGroup = $pluginSlug . '-new-customer-option-group';
        $this->newCustomerCustomerSettingsSectionId = $pluginSlug . '-new-customer-customer-section';
        $this->newCustomerManagerSettingsSectionId = $pluginSlug . '-new-customer-manager-section';
        $this->newCustomerPage = $pluginSlug . '-new-customer';

        $this->newCustomerOptionName = $pluginSlug . '-new-customer';

        $this->newCustomerCustomerActive = 'new-customer-customer-active' . self::CHECKBOX_SUFFIX;
        $this->newCustomerCustomerTemplate = 'new-customer-customer-template' . self::TEXTAREA_SUFFIX;
        $this->newCustomerManagerActive = 'new-customer-manager-active' . self::CHECKBOX_SUFFIX;
        $this->newCustomerManagerPhones = 'new-customer-manager-phones' . self::TEXT_SUFFIX;
        $this->newCustomerManagerTemplate = 'new-customer-manager-template' . self::TEXTAREA_SUFFIX;

    }

    /**
     * Register all the hooks of this class.
     *
     * @param   $isAdmin    boolean    Whether the current request is for an administrative interface page.
     * @since   1.0.0
     */
    public function initializeHooks($isAdmin) {
        // Admin
        if ($isAdmin) {
            add_action('admin_menu', [$this, 'setupSettingsMenu'], 10);
            add_action('admin_init', [$this, 'initializeGeneralOptions'], 10);
            add_action('admin_init', [$this, 'initializeNewStatusOptions'], 10);
            add_action('admin_init', [$this, 'initializeNewCustomerOptions'], 10);
        }
    }

    /**
     * This function introduces the plugin options into the Main menu.
     */
    public function setupSettingsMenu() {
        //Add the menu item to the Options menu
        add_options_page(
            __('Configuring SMS sends', 'sms-assistent-wc'),   // Page title: The title to be displayed in the browser window for this page.
            __('SMS-Assistent', 'sms-assistent-wc'),           // Menu title: The text to be used for the menu.
            'manage_options',          // Capability: The capability required for this menu to be displayed to the user.
            $this->menuSlug,                   // Menu slug: The slug name to refer to this menu by. Should be unique for this menu page.
            [$this, 'renderSettingsPageContent'],  // Callback: The name of the function to call when rendering this menu's page
            81                                  // Position: The position in the menu order this item should appear.
        );
    }

    /**
     * Sanitize tab name from settings page
     *
     * @param string $tab
     *
     * @return string
     */
    private function sanitizeTab($tab) {
        switch ($tab) {
            case 'new_status_options':
                return 'new_status_options';
                break;
            case 'new_customer_options':
                return 'new_customer_options';
                break;
            default:
                return 'general_options';
        }
    }

    /**
     * Sanitize section name from settings page
     *
     * @param string $section
     * @param string $default
     *
     * @return string
     */
    private function sanitizeSection($section, $default) {
        if (wc_is_order_status($section)) {
            return $section;
        } else {
            return $default;
        }
    }

    /**
     * Renders the Settings page to display for the Settings menu defined above.
     */
    public function renderSettingsPageContent() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // Add error/update messages
        // check if the user have submitted the settings. Wordpress will add the "settings-updated" $_GET parameter to the url
        if (isset($_GET['settings-updated'])) {
            // Add settings saved message with the class of "updated"
            add_settings_error($this->pluginSlug, $this->pluginSlug . '-message', __('Settings saved'), 'success');
        }

        // Show error/update messages
        settings_errors($this->pluginSlug);

        ?>
        <!-- Create a header in the default WordPress 'wrap' container -->
        <div class="wrap">

            <h2><?php esc_html_e('Module settings', 'sms-assistent-wc'); ?></h2>

            <?php $activeTab = $this->sanitizeTab($_GET['tab']); ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=<?php echo $this->menuSlug; ?>&tab=general_options"
                   class="nav-tab <?php echo $activeTab === 'general_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Settings', 'sms-assistent-wc'); ?></a>
                <a href="?page=<?php echo $this->menuSlug; ?>&tab=new_status_options"
                   class="nav-tab <?php echo $activeTab === 'new_status_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('New order status', 'sms-assistent-wc'); ?></a>
                <a href="?page=<?php echo $this->menuSlug; ?>&tab=new_customer_options"
                   class="nav-tab <?php echo $activeTab === 'new_customer_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('New customer', 'sms-assistent-wc'); ?></a>
            </h2>

            <?php

            switch ($activeTab) {

                case 'new_status_options':
                {
                    $statuses = wc_get_order_statuses();
                    $activeSection = $this->sanitizeSection($_GET['section'], key($statuses));
                    echo '<form method="post" action="options.php?section=' . $activeSection . '">';

                    echo '<ul class="subsubsub">';
                    foreach ($statuses as $status => $status_name) {
                        echo '<li><a href="?page=' . $this->menuSlug . '&tab=new_status_options&section=' . $status . '" class="' . ($activeSection === $status ? 'current' : '') . '">' . $status_name . '</a> | </li>';
                    }
                    echo '</ul>';
                    echo '</br>';
                    echo '</br>';

                    settings_fields($this->newStatusOptionGroup);
                    do_settings_sections($this->newStatusPage);

                    break;
                }
                case 'new_customer_options':
                {
                    ?>
                    <form method="post" action="options.php">
                        <p style="color: red; font-weight: bold"><?php esc_html_e('Before importing users, you must disable sending to avoid mass notifications', 'sms-assistent-wc') ?></p>
                        <?php
                    settings_fields($this->newCustomerOptionGroup);
                    do_settings_sections($this->newCustomerPage);

                    break;
                }
                default: // general_options
                {
                    echo '<form method="post" action="options.php">';

                    settings_fields($this->generalOptionGroup);
                    do_settings_sections($this->generalPage);
                }
            }

            submit_button();

            echo '</form>';
            ?>

        </div><!-- /.wrap -->
        <?php
    }

#region GENERAL OPTIONS

    /**
     * Initializes the General Options by registering the Sections, Fields, and Settings.
     *
     * This function is registered with the 'admin_init' hook.
     */
    public function initializeGeneralOptions() {
        // Get the values of the setting we've registered with register_setting(). It used in the callback functions.
        $this->generalOptions = $this->getGeneralOptions();

        // Добавляем основную секцию
        add_settings_section(
            $this->generalSettingsSectionId,            // ID used to identify this section and with which to register options
            __('General settings', 'sms-assistent-wc'),         // Title to be displayed on the administration page
            [$this, 'generalOptionsCallback'],     // Callback used to render the description of the section
            $this->generalPage                          // Page on which to add this section of options
        );

        // Глобальные настройки
        add_settings_field($this->generalActive, __('Activate', 'sms-assistent-wc'), [$this, 'generalActiveCallback'], $this->generalPage, $this->generalSettingsSectionId, ['label_for' => $this->generalActive]);
        add_settings_field($this->generalUsername, __('Username', 'sms-assistent-wc'), [$this, 'generalUsernameCallback'], $this->generalPage, $this->generalSettingsSectionId, ['label_for' => $this->generalUsername]);
        add_settings_field($this->generalPassword, __('Password', 'sms-assistent-wc'), [$this, 'generalPasswordCallback'], $this->generalPage, $this->generalSettingsSectionId, ['label_for' => $this->generalPassword]);
        add_settings_field($this->generalToken, __('or Token', 'sms-assistent-wc'), [$this, 'generalTokenCallback'], $this->generalPage, $this->generalSettingsSectionId, ['label_for' => $this->generalToken]);
        add_settings_field($this->generalSender, __('Default Sender', 'sms-assistent-wc'), [$this, 'generalSenderCallback'], $this->generalPage, $this->generalSettingsSectionId, ['label_for' => $this->generalSender]);
        add_settings_field($this->generalBaseUrl, __('Base URL for API', 'sms-assistent-wc'), [$this, 'generalBaseUrlCallback'], $this->generalPage, $this->generalSettingsSectionId, ['label_for' => $this->generalBaseUrl]);

        // Finally, we register the fields with WordPress.
        /**
         * If you want to use the setting in the REST API (wp-json/wp/v2/settings),
         * you’ll need to call register_setting() on the rest_api_init action, in addition to the normal admin_init action.
         */
        $registerSettingArguments = [
            'type' => 'array',
            'description' => '',
            'sanitize_callback' => [$this, 'sanitizeOptionsCallback'],
            'show_in_rest' => false,
        ];
        register_setting($this->generalOptionGroup, $this->generalOptionName, $registerSettingArguments);
    }

    /**
     * Return the General options.
     *
     * @return array
     */
    public function getGeneralOptions() {
        if (isset($this->generalOptions)) {
            return $this->generalOptions;
        }

        $this->generalOptions = array_merge(
            $this->defaultGeneralOptions(),
            get_option($this->generalOptionName, [])
        );

        return $this->generalOptions;
    }

    /**
     * Provide default values for the General Options.
     *
     * @return array
     */
    private function defaultGeneralOptions() {
        return [
            $this->generalActive => false,
            $this->generalUsername => '',
            $this->generalPassword => '',
            $this->generalToken => '',
            $this->generalSender => '',
            $this->generalBaseUrl => '',
        ];
    }

    /**
     * This function provides a simple description for the General Options page.
     *
     * It's called from the initializeGeneralOptions function by being passed as a parameter
     * in the add_settings_section function.
     */
    public function generalOptionsCallback() {
        // Display the settings data for easier examination. Delete it, if you don't need it.
//        echo '<p>Display the settings as stored in the database:</p>';
//        $this->generalOptions = $this->getGeneralOptions();
//        var_dump($this->generalOptions);
//
//        echo '<p>' . esc_html__('General options.', 'sms-assistent-wc') . '</p>';
    }

    public function generalActiveCallback() {
        $this->renderCheckbox($this->generalActive, $this->generalOptionName, $this->generalOptions[$this->generalActive], __('Global Setting for activate module', 'sms-assistent-wc'));
    }

    public function generalUsernameCallback() {
        $this->renderText($this->generalUsername, $this->generalOptionName, $this->generalOptions[$this->generalUsername]);
    }

    public function generalPasswordCallback() {
        $this->renderText($this->generalPassword, $this->generalOptionName, $this->generalOptions[$this->generalPassword],
            sprintf('%s <a href="%s" target="_blank">%s</a>',
                __('Password for API. Generated in your personal account', 'sms-assistent-wc'),
                'https://userarea.sms-assistent.by/api_logs.php',
                __('SMS-Assistent', 'sms-assistent-wc')
            )
        );
    }

    public function generalTokenCallback() {
        $this->renderText($this->generalToken, $this->generalOptionName, $this->generalOptions[$this->generalToken]);
    }

    public function generalSenderCallback() {
        $this->renderText($this->generalSender, $this->generalOptionName, $this->generalOptions[$this->generalSender]);
    }

    public function generalBaseUrlCallback() {
        $this->renderText($this->generalBaseUrl, $this->generalOptionName, $this->generalOptions[$this->generalBaseUrl],
            sprintf('%s %s',
                __('If omitted, the default server address is used', 'sms-assistent-wc'),
                'https://userarea.sms-assistent.by/'
            )
        );
    }

    /**
     * Get General Active option.
     */
    public function getGeneralActive() {
        $this->generalOptions = $this->getGeneralOptions();
        return $this->generalOptions[$this->generalActive];
    }

    /**
     * Get General Username option.
     */
    public function getGeneralUsername() {
        $this->generalOptions = $this->getGeneralOptions();
        return $this->generalOptions[$this->generalUsername];
    }

    /**
     * Get General Password option.
     */
    public function getGeneralPassword() {
        $this->generalOptions = $this->getGeneralOptions();
        return $this->generalOptions[$this->generalPassword];
    }

    /**
     * Get General Token option.
     */
    public function getGeneralToken() {
        $this->generalOptions = $this->getGeneralOptions();
        return $this->generalOptions[$this->generalToken];
    }

    /**
     * Get General Sender option.
     */
    public function getGeneralSender() {
        $this->generalOptions = $this->getGeneralOptions();
        return $this->generalOptions[$this->generalSender];
    }

    /**
     * Get General BaseUrl option.
     */
    public function getGeneralBaseUrl() {
        $this->generalOptions = $this->getGeneralOptions();
        return $this->generalOptions[$this->generalBaseUrl];
    }

#endregion

#region NEW STATUS OPTIONS

    /**
     * Change the default name for options with section
     * @param   $section    string
     */
    public function setNewStatusSection($section) {
        $this->newStatusOptionName = $this->pluginSlug . '-new-status-' . $section;
    }

    /**
     * Initializes the plugins's new statuses options by registering the Sections, Fields, and Settings.
     *
     * This function is registered with the 'admin_init' hook.
     */
    public function initializeNewStatusOptions() {
        // Определяем, какая страница статуса открыта
        $statuses = wc_get_order_statuses();
        $activeSection = $this->sanitizeSection($_GET['section'], key($statuses));
        $this->setNewStatusSection($activeSection);

        $this->newStatusOptions = $this->getNewStatusOptions();

        add_settings_section($this->newStatusCustomerSettingsSectionId, __('Customer notification', 'sms-assistent-wc'), [$this, 'newStatusOptionsCallback'], $this->newStatusPage);
        add_settings_field($this->newStatusCustomerActive, __('Activate', 'sms-assistent-wc'), [$this, 'newStatusCustomerActiveCallback'], $this->newStatusPage, $this->newStatusCustomerSettingsSectionId, ['label_for' => $this->newStatusCustomerActive]);
        add_settings_field($this->newStatusCustomerTemplate, __('Template of SMS message', 'sms-assistent-wc'), [$this, 'newStatusCustomerTemplateCallback'], $this->newStatusPage, $this->newStatusCustomerSettingsSectionId, ['label_for' => $this->newStatusCustomerTemplate]);
        add_settings_section($this->newStatusManagerSettingsSectionId, __('Manager notification', 'sms-assistent-wc'), [$this, 'newStatusOptionsCallback'], $this->newStatusPage);
        add_settings_field($this->newStatusManagerActive, __('Activate', 'sms-assistent-wc'), [$this, 'newStatusManagerActiveCallback'], $this->newStatusPage, $this->newStatusManagerSettingsSectionId, ['label_for' => $this->newStatusManagerActive]);
        add_settings_field($this->newStatusManagerPhones, __('Phone numbers', 'sms-assistent-wc'), [$this, 'newStatusManagerPhonesCallback'], $this->newStatusPage, $this->newStatusManagerSettingsSectionId, ['label_for' => $this->newStatusManagerPhones]);
        add_settings_field($this->newStatusManagerTemplate, __('Template of SMS message', 'sms-assistent-wc'), [$this, 'newStatusManagerTemplateCallback'], $this->newStatusPage, $this->newStatusManagerSettingsSectionId, ['label_for' => $this->newStatusManagerTemplate]);

        $registerSettingArguments = [
            'type' => 'array',
            'description' => '',
            'sanitize_callback' => [$this, 'sanitizeOptionsCallback'],
            'show_in_rest' => false,
        ];
        register_setting($this->newStatusOptionGroup, $this->newStatusOptionName, $registerSettingArguments);
    }

    /**
     * Return the New Status options.
     *
     * @return array
     */
    public function getNewStatusOptions() {
        if (isset($this->newStatusOptions)) {
            return $this->newStatusOptions;
        }

        $this->newStatusOptions = array_merge(
            $this->defaultNewStatusOptions(),
            get_option($this->newStatusOptionName, [])
        );

        return $this->newStatusOptions;
    }

    /**
     * Provides default values for the New Status Options.
     *
     * @return array
     */
    private function defaultNewStatusOptions() {
        return [
            $this->newStatusCustomerActive => false,
            $this->newStatusCustomerTemplate => '',
            $this->newStatusManagerActive => false,
            $this->newStatusManagerPhones => '',
            $this->newStatusManagerTemplate => '',
        ];
    }

    /**
     * This function provides a simple description for the Input NewStatuses page.
     */
    public function newStatusOptionsCallback() {
        // Display the settings data for easier examination. Delete it, if you don't need it.
//        $this->newStatusOptions = $this->getNewStatusOptions();
//        echo '<p>Display the settings as stored in the database:</p>';
//        echo $this->newStatusOptionName;
//        var_dump($this->newStatusOptions);
//
//        echo '<p>' . esc_html__('Provides new status options.', 'sms-assistent-wc') . '</p>';
    }

    public function newStatusCustomerActiveCallback() {
        $this->renderCheckbox($this->newStatusCustomerActive, $this->newStatusOptionName, $this->newStatusOptions[$this->newStatusCustomerActive], __('Activates sending a message to the customer', 'sms-assistent-wc'));
    }

    /**
     * Get Help for new status message template
     *
     * @return string
     */
    private function getNewStatusMessageTemplateHelp() {
        return sprintf('%s:<br>
<strong>{store_name}</strong> - %s;<br>
<strong>{store_url}</strong> - %s;<br>
<strong>{order_id}</strong> - %s;<br>
<strong>{date_added}</strong> - %s;<br>
<strong>{payment_method}</strong> - %s;<br>
<strong>{payment_code}</strong> - %s;<br>
<strong>{email}</strong> - %s;<br>
<strong>{telephone}</strong> - %s;<br>
<strong>{firstname}</strong> - %s;<br>
<strong>{lastname}</strong> - %s;<br>
<strong>{total}</strong> - %s;<br>
<strong>{products_ids}</strong> - %s;<br>
<strong>{products_names}</strong> - %s;<br>
<strong>{products_names_prices}</strong> - %s.',
            __('Available tags', 'sms-assistent-wc'),
            __('Store Name', 'sms-assistent-wc'),
            __('Store URL', 'sms-assistent-wc'),
            __('Order number', 'sms-assistent-wc'),
            __('Date created', 'sms-assistent-wc'),
            __('Payment method', 'sms-assistent-wc'),
            __('Payment code', 'sms-assistent-wc'),
            __('Customer Email', 'sms-assistent-wc'),
            __('Customer Phone Number', 'sms-assistent-wc'),
            __('Customer Firstname', 'sms-assistent-wc'),
            __('Customer Lastname', 'sms-assistent-wc'),
            __('Order total', 'sms-assistent-wc'),
            __('Ordered product identifiers, separated by comma', 'sms-assistent-wc'),
            __('Ordered product names, separated by comma', 'sms-assistent-wc'),
            __('Ordered product names and prices, separated by comma', 'sms-assistent-wc')
        );
    }

    public function newStatusCustomerTemplateCallback() {
        $this->renderTextarea($this->newStatusCustomerTemplate, $this->newStatusOptionName, $this->newStatusOptions[$this->newStatusCustomerTemplate],
            $this->getNewStatusMessageTemplateHelp()
        );
    }

    public function newStatusManagerActiveCallback() {
        $this->renderCheckbox($this->newStatusManagerActive, $this->newStatusOptionName, $this->newStatusOptions[$this->newStatusManagerActive], __('Activates sending a message to the managers', 'sms-assistent-wc'));
    }

    public function newStatusManagerPhonesCallback() {
        $this->renderText($this->newStatusManagerPhones, $this->newStatusOptionName, $this->newStatusOptions[$this->newStatusManagerPhones], __('Phone numbers separated by ;', 'sms-assistent-wc'));
    }

    public function newStatusManagerTemplateCallback() {
        $this->renderTextarea($this->newStatusManagerTemplate, $this->newStatusOptionName, $this->newStatusOptions[$this->newStatusManagerTemplate],
            $this->getNewStatusMessageTemplateHelp()
        );
    }

    /**
     * Get New Status Customer Active Option
     *
     * @return string
     */
    public function getNewStatusCustomerActive() {
        $this->newStatusOptions = $this->getNewStatusOptions();
        return $this->newStatusOptions[$this->newStatusCustomerActive];
    }

    /**
     * Get New Status Customer Active Option
     *
     * @return string
     */
    public function getNewStatusCustomerTemplate() {
        $this->newStatusOptions = $this->getNewStatusOptions();
        return $this->newStatusOptions[$this->newStatusCustomerTemplate];
    }

    /**
     * Get New Status Customer Active Option
     *
     * @return string
     */
    public function getNewStatusManagerActive() {
        $this->newStatusOptions = $this->getNewStatusOptions();
        return $this->newStatusOptions[$this->newStatusManagerActive];
    }

    /**
     * Get New Status Customer Active Option
     *
     * @return string
     */
    public function getNewStatusManagerPhones() {
        $this->newStatusOptions = $this->getNewStatusOptions();
        return $this->newStatusOptions[$this->newStatusManagerPhones];
    }

    /**
     * Get New Status Customer Active Option
     *
     * @return string
     */
    public function getNewStatusManagerTemplate() {
        $this->newStatusOptions = $this->getNewStatusOptions();
        return $this->newStatusOptions[$this->newStatusManagerTemplate];
    }

#endregion

#region NEW CUSTOMER OPTIONS

    /**
     * Initializes the plugins's new customer options by registering the Sections, Fields, and Settings.
     *
     * This function is registered with the 'admin_init' hook.
     */
    public function initializeNewCustomerOptions() {
        // Определяем, какая страница статуса открыта
        $this->newCustomerOptions = $this->getNewCustomerOptions();

        add_settings_section($this->newCustomerCustomerSettingsSectionId, __('Customer notification', 'sms-assistent-wc'), [$this, 'newCustomerOptionsCallback'], $this->newCustomerPage);
        add_settings_field($this->newCustomerCustomerActive, __('Activate', 'sms-assistent-wc'), [$this, 'newCustomerCustomerActiveCallback'], $this->newCustomerPage, $this->newCustomerCustomerSettingsSectionId, ['label_for' => $this->newCustomerCustomerActive]);
        add_settings_field($this->newCustomerCustomerTemplate, __('Template of SMS message', 'sms-assistent-wc'), [$this, 'newCustomerCustomerTemplateCallback'], $this->newCustomerPage, $this->newCustomerCustomerSettingsSectionId, ['label_for' => $this->newCustomerCustomerTemplate]);
        add_settings_section($this->newCustomerManagerSettingsSectionId, __('Manager notification', 'sms-assistent-wc'), [$this, 'newCustomerOptionsCallback'], $this->newCustomerPage);
        add_settings_field($this->newCustomerManagerActive, __('Activate', 'sms-assistent-wc'), [$this, 'newCustomerManagerActiveCallback'], $this->newCustomerPage, $this->newCustomerManagerSettingsSectionId, ['label_for' => $this->newCustomerManagerActive]);
        add_settings_field($this->newCustomerManagerPhones, __('Phone numbers', 'sms-assistent-wc'), [$this, 'newCustomerManagerPhonesCallback'], $this->newCustomerPage, $this->newCustomerManagerSettingsSectionId, ['label_for' => $this->newCustomerManagerPhones]);
        add_settings_field($this->newCustomerManagerTemplate, __('Template of SMS message', 'sms-assistent-wc'), [$this, 'newCustomerManagerTemplateCallback'], $this->newCustomerPage, $this->newCustomerManagerSettingsSectionId, ['label_for' => $this->newCustomerManagerTemplate]);
        $registerSettingArguments = [
            'type' => 'array',
            'description' => '',
            'sanitize_callback' => [$this, 'sanitizeOptionsCallback'],
            'show_in_rest' => false,
        ];
        register_setting($this->newCustomerOptionGroup, $this->newCustomerOptionName, $registerSettingArguments);
    }

    /**
     * Return the New Customer options.
     *
     * @return array
     */
    public function getNewCustomerOptions() {
        if (isset($this->newCustomerOptions)) {
            return $this->newCustomerOptions;
        }

        $this->newCustomerOptions = array_merge(
            $this->defaultNewCustomerOptions(),
            get_option($this->newCustomerOptionName, [])
        );

        return $this->newCustomerOptions;
    }

    /**
     * Provides default values for the New Status Options.
     *
     * @return array
     */
    private function defaultNewCustomerOptions() {
        return [
            $this->newCustomerCustomerActive => false,
            $this->newCustomerCustomerTemplate => '',
            $this->newCustomerManagerActive => false,
            $this->newCustomerManagerPhones => '',
            $this->newCustomerManagerTemplate => '',
        ];
    }

    /**
     * This function provides a simple description for the New Customer settings page.
     */
    public function newCustomerOptionsCallback() {
        // Display the settings data for easier examination. Delete it, if you don't need it.
//        $this->newCustomerOptions = $this->getNewCustomerOptions();
//        echo '<p>Display the settings as stored in the database:</p>';
//        echo $this->newCustomerOptionName;
//        var_dump($this->newCustomerOptions);
//
//        echo '<p>' . esc_html__('Provides new customer options.', 'sms-assistent-wc') . '</p>';
    }

    public function newCustomerCustomerActiveCallback() {
        $this->renderCheckbox($this->newCustomerCustomerActive, $this->newCustomerOptionName, $this->newCustomerOptions[$this->newCustomerCustomerActive], __('Activates sending a message to the customer', 'sms-assistent-wc'));
    }

    /**
     * Get Help for new customer message template
     *
     * @return string
     */
    private function getNewCustomerMessageTemplateHelp() {
        return sprintf('%s:<br>
<strong>{customer_id}</strong> - %s;<br>
<strong>{email}</strong> - %s;<br>
<strong>{firstname}</strong> - %s;<br>
<strong>{lastname}</strong> - %s;<br>
<strong>{telephone}</strong> - %s;<br>
<strong>{store_name}</strong> - %s;<br>
<strong>{store_url}</strong> - %s.<br>',
            __('Available tags', 'sms-assistent-wc'),
            __('Customer identifier', 'sms-assistent-wc'),
            __('Customer Email', 'sms-assistent-wc'),
            __('Customer Firstname', 'sms-assistent-wc'),
            __('Customer Lastname', 'sms-assistent-wc'),
            __('Customer Phone Number', 'sms-assistent-wc'),
            __('Store Name', 'sms-assistent-wc'),
            __('Store URL', 'sms-assistent-wc')
        );
    }

    public function newCustomerCustomerTemplateCallback() {
        $this->renderTextarea($this->newCustomerCustomerTemplate, $this->newCustomerOptionName, $this->newCustomerOptions[$this->newCustomerCustomerTemplate],
            $this->getNewCustomerMessageTemplateHelp()
        );
    }

    public function newCustomerManagerActiveCallback() {
        $this->renderCheckbox($this->newCustomerManagerActive, $this->newCustomerOptionName, $this->newCustomerOptions[$this->newCustomerManagerActive], __('Activates sending a message to the managers', 'sms-assistent-wc'));
    }

    public function newCustomerManagerPhonesCallback() {
        $this->renderText($this->newCustomerManagerPhones, $this->newCustomerOptionName, $this->newCustomerOptions[$this->newCustomerManagerPhones], __('Phone numbers separated by ;', 'sms-assistent-wc'));
    }

    public function newCustomerManagerTemplateCallback() {
        $this->renderTextarea($this->newCustomerManagerTemplate, $this->newCustomerOptionName, $this->newCustomerOptions[$this->newCustomerManagerTemplate],
            $this->getNewCustomerMessageTemplateHelp()
        );
    }

    /**
     * Get New Customer Customer Active Option
     *
     * @return string
     */
    public function getNewCustomerCustomerActive() {
        $this->newCustomerOptions = $this->getNewCustomerOptions();
        return $this->newCustomerOptions[$this->newCustomerCustomerActive];
    }

    /**
     * Get New Customer Customer Active Option
     *
     * @return string
     */
    public function getNewCustomerCustomerTemplate() {
        $this->newCustomerOptions = $this->getNewCustomerOptions();
        return $this->newCustomerOptions[$this->newCustomerCustomerTemplate];
    }

    /**
     * Get New Customer Customer Active Option
     *
     * @return string
     */
    public function getNewCustomerManagerActive() {
        $this->newCustomerOptions = $this->getNewCustomerOptions();
        return $this->newCustomerOptions[$this->newCustomerManagerActive];
    }

    /**
     * Get New Customer Customer Active Option
     *
     * @return string
     */
    public function getNewCustomerManagerPhones() {
        $this->newCustomerOptions = $this->getNewCustomerOptions();
        return $this->newCustomerOptions[$this->newCustomerManagerPhones];
    }

    /**
     * Get New Customer Customer Active Option
     *
     * @return string
     */
    public function getNewCustomerManagerTemplate() {
        $this->newCustomerOptions = $this->getNewCustomerOptions();
        return $this->newCustomerOptions[$this->newCustomerManagerTemplate];
    }

#endregion

    /**
     * Sanitizes the option's value.
     *
     * Based on:
     * @link https://divpusher.com/blog/wordpress-customizer-sanitization-new-order-statuses/
     *
     * @since             1.0.0
     * @package           PluginName
     *
     * @param   $input  array|null  The unsanitized collection of options.
     * @return          array       The collection of sanitized values.
     */
    public function sanitizeOptionsCallback($input = NULL) {
        // Define the array for the sanitized options
        $output = [];

        // Loop through each of the incoming options
        foreach ($input as $key => $value) {
            // Sanitize Checkbox. Input must be boolean.
            if ($this->endsWith($key, self::CHECKBOX_SUFFIX)) {
                $output[$key] = isset($input[$key]);
            } // Sanitize Radio button. Input must be a slug: [a-z,0-9,-,_].
            else if ($this->endsWith($key, self::RADIO_SUFFIX)) {
                $output[$key] = isset($input[$key]) ? sanitize_key($input[$key]) : '';
            } // Sanitize Select aka Dropdown. Input must be a slug: [a-z,0-9,-,_].
            else if ($this->endsWith($key, self::SELECT_SUFFIX)) {
                $output[$key] = isset($input[$key]) ? sanitize_key($input[$key]) : '';
            } // Sanitize Text
            else if ($this->endsWith($key, self::TEXT_SUFFIX)) {
                $output[$key] = isset($input[$key]) ? sanitize_text_field($input[$key]) : '';
            } // Sanitize Textarea
            else if ($this->endsWith($key, self::TEXTAREA_SUFFIX)) {
                $output[$key] = isset($input[$key]) ? sanitize_textarea_field($input[$key]) : '';
            } // Edge cases, fallback to default. Input must be Text.
            else {
                $output[$key] = isset($input[$key]) ? sanitize_text_field($input[$key]) : '';
            }
        }

        /**
         * Settings errors should be added inside the $sanitize_callback function.
         * NewStatus: add_settings_error($this->pluginSlug, $this->pluginSlug . '-message', __('Error.'), 'error');
         */

        // Return the array processing any additional functions filtered by this action
        return $output;
    }

    /**
     * Render checkbox
     *
     * @param $id       string
     * @param $group    string
     * @param $value    string
     * @param $help     string
     */
    private function renderCheckbox($id, $group, $value, $help = '') {
        printf('<input type="checkbox" id="%s" name="%s[%s]" value="1" %s />', $id, $group, $id, checked($value, true, false));
        if ($help !== '') {
            echo '&nbsp;';
            printf('<label for="%s">%s</label>', $id, $help);
        }
    }

    /**
     * Render text
     *
     * @param $id       string
     * @param $group    string
     * @param $value    string
     * @param $help     string
     */
    private function renderText($id, $group, $value, $help = '') {
        printf('<input type="text" id="%s" name="%s[%s]" value="%s" />', $id, $group, $id, esc_attr($value));
        if ($help !== '') {
            printf('<p class="description" id="%s">%s</p>', $id . '-help', $help);
        }
    }

    /**
     * Render textarea
     *
     * @param $id       string
     * @param $group    string
     * @param $value    string
     * @param $help     string
     */
    private function renderTextarea($id, $group, $value, $help = '') {
        printf('<textarea id="%s" name="%s[%s]" rows="5" cols="50">%s</textarea>', $id, $group, $id, esc_textarea($value));
        if ($help !== '') {
            printf('<p class="description" id="%s">%s</p>', $id . '-help', $help);
        }
    }

    /**
     * Determine if a string ends with another string.
     *
     * @param   $haystack   string  Base string.
     * @param   $needle     string  The searched value.
     *
     * @return boolean If the string ends with the another string return false, otherwise true
     */
    private function endsWith($haystack, $needle) {
        $haystackLength = strlen($haystack);
        $needleLength = strlen($needle);

        if ($needleLength > $haystackLength) {
            return false;
        }

        return substr_compare($haystack, $needle, -$needleLength, $needleLength) === 0;
    }
}
