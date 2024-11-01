<?php

use SmsAssistent\Includes\sms_assistent as Client;
use SmsAssistent\Admin\Sms_Assistent_Wc_Settings as Settings;

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Sms_Assistent_Wc
 * @subpackage Sms_Assistent_Wc/public
 * @author     Anarchy <info@koshelev.ml>
 */
class Sms_Assistent_Wc_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $sms_assistent_wc The ID of this plugin.
     */
    private $sms_assistent_wc;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Plugin settings
     *
     * @since   1.0.0
     * @access  private
     * @var     Settings $client
     */
    private $settings;

    /**
     * SMS-assistent API client
     *
     * @since   1.0.0
     * @access  private
     * @var     Client $client
     */
    private $client;

    /**
     * SMS-assistent Logger
     *
     * @since   1.0.0
     * @access  private
     * @var     WC_Logger $client
     */
    private $logger;

    /**
     * Initialize the class and set its properties.
     *
     * @param   string    $sms_assistent_wc The name of the plugin.
     * @param   string    $version The version of this plugin.
     * @param   Settings  $settings Plugin settings
     * @since   1.0.0
     */
    public function __construct($sms_assistent_wc, $version, $settings) {

        $this->sms_assistent_wc = $sms_assistent_wc;
        $this->version = $version;
        $this->settings = $settings;

        $this->createClient();

    }

    /**
     * Create default API client with base settings
     */
    private function createClient() {
        $this->client = new Client(
            $this->settings->getGeneralUsername(),
            $this->settings->getGeneralPassword(),
            $this->settings->getGeneralToken()
        );

        if ($this->settings->getGeneralBaseUrl() !== '') {
            $this->client->setUrl($this->settings->getGeneralBaseUrl());
        }
    }

    /**
     * Create or get logger
     *
     * @return WC_Logger
     */
    private function getLogger() {
        if (isset($this->logger)) {
            return $this->logger;
        }

        $this->logger = $logger = wc_get_logger();

        return $this->logger;
    }

    /**
     * Add log entry
     *
     * @param string $message
     * @param string $type
     */
    private function log($message, $type) {
        $context = ['source' => $this->sms_assistent_wc];
        $this->getLogger()->log($type, $message, $context);
    }

    /**
     * Format the price with a currency symbol
     * Strip all html tags and convert HTML entities
     *
     * @param string $price
     * @param string $currency
     *
     * @return string
     */
    private function stripPrice($price, $currency) {
        $priceFormatted = wc_price($price, array('currency' => $currency));
        return html_entity_decode(strip_tags($priceFormatted));
    }

    /**
     * Get current blog data
     *
     * @return array
     */
    private function getBlogData() {
        $blogData = [
            'name' => '',
            'url' => '',
        ];

        if (is_multisite()) {
            global $blog_id;
            $current_blog_details = get_blog_details( array( 'blog_id' => $blog_id ) );
            $blogData['name'] = $current_blog_details->blogname;
            $blogData['url'] =  $current_blog_details->siteurl;
        } else {
            $blogData['name'] = get_option('blogname');
            $blogData['url'] = get_option('siteurl');
        }

        return $blogData;
    }

    /**
     * Prepare SMS message for send by order data
     *
     * @param string    $template
     * @param WC_Order  $order
     * @param array     $items
     *
     * @return string
     */
    private function prepareMessageByOrderData($template, $order, $items) {
        $products_ids = '';
        $products_names = '';
        $products_names_prices = '';

        /* @var WC_Order_Item $item */
        foreach ($items as $item) {
            $products_ids .= $item->get_id() . ',';
            $products_names .= $item->get_name() . ',';
            $products_names_prices .= $item->get_name() . '(' . $this->stripPrice($item->get_total(), $order->get_currency()) . ')' . ',';
        }
        $products_ids = substr($products_ids, 0, -1);
        $products_names = substr($products_names, 0, -1);
        $products_names_prices = substr($products_names_prices, 0, -1);

        $blog = $this->getBlogData();

        $findReplace = array(
            '{store_name}'              => $blog['name'],
            '{store_url}'               => $blog['url'],
            '{order_id}'                => $order->get_id(),
            '{date_added}'              => $order->get_date_created()->format('d-m-Y H:i'),
            '{payment_method}'          => $order->get_payment_method_title(),
            '{payment_code}'            => $order->get_payment_method(),
            '{email}'                   => $order->get_billing_email(),
            '{telephone}'               => $order->get_billing_phone(),
            '{firstname}'               => $order->get_billing_first_name(),
            '{lastname}'                => $order->get_billing_last_name(),
            '{total}'                   => $this->stripPrice($order->get_total(), $order->get_currency()),
            '{products_ids}'            => $products_ids,
            '{products_names}'          => $products_names,
            '{products_names_prices}'   => $products_names_prices
        );

        return str_replace(array_keys($findReplace), array_values($findReplace), $template);
    }

    /**
     * Prepare SMS message for send by customer data
     *
     * @param string $template
     * @param WC_Customer $customer
     *
     * @return string
     */
    private function prepareMessageByCustomerData($template, $customer) {
        $blog = $this->getBlogData();

        $findReplace = array(
            '{store_name}'              => $blog['name'],
            '{store_url}'               => $blog['url'],
            '{customer_id}'             => $customer->get_id(),
            '{email}'                   => $customer->get_email(),
            '{firstname}'               => $customer->get_first_name(),
            '{lastname}'                => $customer->get_last_name(),
            '{telephone}'               => $customer->get_billing_phone(),
        );

        return str_replace(array_keys($findReplace), array_values($findReplace), $template);
    }

    /**
     * Register all the hooks of this class.
     *
     * @since   1.0.0
     */
    public function initializeHooks() {
        add_action('woocommerce_created_customer', [$this, 'afterCustomerRegistration'], 10, 1);
        add_action('woocommerce_order_status_changed', [$this, 'afterOrderStatusChanged'], 10, 3);
    }

    /**
     * This function introduces callback after new customer created.
     *
     * @param int $customerId
     */
    public function afterCustomerRegistration($customerId) {

        $this->log('Активизирован Webhook после регистрации пользователя', 'info');

        if (!$this->settings->getGeneralActive()) {
            $this->log('Глобальная настройка отключена', 'info');
            return;
        }

        try {
            $customer = new WC_Customer($customerId);
        } catch (Exception $e) {
            $this->log('Ошибка при получении данных пользователя', 'info');
            $this->log($e, 'critical');
            return;
        }

        if ($this->settings->getNewCustomerCustomerActive()) {
            $this->log('Отправка сообщения пользователю: Включена', 'info');
            $phone = $customer->get_billing_phone();
            if ($phone !== '') {
                $message = $this->prepareMessageByCustomerData($this->settings->getNewStatusCustomerTemplate(), $customer);
                $this->log('Попытка отправки сообщения', 'info');
                $result = $this->client->sendSms($this->settings->getGeneralSender(), $phone, $message);
                $this->log(print_r($result, true), 'debug');
            } else {
                $this->log('Отсутствует телефон, отправка невозможна', 'info');
            }
        } else {
            $this->log('Отправка сообщения пользователю: Отключена', 'info');
        }

        if ($this->settings->getNewCustomerManagerActive()) {
            $this->log('Отправка сообщения менеджерам: Включена', 'info');
            $phones = explode(';', $this->settings->getNewCustomerManagerPhones());

            if (count($phones) > 0) {
                $message = $this->prepareMessageByCustomerData($this->settings->getNewCustomerManagerTemplate(), $customer);

                $this->log('Попытка отправки сообщения(ий)', 'info');
                if (count($phones) === 1) {
                    $result = $this->client->sendSms($this->settings->getGeneralSender(), $phones[0], $message);
                } else {
                    $result = $this->client->sendSms($this->settings->getGeneralSender(), $phones, $message);
                }
                $this->log(print_r($result, true), 'debug');

//                $this->addSendedLog('order', $order_info['order_id'], $order_status_id, 'admin');
            } else {
                $this->log('Не указаны номера телефонов для отправки', 'info');
            }
        } else {
            $this->log('Отправка сообщения менеджерам: Отключена', 'info');
        }

    }

    /**
     * This function introduces callback after order change status.
     *
     * @param int $orderId
     * @param string $orderStatusFrom
     * @param string $orderStatusTo
     */
    public function afterOrderStatusChanged($orderId, $orderStatusFrom, $orderStatusTo) {

        $this->log('Активизирован Webhook после изменения статуса заказа', 'info');

        if (!$this->settings->getGeneralActive()) {
            $this->log('Глобальная настройка отключена', 'info');
            return;
        }

        $statusTo = 'wc-' . $orderStatusTo;
        $this->settings->setNewStatusSection($statusTo);

        $order = wc_get_order($orderId);
        $items = $order->get_items('line_item');
        $statuses = wc_get_order_statuses();
        $this->log('Новый статус "' . wc_get_order_status_name($statusTo) . '" (' . $statusTo . ')' , 'info');

        if ($this->settings->getNewStatusCustomerActive()) {
            $this->log('Отправка сообщения пользователю: Включена', 'info');

            $phone = $order->get_billing_phone();
            if ($phone !== '') {
                $message = $this->prepareMessageByOrderData($this->settings->getNewStatusCustomerTemplate(), $order, $items);
                $this->log('Попытка отправки сообщения', 'info');
                $result = $this->client->sendSms($this->settings->getGeneralSender(), $phone, $message);
                $this->log(print_r($result, true), 'debug');
            } else {
                $this->log('Отсутствует телефон, отправка невозможна', 'info');
            }
        } else {
            $this->log('Отправка сообщения пользователю: Отключена', 'info');
        }

        if ($this->settings->getNewStatusManagerActive()) {
            $this->log('Отправка сообщения менеджерам: Включена', 'info');

            $phones = explode(';', $this->settings->getNewStatusManagerPhones());

            if (count($phones) > 0) {
                $message = $this->prepareMessageByOrderData($this->settings->getNewStatusManagerTemplate(), $order, $items);

                $this->log('Попытка отправки сообщения(ий)', 'info');
                if (count($phones) === 1) {
                    $result = $this->client->sendSms($this->settings->getGeneralSender(), $phones[0], $message);
                } else {
                    $result = $this->client->sendSms($this->settings->getGeneralSender(), $phones, $message);
                }
                $this->log(print_r($result, true), 'debug');

//                $this->addSendedLog('order', $order_info['order_id'], $order_status_id, 'admin');
            } else {
                $this->log('Не указаны номера телефонов для отправки', 'info');
            }

        } else {
            $this->log('Отправка сообщения менеджерам: Отключена', 'info');
        }

    }

}
