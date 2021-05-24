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

namespace ViaBill\Service\Strategy;

use Configuration;
use ViaBill\Adapter\Media;
use ViaBill\Config\Config;

/**
 * Class AssetsLoader
 *
 * @package ViaBill\Service\Strategy
 */
class AssetsLoader
{
    /**
     * Module Main Class Variable Declaration.
     *
     * @var \ViaBill
     */
    private $module;

    /**
     * Controller Variable Declaration.
     *
     * @var $controller
     */
    private $controller;

    /**
     * Media Adapter Variable Declaration.
     *
     * @var $mediaAdapter
     */
    private $mediaAdapter;

    /**
     * AssetsLoader constructor.
     *
     * @param \ViaBill $module
     * @param Media $mediaAdapter
     */
    public function __construct(\ViaBill $module, Media $mediaAdapter)
    {
        $this->module = $module;
        $this->mediaAdapter = $mediaAdapter;
    }

    /**
     * Sets Controller.
     *
     * @param object|array $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Loads Price Tags Assets.
     *
     * @throws \SmartyException
     */
    public function load()
    {
        if ($this->controller->php_self == 'product') {
            $productPageAssetsLoader = new ProductPageAssetsLoader();
            $productPageAssetsLoader->loadAssets($this->controller, $this->mediaAdapter, $this->module);
        }

        if (in_array($this->controller->php_self, array('order-opc', 'order'))) {
            if ($this->module->isPS16()) {
                $this->mediaAdapter->addCss($this->controller, 'views/css/front/payment.css');
            } else {
                $this->mediaAdapter->addCss($this->controller, 'views/css/front/payment15.css');
            }

            if (!Configuration::get(Config::ENABLE_PRICE_TAG_ON_CART_SUMMARY) &&
                !Configuration::get(Config::ENABLE_PRICE_TAG_ON_PAYMENT_SELECTION)) {
                return;
            }

            if (Configuration::get(Config::ENABLE_PRICE_TAG_ON_CART_SUMMARY)) {
                if ($this->controller->php_self == 'order' && $this->controller->step == 0) {
                    $this->module->initPriceTagsCartPage($this->mediaAdapter);
                }

                if ($this->controller->php_self == 'order-opc') {
                    $this->module->initPriceTagsCartPage($this->mediaAdapter);
                }
            }

            $productPageAssetsLoader = new OrderPageAssetsLoader();
            $productPageAssetsLoader->loadAssets($this->controller, $this->mediaAdapter, $this->module);
        }
    }
}
