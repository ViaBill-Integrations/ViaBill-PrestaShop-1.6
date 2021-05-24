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

use Controller;
use ViaBill\Adapter\Media;
use ViaBill\Config\Config;
use Module;

/**
 * Class OrderPageAssetsLoader
 *
 * @package ViaBill\Service\Strategy
 */
class OrderPageAssetsLoader implements AssetsLoaderInterface
{
    /**
     * Loads Order Page Price Tags Assets.
     *
     * @param Controller $controller
     * @param Media $mediaAdapter
     * @param Module $module
     *
     * @return mixed|void
     */
    public function loadAssets(Controller $controller, Media $mediaAdapter, Module $module)
    {
        $mediaAdapter->addJsDef(array(
            'dynamicPriceTagTrigger' => Config::DYNAMIC_PRICE_CART_TRIGGER,
            'controller' => $controller->php_self,
        ));

        $mediaAdapter->addJs($controller, 'views/js/front/cart_update_handler.js');
        $mediaAdapter->addJs($controller, 'views/js/front/price-tag-dynamic.js');
        $mediaAdapter->addCss($controller, 'views/css/front/price-tag.css');
    }
}
