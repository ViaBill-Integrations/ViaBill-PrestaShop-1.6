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
use Module;

/**
 * Interface AssetsLoaderInterface
 *
 * @package ViaBill\Service\Strategy
 */
interface AssetsLoaderInterface
{
    /**
     * Price Tag Load Assets Interface.
     *
     * @param Controller $controller
     * @param Media $mediaAdapter
     * @param Module $module
     *
     * @return mixed
     */
    public function loadAssets(Controller $controller, Media $mediaAdapter, Module $module);
}
