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

use ViaBill\Util\DebugLog;

/**
 * Class ViaBill
 */
class ViaBill extends PaymentModule
{
    /**
     * Symfony DI Container Cache
     */
    const DISABLE_CACHE = true;

    /**
     * Symfony DI Container
     *
     * @var ViaBillContainer
     */
    private $moduleContainer;

    /**
     * JS Def Array Variable Declaration.
     *
     * @var array
     */
    private $jsDef = array();

    /**
     * ViaBill constructor.
     */
    public function __construct()
    {
        $this->name = 'viabill';
        $this->tab = 'payments_gateways';
        $this->displayName = $this->l('ViaBill');
        $this->author = 'Written for or by ViaBill';
        $this->description = 'ViaBill Official – Try, before you buy!';
        $this->version = '2.1.15';
        $this->ps_versions_compliancy = array('min' => '1.5.1.0', 'max' => _PS_VERSION_);
        $this->module_key = '026cfbb4e50aac4d9074eb7c9ddc2584';

        parent::__construct();
        $this->autoLoad();
        $this->compile();
    }

    /**
     * ViaBill Module Installation Method
     *
     * @return bool
     *
     * @throws Exception
     */
    public function install()
    {
        if (version_compare(PHP_VERSION, '5.3.29', '<')) {
            $this->context->controller->errors[] = sprintf(
                $this->l('Minimum PHP version required for %s module is %s'),
                $this->displayName,
                '5.3.29'
            );

            return false;
        }

        /**
         * @var \ViaBill\Install\Installer $installer
         */
        $installer = $this->getContainer()->get('installer');

        return parent::install() && $installer->install();
    }

    /**
     * ViaBill Module Uninstall Method
     *
     * @return bool
     *
     * @throws Exception
     */
    public function uninstall()
    {
        /**
         * @var \ViaBill\Install\UnInstaller $unInstaller
         */
        $unInstaller = $this->getContainer()->get('unInstaller');

        return parent::uninstall() && $unInstaller->uninstall();
    }

    /**
     * Getting BO Tabs From Install Folder
     *
     * @return array
     *
     * @throws Exception
     */
    public function getTabs()
    {
        /**
         * @var \ViaBill\Install\Tab $tab
         */
        $tab = $this->getContainer()->get('tab');

        return $tab->getTabs();
    }

    /**
     * Getting Controller Settings Name
     * Redirecting To Settings Controller
     *
     * @throws Exception
     */
    public function getContent()
    {
        /**
         * @var \ViaBill\Install\Tab $tab
         */
        $tab = $this->getContainer()->get('tab');

        Tools::redirectAdmin($this->context->link->getAdminLink($tab->getControllerSettingsName()));
    }

    /**
     * Check if PrestaShop version is >= 1.6
     *
     * @return bool
     */
    public function isPS16()
    {
        return version_compare(_PS_VERSION_, '1.6', '>=');
    }

    /**
     * Displays JS Variables In Display Header Hook In PS 1.5.
     *
     * @return string
     */
    public function hookDisplayHeader()
    {
        if ($this->isPS16()) {
            return '';
        }

        return $this->renderJsDef();
    }

    /**
     * Checks Is Price Tag Active.
     * Checks Is Valid Controller.
     * Checks If User Is Logged In.
     *
     * @param string $controllerName
     *
     * @return bool|mixed
     *
     * @throws Exception
     */
    public function isPriceTagActive($controllerName)
    {
        $cacheKey = __CLASS__ . __FUNCTION__ . '' . $controllerName;

        if (Cache::isStored($cacheKey)) {
            return Cache::retrieve($cacheKey);
        }

        /**
         * @var \ViaBill\Config\Config $config
         */
        $config = $this->getContainer()->get('config');
        /**
         * @var \ViaBill\Service\Validator\Payment\CurrencyValidator $currencyValidator
         */
        $currencyValidator = $this->getContainer()->get('service.validator.currency');

        /** @var \ViaBill\Service\Validator\LocaleValidator $localeValidator */
        $localeValidator = $this->getContainer()->get('service.validator.locale');

        $isValidController = in_array($controllerName, ViaBill\Config\Config::getTagsControllers());

        if (!$isValidController) {
            return false;
        }

        $isLogged = $config->isLoggedIn();
        $isCurrencyMatches = $currencyValidator->isCurrencyMatches($this->context->currency);
        $isLocaleMatches = $localeValidator->isLocaleMatches($this->context->language);

        $isDisplayed = $isLogged && $isCurrencyMatches && $isLocaleMatches;
        Cache::store($cacheKey, $isDisplayed);
        return $isDisplayed;
    }

    /**
     * SetsCSS And JS Files For Front Controller.
     * Calls Price Tag Methods.
     *
     * @throws Exception
     */
    public function hookActionFrontControllerSetMedia()
    {
        if ($this->context->controller instanceof ViaBillReturnModuleFrontController) {
            $this->context->controller->addCSS(
                $this->getPathUri().'/'. 'views/css/front/return.css'
            );
        }

        if (!$this->isPriceTagActive($this->context->controller->php_self)) {
            return;
        }

        /**
         * @var \ViaBill\Service\Strategy\AssetsLoader $strategyAssetsLoader
         */
        $strategyAssetsLoader = $this->getContainer()->get('strategy.assets.loader');
        $strategyAssetsLoader->setController($this->context->controller);
        $strategyAssetsLoader->load();
    }

    /**
     * Setting CSS And JS Files For Admin Order Controller.
     *
     * @throws Exception
     */
    public function hookActionAdminControllerSetMedia()
    {
        if (!$this->context->controller instanceof AdminOrdersController) {
            return;
        }

        /**
         * @var \ViaBill\Adapter\Media $mediaAdapter
         */
        $mediaAdapter = $this->getContainer()->get('adapter.media');
        $mediaAdapter->addJsAdmin($this->context->controller, 'viabill-confirmation-message.js');

        $mediaAdapter->addCssAdmin($this->context->controller, 'info-block.css');

        if (!$this->isPS16()) {
            $mediaAdapter->addCssAdmin($this->context->controller, 'order-actions15.css');
        }
    }

    /**
     * Allows To Add AutoLoad Only To Admin Controllers.
     *
     * @throws Exception
     */
    public function hookModuleRoutes()
    {
        $tabs = $this->getTabs();
        $controllers = array();

        foreach ($tabs as $tab) {
            $controllers[] = $tab['class_name'];
        }

        if (empty($controllers)) {
            return array();
        }

        if (in_array(Tools::getValue('controller'), $controllers)) {
            $this->autoLoad();
        }

        return array();
    }

    /**
     * Adds Payment Management And Order Message Templates To Order Page.
     *
     * @param array $params
     *
     * @return string|void
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplayAdminOrder(array $params)
    {
        $order = new Order($params['id_order']);
        /**
         * @var \ViaBill\Config\Config $config
         */
        $config = $this->getContainer()->get('config');

        $isMuleOrder = $order->module === $this->name;
        $isLogged = $config->isLoggedIn();

        if (!$isMuleOrder || !$isLogged) {
            return;
        }

        /**
         * @var \ViaBill\Builder\Message\OrderMessageBuilder $messageBuilder
         */
        $messageBuilder = $this->getContainer()->get('builder.message.order');
        $messageBuilder->setContext($this->context);
        $messageBuilder->displayConfirmationMessage();
        $messageBuilder->displayErrorMessage();
        $messageBuilder->displayWarningMessage();

        $idViaBillOrder = ViaBillOrder::getPrimaryKey($order->id);
        $viaBillOrder = new ViaBillOrder($idViaBillOrder);

        $isOrderAccepted = Validate::isLoadedObject($viaBillOrder);

        if (!$isOrderAccepted) {
            return;
        }

        /**
         * @var \ViaBill\Builder\Template\PaymentManagementTemplate $paymentTemplate
         */
        $paymentTemplate = $this->getContainer()->get('builder.template.paymentManagement');
        $paymentTemplate->setSmarty($this->context->smarty);
        $paymentTemplate->setLanguage($this->context->language);
        $paymentTemplate->setOrder($order);

        /**
         * @var \ViaBill\Install\Tab $tab
         */
        $tab = $this->getContainer()->get('tab');

        /** @var \ViaBill\Adapter\Link $link */
        $link = $this->getContainer()->get('adapter.link');

        $paymentTemplate->setFormAction(
            $link->getAdminLink(
                $tab->getControllerActionsName(),
                array(
                    'id_order' => $order->id,
                    'action' => 'handleViaBillOrder'
                )
            )
        );

        $returnTemplate = '';
        try {
            $returnTemplate = $paymentTemplate->getHtml();
        } catch (Exception $exception) {
            /**
             * @var string[] $errors
             */
            $errors = json_decode($exception->getMessage());

            foreach ($errors as $error) {
                $this->context->controller->errors[] = $error;
            }
        }

        return $returnTemplate;
    }

    /**
     * Builds New Payment Option And Adds It To Payment Options.
     *
     * @param array $params
     *
     * @return array|string
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookPayment($params)
    {
        /**
         * @var \ViaBill\Config\Config $config
         */
        $config = $this->getContainer()->get('config');

        if (!$config->isLoggedIn()) {
            return '';
        }

        /**
         * @var Cart $cart
         */
        $cart = $params['cart'];
        $currency = new Currency($cart->id_currency);
        $language = new Language($cart->id_lang);

        /**
         * @var \ViaBill\Builder\Payment\PaymentOptionsBuilder $paymentOptionBuilder
         */
        $paymentOptionBuilder = $this->getContainer()->get('builder.payment.paymentOption');
        $paymentOptionBuilder->setLink($this->context->link);
        $paymentOptionBuilder->setCurrency($currency);
        $paymentOptionBuilder->setLanguage($language);
        $paymentOptionBuilder->setSmarty($this->context->smarty);
        $paymentOptionBuilder->setController($this->context->controller->php_self);
        $paymentOptionBuilder->setOrderPrice($cart->getOrderTotal());

        return $paymentOptionBuilder->getPaymentOptions();
    }

    /**
     * Adds Notifications To BO Order Page Header.
     * Checks If User Is Logged In.
     *
     * @throws Exception
     */
    public function hookDisplayBackOfficeHeader()
    {
        if ($this->context->controller instanceof AdminViaBillAuthenticationController) {

            /**
             * @var \ViaBill\Adapter\Media $mediaAdapter
             */
            $mediaAdapter = $this->getContainer()->get('adapter.media');

            $mediaAdapter->addJsDef(array(
                'termsLink' => ViaBill\Config\Config::TERMS_AND_CONDITIONS_LINK,
            ));

            return $this->renderJsDef();
        }

        /**
         * @var \ViaBill\Config\Config $config
         */
        $config = $this->getContainer()->get('config');

        if (!$config->isLoggedIn()) {
            return;
        }

        if (!$this->context->controller instanceof AdminOrdersController) {
            return;
        }

        $idOrder = Tools::getValue('id_order');

        if ($idOrder) {
            return;
        }

        /** @var \ViaBill\Service\Api\Notification\NotificationService $notificationService */
        $notificationService = $this->getContainer()->get('service.notification');
        $notifications = $notificationService->getNotifications();

        foreach ($notifications as $notification) {
            $this->context->controller->informations[] =
                sprintf('%s: %s', $this->displayName, $notification->getMessage());
        }
    }

    /**
     * Adds ViaBill Price Tag To Product Page Price Block.
     *
     * @param array $params
     *
     * @return string|void
     *
     * @throws Exception
     */
    public function hookDisplayProductPriceBlock($params)
    {
        if (!$this->isPS16()) {
            return;
        }

        if ($params['type'] !== 'after_price') {
            return;
        }

        if (!Configuration::get(ViaBill\Config\Config::ENABLE_PRICE_TAG_ON_PRODUCT_PAGE)) {
            return;
        }

        if (!$this->isPriceTagActive($this->context->controller->php_self)) {
            return;
        }

        $product = $params['product'];

        /**
         * @var \ViaBill\Builder\Template\DynamicPriceTemplate $tagPriceTemplate
         */
        $tagPriceTemplate = $this->getContainer()->get('builder.template.tagPriceHolder');
        $tagPriceTemplate->setSmarty($this->context->smarty);
        $tagPriceTemplate->setPrice($product->getPrice());

        /**
         * @var \ViaBill\Adapter\Media $mediaAdapter
         */
        $mediaAdapter = $this->getContainer()->get('adapter.media');

        return $tagPriceTemplate->getHtml() . $this->initPriceTagsProductPage($mediaAdapter);
    }

    /**
     * Adds ViaBill Price Tag To Product Page Price Block.
     *
     * @param array $params
     *
     * @return string|void
     *
     * @throws Exception
     */
    public function hookProductActions($params)
    {
        if ($this->isPS16()) {
            return;
        }

        if (!Configuration::get(ViaBill\Config\Config::ENABLE_PRICE_TAG_ON_PRODUCT_PAGE)) {
            return;
        }

        if (!$this->isPriceTagActive($this->context->controller->php_self)) {
            return;
        }
        /** @var Product $product */
        $product = $params['product'];
        $currency = Currency::getCurrency($params['cart']->id_currency);
        $decimals = (int)$currency['decimals'] * _PS_PRICE_DISPLAY_PRECISION_;

        /**
         * @var \ViaBill\Builder\Template\DynamicPriceTemplate $tagPriceTemplate
         */
        $tagPriceTemplate = $this->getContainer()->get('builder.template.tagPriceHolder');
        $tagPriceTemplate->setSmarty($this->context->smarty);
        $tagPriceTemplate->setPrice(Tools::ps_round($product->getPrice(), $decimals));

        /**
         * @var \ViaBill\Adapter\Media $mediaAdapter
         */
        $mediaAdapter = $this->getContainer()->get('adapter.media');

        $this->initPriceTagsProductPage($mediaAdapter);

        return $tagPriceTemplate->getHtml();
    }

    /**
     * Adds Dynamic Price Holder In Shopping Cart Footer Hook In Checkout.
     *
     * @param $params
     *
     * @return string|void
     *
     * @throws SmartyException
     */
    public function hookDisplayShoppingCartFooter($params)
    {
        if (!$this->isPriceTagActive($this->context->controller->php_self)) {
            return;
        }

        if (!Configuration::get(ViaBill\Config\Config::ENABLE_PRICE_TAG_ON_CART_SUMMARY) &&
            !Configuration::get(ViaBill\Config\Config::ENABLE_PRICE_TAG_ON_PAYMENT_SELECTION)) {
            return;
        }

        /**
         * @var Cart $cart
         */
        $cart = $params['cart'];

        /**
         * @var \ViaBill\Builder\Template\DynamicPriceTemplate $tagPriceTemplate
         */
        $tagPriceTemplate = $this->getContainer()->get('builder.template.tagPriceHolder');
        $tagPriceTemplate->setSmarty($this->context->smarty);
        $tagPriceTemplate->setPrice($cart->getOrderTotal());

        return $tagPriceTemplate->getHtml();
    }

    /**
     * Returns Order Success Or Error Messages To Order Confirmation Template.
     *
     * @return string
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function hookDisplayOrderConfirmation()
    {
        /**
         * @var \ViaBill\Builder\Template\OrderConfirmationMessageTemplate $OrderConfirmationMessageTemplate
         */
        $OrderConfirmationMessageTemplate = $this->getContainer()->get('builder.template.orderConfirmationMessage');
        $OrderConfirmationMessageTemplate->setSmarty($this->context->smarty);

        if (Tools::getValue('error')) {
            $OrderConfirmationMessageTemplate->setOrderMessageTemplateClass('alert-danger');
            $OrderConfirmationMessageTemplate->setOrderMessageText(
                $this->l('An unexpected error occurred while processing the payment.')
            );

            return $OrderConfirmationMessageTemplate->getHtml();
        }

        $orderId = Order::getOrderByCartId(Tools::getValue('id_cart'));
        $order = new Order($orderId);

        if (Tools::getIsset('success')) {
            /**
             * @var \ViaBill\Service\Cart\MemorizeCartService $memorizeService
             */
            $memorizeService = $this->getContainer()->get('cart.memorizeCartService');
            $memorizeService->removeMemorizedCart($order);
            $OrderConfirmationMessageTemplate->setOrderMessageTemplateClass('alert-success');
            $OrderConfirmationMessageTemplate->setOrderMessageText(
                sprintf($this->l('Your order with reference %s has been confirmed'), $order->reference)
            );
        }

        if (Tools::getIsset('cancel')) {
            $OrderConfirmationMessageTemplate->setOrderMessageTemplateClass('alert-danger');
            $OrderConfirmationMessageTemplate->setOrderMessageText(
                sprintf($this->l('Your order with reference %s is canceled'), $order->reference)
            );
        }

        return $OrderConfirmationMessageTemplate->getHtml();
    }

    /**
     * Method disallows to change order status if order status is "Payment pending by ViaBill"
     *
     * @param $params
     * @return bool
     * @throws PrestaShopException
     */
    public function hookActionOrderStatusUpdate($params)
    {
        if (!isset($this->context->employee) || !$this->context->employee->isLoggedBack()) {
            return true;
        }

        /** @var \ViaBill\Service\Validator\Payment\OrderValidator $orderPaymentValidator */
        $orderPaymentValidator = $this->getContainer()->get('service.validator.payment.order');

        $order = new Order((int) $params['id_order']);

        $validationResult = $orderPaymentValidator->validateIsOrderWithModulePayment($order);

        if (!$validationResult->isValidationAccepted()) {
            return false;
        }

        $paymentInPendingState = Configuration::get(ViaBill\Config\Config::PAYMENT_PENDING) &&
            $order->current_state === Configuration::get(ViaBill\Config\Config::PAYMENT_PENDING);

        if (!$paymentInPendingState) {
            return true;
        }

        if ($this->context->controller instanceof ViaBillCallBackModuleFrontController) {
            return true;
        }

        $warnings = array(
            $this->l('The status cannot be changed, since ViaBill has not yet approved the payment. Please try again later.'),
        );

        /** @var \ViaBill\Service\MessageService $messageService */
        $messageService = $this->getContainer()->get('service.message');
        $messageService->redirectWithMessages($order, array(), array(), $warnings);
    }

    /**
     * Adds auto full capture functionality when the status of the order is changed to "Payment completed by ViaBill"
     *
     * @param $params
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionOrderHistoryAddAfter($params)
    {
        /** @var \ViaBill\Service\Validator\Payment\OrderValidator $orderPaymentValidator */
        /** @var \ViaBill\Service\Order\OrderStatusService $orderStatusService */
        $orderPaymentValidator = $this->getContainer()->get('service.validator.payment.order');
        $orderStatusService = $this->getContainer()->get('service.order.orderStatus');

        $orderHistory = $params['order_history'];

        $order = new Order((int)$orderHistory->id_order);

        $validationResult = $orderPaymentValidator->validateIsOrderWithModulePayment($order);

        if (!$validationResult->isValidationAccepted()) {
            return false;
        }

        $debug_str = var_export($order, true);
        DebugLog::msg("hookActionOrderHistoryAddAfter / validation accepted for order: ".$debug_str);

        $newOrderStatusId = (int) $orderHistory->id_order_state;
        $viaBillPaymentCompletedOrderStatus = (int) Configuration::get(
            ViaBill\Config\Config::PAYMENT_COMPLETED
        );
        $enableAutoPaymentCapture = (bool) Configuration::get(
            ViaBill\Config\Config::ENABLE_AUTO_PAYMENT_CAPTURE
        );
        $captureMultiselectOrderStatuses = $orderStatusService->getDecodedCaptureMultiselectOrderStatuses();

        if (($viaBillPaymentCompletedOrderStatus && $newOrderStatusId === $viaBillPaymentCompletedOrderStatus) ||
            ($enableAutoPaymentCapture && in_array($newOrderStatusId, $captureMultiselectOrderStatuses))
        ) {
            $this->capturePayment($order);
        }

        return false;
    }

    /**
     * Getting Order List Action Confirmation Message.
     *
     * @param string $type
     *
     * @return string
     */
    public function getConfirmationMessageTranslation($type)
    {
        $message = '';
        switch ($type) {
            case 'cancel':
                $message = $this->l('Are you sure that you want to cancel selected orders?');
                break;
            case 'capture':
                $message = $this->l('Are you sure that you want to capture selected orders?');
                break;
            case 'refund':
                $message = $this->l('Are you sure you want to refund these transactions?');
                break;
        }
        return $message;
    }

    /**
     * Getting Order Bulk Action Translation.
     *
     * @param string $type
     *
     * @return string
     */
    public function getBulkActionTranslation($type)
    {
        $message = '';
        switch ($type) {
            case 'cancel':
                $message = $this->l('Cancel payments');
                break;
            case 'capture':
                $message = $this->l('Capture payments');
                break;
            case 'refund':
                $message = $this->l('Refund payments');
                break;
        }
        return $message;
    }

    /**
     * Getting Order Single Action Translation.
     *
     * @param string $type
     *
     * @return string
     */
    public function getSingleActionTranslations($type)
    {
        $message = '';
        switch ($type) {
            case 'capture':
                $message = $this->l('Capture payment');
                break;
            case 'refund':
                $message = $this->l('Refund payment');
                break;
            case 'cancel':
                $message = $this->l('Cancel payment');
                break;
        }
        return $message;
    }

    /**
     * Getting Order Action Confirmation Message.
     *
     * @param string $type
     * @param Order $order
     * @param null $customAmount
     *
     * @return string
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getConfirmationTranslation($type, Order $order, $customAmount = null)
    {
        $message = '';

        if ($this->isPS16()) {
            $currency = new Currency($order->id_currency, $this->context->language->id);
        } else {
            $currency = new Currency($order->id_currency);
        }

        switch ($type) {
            case 'refund':
                $amount = $order->total_paid_tax_incl;
                if ($customAmount) {
                    $amount = $customAmount;
                }
                $message =
                    sprintf(
                        $this->l('Are you sure that you want to refund %s ?'),
                        Tools::displayPrice($amount, $currency)
                    );
                break;
            case 'capture':
                $message =
                    sprintf(
                        $this->l('Are you sure that you want to capture %s ?'),
                        Tools::displayPrice($order->total_paid_tax_incl, $currency)
                    );
                break;
            case 'cancel':
                $message =
                    sprintf(
                        $this->l('Are you sure that you want to cancel order %s ?'),
                        $order->reference
                    );
                break;
        }
        return $message;
    }

    /**
     * Init ViaBill Price Tag In Cart Page.
     *
     * @param \ViaBill\Adapter\Media $mediaAdapter
     *
     * @throws SmartyException
     */
    public function initPriceTagsCartPage(\ViaBill\Adapter\Media $mediaAdapter)
    {
        /**
         * @var \ViaBill\Builder\Template\TagBodyTemplate $tagBodyTemplate
         */
        $tagBodyTemplate = $this->getContainer()->get('builder.template.tagBody');

        $tagBodyTemplate->setSmarty($this->context->smarty);
        $tagBodyTemplate->setView(
            ViaBill\Config\Config::getTagsViewByController($this->context->controller->php_self)
        );
        $tagBodyTemplate->setCurrency($this->context->currency);
        $tagBodyTemplate->setLanguage($this->context->language);
        $tagBodyTemplate->setDynamicPriceSelector(
            ViaBill\Config\Config::DYNAMIC_PRICE_CART_SELECTOR
        );
        $tagBodyTemplate->setDynamicPriceTrigger(
            ViaBill\Config\Config::DYNAMIC_PRICE_CART_TRIGGER
        );
        $tagBodyTemplate->useExtraGap(true);

        $this->context->smarty->assign($tagBodyTemplate->getSmartyParams());
        $mediaAdapter->addJsDef(array(
            'priceTagCartBodyHolder' => json_encode($tagBodyTemplate->getHtml())
        ));
    }

    /**
     * Init ViaBill Price Tag In Product Page.
     *
     * @param \ViaBill\Adapter\Media $mediaAdapter
     *
     * @return string
     * @throws SmartyException
     */
    public function initPriceTagsProductPage(\ViaBill\Adapter\Media $mediaAdapter)
    {
        /**
         * @var \ViaBill\Builder\Template\TagBodyTemplate $tagBodyTemplate
         */
        $tagBodyTemplate = $this->getContainer()->get('builder.template.tagBody');

        $tagBodyTemplate->setView(
            ViaBill\Config\Config::getTagsViewByController($this->context->controller->php_self)
        );
        $tagBodyTemplate->setLanguage($this->context->language);
        $tagBodyTemplate->setCurrency($this->context->currency);
        $tagBodyTemplate->setSmarty($this->context->smarty);
        $tagBodyTemplate->setDynamicPriceSelector(
            ViaBill\Config\Config::DYNAMIC_PRICE_PRODUCT_SELECTOR
        );
        $tagBodyTemplate->useExtraGap(true);

        $tagBodyTemplate->setDynamicPriceTrigger(
            ViaBill\Config\Config::DYNAMIC_PRICE_PRODUCT_TRIGGER
        );

        $this->context->smarty->assign($tagBodyTemplate->getSmartyParams());

        $priceTagCartBodyHolder = '';

        if (!$this->isPS16()) {
            $priceTagCartBodyHolder = json_encode($tagBodyTemplate->getHtml());
        }

        $mediaAdapter->addJsDef(array(
            'priceTagCartBodyHolder' => $priceTagCartBodyHolder,
            'dynamicPriceTagTrigger' => ViaBill\Config\Config::DYNAMIC_PRICE_PRODUCT_TRIGGER
        ));

        if (!$this->isPS16()) {
            $this->renderJsDef(true);
        }

        if ($this->isPS16()) {
            return $tagBodyTemplate->getHtml();
        }
    }

    /**
     * Renders And Assigns New HOOK_HEADER Variable With JS Variables.
     *
     * @param bool $productPage
     *
     * @return string
     */
    public function renderJsDef($productPage = false)
    {
        if (empty($this->jsDef)) {
            return '';
        }

        $context = \Context::getContext();

        $tpl = $context->smarty->createTemplate(
            $this->getLocalPath().'views/templates/hook/javascript.tpl'
        );

        $tpl->assign('js_def', $this->jsDef);

        if (!$productPage) {
            return $tpl->fetch();
        }

        $newHeader = $tpl->fetch();

        $headerSmartyVar = $context->smarty->getVariable('HOOK_HEADER');

        if ($headerSmartyVar instanceof Smarty_Variable) {
            $newHeader = $newHeader.$headerSmartyVar->value;
        }

        $context->smarty->assign('HOOK_HEADER', $newHeader);
    }


    /**
     * Adds JS Variables In PS 1.5.
     *
     * @param array $params
     */
    public function addJsDefPS15(array $params)
    {
        foreach ($params as $key => $value) {
            $this->jsDef[$key] = $value;
        }
    }

    /**
     * Captures payment
     *
     * @param Order $order
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function capturePayment(Order $order)
    {
        /** @var \ViaBill\Service\Provider\OrderStatusProvider $orderStatus */
        $orderStatus = $this->getContainer()->get('service.provider.orderStatus');

        DebugLog::msg("capturePayment / called");
        $debug_str = 'Memory Usage:'.memory_get_usage()." Peak Usage: ".memory_get_peak_usage();
        DebugLog::msg($debug_str);

        if (!$orderStatus->canBeCaptured($order)) {
            DebugLog::msg("capturePayment / Order cannot be captured:");
            $debug_str = var_export($order, true);
            DebugLog::msg($debug_str);
            return false;
        }

        $remainingToCapture = $orderStatus->getRemainingToCapture($order);

        DebugLog::msg("capturePayment / remaining to capture: $remainingToCapture");

        /** @var \ViaBill\Service\Handler\PaymentManagementHandler $paymentHandler */
        /** @var \ViaBill\Service\MessageService $messageService */
        $paymentHandler = $this->getContainer()->get('service.handler.paymentManagement');
        $messageService = $this->getContainer()->get('service.message');

        $handleResponse = $paymentHandler->handle(
            $order,
            false,
            true,
            false,
            false,
            $remainingToCapture
        );

        $errors = $handleResponse->getErrors();
        $warnings = $handleResponse->getWarnings();
        $confirmations = array();

        if (empty($errors) && $handleResponse->getSuccessMessage()) {
            DebugLog::msg("capturePayment / success: ".$handleResponse->getSuccessMessage());
            $confirmations[] = $handleResponse->getSuccessMessage();
        }

        $messageService->setMessages($confirmations, $errors, $warnings);

        return true;
    }

    /**
     * Returns Symfony DI Container.
     *
     * @return ViaBillContainer
     */
    public function getContainer()
    {
        return $this->moduleContainer;
    }

    /**
     * Includes Vendor Autoload.
     */
    private function autoLoad()
    {
        require_once $this->getLocalPath() . 'vendor/autoload.php';
    }

    /**
     * Adds Cache To DI Container.
     *
     * @throws Exception
     */
    private function compile()
    {
        $containerCache = $this->getLocalPath() . 'var/cache/container.php';
        $containerConfigCache = new Symfony\Component\Config\ConfigCache($containerCache, self::DISABLE_CACHE);
        $containerClass = get_class($this) . 'Container';
        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = new Symfony\Component\DependencyInjection\ContainerBuilder();
            $locator = new Symfony\Component\Config\FileLocator($this->getLocalPath() . '/config');
            $loader = new Symfony\Component\DependencyInjection\Loader\YamlFileLoader($containerBuilder, $locator);
            $loader->load('config.yml');
            $containerBuilder->compile();
            $dumper = new Symfony\Component\DependencyInjection\Dumper\PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump(array('class' => $containerClass)),
                $containerBuilder->getResources()
            );
        }
        require_once $containerCache;
        $this->moduleContainer = new $containerClass();
    }
}
