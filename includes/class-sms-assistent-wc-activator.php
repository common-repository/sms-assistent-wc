<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sms_Assistent_Wc
 * @subpackage Sms_Assistent_Wc/includes
 * @author     Anarchy <info@koshelev.ml>
 */
class Sms_Assistent_Wc_Activator {

    /**
     * Define the plugins that our plugin requires to function.
     * The key is the plugin name, the value is the plugin file path.
     *
     * @since 1.0.0
     * @const string[]
     */
    private static $requiredPlugins = [
        'WooCommerce' => 'woocommerce/woocommerce.php'
    ];

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
	    // Permission check
        if (!current_user_can('activate_plugins'))
        {
            deactivate_plugins(plugin_basename(__FILE__));

            // Localization class hasn't been loaded yet.
            wp_die('У вас нет разрешения для активации плагинов!');
        }

        // Check dependencies
        self::checkDependencies();
	}

    /**
     * Check whether the required plugins are active.
     *
     * @since      1.0.0
     */
    private static function checkDependencies()
    {
        foreach (self::$requiredPlugins as $pluginName => $pluginFilePath)
        {
            if (!is_plugin_active($pluginFilePath))
            {
                // Deactivate the plugin.
                deactivate_plugins(plugin_basename(__FILE__));

                wp_die("Для использования данного плагина необходимо активировать родительский плагин {$pluginName}!");
            }
        }
    }

}
