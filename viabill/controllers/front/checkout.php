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
 * ViaBill Checkout Module Front Controller Class.
 *
 * Class ViaBillCheckoutModuleFrontController
 */
class ViaBillCheckoutModuleFrontController extends ModuleFrontController
{
    /**
     * Filename Constant
     */
    const FILENAME = 'checkout';

    /**
     * Module Main Class Variable Declaration.
     *
     * @var ViaBill
     */
    public $module;

    /**
     * Validating Payment.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function checkAccess()
    {
        /**
         * @var \ViaBill\Service\Validator\Payment\PaymentValidator $validator
         */
        $validator = $this->module->getContainer()->get('service.validator.payment');

        return parent::checkAccess() &&
            $validator->validate(
                $this->context->link,
                $this->context->cart,
                $this->context->customer
            );
    }

    /**
     * Send Payment Request And Checks For Errors.
     * Redirects To Effective Url From ViaBill Respond.
     *
     * @throws Exception
     */
    public function postProcess()
    {
        $order = $this->getOrder();

        // Debug info
        $order_str = (empty($order))?'empty':var_export($order, true);
        $debug_str = "[order: {$order_str}]";
        DebugLog::msg("Checkout postProcess / ".$debug_str);

        /**
         * @var \ViaBill\Util\LinksGenerator $linkGenerator
         */
        $linkGenerator = $this->module->getContainer()->get('util.linkGenerator');

        /**
         * @var \ViaBill\Service\Api\Payment\PaymentService $paymentService
         */
        $paymentService = $this->module->getContainer()->get('service.api.payment');

        $paymentRequest = $this->createPaymentRequest($order, $linkGenerator);

        // Debug info
        $request_str = (empty($paymentRequest))?'empty':var_export($paymentRequest, true);
        $debug_str = "[payment request: {$request_str}]";
        DebugLog::msg("Checkout postProcess / ".$debug_str);

        try {
            $paymentResponse = $paymentService->createPayment($paymentRequest);
            $errors = $paymentResponse->getErrors();

            // Debug info
            $debug_str = var_export($errors, true);
            DebugLog::msg("Checkout postProcess / errors: ".$debug_str);

            if (empty($errors)) {
                Tools::redirect($paymentResponse->getEffectiveUrl());
            } else {
                // Debug info
                $debug_str = $paymentResponse->getEffectiveUrl();
                DebugLog::msg("Checkout postProcess / success: ".$debug_str);
            }
        } catch (Exception $exception) {
            /**
             * @var \ViaBill\Factory\LoggerFactory $loggerFactory
             */
            $loggerFactory = $this->module->getContainer()->get('factory.logger');
            $logger = $loggerFactory->create();

            // Debug info
            $debug_str = $exception->getMessage();
            DebugLog::msg("Checkout postProcess / exception ".$debug_str);

            $logger->error(
                'Exception in checkout process',
                array(
                    'exception' => $exception->getMessage()
                )
            );
        }

        $order->setCurrentState(
            (int) Configuration::get(\ViaBill\Config\Config::PAYMENT_ERROR)
        );

        Tools::redirect(
            $linkGenerator->getOrderConfirmationLink(
                $this->context->link,
                $order,
                array('error' => 1)
            )
        );
    }

    /**
     * Gets Order By Id Or By Cart.
     *
     * @return Order
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getOrder()
    {
        if (Tools::isSubmit('id_order')) {
            return new Order(Tools::getValue('id_order'));
        }

        /**
         * @var \ViaBill\Service\Order\CreateOrderService $orderCreateService
         */
        $orderCreateService = $this->module->getContainer()->get('service.order.createOrder');

        $order =  $orderCreateService->createOrder(
            $this->context->cart,
            $this->context->currency
        );

        /**
         * @var \ViaBill\Service\Cart\MemorizeCartService $memorizeService
         */
        $memorizeService = $this->module->getContainer()->get('cart.memorizeCartService');
        $memorizeService->memorizeCart($order);

        return $order;
    }

    /**
     * Creates Payment Request.
     *
     * @param Order $order
     * @param \ViaBill\Util\LinksGenerator $linksGenerator
     *
     * @return \ViaBill\Object\Api\Payment\PaymentRequest
     *
     * @throws Exception
     */
    private function createPaymentRequest(Order $order, \ViaBill\Util\LinksGenerator $linksGenerator)
    {
        /**
         * @var \ViaBill\Service\UserService $userService
         */
        $userService = $this->module->getContainer()->get('service.user');
        /**
         * @var \ViaBill\Config\Config $config
         */
        $config = $this->module->getContainer()->get('config');
        /**
         * @var \ViaBill\Util\SignaturesGenerator $signatureGenerator
         */
        $signatureGenerator = $this->module->getContainer()->get('util.signatureGenerator');

        $user = $userService->getUser();

        $currency = new Currency($order->id_currency);

        $callBackUrl = $this->getCallBackUrl(
            array(
                'key' => $signatureGenerator->generateCallBackSecurityKey()
            )
        );

        $totalAmount = $order->total_paid_tax_incl;
        $currencyIso = $currency->iso_code;
        $transaction = $order->reference;
        $idOrder = $order->id;

        $successUrl = $this->getReturnUrl([
            "id_order" => $order->id,
        ]);

        $cancelUrl = $linksGenerator->getOrderConfirmationLink(
            $this->context->link,
            $order,
            array('cancel' => true)
        );

        $md5Check = $signatureGenerator->generatePaymentCheckSum(
            $user,
            $totalAmount,
            $currencyIso,
            $transaction,
            $idOrder,
            $successUrl,
            $cancelUrl
        );

        return new \ViaBill\Object\Api\Payment\PaymentRequest(
            $user->getKey(),
            $transaction,
            $idOrder,
            $totalAmount,
            $currency->iso_code,
            $successUrl,
            $cancelUrl,
            $callBackUrl,
            $config->isTestingEnvironment(),
            $md5Check
        );
    }

    /**
     * Gets CallBack Url
     *
     * @param array $params
     *
     * @return string
     */
    private function getCallBackUrl($params = array())
    {
        return $this->context->link->getModuleLink(
            $this->module->name,
            'callback',
            $params
        );
    }

    /**
     * Gets Return Url
     *
     * @param array $params
     *
     * @return string
     */
    private function getReturnUrl($params = array())
    {
        return $this->context->link->getModuleLink(
            $this->module->name,
            'return',
            $params
        );
    }
}
