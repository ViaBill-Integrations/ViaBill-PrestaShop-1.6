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
use ViaBill\Controller\AbstractAdminController as ModuleAdminController;
use ViaBill\Object\Api\Authentication\LoginRequest;
use ViaBill\Object\Api\Authentication\RegisterRequest;
use ViaBill\Service\Builder\PriceTagScriptBuilder;

require_once dirname(__FILE__).'/../../vendor/autoload.php';

/**
 * ViaBill Authentication Controller Class.
 *
 * Class AdminViaBillAuthenticationController
 */
class AdminViaBillAuthenticationController extends ModuleAdminController
{

    /**
     * ViaBill Countries Variable Declaration.
     *
     * @var
     */
    private $viaBillCountries;

    /**
     * AdminViaBillAuthenticationController constructor.
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->table = Configuration::$definition['table'];
        $this->className = 'Configuration';
        $this->identifier = Configuration::$definition['primary'];
        $this->display = 'add';
        parent::__construct();

        $this->toolbar_title = $this->l('Authentication');
    }

    /**
     * Init Error Messages From Cookies.
     * Checks If User Is Logged In And Init Authentication Form.
     *
     * @throws Exception
     */
    public function init()
    {
        if (isset($this->context->cookie->authErrorMessage)) {
            $authErrors = json_decode($this->context->cookie->authErrorMessage);

            foreach ($authErrors as $authError) {
                $this->errors[] = $authError;
            }

            unset($this->context->cookie->authErrorMessage);
        }

        /**
         * @var Config $config
         */
        $config = $this->module->getContainer()->get('config');

        /**
         * @var \ViaBill\Install\Tab $tab
         */
        $tab = $this->module->getContainer()->get('tab');

        if ($config->isLoggedIn()) {
            Tools::redirectAdmin($this->context->link->getAdminLink($tab->getControllerSettingsName()));
        }

        $this->getViaBillCountries();
        $this->initForm();

        parent::init();
    }

    /**
     * Adds Register Or Login User Value To Url If That Button Is Clicked In Authentication Form.
     *
     * @throws Exception
     */
    public function initContent()
    {
        /**
         * @var \ViaBill\Builder\Template\AuthenticationTemplate $authenticationTemplate
         */
        $authenticationTemplate = $this->module->getContainer()->get('builder.template.authentication');
        $authenticationTemplate->setSmarty($this->context->smarty);

        /** @var \ViaBill\Adapter\Link $link */
        $link = $this->module->getContainer()->get('adapter.link');

        $authenticationTemplate->setNewUser(
            $link->getAdminLink($this->controller_name, array('registerUser' => 1))
        );
        $authenticationTemplate->setExistingUser(
            $link->getAdminLink($this->controller_name, array('loginUser' => 1))
        );

        if (!is_writable($this->module->getLocalPath() . 'views/js/front/')) {
            return parent::initContent();
        }

        if (!Tools::getValue('registerUser') && !Tools::getValue('loginUser')) {
            $this->content .= $authenticationTemplate->getHtml();
        }

        return parent::initContent();
    }

    /**
     * Adds CSS And JS Files To ViaBill Authentication Controller.
     *
     * @throws Exception
     */
    public function setMedia()
    {
        parent::setMedia();

        $this->addCSS($this->module->getPathUri() . 'views/css/admin/authentication.css');
        $this->addCSS($this->module->getPathUri() . 'views/css/admin/info-block.css');
        $this->addJS($this->module->getPathUri() . 'views/js/admin/authentication.js');

        if (Tools::getValue('registerUser')) {
            $this->addJS($this->module->getPathUri() . 'views/js/admin/authentication-form-submit.js');
        }

        if (!$this->module->isPS16()) {
            $this->addCSS($this->module->getPathUri() . 'views/css/admin/authentication15.css');
        }
    }

    /**
     * Login And Registration Forms Validation.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitRegisterForm')) {
            $errorsArray = array();

            $regEmail = Tools::getValue('register_user_email');
            $regCountry = Tools::getValue('register_user_country');
            $regShopUrl = Tools::getValue('register_user_shop_url');
            $regName = Tools::getValue('register_user_name');
            $regPhone = Tools::getValue('register_user_phone');
            $termsAccepted = Tools::getValue('terms_and_conditions');

            if (!$regEmail || !$regCountry || !$regShopUrl || !$termsAccepted) {
                if (!$regEmail) {
                    $errorsArray[] = $this->l('Email is required to create an account');
                }

                if (!$regCountry) {
                    $errorsArray[] = $this->l('Country is required to create an account');
                }

                if (!$regShopUrl) {
                    $errorsArray[] = $this->l('Shop Url is required to create an account');
                }

                if (!$termsAccepted) {
                    $errorsArray[] = $this->l('Please accept Terms And Conditions');
                }

                $this->context->cookie->authErrorMessage = json_encode($errorsArray);

                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminViaBillAuthentication').'&registerUser=1'
                );

                return parent::postProcess();
            }

            if (!Validate::isCleanHtml($regName) ||
                !Validate::isCleanHtml($regPhone) ||
                !Validate::isCleanHtml($regShopUrl)) {
                if (!Validate::isCleanHtml($regShopUrl)) {
                    $errorsArray[] = $this->l('Shop Url field is not valid');
                }

                if (!Validate::isCleanHtml($regName)) {
                    $errorsArray[] = $this->l('Name field is not valid');
                }

                if (!Validate::isCleanHtml($regPhone)) {
                    $errorsArray[] = $this->l('Phone field is not valid');
                }

                $this->context->cookie->authErrorMessage = json_encode($errorsArray);

                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminViaBillAuthentication').'&registerUser=1'
                );

                return parent::postProcess();
            }

            $this->registerFormRequest();
        }

        if (Tools::isSubmit('submitLoginForm')) {
            $loginEmail = Tools::getValue('login_user_email');
            $loginPassword = Tools::getValue('login_user_password');

            if (!$loginEmail || !$loginPassword) {
                $errorsArray = array();
                if (!$loginEmail) {
                    $errorsArray[] = $this->l('Email is required to create an account');
                }

                if (!$loginPassword) {
                    $errorsArray[] = $this->l('Country is required to create an account');
                }

                $this->context->cookie->authErrorMessage = json_encode($errorsArray);
                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminViaBillAuthentication').'&loginUser=1'
                );

                return parent::postProcess();
            }

            $this->loginFormRequest();
        }

        return parent::postProcess();
    }

    /**
     * Init Registration Form Values.
     *
     * @return string
     *
     * @throws SmartyException
     */
    public function renderForm()
    {
        if (!is_writable($this->module->getLocalPath() . 'views/js/front/')) {
            $this->errors[] = $this->l('Incorrect module write permissions. Please set correct permissions to ' . $this->module->getLocalPath() . 'views/js/front directory.');
        }

        $this->initRegFormValues();

        return parent::renderForm();
    }

    /**
     * Checks For $_GET registerUser of loginUser Values And Gets Needed Form.
     *
     * @return bool
     */
    protected function initForm()
    {
        if (!Tools::getValue('registerUser') && !Tools::getValue('loginUser')) {
            return false;
        }

        if (Tools::getValue('registerUser')) {
            $this->getUserRegForm();
        }

        if (Tools::getValue('loginUser')) {
            $this->getUserLoginForm();
        }
    }

    /**
     * User Registration Form Formation.
     */
    protected function getUserRegForm()
    {
        $registrationInfoBlockText =
            $this->l('This gives you a ViaBill account and allows your webshop to handle ViaBill transactions');

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Register'),
            ),
            'input' => array(
                array(
                    'type' => 'free',
                    'name' => 'registration_hint',
                    'desc' => $this->getInfoBlockTemplate($registrationInfoBlockText),
                    'class' => 'hidden',
                    'form_group_class' => 'viabill-info-block'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Email'),
                    'name' => 'register_user_email',
                    'class' => 'fixed-width-xxl',
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Country'),
                    'name' => 'register_user_country',
                    'class' => 'fixed-width-xxl js-country-select',
                    'options' => array(
                        'query' => $this->getRegFormCountriesOptions(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Live shop URL'),
                    'name' => 'register_user_shop_url',
                    'class' => 'fixed-width-xxl',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Contact name'),
                    'name' => 'register_user_name',
                    'class' => 'fixed-width-xxl'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Phone'),
                    'name' => 'register_user_phone',
                    'class' => 'fixed-width-xxl'
                ),
                array(
                    'type' => 'free',
                    'name' => 'terms_and_conditions'
                ),
            ),
            'submit' => array(
                'title' => $this->l('Create ViaBill user'),
                'icon' => 'process-icon-ok',
                'name' => 'submitRegisterForm'
            )
        );
    }

    /**
     * User Login Form Formation.
     */
    protected function getUserLoginForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Login'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Email'),
                    'name' => 'login_user_email',
                    'required' => true,
                    'class' => 'fixed-width-xxl'
                ),
                array(
                    'type' => 'password',
                    'size' => 20,
                    'label' => $this->l('Password'),
                    'name' => 'login_user_password',
                    'required' => true,
                    'class' => 'login-password-field'
                ),
            ),
            'buttons' => array(
                array(
                    'title' => $this->l('Forgot password?'),
                    'icon' => 'process-icon-help',
                    'name' => 'forgotPassword',
                    'type' => 'button',
                    'class' => 'vd-auth-additional-button',
                    'href' => Config::getLoginForgotPassUrl($this->context->language->iso_code),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Connect'),
                'icon' => 'process-icon-ok',
                'name' => 'submitLoginForm'
            )
        );

        if (!$this->module->isPS16()) {
            $this->toolbar_btn = array(
                array(
                    'desc' => $this->l('Forgot password?'),
                    'imgclass' => 'help',
                    'name' => 'forgotPassword',
                    'type' => 'button',
                    'class' => 'vd-auth-additional-button',
                    'href' => Config::getLoginForgotPassUrl($this->context->language->iso_code),
                )
            );
        }
    }

    /**
     * Gets Needed Registration Values And Perform User Registration.
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function registerFormRequest()
    {
        $regEmail = Tools::getValue('register_user_email');
        $regCountryIso = Tools::getValue('register_user_country');
        $regShopUrl = Tools::getValue('register_user_shop_url');
        $regName = Tools::getValue('register_user_name');
        $regPhone = Tools::getValue('register_user_phone');

        $resigterRequest = new RegisterRequest($regEmail, $regShopUrl, $regCountryIso, array($regName, $regPhone));

        /** @var \ViaBill\Service\Api\Authentication\RegisterService $registerService */
        $registerService = $this->module->getContainer()->get('service.register');
        $registerResponse = $registerService->register($resigterRequest);

        if ($registerResponse->hasErrors()) {
            $errors = $registerResponse->getErrors();

            foreach ($errors as $error) {
                $errorField = "";

                if ($error->getField() != "") {
                    $errorField = sprintf($this->l('Field: %s. '), $error->getField());
                }

                $this->context->controller->errors[] =
                    $errorField.' '.sprintf($this->l('Error: %s '), $error->getError());
            }

            return false;
        }

        /** @var \ViaBill\Service\Builder\PriceTagScriptBuilder  $priceTagScriptBuilder */
        $priceTagScriptBuilder = $this->module->getContainer()->get('service.builder.priceTag.script');
        $priceTagScriptBuilder->addPriceTagScript($registerResponse->getPricetagScript());

        Configuration::updateValue(Config::API_KEY, $registerResponse->getKey());
        Configuration::updateValue(Config::API_SECRET, $registerResponse->getSecret());
        Configuration::updateValue(Config::API_TAGS_SCRIPT, $registerResponse->getPricetagScript());

        $this->context->cookie->authSuccessMessage = $this->l('Account was successfully created');
        if (!$this->saveModuleRestrictions()) {
            return false;
        }

        /**
         * @var \ViaBill\Install\Tab $tab
         */
        $tab = $this->module->getContainer()->get('tab');
        $authenticationTab = Tab::getInstanceFromClassName($tab->getControllerAuthenticationName());
        $authenticationTab->active = false;
        $authenticationTab->id_parent = -1;
        $authenticationTab->update();

        Tools::redirectAdmin($this->context->link->getAdminLink('AdminViaBillSettings'));

        return true;
    }

    /**
     * Gets Needed Login Values And Perform User Login.
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function loginFormRequest()
    {
        $logEmail = Tools::getValue('login_user_email');
        $logPassword = Tools::getValue('login_user_password');

        $loginRequest = new LoginRequest($logEmail, $logPassword);

        /** @var \ViaBill\Service\Api\Authentication\LoginService $loginService */
        $loginService = $this->module->getContainer()->get('service.login');
        $loginResponse = $loginService->login($loginRequest);

        if ($loginResponse->hasErrors()) {
            $errors = $loginResponse->getErrors();

            foreach ($errors as $error) {
                $errorField = "";

                if ($error->getField() != "") {
                    $errorField = sprintf($this->l('Field: %s. '), $error->getField());
                }

                $this->context->controller->errors[] =
                    $errorField.' '.sprintf($this->l('Error: %s '), $error->getError());
            }

            return false;
        }

        /** @var \ViaBill\Service\Builder\PriceTagScriptBuilder  $priceTagScriptBuilder */
        $priceTagScriptBuilder = $this->module->getContainer()->get('service.builder.pricetag.script');
        $priceTagScriptBuilder->addPriceTagScript($loginResponse->getPricetagScript());

        Configuration::updateValue(Config::API_KEY, $loginResponse->getKey());
        Configuration::updateValue(Config::API_SECRET, $loginResponse->getSecret());
        Configuration::updateValue(Config::API_TAGS_SCRIPT, $loginResponse->getPricetagScript());

        $this->context->cookie->authSuccessMessage = $this->l('You successfully connected to ViaBill');

        if (!$this->saveModuleRestrictions()) {
            return false;
        }

        /**
         * @var \ViaBill\Install\Tab $tab
         */
        $tab = $this->module->getContainer()->get('tab');
        $authenticationTab = Tab::getInstanceFromClassName($tab->getControllerAuthenticationName());
        $authenticationTab->active = false;
        $authenticationTab->id_parent = -1;
        $authenticationTab->update();

        Tools::redirectAdmin($this->context->link->getAdminLink('AdminViaBillSettings'));

        return true;
    }

    /**
     * Gets ViaBill Supported Countries.
     *
     * @return array|bool
     *
     * @throws Exception
     */
    protected function getRegFormCountriesOptions()
    {
        $countries = $this->viaBillCountries;

        if (!$countries) {
            return false;
        }

        if (!$countries) {
            $this->context->controller->errors = $this->l('Failed to load countries. Please reload page.');

            return false;
        }

        $countriesOptions = array();

        /** @var \ViaBill\Object\Api\Countries\CountryResponse $country*/
        foreach ($countries as $country) {
            $countriesOptions[] = array(
                'id' => $country->getCode(),
                'name' => $country->getName()
            );
        }

        return $countriesOptions;
    }

    /**
     * Init Registration Form Values.
     *
     * @throws SmartyException
     */
    protected function initRegFormValues()
    {
        if (Tools::getValue('registerUser')) {
            $this->fields_value['register_user_email'] = $this->context->employee->email;
            $this->fields_value['register_user_shop_url'] = Tools::getShopDomainSsl(true);
            $this->fields_value['register_user_name'] =
                $this->context->employee->firstname . ' ' . $this->context->employee->lastname;
            $this->fields_value['register_user_phone'] = Configuration::get('PS_SHOP_PHONE');
            $this->initTermsAndConditionsValue();
        }
    }

    /**
     * Adding Country And Currency Restrictions.
     *
     * @return bool
     *
     * @throws Exception
     */
    private function saveModuleRestrictions()
    {
        /** @var \ViaBill\Service\Handler\ModuleRestrictionHandler $restrictionHandler */
        $restrictionHandler = $this->module->getContainer()->get('service.handler.moduleRestriction');
        $warnings = array();

        $failedCountry =
        $this->l('Unable to save module country restrictions. It can be done manually in payment preferences tab.');

        $failedCurrency =
        $this->l('Unable to save module currency restrictions. It can be done manually in payment preferences tab.');

        if (!$restrictionHandler->saveCountryRestriction($this->context->language)) {
            $warnings[] = $failedCountry;
        }

        if (!$restrictionHandler->saveCurrencyRestriction()) {
            $warnings[] = $failedCurrency;
        }

        $result = true;
        if (!empty($warnings)) {
            $this->context->controller->warnings = $warnings;
            $result = false;
        }

        return $result;
    }

    /**
     * Init Terms And Conditions Field Value
     *
     * @throws SmartyException
     */
    public function initTermsAndConditionsValue()
    {
        $termsLinkCountry = '';

        if ($this->viaBillCountries) {
            /** @var \ViaBill\Object\Api\Countries\CountryResponse $viaBillCountry*/
            foreach ($this->viaBillCountries as $viaBillCountry) {
                $termsLinkCountry = Config::formatCountryCodeForTCLink($viaBillCountry->getCode());
                break;
            }
        }

        /**
         * @var \ViaBill\Builder\Template\TermsAndConditionsTemplate $termsAndConditionsTemplate
         */
        $termsAndConditionsTemplate = $this->module->getContainer()->get('builder.template.termsAndConditions');
        $termsAndConditionsTemplate->setSmarty($this->context->smarty);
        $termsAndConditionsTemplate->setTermsLinkCountry($termsLinkCountry);

        $this->fields_value['terms_and_conditions'] = $termsAndConditionsTemplate->getHtml();
    }

    /**
     * Gets Country List From ViaBill API
     *
     * @throws Exception
     */
    private function getViaBillCountries()
    {
        $locale = $this->context->language->iso_code;

        /** @var \ViaBill\Service\Api\Countries\CountryService $countryService */
        $countryService = $this->module->getContainer()->get('service.country');
        $countries = $countryService->getCountries($locale);

        $this->viaBillCountries = $countries;
    }
}
