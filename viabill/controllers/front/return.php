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

/**
 * ViaBill Checkout Module Front Controller Class.
 *
 * Class ViaBillCheckoutModuleFrontController
 */
class ViaBillReturnModuleFrontController extends ModuleFrontController
{
    /**
     * Module Main Class Variable Declaration.
     *
     * @var ViaBill
     */
    public $module;

    public function postProcess()
    {
        $orderId = Tools::getValue('id_order');
        $order = new Order($orderId);

        /**
         * @var \ViaBill\Util\LinksGenerator $linkGenerator
         */
        $linkGenerator = $this->module->getContainer()->get('util.linkGenerator');

        /**
         * @var \ViaBill\Service\Provider\OrderStatusProvider $orderStatusProvider
         */
        $orderStatusProvider = $this->module->getContainer()->get('service.provider.orderStatus');

        $isOrderApproved = $orderStatusProvider->isApproved($order);
        if ($isOrderApproved) {
            /**
             * @var \ViaBill\Service\Cart\MemorizeCartService $memorizeService
             */
            $memorizeService = $this->module->getContainer()->get('cart.memorizeCartService');
            $memorizeService->removeMemorizedCart($order);
        }

        // Debug info
        $debug_str = '[Order id: '.$orderId.']';
        $approved_str = ($isOrderApproved)?'[approved]':'[not approved]';
        $debug_str .= $approved_str;
        $order_str = (empty($order))?'empty':var_export($order, true);
        $debug_str .= "[order: {$order_str}]";
        DebugLog::msg("Return processPost / ".$debug_str);

        Tools::redirect($linkGenerator->getOrderConfirmationLink(
            $this->context->link,
            $order,
            array('success' => true)
        ));
    }

    public function initContent()
    {
        parent::initContent();

        $this->setTemplate("return.tpl");
    }
}
