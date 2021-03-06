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

use ViaBill\Config\Config;
use ViaBill\Util\DebugLog;
use ViaBill\Controller\AbstractAdminController as ModuleAdminController;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

/**
 * ViaBill Settings Controller Class.
 *
 * Class AdminViaBillSettingsController
 */
class AdminViaBillSettingsController extends ModuleAdminController
{
    /**
     * Contact email address
     */
    const VIABILL_TECH_SUPPORT_EMAIL = 'tech@viabill.com';

    /**
     * AdminViaBillSettingsController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->override_folder = 'field-option-settings/';
        $this->tpl_folder = 'field-option-settings/';
    }

    /**
     * Sets Success Messages To Cookie And Unset Them After Print.
     */
    public function init()
    {
        if (isset($this->context->cookie->authSuccessMessage)) {
            $this->confirmations[] = $this->context->cookie->authSuccessMessage;
            unset($this->context->cookie->authSuccessMessage);
        };

        if (isset($this->context->cookie->saveSuccessMessage)) {
            $this->confirmations[] = $this->context->cookie->saveSuccessMessage;
            unset($this->context->cookie->saveSuccessMessage);
        };

        parent::init();

        $this->initOptionsCustomVariables();
        $this->initOptions();
    }

    /**
     * Adding Warning When All PriceTag Settings Are Off.
     * Adding Successful Settings Update Message.
     *
     * @return bool|void
     * @throws Exception
     */
    public function postProcess()
    {
        parent::postProcess();

        if (Tools::isSubmit('submitOptions' . $this->table)) {
            $orderStatusMultiselect = Tools::getIsset('order_status_multiselect') ?
                Tools::getValue('order_status_multiselect') :
                array();

            /** @var \ViaBill\Service\Order\OrderStatusService $orderStatusService */
            $orderStatusService = $this->module->getContainer()->get('service.order.orderStatus');
            $orderStatusService->setEncodedCaptureMultiselectOrderStatuses($orderStatusMultiselect);

            $this->context->cookie->saveSuccessMessage = $this->l('The settings have been successfully updated.');
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminViaBillSettings'));
        }
    }

    /**
     * Adds CSS And JS Files To ViaBill Settings Controller.
     *
     * @param bool $isNewTheme
     *
     * @throws Exception
     */
    public function setMedia()
    {
        parent::setMedia();

        /**
         * @var Config $config
         */
        $config = $this->module->getContainer()->get('config');

        if ($config->isLoggedIn()) {
            $this->addJS($this->module->getLocalPath() . '/views/js/admin/settings.js');
            $this->addCSS($this->module->getPathUri() . '/views/css/admin/tab-hide.css');
            $this->addCSS($this->module->getPathUri() . '/views/css/admin/info-block.css');
            $this->addCSS($this->module->getPathUri() . '/views/css/admin/settings.css');
            if (!$this->module->isPS16()) {
                $this->addJS($this->module->getPathUri() . '/views/js/admin/settings15.js');
                $this->addCSS($this->module->getPathUri() . '/views/css/admin/settings15.css');
            }
        }
    }

    /**
     * Init ViaBill Settings Controller Options Variables.
     */
    private function initOptionsCustomVariables()
    {
        /** @var \ViaBill\Service\Order\OrderStatusService $orderStatusService */
        $orderStatusService = $this->module->getContainer()->get('service.order.orderStatus');

        $this->context->smarty->assign(
            array(
                'isPs16' => $this->module->isPS16(),
                'multiselectOrderStatuses' => $orderStatusService->getOrderStatusesForMultiselect(),
            )
        );
    }

    /**
     * Init ViaBill Settings Controller Options.
     */
    private function initOptions()
    {
        $pricetagSettingsInfoBlockText =
            $this->l('Enable ViaBill???s PriceTags to obtain the best possible conversion, and inform your customers about ViaBill.');

        $myViaBillInfoBlockText =
            $this->l('MyViaBill is where you find your settlement documents for your ViaBill transactions and upload your KYC documents.');

        $myViaBillUrl = $this->getMyViaBillLink();

        $myViaBillButtonClasses = 'btn btn-default pull-right js-go-to-viabill';
        if (!$myViaBillUrl) {
            $myViaBillButtonClasses .= ' disabled';
        }

        $orderStatusMultiselectClasses = 'order-status-multiselect js-order-status-multiselect';
        if (!Configuration::get(Config::ENABLE_AUTO_PAYMENT_CAPTURE)) {
            $orderStatusMultiselectClasses .= ' hidden-form-group';
        }

        $moduleInfoBlockText = $this->getDebugInfo();

        $this->fields_options = array(
            Config::SETTINGS_PRICETAG_SETTINGS_SECTION => array(
                'title' => $this->l('Pricetag Settings'),
                'icon' => 'icon-money',
                'image' => '',
                'fields' => array(
                    Config::PRICETAG_SETTINGS_INFO_BLOCK_FIELD => array(
                        'type' => 'free',
                        'desc' => $this->getInfoBlockTemplate($pricetagSettingsInfoBlockText),
                        'class' => 'hidden',
                        'form_group_class' => 'viabill-info-block'
                    ),
                    Config::ENABLE_PRICE_TAG_ON_PRODUCT_PAGE => array(
                        'title' => $this->l('Enable on Product page'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                    Config::ENABLE_PRICE_TAG_ON_CART_SUMMARY => array(
                        'title' => $this->l('Enable on Cart Summary'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                    Config::ENABLE_PRICE_TAG_ON_PAYMENT_SELECTION => array(
                        'title' => $this->l('Enable on Payment selection'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
            Config::SETTINGS_GENERAL_CONFIGURATION_SECTION => array(
                'title' => $this->l('General Configuration'),
                'icon' => 'icon-cog',
                'image' => '',
                'fields' => array(
                    Config::VIABILL_TEST_MODE => array(
                        'title' => $this->l('ViaBill Test Mode'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                    Config::VIABILL_LOGO_DISPLAY_IN_CHECKOUT => array(
                        'title' => $this->l('Display ViaBill logo in the checkout payment step'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                    Config::SINGLE_ACTION_CAPTURE_CONF_MESSAGE => array(
                        'title' => $this->l('Capture confirmation message for single action'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                    Config::BULK_ACTION_CAPTURE_CONF_MESSAGE => array(
                        'title' => $this->l('Capture confirmation message for bulk action'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                    Config::SINGLE_ACTION_REFUND_CONF_MESSAGE => array(
                        'title' => $this->l('Refund confirmation message for single action'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                    Config::BULK_ACTION_REFUND_CONF_MESSAGE => array(
                        'title' => $this->l('Refund confirmation message for bulk action'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                    Config::SINGLE_ACTION_CANCEL_CONF_MESSAGE => array(
                        'title' => $this->l('Cancel confirmation message for single action'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                    Config::BULK_ACTION_CANCEL_CONF_MESSAGE => array(
                        'title' => $this->l('Cancel confirmation message for bulk action'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
            Config::SETTINGS_PAYMENT_CAPTURE_SECTION => array(
                'title' => $this->l('Payment Capture Configuration'),
                'icon' => 'icon-money',
                'image' => '',
                'fields' => array(
                    Config::ENABLE_AUTO_PAYMENT_CAPTURE => array(
                        'title' => $this->l('Enable ViaBill payment auto-capture'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                    Config::CAPTURE_ORDER_STATUS_MULTISELECT => array(
                        'title' => $this->l('Auto-capture ViaBill payment when status is set to'),
                        'type' => 'orders_status_multiselect',
                        'class' => 'fixed-width-xxl',
                        'form_group_class' => $orderStatusMultiselectClasses
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
            Config::SETTINGS_MY_VIABILL_SECTION => array(
                'title' => $this->l('My ViaBill'),
                'icon' => 'icon-info-sign',
                'image' => '',
                'fields' => array(
                    Config::MY_VIABILL_INFO_BLOCK_FIELD => array(
                        'type' => 'free',
                        'desc' => $this->getInfoBlockTemplate($myViaBillInfoBlockText),
                        'class' => 'hidden',
                        'form_group_class' => 'viabill-info-block'
                    ),
                ),
                'buttons' => array(
                    array(
                        'title' => $this->l('Go to MyViaBill'),
                        'icon' => 'process-icon-next',
                        'name' => 'goToMyViaBill',
                        'type' => 'button',
                        'class' => $myViaBillButtonClasses,
                        'href' => $myViaBillUrl,
                    ),
                ),
            ),
            Config::SETTINGS_DEBUG_SECTION => array(
                'title' => $this->l('Debug and troubleshooting information'),
                'icon' => 'icon-clipboard',
                'fields' => array(
                    Config::ENABLE_DEBUG => array(
                        'title' => $this->l('Enable Debug'),
                        'validation' => 'isBool',
                        'cast' => 'boolval',
                        'type' => 'bool',
                    ),
                    Config::MODULE_INFO_FIELD => array(
                        'type' => 'free',
                        'desc' => '<div class="alert alert-info">'.$moduleInfoBlockText.'</div>',
                        'class' => 'module_info',
                        'form_group_class' => 'viabill-info-block',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        if (!$this->module->isPS16()) {
            $this->context->smarty->assign(
                array(
                    'myViaBillLink' => $myViaBillUrl,
                )
            );
            $this->fields_options[Config::SETTINGS_MY_VIABILL_SECTION]['bottom'] = $this->context->smarty->fetch(
                $this->module->getLocalPath() . 'views/templates/admin/ps15-my-viabill-btn.tpl'
            );
        }
    }

    /**
     * Gets MyViaBill Auto-Login Link.
     *
     * @return bool|string|void
     *
     * @throws Exception
     */
    private function getMyViaBillLink()
    {
        /**
         * @var Config $config
         */
        $config = $this->module->getContainer()->get('config');

        if (!$config->isLoggedIn()) {
            return;
        }

        /** @var \ViaBill\Service\Api\Link\LinkService $linkService */
        $linkService = $this->module->getContainer()->get('service.link');
        $linkResponse = $linkService->getLink();

        if ($linkResponse->hasErrors()) {
            $errors = $linkResponse->getErrors();

            foreach ($errors as $error) {
                $errorField = '';

                if ($error->getField()) {
                    $errorField = sprintf($this->l('Field: %s. '), $error->getField());
                }

                $this->context->controller->warnings[] =
                    $errorField . sprintf($this->l('Error: %s '), $error->getError());
            }

            return false;
        }

        return $linkResponse->getLink();
    }

    private function getDebugInfo()
    {
        try {
            // Get Module Version
            $moduleInstance = Module::getInstanceByName('viabill');
            $module_version = $moduleInstance->version;

            // Get PHP info
            $php_version = phpversion();
            $memory_limit = ini_get('memory_limit');

            // Get Prestashop Version
            $prestashop_version = Configuration::get('PS_VERSION_DB');

            // Log data
            $debug_file_path = DebugLog::getFilename();

            $module_info_data = '<ul>'.
                '<li><strong>'.$this->l('Module Version').'</strong>: '.$module_version.'</li>'.
                '<li><strong>'.$this->l('Prestashop Version').'</strong>: '.$prestashop_version.'</li>'.
                '<li><strong>'.$this->l('PHP Version').'</strong>: '.$php_version.'</li>'.
                '<li><strong>'.$this->l('Memory Limit').'</strong>: '.$memory_limit.'</li>'.
                '<li><strong>'.$this->l('OS').'</strong>: '.PHP_OS.'</li>'.
                '<li><strong>'.$this->l('Debug File').'</strong>: '.$debug_file_path.'</li>'.
                '</ul>';

            $module_params = [
                'module_version'=>$module_version,
                'prestashop_version'=>$prestashop_version,
                'php_version'=>$php_version,
                'memory_limit'=>$memory_limit,
                'os'=>PHP_OS,
                'debug_file'=>$debug_file_path,
            ];

            $email_support = $this->getSupportEmail($module_params);

            $contact_form = $this->getSupportForm();

            $troubleshoot_form = $this->getTroubleshootForm();

            $module_info_data .= $email_support. '<br/>'. $contact_form. '<br/>'.$troubleshoot_form;
        } catch (\Exception $e) {
            $module_info_data = $this->l('N/A');
            DebugLog::msg($e->getMessage(), 'error');
        }

        $html = $module_info_data;

        return $html;
    }

    protected function getSupportEmail($params)
    {
        $site_url = _PS_BASE_URL_;
        $file_lines = 1;

        $email = self::VIABILL_TECH_SUPPORT_EMAIL;
        $subject = "Prestashop 1.6 - Technical Assistance Needed - {$site_url}";
        $body = "Dear support,\r\nI am having an issue with the ViaBill Payment Module.".
            "\r\nHere is the detailed description:\r\n".
            "\r\nType here ....\r\n".
            "\r\n ============================================ ".
            "\r\n[System Info]\r\n".
            "* Module Version: ".$params['module_version']."\r\n".
            "* Prestashop Version: ".$params['prestashop_version']."\r\n".
            "* PHP Version: ".$params['php_version']."\r\n".
            "* Memory Limit: ".$params['memory_limit']."\r\n".
            "* OS: ".$params['os']."\r\n".
            "* Debug File: ".$params['debug_file']."\r\n";

        $html = $this->l('Need support? Contact us at ').
            '<a href="mailto:'.$email.'?subject='.rawurlencode($subject).
            '&body='.rawurlencode($body).'">'.$email.'</a>';

        return $html;
    }

    protected function getSupportForm()
    {
        $url = $this->context->link->getAdminLink('AdminViaBillContact');
        $html = $this->l('Or use instead the').' <a href="'.$url.'">'.$this->l('Contact form').'</a>';

        return $html;
    }

    protected function getTroubleshootForm()
    {
        $url = $this->context->link->getAdminLink('AdminViaBillTroubleshoot');
        $html = $this->l('If you are having trouble displaying the PriceTags visit the'). ' <a href="'.$url.'">'.$this->l('Troubleshooting').'</a>';

        return $html;
    }

    protected function fileTail($filepath, $num_of_lines = 100)
    {
        $tail = '';

        $file = new \SplFileObject($filepath, 'r');
        $file->seek(PHP_INT_MAX);
        $last_line = $file->key();

        if ($last_line < $num_of_lines) {
            $num_of_lines = $last_line;
        }

        if ($num_of_lines>0) {
            $lines = new \LimitIterator($file, $last_line - $num_of_lines, $last_line);
            $arr = iterator_to_array($lines);
            $arr = array_reverse($arr);
            $tail = implode("", $arr);
        }

        return $tail;
    }
}
