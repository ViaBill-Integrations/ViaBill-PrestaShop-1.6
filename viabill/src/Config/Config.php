<?php
/**
* NOTICE OF LICENSE
*
* @author    Written for or by ViaBill
* @copyright Copyright (c) Viabill
* @license   Addons PrestaShop license limitation
* @see       /LICENSE
*
*
*/

namespace ViaBill\Config;

/**
 * Class Config
 *
 * @package ViaBill\Config
 */
class Config
{
    /**
     * Development mode setting
     */
    const DEV_MODE = false;

    const VIABILL_TEST_MODE = 'VB_TEST_MODE';

    const VIABILL_LOGO_DISPLAY_IN_CHECKOUT = 'VB_LOGO_DISPLAY_IN_CHECKOUT';

    const BASE_URL_TEST = 'https://secure-test.viabill.com';
    const BASE_URL_LIVE = 'https://secure.viabill.com';

    const API_KEY = 'VB_API_KEY';
    const API_SECRET = 'VB_API_SECRET';
    const API_TAGS_SCRIPT = 'VB_TAGS_SCRIPT';

//    price tag all available views
    const DATA_VIEW = 'product';
    const DATA_LIST = 'list';
    const DATA_BASKET = 'basket';
    const DATA_PAYMENT = 'payment';

//    register request
    const REGISTER_REQUEST_AFFILIATE = 'PRESTASHOP';

//    settings controller
    const SETTINGS_PRICETAG_SETTINGS_SECTION = 'VB_PRICETAG_SETTINGS';
    const SETTINGS_GENERAL_CONFIGURATION_SECTION = 'VB_GENERAL_CONFIGURATION';
    const SETTINGS_PAYMENT_CAPTURE_SECTION = 'VB_PAYMENT_CAPTURE';
    const SETTINGS_MY_VIABILL_SECTION = 'VB_MY_VIABILL';
    const SETTINGS_DEBUG_SECTION='VB_DEBUG_INFO';

    const ENABLE_PRICE_TAG_ON_PRODUCT_PAGE = 'VB_ENABLE_ON_PRODUCT_PAGE';
    const ENABLE_PRICE_TAG_ON_CART_SUMMARY = 'VB_ENABLE_ON_CART_SUMMARY';
    const ENABLE_PRICE_TAG_ON_PAYMENT_SELECTION = 'VB_ENABLE_ON_PAYMENT_SELECTION';

    const SINGLE_ACTION_CAPTURE_CONF_MESSAGE = 'VB_SINGLE_ACTION_CAPTURE_CONF';
    const BULK_ACTION_CAPTURE_CONF_MESSAGE = 'VB_BULK_ACTION_CAPTURE_CONF';
    const SINGLE_ACTION_REFUND_CONF_MESSAGE = 'VB_SINGLE_ACTION_REFUND_CONF';
    const BULK_ACTION_REFUND_CONF_MESSAGE = 'VB_BULK_ACTION_REFUND_CONF';
    const SINGLE_ACTION_CANCEL_CONF_MESSAGE = 'VB_SINGLE_ACTION_CANCEL_CONF';
    const BULK_ACTION_CANCEL_CONF_MESSAGE = 'VB_BULK_ACTION_CANCEL_CONF';

    const ENABLE_AUTO_PAYMENT_CAPTURE = 'VB_ENABLE_AUTO_PAYMENT_CAPTURE';
    const CAPTURE_ORDER_STATUS_MULTISELECT = 'VB_CAPTURE_ORDER_STATUS_MULTI';

    const PRICETAG_SETTINGS_INFO_BLOCK_FIELD = 'VB_PRICE_TAG_INFO_BLOCK';
    const MY_VIABILL_INFO_BLOCK_FIELD = 'VB_MY_VIABILL_INFO_BLOCK';

    const DYNAMIC_PRICE_CART_SELECTOR = '#page|.dynamic-price-tag-selector';
    const DYNAMIC_PRICE_CART_TRIGGER = 'dynamic-price-tag-trigger';
    const DYNAMIC_PRICE_PRODUCT_SELECTOR = '#product|.dynamic-price-tag-selector';
    const DYNAMIC_PRICE_PRODUCT_TRIGGER = 'dynamic-price-tag-trigger';

    const ENABLE_DEBUG = 'VB_ENABLE_DEBUG';
    const MODULE_INFO_FIELD = 'VB_MODULE_INFO_BLOCK';

    // payment statuses
    const PAYMENT_PENDING = 'VB_PAYMENT_PENDING';
    const PAYMENT_ACCEPTED = 'VB_PAYMENT_ACCEPTED';
    const PAYMENT_COMPLETED = 'VB_PAYMENT_COMPLETED';
    const PAYMENT_CANCELED = 'VB_PAYMENT_CANCELED';
    const PAYMENT_REFUNDED = 'VB_PAYMENT_REFUNDED';
    const PAYMENT_ERROR = 'PS_OS_ERROR';

    const CALLBACK_STATUS_SUCCESS = 'APPROVED';
    const CALLBACK_STATUS_CANCEL = 'CANCELLED';
    const CALLBACK_STATUS_REJECTED = 'REJECTED';

    const ORDER_STATUS_CANCELLED = 'CANCELLED';
    const ORDER_STATUS_APPROVED = 'APPROVED';
    const ORDER_STATUS_CAPTURED = 'CAPTURED';

    const DKK_ISO_CODE = 'DKK';
    const USD_ISO_CODE = 'USD';
    const EUR_ISO_CODE = 'EUR';

    const DK_COUNTRY_ISO_CODE = 'DK';
    const US_COUNTRY_ISO_CODE = 'US';
    const ES_COUNTRY_ISO_CODE = 'ES';

    //Terms And Conditions
    const TERMS_AND_CONDITIONS_LINK = 'https://viabill.com/trade-terms/';

    /**
     * Formats Country Code For Terms & Conditions Link
     *
     * @return string
     */
    public static function formatCountryCodeForTCLink($countryCode)
    {
        return '#' . \Tools::strtoupper($countryCode);
    }

    /**
     * Gets Country ISO by Currency ISO
     *
     * @param $currencyISO
     *
     * @return string
     */
    public static function getCountryISOCodeByCurrencyISO($currencyISO)
    {
        switch ($currencyISO) {
            case self::DKK_ISO_CODE:
                return self::DK_COUNTRY_ISO_CODE;
            case self::USD_ISO_CODE:
                return self::US_COUNTRY_ISO_CODE;
            case self::EUR_ISO_CODE:
                return self::ES_COUNTRY_ISO_CODE;
            default:
                return '';
        }
    }

    /**
     * Gets Norway Iso Exceptions Array.
     *
     * @return array
     */
    public static function getNorwayIsoExceptionsArray()
    {
        return array(
            'NO',
            'NN',
            'NB'
        );
    }

    /**
     * Gets Tag Controller.
     *
     * @return array
     */
    public static function getTagsControllers()
    {
        return array(
            'product',
            'order',
            'order-opc',
            'cart'
        );
    }

    /**
     * Gets Tags View By Controller Name.
     *
     * @param string $controllerName
     *
     * @return string
     */
    public static function getTagsViewByController($controllerName)
    {
        switch ($controllerName) {
            case 'product':
                return self::DATA_VIEW;
            case 'order-opc':
                return self::DATA_BASKET;
            case 'order':
                return self::DATA_BASKET;
            default:
                return '';
        }
    }

    /**
     * Gets Forgot Password URL In BO Login Page.
     *
     * @param string $isoCode
     *
     * @return string
     */
    public static function getLoginForgotPassUrl($isoCode)
    {
        if (self::DEV_MODE) {
            return 'https://my-test.viabill.com/'.$isoCode.'/#/auth/forgot';
        }

        return 'https://viabill.com/auth/forgot/?lang='.$isoCode;
    }

    /**
     * Gets order Statuses.
     *
     * @return array
     */
    public static function getOrderStatuses()
    {
        return array(
            self::PAYMENT_PENDING,
            self::PAYMENT_ACCEPTED,
            self::PAYMENT_COMPLETED,
            self::PAYMENT_CANCELED,
            self::PAYMENT_REFUNDED
        );
    }

    /**
     * Checks If Testing Environment Is On.
     *
     * @return bool
     */
    public function isTestingEnvironment()
    {
        return (bool) \Configuration::get(self::VIABILL_TEST_MODE);
    }

    /**
     * Checks If Live Environment Is On.
     *
     * @return bool
     */
    public function isLiveEnvironment()
    {
        return (bool) \Configuration::get(self::VIABILL_TEST_MODE);
    }

    /**
     * Gets Base URL For Testing Or Live Environments.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if (self::DEV_MODE) {
            return self::BASE_URL_TEST;
        }

        return self::BASE_URL_LIVE;
    }

    /**
     * Checks If User Is Logged In.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return \Configuration::get(self::API_KEY) &&
            \Configuration::get(self::API_SECRET) &&
            \Configuration::get(self::API_TAGS_SCRIPT);
    }

    /**
     * Checks If Debugging Is On.
     *
     * @return bool
     */
    public function isDebug()
    {
        return (bool) \Configuration::get(self::ENABLE_DEBUG);
    }
}
