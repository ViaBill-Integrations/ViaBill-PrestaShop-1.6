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
use Controller;
use ViaBill\Adapter\Media;
use ViaBill\Config\Config;
use Module;

/**
 * Class ProductPageAssetsLoader
 *
 * @package ViaBill\Service\Strategy
 */
class ProductPageAssetsLoader implements AssetsLoaderInterface
{
    /**
     * Loads Product Page Price Tag Assets.
     *
     * @param Controller $controller
     * @param Media $mediaAdapter
     * @param Module $module
     *
     * @return mixed|void
     */
    public function loadAssets(Controller $controller, Media $mediaAdapter, Module $module)
    {
        if (!Configuration::get(Config::ENABLE_PRICE_TAG_ON_PRODUCT_PAGE)) {
            return;
        }

        if ($module->isPS16()) {
            $mediaAdapter->addJs($controller, 'views/js/front/product_update_handler.js');
        } else {
            $mediaAdapter->addJs($controller, 'views/js/front/product_update_handler_15.js');
        }

        $mediaAdapter->addJs($controller, 'views/js/front/price-tag-dynamic.js');
        $mediaAdapter->addCss($controller, 'views/css/front/price-tag.css');
    }
}
