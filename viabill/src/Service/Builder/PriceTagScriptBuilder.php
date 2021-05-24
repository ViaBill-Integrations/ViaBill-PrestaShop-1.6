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

namespace ViaBill\Service\Builder;

use Tools;

/**
 * Class PriceTagScriptBuilder
 *
 * @package ViaBill\Service\Builder
 */
class PriceTagScriptBuilder
{
    /**
     * Module Main Class Variable Declaration.
     *
     * @var \ViaBill
     */
    private $module;

    /**
     * PriceTagScriptBuilder constructor.
     *
     * @param \ViaBill $module
     */
    public function __construct(\ViaBill $module)
    {
        $this->module = $module;
    }

    /**
     * Adds Price Tag Script File That Comes From Login/Register Request.
     *
     * @param $priceTagScript
     */
    public function addPriceTagScript($priceTagScript)
    {
        $priceTagScript = str_replace(array('<script>', '</script>'), '', $priceTagScript);

        $priceTagTemplate =
            Tools::file_get_contents($this->module->getLocalPath() . 'views/js/front/price-tag-template.js');

        $priceTagTemplate = str_replace('// price-tag-placeholder', $priceTagScript, $priceTagTemplate);

        file_put_contents($this->module->getLocalPath() . 'views/js/front/price-tag-dynamic.js', $priceTagTemplate);
    }
}
