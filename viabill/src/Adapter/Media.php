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

namespace ViaBill\Adapter;

use Controller;
use Smarty_Variable;

/**
 * Class Media
 *
 * @package ViaBill\Adapter
 */
class Media
{
    /**
     * Module Main Class Variable Declaration.
     *
     * @var \ViaBill
     */
    private $module;

    /**
     * Media constructor.
     *
     * @param \ViaBill $module
     */
    public function __construct(\ViaBill $module)
    {
        $this->module = $module;
    }

    /**
     * Adds Front Office CSS.
     *
     * @param Controller $controller
     * @param string $url
     */
    public function addCss(Controller $controller, $url)
    {
        $controller->addCSS(
            $this->module->getPathUri().'/'.$url
        );
    }

    /**
     * Adds Front Office JS.
     *
     * @param Controller $controller
     * @param string $url
     */
    public function addJs(Controller $controller, $url)
    {
        $controller->addJS(
            $this->module->getPathUri().'/'.$url
        );
    }

    /**
     * Adds JS Variables Definitions.
     *
     * @param array $data
     */
    public function addJsDef(array $data)
    {
        if ($this->module->isPS16()) {
            \Media::addJsDef($data);
        } else {
            $this->module->addJsDefPS15($data);
        }
    }

    /**
     * Adds Back Office JS.
     *
     * @param Controller $controller
     * @param string $fileUrl
     */
    public function addJsAdmin(Controller $controller, $fileUrl)
    {
        $controller->addJS(
            $this->module->getPathUri().'views/js/admin/'.$fileUrl
        );
    }

    /**
     * Adds Back Office CSS.
     *
     * @param Controller $controller
     * @param string $fileUrl
     */
    public function addCssAdmin(Controller $controller, $fileUrl)
    {
        $controller->addCSS(
            $this->module->getPathUri().'views/css/admin/'.$fileUrl
        );
    }
}
