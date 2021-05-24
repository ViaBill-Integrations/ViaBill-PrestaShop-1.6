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

namespace ViaBill\Builder\Template;

/**
 * Class TagBodyTemplate
 *
 * @package ViaBill\Builder\Template
 */
class OrderConfirmationMessageTemplate implements TemplateInterface
{
    /**
     * Module Main Class Variable Declaration.
     *
     * @var \ViaBill
     */
    private $module;

    /**
     * Order Message Template Class Variable Declaration.
     *
     * @var string $orderMessageTemplateClass
     */
    private $orderMessageTemplateClass;

    /**
     * Order Message text Variable Declaration.
     *
     * @var string $orderMessageText
     */
    private $orderMessageText;

    /**
     * Smarty Variable Declaration.
     *
     * @var \Smarty
     */
    private $smarty;

    /**
     * TagScriptTemplate constructor.
     *
     * @param \ViaBill $module
     */
    public function __construct(\ViaBill $module)
    {
        $this->module = $module;
    }

    /**
     * Sets Smarty From Given Param.
     *
     * @param \Smarty $smarty
     */
    public function setSmarty(\Smarty $smarty)
    {
        $this->smarty = $smarty;
    }

    /**
     * Sets Order Message Template Class.
     *
     * @param string $orderMessageTemplateClass
     */
    public function setOrderMessageTemplateClass($orderMessageTemplateClass)
    {
        $this->orderMessageTemplateClass = $orderMessageTemplateClass;
    }

    /**
     * Sets Order Message Text.
     *
     * @param string $orderMessageText
     */
    public function setOrderMessageText($orderMessageText)
    {
        $this->orderMessageText = $orderMessageText;
    }

    /**
     * Gets Smarty Params.
     *
     * @return array
     */
    public function getSmartyParams()
    {
        return array(
            'orderMessageText' => $this->orderMessageText,
            'orderMessageClass' => $this->orderMessageTemplateClass,
        );
    }

    /**
     * Gets Order Confirmation Message Template.
     *
     * @return string
     *
     * @throws \SmartyException
     */
    public function getHtml()
    {
        $this->smarty->assign($this->getSmartyParams());
        return $this->smarty->fetch(
            $this->module->getLocalPath().'views/templates/front/order-confirmation-message.tpl'
        );
    }
}
