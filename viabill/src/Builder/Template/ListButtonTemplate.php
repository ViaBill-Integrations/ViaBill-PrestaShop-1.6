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
 * Class ListButtonTemplate
 * @package ViaBill\Builder\Template
 */
class ListButtonTemplate implements TemplateInterface
{
    /**
     * Link Variable Declaration.
     *
     * @var string
     */
    private $link;

    /**
     * Name Variable Declaration.
     *
     * @var string
     */
    private $name;

    /**
     * Confirmation Message Variable Declaration.
     *
     * @var string
     */
    private $confMessage;

    /**
     * Icon Link Variable Declaration.
     *
     * @var string
     */
    private $iconLink;

    /**
     * Smarty Variable Declaration.
     *
     * @var \Smarty
     */
    private $smarty;

    /**
     * Module Main Class Variable Declaration.
     *
     * @var \ViaBill
     */
    private $module;

    /**
     * ListButtonTemplate constructor.
     *
     * @param \ViaBill $module
     */
    public function __construct(\ViaBill $module)
    {
        $this->module = $module;
    }

    /**
     * Sets Link From Given Param.
     *
     * @param $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Sets Name From Given Param.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Sets Confirmation Message From Given Param.
     *
     * @param string $message
     */
    public function setConfMessage($message)
    {
        $this->confMessage = $message;
    }

    /**
     * Sets Icon Link From Given Param.
     *
     * @param string $iconLink
     */
    public function setIconLink($iconLink)
    {
        $this->iconLink = $iconLink;
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
     * Gets Smarty Params.
     *
     * @return array
     */
    public function getSmartyParams()
    {
        return array(
            'templateLink' => $this->link,
            'templateName' => $this->name,
            'confMessage' => $this->confMessage,
            'iconLink' => $this->iconLink ? $this->iconLink : ''
        );
    }

    /**
     * Gets Smarty List Button HTML Template.
     *
     * @return string
     *
     * @throws \SmartyException
     */
    public function getHtml()
    {
        $this->smarty->assign($this->getSmartyParams());

        if ($this->module->isPS16()) {
            $listButtonTpl = 'views/templates/admin/list-button.tpl';
        } else {
            $listButtonTpl = 'views/templates/admin/list-button15.tpl';
        }

        return $this->smarty->fetch($this->module->getLocalPath() . $listButtonTpl);
    }
}
